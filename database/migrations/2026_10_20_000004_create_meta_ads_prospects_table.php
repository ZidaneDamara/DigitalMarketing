<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meta_ads_prospects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('meta_ads_report_id')->nullable()->constrained('meta_ads_reports')->nullOnDelete();
            $table->string('nama');
            $table->string('nomor_hp');
            $table->string('sumber_leads'); // Instagram, Facebook, WhatsApp
            $table->date('tanggal_input');
            $table->string('status')->default('Prospek Baru'); // Prospek Baru, Dihubungi, Closing STU, Tidak Tertarik
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['branch_id', 'tanggal_input']);
            $table->index('sumber_leads');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meta_ads_prospects');
    }
};
