Repair Migration & Deployment Note — Creating Missing Indexes

Purpose
- Provide a safe, idempotent migration that creates missing performance indexes without failing on DB drivers that don't support specific index inspection SQL (e.g., sqlite in-memory tests).

Files added
- database/migrations/2026_01_12_000000_create_missing_indexes_repair.php

Why this is safe
- Uses Doctrine schema manager when available to check if the index already exists.
- Attempts index creation in a try/catch; failures are ignored to prevent migration from failing in unsupported environments.
- The down() attempts multiple syntaxes to drop indexes safely where supported.

Production deployment checklist (recommended)
1. Backup the production database.
2. Run the migration first on a staging environment that mirrors production (same DB engine/version) and inspect indexes (see verification steps below).
3. Schedule a short maintenance window (recommended) if you prefer to avoid concurrent DDL on a busy DB.
4. On production, run:
   - php artisan migrate --path=database/migrations/2026_01_12_000000_create_missing_indexes_repair.php --force
   - If migration errors, check the DB engine and apply the SQL manually if safe.

Manual SQL fallback (MySQL example)
- To create an index manually (MySQL):
  ALTER TABLE `videos` ADD INDEX `videos_category_id_status_index` (`category_id`, `status`);
- To drop an index manually (MySQL):
  ALTER TABLE `videos` DROP INDEX `videos_category_id_status_index`;

Verification steps after running migration
- MySQL: SHOW INDEX FROM `videos` WHERE Key_name = 'videos_category_id_status_index';
- Postgres: \d+ videos  (or use pg_indexes view)
- Confirm no 500s occur during category or video listing endpoints.

Notes
- This migration is intentionally conservative: it will not fail if index creation is unsupported by the environment. If you want stricter behavior in production, replace the try/catch with explicit checks or add a DB-specific implementation.

If you'd like, I can also generate a small shell script that checks index presence post-migration and reports missing ones.
