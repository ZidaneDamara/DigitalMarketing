<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleRatingTestimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'google_rating_month_id',
        'branch_id',
        'user_id',
        'tanggal',
        'bulan',
        'tahun',
        'nama_reviewer',
        'rating_bintang',
        'foto_testimoni',
        'komentar_ulasan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'bulan' => 'integer',
        'tahun' => 'integer',
        'rating_bintang' => 'integer',
    ];

    protected $appends = [
        'foto_testimoni_url',
        'formatted_tanggal',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function googleRatingMonth()
    {
        return $this->belongsTo(GoogleRatingMonth::class);
    }

    public function getFotoTestimoniUrlAttribute(): ?string
    {
        $raw = trim($this->foto_testimoni ?? '');
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

    public function getFormattedTanggalAttribute(): string
    {
        return $this->tanggal ? $this->tanggal->format('d M Y') : '-';
    }
}
