<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PersonalKpiRequest;
use App\Models\PersonalKpi;
use App\Services\AuditLogService;
use App\Services\KpiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersonalKpiController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->input('tahun', now()->year);
        $bulan = $request->input('bulan', now()->month);

        $personalKpis = PersonalKpi::where('user_id', Auth::id())
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->get();

        $overallTarget = $personalKpis->sum('target');
        $overallRealisasi = $personalKpis->sum('realisasi');
        $overallAchievement = $overallTarget > 0 ? round(($overallRealisasi / $overallTarget) * 100, 1) : 0;
        $statusBadge = KpiService::getStatusBadge($overallAchievement);

        return view('admin.personal_kpi.index', compact('personalKpis', 'tahun', 'bulan', 'overallAchievement', 'statusBadge'));
    }

    public function store(PersonalKpiRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        $personalKpi = PersonalKpi::create($data);

        AuditLogService::log('CREATE', 'Personal KPI', "Menambahkan KPI Pribadi: {$personalKpi->kategori}");

        return redirect()->route('personal-kpis.index', ['tahun' => $data['tahun'], 'bulan' => $data['bulan']])
            ->with('success', 'Target KPI pribadi berhasil ditambahkan.');
    }

    public function update(PersonalKpiRequest $request, PersonalKpi $personalKpi)
    {
        $data = $request->validated();
        $personalKpi->update($data);

        AuditLogService::log('UPDATE', 'Personal KPI', "Memperbarui KPI Pribadi: {$personalKpi->kategori}");

        return redirect()->route('personal-kpis.index', ['tahun' => $data['tahun'], 'bulan' => $data['bulan']])
            ->with('success', 'Realisasi KPI pribadi berhasil diperbarui.');
    }

    public function destroy(PersonalKpi $personalKpi)
    {
        $kategori = $personalKpi->kategori;
        $personalKpi->delete();

        AuditLogService::log('DELETE', 'Personal KPI', "Menghapus KPI Pribadi: {$kategori}");

        return redirect()->back()->with('success', 'KPI pribadi berhasil dihapus.');
    }
}
