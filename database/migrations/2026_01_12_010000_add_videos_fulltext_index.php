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
        // Only attempt on MySQL engines that support FULLTEXT
        $driver = Schema::getConnection()->getDriverName();

        if ($driver !== 'mysql') {
            return;
        }

        try {
            // Check if index already exists
            $exists = DB::select("SHOW INDEX FROM `videos` WHERE Key_name = 'videos_title_description_fulltext'");

            if (empty($exists)) {
                DB::statement("ALTER TABLE `videos` ADD FULLTEXT `videos_title_description_fulltext` (`title`,`description`)");
            }
        } catch (\Throwable $e) {
            // Ignore failures to avoid breaking migrations in unexpected environments
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver !== 'mysql') {
            return;
        }

        try {
            DB::statement("ALTER TABLE `videos` DROP INDEX `videos_title_description_fulltext`");
        } catch (\Throwable $e) {
            // Ignore
        }
    }
};
