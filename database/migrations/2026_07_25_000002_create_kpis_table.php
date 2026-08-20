<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpis', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('tahun');
            $table->unsignedTinyInteger('bulan');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->unique(['tahun', 'bulan']);
        });

        Schema::create('kpi_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kpi_id')->constrained('kpis')->onDelete('cascade');
            
            // Instagram
            $table->integer('ig_feed_target')->default(0);
            $table->integer('ig_reels_target')->default(0);
            $table->integer('ig_story_target')->default(0);
            $table->integer('ig_followers_target')->default(0);
            
            // Facebook
            $table->integer('fb_post_target')->default(0);
            $table->integer('fb_marketplace_target')->default(0);
            $table->integer('fb_followers_target')->default(0);
            
            // TikTok
            $table->integer('tiktok_post_target')->default(0);
            $table->integer('tiktok_live_target')->default(0);
            $table->integer('tiktok_followers_target')->default(0);
            
            // Google Business
            $table->decimal('google_rating_target', 3, 1)->default(4.5);
            $table->integer('google_review_target')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_targets');
        Schema::dropIfExists('kpis');
    }
};
