<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Kpi;
use App\Models\KpiTarget;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure default KPI Target for year 2026 with 7 Dealer STU Target overrides
        $superAdminUser = User::whereHas('roles', fn($q) => $q->where('name', 'Super Admin'))->first();
        $dealerTargetsMap = [
            'pekanbaru' => 95,
            'sungai pagar' => 75,
            'air molek' => 108,
            'sorek' => 177,
            'kandis' => 125,
            'medan' => 136,
            'tembilahan' => 75,
        ];

        $branchOverrides = [];
        foreach (Branch::all() as $b) {
            foreach ($dealerTargetsMap as $name => $tgt) {
                if (str_contains(strtolower($b->nama_cabang), $name)) {
                    $branchOverrides[$b->id] = $tgt;
                }
            }
        }

        foreach (range(1, 12) as $m) {
            $kpi = Kpi::firstOrCreate(
                ['tahun' => 2026, 'bulan' => $m],
                ['created_by' => $superAdminUser?->id ?? User::first()?->id ?? 1]
            );

            KpiTarget::updateOrCreate(
                ['kpi_id' => $kpi->id],
                [
                    'insentif_pokok' => 500000,
                    'sosmed_content_plan_pct' => 100,
                    'sales_digital_posting_target' => 2,
                    'tiktok_live_daily_hours' => 3.0,
                    'google_review_target' => 100,
                    'google_rating_target' => 4.8,
                    'google_foto_daily_target' => 4,
                    'meta_ads_deadline_day' => 26,
                    'report_compliance_pct' => 100,
                    'rapat_review_target' => 2,
                    'ig_followers_growth_target' => 150,
                    'tiktok_followers_growth_target' => 150,
                    'prospect_digital_ratio_pct' => 130.00,
                    'dealer_stu_target' => 100,
                    'ads_cpm_max' => 30000,
                    'ads_cpc_max' => 5000,
                    'ads_ctr_min' => 1.5,
                    'stu_digital_ratio_pct' => 15.00,
                    'min_result_score' => 60.0,
                    'min_proses_score' => 80.0,
                    'branch_dealer_stu_targets' => $branchOverrides,
                ]
            );
        }
    }
}
