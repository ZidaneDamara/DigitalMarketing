<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change jabatan column to string (VARCHAR 100) to support all sales roles
        try {
            DB::statement("ALTER TABLE tiktok_live_reports MODIFY COLUMN jabatan VARCHAR(100) NOT NULL");
        } catch (\Throwable $e) {
            Schema::table('tiktok_live_reports', function (Blueprint $table) {
                $table->string('jabatan', 100)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::statement("ALTER TABLE tiktok_live_reports MODIFY COLUMN jabatan ENUM('PIC Digital', 'Sales Digital') NOT NULL");
        } catch (\Throwable $e) {
            // Ignore down error if incompatible data exists
        }
    }
};
