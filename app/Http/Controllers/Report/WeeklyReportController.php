<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\WeeklyReport;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class WeeklyReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($request->ajax()) {
            $query = WeeklyReport::with(['branch', 'user']);

            if ($user->hasRole('PIC Digital Cabang')) {
                $query->where('branch_id', $user->branch_id);
            } elseif ($request->filled('branch_id')) {
                $query->where('branch_id', $request->branch_id);
            }

            if ($request->filled('tahun')) {
                $query->where('tahun', $request->tahun);
            }

            if ($request->filled('minggu_ke')) {
                $query->where('minggu_ke', $request->minggu_ke);
            }

            return DataTables::of($query->latest('tanggal_post'))
                ->addIndexColumn()
                ->editColumn('tanggal_post', function ($row) {
                    return $row->tanggal_post ? $row->tanggal_post->format('d M Y') : '-';
                })
                ->addColumn('nama_cabang', fn($row) => $row->branch->nama_cabang ?? '-')
                ->addColumn('link_badge', function ($row) {
                    if (!$row->link_content) return '<span class="text-muted">-</span>';
                    return '<a href="' . e($row->link_content) . '" target="_blank" class="btn btn-xs btn-outline-primary rounded-pill"><i class="fas fa-external-link-alt me-1"></i> Buka Link</a>';
                })
                ->addColumn('total_interactions', fn($row) => number_format($row->total_interactions))
                ->addColumn('views_formatted', fn($row) => number_format($row->views))
                ->addColumn('reach_formatted', fn($row) => number_format($row->account_reached))
                ->addColumn('followers_ratio', function ($row) {
                    $f = $row->followers_ratio_pct;
                    $nf = $row->non_followers_ratio_pct;
                    return "<span class='badge bg-success me-1'>F: {$f}%</span><span class='badge bg-warning text-dark'>NF: {$nf}%</span>";
                })
                ->addColumn('action', function ($row) use ($user) {
                    $canEdit = $user->hasRole('Super Admin') || ($user->hasRole('PIC Digital Cabang') && $row->branch_id === $user->branch_id);
                    $html = '<div class="btn-group btn-group-sm">';
                    $html .= '<button class="btn btn-outline-info btn-view" data-id="' . $row->id . '" title="Detail Insight"><i class="fas fa-eye"></i></button>';
                    if ($canEdit) {
                        $html .= '<button class="btn btn-outline-primary btn-edit" data-id="' . $row->id . '" title="Edit"><i class="fas fa-edit"></i></button>';
                        $html .= '<button class="btn btn-outline-danger btn-delete" data-id="' . $row->id . '" title="Hapus"><i class="fas fa-trash"></i></button>';
                    }
                    $html .= '</div>';
                    return $html;
                })
                ->rawColumns(['link_badge', 'followers_ratio', 'action'])
                ->make(true);
        }

        $branches = Branch::where('status', 'active')->get();
        $userBranch = $user->branch;
        
        // Overview Summary Cards Data
        $summaryQuery = WeeklyReport::query();
        if ($user->hasRole('PIC Digital Cabang')) {
            $summaryQuery->where('branch_id', $user->branch_id);
        }

        $totalPosts = (clone $summaryQuery)->count();
        $totalViews = (clone $summaryQuery)->sum('views');
        $totalReach = (clone $summaryQuery)->sum('account_reached');
        $totalInteractions = (clone $summaryQuery)->selectRaw('SUM(interactions_followers + interactions_non_followers) as total')->value('total') ?? 0;

        return view('reports.weekly.index', compact(
            'branches', 'userBranch', 'totalPosts', 'totalViews', 'totalReach', 'totalInteractions'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'id' => 'nullable|exists:weekly_reports,id',
            'branch_id' => 'required|exists:branches,id',
            'tanggal_post' => 'required|date',
            'link_content' => 'required|url|max:500',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'tahun' => 'required|integer|min:2020|max:2099',
            'minggu_ke' => 'required|integer|min:1|max:53',
            'views' => 'required|integer|min:0',
            'account_reached' => 'required|integer|min:0',
            'interactions_followers' => 'required|integer|min:0',
            'interactions_non_followers' => 'required|integer|min:0',
            'likes' => 'required|integer|min:0',
            'shares' => 'required|integer|min:0',
            'saves' => 'required|integer|min:0',
            'comments' => 'required|integer|min:0',
            'reposts' => 'required|integer|min:0',
            'profile_visits' => 'required|integer|min:0',
            'external_link_taps' => 'required|integer|min:0',
            'follows' => 'required|integer|min:0',
            'source_feed_pct' => 'required|numeric|min:0|max:100',
            'source_profile_pct' => 'required|numeric|min:0|max:100',
            'source_stories_pct' => 'required|numeric|min:0|max:100',
            'gender_men_pct' => 'required|numeric|min:0|max:100',
            'gender_women_pct' => 'required|numeric|min:0|max:100',
            'top_country' => 'nullable|string|max:255',
            'top_age' => 'nullable|string|max:255',
            'catatan' => 'nullable|string',
        ]);

        // Security check for PIC Digital Cabang
        if ($user->hasRole('PIC Digital Cabang') && $validated['branch_id'] != $user->branch_id) {
            return response()->json(['message' => 'Anda tidak berhak menginput data cabang lain.'], 403);
        }

        $validated['user_id'] = $user->id;

        $report = WeeklyReport::updateOrCreate(
            ['id' => $request->input('id')],
            $validated
        );

        $action = $request->input('id') ? 'UPDATE' : 'CREATE';
        AuditLogService::log(
            $action,
            'Weekly Report',
            "{$action} Report Mingguan Post Insight (ID: {$report->id}) cabang ID {$report->branch_id}"
        );

        return response()->json([
            'success' => true,
            'message' => 'Laporan mingguan (Post Insight) berhasil disimpan.',
            'data' => $report,
        ]);
    }

    public function show(WeeklyReport $weeklyReport)
    {
        return response()->json($weeklyReport->load(['branch', 'user']));
    }

    public function destroy(WeeklyReport $weeklyReport)
    {
        $user = Auth::user();
        if ($user->hasRole('PIC Digital Cabang') && $weeklyReport->branch_id !== $user->branch_id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $id = $weeklyReport->id;
        $branchId = $weeklyReport->branch_id;
        $weeklyReport->delete();

        AuditLogService::log('DELETE', 'Weekly Report', "Hapus Report Mingguan ID {$id} cabang ID {$branchId}");

        return response()->json([
            'success' => true,
            'message' => 'Laporan mingguan berhasil dihapus.',
        ]);
    }
}
