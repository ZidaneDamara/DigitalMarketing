<?php

namespace App\Http\Controllers\Kpi;

use App\Http\Controllers\Controller;
use App\Http\Requests\CopyKpiRequest;
use App\Http\Requests\KpiRequest;
use App\Models\Kpi;
use App\Models\KpiTarget;
use App\Services\AuditLogService;
use App\Services\KpiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KpiManagementController extends Controller
{
    protected $kpiService;

    public function __construct(KpiService $kpiService)
    {
        $this->kpiService = $kpiService;
    }

    public function index(Request $request)
    {
        $tahun = $request->input('tahun', now()->year);
        $bulan = $request->input('bulan', now()->month);

        $kpis = Kpi::with('target')->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->get();
        $selectedKpi = Kpi::with('target')->where('tahun', $tahun)->where('bulan', $bulan)->first();

        return view('kpi.index', compact('kpis', 'selectedKpi', 'tahun', 'bulan'));
    }

    public function storeOrUpdate(KpiRequest $request)
    {
        $data = $request->validated();
        $user = Auth::user();

        $kpi = Kpi::updateOrCreate(
            ['tahun' => $data['tahun'], 'bulan' => $data['bulan']],
            ['created_by' => $user->id]
        );

        KpiTarget::updateOrCreate(
            ['kpi_id' => $kpi->id],
            [
                'ig_feed_target' => $data['ig_feed_target'],
                'ig_reels_target' => $data['ig_reels_target'],
                'ig_story_target' => $data['ig_story_target'],
                'ig_followers_target' => $data['ig_followers_target'],
                'fb_post_target' => $data['fb_post_target'],
                'fb_marketplace_target' => $data['fb_marketplace_target'],
                'fb_followers_target' => $data['fb_followers_target'],
                'tiktok_post_target' => $data['tiktok_post_target'],
                'tiktok_live_target' => $data['tiktok_live_target'],
                'tiktok_followers_target' => $data['tiktok_followers_target'],
                'google_rating_target' => $data['google_rating_target'],
                'google_review_target' => $data['google_review_target'],
            ]
        );

        AuditLogService::log('UPDATE', 'KPI Management', "Memperbarui target KPI periode {$data['bulan']}/{$data['tahun']}");

        return redirect()->route('kpis.index', ['tahun' => $data['tahun'], 'bulan' => $data['bulan']])
            ->with('success', "Target KPI untuk periode {$data['bulan']}/{$data['tahun']} berhasil disimpan.");
    }

    public function copy(CopyKpiRequest $request)
    {
        $data = $request->validated();

        $this->kpiService->copyPreviousMonthKpi(
            $data['from_tahun'],
            $data['from_bulan'],
            $data['to_tahun'],
            $data['to_bulan'],
            Auth::id()
        );

        return redirect()->route('kpis.index', ['tahun' => $data['to_tahun'], 'bulan' => $data['to_bulan']])
            ->with('success', "Berhasil menyalin target KPI dari {$data['from_bulan']}/{$data['from_tahun']} ke {$data['to_bulan']}/{$data['to_tahun']}. Silakan sesuaikan angka jika diperlukan.");
    }
}
