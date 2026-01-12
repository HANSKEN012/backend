<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $indexMap = [
            'videos' => [
                ['columns' => ['category_id', 'status'], 'name' => 'videos_category_id_status_index'],
                ['columns' => ['user_id', 'created_at'], 'name' => 'videos_user_id_created_at_index'],
                ['columns' => ['views_count', 'created_at'], 'name' => 'videos_views_count_created_at_index'],
                ['columns' => ['title'], 'name' => 'videos_title_index'],
            ],
            'watch_history' => [
                ['columns' => ['user_id', 'created_at'], 'name' => 'watch_history_user_id_created_at_index'],
                ['columns' => ['user_id', 'completed'], 'name' => 'watch_history_user_id_completed_index'],
                ['columns' => ['user_id', 'completed', 'created_at'], 'name' => 'watch_history_user_id_completed_created_at_index'],
                ['columns' => ['video_id', 'user_id'], 'name' => 'watch_history_video_id_user_id_index'],
            ],
            'playlist_videos' => [
                ['columns' => ['playlist_id', 'position'], 'name' => 'playlist_videos_playlist_id_position_index'],
                ['columns' => ['playlist_id', 'video_id'], 'name' => 'playlist_videos_playlist_id_video_id_index'],
                ['columns' => ['playlist_id', 'video_id', 'position'], 'name' => 'playlist_videos_playlist_id_video_id_position_index'],
            ],
            'playlists' => [
                ['columns' => ['user_id', 'created_at'], 'name' => 'playlists_user_id_created_at_index'],
                ['columns' => ['is_public', 'created_at'], 'name' => 'playlists_is_public_created_at_index'],
            ],
            'categories' => [
                ['columns' => ['slug'], 'name' => 'categories_slug_index'],
            ],
        ];

        foreach ($indexMap as $table => $indexes) {
            foreach ($indexes as $index) {
                $this->createIndexIfNotExists($table, $index['columns'], $index['name']);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $indexNames = [
            'videos_category_id_status_index',
            'videos_user_id_created_at_index',
            'videos_views_count_created_at_index',
            'videos_title_index',

            'watch_history_user_id_created_at_index',
            'watch_history_user_id_completed_index',
            'watch_history_user_id_completed_created_at_index',
            'watch_history_video_id_user_id_index',

            'playlist_videos_playlist_id_position_index',
            'playlist_videos_playlist_id_video_id_index',
            'playlist_videos_playlist_id_video_id_position_index',

            'playlists_user_id_created_at_index',
            'playlists_is_public_created_at_index',

            'categories_slug_index',
        ];

        // Attempt to drop each index if present; ignore errors
        foreach ($indexNames as $indexName) {
            $this->dropIndexIfExistsByName($indexName);
        }
    }

    /**
     * Create an index if it doesn't already exist (DB-agnostic and safe).
     */
    protected function createIndexIfNotExists(string $table, array $columns, string $indexName): void
    {
        try {
            // Use Doctrine where available to check indexes
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = $sm->listTableIndexes($table);
            if (isset($indexes[$indexName])) {
                return;
            }
        } catch (\Throwable $e) {
            // Doctrine not available (e.g., sqlite in-memory during tests) — proceed to attempt creation
        }

        try {
            // Attempt to create a named index; this will work on most drivers or be ignored on failure
            $columnList = implode('`, `', $columns);
            // Use CREATE INDEX which is broadly supported (wrapped in try/catch)
            DB::statement("CREATE INDEX `{$indexName}` ON `{$table}` (`{$columnList}`)");
        } catch (\Throwable $e) {
            // Last resort: some drivers (older sqlite) may not support CREATE INDEX with backticks — try without backticks
            try {
                $colListPlain = implode(',', $columns);
                DB::statement("CREATE INDEX {$indexName} ON {$table} ({$colListPlain})");
            } catch (\Throwable $e) {
                // If creation fails, ignore — we prefer not to break migrations in incompatible environments
            }
        }
    }

    /**
     * Drop an index by name, trying several syntaxes to support MySQL/Postgres/SQLite.
     */
    protected function dropIndexIfExistsByName(string $indexName): void
    {
        $attempts = [
            // Postgres: DROP INDEX IF EXISTS index_name;
            "DROP INDEX IF EXISTS `{$indexName}`",
            "DROP INDEX IF EXISTS {$indexName}",
            // MySQL: DROP INDEX index_name ON table_name; (we'll attempt for each possible table)
            // We'll try a generic "DROP INDEX index ON table" via searching tables (best-effort)
        ];

        foreach ($attempts as $stmt) {
            try {
                DB::statement($stmt);
                return;
            } catch (\Throwable $e) {
                // ignore and try next
            }
        }

        // As a best-effort fallback, attempt to drop index on known tables (MySQL syntax)
        $tablesToTry = ['videos', 'watch_history', 'playlist_videos', 'playlists', 'categories'];
        foreach ($tablesToTry as $table) {
            try {
                DB::statement("DROP INDEX `{$indexName}` ON `{$table}`");
                return;
            } catch (\Throwable $e) {
                // ignore
            }
            try {
                DB::statement("DROP INDEX {$indexName} ON {$table}");
                return;
            } catch (\Throwable $e) {
                // ignore
            }
        }
    }
};
