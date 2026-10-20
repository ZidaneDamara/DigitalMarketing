<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kpi_targets', function (Blueprint $table) {
            $table->decimal('insentif_pokok', 12, 2)->default(500000)->after('kpi_id');
            
            // Kategori Proses
            $table->decimal('sosmed_content_plan_pct', 5, 2)->default(100.00)->after('insentif_pokok');
            $table->integer('sales_digital_posting_target')->default(2)->after('sosmed_content_plan_pct'); // 2 postingan/SDM/hari
            $table->decimal('tiktok_live_daily_hours', 4, 2)->default(3.00)->after('sales_digital_posting_target'); // 3 jam/SDM/hari
            $table->integer('google_foto_daily_target')->default(4)->after('google_review_target'); // 4 foto 3S/hari
            $table->integer('meta_ads_deadline_day')->default(26)->after('google_foto_daily_target'); // Pasang Meta Ads sebelum tanggal 26
            $table->decimal('report_compliance_pct', 5, 2)->default(100.00)->after('meta_ads_deadline_day'); // Report Harian, Mingguan, Bulanan
            $table->integer('rapat_review_target')->default(2)->after('report_compliance_pct'); // Rapat AMD 2x sebulan

            // Kategori Result
            $table->integer('ig_followers_growth_target')->default(150)->after('rapat_review_target'); // 150 follower baru IG
            $table->integer('tiktok_followers_growth_target')->default(150)->after('ig_followers_growth_target'); // 150 follower baru TikTok
            $table->decimal('prospect_digital_ratio_pct', 5, 2)->default(130.00)->after('tiktok_followers_growth_target'); // 130% target STU
            $table->integer('dealer_stu_target')->default(50)->after('prospect_digital_ratio_pct'); // Base STU Target Dealer
            $table->decimal('ads_cpm_max', 12, 2)->default(30000.00)->after('dealer_stu_target'); // Maks Rp 30.000
            $table->decimal('ads_cpc_max', 12, 2)->default(5000.00)->after('ads_cpm_max'); // Maks Rp 5.000
            $table->decimal('ads_ctr_min', 5, 2)->default(1.50)->after('ads_cpc_max'); // Min 1.5%
            $table->decimal('stu_digital_ratio_pct', 5, 2)->default(15.00)->after('ads_ctr_min'); // Min STU 15% dari target dealer

            // Syarat Pembayaran
            $table->decimal('min_result_score', 5, 2)->default(60.00)->after('stu_digital_ratio_pct'); // Result min 60
            $table->decimal('min_proses_score', 5, 2)->default(80.00)->after('min_result_score'); // Proses min 80
            
            // Optional branch-specific STU target overrides
            $table->json('branch_dealer_stu_targets')->nullable()->after('min_proses_score');
        });
    }

    public function down(): void
    {
        Schema::table('kpi_targets', function (Blueprint $table) {
            $table->dropColumn([
                'insentif_pokok',
                'sosmed_content_plan_pct',
                'sales_digital_posting_target',
                'tiktok_live_daily_hours',
                'google_foto_daily_target',
                'meta_ads_deadline_day',
                'report_compliance_pct',
                'rapat_review_target',
                'ig_followers_growth_target',
                'tiktok_followers_growth_target',
                'prospect_digital_ratio_pct',
                'dealer_stu_target',
                'ads_cpm_max',
                'ads_cpc_max',
                'ads_ctr_min',
                'stu_digital_ratio_pct',
                'min_result_score',
                'min_proses_score',
                'branch_dealer_stu_targets',
            ]);
        });
    }
};
