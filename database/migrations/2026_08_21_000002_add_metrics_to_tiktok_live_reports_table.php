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
        Schema::table('tiktok_live_reports', function (Blueprint $table) {
            $table->integer('jumlah_penonton')->default(0)->after('durasi_menit');
            $table->integer('jumlah_like')->default(0)->after('jumlah_penonton');
            $table->integer('jumlah_komentar')->default(0)->after('jumlah_like');
            $table->integer('jumlah_share')->default(0)->after('jumlah_komentar');
            $table->integer('stu')->nullable()->after('jumlah_share');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tiktok_live_reports', function (Blueprint $table) {
            $table->dropColumn(['jumlah_penonton', 'jumlah_like', 'jumlah_komentar', 'jumlah_share', 'stu']);
        });
    }
};
