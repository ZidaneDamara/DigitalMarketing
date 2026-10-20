<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('google_rating_months', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedTinyInteger('bulan'); // 1-12
            $table->unsignedSmallInteger('tahun'); // e.g. 2026
            $table->decimal('rating_awal_bulan', 3, 2)->default(4.80); // e.g. 4.85
            $table->unsignedInteger('total_review_awal_bulan')->default(0); // e.g. 850 reviews
            $table->unsignedInteger('target_bulanan')->default(125); // Target 125 Google Rating perbulan
            $table->unsignedTinyInteger('target_harian')->default(5); // Minimal 5 perhari
            $table->decimal('rating_saat_ini', 3, 2)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['branch_id', 'tahun', 'bulan'], 'unique_branch_month_rating');
        });

        Schema::create('google_rating_testimonials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('google_rating_month_id')->nullable()->constrained('google_rating_months')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal');
            $table->unsignedTinyInteger('bulan');
            $table->unsignedSmallInteger('tahun');
            $table->string('nama_reviewer')->default('Konsumen Yamaha');
            $table->unsignedTinyInteger('rating_bintang')->default(5); // 1-5
            $table->string('foto_testimoni'); // Screenshot Testimoni Review Google
            $table->text('komentar_ulasan')->nullable();
            $table->timestamps();

            $table->index(['branch_id', 'tanggal']);
            $table->index(['branch_id', 'tahun', 'bulan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_rating_testimonials');
        Schema::dropIfExists('google_rating_months');
    }
};
