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
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Compile comprehensive Executive Analytics & Business Insights
     */
    public function getDashboardMetrics(?int $tahun = null, ?int $bulan = null, ?int $branchId = null): array
    {
        $tahun = $tahun ?? (int) now()->year;
        $bulan = $bulan ?? (int) now()->month;

        // Fetch branches
        $allActiveBranches = Branch::where('status', 'active')->orderBy('id')->get();
        $branchQuery = Branch::where('status', 'active');
        if ($branchId) {
            $branchQuery->where('id', $branchId);
        }
        $filteredBranches = $branchQuery->get();
        $totalBranches = $allActiveBranches->count();

        // 1. Unsubmitted daily report today
        $today = now()->format('Y-m-d');
        $submittedBranchIdsToday = DailyReport::where('tanggal', $today)->pluck('branch_id')->toArray();
        $missingBranchesToday = Branch::where('status', 'active')
            ->whereNotIn('id', $submittedBranchIdsToday)
            ->get();

        // 2. Fetch KPI configuration
        $kpi = Kpi::with('target')->where('tahun', $tahun)->where('bulan', $bulan)->first();
        $target = $kpi?->target;

        // 3. Raw Data Queries for the selected period
        $liveQuery = TiktokLiveReport::whereYear('tanggal_live', $tahun)->whereMonth('tanggal_live', $bulan);
        if ($branchId) {
            $liveQuery->where('branch_id', $branchId);
        }
        $liveReports = $liveQuery->get();

        $adsQuery = MetaAdsReport::where('tahun', $tahun)->where('bulan', $bulan);
        if ($branchId) {
            $adsQuery->where('branch_id', $branchId);
        }
        $adsReports = $adsQuery->get();

        $prospectQuery = MetaAdsProspect::whereYear('tanggal_input', $tahun)->whereMonth('tanggal_input', $bulan);
        if ($branchId) {
            $prospectQuery->where('branch_id', $branchId);
        }
        $prospects = $prospectQuery->get();

        $dailyQuery = DailyReport::whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan);
        if ($branchId) {
            $dailyQuery->where('branch_id', $branchId);
        }
        $dailyReports = $dailyQuery->get();

        $insightQuery = MonthlyInsight::where('tahun', $tahun)->where('bulan', $bulan);
        if ($branchId) {
            $insightQuery->where('branch_id', $branchId);
        }
        $monthlyInsights = $insightQuery->get();

        // 4. Calculate STU Core Metrics
        $stuLiveTikTok = (int) $liveReports->sum('stu');
        $stuMetaAds = (int) $adsReports->sum('stu');
        $grandTotalStu = $stuLiveTikTok + $stuMetaAds;

        $stuLivePct = $grandTotalStu > 0 ? round(($stuLiveTikTok / $grandTotalStu) * 100, 1) : 0;
        $stuAdsPct = $grandTotalStu > 0 ? round(($stuMetaAds / $grandTotalStu) * 100, 1) : 0;

        // 5. Prospects & Leads Core Metrics
        $totalAdsConversations = (int) $adsReports->sum('total_percakapan');
        $prospectRecordsCount = $prospects->count();
        // Total prospect digital is the comprehensive pool of leads (conversations or CRM entries)
        $totalProspectsDigital = max($totalAdsConversations, $prospectRecordsCount);

        // 6. Meta Ads Financial & Efficiency Metrics
        $totalAdsBudget = (float) $adsReports->sum('budget');
        $totalAdsViews = (int) $adsReports->sum('total_views');
        $avgAdsCtr = $adsReports->isNotEmpty() ? round($adsReports->avg('ctr'), 2) : 0;
        $avgAdsCpc = $adsReports->isNotEmpty() ? round($adsReports->avg('cpc')) : 0;
        $avgAdsCpm = $adsReports->isNotEmpty() ? round($adsReports->avg('cpm')) : 0;
        $avgAdsCpl = $totalProspectsDigital > 0 && $totalAdsBudget > 0 ? round($totalAdsBudget / $totalProspectsDigital) : ($adsReports->isNotEmpty() ? round($adsReports->avg('cpl')) : 0);
        $costPerStu = $grandTotalStu > 0 && $totalAdsBudget > 0 ? round($totalAdsBudget / $grandTotalStu) : 0;
        $conversionRateProspectToStu = $totalProspectsDigital > 0 ? round(($grandTotalStu / $totalProspectsDigital) * 100, 2) : 0;

        // 7. TikTok Live Streaming Metrics
        $totalLiveSessions = $liveReports->count();
        $totalLiveMinutes = $liveReports->sum(fn($r) => ($r->durasi_jam * 60) + $r->durasi_menit);
        $totalLiveHours = round($totalLiveMinutes / 60, 1);
        $totalLiveViewers = (int) $liveReports->sum('jumlah_penonton');
        $totalLiveLikes = (int) $liveReports->sum('jumlah_like');
        $totalLiveComments = (int) $liveReports->sum('jumlah_komentar');
        $totalLiveShares = (int) $liveReports->sum('jumlah_share');
        $avgStuPerLiveSession = $totalLiveSessions > 0 ? round($stuLiveTikTok / $totalLiveSessions, 2) : 0;

        // 8. Social Media & Content Productivity
        $totalIgFeed = $dailyReports->sum('ig_feed');
        $totalIgReels = $dailyReports->sum('ig_reels');
        $totalIgStory = $dailyReports->sum('ig_story');
        $totalIgFollowers = $dailyReports->sum('ig_followers_gained');

        $totalFbPost = $dailyReports->sum('fb_post');
        $totalFbMarketplace = $dailyReports->sum('fb_marketplace');
        $totalFbFollowers = $dailyReports->sum('fb_followers_gained');

        $totalTiktokPost = $dailyReports->sum('tiktok_post');
        $totalTiktokFollowers = $dailyReports->sum('tiktok_followers_gained');

        $totalPosts = $totalIgFeed + $totalIgReels + $totalIgStory + $totalFbPost + $totalFbMarketplace + $totalTiktokPost;
        $followersGrowth = $totalIgFollowers + $totalFbFollowers + $totalTiktokFollowers;
        $totalViews = $monthlyInsights->sum('ig_views') + $monthlyInsights->sum('fb_views') + $monthlyInsights->sum('tiktok_views');

        $avgGoogleRating = $dailyReports->avg('google_rating') ?: 0;
        $googleReviews = $dailyReports->sum('google_review_gained');

        // 9. Cutoff Day & Last Month MTD (Per Hari Ini) Calculations
        $today = now();
        $isCurrentMonth = ($tahun == $today->year && $bulan == $today->month);
        $cutoffDay = $isCurrentMonth ? $today->day : Carbon::create($tahun, $bulan, 1)->daysInMonth;

        $lastMonthCarbon = Carbon::create($tahun, $bulan, 1)->subMonth();
        $lastMonthTahun = $lastMonthCarbon->year;
        $lastMonthBulan = $lastMonthCarbon->month;
        $daysInLastMonth = $lastMonthCarbon->daysInMonth;
        $lastMonthCutoffDay = min($cutoffDay, $daysInLastMonth);

        $stuScorecard = [];
        $prospectScorecard = [];
        $totalDealerStuTarget = 0;
        $totalProspectTarget = 0;
        $totalDigitalStuTarget = 0;

        $branchesForScorecard = $branchId ? $filteredBranches : $allActiveBranches;

        foreach ($branchesForScorecard as $branch) {
            $bStuTarget = $target ? $target->getDealerStuTargetForBranch($branch->id, $branch->nama_cabang) : KpiTarget::resolveTargetForBranchName($branch->nama_cabang);
            // Target Team digital adalah 15% dari target STU Dealer
            $bDigitalStuTarget = (int) round(($target ? (float)$target->stu_digital_ratio_pct : 15.0) / 100 * $bStuTarget);
            // Target prospect adalah 130% dari Target STU Dealer
            $bProspectTarget = (int) round(($target ? (float)$target->prospect_digital_ratio_pct : 130.0) / 100 * $bStuTarget);

            // Bulan ini (MTD up to cutoffDay)
            $bLiveReports = TiktokLiveReport::where('branch_id', $branch->id)
                ->whereYear('tanggal_live', $tahun)
                ->whereMonth('tanggal_live', $bulan)
                ->whereDay('tanggal_live', '<=', $cutoffDay)
                ->get();

            $bAdsReports = MetaAdsReport::where('branch_id', $branch->id)
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->get();

            $bProspectsCount = MetaAdsProspect::where('branch_id', $branch->id)
                ->whereYear('tanggal_input', $tahun)
                ->whereMonth('tanggal_input', $bulan)
                ->whereDay('tanggal_input', '<=', $cutoffDay)
                ->count();

            $bStuLive = (int) $bLiveReports->sum('stu');
            $bStuAds = (int) $bAdsReports->sum('stu');
            $bTotalStu = $bStuLive + $bStuAds;

            $bAdsConversations = (int) $bAdsReports->sum('total_percakapan');
            $bActualProspects = max($bAdsConversations, $bProspectsCount);

            // Last Month MTD (per hari ini)
            $bLastMonthLiveStu = (int) TiktokLiveReport::where('branch_id', $branch->id)
                ->whereYear('tanggal_live', $lastMonthTahun)
                ->whereMonth('tanggal_live', $lastMonthBulan)
                ->whereDay('tanggal_live', '<=', $lastMonthCutoffDay)
                ->sum('stu');

            $bLastMonthAdsReports = MetaAdsReport::where('branch_id', $branch->id)
                ->where('tahun', $lastMonthTahun)
                ->where('bulan', $lastMonthBulan)
                ->get();

            $bLastMonthAdsStu = (int) $bLastMonthAdsReports->sum('stu');
            $bLastMonthAdsStuMtd = (int) round($bLastMonthAdsStu * ($lastMonthCutoffDay / max(1, $daysInLastMonth)));
            $bLastMonthTotalStuMtd = $bLastMonthLiveStu + $bLastMonthAdsStuMtd;

            $bLastMonthProspectsCount = MetaAdsProspect::where('branch_id', $branch->id)
                ->whereYear('tanggal_input', $lastMonthTahun)
                ->whereMonth('tanggal_input', $lastMonthBulan)
                ->whereDay('tanggal_input', '<=', $lastMonthCutoffDay)
                ->count();

            $bLastMonthAdsConversations = (int) $bLastMonthAdsReports->sum('total_percakapan');
            $bLastMonthAdsConversationsMtd = (int) round($bLastMonthAdsConversations * ($lastMonthCutoffDay / max(1, $daysInLastMonth)));
            $bLastMonthActualProspectsMtd = max($bLastMonthAdsConversationsMtd, $bLastMonthProspectsCount);

            // STU Calculations
            $bDigitalStuAchvPct = round(($bTotalStu / max(1, $bDigitalStuTarget)) * 100, 1);
            $bDealerStuAchvPct = round(($bTotalStu / max(1, $bStuTarget)) * 100, 1);
            $stuDiffVsDigital = $bTotalStu - $bDigitalStuTarget;
            $stuDiffVsLastMonth = $bTotalStu - $bLastMonthTotalStuMtd;
            $stuKeterangan = $stuDiffVsLastMonth > 0 ? 'Naik' : ($stuDiffVsLastMonth < 0 ? 'Turun' : 'Stabil');

            // Prospect Calculations
            $bProspectAchvPct = round(($bActualProspects / max(1, $bProspectTarget)) * 100, 1);
            $prospectDiffVsTarget = $bActualProspects - $bProspectTarget;
            $prospectDiffVsLastMonth = $bActualProspects - $bLastMonthActualProspectsMtd;
            $prospectKeterangan = $prospectDiffVsLastMonth > 0 ? 'Naik' : ($prospectDiffVsLastMonth < 0 ? 'Turun' : 'Stabil');

            $bAdsBudget = (float) $bAdsReports->sum('budget');
            $bCostPerStu = $bTotalStu > 0 ? round($bAdsBudget / $bTotalStu) : 0;
            $bConversionRate = $bActualProspects > 0 ? round(($bTotalStu / $bActualProspects) * 100, 2) : 0;
            $bLiveMinutes = $bLiveReports->sum(fn($r) => ($r->durasi_jam * 60) + $r->durasi_menit);
            $bLiveHours = round($bLiveMinutes / 60, 1);

            $stuScorecard[] = [
                'branch_id' => $branch->id,
                'kode' => $branch->kode,
                'nama_cabang' => $branch->nama_cabang,
                'area' => $branch->area,
                'manager_name' => $branch->manager_name,
                'dealer_stu_target' => $bStuTarget,
                'digital_stu_target' => $bDigitalStuTarget,
                'stu_tiktok_live' => $bStuLive,
                'stu_meta_ads' => $bStuAds,
                'total_stu' => $bTotalStu,
                'digital_stu_achievement_pct' => $bDigitalStuAchvPct,
                'dealer_stu_achievement_pct' => $bDealerStuAchvPct,
                'stu_achievement_pct' => $bDealerStuAchvPct,
                'diff_vs_digital' => $stuDiffVsDigital,
                'diff_vs_digital_formatted' => ($stuDiffVsDigital >= 0 ? '+' : '') . $stuDiffVsDigital,
                'last_month_stu_mtd' => $bLastMonthTotalStuMtd,
                'diff_vs_last_month' => $stuDiffVsLastMonth,
                'diff_vs_last_month_formatted' => ($stuDiffVsLastMonth >= 0 ? '+' : '') . $stuDiffVsLastMonth,
                'keterangan' => $stuKeterangan,
                'conversion_rate' => $bConversionRate,
                'cost_per_stu' => $bCostPerStu,
                'live_total_hours' => $bLiveHours,
                'status_badge' => KpiService::getStatusBadge($bDigitalStuAchvPct),
            ];

            $prospectScorecard[] = [
                'branch_id' => $branch->id,
                'kode' => $branch->kode,
                'nama_cabang' => $branch->nama_cabang,
                'area' => $branch->area,
                'manager_name' => $branch->manager_name,
                'dealer_stu_target' => $bStuTarget,
                'prospect_target' => $bProspectTarget,
                'realisasi_prospek' => $bActualProspects,
                'prospek_achievement_pct' => $bProspectAchvPct,
                'diff_vs_target' => $prospectDiffVsTarget,
                'diff_vs_target_formatted' => ($prospectDiffVsTarget >= 0 ? '+' : '') . $prospectDiffVsTarget,
                'last_month_prospect_mtd' => $bLastMonthActualProspectsMtd,
                'diff_vs_last_month' => $prospectDiffVsLastMonth,
                'diff_vs_last_month_formatted' => ($prospectDiffVsLastMonth >= 0 ? '+' : '') . $prospectDiffVsLastMonth,
                'keterangan' => $prospectKeterangan,
                'status_badge' => KpiService::getStatusBadge($bProspectAchvPct),
            ];

            $totalDealerStuTarget += $bStuTarget;
            $totalProspectTarget += $bProspectTarget;
            $totalDigitalStuTarget += $bDigitalStuTarget;
        }

        // Sort STU Scorecard by total_stu descending
        usort($stuScorecard, fn($a, $b) => $b['total_stu'] <=> $a['total_stu']);
        foreach ($stuScorecard as $idx => &$item) {
            $item['rank'] = $idx + 1;
        }
        unset($item);

        // Sort Prospect Scorecard by realisasi_prospek descending
        usort($prospectScorecard, fn($a, $b) => $b['realisasi_prospek'] <=> $a['realisasi_prospek']);
        foreach ($prospectScorecard as $idx => &$item) {
            $item['rank'] = $idx + 1;
        }
        unset($item);

        // Keep backwards compatible $scorecard alias
        $scorecard = $stuScorecard;

        $overallDealerStuAchvPct = $totalDealerStuTarget > 0 ? round(($grandTotalStu / $totalDealerStuTarget) * 100, 1) : 0;
        $overallProspectAchvPct = $totalProspectTarget > 0 ? round(($totalProspectsDigital / $totalProspectTarget) * 100, 1) : 0;
        $overallDigitalStuAchvPct = $totalDigitalStuTarget > 0 ? round(($grandTotalStu / $totalDigitalStuTarget) * 100, 1) : 0;

        // 10. Monthly Trends across all 12 Months of the Year (Total STU per bulan)
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $monthlyStuLive = array_fill(1, 12, 0);
        $monthlyStuAds = array_fill(1, 12, 0);
        $monthlyStuTotal = array_fill(1, 12, 0);
        $monthlyProspects = array_fill(1, 12, 0);
        $monthlyBudget = array_fill(1, 12, 0);

        // Fetch year-long data
        $yearLiveQuery = TiktokLiveReport::whereYear('tanggal_live', $tahun);
        if ($branchId) {
            $yearLiveQuery->where('branch_id', $branchId);
        }
        $yearLiveReports = $yearLiveQuery->get();

        foreach ($yearLiveReports as $lr) {
            $m = (int) $lr->tanggal_live->month;
            if (isset($monthlyStuLive[$m])) {
                $monthlyStuLive[$m] += (int) $lr->stu;
            }
        }

        $yearAdsQuery = MetaAdsReport::where('tahun', $tahun);
        if ($branchId) {
            $yearAdsQuery->where('branch_id', $branchId);
        }
        $yearAdsReports = $yearAdsQuery->get();

        foreach ($yearAdsReports as $ar) {
            $m = (int) $ar->bulan;
            if (isset($monthlyStuAds[$m])) {
                $monthlyStuAds[$m] += (int) $ar->stu;
                $monthlyProspects[$m] += (int) $ar->total_percakapan;
                $monthlyBudget[$m] += (float) $ar->budget;
            }
        }

        for ($m = 1; $m <= 12; $m++) {
            $monthlyStuTotal[$m] = $monthlyStuLive[$m] + $monthlyStuAds[$m];
        }

        // 11. Dedicated Yamaha Product Table (Model, Kategori, STU Live, STU Ads, Total, Budget, CR)
        $productCatalog = [
            ['category' => 'Generasi 125', 'model' => 'GEAR ULTIMA 125 HYBRID', 'badge' => 'bg-success'],
            ['category' => 'Generasi 125', 'model' => 'GEAR ULTIMA 125 HYBRID S', 'badge' => 'bg-success'],
            ['category' => 'Generasi 125', 'model' => 'GEAR ULTIMA 125 HYBRID SOLID', 'badge' => 'bg-success'],
            ['category' => 'Generasi 125', 'model' => 'GEAR ULTIMA 125 HYBRID SMART', 'badge' => 'bg-success'],
            ['category' => 'Classy Yamaha', 'model' => 'FAZZIO HYBRID', 'badge' => 'bg-warning text-dark'],
            ['category' => 'Classy Yamaha', 'model' => 'FAZZIO HYBRID NEO', 'badge' => 'bg-warning text-dark'],
            ['category' => 'Classy Yamaha', 'model' => 'FAZZIO HYBRID LUX', 'badge' => 'bg-warning text-dark'],
            ['category' => 'Classy Yamaha', 'model' => 'FAZZIO STARRY NIGHT', 'badge' => 'bg-warning text-dark'],
            ['category' => 'Classy Yamaha', 'model' => 'GRAND FILANO HYBRID NEO', 'badge' => 'bg-info text-dark'],
            ['category' => 'Classy Yamaha', 'model' => 'GRAND FILANO HYBRID LUX', 'badge' => 'bg-info text-dark'],
            ['category' => 'Generasi 125', 'model' => 'FREEGO 125', 'badge' => 'bg-success'],
            ['category' => 'Moped / Bebek', 'model' => 'VEGA FORCE DB CW', 'badge' => 'bg-secondary'],
            ['category' => 'Moped / Bebek', 'model' => 'JUPITER Z CW FI', 'badge' => 'bg-secondary'],
            ['category' => 'Moped / Bebek', 'model' => 'MX KING 150', 'badge' => 'bg-dark'],
            ['category' => 'Moped / Bebek', 'model' => 'MX KING 150 PRIMA PRAMAC', 'badge' => 'bg-dark'],
            ['category' => 'MAXi Yamaha', 'model' => 'LEXI LX 155 C', 'badge' => 'bg-secondary'],
            ['category' => 'MAXi Yamaha', 'model' => 'LEXI LX 155 S C', 'badge' => 'bg-secondary'],
            ['category' => 'MAXi Yamaha', 'model' => 'ALL NEW AEROX 155', 'badge' => 'bg-primary'],
            ['category' => 'MAXi Yamaha', 'model' => 'ALL NEW AEROX 155 CYBERCITY', 'badge' => 'bg-primary'],
            ['category' => 'MAXi Yamaha', 'model' => 'AEROX ALPHA', 'badge' => 'bg-primary'],
            ['category' => 'MAXi Yamaha', 'model' => 'AEROX ALPHA CYBERCITY ABS', 'badge' => 'bg-primary'],
            ['category' => 'MAXi Yamaha', 'model' => 'AEROX ALPHA TURBO', 'badge' => 'bg-primary'],
            ['category' => 'MAXi Yamaha', 'model' => 'NMAX NEO', 'badge' => 'bg-danger'],
            ['category' => 'MAXi Yamaha', 'model' => 'NMAX NEO S', 'badge' => 'bg-danger'],
            ['category' => 'MAXi Yamaha', 'model' => 'NMAX TURBO', 'badge' => 'bg-danger'],
            ['category' => 'MAXi Yamaha', 'model' => 'NMAX TURBO TECH MAX', 'badge' => 'bg-danger'],
            ['category' => 'MAXi Yamaha', 'model' => 'XMAX C', 'badge' => 'bg-dark'],
            ['category' => 'MAXi Yamaha', 'model' => 'XMAX TECH MAX', 'badge' => 'bg-dark'],
            ['category' => 'Sport', 'model' => 'ALL NEW VIXION', 'badge' => 'bg-secondary'],
            ['category' => 'Sport & Off-Road', 'model' => 'WR 155 R', 'badge' => 'bg-primary'],
            ['category' => 'Sport Heritage', 'model' => 'ALL NEW XSR 155', 'badge' => 'bg-warning text-dark'],
            ['category' => 'Sport Racing', 'model' => 'ALL NEW R15 C', 'badge' => 'bg-primary'],
        ];

        $yamahaProductsTable = [];
        $recordedModelKeys = [];

        foreach ($productCatalog as $item) {
            $modelName = $item['model'];
            $normalizedModel = strtolower(trim($modelName));

            // Match from adsReports (either product name or unit_terjual matches)
            $matchedAds = $adsReports->filter(function ($ad) use ($normalizedModel) {
                $p = strtolower(trim($ad->product ?? ''));
                $u = strtolower(trim($ad->unit_terjual ?? ''));
                return $p === $normalizedModel || $u === $normalizedModel || str_contains($p, $normalizedModel) || str_contains($u, $normalizedModel);
            });

            // Match from liveReports (unit_terjual matches)
            $matchedLive = $liveReports->filter(function ($live) use ($normalizedModel) {
                $u = strtolower(trim($live->unit_terjual ?? ''));
                return $u === $normalizedModel || str_contains($u, $normalizedModel);
            });

            $pStuAds = (int) $matchedAds->sum('stu');
            $pStuLive = (int) $matchedLive->sum('stu');
            $pTotalStu = $pStuAds + $pStuLive;
            $pBudget = (float) $matchedAds->sum('budget');
            $pPercakapan = (int) $matchedAds->sum('total_percakapan');

            $pCostPerStu = $pTotalStu > 0 ? round($pBudget / $pTotalStu) : 0;
            $pConversionRate = $pPercakapan > 0 ? round(($pTotalStu / $pPercakapan) * 100, 2) : 0;

            $yamahaProductsTable[] = [
                'category' => $item['category'],
                'model' => $modelName,
                'category_badge' => $item['badge'],
                'stu_live' => $pStuLive,
                'stu_ads' => $pStuAds,
                'total_stu' => $pTotalStu,
                'budget' => $pBudget,
                'percakapan' => $pPercakapan,
                'cost_per_stu' => $pCostPerStu,
                'conversion_rate' => $pConversionRate,
                'status' => $pTotalStu >= 5 ? 'Best Seller' : ($pTotalStu >= 2 ? 'High Demand' : ($pPercakapan >= 20 ? 'High Interest' : 'Reguler')),
            ];
            $recordedModelKeys[] = $normalizedModel;
        }

        // Catch any other custom model entries in ads reports that weren't in the 32 catalog items
        $otherAds = $adsReports->filter(function ($ad) use ($recordedModelKeys) {
            $p = strtolower(trim($ad->product ?? ''));
            if (empty($p)) return false;
            foreach ($recordedModelKeys as $k) {
                if ($p === $k || str_contains($p, $k)) return false;
            }
            return true;
        })->groupBy('product');

        foreach ($otherAds as $prodName => $items) {
            $pStu = (int) $items->sum('stu');
            $pBudget = (float) $items->sum('budget');
            $pPercakapan = (int) $items->sum('total_percakapan');
            $yamahaProductsTable[] = [
                'category' => 'Yamaha Lainnya',
                'model' => $prodName,
                'category_badge' => 'bg-secondary',
                'stu_live' => 0,
                'stu_ads' => $pStu,
                'total_stu' => $pStu,
                'budget' => $pBudget,
                'percakapan' => $pPercakapan,
                'cost_per_stu' => $pStu > 0 ? round($pBudget / $pStu) : 0,
                'conversion_rate' => $pPercakapan > 0 ? round(($pStu / $pPercakapan) * 100, 2) : 0,
                'status' => 'Reguler',
            ];
        }

        // Calculate share of sales (%)
        foreach ($yamahaProductsTable as &$pRow) {
            $pRow['stu_share_pct'] = $grandTotalStu > 0 ? round(($pRow['total_stu'] / $grandTotalStu) * 100, 1) : 0;
        }
        unset($pRow);

        // Sort products by total STU descending
        usort($yamahaProductsTable, fn($a, $b) => $b['total_stu'] <=> $a['total_stu']);

        // Backwards compatibility product stats
        $productStats = collect($yamahaProductsTable)->map(function ($item) {
            return [
                'product' => $item['model'],
                'stu' => $item['total_stu'],
                'budget' => $item['budget'],
                'percakapan' => $item['percakapan'],
                'cost_per_stu' => $item['cost_per_stu'],
                'conversion_rate' => $item['conversion_rate'],
            ];
        })->all();

        // 12. Top Campaigns & Top Live Hosts
        $topCampaigns = $adsReports->sortByDesc('stu')->take(5)->values()->all();
        $topLiveSessions = $liveReports->sortByDesc('stu')->take(5)->values()->all();

        // 13. System Leaderboard
        $leaderboard = $this->calculateLeaderboard($tahun, $bulan);
        $overallAchievement = $leaderboard->avg('achievement_pct') ?: 0;
        $statusBadge = KpiService::getStatusBadge($overallAchievement);

        // 14. Executive AI & Business Takeaways Generator
        $executiveInsights = $this->generateExecutiveInsights(
            $scorecard,
            $grandTotalStu,
            $stuLiveTikTok,
            $stuMetaAds,
            $totalProspectsDigital,
            $totalProspectTarget,
            $costPerStu,
            $conversionRateProspectToStu
        );

        return [
            // Period & Meta
            'tahun' => $tahun,
            'bulan' => $bulan,
            'nama_bulan' => Carbon::create($tahun, $bulan, 1)->translatedFormat('F'),
            'total_branches' => $totalBranches,
            'missing_branches_today' => $missingBranchesToday,
            'missing_count_today' => $missingBranchesToday->count(),

            // STU Core Metrics
            'grand_total_stu' => $grandTotalStu,
            'stu_tiktok_live' => $stuLiveTikTok,
            'stu_meta_ads' => $stuMetaAds,
            'stu_live_pct' => $stuLivePct,
            'stu_ads_pct' => $stuAdsPct,

            // Targets & Achievements
            'total_dealer_stu_target' => $totalDealerStuTarget,
            'total_prospect_target' => $totalProspectTarget,
            'total_digital_stu_target' => $totalDigitalStuTarget,
            'overall_dealer_stu_achievement_pct' => $overallDealerStuAchvPct,
            'overall_prospect_achievement_pct' => $overallProspectAchvPct,
            'overall_digital_stu_achievement_pct' => $overallDigitalStuAchvPct,

            // Prospects & Leads
            'total_prospects_digital' => $totalProspectsDigital,
            'total_ads_conversations' => $totalAdsConversations,
            'conversion_rate_prospect_to_stu' => $conversionRateProspectToStu,

            // Financial & Ads Performance
            'total_ads_budget' => $totalAdsBudget,
            'total_ads_views' => $totalAdsViews,
            'cost_per_stu' => $costPerStu,
            'avg_ads_cpl' => $avgAdsCpl,
            'avg_ads_cpm' => $avgAdsCpm,
            'avg_ads_cpc' => $avgAdsCpc,
            'avg_ads_ctr' => $avgAdsCtr,

            // TikTok Live Streaming
            'total_live_sessions' => $totalLiveSessions,
            'total_live_hours' => $totalLiveHours,
            'total_live_viewers' => $totalLiveViewers,
            'total_live_likes' => $totalLiveLikes,
            'total_live_comments' => $totalLiveComments,
            'total_live_shares' => $totalLiveShares,
            'avg_stu_per_live_session' => $avgStuPerLiveSession,

            // Social & Reputation
            'total_posts' => $totalPosts,
            'followers_growth' => $followersGrowth,
            'total_views' => $totalViews,
            'avg_google_rating' => round($avgGoogleRating, 1),
            'google_reviews' => $googleReviews,

            // Tables & Breakdowns
            'scorecard' => $scorecard,
            'stu_scorecard' => $stuScorecard,
            'prospect_scorecard' => $prospectScorecard,
            'yamaha_products_table' => $yamahaProductsTable,
            'cutoff_day' => $cutoffDay,
            'last_month_name' => $lastMonthCarbon->translatedFormat('F'),
            'last_month_year' => $lastMonthTahun,
            'is_current_month' => $isCurrentMonth,
            'product_stats' => $productStats,
            'top_campaigns' => $topCampaigns,
            'top_live_sessions' => $topLiveSessions,

            // Monthly Trends Data (Jan-Dec)
            'monthly_trend_months' => $monthNames,
            'monthly_stu_live' => array_values($monthlyStuLive),
            'monthly_stu_ads' => array_values($monthlyStuAds),
            'monthly_stu_total' => array_values($monthlyStuTotal),
            'monthly_prospects' => array_values($monthlyProspects),
            'monthly_budget' => array_values($monthlyBudget),

            // Leaderboard & System
            'overall_achievement' => round($overallAchievement, 1),
            'status_badge' => $statusBadge,
            'leaderboard' => $leaderboard,
            'kpi_target' => $target,

            // AI / Executive Takeaways
            'executive_insights' => $executiveInsights,
        ];
    }

    /**
     * Generate dynamic, highly actionable executive business insights
     */
    protected function generateExecutiveInsights(
        array $scorecard,
        int $grandTotalStu,
        int $stuLive,
        int $stuAds,
        int $totalProspects,
        int $targetProspects,
        float $costPerStu,
        float $conversionRate
    ): array {
        $insights = [];

        // 1. Top Performing STU Branch
        if (!empty($scorecard)) {
            $topBranch = $scorecard[0];
            $contribution = $grandTotalStu > 0 ? round(($topBranch['total_stu'] / $grandTotalStu) * 100, 1) : 0;
            $insights[] = [
                'type' => 'success',
                'icon' => 'fas fa-trophy',
                'title' => 'Top STU Contributor',
                'text' => "<strong>{$topBranch['nama_cabang']}</strong> memimpin penjualan digital dengan total <strong>{$topBranch['total_stu']} Unit STU</strong> (kontribusi {$contribution}% dari total perusahaan).",
            ];
        }

        // 2. Channel Dynamics (Live vs Ads)
        $channelDominance = $stuAds >= $stuLive ? 'Meta Ads' : 'Live TikTok';
        $channelDiff = abs($stuAds - $stuLive);
        $insights[] = [
            'type' => 'info',
            'icon' => 'fas fa-chart-pie',
            'title' => 'Channel Sales Dominance',
            'text' => "Saluran <strong>{$channelDominance}</strong> menyumbang kontribusi STU terbesar (" . ($stuAds >= $stuLive ? "{$stuAds} Unit" : "{$stuLive} Unit") . "), selisih {$channelDiff} unit dibanding channel lainnya. Optimalisasi omnichannel tetap disarankan.",
        ];

        // 3. Prospect Target Achievement (130% Mandate)
        $prospectPace = $targetProspects > 0 ? round(($totalProspects / $targetProspects) * 100, 1) : 0;
        if ($prospectPace >= 100) {
            $insights[] = [
                'type' => 'success',
                'icon' => 'fas fa-bullseye',
                'title' => 'Target Prospek Tercapai (130%)',
                'text' => "Realisasi prospek mencapai <strong>{$totalProspects} Prospek</strong> ({$prospectPace}% dari target 130% STU: {$targetProspects}). Pipeline penjualan sangat sehat.",
            ];
        } else {
            $deficit = $targetProspects - $totalProspects;
            $insights[] = [
                'type' => 'warning',
                'icon' => 'fas fa-exclamation-circle',
                'title' => 'Pace Prospek Di Bawah 130%',
                'text' => "Realisasi prospek baru mencapai {$totalProspects} dari target {$targetProspects} prospek (kurang {$deficit} prospek). Perlu boost iklan Meta Ads & interaksi form prospek.",
            ];
        }

        // 4. Unit Economics & Conversion Efficiency
        $insights[] = [
            'type' => 'primary',
            'icon' => 'fas fa-coins',
            'title' => 'Efisiensi Biaya Penjualan (Unit Economics)',
            'text' => "Biaya akuisisi iklan rata-rata adalah <strong>Rp " . number_format($costPerStu, 0, ',', '.') . " per STU</strong> dengan conversion rate prospek-ke-STU sebesar <strong>{$conversionRate}%</strong>.",
        ];

        // 5. Branch Requiring Acceleration
        if (count($scorecard) > 1) {
            $underperforming = end($scorecard);
            if ($underperforming['stu_achievement_pct'] < 50) {
                $insights[] = [
                    'type' => 'danger',
                    'icon' => 'fas fa-exclamation-triangle',
                    'title' => 'Perhatian Khusus (Butuh Akselerasi)',
                    'text' => "<strong>{$underperforming['nama_cabang']}</strong> mencatat pencapaian STU terendah ({$underperforming['stu_achievement_pct']}%). Direkomendasikan evaluasi jam live streaming dan rotasi materi iklan.",
                ];
            }
        }

        return $insights;
    }

    /**
     * Calculate Leaderboard rankings
     */
    public function calculateLeaderboard(int $tahun, int $bulan): Collection
    {
        $branches = Branch::where('status', 'active')->get();
        $kpi = Kpi::with('target')->where('tahun', $tahun)->where('bulan', $bulan)->first();
        $target = $kpi?->target;

        $results = collect();

        foreach ($branches as $branch) {
            $reports = DailyReport::where('branch_id', $branch->id)
                ->whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan)
                ->get();

            $liveStu = TiktokLiveReport::where('branch_id', $branch->id)->whereYear('tanggal_live', $tahun)->whereMonth('tanggal_live', $bulan)->sum('stu');
            $adsStu = MetaAdsReport::where('branch_id', $branch->id)->where('tahun', $tahun)->where('bulan', $bulan)->sum('stu');
            $totalStu = $liveStu + $adsStu;

            $dealerStuTarget = $target ? $target->getDealerStuTargetForBranch($branch->id, $branch->nama_cabang) : KpiTarget::resolveTargetForBranchName($branch->nama_cabang);

            if (!$target || $reports->isEmpty()) {
                $achievementPct = $totalStu > 0 ? min(100, round(($totalStu / max(1, $dealerStuTarget)) * 100, 1)) : 0;
            } else {
                $igFeedPct = $target->ig_feed_target > 0 ? ($reports->sum('ig_feed') / $target->ig_feed_target) * 100 : 100;
                $igReelsPct = $target->ig_reels_target > 0 ? ($reports->sum('ig_reels') / $target->ig_reels_target) * 100 : 100;
                $fbPostPct = $target->fb_post_target > 0 ? ($reports->sum('fb_post') / $target->fb_post_target) * 100 : 100;
                $tiktokPostPct = $target->tiktok_post_target > 0 ? ($reports->sum('tiktok_post') / $target->tiktok_post_target) * 100 : 100;
                $stuPct = $dealerStuTarget > 0 ? ($totalStu / $dealerStuTarget) * 100 : 100;

                $achievementPct = round(($igFeedPct + $igReelsPct + $fbPostPct + $tiktokPostPct + ($stuPct * 2)) / 6, 1);
            }

            $badge = KpiService::getStatusBadge($achievementPct);

            $results->push([
                'branch_id' => $branch->id,
                'kode' => $branch->kode,
                'nama_cabang' => $branch->nama_cabang,
                'area' => $branch->area,
                'total_posts' => $reports->sum('ig_feed') + $reports->sum('ig_reels') + $reports->sum('fb_post') + $reports->sum('tiktok_post'),
                'total_followers_gained' => $reports->sum('ig_followers_gained') + $reports->sum('fb_followers_gained') + $reports->sum('tiktok_followers_gained'),
                'total_stu' => $totalStu,
                'achievement_pct' => $achievementPct,
                'badge' => $badge,
            ]);
        }

        return $results->sortByDesc('achievement_pct')->values()->map(function ($item, $index) {
            $item['rank'] = $index + 1;
            return $item;
        });
    }
}
