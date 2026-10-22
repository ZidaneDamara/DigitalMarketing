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
        if (Schema::hasTable('google_rating_testimonials') && !Schema::hasColumn('google_rating_testimonials', 'jabatan')) {
            Schema::table('google_rating_testimonials', function (Blueprint $table) {
                $table->string('jabatan', 100)->nullable()->default('PIC Digital')->after('rating_bintang');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('google_rating_testimonials') && Schema::hasColumn('google_rating_testimonials', 'jabatan')) {
            Schema::table('google_rating_testimonials', function (Blueprint $table) {
                $table->dropColumn('jabatan');
            });
        }
    }
};
