<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekly_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal_post');
            $table->string('link_content', 500);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedSmallInteger('tahun')->default(2026);
            $table->unsignedTinyInteger('minggu_ke')->default(1);
            
            // Metrik Utama
            $table->unsignedBigInteger('views')->default(0);
            $table->unsignedBigInteger('account_reached')->default(0);

            // Breakdown Interaksi (Followers / Non Followers)
            $table->unsignedBigInteger('interactions_followers')->default(0);
            $table->unsignedBigInteger('interactions_non_followers')->default(0);
            $table->unsignedInteger('likes')->default(0);
            $table->unsignedInteger('shares')->default(0);
            $table->unsignedInteger('saves')->default(0);
            $table->unsignedInteger('comments')->default(0);
            $table->unsignedInteger('reposts')->default(0);

            // Profile Activity
            $table->unsignedInteger('profile_visits')->default(0);
            $table->unsignedInteger('external_link_taps')->default(0);
            $table->unsignedInteger('follows')->default(0);

            // Top Sources (%)
            $table->decimal('source_feed_pct', 5, 2)->default(0.00);
            $table->decimal('source_profile_pct', 5, 2)->default(0.00);
            $table->decimal('source_stories_pct', 5, 2)->default(0.00);

            // Audience Demographics
            $table->decimal('gender_men_pct', 5, 2)->default(0.00);
            $table->decimal('gender_women_pct', 5, 2)->default(0.00);
            $table->string('top_country')->nullable();
            $table->string('top_age')->nullable();

            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_reports');
    }
};
