<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\DailyReport;
use App\Models\Kpi;
use App\Models\KpiTarget;
use App\Models\MonthlyInsight;
use App\Models\MonthlyInsightScreenshot;
use App\Models\PersonalKpi;
use App\Models\Todo;
use App\Models\User;
use App\Models\WeeklyReport;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles & Permissions setup
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $areaManagerRole = Role::firstOrCreate(['name' => 'Area Manager']);
        $picRole = Role::firstOrCreate(['name' => 'PIC Digital Cabang']);

        // 2. Create 5 Branches (PT Aspacindo Kedaton Motor Yamaha Dealers)
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
        ];

        $branches = collect();
        foreach ($branchesData as $bData) {
            $branches->push(Branch::create($bData));
        }

        // 3. Create Super Admin User
        $superAdmin = User::create([
            'name' => 'Dimas Prayoga (Area Marketing Development)',
            'email' => 'superadmin@aspacindo.co.id',
            'password' => Hash::make('password123'),
            'branch_id' => null,
            'status' => 'active',
        ]);
        $superAdmin->assignRole($superAdminRole);

        // 4. Create Area Manager User
        $areaManager = User::create([
            'name' => 'Bambang Sukoco (General Manager Marketing)',
            'email' => 'areamanager@aspacindo.co.id',
            'password' => Hash::make('password123'),
            'branch_id' => null,
            'status' => 'active',
        ]);
        $areaManager->assignRole($areaManagerRole);

        // 5. Create 5 PIC Digital Users (1 per branch) - Supporting both simple and pic. prefix email formats
        $pics = collect();
        $picSlugList = ['pekanbaru', 'sorek', 'kandis', 'sungaipagar', 'medan'];
        foreach ($branches as $index => $branch) {
            $slug = $picSlugList[$index];

            // Primary Account (e.g. sungaipagar@aspacindo.co.id)
            $picUser = User::create([
                'name' => 'PIC Digital ' . $branch->nama_cabang,
                'email' => $slug . '@aspacindo.co.id',
                'password' => Hash::make('password123'),
                'branch_id' => $branch->id,
                'status' => 'active',
            ]);
            $picUser->assignRole($picRole);
            $pics->push($picUser);

            // Alias Account with pic. prefix (e.g. pic.sungaipagar@aspacindo.co.id)
            $picUserAlias = User::create([
                'name' => 'PIC Digital ' . $branch->nama_cabang . ' (Alias)',
                'email' => 'pic.' . $slug . '@aspacindo.co.id',
                'password' => Hash::make('password123'),
                'branch_id' => $branch->id,
                'status' => 'active',
            ]);
            $picUserAlias->assignRole($picRole);
        }

        // 6. Create Monthly KPIs for June & July 2026
        $months = [
            ['tahun' => 2026, 'bulan' => 6],
            ['tahun' => 2026, 'bulan' => 7],
        ];

        foreach ($months as $m) {
            $kpi = Kpi::create([
                'tahun' => $m['tahun'],
                'bulan' => $m['bulan'],
                'created_by' => $superAdmin->id,
            ]);

            KpiTarget::create([
                'kpi_id' => $kpi->id,
                'ig_feed_target' => 30,
                'ig_reels_target' => 20,
                'ig_story_target' => 60,
                'ig_followers_target' => 1500,
                'fb_post_target' => 30,
                'fb_marketplace_target' => 45,
                'fb_followers_target' => 800,
                'tiktok_post_target' => 25,
                'tiktok_live_target' => 15,
                'tiktok_followers_target' => 2000,
                'google_rating_target' => 4.8,
                'google_review_target' => 50,
            ]);
        }

        // 7. Seed Daily Reports for 30 consecutive days up to current date (July 25, 2026)
        $startDate = Carbon::create(2026, 6, 26);
        $endDate = Carbon::create(2026, 7, 25);

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            foreach ($branches as $bIndex => $branch) {
                // Let branch 4 (AKM Medan) miss input on today (July 25) to test the Missing Report Widget!
                if ($date->format('Y-m-d') === $endDate->format('Y-m-d') && $bIndex === 4) {
                    continue; // Skip AKM Medan today
                }

                $picUser = $pics->firstWhere('branch_id', $branch->id);

                // Vary activity realistic performance per branch
                $multiplier = 1.0 - ($bIndex * 0.08);

                DailyReport::create([
                    'branch_id' => $branch->id,
                    'user_id' => $picUser->id,
                    'tanggal' => $date->format('Y-m-d'),
                    'ig_feed' => rand(1, 2),
                    'ig_reels' => rand(0, 2),
                    'ig_story' => rand(2, 4),
                    'ig_followers_gained' => (int) (rand(40, 80) * $multiplier),
                    'fb_post' => rand(1, 2),
                    'fb_marketplace' => rand(1, 3),
                    'fb_followers_gained' => (int) (rand(20, 50) * $multiplier),
                    'tiktok_post' => rand(0, 2),
                    'tiktok_live' => rand(0, 1),
                    'tiktok_followers_gained' => (int) (rand(50, 110) * $multiplier),
                    'google_rating' => rand(47, 50) / 10.0,
                    'google_review_gained' => rand(1, 3),
                    'catatan' => 'Aktivitas promosi harian produk Yamaha NMAX Turbo & Aerox Alpha di cabang ' . $branch->nama_cabang,
                ]);
            }
        }

        // 8. Monthly Insights & Screenshots (June & July 2026)
        foreach ($branches as $bIndex => $branch) {
            $picUser = $pics->firstWhere('branch_id', $branch->id);

            $insight = MonthlyInsight::create([
                'branch_id' => $branch->id,
                'user_id' => $picUser->id,
                'tahun' => 2026,
                'bulan' => 7,
                'ig_views' => rand(250000, 500000),
                'ig_reach' => rand(180000, 350000),
                'ig_accounts_reached' => rand(120000, 280000),
                'ig_profile_visits' => rand(15000, 35000),
                'ig_total_followers' => rand(12000, 25000),
                'ig_male_pct' => 62.5,
                'ig_female_pct' => 37.5,
                'ig_top_age' => '18-24 (42%), 25-34 (38%), 35-44 (15%)',
                'ig_top_cities' => 'Pekanbaru (55%), Medan (22%), Pelalawan (12%)',
                'fb_views' => rand(120000, 300000),
                'fb_total_followers' => rand(8000, 18000),
                'tiktok_views' => rand(400000, 900000),
                'tiktok_total_followers' => rand(15000, 35000),
                'google_total_rating' => 4.9,
                'google_total_reviews' => rand(200, 500),
            ]);

            // Create placeholder screenshots metadata
            $kategoriList = ['Instagram Insight', 'Facebook Insight', 'TikTok Analytics', 'Google Business'];
            foreach ($kategoriList as $kat) {
                MonthlyInsightScreenshot::create([
                    'monthly_insight_id' => $insight->id,
                    'kategori' => $kat,
                    'file_path' => 'screenshots/sample_' . strtolower(str_replace(' ', '_', $kat)) . '.jpg',
                    'file_name' => 'Insight_' . $kat . '_' . $branch->kode . '.jpg',
                ]);
            }

            // Create 3 Weekly Post Insights per branch for July 2026
            for ($w = 1; $w <= 3; $w++) {
                $postDate = Carbon::create(2026, 7, $w * 7);
                WeeklyReport::create([
                    'branch_id' => $branch->id,
                    'user_id' => $picUser->id,
                    'tanggal_post' => $postDate->format('Y-m-d'),
                    'link_content' => 'https://www.instagram.com/p/C9' . rand(10000, 99999) . 'X' . $branch->id,
                    'start_date' => $postDate->copy()->startOfWeek()->format('Y-m-d'),
                    'end_date' => $postDate->copy()->endOfWeek()->format('Y-m-d'),
                    'tahun' => 2026,
                    'minggu_ke' => 27 + $w,
                    'views' => rand(35000, 120000),
                    'account_reached' => rand(25000, 85000),
                    'interactions_followers' => rand(2000, 6000),
                    'interactions_non_followers' => rand(1500, 5000),
                    'likes' => rand(1200, 4500),
                    'shares' => rand(300, 1200),
                    'saves' => rand(250, 900),
                    'comments' => rand(150, 600),
                    'reposts' => rand(100, 400),
                    'profile_visits' => rand(1200, 3800),
                    'external_link_taps' => rand(150, 750),
                    'follows' => rand(180, 650),
                    'source_feed_pct' => 65.5,
                    'source_profile_pct' => 22.0,
                    'source_stories_pct' => 12.5,
                    'gender_men_pct' => 58.0,
                    'gender_women_pct' => 42.0,
                    'top_country' => 'Indonesia (94%), Malaysia (4%)',
                    'top_age' => '18-24 (45%), 25-34 (38%), 35-44 (12%)',
                    'catatan' => 'Postingan campaign promosi NMAX Turbo minggu ke-' . (27 + $w) . ' cabang ' . $branch->nama_cabang,
                ]);
            }
        }

        // 9. Kanban To-Do List (Super Admin)
        $todos = [
            [
                'user_id' => $superAdmin->id,
                'judul' => 'Evaluasi Campaign Launching NMAX Turbo',
                'deskripsi' => 'Review efektivitas postingan Reels dan TikTok Live cabang AKM Pekanbaru & AKM Sorek.',
                'priority' => 'High',
                'deadline' => '2026-07-28',
                'status' => 'To Do',
                'color_badge' => '#dc3545',
                'position' => 1,
            ],
            [
                'user_id' => $superAdmin->id,
                'judul' => 'Audit Media Sosial Cabang AKM Medan',
                'deskripsi' => 'Pemeriksaan rutin kepatuhan jadwal upload daily report dan kualitas feed.',
                'priority' => 'High',
                'deadline' => '2026-07-29',
                'status' => 'Progress',
                'color_badge' => '#ffc107',
                'position' => 1,
            ],
            [
                'user_id' => $superAdmin->id,
                'judul' => 'Distribusi Asset Graphic Banner Promo Agustus',
                'deskripsi' => 'Kirim materi design banner promo Hari Kemerdekaan ke seluruh PIC Digital cabang.',
                'priority' => 'Medium',
                'deadline' => '2026-07-30',
                'status' => 'Progress',
                'color_badge' => '#0d6efd',
                'position' => 2,
            ],
            [
                'user_id' => $superAdmin->id,
                'judul' => 'Pelatihan Video Editing CapCut untuk PIC Cabang',
                'deskripsi' => 'Sesi webinar internal peningkatan kemampuan produksi Reels & TikTok.',
                'priority' => 'Medium',
                'deadline' => '2026-07-20',
                'status' => 'Done',
                'color_badge' => '#198754',
                'position' => 1,
            ],
        ];

        foreach ($todos as $t) {
            Todo::create($t);
        }

        // 10. Personal KPI Super Admin
        $personalKpis = [
            ['kategori' => 'Training PIC Cabang', 'target' => 4, 'realisasi' => 3],
            ['kategori' => 'Audit Digital Cabang', 'target' => 5, 'realisasi' => 4],
            ['kategori' => 'Kunjungan Lapangan', 'target' => 3, 'realisasi' => 3],
            ['kategori' => 'Campaign Promo Digital', 'target' => 2, 'realisasi' => 2],
            ['kategori' => 'Meeting Coordinasi Bulanan', 'target' => 4, 'realisasi' => 4],
            ['kategori' => 'SOP & Guideline Content', 'target' => 1, 'realisasi' => 1],
            ['kategori' => 'Project Digital Optimization', 'target' => 2, 'realisasi' => 1],
        ];

        foreach ($personalKpis as $pk) {
            PersonalKpi::create([
                'user_id' => $superAdmin->id,
                'tahun' => 2026,
                'bulan' => 7,
                'kategori' => $pk['kategori'],
                'target' => $pk['target'],
                'realisasi' => $pk['realisasi'],
            ]);
        }

        // 11. Audit Logs Initial Data
        $auditLogs = [
            ['user_id' => $superAdmin->id, 'user_name' => $superAdmin->name, 'action' => 'LOGIN', 'module' => 'Authentication', 'description' => 'User Super Admin login ke sistem', 'ip_address' => '127.0.0.1'],
            ['user_id' => $superAdmin->id, 'user_name' => $superAdmin->name, 'action' => 'COPY_KPI', 'module' => 'KPI Management', 'description' => 'Copy KPI dari bulan Juni 2026 ke Juli 2026', 'ip_address' => '127.0.0.1'],
            ['user_id' => $pics[0]->id, 'user_name' => $pics[0]->name, 'action' => 'CREATE', 'module' => 'Daily Report', 'description' => 'Input Daily Report tanggal 25 Juli 2026 cabang AKM Pekanbaru', 'ip_address' => '127.0.0.1'],
            ['user_id' => $pics[1]->id, 'user_name' => $pics[1]->name, 'action' => 'CREATE', 'module' => 'Monthly Insight', 'description' => 'Input Monthly Insight Juli 2026 cabang AKM Sorek', 'ip_address' => '127.0.0.1'],
        ];

        foreach ($auditLogs as $al) {
            AuditLog::create($al);
        }
    }
}
