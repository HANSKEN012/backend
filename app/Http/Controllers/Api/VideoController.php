<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\WatchHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * VideoController
 *
 * Handles all video-related operations including upload, CRUD, and streaming.
 * All operations require authentication except viewing videos.
 */
class VideoController extends Controller
{
    /**
     * List all videos.
     * Optimized with eager loading and query optimization.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Video::query();

        // Filter by category if provided
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status (default to active for public)
        if (!$request->user()) {
            $query->active();
        } elseif ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Sorting - uses database indexes for performance
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $allowedSorts = ['created_at', 'views_count', 'title'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        // Pagination with optimized query
        $perPage = min($request->input('per_page', 10), 50);

        // Eager loading prevents N+1 queries
        $videos = $query->with(['user:id,name,avatar', 'category:id,name,slug'])->paginate($perPage);

        return response()->json([
            'message' => 'Videos retrieved successfully',
            'data' => $videos,
        ]);
    }

    /**
     * Upload and create a new video.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'category_id' => ['nullable', 'exists:categories,id'],
                'video' => ['required', 'file', 'mimes:mp4,mov,avi,mkv,webm', 'max:500000'], // Max 500MB
                'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5000'], // Max 5MB
            ]);

            $user = $request->user();

            // Handle video file upload
            $videoFile = $validated['video'];
            $videoFileName = Str::uuid() . '.' . $videoFile->getClientOriginalExtension();
            $videoPath = $videoFile->storeAs('videos', $videoFileName, 'public');

            // Get video file size
            $fileSize = Storage::disk('public')->size($videoPath);

            // Handle thumbnail upload (optional)
            $thumbnailPath = null;
            if ($request->hasFile('thumbnail')) {
                $thumbnailFile = $validated['thumbnail'];
                $thumbnailFileName = Str::uuid() . '.' . $thumbnailFile->getClientOriginalExtension();
                $thumbnailPath = $thumbnailFile->storeAs('thumbnails', $thumbnailFileName, 'public');
            }

            // Create video record
            $video = Video::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'user_id' => $user->id,
                'category_id' => $validated['category_id'] ?? null,
                'file_path' => $videoPath,
                'thumbnail_path' => $thumbnailPath,
                'file_size' => $fileSize,
                'duration' => 0, // Will be calculated later or by FFmpeg
                'views_count' => 0,
                'status' => 'active', // Auto-activate for now (could be 'processing')
            ]);

            return response()->json([
                'message' => 'Video uploaded successfully',
                'data' => $video->load(['user', 'category']),
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to upload video',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show a single video.
     *
     * @param Video $video
     * @return JsonResponse
     */
    public function show(Video $video): JsonResponse
    {
        // Increment view count
        $video->incrementViews();

        return response()->json([
            'message' => 'Video retrieved successfully',
            'data' => $video->load(['user', 'category']),
        ]);
    }

    /**
     * Update a video.
     *
     * Only the video owner can update it.
     *
     * @param Request $request
     * @param Video $video
     * @return JsonResponse
     */
    public function update(Request $request, Video $video): JsonResponse
    {
        try {
            // Check ownership
            if ($video->user_id !== $request->user()->id) {
                return response()->json([
                    'message' => 'Unauthorized. You can only update your own videos.',
                ], 403);
            }

            $validated = $request->validate([
                'title' => ['sometimes', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'category_id' => ['nullable', 'exists:categories,id'],
                'status' => ['sometimes', 'in:processing,active,inactive'],
            ]);

            $video->update($validated);

            return response()->json([
                'message' => 'Video updated successfully',
                'data' => $video->fresh()->load(['user', 'category']),
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update video',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a video.
     *
     * Only the video owner can delete it.
     *
     * @param Request $request
     * @param Video $video
     * @return JsonResponse
     */
    public function destroy(Request $request, Video $video): JsonResponse
    {
        try {
            // Check ownership
            if ($video->user_id !== $request->user()->id) {
                return response()->json([
                    'message' => 'Unauthorized. You can only delete your own videos.',
                ], 403);
            }

            // Delete files from storage
            if (Storage::disk('public')->exists($video->file_path)) {
                Storage::disk('public')->delete($video->file_path);
            }

            if ($video->thumbnail_path && Storage::disk('public')->exists($video->thumbnail_path)) {
                Storage::disk('public')->delete($video->thumbnail_path);
            }

            // Delete video record
            $video->delete();

            return response()->json([
                'message' => 'Video deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete video',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Stream a video.
     *
     * Handles HTTP Range requests for progressive download streaming.
     *
     * @param Request $request
     * @param Video $video
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\JsonResponse
     */
    public function stream(Request $request, Video $video)
    {
        try {
            // Check if video is streamable
            if (!$video->isStreamable()) {
                return response()->json([
                    'message' => 'Video is not available for streaming',
                ], 403);
            }

            // Check if file exists
            if (!Storage::disk('public')->exists($video->file_path)) {
                return response()->json([
                    'message' => 'Video file not found',
                ], 404);
            }

            // Increment view count (only once per session ideally)
            $video->incrementViews();

            // Get the full path
            $fullPath = Storage::disk('public')->path($video->file_path);
            $fileSize = filesize($fullPath);

            // Handle Range header for streaming
            $range = $request->header('Range');

            if ($range) {
                // Parse the Range header
                $ranges = explode('=', $range);
                $byteRanges = explode(',', $ranges[1]);
                $byteRange = explode('-', $byteRanges[0]);

                $start = (int) $byteRange[0];
                $end = isset($byteRange[1]) ? (int) $byteRange[1] : $fileSize - 1;

                // Validate range
                if ($start >= $fileSize || $end >= $fileSize || $start > $end) {
                    return response()->make('', 416); // Range Not Satisfiable
                }

                $length = $end - $start + 1;

                return response()->stream(function () use ($fullPath, $start, $end) {
                    $handle = fopen($fullPath, 'rb');
                    fseek($handle, $start);
                    $buffer = '';
                    $bytesToRead = $end - $start + 1;

                    while ($bytesToRead > 0 && !feof($handle)) {
                        $chunk = fread($handle, min(8192, $bytesToRead));
                        echo $chunk;
                        flush();
                        $bytesToRead -= strlen($chunk);
                    }

                    fclose($handle);
                }, 206, [
                    'Content-Type' => 'video/mp4',
                    'Content-Length' => $length,
                    'Content-Range' => "bytes {$start}-{$end}/{$fileSize}",
                    'Accept-Ranges' => 'bytes',
                ]);
            }

            // Return full file if no Range header
            return response()->stream(function () use ($fullPath) {
                $handle = fopen($fullPath, 'rb');
                while (!feof($handle)) {
                    echo fread($handle, 8192);
                    flush();
                }
                fclose($handle);
            }, 200, [
                'Content-Type' => 'video/mp4',
                'Content-Length' => $fileSize,
                'Accept-Ranges' => 'bytes',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to stream video',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get videos uploaded by the authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function myVideos(Request $request): JsonResponse
    {
        $user = $request->user();
        $videos = $user->videos()->with('category')->orderByDesc('created_at')->paginate(10);

        return response()->json([
            'message' => 'User videos retrieved successfully',
            'data' => $videos,
        ]);
    }

    /**
     * Search videos by title and description.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'q' => ['required', 'string', 'min:2', 'max:255'],
                'category_id' => ['nullable', 'exists:categories,id'],
                'sort_by' => ['nullable', 'in:created_at,views_count,title'],
                'sort_order' => ['nullable', 'in:asc,desc'],
                'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
            ]);

            $query = Video::query()->active();

            // Apply search filter
            $query->search($validated['q']);

            // Filter by category if provided
            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // Apply sorting
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = min($request->input('per_page', 10), 50);
            $videos = $query->with(['user', 'category'])->paginate($perPage);

            return response()->json([
                'message' => 'Search results retrieved successfully',
                'data' => $videos,
                'search_term' => $validated['q'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Search failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Record watch progress for a video.
     *
     * @param Request $request
     * @param Video $video
     * @return JsonResponse
     */
    public function recordWatch(Request $request, Video $video): JsonResponse
    {
        try {
            $validated = $request->validate([
                'progress' => ['nullable', 'integer', 'min:0'],
                'completed' => ['nullable', 'boolean'],
            ]);

            $user = $request->user();
            $progress = $validated['progress'] ?? 0;
            $completed = $validated['completed'] ?? false;

            // Find or create watch history entry
            $history = WatchHistory::updateOrCreate(
                ['user_id' => $user->id, 'video_id' => $video->id],
                [
                    'progress' => $progress,
                    'completed' => $completed,
                ]
            );

            return response()->json([
                'message' => 'Watch progress recorded successfully',
                'data' => $history,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to record watch progress',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

