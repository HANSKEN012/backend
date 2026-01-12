# Rio tube API Documentation

## Table of Contents
- [Overview](#overview)
- [Base URL](#base-url)
- [Authentication](#authentication)
- [Response Format](#response-format)
- [Rate Limiting](#rate-limiting)
- [Error Handling](#error-handling)
- [API Endpoints](#api-endpoints)

---

## Overview

Rio tube is a RESTful API for a video streaming platform. This API provides endpoints for user authentication, video management, categorization, playlist management, and watch history tracking.

### Features
- User registration and authentication
- Video upload, management, and streaming
- Category organization
- Playlist creation and management
- Watch history tracking
- Video search and filtering

---

## Base URL

```
http://127.0.0.1:8000/api
```

All API endpoints are relative to this base URL.

---

## Authentication

The API uses Laravel Sanctum for authentication via Bearer tokens.

### Obtaining a Token

Tokens are obtained through registration or login:

```json
{
  "token": "1|abcdef123456...",
  "user": { "id": 1, "name": "John Doe", "email": "john@example.com" }
}
```

### Using the Token

Include the token in the Authorization header:

```
Authorization: Bearer 1|abcdef123456...
```

---

## Response Format

### Success Response

```json
{
  "message": "Descriptive success message",
  "data": { ... }
}
```

### Paginated Response

```json
{
  "message": "Items retrieved successfully",
  "data": {
    "data": [...],
    "links": { "first": "...", "last": "...", "prev": null, "next": "..." },
    "meta": { "current_page": 1, "last_page": 5, "per_page": 10, "total": 50 }
  }
}
```

---

## Rate Limiting

| Endpoint Group | Limit | Window |
|---------------|-------|--------|
| Authentication | 5 | per minute |
| Search | 60 | per minute |
| Upload | 10 | per minute |
| General | 60 | per minute |

Response headers:
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
```

---

## Error Handling

### HTTP Status Codes

| Code | Meaning |
|------|---------|
| 200 | OK |
| 201 | Created |
| 400 | Bad Request |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Validation Error |
| 429 | Rate Limited |

### Error Response

```json
{
  "message": "Error description",
  "errors": { "field": ["Error message"] }
}
```

---

## API Endpoints

---

### Authentication

#### POST /register
Create a new user account.

**Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response (201):**
```json
{
  "message": "User registered successfully",
  "user": { "id": 1, "name": "...", "email": "...", "created_at": "..." },
  "token": "1|..."
}
```

#### POST /login
Authenticate and obtain access token.

**Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response (200):**
```json
{
  "message": "Login successful",
  "user": { "id": 1, "name": "...", "email": "..." },
  "token": "2|..."
}
```

#### GET /user
Get authenticated user's profile.

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "id": 1, "name": "John Doe", "email": "john@example.com",
  "avatar": null, "role": "user", "is_active": true
}
```

#### POST /logout
Revoke current access token.

**Headers:** `Authorization: Bearer {token}`

**Response (200):** `{ "message": "Logged out successfully" }`

---

### Categories

#### GET /categories
List all categories (public).

**Response (200):**
```json
{
  "message": "Categories retrieved successfully",
  "data": [
    { "id": 1, "name": "Technology", "slug": "technology", "description": "..." }
  ]
}
```

#### GET /categories/{id}
Get single category.

**Response (200):**
```json
{
  "message": "Category retrieved successfully",
  "data": { "id": 1, "name": "Technology", "videos": [...] }
}
```

#### POST /categories
Create category (authenticated).

**Headers:** `Authorization: Bearer {token}`

**Body:**
```json
{ "name": "Gaming", "description": "Gaming videos" }
```

**Response (201):**
```json
{
  "message": "Category created successfully",
  "data": { "id": 3, "name": "Gaming", "slug": "gaming" }
}
```

#### PUT /categories/{id}
Update category (authenticated).

**Response (200):**
```json
{ "message": "Category updated successfully", "data": {...} }
```

#### DELETE /categories/{id}
Delete category (authenticated).

**Response (200):** `{ "message": "Category deleted successfully" }`

---

### Videos

#### GET /videos
List all videos with pagination (public).

**Query Parameters:**
- `category_id` - Filter by category
- `sort_by` - created_at, views_count, title
- `sort_order` - asc, desc
- `per_page` - Items per page

**Response (200):**
```json
{
  "message": "Videos retrieved successfully",
  "data": {
    "data": [
      {
        "id": 1, "title": "Laravel Tutorial", "views_count": 15000,
        "duration": 7200, "status": "active",
        "user": { "id": 1, "name": "John Doe" },
        "category": { "id": 1, "name": "Technology" }
      }
    ],
    "links": {...}, "meta": {...}
  }
}
```

#### GET /videos/{id}
Get single video (increments view count).

**Response (200):**
```json
{
  "message": "Video retrieved successfully",
  "data": {
    "id": 1, "title": "Laravel Tutorial", "file_path": "videos/abc.mp4",
    "thumbnail_path": "thumbnails/abc.jpg", "file_size": 524288000,
    "duration": 7200, "views_count": 15001, "status": "active"
  }
}
```

#### GET /videos/search
Search videos (public).

**Query:** `?q=tutorial&category_id=1`

**Response (200):**
```json
{
  "message": "Search results retrieved successfully",
  "data": [...],
  "search_term": "tutorial"
}
```

#### POST /videos
Upload video (authenticated).

**Headers:** `Authorization: Bearer {token}`
**Content-Type:** multipart/form-data

**Form Data:**
| Field | Type | Required |
|-------|------|----------|
| title | text | Yes |
| description | text | No |
| category_id | text | No |
| video | file | Yes |
| thumbnail | file | No |

**Response (201):**
```json
{
  "message": "Video uploaded successfully",
  "data": {
    "id": 1, "title": "My New Video", "file_path": "videos/abc.mp4",
    "file_size": 524288000, "duration": 0, "views_count": 0, "status": "active"
  }
}
```

#### PUT /videos/{id}
Update video (owner only).

**Headers:** `Authorization: Bearer {token}`

**Body:**
```json
{ "title": "Updated Title", "description": "New description", "status": "active" }
```

**Response (200):** `{ "message": "Video updated successfully", "data": {...} }`

**Error (403):** `{ "message": "Unauthorized. You can only update your own videos." }`

#### DELETE /videos/{id}
Delete video (owner only).

**Headers:** `Authorization: Bearer {token}`

**Response (200):** `{ "message": "Video deleted successfully" }`

#### GET /my-videos
Get authenticated user's videos.

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "message": "User videos retrieved successfully",
  "data": [...]
}
```

#### POST /videos/{id}/watch
Record watch progress.

**Headers:** `Authorization: Bearer {token}`

**Body:**
```json
{ "progress": 120, "completed": false }
```

**Response (201):**
```json
{
  "message": "Watch progress recorded successfully",
  "data": { "id": 1, "video_id": 1, "progress": 120, "completed": false }
}
```

---

### Playlists

#### GET /playlists
List user's playlists (authenticated).

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "message": "Playlists retrieved successfully",
  "data": {
    "data": [
      { "id": 1, "name": "My Favorites", "is_public": true, "videos_count": 5 }
    ]
  }
}
```

#### POST /playlists
Create playlist.

**Headers:** `Authorization: Bearer {token}`

**Body:**
```json
{ "name": "Watch Later", "description": "...", "is_public": false }
```

**Response (201):**
```json
{
  "message": "Playlist created successfully",
  "data": { "id": 2, "name": "Watch Later", "is_public": false, "videos": [] }
}
```

#### GET /playlists/{id}
Get playlist details.

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "message": "Playlist retrieved successfully",
  "data": {
    "id": 1, "name": "My Favorites", "is_public": true,
    "videos": [
      {
        "id": 1, "title": "Video", "duration": 600,
        "pivot": { "playlist_id": 1, "video_id": 1, "position": 0 }
      }
    ]
  }
}
```

#### PUT /playlists/{id}
Update playlist.

**Response (200):** `{ "message": "Playlist updated successfully", "data": {...} }`

#### DELETE /playlists/{id}
Delete playlist.

**Response (200):** `{ "message": "Playlist deleted successfully" }`

#### POST /playlists/{id}/videos
Add video to playlist.

**Body:** `{ "video_id": 5 }`

**Response (201):** `{ "message": "Video added to playlist successfully" }`

**Error (422):** `{ "message": "Video already exists in this playlist" }`

#### DELETE /playlists/{id}/videos/{video_id}
Remove video from playlist.

**Response (200):** `{ "message": "Video removed from playlist successfully" }`

#### PUT /playlists/{id}/reorder
Reorder videos in playlist.

**Body:** `{ "video_ids": [5, 3, 1, 2, 4] }`

**Response (200):** `{ "message": "Videos reordered successfully" }`

---

### Watch History

#### GET /history
Get watch history.

**Headers:** `Authorization: Bearer {token}`

**Query:** `?incomplete=true` or `?completed=true`

**Response (200):**
```json
{
  "message": "Watch history retrieved successfully",
  "data": {
    "data": [
      {
        "id": 1, "video_id": 5, "progress": 120, "completed": false,
        "watched_at": "2024-01-15T12:00:00.000000Z",
        "video": { "id": 5, "title": "Video Title", "duration": 600 }
      }
    ]
  }
}
```

#### POST /history
Record watch progress.

**Body:** `{ "video_id": 5, "progress": 180, "completed": false }`

**Response (201):** `{ "message": "Watch progress recorded successfully", "data": {...} }`

#### GET /history/video/{video_id}
Get progress for specific video.

**Response (200):**
```json
{
  "message": "Watch history retrieved successfully",
  "data": { "id": 2, "video_id": 5, "progress": 180, "completed": false }
}
```

**If not found:** `{ "message": "No watch history found for this video", "data": null }`

#### PUT /history/video/{video_id}
Update watch progress.

**Body:** `{ "progress": 300, "completed": true }`

**Response (200):** `{ "message": "Watch progress updated successfully" }`

#### DELETE /history/{id}
Delete history entry.

**Response (200):** `{ "message": "Watch history entry deleted successfully" }`

#### DELETE /history
Clear all history.

**Response (200):** `{ "message": "All watch history cleared successfully" }`

#### GET /history/continue-watching
Get incomplete videos sorted by recent activity.

**Response (200):**
```json
{
  "message": "Continue watching list retrieved successfully",
  "data": [...]
}
```

---

### Streaming

#### GET /videos/{id}/stream
Stream video content.

**Headers:** `Range: bytes=0-1048575`

**Response (206 Partial Content):**
```
HTTP/1.1 206 Partial Content
Content-Type: video/mp4
Content-Length: 1048576
Content-Range: bytes 0-1048575/524288000
```

**Without Range:** Returns full file with status 200.

**Note:** View count increments on each stream request.

---

## Models Reference

### User
| Field | Type | Description |
|-------|------|-------------|
| id | integer | Primary key |
| name | string | User's full name |
| email | string | Email (unique) |
| password | string | Hashed password |
| avatar | string\|null | Avatar path |
| role | string | user or admin |
| is_active | boolean | Account status |

### Category
| Field | Type | Description |
|-------|------|-------------|
| id | integer | Primary key |
| name | string | Category name (unique) |
| slug | string | URL slug (unique) |
| description | string\|null | Description |

### Video
| Field | Type | Description |
|-------|------|-------------|
| id | integer | Primary key |
| title | string | Video title |
| description | string\|null | Description |
| user_id | integer | Owner ID |
| category_id | integer\|null | Category ID |
| file_path | string | Video file path |
| thumbnail_path | string\|null | Thumbnail path |
| file_size | integer | Size in bytes |
| duration | integer | Duration in seconds |
| views_count | integer | View count |
| status | string | active/inactive/processing |

### Playlist
| Field | Type | Description |
|-------|------|-------------|
| id | integer | Primary key |
| user_id | integer | Owner ID |
| name | string | Playlist name |
| description | string\|null | Description |
| is_public | boolean | Visibility |

### WatchHistory
| Field | Type | Description |
|-------|------|-------------|
| id | integer | Primary key |
| user_id | integer | User ID |
| video_id | integer | Video ID |
| progress | integer | Progress in seconds |
| completed | boolean | Completion status |
| watched_at | datetime | Last watched |

---

## Validation Reference

### Authentication Validation
| Field | Required | Rules |
|-------|----------|-------|
| name | Yes | string, max:255 |
| email | Yes | email, unique:users |
| password | Yes | min:8, confirmed |

### Category Validation
| Field | Required | Rules |
|-------|----------|-------|
| name | Yes | string, max:255, unique:categories |
| description | No | string |

### Video Validation
| Field | Required | Rules |
|-------|----------|-------|
| title | Yes | string, max:255 |
| description | No | string |
| category_id | No | exists:categories,id |
| video | Yes | file, mimes:mp4,mov,avi,mkv,webm, max:500MB |
| thumbnail | No | image, mimes:jpg,jpeg,png,webp, max:5MB |

### Playlist Validation
| Field | Required | Rules |
|-------|----------|-------|
| name | Yes | string, max:255 |
| description | No | string |
| is_public | No | boolean |

---

## File Storage

### Directory Structure
```
storage/app/public/
├── videos/          # Video files
├── thumbnails/      # Video thumbnails
└── avatars/         # User avatars
```

### Access Files
```
http://127.0.0.1:8000/storage/videos/abc123.mp4
http://127.0.0.1:8000/storage/thumbnails/abc123.jpg
```

---

## Running Tests

```bash
# Run all tests
./vendor/bin/phpunit

# Run specific test file
./vendor/bin/phpunit tests/Feature/VideoApiTest.php

# Run unit tests only
./vendor/bin/phpunit tests/Unit/

# Run with coverage
./vendor/bin/phpunit --coverage-html coverage
```

---

## Environment Setup

1. Install dependencies:
```bash
composer install
```

2. Copy environment file:
```bash
cp .env.example .env
```

3. Generate app key:
```bash
php artisan key:generate
```

4. Configure database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=Rio tube
DB_USERNAME=root
DB_PASSWORD=
```

5. Run migrations:
```bash
php artisan migrate
```

6. Start development server:
```bash
php artisan serve
```

7. Create storage link:
```bash
php artisan storage:link
```

---

This documentation provides a complete reference for the Rio tube API. For additional examples and testing instructions, see THUNDER_CLIENT_TESTING_GUIDE.md.

