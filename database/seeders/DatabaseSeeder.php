<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles & Permissions setup
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $areaManagerRole = Role::firstOrCreate(['name' => 'Area Manager']);
        $picRole = Role::firstOrCreate(['name' => 'PIC Digital Cabang']);

        // 2. Create 7 Branches (PT Aspacindo Kedaton Motor Yamaha Dealers)
        $branchesData = [
            [
                'kode' => 'AKM-PKU-01',
                'nama_cabang' => 'AKM Pekanbaru',
                'alamat' => 'Jl. Jendral Sudirman No. 120, Pekanbaru, Riau',
                'area' => 'Pekanbaru City',
                'manager_name' => 'Budi Santoso, S.E.',
                'status' => 'active',
            ],
            [
                'kode' => 'AKM-SRK-02',
                'nama_cabang' => 'AKM Sorek',
                'alamat' => 'Jl. Lintas Timur No. 45, Sorek Satu, Pelalawan, Riau',
                'area' => 'Pelalawan & Sorek',
                'manager_name' => 'Hendra Wijaya',
                'status' => 'active',
            ],
            [
                'kode' => 'AKM-KND-03',
                'nama_cabang' => 'AKM Kandis',
                'alamat' => 'Jl. Raya Pekanbaru-Duri Km 72, Kandis, Siak, Riau',
                'area' => 'Siak & Kandis',
                'manager_name' => 'Siti Nurhaliza, S.E.',
                'status' => 'active',
            ],
            [
                'kode' => 'AKM-SPG-04',
                'nama_cabang' => 'AKM Sungai Pagar',
                'alamat' => 'Jl. Poros Kampar Kiri No. 88, Sungai Pagar, Kampar, Riau',
                'area' => 'Kampar & Sungai Pagar',
                'manager_name' => 'Rahmat Hidayat',
                'status' => 'active',
            ],
            [
                'kode' => 'AKM-MDN-05',
                'nama_cabang' => 'AKM Medan',
                'alamat' => 'Jl. Sisingamangaraja No. 210, Medan, Sumatera Utara',
                'area' => 'Medan & North Sumatra',
                'manager_name' => 'Agus Pratama',
                'status' => 'active',
            ],
            [
                'kode' => 'AKM-AMK-06',
                'nama_cabang' => 'AKM Air Molek',
                'alamat' => 'Jl. Jendral Sudirman No. 34, Air Molek, Indragiri Hulu, Riau',
                'area' => 'Indragiri Hulu & Air Molek',
                'manager_name' => 'Dedi Kurniawan',
                'status' => 'active',
            ],
            [
                'kode' => 'AKM-TBL-07',
                'nama_cabang' => 'AKM Tembilahan',
                'alamat' => 'Jl. M. Boya No. 56, Tembilahan, Indragiri Hilir, Riau',
                'area' => 'Indragiri Hilir & Tembilahan',
                'manager_name' => 'Faisal Rahman',
                'status' => 'active',
            ],
        ];

        $branches = collect();
        foreach ($branchesData as $bData) {
            $branches->push(Branch::updateOrCreate(['kode' => $bData['kode']], $bData));
        }

        // 3. Create Super Admin Users
        $superAdmin1 = User::updateOrCreate(
            ['email' => 'zidane@aspacindo.co.id'],
            [
                'name' => 'Muhammad Zidane Damara',
                'password' => Hash::make('password123'),
                'branch_id' => null,
                'status' => 'active',
            ]
        );
        $superAdmin1->assignRole($superAdminRole);

        $superAdmin1Alias = User::updateOrCreate(
            ['email' => 'superadmin@aspacindo.co.id'],
            [
                'name' => 'Muhammad Zidane Damara (Super Admin)',
                'password' => Hash::make('password123'),
                'branch_id' => null,
                'status' => 'active',
            ]
        );
        $superAdmin1Alias->assignRole($superAdminRole);

        $superAdmin2 = User::updateOrCreate(
            ['email' => 'amalia@aspacindo.co.id'],
            [
                'name' => 'Amalia Putri',
                'password' => Hash::make('password123'),
                'branch_id' => null,
                'status' => 'active',
            ]
        );
        $superAdmin2->assignRole($superAdminRole);

        // 4. Create Area Manager User
        $areaManager = User::updateOrCreate(
            ['email' => 'suparman@aspacindo.co.id'],
            [
                'name' => 'Suparman',
                'password' => Hash::make('password123'),
                'branch_id' => null,
                'status' => 'active',
            ]
        );
        $areaManager->assignRole($areaManagerRole);

        $areaManagerAlias = User::updateOrCreate(
            ['email' => 'areamanager@aspacindo.co.id'],
            [
                'name' => 'Suparman (Area Manager)',
                'password' => Hash::make('password123'),
                'branch_id' => null,
                'status' => 'active',
            ]
        );
        $areaManagerAlias->assignRole($areaManagerRole);

        // 5. Create PIC Digital Users (1 per branch)
        $picSlugList = ['pekanbaru', 'sorek', 'kandis', 'sungaipagar', 'medan', 'airmolek', 'tembilahan'];
        foreach ($branches as $index => $branch) {
            $slug = $picSlugList[$index];

            $picUser = User::updateOrCreate(
                ['email' => $slug . '@aspacindo.co.id'],
                [
                    'name' => 'PIC Digital ' . $branch->nama_cabang,
                    'password' => Hash::make('password123'),
                    'branch_id' => $branch->id,
                    'status' => 'active',
                ]
            );
            $picUser->assignRole($picRole);

            $picUserAlias = User::updateOrCreate(
                ['email' => 'pic.' . $slug . '@aspacindo.co.id'],
                [
                    'name' => 'PIC Digital ' . $branch->nama_cabang . ' (Alias)',
                    'password' => Hash::make('password123'),
                    'branch_id' => $branch->id,
                    'status' => 'active',
                ]
            );
            $picUserAlias->assignRole($picRole);
        }

        // 6. Ensure default KPI Target for year 2026 with 7 Dealer STU Target overrides
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
        foreach (\App\Models\Branch::all() as $b) {
            foreach ($dealerTargetsMap as $name => $tgt) {
                if (str_contains(strtolower($b->nama_cabang), $name)) {
                    $branchOverrides[$b->id] = $tgt;
                }
            }
        }

        foreach (range(1, 12) as $m) {
            $kpi = \App\Models\Kpi::firstOrCreate(
                ['tahun' => 2026, 'bulan' => $m],
                ['created_by' => $superAdminUser?->id ?? 1]
            );

            \App\Models\KpiTarget::updateOrCreate(
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
