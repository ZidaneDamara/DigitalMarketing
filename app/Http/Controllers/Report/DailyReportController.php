<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Requests\DailyReportRequest;
use App\Models\Branch;
use App\Models\DailyReport;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class DailyReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $today = now()->format('Y-m-d');

        if ($request->ajax()) {
            $query = DailyReport::with(['branch', 'user']);

            if ($user->hasRole('PIC Digital Cabang')) {
                $query->where('branch_id', $user->branch_id);
            } elseif ($request->filled('branch_id')) {
                $query->where('branch_id', $request->branch_id);
            }

            if ($request->filled('tanggal')) {
                $query->where('tanggal', $request->tanggal);
            }

            return DataTables::of($query->latest('tanggal'))
                ->addIndexColumn()
                ->editColumn('tanggal', function ($row) {
                    return $row->tanggal->format('d M Y');
                })
                ->addColumn('nama_cabang', fn($row) => $row->branch->nama_cabang)
                ->addColumn('total_post', function ($row) {
                    return $row->ig_feed + $row->ig_reels + $row->ig_story + $row->fb_post + $row->fb_marketplace + $row->tiktok_post;
                })
                ->addColumn('followers_gained', function ($row) {
                    return $row->ig_followers_gained + $row->fb_followers_gained + $row->tiktok_followers_gained;
                })
                ->addColumn('action', function ($row) use ($today, $user) {
                    $isToday = $row->tanggal->format('Y-m-d') === $today;
                    $canEdit = $user->hasRole('Super Admin') || ($user->hasRole('PIC Digital Cabang') && $isToday);

                    if ($canEdit) {
                        return '<button class="btn btn-sm btn-outline-primary btn-edit" data-id="' . $row->id . '"><i class="fas fa-edit"></i> Edit</button>';
                    }
                    return '<span class="badge bg-secondary"><i class="fas fa-lock me-1"></i> Terkunci</span>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $branches = Branch::where('status', 'active')->get();
        $userBranch = $user->branch;
        
        // Existing report for today for current user's branch
        $todayReport = null;
        if ($user->branch_id) {
            $todayReport = DailyReport::where('branch_id', $user->branch_id)
                ->where('tanggal', $today)
                ->first();
        }

        return view('reports.daily.index', compact('branches', 'todayReport', 'today', 'userBranch'));
    }

    public function store(DailyReportRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();
        $data['user_id'] = $user->id;

        $report = DailyReport::updateOrCreate(
            ['branch_id' => $data['branch_id'], 'tanggal' => $data['tanggal']],
            $data
        );

        AuditLogService::log(
            'CREATE',
            'Daily Report',
            "Input Daily Report tanggal {$data['tanggal']} untuk cabang ID {$data['branch_id']}"
        );

        return response()->json([
            'success' => true,
            'message' => 'Laporan harian berhasil disimpan.',
            'data' => $report,
        ]);
    }

    public function show(DailyReport $dailyReport)
    {
        return response()->json($dailyReport->load(['branch', 'user']));
    }
}
