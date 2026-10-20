<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\MetaAdsReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MetaAdsDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $tahun = $request->input('tahun', now()->year);
        $bulan = $request->input('bulan', now()->month);
        $branchId = $user->hasRole('PIC Digital Cabang') ? $user->branch_id : $request->input('branch_id');

        $query = MetaAdsReport::with(['branch', 'closingProofs'])
            ->where('tahun', $tahun);

        if ($bulan) {
            $query->where('bulan', $bulan);
        }

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $reports = $query->get();

        // High-level aggregates
        $totalBudget = $reports->sum('budget');
        $totalViews = $reports->sum('total_views');
        $totalPercakapan = $reports->sum('total_percakapan');
        $totalStu = $reports->sum('stu');
        $countReports = $reports->count();

        $avgCtr = $countReports > 0 ? round($reports->avg('ctr'), 2) : 0;
        $avgCpc = $countReports > 0 ? round($reports->avg('cpc')) : 0;
        $avgCpm = $countReports > 0 ? round($reports->avg('cpm')) : 0;
        $avgCpl = $countReports > 0 ? round($reports->avg('cpl')) : 0;

        $costPerStu = $totalStu > 0 ? round($totalBudget / $totalStu) : 0;
        $conversionRate = $totalPercakapan > 0 ? round(($totalStu / $totalPercakapan) * 100, 2) : 0;

        // 1. Monthly Trends (12 months for current year)
        $monthlyQuery = MetaAdsReport::where('tahun', $tahun);
        if ($branchId) {
            $monthlyQuery->where('branch_id', $branchId);
        }
        $monthlyReports = $monthlyQuery->get();

        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $monthlyBudget = array_fill(1, 12, 0);
        $monthlyPercakapan = array_fill(1, 12, 0);
        $monthlyStu = array_fill(1, 12, 0);

        foreach ($monthlyReports as $r) {
            $m = (int) $r->bulan;
            if (isset($monthlyBudget[$m])) {
                $monthlyBudget[$m] += (float) $r->budget;
                $monthlyPercakapan[$m] += (int) $r->total_percakapan;
                $monthlyStu[$m] += (int) $r->stu;
            }
        }

        // 2. Product Performance
        $productStats = $reports->groupBy('product')->map(function ($items, $product) {
            return [
                'product' => $product,
                'budget' => $items->sum('budget'),
                'views' => $items->sum('total_views'),
                'percakapan' => $items->sum('total_percakapan'),
                'stu' => $items->sum('stu'),
                'avg_cpl' => $items->avg('cpl'),
            ];
        })->sortByDesc('stu')->values();

        // 3. Branch Performance Comparison (All branches for the selected period)
        $branchStatsQuery = MetaAdsReport::where('tahun', $tahun);
        if ($bulan) {
            $branchStatsQuery->where('bulan', $bulan);
        }
        $branchReports = $branchStatsQuery->with('branch')->get();

        $allBranches = Branch::where('status', 'active')->get();
        $branchStats = $allBranches->map(function ($b) use ($branchReports) {
            $branchItems = $branchReports->where('branch_id', $b->id);
            return [
                'branch_id' => $b->id,
                'nama_cabang' => $b->nama_cabang,
                'budget' => $branchItems->sum('budget'),
                'percakapan' => $branchItems->sum('total_percakapan'),
                'stu' => $branchItems->sum('stu'),
                'campaign_count' => $branchItems->count(),
            ];
        })->sortByDesc('stu')->values();

        // 4. Top Performing Campaigns
        $topCampaigns = $reports->sortByDesc('stu')->take(5);

        // Branches for filter dropdown
        $branches = Branch::where('status', 'active')->get();

        return view('dashboard.meta_ads.index', compact(
            'tahun',
            'bulan',
            'branchId',
            'branches',
            'reports',
            'totalBudget',
            'totalViews',
            'totalPercakapan',
            'totalStu',
            'avgCtr',
            'avgCpc',
            'avgCpm',
            'avgCpl',
            'costPerStu',
            'conversionRate',
            'monthNames',
            'monthlyBudget',
            'monthlyPercakapan',
            'monthlyStu',
            'productStats',
            'branchStats',
            'topCampaigns'
        ));
    }
}
