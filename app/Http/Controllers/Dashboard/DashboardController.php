<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\DailyReport;
use App\Models\MonthlyInsight;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $tahun = $request->input('tahun', now()->year);
        $bulan = $request->input('bulan', now()->month);
        
        // If user is PIC Digital, restrict filter to own branch
        $branchId = $user->hasRole('PIC Digital Cabang') ? $user->branch_id : $request->input('branch_id');

        $metrics = $this->dashboardService->getDashboardMetrics($tahun, $bulan, $branchId);
        $branches = Branch::where('status', 'active')->get();

        // Chart Data Generation for Posting Trend (Days of the month)
        $dailyData = DailyReport::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->orderBy('tanggal')
            ->get();

        $chartLabels = [];
        $igPostsSeries = [];
        $fbPostsSeries = [];
        $tiktokPostsSeries = [];

        foreach ($dailyData->groupBy(fn($item) => $item->tanggal->format('d M')) as $dateLabel => $items) {
            $chartLabels[] = $dateLabel;
            $igPostsSeries[] = $items->sum('ig_feed') + $items->sum('ig_reels');
            $fbPostsSeries[] = $items->sum('fb_post') + $items->sum('fb_marketplace');
            $tiktokPostsSeries[] = $items->sum('tiktok_post') + $items->sum('tiktok_live');
        }

        // Views Trend Data from Monthly Insight
        $monthlyInsightsData = MonthlyInsight::with('branch')
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->get();

        $platformDistribution = [
            'Instagram Views' => $monthlyInsightsData->sum('ig_views'),
            'Facebook Views' => $monthlyInsightsData->sum('fb_views'),
            'TikTok Views' => $monthlyInsightsData->sum('tiktok_views'),
        ];

        return view('dashboard.index', compact(
            'metrics',
            'branches',
            'tahun',
            'bulan',
            'branchId',
            'chartLabels',
            'igPostsSeries',
            'fbPostsSeries',
            'tiktokPostsSeries',
            'platformDistribution'
        ));
    }
}
