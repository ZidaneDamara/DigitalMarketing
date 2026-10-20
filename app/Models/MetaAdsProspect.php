<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetaAdsProspect extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'user_id',
        'meta_ads_report_id',
        'nama',
        'nomor_hp',
        'sumber_leads',
        'tanggal_input',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_input' => 'date',
    ];

    protected $appends = [
        'wa_link',
        'formatted_tanggal',
        'sumber_badge',
        'status_badge',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function metaAdsReport()
    {
        return $this->belongsTo(MetaAdsReport::class);
    }

    public function getWaLinkAttribute(): string
    {
        $phone = preg_replace('/[^0-9]/', '', $this->nomor_hp ?? '');
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (str_starts_with($phone, '8')) {
            $phone = '62' . $phone;
        }

        $namaCabang = $this->branch?->nama_cabang ?? 'Yamaha Aspacindo';
        $greeting = urlencode("Halo {$this->nama}, terima kasih telah menghubungi {$namaCabang}. Apakah ada promo motor Yamaha yang bisa kami bantu informasikan?");
        return "https://wa.me/{$phone}?text={$greeting}";
    }

    public function getFormattedTanggalAttribute(): string
    {
        return $this->tanggal_input ? $this->tanggal_input->format('d M Y') : '-';
    }

    public function getSumberBadgeAttribute(): string
    {
        switch ($this->sumber_leads) {
            case 'Instagram':
                return '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-2.5 py-1 fw-semibold"><i class="fab fa-instagram me-1"></i>Instagram</span>';
            case 'Facebook':
                return '<span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2.5 py-1 fw-semibold"><i class="fab fa-facebook me-1"></i>Facebook</span>';
            case 'WhatsApp':
            default:
                return '<span class="badge bg-success bg-opacity-10 text-success border border-success px-2.5 py-1 fw-semibold"><i class="fab fa-whatsapp me-1"></i>WhatsApp</span>';
        }
    }

    public function getStatusBadgeAttribute(): string
    {
        switch ($this->status) {
            case 'Closing STU':
                return '<span class="badge bg-danger text-white px-2.5 py-1 fw-bold"><i class="fas fa-motorcycle me-1"></i>Closing STU</span>';
            case 'Dihubungi':
                return '<span class="badge bg-info text-white px-2.5 py-1 fw-semibold"><i class="fas fa-phone-alt me-1"></i>Dihubungi</span>';
            case 'Tidak Tertarik':
                return '<span class="badge bg-secondary text-white px-2.5 py-1 fw-semibold"><i class="fas fa-times me-1"></i>Tidak Tertarik</span>';
            case 'Prospek Baru':
            default:
                return '<span class="badge bg-warning text-dark px-2.5 py-1 fw-semibold"><i class="fas fa-star me-1"></i>Prospek Baru</span>';
        }
    }
}
