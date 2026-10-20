<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\DailyReport;
use App\Models\GoogleRatingMonth;
use App\Models\GoogleRatingTestimonial;
use App\Models\Kpi;
use App\Models\KpiTarget;
use App\Models\MetaAdsProspect;
use App\Models\MetaAdsReport;
use App\Models\TiktokLiveReport;
use App\Models\User;
use App\Services\KpiEvaluationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KpiManagementPicDigitalTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $branch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $this->user = User::where('email', 'superadmin@aspacindo.co.id')->first();
        $this->branch = Branch::where('status', 'active')->first();
    }

    public function test_kpi_management_page_accessible()
    {
        $response = $this->actingAs($this->user)->get(route('kpis.index', ['tahun' => 2026, 'bulan' => 10]));
        $response->assertStatus(200);
        $response->assertSee('KPI PIC Digital');
        $response->assertSee('Hasil Evaluasi Otomatis Cabang');
        $response->assertSee('SYARAT PEMBAYARAN KPI PIC DIGITAL');
    }

    public function test_store_or_update_kpi_pic_digital_targets()
    {
        $payload = [
            'tahun' => 2026,
            'bulan' => 10,
            'insentif_pokok' => 500000,
            // Kategori Proses
            'sosmed_content_plan_pct' => 100,
            'sales_digital_posting_target' => 2,
            'tiktok_live_daily_hours' => 3.0,
            'google_review_target' => 100,
            'google_rating_target' => 4.8,
            'google_foto_daily_target' => 4,
            'meta_ads_deadline_day' => 26,
            'report_compliance_pct' => 100,
            'rapat_review_target' => 2,
            // Kategori Result
            'ig_followers_growth_target' => 150,
            'tiktok_followers_growth_target' => 150,
            'prospect_digital_ratio_pct' => 130,
            'dealer_stu_target' => 50,
            'ads_cpm_max' => 30000,
            'ads_cpc_max' => 5000,
            'ads_ctr_min' => 1.5,
            'stu_digital_ratio_pct' => 15.0,
            // Syarat Pembayaran
            'min_result_score' => 60.0,
            'min_proses_score' => 80.0,
        ];

        $response = $this->actingAs($this->user)->post(route('kpis.store'), $payload);
        $response->assertRedirect(route('kpis.index', ['tahun' => 2026, 'bulan' => 10]));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('kpi_targets', [
            'insentif_pokok' => 500000,
            'sosmed_content_plan_pct' => 100,
            'sales_digital_posting_target' => 2,
            'tiktok_live_daily_hours' => 3.0,
            'google_review_target' => 100,
            'ads_cpm_max' => 30000,
            'ads_cpc_max' => 5000,
            'ads_ctr_min' => 1.5,
        ]);
    }

    public function test_branch_evaluation_json_endpoint()
    {
        $response = $this->actingAs($this->user)->get(route('kpis.branch.evaluation', [
            'branch' => $this->branch->id,
            'tahun' => 2026,
            'bulan' => 10,
        ]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'branch',
                'tahun',
                'bulan',
                'insentif_pokok',
                'insentif_diterima',
                'proses_items',
                'result_items',
                'skor_proses_murni',
                'skor_proses_efektif',
                'skor_result',
                'is_result_eligible',
                'is_proses_eligible',
                'is_full_payment_eligible',
                'payment_status',
            ]
        ]);
    }

    public function test_evaluation_service_rule_enforcement()
    {
        $service = app(KpiEvaluationService::class);
        $eval = $service->evaluateBranch($this->branch, 2026, 10);

        // Without activity, Result is < 60
        $this->assertFalse($eval['is_result_eligible']);
        // Rule 3: If Result < 60, Proses TIDAK dihitung / dianggap 0
        $this->assertEquals(0.0, $eval['skor_proses_efektif']);
        // Rule 4: Insentif is 0
        $this->assertEquals(0.0, $eval['insentif_diterima']);
        $this->assertFalse($eval['is_full_payment_eligible']);
        $this->assertStringContainsString('GUGUR', $eval['payment_status']);
    }

    public function test_evaluation_service_eligible_scenario()
    {
        $service = app(KpiEvaluationService::class);

        // Seed data satisfying Result >= 60 and Process >= 80 for this branch in 2026-10
        // Result items:
        // 1. IG & TikTok follower growth: 150 each
        // 2. Meta Ads report with good CTR (3.0%), CPM (20,000), CPC (2,000), and STU (10)
        // 3. Meta Ads prospects (70 prospects >= 130% * 50 = 65)
        MetaAdsReport::create([
            'branch_id' => $this->branch->id,
            'bulan' => 10,
            'tahun' => 2026,
            'nama_iklan' => 'Campaign Top Performer',
            'product' => 'Yamaha Aerox Alpha',
            'ctr' => 3.2,
            'cpc' => 2500,
            'cpm' => 18000,
            'cpl' => 25000,
            'total_views' => 50000,
            'total_percakapan' => 85,
            'budget' => 2000000,
            'bukti_topup' => 'proofs/topup.jpg',
            'user_id' => $this->user->id,
            'stu' => 12,
            'tanggal_laporan' => '2026-10-15',
        ]);

        // Daily Reports for full month (posting & follower growth)
        for ($d = 1; $d <= 31; $d++) {
            DailyReport::create([
                'branch_id' => $this->branch->id,
                'user_id' => $this->user->id,
                'tanggal' => Carbon::create(2026, 10, $d)->toDateString(),
                'ig_feed' => 1,
                'ig_reels' => 1,
                'fb_post' => 1,
                'tiktok_post' => 1,
                'ig_followers_gained' => 5, // 5 * 31 = 155 >= 150
                'tiktok_followers_gained' => 5, // 5 * 31 = 155 >= 150
                'google_review_gained' => 4, // 4 * 31 = 124 >= 100
                'google_rating' => 4.9,
            ]);

            TiktokLiveReport::create([
                'branch_id' => $this->branch->id,
                'user_id' => $this->user->id,
                'tanggal_live' => Carbon::create(2026, 10, $d)->toDateString(),
                'nama_host' => 'Host Top',
                'jabatan' => 'PIC Digital Cabang',
                'durasi_jam' => 3,
                'durasi_menit' => 30, // 210 minutes > 180 minutes (3.5 hours/day)
                'jumlah_penonton' => 1000,
                'stu' => 0,
            ]);
        }

        // Testimonials
        for ($i = 1; $i <= 65; $i++) {
            GoogleRatingTestimonial::create([
                'branch_id' => $this->branch->id,
                'user_id' => $this->user->id,
                'nama_reviewer' => "Customer {$i}",
                'rating_bintang' => 5,
                'komentar_ulasan' => 'Sangat ramah dan cepat',
                'foto_testimoni' => 'proofs/testi.jpg',
                'tanggal' => '2026-10-10',
                'bulan' => 10,
                'tahun' => 2026,
            ]);
        }

        $eval = $service->evaluateBranch($this->branch, 2026, 10);

        // Result must be >= 60
        $this->assertGreaterThanOrEqual(60.0, $eval['skor_result']);
        $this->assertTrue($eval['is_result_eligible']);

        // Process must be >= 80
        $this->assertGreaterThanOrEqual(80.0, $eval['skor_proses_efektif']);
        $this->assertTrue($eval['is_proses_eligible']);

        // Eligible for full Rp 500.000 payment
        $this->assertTrue($eval['is_full_payment_eligible']);
        $this->assertEquals(500000.0, $eval['insentif_diterima']);
        $this->assertEquals('LOLOS PEMBAYARAN PENUH', $eval['payment_status']);
    }
}
