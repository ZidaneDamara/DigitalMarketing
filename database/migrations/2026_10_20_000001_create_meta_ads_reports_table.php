<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meta_ads_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedTinyInteger('bulan'); // 1-12
            $table->unsignedSmallInteger('tahun'); // e.g. 2026
            $table->date('tanggal_laporan')->nullable();
            $table->string('nama_iklan');
            $table->string('product'); // e.g. NMAX Turbo, Aerox 155, Grand Filano, Fazzio
            $table->decimal('ctr', 8, 2)->default(0); // Click-Through Rate (%)
            $table->decimal('cpc', 15, 2)->default(0); // Cost Per Click (Rp)
            $table->decimal('cpm', 15, 2)->default(0); // Cost Per Mille (Rp)
            $table->decimal('cpl', 15, 2)->default(0); // Cost Per Lead / Chat (Rp)
            $table->unsignedBigInteger('total_views')->default(0); // Impressions / Tayangan
            $table->unsignedInteger('total_percakapan')->default(0); // Messaging Conversations Started / Leads
            $table->decimal('budget', 15, 2)->default(0); // Total Spend / Anggaran (Rp)
            $table->string('bukti_topup')->nullable(); // Screenshot bukti transfer / invoice top up
            $table->unsignedInteger('stu')->default(0); // Sales To Unit (Closing Unit)
            $table->text('catatan')->nullable(); // Evaluasi / Analisis Iklan
            $table->timestamps();

            $table->index(['branch_id', 'tahun', 'bulan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meta_ads_reports');
    }
};
