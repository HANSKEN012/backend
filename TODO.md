# Streaming Platform Backend - TODO List

## Project Configuration (CONFIRMED)
- **Database**: MySQL (database: streamflix)
- **Authentication**: Laravel Sanctum (token-based)
- **Storage**: Local storage
- **Streaming**: Progressive download
- **API Type**: RESTful JSON API
- **Learning Focus**: Beginner-friendly with detailed explanations

## Step-by-Step Learning Plan

### 📚 PHASE 1: Foundation (Week 1-2)
#### Step 1: Environment Setup & Understanding Laravel Structure
- [ ] 1.1 Verify XAMPP MySQL is running and database exists
- [ ] 1.2 Install Laravel Sanctum package
- [ ] 1.3 Configure Sanctum for API authentication
- [ ] 1.4 Understand project structure and MVC pattern
- [ ] 1.5 Learn about migrations (database schema version control)

#### Step 2: Database Design & Migrations
- [ ] 2.1 Create users table migration (extended version)
- [ ] 2.2 Create categories table
- [ ] 2.3 Create videos table
- [ ] 2.4 Create playlists table
- [ ] 2.5 Create watch_history table
- [ ] 2.6 Run migrations and verify database structure

#### Step 3: Models & Relationships (Understanding Eloquent ORM)
- [ ] 3.1 Create User model with relationships
- [ ] 3.2 Create Category model
- [ ] 3.3 Create Video model with relationships
- [ ] 3.4 Create Playlist model
- [ ] 3.5 Create WatchHistory model
- [ ] 3.6 Understand foreign keys and database relationships

#### Step 4: Controllers & Routes
- [ ] 4.1 Create base ApiController
- [ ] 4.2 Create routes/api.php structure
- [ ] 4.3 Understand RESTful API design
- [ ] 4.4 Learn about route groups and middleware

### 🔐 PHASE 2: Authentication (Week 3-4) ✅ COMPLETED
#### Step 5: Laravel Sanctum Authentication ✅
- [x] 5.1 Install and configure Sanctum
- [x] 5.2 Create Register API endpoint
- [x] 5.3 Create Login API endpoint
- [x] 5.4 Create Logout API endpoint
- [x] 5.5 Create User Profile API endpoint
- [x] 5.6 Understand API tokens and authentication middleware

#### Step 6: Token Management ✅
- [x] 6.1 Create token on login
- [x] 6.2 Revoke tokens on logout
- [ ] 6.3 Handle multiple devices (token management)
- [ ] 6.4 Token expiration policies

### 📹 PHASE 3: Content Management (Week 5-6)
#### Step 7: Category Management ✅ COMPLETED
- [x] 7.1 Create Category CRUD operations (index, store, show, update, destroy)
- [x] 7.2 Learn about API Resource responses (JsonResponse)
- [x] 7.3 Add validation rules (laravel validation with unique constraint)
- [x] 7.4 Handle errors gracefully (try-catch with proper status codes)

#### Step 8: Video Management ✅ COMPLETED
- [x] 8.1 Create Video model and migration
- [x] 8.2 Upload videos to local storage
- [x] 8.3 Create video upload endpoint
- [x] 8.4 Create video CRUD endpoints
- [x] 8.5 Add video metadata (title, description, duration)
- [x] 8.6 Learn about file handling in Laravel

#### Step 9: Playlist Management ✅ COMPLETED
- [x] 9.1 Create Playlist model (app/Models/Playlist.php)
- [x] 9.2 Create playlist CRUD endpoints (index, store, show, update, destroy)
- [x] 9.3 Add videos to playlists (POST /playlists/{id}/videos)
- [x] 9.4 Remove videos from playlists (DELETE /playlists/{id}/videos/{vid})
- [x] 9.5 Reorder videos in playlist (PUT /playlists/{id}/reorder)
- [x] 9.6 Public/private playlist visibility
- [x] 9.7 Video position tracking in playlists

### ⏱️ PHASE 4: Streaming Features (Week 7-8)
#### Step 10: Progressive Download Streaming ✅ COMPLETED
- [x] 10.1 Create streaming endpoint (`GET /api/videos/{id}/stream`)
- [x] 10.2 Implement range header handling
- [x] 10.3 Create video streaming controller
- [x] 10.4 Handle partial content requests (206 Partial Content)
- [x] 10.5 Understand HTTP range requests

#### Step 11: Watch History ✅ COMPLETED
- [x] 11.1 Create WatchHistory model
- [x] 11.2 Track video views (incrementViews)
- [x] 11.3 Store watch progress (POST /api/videos/{id}/watch)
- [x] 11.4 Retrieve watch history (GET /api/history)
- [x] 11.5 Implement continue watching feature

#### Step 12: Video Search & Discovery ✅ COMPLETED
- [x] 12.1 Create search API endpoint (GET /api/videos/search)
- [x] 12.2 Filter videos by category
- [x] 12.3 Sort videos by date/views
- [x] 12.4 Implement pagination

### 🛡️ PHASE 5: Security & Best Practices (Week 9-10)
#### Step 13: API Security ✅ COMPLETED
- [x] 13.1 Configure CORS (`config/cors.php` - allowed origins, methods, headers, exposed headers, max age, credentials)
- [x] 13.2 Add rate limiting (`config/rate_limiting.php`, `RouteServiceProvider.php` - global, auth, endpoints, user tiers)
- [x] 13.3 Input validation and sanitization (`app/Http/Middleware/InputSanitization.php` - XSS protection, input filtering)
- [x] 13.4 API response formatting (`app/Http/ApiResponse.php`, `config/api_response.php` - standardized JSON responses)
- [x] 13.5 Error handling (`app/Exceptions/ApiExceptionHandler.php` - consistent error responses with proper status codes)

#### Step 14: Performance Optimization ✅ COMPLETED
- [x] 14.1 Database indexing - Created migration `2025_12_31_180000_add_performance_indexes.php` with indexes on:
  - `videos`: category_id+status, user_id+created_at, views_count+created_at, title
  - `watch_history`: user_id+created_at, user_id+completed, user_id+completed+created_at
  - `playlist_videos`: playlist_id+position, playlist_id+video_id, playlist_id+video_id+position
  - `playlists`: user_id+created_at, is_public+created_at
  - `categories`: slug
  - Migration includes duplicate index checking to handle existing indexes
- [x] 14.2 Query optimization - Added optimized scopes to models:
  - `scopeSelectMinimal()` for fetching only needed columns
  - `scopeInCategories()` for category filtering
  - `scopeMinViews()` for minimum view count filtering
  - `scopeViewedWithinDays()` for recency filtering
  - `scopeForUser()`, `scopeForVideo()` for WatchHistory filtering
  - `videosWithRelationships()` optimized eager loading method
- [x] 14.3 Eager loading to prevent N+1 queries:
  - Created `videosWithRelationships()` method in Playlist model
  - Updated controllers to use eager loading with specific columns (select only needed fields)
  - Added `withCount()` for aggregate queries
  - Used partial loading: `user:id,name,avatar`, `category:id,name,slug`
- [x] 14.4 Caching strategies:
  - Created `CacheService` (`app/Services/CacheService.php`) with methods for:
    - Categories list caching (1 hour TTL)
    - Featured/recent videos caching (30 min / 5 min TTL)
    - Video count by category caching
    - User watch history caching (1 min TTL)
    - Cache invalidation on model events
  - Added model observers (boot methods) for automatic cache invalidation
  - Controllers updated to use `CacheService::getCategories()` for category listing

#### Step 15: Testing & Documentation
- [ ] 15.1 Write basic unit tests
- [ ] 15.2 Test API endpoints explanation on how to do in thunder client 
- [ ] 15.3 Create API documentation

## 📖 Learning Resources per Step

### Step 1.1: Understanding Laravel Structure
**What is Laravel?**
- Laravel is a PHP framework for web artisans
- Follows MVC (Model-View-Controller) pattern
- Provides elegant syntax for common tasks

**Project Structure:**
```
/home/prosper/itechtube (root)
├── app/          # Application code (MVC)
├── bootstrap/    # Framework initialization
├── config/       # Configuration files
├── database/     # Migrations and seeders
├── public/       # Web root (index.php)
├── resources/    # Views, assets
├── routes/       # Route definitions
├── storage/      # Logs, caches, uploads
└── vendor/       # Dependencies (composer)
```

**Understanding MVC:**
- **Model**: Database operations (app/Models/)
- **View**: What user sees (not used in APIs, we return JSON)
- **Controller**: Handle requests, business logic (app/Http/Controllers/)

### Step 1.4: What are Migrations?
**Concept:**
Migrations are like "version control for your database"
- They track database schema changes over time
- Everyone on the team has the same database structure
- Easy to rollback if something goes wrong

**How they work:**
1. Create a migration file (describes the table)
2. Run migration (creates the table in database)
3. If needed, rollback (drops the table)

### Step 3: Understanding Eloquent ORM
**What is Eloquent?**
Laravel's ORM (Object-Relational Mapping)
- Instead of writing SQL, use PHP objects
- Example: `User::find(1)` instead of `SELECT * FROM users WHERE id = 1`

**Relationships:**
- **One-to-One**: User has one Profile
- **One-to-Many**: User has many Videos
- **Many-to-Many**: Videos have many Categories

### Step 5: Understanding API Authentication
**What is Laravel Sanctum?**
- Laravel's lightweight API authentication package
- Issues API tokens to users
- Tokens are stored in database
- Each request includes the token in header

**How it works:**
1. User logs in with email/password
2. Server returns an API token
3. User stores token on client side
4. Every API request includes: `Authorization: Bearer <token>`
5. Server validates token and identifies user

### Step 8: File Upload in Laravel
**Process:**
1. Receive file from request
2. Validate file type/size
3. Store file in disk (local/cloudinary)
4. Save file path in database
5. Return path to client

**Storage locations:**
- `storage/app/public/videos/` - For public files
- `storage/app/private/` - For private files
- Use `Storage::disk('local')->put()` to save files

### Step 10: Progressive Download Streaming
**Concept:**
Instead of downloading entire file, stream it in chunks
- Client requests video
- Server sends first chunk
- Client plays while downloading next chunk
- Uses HTTP Range header to request specific bytes

**Benefits:**
- Faster initial playback
- Less bandwidth waste
- Works on all devices

**HTTP Range Header Example:**
```
Request: Range: bytes=0-1048575
Response: Content-Range: bytes 0-1048575/12345678
Status: 206 Partial Content
```

## API Endpoints Structure

### Authentication
```
POST   /api/register     - Register new user
POST   /api/login        - Login and get token
POST   /api/logout       - Logout (revoke token)
GET    /api/user         - Get current user profile
PUT    /api/user         - Update profile
```

### Categories
```
GET    /api/categories           - List all categories
POST   /api/categories           - Create category (auth)
GET    /api/categories/{id}      - Get single category
PUT    /api/categories/{id}      - Update category (auth)
DELETE /api/categories/{id}      - Delete category (auth)
```

### Videos
```
GET    /api/videos               - List all videos
POST   /api/videos               - Upload video (auth)
GET    /api/videos/{id}          - Get video details
PUT    /api/videos/{id}          - Update video (auth)
DELETE /api/videos/{id}          - Delete video (auth)
GET    /api/videos/{id}/stream   - Stream video
```

### Playlists
```
GET    /api/playlists                    - List user playlists (auth)
POST   /api/playlists                    - Create playlist (auth)
GET    /api/playlists/{id}               - Get playlist
PUT    /api/playlists/{id}               - Update playlist (auth)
DELETE /api/playlists/{id}               - Delete playlist (auth)
POST   /api/playlists/{id}/videos        - Add video to playlist
DELETE /api/playlists/{id}/videos/{vid}  - Remove video from playlist
```

### Watch History
```
GET    /api/history              - Get watch history (auth)
POST   /api/history              - Record video watch (auth)
DELETE /api/history/{id}         - Remove history entry (auth)
```

## Database Schema

### users table
```sql
id              - Auto increment ID
name            - User's full name
email           - Unique email address
password        - Hashed password
avatar          - Profile picture path (nullable)
role            - 'user', 'admin', 'creator'
is_active       - Account status
email_verified_at- When email was verified
created_at      - Timestamp
updated_at      - Timestamp
```

### videos table
```sql
id              - Auto increment ID
user_id         - Foreign key to users
category_id     - Foreign key to categories
title           - Video title
description     - Video description
file_path       - Path to video file
thumbnail_path  - Path to thumbnail image
duration        - Video duration in seconds
file_size       - File size in bytes
views_count     - Number of views
status          - 'processing', 'active', 'inactive'
created_at      - Timestamp
updated_at      - Timestamp
```

### categories table
```sql
id              - Auto increment ID
name            - Category name
slug            - URL-friendly version
description     - Category description
created_at      - Timestamp
updated_at      - Timestamp
```

### playlists table
```sql
id              - Auto increment ID
user_id         - Foreign key to users
name            - Playlist name
description     - Playlist description
is_public       - Public or private
created_at      - Timestamp
updated_at      - Timestamp
```

### watch_history table
```sql
id              - Auto increment ID
user_id         - Foreign key to users
video_id        - Foreign key to videos
progress        - Watch progress in seconds
completed       - Boolean (fully watched)
watched_at      - Timestamp
```

## Command Line Reference

```bash
# Start development server
php artisan serve

# Create new migration
php artisan make:model Video -m

# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Create controller
php artisan make:controller Api/AuthController

# Clear cache
php artisan cache:clear
php artisan config:clear

# Run tests
php artisan test
```

## Success Criteria
- [ ] All core features implemented
- [ ] API endpoints tested with Postman/Thunder Client
- [ ] User can register, login, upload videos
- [ ] Video streaming works correctly
- [ ] Watch history is tracked
- [ ] Understanding of Laravel concepts demonstrated

## Important Notes
⚠️ **Always backup your database before running migrations**
⚠️ **Never commit .env file to version control**
⚠️ **Validate all user inputs**
⚠️ **Use proper HTTP status codes**
⚠️ **Handle errors gracefully with try-catch**
