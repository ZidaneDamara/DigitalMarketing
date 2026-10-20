<?php

namespace App\Http\Controllers\Kpi;

use App\Http\Controllers\Controller;
use App\Http\Requests\CopyKpiRequest;
use App\Http\Requests\KpiRequest;
use App\Models\Branch;
use App\Models\Kpi;
use App\Models\KpiTarget;
use App\Services\AuditLogService;
use App\Services\KpiEvaluationService;
use App\Services\KpiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KpiManagementController extends Controller
{
    protected $kpiService;
    protected $evaluationService;

    public function __construct(KpiService $kpiService, KpiEvaluationService $evaluationService)
    {
        $this->kpiService = $kpiService;
        $this->evaluationService = $evaluationService;
    }

    public function index(Request $request)
    {
        $tahun = (int) $request->input('tahun', now()->year);
        $bulan = (int) $request->input('bulan', now()->month);
        $user = Auth::user();

        $kpis = Kpi::with('target')->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->get();
        $selectedKpi = Kpi::with('target')->where('tahun', $tahun)->where('bulan', $bulan)->first();
        $target = $selectedKpi?->target;

        $branches = Branch::where('status', 'active')->get();

        // If user is PIC Digital Cabang, prioritize their branch
        $userBranchId = $user->hasRole('PIC Digital Cabang') ? $user->branch_id : null;

        // Automated evaluation for all branches for the selected period
        $branchEvaluations = $this->evaluationService->evaluateAllBranches($tahun, $bulan);

        return view('kpi.index', compact(
            'kpis',
            'selectedKpi',
            'target',
            'tahun',
            'bulan',
            'branches',
            'branchEvaluations',
            'userBranchId'
        ));
    }

    public function getBranchEvaluation(Request $request, Branch $branch)
    {
        $tahun = (int) $request->input('tahun', now()->year);
        $bulan = (int) $request->input('bulan', now()->month);

        $evaluation = $this->evaluationService->evaluateBranch($branch, $tahun, $bulan);

        return response()->json([
            'success' => true,
            'data' => $evaluation,
        ]);
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
                'insentif_pokok' => $data['insentif_pokok'] ?? 500000,
                // Kategori Proses
                'sosmed_content_plan_pct' => $data['sosmed_content_plan_pct'] ?? 100,
                'sales_digital_posting_target' => $data['sales_digital_posting_target'] ?? 2,
                'tiktok_live_daily_hours' => $data['tiktok_live_daily_hours'] ?? 3.0,
                'google_review_target' => $data['google_review_target'] ?? 100,
                'google_rating_target' => $data['google_rating_target'] ?? 4.8,
                'google_foto_daily_target' => $data['google_foto_daily_target'] ?? 4,
                'meta_ads_deadline_day' => $data['meta_ads_deadline_day'] ?? 26,
                'report_compliance_pct' => $data['report_compliance_pct'] ?? 100,
                'rapat_review_target' => $data['rapat_review_target'] ?? 2,
                // Kategori Result
                'ig_followers_growth_target' => $data['ig_followers_growth_target'] ?? 150,
                'tiktok_followers_growth_target' => $data['tiktok_followers_growth_target'] ?? 150,
                'prospect_digital_ratio_pct' => $data['prospect_digital_ratio_pct'] ?? 130,
                'dealer_stu_target' => $data['dealer_stu_target'] ?? 50,
                'ads_cpm_max' => $data['ads_cpm_max'] ?? 30000,
                'ads_cpc_max' => $data['ads_cpc_max'] ?? 5000,
                'ads_ctr_min' => $data['ads_ctr_min'] ?? 1.5,
                'stu_digital_ratio_pct' => $data['stu_digital_ratio_pct'] ?? 15.0,
                // Syarat Pembayaran
                'min_result_score' => $data['min_result_score'] ?? 60.0,
                'min_proses_score' => $data['min_proses_score'] ?? 80.0,
                'branch_dealer_stu_targets' => $data['branch_dealer_stu_targets'] ?? null,
                // Backward compatibility defaults
                'ig_feed_target' => $data['ig_feed_target'] ?? 30,
                'ig_reels_target' => $data['ig_reels_target'] ?? 20,
                'ig_story_target' => $data['ig_story_target'] ?? 60,
                'ig_followers_target' => $data['ig_followers_target'] ?? ($data['ig_followers_growth_target'] ?? 150),
                'fb_post_target' => $data['fb_post_target'] ?? 30,
                'fb_marketplace_target' => $data['fb_marketplace_target'] ?? 45,
                'fb_followers_target' => $data['fb_followers_target'] ?? 50,
                'tiktok_post_target' => $data['tiktok_post_target'] ?? 25,
                'tiktok_live_target' => $data['tiktok_live_target'] ?? 15,
                'tiktok_followers_target' => $data['tiktok_followers_target'] ?? ($data['tiktok_followers_growth_target'] ?? 150),
            ]
        );

        AuditLogService::log('UPDATE', 'KPI Management', "Memperbarui target KPI PIC Digital periode {$data['bulan']}/{$data['tahun']}");

        return redirect()->route('kpis.index', ['tahun' => $data['tahun'], 'bulan' => $data['bulan']])
            ->with('success', "Target KPI PIC Digital untuk periode {$data['bulan']}/{$data['tahun']} berhasil disimpan dan dievaluasi.");
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
