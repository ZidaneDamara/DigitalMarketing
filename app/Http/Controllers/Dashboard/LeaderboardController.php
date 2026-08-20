<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(Request $request)
    {
        $tahun = $request->input('tahun', now()->year);
        $bulan = $request->input('bulan', now()->month);

        $leaderboard = $this->dashboardService->calculateLeaderboard($tahun, $bulan);

        return view('dashboard.leaderboard', compact('leaderboard', 'tahun', 'bulan'));
    }
}
