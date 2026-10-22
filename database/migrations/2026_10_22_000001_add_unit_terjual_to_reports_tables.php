<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('tiktok_live_reports')) {
            Schema::table('tiktok_live_reports', function (Blueprint $table) {
                if (!Schema::hasColumn('tiktok_live_reports', 'unit_terjual')) {
                    $table->string('unit_terjual')->nullable()->after('stu');
                }
            });
        }

        if (Schema::hasTable('meta_ads_reports')) {
            Schema::table('meta_ads_reports', function (Blueprint $table) {
                if (!Schema::hasColumn('meta_ads_reports', 'unit_terjual')) {
                    $table->string('unit_terjual')->nullable()->after('stu');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tiktok_live_reports') && Schema::hasColumn('tiktok_live_reports', 'unit_terjual')) {
            Schema::table('tiktok_live_reports', function (Blueprint $table) {
                $table->dropColumn('unit_terjual');
            });
        }

        if (Schema::hasTable('meta_ads_reports') && Schema::hasColumn('meta_ads_reports', 'unit_terjual')) {
            Schema::table('meta_ads_reports', function (Blueprint $table) {
                $table->dropColumn('unit_terjual');
            });
        }
    }
};
