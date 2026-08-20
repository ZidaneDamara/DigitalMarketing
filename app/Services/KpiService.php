<?php

namespace App\Services;

use App\Models\Kpi;
use App\Models\KpiTarget;
use Illuminate\Support\Facades\DB;

class KpiService
{
    public static function getStatusBadge(float $percentage): array
    {
        if ($percentage >= 100) {
            return [
                'label' => 'Excellent (>100%)',
                'color' => 'primary',
                'hex' => '#0d6efd',
                'bg_class' => 'bg-primary text-white',
            ];
        } elseif ($percentage >= 80) {
            return [
                'label' => 'Hijau (80%-100%)',
                'color' => 'success',
                'hex' => '#198754',
                'bg_class' => 'bg-success text-white',
            ];
        } elseif ($percentage >= 70) {
            return [
                'label' => 'Kuning (70%-80%)',
                'color' => 'warning',
                'hex' => '#ffc107',
                'bg_class' => 'bg-warning text-dark',
            ];
        } else {
            return [
                'label' => 'Merah (<70%)',
                'color' => 'danger',
                'hex' => '#dc3545',
                'bg_class' => 'bg-danger text-white',
            ];
        }
    }

    public function copyPreviousMonthKpi(int $fromYear, int $fromMonth, int $toYear, int $toMonth, int $userId): Kpi
    {
        return DB::transaction(function () use ($fromYear, $fromMonth, $toYear, $toMonth, $userId) {
            $sourceKpi = Kpi::with('target')
                ->where('tahun', $fromYear)
                ->where('bulan', $fromMonth)
                ->firstOrFail();

            $targetKpi = Kpi::updateOrCreate(
                ['tahun' => $toYear, 'bulan' => $toMonth],
                ['created_by' => $userId]
            );

            if ($sourceKpi->target) {
                $targetData = $sourceKpi->target->toArray();
                unset($targetData['id'], $targetData['kpi_id'], $targetData['created_at'], $targetData['updated_at']);

                KpiTarget::updateOrCreate(
                    ['kpi_id' => $targetKpi->id],
                    $targetData
                );
            }

            AuditLogService::log(
                'COPY_KPI',
                'KPI Management',
                "Copy KPI dari $fromMonth/$fromYear ke $toMonth/$toYear"
            );

            return $targetKpi;
        });
    }
}
