<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\DailyReport;
use App\Models\MonthlyInsight;
use App\Services\KpiService;
use Illuminate\Http\Request;

class BranchPerformanceController extends Controller
{
    public function index(Request $request)
    {
        $branches = Branch::where('status', 'active')->get();
        $selectedBranchId = $request->input('branch_id', $branches->first()?->id);
        $tahun = $request->input('tahun', now()->year);

        $selectedBranch = Branch::find($selectedBranchId);

        // Calculate month-by-month timeline achievement
        $monthlyPerformance = [];
        for ($m = 1; $m <= 12; $m++) {
            $reports = DailyReport::where('branch_id', $selectedBranchId)
                ->whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $m)
                ->get();

            $insight = MonthlyInsight::where('branch_id', $selectedBranchId)
                ->where('tahun', $tahun)
                ->where('bulan', $m)
                ->first();

            $totalPosts = $reports->sum('ig_feed') + $reports->sum('ig_reels') + $reports->sum('fb_post') + $reports->sum('tiktok_post');
            $followersGained = $reports->sum('ig_followers_gained') + $reports->sum('fb_followers_gained') + $reports->sum('tiktok_followers_gained');
            $totalViews = $insight ? ($insight->ig_views + $insight->fb_views + $insight->tiktok_views) : 0;

            // Simple achievement estimation based on posts threshold (target: 80 posts/month)
            $achvPct = min(100, round(($totalPosts / 80) * 100, 1));
            $badge = KpiService::getStatusBadge($achvPct);

            $monthlyPerformance[] = [
                'bulan_num' => $m,
                'bulan_name' => date('F', mktime(0, 0, 0, $m, 1)),
                'total_posts' => $totalPosts,
                'followers_gained' => $followersGained,
                'total_views' => $totalViews,
                'achv_pct' => $achvPct,
                'badge' => $badge,
            ];
        }

        return view('dashboard.branch_performance', compact('branches', 'selectedBranch', 'selectedBranchId', 'tahun', 'monthlyPerformance'));
    }
}
