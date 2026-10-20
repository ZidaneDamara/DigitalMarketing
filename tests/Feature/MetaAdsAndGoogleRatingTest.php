<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\GoogleRatingMonth;
use App\Models\GoogleRatingTestimonial;
use App\Models\MetaAdsReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MetaAdsAndGoogleRatingTest extends TestCase
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

    public function test_meta_ads_dashboard_accessible()
    {
        $response = $this->actingAs($this->user)->get(route('dashboard.meta-ads'));
        $response->assertStatus(200);
        $response->assertSee('Executive Dashboard Meta Ads');
    }

    public function test_meta_ads_report_index_accessible()
    {
        $response = $this->actingAs($this->user)->get(route('reports.meta-ads.index'));
        $response->assertStatus(200);
        $response->assertSee('Monthly Report Meta Ads');
    }

    public function test_meta_ads_report_store()
    {
        Storage::fake('public');

        $topupFile = UploadedFile::fake()->create('topup.jpg', 100, 'image/jpeg');
        $closingFile1 = UploadedFile::fake()->create('closing1.jpg', 100, 'image/jpeg');
        $closingFile2 = UploadedFile::fake()->create('closing2.jpg', 100, 'image/jpeg');

        $payload = [
            'branch_id' => $this->branch->id,
            'bulan' => 10,
            'tahun' => 2026,
            'nama_iklan' => 'Campaign Test Feature NMAX',
            'product' => 'NMAX Turbo',
            'ctr' => 2.50,
            'cpc' => 1500,
            'cpm' => 18000,
            'cpl' => 25000,
            'total_views' => 50000,
            'total_percakapan' => 120,
            'budget' => 3000000,
            'stu' => 10,
            'catatan' => 'Test catatan iklan',
            'bukti_topup' => $topupFile,
            'bukti_closing_stu' => [$closingFile1, $closingFile2],
        ];

        $response = $this->actingAs($this->user)->post(route('reports.meta-ads.store'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('meta_ads_reports', [
            'nama_iklan' => 'Campaign Test Feature NMAX',
            'product' => 'NMAX Turbo',
            'stu' => 10,
        ]);

        $report = MetaAdsReport::where('nama_iklan', 'Campaign Test Feature NMAX')->first();
        $this->assertCount(2, $report->closingProofs);
    }

    public function test_google_rating_report_index_accessible()
    {
        $response = $this->actingAs($this->user)->get(route('reports.google-rating.index'));
        $response->assertStatus(200);
        $response->assertSee('Report Google Rating');
        $response->assertSee('Testimoni Cabang');
        $response->assertSee('Minimal 5');
    }

    public function test_google_rating_baseline_store()
    {
        $payload = [
            'branch_id' => $this->branch->id,
            'bulan' => 10,
            'tahun' => 2026,
            'rating_awal_bulan' => 4.85,
            'total_review_awal_bulan' => 870,
            'rating_saat_ini' => 4.90,
            'catatan' => 'Target peningkatan review',
        ];

        $response = $this->actingAs($this->user)->post(route('reports.google-rating.baseline.store'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('google_rating_months', [
            'branch_id' => $this->branch->id,
            'bulan' => 10,
            'tahun' => 2026,
            'rating_awal_bulan' => 4.85,
            'total_review_awal_bulan' => 870,
        ]);
    }

    public function test_google_rating_testimonial_store_with_warning_logic()
    {
        Storage::fake('public');

        $photo1 = UploadedFile::fake()->create('review1.jpg', 100, 'image/jpeg');
        $photo2 = UploadedFile::fake()->create('review2.jpg', 100, 'image/jpeg');

        $payload = [
            'branch_id' => $this->branch->id,
            'tanggal' => now()->toDateString(),
            'nama_reviewer' => 'Test Reviewer Yamaha',
            'rating_bintang' => 5,
            'komentar_ulasan' => 'Pelayanan ramah dan profesional',
            'fotos' => [$photo1, $photo2],
        ];

        $response = $this->actingAs($this->user)->post(route('reports.google-rating.testimonial.store'), $payload);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'today_count',
            'is_target_met',
            'deficiency',
            'warning_message',
        ]);
    }

    public function test_meta_ads_report_store_with_pdf_invoice()
    {
        Storage::fake('public');

        $pdfInvoice = UploadedFile::fake()->create('invoice_topup.pdf', 500, 'application/pdf');

        $payload = [
            'branch_id' => $this->branch->id,
            'bulan' => 10,
            'tahun' => 2026,
            'nama_iklan' => 'Campaign Turbo PDF Invoice',
            'product' => 'NMAX Turbo',
            'ctr' => 3.10,
            'cpc' => 1200,
            'cpm' => 14000,
            'cpl' => 20000,
            'total_views' => 60000,
            'total_percakapan' => 150,
            'budget' => 3000000,
            'stu' => 12,
            'catatan' => 'Invoice resmi PDF topup',
            'bukti_topup' => $pdfInvoice,
        ];

        $response = $this->actingAs($this->user)->post(route('reports.meta-ads.store'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $report = MetaAdsReport::where('nama_iklan', 'Campaign Turbo PDF Invoice')->first();
        $this->assertNotNull($report);
        $this->assertTrue($report->is_bukti_topup_pdf);
    }

    public function test_meta_ads_prospect_store_and_index()
    {
        // Test Store Prospect with fields: nama, nomor_hp, sumber_leads (Instagram, Facebook, WhatsApp)
        $payload = [
            'branch_id' => $this->branch->id,
            'nama' => 'Budi Santoso',
            'nomor_hp' => '081234567890',
            'sumber_leads' => 'Instagram',
            'tanggal_input' => now()->toDateString(),
            'catatan' => 'Minat NMAX Turbo',
        ];

        $response = $this->actingAs($this->user)->post(route('reports.meta-ads.prospects.store'), $payload);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('meta_ads_prospects', [
            'nama' => 'Budi Santoso',
            'nomor_hp' => '081234567890',
            'sumber_leads' => 'Instagram',
        ]);

        // Test Prospects DataTables Endpoint
        $ajaxResponse = $this->actingAs($this->user)->getJson(route('reports.meta-ads.prospects'));
        $ajaxResponse->assertStatus(200);
        $ajaxResponse->assertJsonStructure(['data']);
    }
}

