<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedSmallInteger('tahun');
            $table->unsignedTinyInteger('bulan');
            
            // Instagram Insight
            $table->unsignedBigInteger('ig_views')->default(0);
            $table->unsignedBigInteger('ig_reach')->default(0);
            $table->unsignedBigInteger('ig_accounts_reached')->default(0);
            $table->unsignedBigInteger('ig_profile_visits')->default(0);
            $table->unsignedBigInteger('ig_total_followers')->default(0);
            $table->decimal('ig_male_pct', 5, 2)->default(0.00);
            $table->decimal('ig_female_pct', 5, 2)->default(0.00);
            $table->string('ig_top_age')->nullable();
            $table->string('ig_top_cities')->nullable();
            
            // Facebook Insight
            $table->unsignedBigInteger('fb_views')->default(0);
            $table->unsignedBigInteger('fb_total_followers')->default(0);
            
            // TikTok Analytics
            $table->unsignedBigInteger('tiktok_views')->default(0);
            $table->unsignedBigInteger('tiktok_total_followers')->default(0);
            
            // Google Business
            $table->decimal('google_total_rating', 3, 1)->default(0.0);
            $table->unsignedInteger('google_total_reviews')->default(0);

            $table->timestamps();

            $table->unique(['branch_id', 'tahun', 'bulan']);
        });

        Schema::create('monthly_insight_screenshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monthly_insight_id')->constrained('monthly_insights')->onDelete('cascade');
            $table->enum('kategori', [
                'Instagram Insight',
                'Facebook Insight',
                'TikTok Analytics',
                'Google Business'
            ]);
            $table->string('file_path');
            $table->string('file_name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_insight_screenshots');
        Schema::dropIfExists('monthly_insights');
    }
};
