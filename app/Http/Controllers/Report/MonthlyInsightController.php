<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Requests\MonthlyInsightRequest;
use App\Models\Branch;
use App\Models\MonthlyInsight;
use App\Models\MonthlyInsightScreenshot;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MonthlyInsightController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $tahun = $request->input('tahun', now()->year);
        $bulan = $request->input('bulan', now()->month);

        $branchId = $user->hasRole('PIC Digital Cabang') ? $user->branch_id : $request->input('branch_id');

        $insightsQuery = MonthlyInsight::with(['branch', 'user', 'screenshots'])
            ->where('tahun', $tahun)
            ->where('bulan', $bulan);

        if ($branchId) {
            $insightsQuery->where('branch_id', $branchId);
        }

        $insights = $insightsQuery->get();
        $branches = Branch::where('status', 'active')->get();

        $selectedInsight = null;
        if ($user->hasRole('PIC Digital Cabang')) {
            $selectedInsight = MonthlyInsight::with('screenshots')
                ->where('branch_id', $user->branch_id)
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->first();
        }

        return view('reports.monthly.index', compact('insights', 'branches', 'tahun', 'bulan', 'branchId', 'selectedInsight'));
    }

    public function store(MonthlyInsightRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        $data['ig_top_age'] = $data['ig_top_age'] ?? '';
        $data['ig_top_cities'] = $data['ig_top_cities'] ?? '';

        $insight = MonthlyInsight::updateOrCreate(
            ['branch_id' => $data['branch_id'], 'tahun' => $data['tahun'], 'bulan' => $data['bulan']],
            array_merge($data, ['user_id' => $user->id])
        );

        // Process Screenshot Uploads per category
        $screenshotTypes = [
            'screenshot_ig' => 'Instagram Insight',
            'screenshot_fb' => 'Facebook Insight',
            'screenshot_tiktok' => 'TikTok Analytics',
            'screenshot_google' => 'Google Business',
        ];

        foreach ($screenshotTypes as $inputName => $kategori) {
            if ($request->hasFile($inputName)) {
                $file = $request->file($inputName);
                $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('screenshots', $filename, 'public');

                // Update or create screenshot record
                MonthlyInsightScreenshot::updateOrCreate(
                    ['monthly_insight_id' => $insight->id, 'kategori' => $kategori],
                    [
                        'file_path' => $path,
                        'file_name' => $filename,
                    ]
                );
            }
        }

        AuditLogService::log(
            'CREATE',
            'Monthly Insight',
            "Input Monthly Insight & Upload Screenshot periode {$data['bulan']}/{$data['tahun']} cabang ID {$data['branch_id']}"
        );

        return redirect()->route('reports.monthly.index', ['tahun' => $data['tahun'], 'bulan' => $data['bulan']])
            ->with('success', 'Monthly Insight dan Screenshot pendukung berhasil disimpan.');
    }
}
