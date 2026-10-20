<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\GoogleRatingMonth;
use App\Models\GoogleRatingTestimonial;
use App\Services\AuditLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class GoogleRatingReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $tahun = (int) $request->input('tahun', now()->year);
        $bulan = (int) $request->input('bulan', now()->month);

        if ($user->hasRole('PIC Digital Cabang')) {
            $branchId = $user->branch_id;
        } else {
            $branchId = $request->input('branch_id');
            if (!$branchId) {
                $firstBranch = Branch::where('status', 'active')->first();
                $branchId = $firstBranch ? $firstBranch->id : null;
            }
        }

        // Handle AJAX DataTables for testimonials list
        if ($request->ajax() && $request->get('action') === 'get_testimonials') {
            $query = GoogleRatingTestimonial::with(['branch', 'user'])
                ->where('tahun', $tahun)
                ->where('bulan', $bulan);

            if ($branchId) {
                $query->where('branch_id', $branchId);
            }

            if ($request->filled('tanggal')) {
                $query->where('tanggal', $request->tanggal);
            }

            return DataTables::of($query->latest('tanggal'))
                ->addIndexColumn()
                ->addColumn('cabang', fn($row) => $row->branch->nama_cabang ?? '-')
                ->editColumn('tanggal', fn($row) => $row->formatted_tanggal)
                ->editColumn('nama_reviewer', fn($row) => '<strong>' . e($row->nama_reviewer) . '</strong>')
                ->editColumn('rating_bintang', function ($row) {
                    $stars = '';
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $row->rating_bintang) {
                            $stars .= '<i class="fas fa-star text-warning"></i>';
                        } else {
                            $stars .= '<i class="far fa-star text-muted"></i>';
                        }
                    }
                    return '<div class="d-inline-flex gap-1">' . $stars . '</div> <span class="badge bg-warning text-dark ms-1">' . $row->rating_bintang . '.0</span>';
                })
                ->editColumn('komentar_ulasan', function ($row) {
                    if ($row->komentar_ulasan) {
                        return '<span class="text-truncate d-inline-block" style="max-width: 250px;" title="' . e($row->komentar_ulasan) . '">"' . e($row->komentar_ulasan) . '"</span>';
                    }
                    return '<span class="text-muted fst-italic">Tanpa komentar</span>';
                })
                ->addColumn('foto_btn', function ($row) {
                    if ($row->foto_testimoni_url) {
                        return '<button type="button" class="btn btn-sm btn-outline-primary btn-preview-image" data-url="' . $row->foto_testimoni_url . '" data-title="Testimoni: ' . e($row->nama_reviewer) . ' (' . $row->formatted_tanggal . ')">
                            <i class="fas fa-image me-1"></i> Lihat Foto
                        </button>';
                    }
                    return '<span class="text-muted small">-</span>';
                })
                ->addColumn('action', function ($row) use ($user) {
                    $canDelete = $user->hasRole('Super Admin') || ($user->hasRole('PIC Digital Cabang') && $user->branch_id == $row->branch_id);
                    if ($canDelete) {
                        return '<button type="button" class="btn btn-sm btn-outline-danger btn-delete-testimonial" data-id="' . $row->id . '" title="Hapus Testimoni"><i class="fas fa-trash"></i></button>';
                    }
                    return '-';
                })
                ->rawColumns(['nama_reviewer', 'rating_bintang', 'komentar_ulasan', 'foto_btn', 'action'])
                ->make(true);
        }

        // Today's Testimonial Count & Warning calculation
        $todayDate = now()->toDateString();
        $todayCountQuery = GoogleRatingTestimonial::whereDate('tanggal', $todayDate);
        if ($branchId) {
            $todayCountQuery->where('branch_id', $branchId);
        }
        $todayCount = $todayCountQuery->count();
        $isTodayTargetMet = ($todayCount >= 5);
        $todayDeficiency = max(0, 5 - $todayCount);

        // Warning message
        if ($todayCount < 5) {
            $warningMessage = "⚠️ PERINGATAN: Input testimoni hari ini baru {$todayCount} dari minimal 5 Google Rating per hari! Masih kurang {$todayDeficiency} testimoni lagi untuk mencapai target harian.";
        } else {
            $warningMessage = "🎉 HEBAT! Target harian minimal 5 testimoni telah terpenuhi hari ini ({$todayCount} testimoni diinput).";
        }

        // Month Testimonials Count & Target calculation
        $monthQuery = GoogleRatingTestimonial::where('tahun', $tahun)->where('bulan', $bulan);
        if ($branchId) {
            $monthQuery->where('branch_id', $branchId);
        }
        $monthCount = $monthQuery->count();
        $targetBulanan = 125;
        $monthProgressPct = min(100, round(($monthCount / $targetBulanan) * 100, 1));
        $monthDeficiency = max(0, $targetBulanan - $monthCount);

        // Baseline (Google Rating Awal Bulan)
        $baseline = null;
        if ($branchId) {
            $baseline = GoogleRatingMonth::where('branch_id', $branchId)
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->first();
        }

        // Daily Breakdown for the current month (1..daysInMonth)
        $daysInMonth = Carbon::create($tahun, $bulan)->daysInMonth;
        $dailyCounts = [];
        $testimonialsInMonth = GoogleRatingTestimonial::where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->get(['tanggal']);

        $testimonialsByDay = [];
        foreach ($testimonialsInMonth as $t) {
            if ($t->tanggal) {
                $dNum = (int) $t->tanggal->format('j');
                $testimonialsByDay[$dNum] = ($testimonialsByDay[$dNum] ?? 0) + 1;
            }
        }

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dailyCounts[$d] = $testimonialsByDay[$d] ?? 0;
        }

        // Recent testimonials for visual gallery
        $recentTestimonials = GoogleRatingTestimonial::with('branch')
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->latest('tanggal')
            ->take(12)
            ->get();

        $branches = Branch::where('status', 'active')->get();
        $selectedBranch = Branch::find($branchId);

        return view('reports.google_rating.index', compact(
            'branches',
            'branchId',
            'selectedBranch',
            'tahun',
            'bulan',
            'baseline',
            'todayCount',
            'isTodayTargetMet',
            'todayDeficiency',
            'warningMessage',
            'monthCount',
            'targetBulanan',
            'monthProgressPct',
            'monthDeficiency',
            'daysInMonth',
            'dailyCounts',
            'recentTestimonials'
        ));
    }

    public function storeBaseline(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'branch_id' => 'required|exists:branches,id',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2020|max:2035',
            'rating_awal_bulan' => 'required|numeric|between:1.00,5.00',
            'total_review_awal_bulan' => 'required|integer|min:0',
            'rating_saat_ini' => 'nullable|numeric|between:1.00,5.00',
            'catatan' => 'nullable|string',
        ];

        if ($user->hasRole('PIC Digital Cabang')) {
            $request->merge(['branch_id' => $user->branch_id]);
        }

        $validated = $request->validate($rules);

        $baseline = GoogleRatingMonth::updateOrCreate(
            [
                'branch_id' => $validated['branch_id'],
                'bulan' => $validated['bulan'],
                'tahun' => $validated['tahun'],
            ],
            [
                'user_id' => $user->id,
                'rating_awal_bulan' => $validated['rating_awal_bulan'],
                'total_review_awal_bulan' => $validated['total_review_awal_bulan'],
                'rating_saat_ini' => $validated['rating_saat_ini'] ?? $validated['rating_awal_bulan'],
                'target_bulanan' => 125,
                'target_harian' => 5,
                'catatan' => $validated['catatan'] ?? null,
            ]
        );

        AuditLogService::log(
            'UPDATE',
            'Google Rating Baseline',
            "Memperbarui Google Rating Awal Bulan {$validated['bulan']}/{$validated['tahun']} Cabang ID #{$validated['branch_id']}: {$validated['rating_awal_bulan']} Bintang"
        );

        return response()->json([
            'success' => true,
            'message' => 'Data Google Rating Awal Bulan berhasil disimpan!',
            'data' => $baseline,
        ]);
    }

    public function storeTestimonial(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'branch_id' => 'required|exists:branches,id',
            'tanggal' => 'required|date',
            'nama_reviewer' => 'nullable|string|max:255',
            'rating_bintang' => 'required|integer|between:1,5',
            'komentar_ulasan' => 'nullable|string',
            'foto_testimoni' => 'nullable|image|max:10240',
            'fotos.*' => 'nullable|image|max:10240', // Multiple photo uploads
        ];

        if ($user->hasRole('PIC Digital Cabang')) {
            $request->merge(['branch_id' => $user->branch_id]);
        }

        $validated = $request->validate($rules);

        $tanggal = Carbon::parse($validated['tanggal']);
        $bulan = $tanggal->month;
        $tahun = $tanggal->year;

        // Auto link to or create GoogleRatingMonth
        $monthRecord = GoogleRatingMonth::firstOrCreate(
            [
                'branch_id' => $validated['branch_id'],
                'bulan' => $bulan,
                'tahun' => $tahun,
            ],
            [
                'user_id' => $user->id,
                'rating_awal_bulan' => 4.80,
                'total_review_awal_bulan' => 0,
                'target_bulanan' => 125,
                'target_harian' => 5,
            ]
        );

        $savedCount = 0;

        // Check if multiple photos uploaded
        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $idx => $photo) {
                $filename = 'gr_' . time() . '_' . uniqid() . '_' . ($idx + 1) . '.' . $photo->getClientOriginalExtension();
                $path = $photo->storeAs('screenshots/google_rating', $filename, 'public');

                $reviewerName = $validated['nama_reviewer'] ? ($validated['nama_reviewer'] . ($idx > 0 ? " #{$idx}" : "")) : 'Konsumen Yamaha';

                GoogleRatingTestimonial::create([
                    'google_rating_month_id' => $monthRecord->id,
                    'branch_id' => $validated['branch_id'],
                    'user_id' => $user->id,
                    'tanggal' => $tanggal->toDateString(),
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'nama_reviewer' => $reviewerName,
                    'rating_bintang' => $validated['rating_bintang'],
                    'foto_testimoni' => $path,
                    'komentar_ulasan' => $validated['komentar_ulasan'] ?? null,
                ]);
                $savedCount++;
            }
        } elseif ($request->hasFile('foto_testimoni')) {
            $photo = $request->file('foto_testimoni');
            $filename = 'gr_' . time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
            $path = $photo->storeAs('screenshots/google_rating', $filename, 'public');

            GoogleRatingTestimonial::create([
                'google_rating_month_id' => $monthRecord->id,
                'branch_id' => $validated['branch_id'],
                'user_id' => $user->id,
                'tanggal' => $tanggal->toDateString(),
                'bulan' => $bulan,
                'tahun' => $tahun,
                'nama_reviewer' => $validated['nama_reviewer'] ?: 'Konsumen Yamaha',
                'rating_bintang' => $validated['rating_bintang'],
                'foto_testimoni' => $path,
                'komentar_ulasan' => $validated['komentar_ulasan'] ?? null,
            ]);
            $savedCount++;
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Wajib melampirkan minimal satu foto bukti testimoni Google Rating.',
            ], 422);
        }

        // Recalculate today's count for this branch
        $todayCount = GoogleRatingTestimonial::where('branch_id', $validated['branch_id'])
            ->whereDate('tanggal', now()->toDateString())
            ->count();
        $isTodayTargetMet = ($todayCount >= 5);
        $todayDeficiency = max(0, 5 - $todayCount);

        AuditLogService::log(
            'CREATE',
            'Google Rating Testimonial',
            "Menambahkan {$savedCount} testimoni Google Rating untuk Cabang ID #{$validated['branch_id']} tanggal {$tanggal->format('d M Y')}"
        );

        return response()->json([
            'success' => true,
            'message' => "Berhasil menyimpan {$savedCount} testimoni Google Rating!",
            'today_count' => $todayCount,
            'is_target_met' => $isTodayTargetMet,
            'deficiency' => $todayDeficiency,
            'warning_message' => $isTodayTargetMet
                ? "🎉 Target harian minimal 5 testimoni terpenuhi ({$todayCount} testimoni)!"
                : "⚠️ Input testimoni hari ini: {$todayCount}/5. Kurang {$todayDeficiency} testimoni lagi!",
        ]);
    }

    public function destroyTestimonial(GoogleRatingTestimonial $testimonial)
    {
        $user = Auth::user();

        if ($user->hasRole('PIC Digital Cabang') && $testimonial->branch_id != $user->branch_id) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki hak untuk menghapus data ini.'], 403);
        }

        if ($testimonial->foto_testimoni) {
            $clean = str_replace(['/storage/', 'storage/', '/files/', 'files/'], '', $testimonial->foto_testimoni);
            $withoutPublic = preg_replace('#^public/#', '', ltrim($clean, '/'));
            Storage::disk('public')->delete($withoutPublic);
            Storage::delete($withoutPublic);
        }

        $reviewer = $testimonial->nama_reviewer;
        $testimonial->delete();

        AuditLogService::log(
            'DELETE',
            'Google Rating Testimonial',
            "Menghapus testimoni Google Rating reviewer: {$reviewer}"
        );

        return response()->json([
            'success' => true,
            'message' => 'Testimoni Google Rating berhasil dihapus.',
        ]);
    }
}
