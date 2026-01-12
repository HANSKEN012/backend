<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PlaylistController;
use App\Http\Controllers\Api\VideoController;
use App\Http\Controllers\Api\WatchHistoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
| Security Features Applied:
| - CORS: Configured in config/cors.php
| - Rate Limiting: Configured in RouteServiceProvider and config/rate_limiting.php
| - Input Sanitization: Applied via 'sanitize.input' middleware
| - Authentication: Sanctum for token-based auth
|
*/

// User info (sanctum auth)
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Authentication routes (public, with rate limiting)
Route::post('/register', [AuthController::class, 'register'])
    ->middleware('throttle:register');

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:login');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');

// Categories (public read, auth required for modifications)
Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
Route::apiResource('categories', CategoryController::class)
    ->only(['store', 'update', 'destroy'])
    ->middleware('auth:sanctum');

// Videos (public read, auth required for modifications and upload)
Route::get('/videos', [VideoController::class, 'index'])
    ->middleware('sanitize.input');

Route::get('/videos/{video}', [VideoController::class, 'show'])
    ->middleware('sanitize.input');

Route::get('/videos/{video}/stream', [VideoController::class, 'stream'])
    ->middleware('throttle:streaming');

// Video search (with rate limiting)
Route::get('/videos/search', [VideoController::class, 'search'])
    ->middleware(['sanitize.input', 'throttle:search']);

// Authenticated video operations (with upload rate limiting)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/videos', [VideoController::class, 'store'])
        ->middleware('throttle:upload');
    Route::put('/videos/{video}', [VideoController::class, 'update'])
        ->middleware('sanitize.input');
    Route::delete('/videos/{video}', [VideoController::class, 'destroy']);
    Route::get('/my-videos', [VideoController::class, 'myVideos'])
        ->middleware('sanitize.input');

    // Watch progress tracking
    Route::post('/videos/{video}/watch', [VideoController::class, 'recordWatch'])
        ->middleware('sanitize.input');
});

// Playlists (all operations require authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/playlists', [PlaylistController::class, 'index'])
        ->middleware('sanitize.input');
    Route::post('/playlists', [PlaylistController::class, 'store'])
        ->middleware('sanitize.input');
    Route::get('/playlists/{playlist}', [PlaylistController::class, 'show'])
        ->middleware('sanitize.input');
    Route::put('/playlists/{playlist}', [PlaylistController::class, 'update'])
        ->middleware('sanitize.input');
    Route::delete('/playlists/{playlist}', [PlaylistController::class, 'destroy']);

    // Playlist video management
    Route::post('/playlists/{playlist}/videos', [PlaylistController::class, 'addVideo'])
        ->middleware('sanitize.input');
    Route::delete('/playlists/{playlist}/videos/{video}', [PlaylistController::class, 'removeVideo']);
    Route::put('/playlists/{playlist}/reorder', [PlaylistController::class, 'reorderVideos'])
        ->middleware('sanitize.input');
});

// Watch history (all operations require authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/history', [WatchHistoryController::class, 'index'])
        ->middleware('sanitize.input');
    Route::post('/history', [WatchHistoryController::class, 'store'])
        ->middleware('sanitize.input');
    Route::get('/history/video/{videoId}', [WatchHistoryController::class, 'show'])
        ->middleware('sanitize.input');
    Route::put('/history/video/{videoId}', [WatchHistoryController::class, 'update'])
        ->middleware('sanitize.input');
    Route::delete('/history/{id}', [WatchHistoryController::class, 'destroy']);
    Route::delete('/history', [WatchHistoryController::class, 'clearAll']);
    Route::get('/history/continue-watching', [WatchHistoryController::class, 'continueWatching'])
        ->middleware('sanitize.input');
});

