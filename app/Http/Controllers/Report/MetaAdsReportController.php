<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\MetaAdsClosingProof;
use App\Models\MetaAdsProspect;
use App\Models\MetaAdsReport;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class MetaAdsReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($request->ajax()) {
            $query = MetaAdsReport::with(['branch', 'user', 'closingProofs']);

            if ($user->hasRole('PIC Digital Cabang')) {
                $query->where('branch_id', $user->branch_id);
            } elseif ($request->filled('branch_id')) {
                $query->where('branch_id', $request->branch_id);
            }

            if ($request->filled('bulan')) {
                $query->where('bulan', $request->bulan);
            }

            if ($request->filled('tahun')) {
                $query->where('tahun', $request->tahun);
            }

            if ($request->filled('product')) {
                $query->where('product', 'like', '%' . $request->product . '%');
            }

            return DataTables::of($query->latest())
                ->addIndexColumn()
                ->addColumn('cabang', fn($row) => $row->branch->nama_cabang ?? '-')
                ->addColumn('periode', fn($row) => $row->nama_bulan . ' ' . $row->tahun)
                ->editColumn('nama_iklan', fn($row) => '<strong>' . e($row->nama_iklan) . '</strong><br><small class="text-muted">' . e($row->product) . '</small>')
                ->editColumn('budget', fn($row) => '<span class="fw-bold text-primary">' . $row->formatted_budget . '</span>')
                ->editColumn('total_views', fn($row) => number_format($row->total_views))
                ->editColumn('total_percakapan', fn($row) => '<span class="badge bg-success-subtle text-success border border-success fw-bold px-2 py-1">' . number_format($row->total_percakapan) . ' Chat</span>')
                ->editColumn('ctr', fn($row) => number_format($row->ctr, 2) . '%')
                ->editColumn('cpc', fn($row) => $row->formatted_cpc)
                ->editColumn('cpm', fn($row) => $row->formatted_cpm)
                ->editColumn('cpl', fn($row) => '<span class="badge bg-info-subtle text-info border border-info fw-bold px-2 py-1">' . $row->formatted_cpl . '</span>')
                ->editColumn('stu', function ($row) {
                    $unitInfo = '';
                    if ($row->unit_terjual) {
                        $units = array_filter(array_map('trim', explode(',', $row->unit_terjual)));
                        $unitInfo = '<div class="d-flex flex-wrap gap-1 mt-1 justify-content-center">';
                        foreach ($units as $u) {
                            $unitInfo .= '<span class="badge bg-light text-dark border shadow-xs" style="font-size: 0.68rem;"><i class="fas fa-motorcycle text-danger me-1"></i>' . e($u) . '</span>';
                        }
                        $unitInfo .= '</div>';
                    }
                    return '<span class="badge bg-danger text-white fw-bold px-2.5 py-1.5 fs-7"><i class="fas fa-motorcycle me-1"></i>' . number_format($row->stu) . ' Unit</span>' . $unitInfo;
                })
                ->addColumn('bukti_topup_btn', function ($row) {
                    if ($row->bukti_topup_url) {
                        if ($row->is_bukti_topup_pdf) {
                            return '<a href="' . $row->bukti_topup_url . '" target="_blank" class="btn btn-sm btn-outline-danger shadow-sm">
                                <i class="fas fa-file-pdf me-1"></i> Invoice PDF
                            </a>';
                        }
                        return '<button type="button" class="btn btn-sm btn-outline-primary btn-preview-image" data-url="' . $row->bukti_topup_url . '" data-title="Bukti Top Up: ' . e($row->nama_iklan) . '">
                            <i class="fas fa-receipt me-1"></i> Lihat Bukti
                        </button>';
                    }
                    return '<span class="text-muted small">-</span>';
                })
                ->addColumn('bukti_closing_btn', function ($row) {
                    $count = $row->closingProofs->count();
                    if ($count > 0) {
                        return '<button type="button" class="btn btn-sm btn-outline-success btn-view-closing-proofs" data-id="' . $row->id . '">
                            <i class="fas fa-images me-1"></i> ' . $count . ' Foto Closing
                        </button>';
                    }
                    return '<span class="text-muted small">0 Foto</span>';
                })
                ->addColumn('action', function ($row) use ($user) {
                    $canEdit = $user->hasRole('Super Admin') || $user->hasRole('Area Manager') || ($user->hasRole('PIC Digital Cabang') && $user->branch_id == $row->branch_id);

                    $btnDetail = '<button type="button" class="btn btn-sm btn-info text-white btn-detail me-1 shadow-sm" data-id="' . $row->id . '" title="Detail Laporan"><i class="fas fa-eye"></i></button>';
                    $btnEdit = $canEdit ? '<button type="button" class="btn btn-sm btn-warning text-dark btn-edit me-1 shadow-sm" data-id="' . $row->id . '" title="Edit Laporan"><i class="fas fa-edit"></i></button>' : '';
                    $btnDelete = $user->hasRole('Super Admin') ? '<button type="button" class="btn btn-sm btn-danger btn-delete shadow-sm" data-id="' . $row->id . '" title="Hapus"><i class="fas fa-trash"></i></button>' : '';

                    return '<div class="btn-group">' . $btnDetail . $btnEdit . $btnDelete . '</div>';
                })
                ->rawColumns(['nama_iklan', 'budget', 'total_percakapan', 'cpl', 'stu', 'bukti_topup_btn', 'bukti_closing_btn', 'action'])
                ->make(true);
        }

        // Summary stats query
        $statsQuery = MetaAdsReport::query();
        if ($user->hasRole('PIC Digital Cabang')) {
            $statsQuery->where('branch_id', $user->branch_id);
        } elseif ($request->filled('branch_id')) {
            $statsQuery->where('branch_id', $request->branch_id);
        }

        if ($request->filled('bulan')) {
            $statsQuery->where('bulan', $request->bulan);
        } else {
            $statsQuery->where('bulan', now()->month);
        }

        if ($request->filled('tahun')) {
            $statsQuery->where('tahun', $request->tahun);
        } else {
            $statsQuery->where('tahun', now()->year);
        }

        $totalBudget = (clone $statsQuery)->sum('budget') ?? 0;
        $totalViews = (clone $statsQuery)->sum('total_views') ?? 0;
        $totalPercakapan = (clone $statsQuery)->sum('total_percakapan') ?? 0;
        $totalStu = (clone $statsQuery)->sum('stu') ?? 0;
        $avgCtr = (clone $statsQuery)->avg('ctr') ?? 0;
        $avgCpc = (clone $statsQuery)->avg('cpc') ?? 0;
        $avgCpm = (clone $statsQuery)->avg('cpm') ?? 0;
        $avgCpl = (clone $statsQuery)->avg('cpl') ?? 0;

        $branches = Branch::where('status', 'active')->get();
        $userBranch = $user->branch;
        $activeBulan = $request->input('bulan', now()->month);
        $activeTahun = $request->input('tahun', now()->year);

        // Prospects summary query
        $prospectQuery = MetaAdsProspect::query();
        if ($user->hasRole('PIC Digital Cabang')) {
            $prospectQuery->where('branch_id', $user->branch_id);
        } elseif ($request->filled('branch_id')) {
            $prospectQuery->where('branch_id', $request->branch_id);
        }
        if ($request->filled('bulan')) {
            $prospectQuery->whereMonth('tanggal_input', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $prospectQuery->whereYear('tanggal_input', $request->tahun);
        }

        $totalProspects = (clone $prospectQuery)->count();
        $igProspects = (clone $prospectQuery)->where('sumber_leads', 'Instagram')->count();
        $fbProspects = (clone $prospectQuery)->where('sumber_leads', 'Facebook')->count();
        $waProspects = (clone $prospectQuery)->where('sumber_leads', 'WhatsApp')->count();

        // Active Meta Ads campaigns for attribution dropdown in prospect form
        $metaCampaigns = (clone $statsQuery)->latest()->get(['id', 'nama_iklan', 'product']);

        return view('reports.meta_ads.index', compact(
            'branches',
            'userBranch',
            'totalBudget',
            'totalViews',
            'totalPercakapan',
            'totalStu',
            'avgCtr',
            'avgCpc',
            'avgCpm',
            'avgCpl',
            'activeBulan',
            'activeTahun',
            'totalProspects',
            'igProspects',
            'fbProspects',
            'waProspects',
            'metaCampaigns'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'id' => 'nullable|exists:meta_ads_reports,id',
            'branch_id' => 'required|exists:branches,id',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2020|max:2035',
            'nama_iklan' => 'required|string|max:255',
            'product' => 'required|string|max:255',
            'ctr' => 'required|numeric|min:0',
            'cpc' => 'required|numeric|min:0',
            'cpm' => 'required|numeric|min:0',
            'cpl' => 'required|numeric|min:0',
            'total_views' => 'required|integer|min:0',
            'total_percakapan' => 'required|integer|min:0',
            'budget' => 'required|numeric|min:0',
            'stu' => 'required|integer|min:0',
            'unit_terjual' => 'nullable',
            'catatan' => 'nullable|string',
            'bukti_topup' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:20480', // Support PDF & Image
            'bukti_closing_stu.*' => 'nullable|image|max:10240',
        ];

        // Authorization check for branch
        if ($user->hasRole('PIC Digital Cabang')) {
            $request->merge(['branch_id' => $user->branch_id]);
        }

        $validated = $request->validate($rules);

        if ($request->filled('id')) {
            $report = MetaAdsReport::findOrFail($request->id);
            if ($user->hasRole('PIC Digital Cabang') && $report->branch_id != $user->branch_id) {
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki hak untuk mengedit laporan cabang lain.'], 403);
            }
            $action = 'UPDATE';
            $logMsg = "Mengupdate laporan Meta Ads #{$report->id} ({$validated['nama_iklan']})";
        } else {
            $report = new MetaAdsReport();
            $action = 'CREATE';
            $logMsg = "Menambahkan laporan Meta Ads baru: {$validated['nama_iklan']}";
        }

        $report->branch_id = $validated['branch_id'];
        $report->user_id = $user->id;
        $report->bulan = $validated['bulan'];
        $report->tahun = $validated['tahun'];
        $report->tanggal_laporan = now()->toDateString();
        $report->nama_iklan = $validated['nama_iklan'];
        $report->product = $validated['product'];
        $report->ctr = $validated['ctr'];
        $report->cpc = $validated['cpc'];
        $report->cpm = $validated['cpm'];
        $report->cpl = $validated['cpl'];
        $report->total_views = $validated['total_views'];
        $report->total_percakapan = $validated['total_percakapan'];
        $report->budget = $validated['budget'];
        $report->stu = $validated['stu'];

        if ($request->has('unit_terjual')) {
            $inputUnits = $request->input('unit_terjual');
            if (is_array($inputUnits)) {
                $cleaned = array_values(array_filter(array_map('trim', $inputUnits), fn($val) => !empty($val)));
                $report->unit_terjual = !empty($cleaned) ? implode(', ', $cleaned) : null;
            } else {
                $report->unit_terjual = !empty(trim($inputUnits)) ? trim($inputUnits) : null;
            }
        } else {
            $report->unit_terjual = null;
        }

        $report->catatan = $validated['catatan'] ?? null;

        // Bukti Topup Upload
        if ($request->hasFile('bukti_topup')) {
            // Delete old file if exists
            if ($report->bukti_topup) {
                $clean = str_replace(['/storage/', 'storage/', '/files/', 'files/'], '', $report->bukti_topup);
                $withoutPublic = preg_replace('#^public/#', '', ltrim($clean, '/'));
                Storage::disk('public')->delete($withoutPublic);
                Storage::delete($withoutPublic);
            }

            $file = $request->file('bukti_topup');
            $filename = 'topup_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('screenshots/meta_ads/topup', $filename, 'public');
            $report->bukti_topup = $path;
        }

        $report->save();

        // Bukti Closing STU Multiple Uploads
        if ($request->hasFile('bukti_closing_stu')) {
            $files = $request->file('bukti_closing_stu');
            foreach ($files as $idx => $closingFile) {
                $cFilename = 'closing_' . time() . '_' . uniqid() . '_' . ($idx + 1) . '.' . $closingFile->getClientOriginalExtension();
                $cPath = $closingFile->storeAs('screenshots/meta_ads/closing', $cFilename, 'public');

                MetaAdsClosingProof::create([
                    'meta_ads_report_id' => $report->id,
                    'file_path' => $cPath,
                    'file_name' => $closingFile->getClientOriginalName(),
                    'keterangan' => 'Bukti Closing STU #' . ($idx + 1),
                ]);
            }
        }

        AuditLogService::log(
            $action,
            'Meta Ads Monthly Report',
            $logMsg
        );

        return response()->json([
            'success' => true,
            'message' => 'Laporan Meta Ads berhasil disimpan dengan sukses!',
            'data' => $report->load(['branch', 'user', 'closingProofs']),
        ]);
    }

    public function show(MetaAdsReport $metaAdsReport)
    {
        return response()->json($metaAdsReport->load(['branch', 'user', 'closingProofs']));
    }

    public function destroy(MetaAdsReport $metaAdsReport)
    {
        $user = Auth::user();
        if (!$user->hasRole('Super Admin')) {
            return response()->json(['success' => false, 'message' => 'Hanya Super Admin yang berhak menghapus laporan Meta Ads.'], 403);
        }

        // Delete topup file
        if ($metaAdsReport->bukti_topup) {
            $clean = str_replace(['/storage/', 'storage/', '/files/', 'files/'], '', $metaAdsReport->bukti_topup);
            $withoutPublic = preg_replace('#^public/#', '', ltrim($clean, '/'));
            Storage::disk('public')->delete($withoutPublic);
            Storage::delete($withoutPublic);
        }

        // Delete closing proofs
        foreach ($metaAdsReport->closingProofs as $proof) {
            $clean = str_replace(['/storage/', 'storage/', '/files/', 'files/'], '', $proof->file_path);
            $withoutPublic = preg_replace('#^public/#', '', ltrim($clean, '/'));
            Storage::disk('public')->delete($withoutPublic);
            Storage::delete($withoutPublic);
            $proof->delete();
        }

        $reportTitle = $metaAdsReport->nama_iklan;
        $metaAdsReport->delete();

        AuditLogService::log(
            'DELETE',
            'Meta Ads Monthly Report',
            "Menghapus laporan Meta Ads: {$reportTitle}"
        );

        return response()->json([
            'success' => true,
            'message' => 'Laporan Meta Ads dan seluruh bukti berhasil dihapus.',
        ]);
    }

    public function destroyClosingProof(MetaAdsClosingProof $proof)
    {
        $user = Auth::user();
        $report = $proof->metaAdsReport;

        if ($user->hasRole('PIC Digital Cabang') && $report->branch_id != $user->branch_id) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        if ($proof->file_path) {
            $clean = str_replace(['/storage/', 'storage/', '/files/', 'files/'], '', $proof->file_path);
            $withoutPublic = preg_replace('#^public/#', '', ltrim($clean, '/'));
            Storage::disk('public')->delete($withoutPublic);
            Storage::delete($withoutPublic);
        }

        $proof->delete();

        return response()->json([
            'success' => true,
            'message' => 'Foto bukti closing STU berhasil dihapus.',
        ]);
    }

    /**
     * AJAX DataTables for Meta Ads Prospects (Leads)
     */
    public function getProspects(Request $request)
    {
        $user = Auth::user();
        $query = MetaAdsProspect::with(['branch', 'user', 'metaAdsReport']);

        if ($user->hasRole('PIC Digital Cabang')) {
            $query->where('branch_id', $user->branch_id);
        } elseif ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('sumber_leads')) {
            $query->where('sumber_leads', $request->sumber_leads);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_input', $request->tahun);
        }

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_input', $request->bulan);
        }

        return DataTables::of($query->latest('tanggal_input'))
            ->addIndexColumn()
            ->addColumn('cabang', fn($row) => $row->branch->nama_cabang ?? '-')
            ->editColumn('tanggal_input', fn($row) => $row->formatted_tanggal)
            ->editColumn('nama', fn($row) => '<strong>' . e($row->nama) . '</strong>' . ($row->metaAdsReport ? '<br><small class="text-muted"><i class="fas fa-bullhorn me-1"></i>' . e($row->metaAdsReport->nama_iklan) . '</small>' : ''))
            ->editColumn('nomor_hp', function ($row) {
                return '<div class="d-flex align-items-center gap-1">
                    <span class="font-monospace fw-semibold">' . e($row->nomor_hp) . '</span>
                    <a href="' . $row->wa_link . '" target="_blank" class="btn btn-sm btn-success px-2 py-0.5 rounded-pill ms-1 text-white text-decoration-none shadow-sm" title="Chat WhatsApp Sekarang">
                        <i class="fab fa-whatsapp me-1"></i> Chat
                    </a>
                </div>';
            })
            ->editColumn('sumber_leads', fn($row) => $row->sumber_badge)
            ->editColumn('status', fn($row) => $row->status_badge)
            ->editColumn('catatan', fn($row) => $row->catatan ? '<small class="text-muted">' . e($row->catatan) . '</small>' : '<span class="text-muted small fst-italic">-</span>')
            ->addColumn('action', function ($row) use ($user) {
                $canEdit = $user->hasRole('Super Admin') || $user->hasRole('Area Manager') || ($user->hasRole('PIC Digital Cabang') && $user->branch_id == $row->branch_id);
                $canDelete = $user->hasRole('Super Admin') || ($user->hasRole('PIC Digital Cabang') && $user->branch_id == $row->branch_id);

                $btnEdit = $canEdit ? '<button type="button" class="btn btn-sm btn-outline-warning btn-edit-prospect me-1 shadow-xs" data-id="' . $row->id . '" title="Edit Data Prospek"><i class="fas fa-edit"></i></button>' : '';
                $btnDelete = $canDelete ? '<button type="button" class="btn btn-sm btn-outline-danger btn-delete-prospect shadow-xs" data-id="' . $row->id . '" title="Hapus Data Prospek"><i class="fas fa-trash"></i></button>' : '';

                return '<div class="btn-group">' . $btnEdit . $btnDelete . '</div>';
            })
            ->rawColumns(['nama', 'nomor_hp', 'sumber_leads', 'status', 'catatan', 'action'])
            ->make(true);
    }

    /**
     * Show single Prospect Lead JSON for Editing
     */
    public function showProspect(MetaAdsProspect $prospect)
    {
        return response()->json($prospect->load(['branch', 'user', 'metaAdsReport']));
    }

    /**
     * Store or Update Prospect Lead
     */
    public function storeProspect(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'id' => 'nullable|exists:meta_ads_prospects,id',
            'branch_id' => 'required|exists:branches,id',
            'nama' => 'required|string|max:255',
            'nomor_hp' => 'required|string|max:50',
            'sumber_leads' => 'required|in:Instagram,Facebook,WhatsApp',
            'tanggal_input' => 'nullable|date',
            'meta_ads_report_id' => 'nullable|exists:meta_ads_reports,id',
            'status' => 'nullable|string|max:50',
            'catatan' => 'nullable|string',
        ];

        if ($user->hasRole('PIC Digital Cabang')) {
            $request->merge(['branch_id' => $user->branch_id]);
        }

        $validated = $request->validate($rules);

        if ($request->filled('id')) {
            $prospect = MetaAdsProspect::findOrFail($request->id);
            if ($user->hasRole('PIC Digital Cabang') && $prospect->branch_id != $user->branch_id) {
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki hak untuk mengedit data cabang lain.'], 403);
            }
            $action = 'UPDATE';
            $logMsg = "Mengupdate data prospek Meta Ads: {$validated['nama']} ({$validated['nomor_hp']})";
            $prospect->update([
                'branch_id' => $validated['branch_id'],
                'meta_ads_report_id' => $validated['meta_ads_report_id'] ?? null,
                'nama' => $validated['nama'],
                'nomor_hp' => $validated['nomor_hp'],
                'sumber_leads' => $validated['sumber_leads'],
                'tanggal_input' => $validated['tanggal_input'] ?? $prospect->tanggal_input,
                'status' => $validated['status'] ?? $prospect->status,
                'catatan' => $validated['catatan'] ?? null,
            ]);
            $msg = 'Data prospek berhasil diperbarui!';
        } else {
            $prospect = MetaAdsProspect::create([
                'branch_id' => $validated['branch_id'],
                'user_id' => $user->id,
                'meta_ads_report_id' => $validated['meta_ads_report_id'] ?? null,
                'nama' => $validated['nama'],
                'nomor_hp' => $validated['nomor_hp'],
                'sumber_leads' => $validated['sumber_leads'],
                'tanggal_input' => $validated['tanggal_input'] ?? now()->toDateString(),
                'status' => $validated['status'] ?? 'Prospek Baru',
                'catatan' => $validated['catatan'] ?? null,
            ]);
            $action = 'CREATE';
            $logMsg = "Menambahkan data prospek baru: {$prospect->nama} ({$prospect->nomor_hp}) dari sumber {$prospect->sumber_leads}";
            $msg = 'Data prospek berhasil ditambahkan!';
        }

        AuditLogService::log(
            $action,
            'Meta Ads Prospect',
            $logMsg
        );

        return response()->json([
            'success' => true,
            'message' => $msg,
            'data' => $prospect->load(['branch', 'user', 'metaAdsReport']),
        ]);
    }

    /**
     * Delete Prospect Lead
     */
    public function destroyProspect(MetaAdsProspect $prospect)
    {
        $user = Auth::user();

        if ($user->hasRole('PIC Digital Cabang') && $prospect->branch_id != $user->branch_id) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki hak untuk menghapus data ini.'], 403);
        }

        $nama = $prospect->nama;
        $prospect->delete();

        AuditLogService::log(
            'DELETE',
            'Meta Ads Prospect',
            "Menghapus prospek Meta Ads: {$nama}"
        );

        return response()->json([
            'success' => true,
            'message' => 'Data prospek berhasil dihapus.',
        ]);
    }
}
