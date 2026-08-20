<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\DailyReport;
use App\Models\Kpi;
use App\Models\MonthlyInsight;
use Carbon\Carbon;

class DashboardService
{
    public function getDashboardMetrics(?int $tahun = null, ?int $bulan = null, ?int $branchId = null)
    {
        $tahun = $tahun ?? now()->year;
        $bulan = $bulan ?? now()->month;

        $branchQuery = Branch::where('status', 'active');
        if ($branchId) {
            $branchQuery->where('id', $branchId);
        }
        $branches = $branchQuery->get();
        $totalBranches = $branches->count();

        // Branches that haven't submitted today's daily report
        $today = now()->format('Y-m-d');
        $submittedBranchIdsToday = DailyReport::where('tanggal', $today)->pluck('branch_id')->toArray();
        
        $missingBranchesToday = Branch::where('status', 'active')
            ->whereNotIn('id', $submittedBranchIdsToday)
            ->get();

        // Get Monthly KPI targets
        $kpi = Kpi::with('target')->where('tahun', $tahun)->where('bulan', $bulan)->first();
        $target = $kpi?->target;

        // Daily Reports aggregated for month/year
        $dailyQuery = DailyReport::whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan);
        if ($branchId) {
            $dailyQuery->where('branch_id', $branchId);
        }
        $dailyReports = $dailyQuery->get();

        $totalIgFeed = $dailyReports->sum('ig_feed');
        $totalIgReels = $dailyReports->sum('ig_reels');
        $totalIgStory = $dailyReports->sum('ig_story');
        $totalIgFollowersGained = $dailyReports->sum('ig_followers_gained');

        $totalFbPost = $dailyReports->sum('fb_post');
        $totalFbMarketplace = $dailyReports->sum('fb_marketplace');
        $totalFbFollowersGained = $dailyReports->sum('fb_followers_gained');

        $totalTiktokPost = $dailyReports->sum('tiktok_post');
        $totalTiktokLive = $dailyReports->sum('tiktok_live');
        $totalTiktokFollowersGained = $dailyReports->sum('tiktok_followers_gained');

        $avgGoogleRating = $dailyReports->avg('google_rating') ?: 0;
        $totalGoogleReviewGained = $dailyReports->sum('google_review_gained');

        $totalPosts = $totalIgFeed + $totalIgReels + $totalIgStory + $totalFbPost + $totalFbMarketplace + $totalTiktokPost;
        $totalFollowersGrowth = $totalIgFollowersGained + $totalFbFollowersGained + $totalTiktokFollowersGained;

        // Monthly Insights aggregated
        $insightQuery = MonthlyInsight::where('tahun', $tahun)->where('bulan', $bulan);
        if ($branchId) {
            $insightQuery->where('branch_id', $branchId);
        }
        $monthlyInsights = $insightQuery->get();

        $totalViews = $monthlyInsights->sum('ig_views') + $monthlyInsights->sum('fb_views') + $monthlyInsights->sum('tiktok_views');

        // Calculate KPI Achievements percentage per branch for Leaderboard
        $leaderboard = $this->calculateLeaderboard($tahun, $bulan);

        // Overall System Achievement
        $overallAchievement = $leaderboard->avg('achievement_pct') ?: 0;
        $statusBadge = KpiService::getStatusBadge($overallAchievement);

        return [
            'total_branches' => $totalBranches,
            'missing_branches_today' => $missingBranchesToday,
            'missing_count_today' => $missingBranchesToday->count(),
            'total_posts' => $totalPosts,
            'followers_growth' => $totalFollowersGrowth,
            'total_views' => $totalViews,
            'avg_google_rating' => round($avgGoogleRating, 1),
            'google_reviews' => $totalGoogleReviewGained,
            'overall_achievement' => round($overallAchievement, 1),
            'status_badge' => $statusBadge,
            'leaderboard' => $leaderboard,
            'kpi_target' => $target,
        ];
    }

    public function calculateLeaderboard(int $tahun, int $bulan)
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

            if (!$target || $reports->isEmpty()) {
                $achievementPct = 0;
            } else {
                // Compute ratio for key metrics
                $igFeedPct = $target->ig_feed_target > 0 ? ($reports->sum('ig_feed') / $target->ig_feed_target) * 100 : 100;
                $igReelsPct = $target->ig_reels_target > 0 ? ($reports->sum('ig_reels') / $target->ig_reels_target) * 100 : 100;
                $fbPostPct = $target->fb_post_target > 0 ? ($reports->sum('fb_post') / $target->fb_post_target) * 100 : 100;
                $tiktokPostPct = $target->tiktok_post_target > 0 ? ($reports->sum('tiktok_post') / $target->tiktok_post_target) * 100 : 100;
                $follPct = $target->ig_followers_target > 0 ? ($reports->sum('ig_followers_gained') / $target->ig_followers_target) * 100 : 100;

                $achievementPct = round(($igFeedPct + $igReelsPct + $fbPostPct + $tiktokPostPct + $follPct) / 5, 1);
            }

            $badge = KpiService::getStatusBadge($achievementPct);

            $results->push([
                'branch_id' => $branch->id,
                'kode' => $branch->kode,
                'nama_cabang' => $branch->nama_cabang,
                'area' => $branch->area,
                'total_posts' => $reports->sum('ig_feed') + $reports->sum('ig_reels') + $reports->sum('fb_post') + $reports->sum('tiktok_post'),
                'total_followers_gained' => $reports->sum('ig_followers_gained') + $reports->sum('fb_followers_gained') + $reports->sum('tiktok_followers_gained'),
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
