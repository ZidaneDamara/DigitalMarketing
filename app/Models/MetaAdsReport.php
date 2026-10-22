<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetaAdsReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'user_id',
        'bulan',
        'tahun',
        'tanggal_laporan',
        'nama_iklan',
        'product',
        'ctr',
        'cpc',
        'cpm',
        'cpl',
        'total_views',
        'total_percakapan',
        'budget',
        'bukti_topup',
        'stu',
        'unit_terjual',
        'catatan',
    ];

    protected $casts = [
        'bulan' => 'integer',
        'tahun' => 'integer',
        'tanggal_laporan' => 'date',
        'ctr' => 'float',
        'cpc' => 'float',
        'cpm' => 'float',
        'cpl' => 'float',
        'total_views' => 'integer',
        'total_percakapan' => 'integer',
        'budget' => 'float',
        'stu' => 'integer',
    ];

    protected $appends = [
        'bukti_topup_url',
        'is_bukti_topup_pdf',
        'formatted_budget',
        'formatted_cpc',
        'formatted_cpm',
        'formatted_cpl',
        'cost_per_stu',
        'conversion_rate',
        'nama_bulan',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function closingProofs()
    {
        return $this->hasMany(MetaAdsClosingProof::class);
    }

    public function prospects()
    {
        return $this->hasMany(MetaAdsProspect::class);
    }

    public function getIsBuktiTopupPdfAttribute(): bool
    {
        return strtolower(pathinfo($this->bukti_topup ?? '', PATHINFO_EXTENSION)) === 'pdf';
    }

    public function getBuktiTopupUrlAttribute(): ?string
    {
        $raw = trim($this->bukti_topup ?? '');
        if ($raw === '') {
            return null;
        }

        if (preg_match('#https?://#i', $raw)) {
            if (preg_match('#(screenshots|tiktok_live_screenshots)/.+$#i', $raw, $matches)) {
                $cleanPath = $matches[0];
            } else {
                $parsed = parse_url($raw, PHP_URL_PATH);
                $cleanPath = $parsed ?? $raw;
            }
        } else {
            $cleanPath = $raw;
        }

        $cleanPath = preg_replace('#^/(storage|files|public)/#i', '', $cleanPath);
        $cleanPath = preg_replace('#^(storage|files|public)/#i', '', $cleanPath);

        return url('files/' . ltrim($cleanPath, '/'));
    }

    public function getFormattedBudgetAttribute(): string
    {
        return 'Rp ' . number_format((float) ($this->budget ?? 0), 0, ',', '.');
    }

    public function getFormattedCpcAttribute(): string
    {
        return 'Rp ' . number_format((float) ($this->cpc ?? 0), 0, ',', '.');
    }

    public function getFormattedCpmAttribute(): string
    {
        return 'Rp ' . number_format((float) ($this->cpm ?? 0), 0, ',', '.');
    }

    public function getFormattedCplAttribute(): string
    {
        return 'Rp ' . number_format((float) ($this->cpl ?? 0), 0, ',', '.');
    }

    public function getCostPerStuAttribute(): float
    {
        if ($this->stu > 0 && $this->budget > 0) {
            return round($this->budget / $this->stu);
        }
        return 0;
    }

    public function getConversionRateAttribute(): float
    {
        if ($this->total_percakapan > 0 && $this->stu > 0) {
            return round(($this->stu / $this->total_percakapan) * 100, 2);
        }
        return 0;
    }

    public function getNamaBulanAttribute(): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $months[$this->bulan] ?? ('Bulan ' . $this->bulan);
    }
}
