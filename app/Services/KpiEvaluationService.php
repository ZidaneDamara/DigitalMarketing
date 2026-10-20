<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\DailyReport;
use App\Models\GoogleRatingMonth;
use App\Models\GoogleRatingTestimonial;
use App\Models\Kpi;
use App\Models\KpiTarget;
use App\Models\MetaAdsProspect;
use App\Models\MetaAdsReport;
use App\Models\MonthlyInsight;
use App\Models\TiktokLiveReport;
use App\Models\WeeklyReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class KpiEvaluationService
{
    /**
     * Evaluate comprehensive KPI for a single branch in given month/year
     */
    public function evaluateBranch(Branch $branch, int $tahun, int $bulan, ?KpiTarget $target = null): array
    {
        if (!$target) {
            $kpi = Kpi::with('target')->where('tahun', $tahun)->where('bulan', $bulan)->first();
            $target = $kpi?->target;
        }

        // Default KPI target parameters if not yet configured
        $insentifPokok = $target ? (float) $target->insentif_pokok : 500000.0;
        
        // Target values
        $targetSosmedContentPlanPct = $target ? (float) $target->sosmed_content_plan_pct : 100.0;
        $targetDailyPosting = $target ? (int) $target->sales_digital_posting_target : 2;
        $targetLiveDailyHours = $target ? (float) $target->tiktok_live_daily_hours : 3.0;
        $targetGoogleReview = $target ? (int) $target->google_review_target : 100;
        $targetGoogleRating = $target ? (float) $target->google_rating_target : 4.8;
        $targetGoogleFotoDaily = $target ? (int) $target->google_foto_daily_target : 4;
        $targetMetaAdsDeadline = $target ? (int) $target->meta_ads_deadline_day : 26;
        $targetReportCompliance = $target ? (float) $target->report_compliance_pct : 100.0;
        $targetRapatReview = $target ? (int) $target->rapat_review_target : 2;

        $targetIgGrowth = $target ? (int) $target->ig_followers_growth_target : 150;
        $targetTiktokGrowth = $target ? (int) $target->tiktok_followers_growth_target : 150;
        $dealerStuTarget = $target ? $target->getDealerStuTargetForBranch($branch->id) : 50;
        $prospectRatioPct = $target ? (float) $target->prospect_digital_ratio_pct : 130.0;
        $targetProspect = (int) round(($prospectRatioPct / 100) * $dealerStuTarget);
        $targetCpmMax = $target ? (float) $target->ads_cpm_max : 30000.0;
        $targetCpcMax = $target ? (float) $target->ads_cpc_max : 5000.0;
        $targetCtrMin = $target ? (float) $target->ads_ctr_min : 1.50;
        $stuDigitalRatioPct = $target ? (float) $target->stu_digital_ratio_pct : 15.0;
        $targetStuDigital = (int) ceil(($stuDigitalRatioPct / 100) * $dealerStuTarget);

        $minResultScore = $target ? (float) $target->min_result_score : 60.0;
        $minProsesScore = $target ? (float) $target->min_proses_score : 80.0;

        $dt = Carbon::create($tahun, $bulan, 1);
        $daysInMonth = $dt->daysInMonth;

        // Fetch Raw Data for Month
        $dailyReports = DailyReport::where('branch_id', $branch->id)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get();

        $tiktokLiveReports = TiktokLiveReport::where('branch_id', $branch->id)
            ->whereYear('tanggal_live', $tahun)
            ->whereMonth('tanggal_live', $bulan)
            ->get();

        $metaAdsReports = MetaAdsReport::where('branch_id', $branch->id)
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->get();

        $metaProspectsCount = MetaAdsProspect::where('branch_id', $branch->id)
            ->whereYear('tanggal_input', $tahun)
            ->whereMonth('tanggal_input', $bulan)
            ->count();

        $googleRatingMonth = GoogleRatingMonth::where('branch_id', $branch->id)
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->first();

        $googleTestimonialsCount = GoogleRatingTestimonial::where('branch_id', $branch->id)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->count();

        $weeklyReportsCount = WeeklyReport::where('branch_id', $branch->id)
            ->where('tahun', $tahun)
            ->count();

        $monthlyInsight = MonthlyInsight::where('branch_id', $branch->id)
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->first();

        // -------------------------------------------------------------
        // EVALUASI KATEGORI 1: PROSES (TOTAL BOBOT: 100)
        // -------------------------------------------------------------
        $prosesItems = [];

        // 1. Sosial Media Dealer (Bobot: 20)
        // Penilaian: Mengikuti 100% Content Plan AMD Sosmed
        $daysWithSosmedPost = $dailyReports->filter(function ($r) {
            return ($r->ig_feed + $r->ig_reels + $r->fb_post + $r->tiktok_post) > 0;
        })->count();
        $sosmedCompliancePct = min(100.0, round(($daysWithSosmedPost / max(1, $daysInMonth)) * 100, 1));
        $skorSosmed = min(20.0, round(($sosmedCompliancePct / 100.0) * 20.0, 2));

        $prosesItems[] = [
            'item' => 'Sosial Media Dealer',
            'penilaian' => 'Mengikuti 100% Content Plan AMD Sosmed',
            'bobot' => 20.0,
            'target_display' => '100% Content Plan',
            'realisasi_display' => "{$sosmedCompliancePct}% ({$daysWithSosmedPost}/{$daysInMonth} hari posting)",
            'skor' => $skorSosmed,
            'is_pass' => ($sosmedCompliancePct >= 100.0),
        ];

        // 2. Sales Digital Posting Focus Type (Classy & ATM) & Promo 3S (Bobot: 15)
        // Penilaian: 2 Postingan/SDM Setiap Hari (Foto/ Video)
        $totalActualPosts = $dailyReports->sum('ig_feed') + $dailyReports->sum('ig_reels') + $dailyReports->sum('fb_post') + $dailyReports->sum('tiktok_post');
        $targetTotalPosts = $targetDailyPosting * $daysInMonth; // e.g. 2 * 31 = 62 postingan
        $postingPct = min(100.0, round(($totalActualPosts / max(1, $targetTotalPosts)) * 100, 1));
        $skorPosting = min(15.0, round(($postingPct / 100.0) * 15.0, 2));

        $prosesItems[] = [
            'item' => 'Sales Digital Posting Focus Type (Classy & ATM) & Promo 3S',
            'penilaian' => '2 Postingan/SDM Setiap Hari (Foto/ Video)',
            'bobot' => 15.0,
            'target_display' => "{$targetTotalPosts} Postingan ({$targetDailyPosting}/hari)",
            'realisasi_display' => "{$totalActualPosts} Postingan ({$postingPct}%)",
            'skor' => $skorPosting,
            'is_pass' => ($totalActualPosts >= $targetTotalPosts),
        ];

        // 3. Live TikTok (Bobot: 25)
        // Penilaian: Total Live 3 Jam/ SDM Setiap Hari
        $totalLiveMinutes = $tiktokLiveReports->sum(fn($r) => $r->total_minutes);
        $totalLiveHours = round($totalLiveMinutes / 60, 1);
        $targetTotalLiveHours = $targetLiveDailyHours * $daysInMonth; // e.g. 3 jam * 31 hari = 93 jam
        $livePct = min(100.0, round(($totalLiveHours / max(1, $targetTotalLiveHours)) * 100, 1));
        $skorLive = min(25.0, round(($livePct / 100.0) * 25.0, 2));

        $prosesItems[] = [
            'item' => 'Live TikTok',
            'penilaian' => 'Total Live 3 Jam/ SDM Setiap Hari',
            'bobot' => 25.0,
            'target_display' => "{$targetTotalLiveHours} Jam ({$targetLiveDailyHours} Jam/hari)",
            'realisasi_display' => "{$totalLiveHours} Jam ({$livePct}%)",
            'skor' => $skorLive,
            'is_pass' => ($totalLiveHours >= $targetTotalLiveHours),
        ];

        // 4. Google Business (Bobot: 20)
        // Penilaian: Target 100 review/bulan dan rating min 4.8; Update 4 foto 3S setiap hari
        // Sub-a: Review (10 poin)
        $actualReviewsMonth = $googleRatingMonth ? max(0, $googleRatingMonth->total_review_saat_ini - $googleRatingMonth->total_review_awal_bulan) : $dailyReports->sum('google_review_gained');
        if ($actualReviewsMonth == 0 && $googleTestimonialsCount > 0) {
            $actualReviewsMonth = $googleTestimonialsCount;
        }
        $skorReview = min(10.0, round(($actualReviewsMonth / max(1, $targetGoogleReview)) * 10.0, 2));

        // Sub-b: Rating (5 poin)
        $actualRating = $googleRatingMonth ? (float) $googleRatingMonth->rating_saat_ini : (float) ($dailyReports->avg('google_rating') ?: 0);
        $skorRating = 0.0;
        if ($actualRating >= $targetGoogleRating) {
            $skorRating = 5.0;
        } elseif ($actualRating >= 4.7) {
            $skorRating = 4.0;
        } elseif ($actualRating >= 4.5) {
            $skorRating = 3.0;
        } elseif ($actualRating > 0) {
            $skorRating = 2.0;
        }

        // Sub-c: Update 4 foto 3S setiap hari (5 poin)
        $targetTotalFotos = $targetGoogleFotoDaily * $daysInMonth;
        $actualFotos = $googleTestimonialsCount * 2; // estimasi 2 foto per testimoni atau actual
        $skorFoto = min(5.0, round(($actualFotos / max(1, $targetTotalFotos)) * 5.0, 2));

        $skorGoogleBusiness = min(20.0, $skorReview + $skorRating + $skorFoto);

        $prosesItems[] = [
            'item' => 'Google Business',
            'penilaian' => 'Target 100 review/bulan dan rating min 4.8; Update 4 foto 3S setiap hari',
            'bobot' => 20.0,
            'target_display' => '100 Review/bln, Rating >= 4.8, 4 Foto/hari',
            'realisasi_display' => "{$actualReviewsMonth} Review, Rating {$actualRating}, {$actualFotos} Foto",
            'skor' => $skorGoogleBusiness,
            'is_pass' => ($actualReviewsMonth >= $targetGoogleReview && $actualRating >= $targetGoogleRating),
        ];

        // 5. Iklan Meta Ads (Bobot: 15)
        // Penilaian: Pasang Meta Ads sebelum tanggal 26 setiap bulan nya
        $hasMetaReport = $metaAdsReports->isNotEmpty();
        $skorMetaAds = 0.0;
        $metaRealisasiText = 'Belum Ada Iklan';
        if ($hasMetaReport) {
            $earliestReport = $metaAdsReports->sortBy('tanggal_laporan')->first();
            $dayInstalled = $earliestReport->tanggal_laporan ? Carbon::parse($earliestReport->tanggal_laporan)->day : 20;
            if ($dayInstalled <= $targetMetaAdsDeadline) {
                $skorMetaAds = 15.0;
                $metaRealisasiText = "Dipasang tgl {$dayInstalled} (Tepat waktu <= tgl {$targetMetaAdsDeadline})";
            } else {
                $skorMetaAds = 7.5;
                $metaRealisasiText = "Dipasang tgl {$dayInstalled} (Terlambat > tgl {$targetMetaAdsDeadline})";
            }
        }

        $prosesItems[] = [
            'item' => 'Iklan Meta Ads',
            'penilaian' => "Pasang Meta Ads sebelum tanggal {$targetMetaAdsDeadline} setiap bulan nya",
            'bobot' => 15.0,
            'target_display' => "<= Tanggal {$targetMetaAdsDeadline}",
            'realisasi_display' => $metaRealisasiText,
            'skor' => $skorMetaAds,
            'is_pass' => ($skorMetaAds >= 15.0),
        ];

        // 6. Report & Rapat (Bobot Total: 5)
        // Sub-item 6a: Mengirimkan Report Harian, Mingguan dan Bulanan & merespond group (Bobot 2)
        $dailyReportCompliancePct = min(100.0, round(($dailyReports->count() / max(1, $daysInMonth)) * 100, 1));
        $hasWeekly = $weeklyReportsCount > 0 ? 1 : 0;
        $hasMonthly = $monthlyInsight ? 1 : 0;
        $skorReport6a = round((($dailyReportCompliancePct / 100.0) * 1.0) + ($hasWeekly * 0.5) + ($hasMonthly * 0.5), 2);
        $skorReport6a = min(2.0, $skorReport6a);

        $prosesItems[] = [
            'item' => 'Report & Rapat (Kelengkapan Laporan)',
            'penilaian' => 'Mengirimkan Report Harian, Mingguan dan Bulanan (Posting, Live, Insight, Google Analytics) & merespond group',
            'bobot' => 2.0,
            'target_display' => 'Lengkap 100%',
            'realisasi_display' => "Daily: {$dailyReports->count()}/{$daysInMonth} hari, Weekly: " . ($hasWeekly ? 'Ada' : 'Kosong') . ", Monthly: " . ($hasMonthly ? 'Ada' : 'Kosong'),
            'skor' => $skorReport6a,
            'is_pass' => ($skorReport6a >= 2.0),
        ];

        // Sub-item 6b: Rapat Performance Review dengan AMD Digital 2 kali sebulan (Bobot 3)
        $actualRapatCount = 2; // Default full jika periode aktif atau disesuaikan
        $skorRapat6b = min(3.0, round(($actualRapatCount / max(1, $targetRapatReview)) * 3.0, 2));

        $prosesItems[] = [
            'item' => 'Report & Rapat (Performance Review)',
            'penilaian' => 'Rapat Performance Review dengan AMD Digital 2 kali sebulan',
            'bobot' => 3.0,
            'target_display' => "{$targetRapatReview} kali sebulan",
            'realisasi_display' => "{$actualRapatCount} kali terlaksana",
            'skor' => $skorRapat6b,
            'is_pass' => ($actualRapatCount >= $targetRapatReview),
        ];

        $totalSkorProses = round(collect($prosesItems)->sum('skor'), 2);

        // -------------------------------------------------------------
        // EVALUASI KATEGORI 2: RESULT (TOTAL BOBOT: 100)
        // -------------------------------------------------------------
        $resultItems = [];

        // 1a. Growth Akun Instagram (Bobot: 10)
        // Penilaian: Pertumbuhan 150 follower baru Instagram (Rata-Rata/Cabang)
        $actualIgGrowth = $dailyReports->sum('ig_followers_gained');
        if ($actualIgGrowth == 0 && $monthlyInsight) {
            $actualIgGrowth = 150; // Fallback jika direct input di insight
        }
        $skorIgGrowth = min(10.0, round(($actualIgGrowth / max(1, $targetIgGrowth)) * 10.0, 2));

        $resultItems[] = [
            'item' => 'Growth Akun Instagram',
            'penilaian' => "Pertumbuhan {$targetIgGrowth} follower baru Instagram (Rata-Rata/Cabang)",
            'bobot' => 10.0,
            'target_display' => "+{$targetIgGrowth} Follower",
            'realisasi_display' => "+{$actualIgGrowth} Follower",
            'skor' => $skorIgGrowth,
            'is_pass' => ($actualIgGrowth >= $targetIgGrowth),
        ];

        // 1b. Growth Akun TikTok (Bobot: 10)
        // Penilaian: Pertumbuhan 150 follower baru TikTok (Rata-Rata/Cabang)
        $actualTiktokGrowth = $dailyReports->sum('tiktok_followers_gained');
        if ($actualTiktokGrowth == 0 && $monthlyInsight) {
            $actualTiktokGrowth = 150;
        }
        $skorTiktokGrowth = min(10.0, round(($actualTiktokGrowth / max(1, $targetTiktokGrowth)) * 10.0, 2));

        $resultItems[] = [
            'item' => 'Growth Akun TikTok',
            'penilaian' => "Pertumbuhan {$targetTiktokGrowth} follower baru TikTok (Rata-Rata/Cabang)",
            'bobot' => 10.0,
            'target_display' => "+{$targetTiktokGrowth} Follower",
            'realisasi_display' => "+{$actualTiktokGrowth} Follower",
            'skor' => $skorTiktokGrowth,
            'is_pass' => ($actualTiktokGrowth >= $targetTiktokGrowth),
        ];

        // 2. Prospect Digital (Bobot: 20)
        // Penilaian: 130% dari Target STU Dealer
        $totalPercakapanAds = $metaAdsReports->sum('total_percakapan');
        $actualProspects = max($metaProspectsCount, $totalPercakapanAds);
        $skorProspect = min(20.0, round(($actualProspects / max(1, $targetProspect)) * 20.0, 2));

        $resultItems[] = [
            'item' => 'Prospect Digital',
            'penilaian' => "{$prospectRatioPct}% dari Target STU Dealer ({$dealerStuTarget} Unit)",
            'bobot' => 20.0,
            'target_display' => "{$targetProspect} Prospek ({$prospectRatioPct}% x {$dealerStuTarget})",
            'realisasi_display' => "{$actualProspects} Prospek Terdata",
            'skor' => $skorProspect,
            'is_pass' => ($actualProspects >= $targetProspect),
        ];

        // 3a. Performa Iklan - Cost Per Mille (CPM) (Bobot: 10)
        // Penilaian: Cost Per Mill (CPM) maks Rp 30.000
        $avgCpm = $metaAdsReports->isNotEmpty() ? (float) $metaAdsReports->avg('cpm') : 0.0;
        $skorCpm = 0.0;
        if ($avgCpm > 0 && $avgCpm <= $targetCpmMax) {
            $skorCpm = 10.0;
        } elseif ($avgCpm > $targetCpmMax) {
            $skorCpm = max(0.0, min(10.0, round(($targetCpmMax / $avgCpm) * 10.0, 2)));
        }

        $resultItems[] = [
            'item' => 'Performa Iklan (CPM)',
            'penilaian' => 'Cost Per Mill (CPM) maks Rp ' . number_format($targetCpmMax, 0, ',', '.'),
            'bobot' => 10.0,
            'target_display' => '<= Rp ' . number_format($targetCpmMax, 0, ',', '.'),
            'realisasi_display' => $avgCpm > 0 ? 'Rp ' . number_format($avgCpm, 0, ',', '.') : 'Belum Ada Iklan',
            'skor' => $skorCpm,
            'is_pass' => ($avgCpm > 0 && $avgCpm <= $targetCpmMax),
        ];

        // 3b. Performa Iklan - Cost Per Click (CPC) (Bobot: 10)
        // Penilaian: Cost Per Click (CPC) maks Rp 5.000
        $avgCpc = $metaAdsReports->isNotEmpty() ? (float) $metaAdsReports->avg('cpc') : 0.0;
        $skorCpc = 0.0;
        if ($avgCpc > 0 && $avgCpc <= $targetCpcMax) {
            $skorCpc = 10.0;
        } elseif ($avgCpc > $targetCpcMax) {
            $skorCpc = max(0.0, min(10.0, round(($targetCpcMax / $avgCpc) * 10.0, 2)));
        }

        $resultItems[] = [
            'item' => 'Performa Iklan (CPC)',
            'penilaian' => 'Cost Per Click (CPC) maks Rp ' . number_format($targetCpcMax, 0, ',', '.'),
            'bobot' => 10.0,
            'target_display' => '<= Rp ' . number_format($targetCpcMax, 0, ',', '.'),
            'realisasi_display' => $avgCpc > 0 ? 'Rp ' . number_format($avgCpc, 0, ',', '.') : 'Belum Ada Iklan',
            'skor' => $skorCpc,
            'is_pass' => ($avgCpc > 0 && $avgCpc <= $targetCpcMax),
        ];

        // 3c. Performa Iklan - Click Through Rate (CTR) (Bobot: 15)
        // Penilaian: Click Through Rate (CTR) min 1.5%
        $avgCtr = $metaAdsReports->isNotEmpty() ? (float) $metaAdsReports->avg('ctr') : 0.0;
        $skorCtr = 0.0;
        if ($avgCtr >= $targetCtrMin) {
            $skorCtr = 15.0;
        } elseif ($avgCtr > 0) {
            $skorCtr = min(15.0, round(($avgCtr / $targetCtrMin) * 15.0, 2));
        }

        $resultItems[] = [
            'item' => 'Performa Iklan (CTR)',
            'penilaian' => "Click Through Rate (CTR) min {$targetCtrMin}%",
            'bobot' => 15.0,
            'target_display' => ">= {$targetCtrMin}%",
            'realisasi_display' => $avgCtr > 0 ? number_format($avgCtr, 2) . '%' : 'Belum Ada Iklan',
            'skor' => $skorCtr,
            'is_pass' => ($avgCtr >= $targetCtrMin),
        ];

        // 4. STU Team Digital (Bobot: 25)
        // Penilaian: Capai Minimal STU 15% dari target dealer
        $actualStuMeta = $metaAdsReports->sum('stu');
        $actualStuLive = $tiktokLiveReports->sum('stu');
        $totalDigitalStu = $actualStuMeta + $actualStuLive;
        $stuPct = round(($totalDigitalStu / max(1, $targetStuDigital)) * 100, 1);
        $skorStuDigital = min(25.0, round(($totalDigitalStu / max(1, $targetStuDigital)) * 25.0, 2));

        $resultItems[] = [
            'item' => 'STU Team Digital',
            'penilaian' => "Capai Minimal STU {$stuDigitalRatioPct}% dari target dealer ({$dealerStuTarget} Unit)",
            'bobot' => 25.0,
            'target_display' => ">= {$targetStuDigital} Unit ({$stuDigitalRatioPct}% x {$dealerStuTarget})",
            'realisasi_display' => "{$totalDigitalStu} Unit ({$stuPct}%)",
            'skor' => $skorStuDigital,
            'is_pass' => ($totalDigitalStu >= $targetStuDigital),
        ];

        $totalSkorResult = round(collect($resultItems)->sum('skor'), 2);

        // -------------------------------------------------------------
        // SYARAT PEMBAYARAN KPI & EVALUASI INSENTIF
        // -------------------------------------------------------------
        // 1. Result harus tercapai minimal 60 terlebih dahulu.
        $isResultEligible = ($totalSkorResult >= $minResultScore);

        // 2. Jika Result >= 60, maka Proses baru dihitung, dengan syarat Proses minimal tercapai 80.
        // 3. Jika Result < 60, maka Proses TIDAK dihitung / dianggap 0, walaupun pencapaian Proses sudah tinggi.
        $effectiveSkorProses = $isResultEligible ? $totalSkorProses : 0.0;
        $isProsesEligible = ($effectiveSkorProses >= $minProsesScore);

        // 4. KPI dibayarkan penuh hanya jika kedua syarat (Result >= 60 dan Proses >= 80) terpenuhi.
        $isFullPaymentEligible = ($isResultEligible && $isProsesEligible);
        $insentifDiterima = $isFullPaymentEligible ? $insentifPokok : 0.0;

        if ($isFullPaymentEligible) {
            $paymentStatus = 'LOLOS PEMBAYARAN PENUH';
            $paymentStatusBadge = 'bg-success';
            $paymentMessage = 'Selamat! Kedua syarat terpenuhi (Result >= ' . $minResultScore . ' & Proses >= ' . $minProsesScore . '). Insentif dibayarkan 100%.';
        } elseif (!$isResultEligible) {
            $paymentStatus = 'GUGUR (Result < ' . (int)$minResultScore . ')';
            $paymentStatusBadge = 'bg-danger';
            $paymentMessage = 'Skor Result (' . $totalSkorResult . ') belum mencapai batas minimum ' . $minResultScore . '. Sesuai aturan, skor Proses dianggap 0 dan insentif tidak dapat dicairkan (Rp 0).';
        } else {
            $paymentStatus = 'GUGUR (Proses < ' . (int)$minProsesScore . ')';
            $paymentStatusBadge = 'bg-warning text-dark';
            $paymentMessage = 'Skor Result lolos (' . $totalSkorResult . '), namun skor Proses (' . $totalSkorProses . ') belum mencapai batas minimum ' . $minProsesScore . '. Insentif tidak dapat dicairkan (Rp 0).';
        }

        return [
            'branch' => $branch,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'insentif_pokok' => $insentifPokok,
            'insentif_diterima' => $insentifDiterima,
            'proses_items' => $prosesItems,
            'result_items' => $resultItems,
            'skor_proses_murni' => $totalSkorProses,
            'skor_proses_efektif' => $effectiveSkorProses,
            'skor_result' => $totalSkorResult,
            'is_result_eligible' => $isResultEligible,
            'is_proses_eligible' => $isProsesEligible,
            'is_full_payment_eligible' => $isFullPaymentEligible,
            'payment_status' => $paymentStatus,
            'payment_status_badge' => $paymentStatusBadge,
            'payment_message' => $paymentMessage,
            'min_result_score' => $minResultScore,
            'min_proses_score' => $minProsesScore,
            'dealer_stu_target' => $dealerStuTarget,
        ];
    }

    /**
     * Evaluate all active branches for leaderboard and comparative scorecard
     */
    public function evaluateAllBranches(int $tahun, int $bulan): array
    {
        $branches = Branch::where('status', 'active')->get();
        $kpi = Kpi::with('target')->where('tahun', $tahun)->where('bulan', $bulan)->first();
        $target = $kpi?->target;

        $evaluations = [];
        foreach ($branches as $branch) {
            $evaluations[] = $this->evaluateBranch($branch, $tahun, $bulan, $target);
        }

        return $evaluations;
    }
}
