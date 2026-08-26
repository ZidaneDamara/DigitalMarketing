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
        $totalPicDigital = (clone $statsQuery)->where('jabatan', 'PIC Digital')->count();
        $totalSalesDigital = (clone $statsQuery)->where('jabatan', 'Sales Digital')->count();

        $branches = Branch::where('status', 'active')->get();
        $userBranch = $user->branch;

        return view('reports.tiktok_live.index', compact(
            'branches', 'userBranch', 'totalSesi', 'totalJamFormat', 'totalPenonton', 'totalLikes', 'totalStu', 'totalPicDigital', 'totalSalesDigital'
        ));
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
                $oldPath = str_replace('/storage/', 'public/', $report->bukti_screenshot);
                Storage::delete($oldPath);
            }

            $file = $request->file('bukti_screenshot');
            $filename = 'tiktok_live_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/tiktok_live_screenshots', $filename);
            $report->bukti_screenshot = Storage::url($path);
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
            $oldPath = str_replace('/storage/', 'public/', $tiktokLive->bukti_screenshot);
            Storage::delete($oldPath);
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
