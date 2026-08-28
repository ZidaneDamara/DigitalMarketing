<?php

namespace App\Http\Controllers\Export;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\DailyReport;
use App\Models\MonthlyInsight;
use App\Models\TiktokLiveReport;
use App\Models\WeeklyReport;
use App\Services\ExcelExporterService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function index()
    {
        $branches = Branch::where('status', 'active')->get();
        return view('export.index', compact('branches'));
    }

    public function exportPdf(Request $request)
    {
        $type = $request->input('report_type', 'daily');
        $tahun = $request->input('tahun', now()->year);
        $bulan = $request->input('bulan', now()->month);
        $branchId = $request->input('branch_id');
        $tanggal = $request->input('tanggal');
        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');
        $mingguKe = $request->input('minggu_ke');

        $branchQuery = Branch::where('status', 'active');
        if ($branchId) {
            $branchQuery->where('id', $branchId);
        }
        $branches = $branchQuery->get();

        $dailyReports = collect();
        $weeklyReports = collect();
        $monthlyInsights = collect();
        $tiktokLiveReports = collect();

        if ($type === 'daily') {
            $query = DailyReport::with(['branch', 'user']);
            if ($branchId) {
                $query->where('branch_id', $branchId);
            }
            if ($tanggal) {
                $query->where('tanggal', $tanggal);
            } elseif ($tanggalAwal && $tanggalAkhir) {
                $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
            } else {
                $query->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan);
            }
            $dailyReports = $query->latest('tanggal')->get();

        } elseif ($type === 'tiktok_live') {
            $query = TiktokLiveReport::with(['branch', 'user']);
            if ($branchId) {
                $query->where('branch_id', $branchId);
            }
            if ($tanggal) {
                $query->where('tanggal_live', $tanggal);
            } elseif ($tanggalAwal && $tanggalAkhir) {
                $query->whereBetween('tanggal_live', [$tanggalAwal, $tanggalAkhir]);
            } else {
                $query->whereYear('tanggal_live', $tahun)->whereMonth('tanggal_live', $bulan);
            }
            $tiktokLiveReports = $query->latest('tanggal_live')->get();

        } elseif ($type === 'weekly') {
            $query = WeeklyReport::with(['branch', 'user']);
            if ($branchId) {
                $query->where('branch_id', $branchId);
            }
            if ($tanggalAwal && $tanggalAkhir) {
                $query->whereBetween('tanggal_post', [$tanggalAwal, $tanggalAkhir]);
            } elseif ($mingguKe) {
                $query->where('tahun', $tahun)->where('minggu_ke', $mingguKe);
            } else {
                $query->where('tahun', $tahun);
            }
            $weeklyReports = $query->latest('tanggal_post')->get();

        } else {
            $monthlyInsights = MonthlyInsight::with('branch')
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->get();
        }

        $pdf = Pdf::loadView('export.pdf_template', compact(
            'type', 'branches', 'dailyReports', 'weeklyReports', 'monthlyInsights', 'tiktokLiveReports',
            'tahun', 'bulan', 'tanggal', 'tanggalAwal', 'tanggalAkhir', 'mingguKe'
        ))->setPaper('a4', 'landscape');

        $titleType = ucfirst($type);
        return $pdf->download("DMPMS_{$titleType}_Report.pdf");
    }

    public function exportExcel(Request $request, ExcelExporterService $exporter)
    {
        $type = $request->input('report_type', 'daily');
        $tahun = $request->input('tahun', now()->year);
        $bulan = $request->input('bulan', now()->month);
        $branchId = $request->input('branch_id');
        $tanggal = $request->input('tanggal');
        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');
        $mingguKe = $request->input('minggu_ke');

        $branchName = null;
        if ($branchId) {
            $branch = Branch::find($branchId);
            $branchName = $branch ? $branch->nama_cabang : null;
        }

        $meta = [
            'type' => $type,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'branch_name' => $branchName,
            'tanggal' => $tanggal,
            'tanggal_awal' => $tanggalAwal,
            'tanggal_akhir' => $tanggalAkhir,
            'minggu_ke' => $mingguKe,
        ];

        if ($type === 'daily') {
            $query = DailyReport::with(['branch', 'user']);
            if ($branchId) {
                $query->where('branch_id', $branchId);
            }
            if ($tanggal) {
                $query->where('tanggal', $tanggal);
            } elseif ($tanggalAwal && $tanggalAkhir) {
                $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
            } else {
                $query->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan);
            }
            $reports = $query->latest('tanggal')->get();

        } elseif ($type === 'tiktok_live') {
            $query = TiktokLiveReport::with(['branch', 'user']);
            if ($branchId) {
                $query->where('branch_id', $branchId);
            }
            if ($tanggal) {
                $query->where('tanggal_live', $tanggal);
            } elseif ($tanggalAwal && $tanggalAkhir) {
                $query->whereBetween('tanggal_live', [$tanggalAwal, $tanggalAkhir]);
            } else {
                $query->whereYear('tanggal_live', $tahun)->whereMonth('tanggal_live', $bulan);
            }
            $reports = $query->latest('tanggal_live')->get();

        } elseif ($type === 'weekly') {
            $query = WeeklyReport::with(['branch', 'user']);
            if ($branchId) {
                $query->where('branch_id', $branchId);
            }
            if ($tanggalAwal && $tanggalAkhir) {
                $query->whereBetween('tanggal_post', [$tanggalAwal, $tanggalAkhir]);
            } elseif ($mingguKe) {
                $query->where('tahun', $tahun)->where('minggu_ke', $mingguKe);
            } else {
                $query->where('tahun', $tahun);
            }
            $reports = $query->latest('tanggal_post')->get();

        } else {
            $reports = MonthlyInsight::with(['branch'])
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->get();
        }

        return $exporter->export($type, $reports, $meta);
    }
}
