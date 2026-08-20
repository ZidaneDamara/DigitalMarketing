<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal');
            
            // Instagram
            $table->integer('ig_feed')->default(0);
            $table->integer('ig_reels')->default(0);
            $table->integer('ig_story')->default(0);
            $table->integer('ig_followers_gained')->default(0);
            
            // Facebook
            $table->integer('fb_post')->default(0);
            $table->integer('fb_marketplace')->default(0);
            $table->integer('fb_followers_gained')->default(0);
            
            // TikTok
            $table->integer('tiktok_post')->default(0);
            $table->integer('tiktok_live')->default(0);
            $table->integer('tiktok_followers_gained')->default(0);
            
            // Google Business
            $table->decimal('google_rating', 3, 1)->default(0.0);
            $table->integer('google_review_gained')->default(0);
            
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['branch_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};
