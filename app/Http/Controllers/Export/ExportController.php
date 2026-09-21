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
            $data = $this->getTiktokLiveExportData($request, $branchId, $tanggalAwal, $tanggalAkhir, $tanggal, $tahun, $bulan);
            $tiktokLiveReports = $data['reports'];
            $tiktokLiveSummary = $data['summary'];
            $tiktokLiveBranchRankings = $data['branch_rankings'];
            $tiktokLiveHostRankings = $data['host_rankings'];
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
            'tiktokLiveSummary', 'tiktokLiveBranchRankings', 'tiktokLiveHostRankings',
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
            $data = $this->getTiktokLiveExportData($request, $branchId, $tanggalAwal, $tanggalAkhir, $tanggal, $tahun, $bulan);
            $reports = $data['reports'];
            $meta['tiktok_summary'] = $data['summary'];
            $meta['branch_rankings'] = $data['branch_rankings'];
            $meta['host_rankings'] = $data['host_rankings'];

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

    private function getTiktokLiveExportData(Request $request, $branchId, $tanggalAwal, $tanggalAkhir, $tanggal, $tahun, $bulan)
    {
        $query = TiktokLiveReport::with(['branch', 'user']);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $start = $tanggalAwal ?: $tanggal;
        if ($start && $tanggalAkhir) {
            $query->whereBetween('tanggal_live', [$start, $tanggalAkhir]);
        } elseif ($start) {
            $query->where('tanggal_live', $start);
        } elseif ($tanggalAkhir) {
            $query->where('tanggal_live', '<=', $tanggalAkhir);
        } else {
            $query->whereYear('tanggal_live', $tahun)->whereMonth('tanggal_live', $bulan);
        }

        $tiktokLiveReports = (clone $query)->latest('tanggal_live')->get();

        // Calculate KPI Metrics
        $totalSesi = $tiktokLiveReports->count();
        $totalMenit = $tiktokLiveReports->sum(fn($r) => ($r->durasi_jam * 60) + $r->durasi_menit);
        $hours = floor($totalMenit / 60);
        $mins = $totalMenit % 60;
        $totalDurasiFormatted = "{$hours} Jam {$mins} Mnt";
        $totalPenonton = $tiktokLiveReports->sum('jumlah_penonton');
        $totalLikes = $tiktokLiveReports->sum('jumlah_like');
        $totalStu = $tiktokLiveReports->sum('stu');

        $tiktokLiveSummary = [
            'total_sesi' => $totalSesi,
            'total_menit' => $totalMenit,
            'total_durasi_formatted' => $totalDurasiFormatted,
            'total_durasi_jam' => round($totalMenit / 60, 1),
            'total_penonton' => $totalPenonton,
            'total_likes' => $totalLikes,
            'total_stu' => $totalStu,
        ];

        // Branch Leaderboard Ranking
        $branchRankQuery = TiktokLiveReport::query();
        if ($branchId) {
            $branchRankQuery->where('branch_id', $branchId);
        }
        if ($start && $tanggalAkhir) {
            $branchRankQuery->whereBetween('tanggal_live', [$start, $tanggalAkhir]);
        } elseif ($start) {
            $branchRankQuery->where('tanggal_live', $start);
        } elseif ($tanggalAkhir) {
            $branchRankQuery->where('tanggal_live', '<=', $tanggalAkhir);
        } else {
            $branchRankQuery->whereYear('tanggal_live', $tahun)->whereMonth('tanggal_live', $bulan);
        }

        $tiktokLiveBranchRankings = $branchRankQuery
            ->selectRaw('branch_id, COUNT(*) as total_sesi, SUM((durasi_jam * 60) + durasi_menit) as total_menit, SUM(jumlah_penonton) as total_penonton, SUM(jumlah_like) as total_likes, SUM(stu) as total_stu')
            ->groupBy('branch_id')
            ->with('branch')
            ->orderBy('total_menit', 'desc')
            ->orderBy('total_sesi', 'desc')
            ->get()
            ->map(function ($item, $index) {
                $h = floor($item->total_menit / 60);
                $m = $item->total_menit % 60;
                return [
                    'rank' => $index + 1,
                    'branch_name' => $item->branch->nama_cabang ?? 'Cabang',
                    'branch_code' => $item->branch->kode ?? '-',
                    'total_sesi' => (int) $item->total_sesi,
                    'total_durasi_formatted' => "{$h} Jam {$m} Mnt",
                    'total_durasi_jam' => round($item->total_menit / 60, 1),
                    'total_penonton' => (int) ($item->total_penonton ?? 0),
                    'total_likes' => (int) ($item->total_likes ?? 0),
                    'total_stu' => (int) ($item->total_stu ?? 0),
                ];
            });

        // Individual Host Leaderboard Ranking
        $hostRankQuery = TiktokLiveReport::query();
        if ($branchId) {
            $hostRankQuery->where('branch_id', $branchId);
        }
        if ($start && $tanggalAkhir) {
            $hostRankQuery->whereBetween('tanggal_live', [$start, $tanggalAkhir]);
        } elseif ($start) {
            $hostRankQuery->where('tanggal_live', $start);
        } elseif ($tanggalAkhir) {
            $hostRankQuery->where('tanggal_live', '<=', $tanggalAkhir);
        } else {
            $hostRankQuery->whereYear('tanggal_live', $tahun)->whereMonth('tanggal_live', $bulan);
        }

        $tiktokLiveHostRankings = $hostRankQuery
            ->selectRaw('nama_host, jabatan, branch_id, COUNT(*) as total_sesi, COUNT(DISTINCT tanggal_live) as total_hari, SUM((durasi_jam * 60) + durasi_menit) as total_menit, SUM(jumlah_penonton) as total_penonton, SUM(jumlah_like) as total_likes, SUM(stu) as total_stu')
            ->groupBy('nama_host', 'jabatan', 'branch_id')
            ->with('branch')
            ->orderBy('total_menit', 'desc')
            ->orderBy('total_stu', 'desc')
            ->get()
            ->map(function ($item, $index) {
                $h = floor($item->total_menit / 60);
                $m = $item->total_menit % 60;
                $totalSesi = (int) ($item->total_sesi ?? 1);
                $totalHari = (int) ($item->total_hari ?? 1);

                $avgMinPerHari = $totalHari > 0 ? ($item->total_menit / $totalHari) : 0;
                $avgHoursPerHari = round($avgMinPerHari / 60, 2);
                $avgMinPerSesi = $totalSesi > 0 ? ($item->total_menit / $totalSesi) : 0;
                $avgHoursPerSesi = round($avgMinPerSesi / 60, 2);

                return [
                    'rank' => $index + 1,
                    'nama_host' => $item->nama_host,
                    'jabatan' => $item->jabatan,
                    'branch_name' => $item->branch->nama_cabang ?? 'Cabang',
                    'branch_code' => $item->branch->kode ?? '-',
                    'total_sesi' => $totalSesi,
                    'total_hari' => $totalHari,
                    'total_durasi_formatted' => "{$h} Jam {$m} Mnt",
                    'avg_jam_per_hari_formatted' => number_format($avgHoursPerHari, 2) . ' Jam/Hari',
                    'avg_jam_per_sesi_formatted' => number_format($avgHoursPerSesi, 2) . ' Jam/Sesi',
                    'total_penonton' => (int) ($item->total_penonton ?? 0),
                    'total_likes' => (int) ($item->total_likes ?? 0),
                    'total_stu' => (int) ($item->total_stu ?? 0),
                ];
            });

        return [
            'reports' => $tiktokLiveReports,
            'summary' => $tiktokLiveSummary,
            'branch_rankings' => $tiktokLiveBranchRankings,
            'host_rankings' => $tiktokLiveHostRankings,
        ];
    }
}
