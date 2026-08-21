<?php

namespace App\Http\Controllers\Export;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\DailyReport;
use App\Models\MonthlyInsight;
use App\Models\TiktokLiveReport;
use App\Models\WeeklyReport;
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

    public function exportExcel(Request $request)
    {
        $type = $request->input('report_type', 'daily');
        $tahun = $request->input('tahun', now()->year);
        $bulan = $request->input('bulan', now()->month);
        $branchId = $request->input('branch_id');
        $tanggal = $request->input('tanggal');
        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');
        $mingguKe = $request->input('minggu_ke');

        $titleType = ucfirst($type);
        $fileName = "DMPMS_{$titleType}_Report.csv";
        
        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($type, $tahun, $bulan, $branchId, $tanggal, $tanggalAwal, $tanggalAkhir, $mingguKe) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            if ($type === 'daily') {
                $query = DailyReport::with(['branch']);
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

                fputcsv($file, [
                    'No', 'Kode Cabang', 'Nama Cabang', 'Tanggal', 
                    'IG Feed', 'IG Reels', 'IG Story', 'IG Followers Gained',
                    'FB Post', 'FB Marketplace', 'FB Followers Gained',
                    'TikTok Post', 'TikTok Live', 'TikTok Followers Gained',
                    'Google Rating', 'Google Review Gained', 'Catatan'
                ]);

                foreach ($reports as $index => $row) {
                    fputcsv($file, [
                        $index + 1,
                        $row->branch->kode ?? '-',
                        $row->branch->nama_cabang ?? '-',
                        $row->tanggal ? $row->tanggal->format('Y-m-d') : '-',
                        $row->ig_feed,
                        $row->ig_reels,
                        $row->ig_story,
                        $row->ig_followers_gained,
                        $row->fb_post,
                        $row->fb_marketplace,
                        $row->fb_followers_gained,
                        $row->tiktok_post,
                        $row->tiktok_live,
                        $row->tiktok_followers_gained,
                        $row->google_rating,
                        $row->google_review_gained,
                        $row->catatan,
                    ]);
                }
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

                fputcsv($file, [
                    'No', 'Kode Cabang', 'Nama Cabang', 'Tanggal Live', 'Nama Host (Yang Live)',
                    'Jabatan Host', 'Durasi Jam', 'Durasi Menit', 'Total Menit', 'Diinput Oleh',
                    'Bukti Screenshot URL', 'Catatan'
                ]);

                foreach ($reports as $index => $row) {
                    fputcsv($file, [
                        $index + 1,
                        $row->branch->kode ?? '-',
                        $row->branch->nama_cabang ?? '-',
                        $row->tanggal_live ? $row->tanggal_live->format('Y-m-d') : '-',
                        $row->nama_host,
                        $row->jabatan,
                        $row->durasi_jam,
                        $row->durasi_menit,
                        $row->total_minutes,
                        $row->user->name ?? '-',
                        $row->bukti_screenshot ? asset($row->bukti_screenshot) : '-',
                        $row->catatan,
                    ]);
                }
            } elseif ($type === 'weekly') {
                $query = WeeklyReport::with(['branch']);
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

                fputcsv($file, [
                    'No', 'Kode Cabang', 'Nama Cabang', 'Tanggal Post', 'Minggu Ke', 'Tahun', 'Link Content', 
                    'Views', 'Account Reached', 'Interaksi Followers', 'Interaksi Non-Followers', 'Total Interaksi',
                    'Likes', 'Shares', 'Saves', 'Comments', 'Reposts',
                    'Profile Visits', 'External Link Taps', 'Follows',
                    'Source Feed (%)', 'Source Profile (%)', 'Source Stories (%)',
                    'Gender Men (%)', 'Gender Women (%)', 'Top Country', 'Top Age', 'Catatan'
                ]);

                foreach ($reports as $index => $row) {
                    fputcsv($file, [
                        $index + 1,
                        $row->branch->kode ?? '-',
                        $row->branch->nama_cabang ?? '-',
                        $row->tanggal_post ? $row->tanggal_post->format('Y-m-d') : '-',
                        $row->minggu_ke,
                        $row->tahun,
                        $row->link_content,
                        $row->views,
                        $row->account_reached,
                        $row->interactions_followers,
                        $row->interactions_non_followers,
                        $row->total_interactions,
                        $row->likes,
                        $row->shares,
                        $row->saves,
                        $row->comments,
                        $row->reposts,
                        $row->profile_visits,
                        $row->external_link_taps,
                        $row->follows,
                        $row->source_feed_pct,
                        $row->source_profile_pct,
                        $row->source_stories_pct,
                        $row->gender_men_pct,
                        $row->gender_women_pct,
                        $row->top_country,
                        $row->top_age,
                        $row->catatan,
                    ]);
                }
            } else {
                $insights = MonthlyInsight::with(['branch'])
                    ->where('tahun', $tahun)
                    ->where('bulan', $bulan)
                    ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                    ->get();

                fputcsv($file, [
                    'No', 'Kode Cabang', 'Nama Cabang', 'Tahun', 'Bulan',
                    'IG Views', 'IG Reach', 'IG Accounts Reached', 'IG Profile Visits', 'IG Followers',
                    'IG Male %', 'IG Female %', 'IG Top Age', 'IG Top Cities',
                    'FB Views', 'FB Followers', 'TikTok Views', 'TikTok Followers',
                    'Google Rating', 'Google Reviews'
                ]);

                foreach ($insights as $index => $row) {
                    fputcsv($file, [
                        $index + 1,
                        $row->branch->kode ?? '-',
                        $row->branch->nama_cabang ?? '-',
                        $row->tahun,
                        $row->bulan,
                        $row->ig_views,
                        $row->ig_reach,
                        $row->ig_accounts_reached,
                        $row->ig_profile_visits,
                        $row->ig_total_followers,
                        $row->ig_male_pct,
                        $row->ig_female_pct,
                        $row->ig_top_age,
                        $row->ig_top_cities,
                        $row->fb_views,
                        $row->fb_total_followers,
                        $row->tiktok_views,
                        $row->tiktok_total_followers,
                        $row->google_total_rating,
                        $row->google_total_reviews,
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
