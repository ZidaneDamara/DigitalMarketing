<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\GoogleRatingMonth;
use App\Models\GoogleRatingTestimonial;
use App\Models\MetaAdsReport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MetaAdsAndGoogleRatingSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::where('email', 'superadmin@aspacindo.co.id')->first() ?? User::first();
        $branches = Branch::where('status', 'active')->get();

        if ($branches->isEmpty()) {
            return;
        }

        // 1. Meta Ads Reports Seeder (2026 months)
        $campaignTemplates = [
            [
                'nama' => 'Promo NMAX Turbo Gaspol Awal Bulan',
                'product' => 'NMAX Turbo',
                'budget' => 4500000,
                'views' => 78500,
                'percakapan' => 195,
                'ctr' => 2.85,
                'cpc' => 1450,
                'cpm' => 18500,
                'cpl' => 23076,
                'stu' => 18,
                'catatan' => 'Respon calon konsumen sangat antusias terhadap fitur Turbo Riding Mode dan promo DP terjangkau.',
            ],
            [
                'nama' => 'Aerox 155 Connected Cyber City Special',
                'product' => 'Aerox 155 Connected',
                'budget' => 3800000,
                'views' => 64200,
                'percakapan' => 160,
                'ctr' => 2.40,
                'cpc' => 1650,
                'cpm' => 21000,
                'cpl' => 23750,
                'stu' => 14,
                'catatan' => 'Segmentasi anak muda usia 18-28 tahun berhasil meningkatkan interaksi chat WhatsApp.',
            ],
            [
                'nama' => 'Classy Yamaha Grand Filano & Fazzio Hybrid',
                'product' => 'Grand Filano Hybrid',
                'budget' => 3200000,
                'views' => 52000,
                'percakapan' => 135,
                'ctr' => 3.10,
                'cpc' => 1320,
                'cpm' => 16000,
                'cpl' => 23700,
                'stu' => 12,
                'catatan' => 'Iklan visual estetik gaya hidup perkotaan menghasilkan leads wanita & profesional muda.',
            ],
            [
                'nama' => 'Fazzio Smart & Trendy Promo Hemat',
                'product' => 'Fazzio Hybrid',
                'budget' => 2800000,
                'views' => 48000,
                'percakapan' => 125,
                'ctr' => 2.95,
                'cpc' => 1280,
                'cpm' => 15500,
                'cpl' => 22400,
                'stu' => 11,
                'catatan' => 'Program bundling jaket eksklusif dan servis gratis menarik minat pembeli pertama.',
            ],
            [
                'nama' => 'XMAX Connected Maxi Touring Campaign',
                'product' => 'XMAX Connected',
                'budget' => 5000000,
                'views' => 85000,
                'percakapan' => 140,
                'ctr' => 2.15,
                'cpc' => 2100,
                'cpm' => 26500,
                'cpl' => 35714,
                'stu' => 9,
                'catatan' => 'Target segmen mapan, margin profit tinggi dengan rata-rata pembelian tunai dan kredit DP besar.',
            ],
        ];

        foreach ($branches as $bIndex => $branch) {
            $pic = User::where('branch_id', $branch->id)->first() ?? $superAdmin;

            // Generate reports for months 8, 9, 10 in 2026
            foreach ([8, 9, 10] as $m) {
                $template = $campaignTemplates[($bIndex + $m) % count($campaignTemplates)];
                
                MetaAdsReport::updateOrCreate(
                    [
                        'branch_id' => $branch->id,
                        'bulan' => $m,
                        'tahun' => 2026,
                        'nama_iklan' => $template['nama'] . ' - ' . $branch->nama_cabang,
                    ],
                    [
                        'user_id' => $pic->id,
                        'tanggal_laporan' => Carbon::create(2026, $m, 15)->toDateString(),
                        'product' => $template['product'],
                        'ctr' => $template['ctr'],
                        'cpc' => $template['cpc'],
                        'cpm' => $template['cpm'],
                        'cpl' => $template['cpl'],
                        'total_views' => $template['views'] + ($bIndex * 3500),
                        'total_percakapan' => $template['percakapan'] + ($bIndex * 8),
                        'budget' => $template['budget'] + ($bIndex * 200000),
                        'stu' => $template['stu'] + ($bIndex * 2),
                        'catatan' => $template['catatan'],
                    ]
                );
            }

            // 2. Google Rating Awal Bulan (Baseline for 2026)
            foreach ([8, 9, 10] as $m) {
                $baseRating = 4.80 + ($bIndex * 0.02);
                $baseReviews = 450 + ($bIndex * 110) + (($m - 8) * 125);

                $grMonth = GoogleRatingMonth::updateOrCreate(
                    [
                        'branch_id' => $branch->id,
                        'bulan' => $m,
                        'tahun' => 2026,
                    ],
                    [
                        'user_id' => $pic->id,
                        'rating_awal_bulan' => $baseRating,
                        'total_review_awal_bulan' => $baseReviews,
                        'target_bulanan' => 125,
                        'target_harian' => 5,
                        'rating_saat_ini' => min(5.0, $baseRating + 0.05),
                        'catatan' => 'Fokus meminta review bintang 5 ke setiap konsumen yang servis berkala dan serah terima unit motor baru.',
                    ]
                );

                // For October 2026, generate sample daily testimonials
                if ($m === 10) {
                    $customerReviews = [
                        ['name' => 'Hendra Gunawan', 'star' => 5, 'comment' => 'Pelayanan sales ramah sekali, unit NMAX Turbo sampai tepat waktu dan dapat merchandise gratis!'],
                        ['name' => 'Siti Rahmawati', 'star' => 5, 'comment' => 'Beli Fazzio proses kredit cepat dan dibantu sampai acc. Dealer bersih dan ruang tunggu dingin.'],
                        ['name' => 'Budi Santoso', 'star' => 5, 'comment' => 'Bengkel resmi Yamaha terbaik di area ini, mekanik teliti dan penjelasan transparan.'],
                        ['name' => 'Rian Saputra', 'star' => 5, 'comment' => 'Aerox Cyber City keren abis! Terima kasih mbak sales sudah bantu pengurusan plat nomor cepat.'],
                        ['name' => 'Dewi Anggraini', 'star' => 5, 'comment' => 'Grand Filano hybrid nyaman banget dipake harian. Pelayanan dealer bintang lima!'],
                        ['name' => 'Agus Pratama', 'star' => 5, 'comment' => 'Selalu puas servis motor di sini, dapat diskon oli dan cuci motor gratis.'],
                        ['name' => 'Mega Lestari', 'star' => 5, 'comment' => 'Proses pembelian mudah dan staf sangat komunikatif lewat WhatsApp.'],
                    ];

                    // Seed reviews across days 1 to 20
                    for ($day = 1; $day <= 20; $day++) {
                        $dailyCount = ($day % 4 === 0) ? 3 : (($day % 2 === 0) ? 6 : 5);
                        for ($k = 0; $k < $dailyCount; $k++) {
                            $sample = $customerReviews[($day + $k) % count($customerReviews)];
                            GoogleRatingTestimonial::firstOrCreate(
                                [
                                    'branch_id' => $branch->id,
                                    'tanggal' => Carbon::create(2026, 10, $day)->toDateString(),
                                    'nama_reviewer' => $sample['name'] . " (Tgl {$day}-#{$k})",
                                ],
                                [
                                    'google_rating_month_id' => $grMonth->id,
                                    'user_id' => $pic->id,
                                    'bulan' => 10,
                                    'tahun' => 2026,
                                    'rating_bintang' => $sample['star'],
                                    'foto_testimoni' => 'screenshots/google_rating/sample_review.png',
                                    'komentar_ulasan' => $sample['comment'],
                                ]
                            );
                        }
                    }
                }
            }
        }
    }
}
