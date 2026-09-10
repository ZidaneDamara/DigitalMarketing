<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Requests\TiktokLiveReportRequest;
use App\Models\Branch;
use App\Models\TiktokLiveReport;
use App\Services\AuditLogService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class TiktokLiveReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($request->ajax()) {
            if ($request->get('action') === 'get_analytics') {
                return $this->getAnalyticsData($request, $user);
            }

            $query = TiktokLiveReport::with(['branch', 'user']);

            if ($user->hasRole('PIC Digital Cabang')) {
                $query->where('branch_id', $user->branch_id);
            } elseif ($request->filled('branch_id')) {
                $query->where('branch_id', $request->branch_id);
            }

            if ($request->filled('tanggal')) {
                $query->where('tanggal_live', $request->tanggal);
            } elseif ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
                $query->whereBetween('tanggal_live', [$request->tanggal_awal, $request->tanggal_akhir]);
            }

            return DataTables::of($query->latest('tanggal_live'))
                ->addIndexColumn()
                ->editColumn('tanggal_live', function ($row) {
                    return $row->tanggal_live->format('d M Y');
                })
                ->addColumn('nama_cabang', fn($row) => $row->branch->nama_cabang ?? '-')
                ->addColumn('durasi_formatted', fn($row) => $row->formatted_durasi)
                ->addColumn('jumlah_penonton', fn($row) => number_format($row->jumlah_penonton))
                ->addColumn('jumlah_like', fn($row) => number_format($row->jumlah_like))
                ->addColumn('jumlah_komentar', fn($row) => number_format($row->jumlah_komentar))
                ->addColumn('jumlah_share', fn($row) => number_format($row->jumlah_share))
                ->addColumn('stu', fn($row) => $row->stu !== null ? number_format($row->stu) . ' Unit' : '-')
                ->addColumn('bukti_screenshot_url', function ($row) {
                    if ($row->bukti_screenshot_url) {
                        return '<a href="' . $row->bukti_screenshot_url . '" target="_blank" class="badge bg-info text-white text-decoration-none px-2 py-1"><i class="fas fa-external-link-alt me-1"></i> Link SS Live</a>';
                    }
                    return '<span class="text-muted small">-</span>';
                })
                ->addColumn('action', function ($row) use ($user) {
                    $canEdit = $user->hasRole('Super Admin') || $user->hasRole('Area Manager') || ($user->hasRole('PIC Digital Cabang') && $user->branch_id == $row->branch_id);

                    $btnEdit = $canEdit ? '<button class="btn btn-sm btn-outline-primary btn-edit me-1" data-id="' . $row->id . '"><i class="fas fa-edit"></i> Edit</button>' : '';
                    $btnDelete = $user->hasRole('Super Admin') ? '<button class="btn btn-sm btn-outline-danger btn-delete me-1" data-id="' . $row->id . '"><i class="fas fa-trash me-1"></i> Hapus</button>' : '';

                    return '<div class="btn-group">' . $btnEdit . $btnDelete . '</div>';
                })
                ->rawColumns(['bukti_screenshot_url', 'action'])
                ->make(true);
        }

        // Calculate statistics based on access role
        $statsQuery = TiktokLiveReport::query();
        if ($user->hasRole('PIC Digital Cabang')) {
            $statsQuery->where('branch_id', $user->branch_id);
        }

        $totalSesi = (clone $statsQuery)->count();
        $totalMenit = (clone $statsQuery)->selectRaw('SUM((durasi_jam * 60) + durasi_menit) as total_min')->value('total_min') ?? 0;
        $totalJamFormat = floor($totalMenit / 60) . ' Jam ' . ($totalMenit % 60) . ' Mnt';
        $totalPenonton = (clone $statsQuery)->sum('jumlah_penonton') ?? 0;
        $totalLikes = (clone $statsQuery)->sum('jumlah_like') ?? 0;
        $totalStu = (clone $statsQuery)->sum('stu') ?? 0;

        $branches = Branch::where('status', 'active')->get();
        $userBranch = $user->branch;

        return view('reports.tiktok_live.index', compact(
            'branches', 'userBranch', 'totalSesi', 'totalJamFormat', 'totalPenonton', 'totalLikes', 'totalStu'
        ));
    }

    private function getAnalyticsData(Request $request, $user)
    {
        $query = TiktokLiveReport::query();

        if ($user->hasRole('PIC Digital Cabang')) {
            $query->where('branch_id', $user->branch_id);
        } elseif ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('tanggal')) {
            $query->where('tanggal_live', $request->tanggal);
        } elseif ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal_live', [$request->tanggal_awal, $request->tanggal_akhir]);
        }

        // Overall Stats
        $totalSesi = (clone $query)->count();
        $totalMenit = (clone $query)->selectRaw('SUM((durasi_jam * 60) + durasi_menit) as total_min')->value('total_min') ?? 0;
        $hours = floor($totalMenit / 60);
        $mins = $totalMenit % 60;
        $totalJamFormat = "{$hours} Jam {$mins} Mnt";
        $totalPenonton = (clone $query)->sum('jumlah_penonton') ?? 0;
        $totalLikes = (clone $query)->sum('jumlah_like') ?? 0;
        $totalStu = (clone $query)->sum('stu') ?? 0;

        // Line Chart Data (Progress Live per Tanggal)
        $trendQuery = (clone $query)
            ->selectRaw('tanggal_live, COUNT(*) as total_sesi, SUM(jumlah_penonton) as total_penonton, SUM((durasi_jam * 60) + durasi_menit) as total_durasi')
            ->groupBy('tanggal_live')
            ->orderBy('tanggal_live', 'asc')
            ->get();

        $chartLabels = [];
        $chartSesi = [];
        $chartPenonton = [];
        $chartDurasiJam = [];

        foreach ($trendQuery as $item) {
            $chartLabels[] = \Carbon\Carbon::parse($item->tanggal_live)->format('d M Y');
            $chartSesi[] = (int) $item->total_sesi;
            $chartPenonton[] = (int) $item->total_penonton;
            $chartDurasiJam[] = round($item->total_durasi / 60, 1);
        }

        // Host Role Distribution Data
        $roles = ['PIC Digital', 'Sales Digital', 'Sales Reguler', 'Sales Counter'];
        $hostDistribution = [];
        foreach ($roles as $role) {
            $hostDistribution[$role] = (clone $query)->where('jabatan', $role)->count();
        }

        // Branch Leaderboard Ranking
        $leaderboardQuery = TiktokLiveReport::query();
        if ($request->filled('tanggal')) {
            $leaderboardQuery->where('tanggal_live', $request->tanggal);
        } elseif ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $leaderboardQuery->whereBetween('tanggal_live', [$request->tanggal_awal, $request->tanggal_akhir]);
        }
        if ($request->filled('branch_id')) {
            $leaderboardQuery->where('branch_id', $request->branch_id);
        } elseif ($user->hasRole('PIC Digital Cabang')) {
            $leaderboardQuery->where('branch_id', $user->branch_id);
        }

        $branchRankings = $leaderboardQuery
            ->selectRaw('branch_id, COUNT(*) as total_sesi, SUM((durasi_jam * 60) + durasi_menit) as total_menit, SUM(jumlah_penonton) as total_penonton, SUM(stu) as total_stu')
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
                    'total_stu' => (int) ($item->total_stu ?? 0),
                ];
            });

        // Strategic Decision Insights
        $avgDurationMins = $totalSesi > 0 ? round($totalMenit / $totalSesi) : 0;
        $avgDurFormatted = floor($avgDurationMins / 60) > 0 
            ? floor($avgDurationMins / 60) . ' Jam ' . ($avgDurationMins % 60) . ' Mnt'
            : $avgDurationMins . ' Mnt';

        arsort($hostDistribution);
        $topHostRole = key($hostDistribution) ?? '-';
        $topHostCount = current($hostDistribution) ?? 0;
        $topHostPct = $totalSesi > 0 ? round(($topHostCount / $totalSesi) * 100) : 0;

        $stuPerThousandViewers = $totalPenonton > 0 ? round(($totalStu / $totalPenonton) * 1000, 2) : 0;

        return response()->json([
            'success' => true,
            'stats' => [
                'total_sesi' => number_format($totalSesi),
                'total_durasi' => $totalJamFormat,
                'total_penonton' => number_format($totalPenonton),
                'total_likes' => number_format($totalLikes),
                'total_stu' => number_format($totalStu) . ' Unit',
            ],
            'chart' => [
                'labels' => $chartLabels,
                'sesi' => $chartSesi,
                'penonton' => $chartPenonton,
                'durasi_jam' => $chartDurasiJam,
            ],
            'host_distribution' => $hostDistribution,
            'rankings' => $branchRankings,
            'insights' => [
                'avg_duration' => $avgDurFormatted,
                'top_host_role' => $topHostRole,
                'top_host_pct' => $topHostPct . '%',
                'stu_per_thousand' => $stuPerThousandViewers,
            ],
        ]);
    }

    public function store(TiktokLiveReportRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        if ($request->filled('id')) {
            $report = TiktokLiveReport::findOrFail($request->id);
            $actionName = 'UPDATE';
            $logMessage = "Mengubah data Laporan TikTok Live ID #{$report->id}";
        } else {
            $report = new TiktokLiveReport();
            $actionName = 'CREATE';
            $logMessage = "Menambahkan Laporan TikTok Live host {$data['nama_host']}";
        }

        $report->branch_id = $data['branch_id'];
        $report->user_id = $user->id;
        $report->nama_host = $data['nama_host'];
        $report->jabatan = $data['jabatan'];
        $report->tanggal_live = $data['tanggal_live'];
        $report->durasi_jam = $data['durasi_jam'];
        $report->durasi_menit = $data['durasi_menit'];
        $report->jumlah_penonton = $data['jumlah_penonton'] ?? 0;
        $report->jumlah_like = $data['jumlah_like'] ?? 0;
        $report->jumlah_komentar = $data['jumlah_komentar'] ?? 0;
        $report->jumlah_share = $data['jumlah_share'] ?? 0;
        $report->stu = array_key_exists('stu', $data) ? $data['stu'] : null;
        $report->catatan = $data['catatan'] ?? null;

        if ($request->hasFile('bukti_screenshot')) {
            // Delete old file if exists
            if ($report->bukti_screenshot) {
                $clean = str_replace(['/storage/', 'storage/', '/files/', 'files/'], '', $report->bukti_screenshot);
                $withoutPublic = preg_replace('#^public/#', '', ltrim($clean, '/'));
                Storage::disk('public')->delete($withoutPublic);
                Storage::disk('public')->delete('public/' . $withoutPublic);
                Storage::delete($withoutPublic);
                Storage::delete('public/' . $withoutPublic);
            }

            $file = $request->file('bukti_screenshot');
            $filename = 'tiktok_live_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('tiktok_live_screenshots', $filename, 'public');
            $report->bukti_screenshot = $path;
        }

        $report->save();

        AuditLogService::log(
            $actionName,
            'TikTok Live Report',
            $logMessage
        );

        return response()->json([
            'success' => true,
            'message' => 'Laporan Live TikTok berhasil disimpan.',
            'data' => $report->load(['branch', 'user']),
        ]);
    }

    public function show(TiktokLiveReport $tiktokLive)
    {
        return response()->json($tiktokLive->load(['branch', 'user']));
    }

    public function destroy(TiktokLiveReport $tiktokLive)
    {
        if ($tiktokLive->bukti_screenshot) {
            $clean = str_replace(['/storage/', 'storage/', '/files/', 'files/'], '', $tiktokLive->bukti_screenshot);
            $withoutPublic = preg_replace('#^public/#', '', ltrim($clean, '/'));
            Storage::disk('public')->delete($withoutPublic);
            Storage::disk('public')->delete('public/' . $withoutPublic);
            Storage::delete($withoutPublic);
            Storage::delete('public/' . $withoutPublic);
        }

        $id = $tiktokLive->id;
        $host = $tiktokLive->nama_host;
        $tiktokLive->delete();

        AuditLogService::log(
            'DELETE',
            'TikTok Live Report',
            "Menghapus Laporan TikTok Live ID #{$id} (Host: {$host})"
        );

        return response()->json([
            'success' => true,
            'message' => 'Laporan Live TikTok berhasil dihapus.',
        ]);
    }

    public function exportSinglePdf(TiktokLiveReport $tiktokLive)
    {
        $report = $tiktokLive->load(['branch', 'user']);
        $pdf = Pdf::loadView('reports.tiktok_live.pdf', compact('report'))->setPaper('a4', 'portrait');

        return $pdf->download("Laporan_Live_TikTok_{$report->branch->kode}_{$report->tanggal_live->format('Y-m-d')}.pdf");
    }

    public function exportSingleJpg(TiktokLiveReport $tiktokLive)
    {
        $report = $tiktokLive->load(['branch', 'user']);
        return view('reports.tiktok_live.export_jpg', compact('report'));
    }
}
