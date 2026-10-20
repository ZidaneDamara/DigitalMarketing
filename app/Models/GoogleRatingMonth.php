<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleRatingMonth extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'user_id',
        'bulan',
        'tahun',
        'rating_awal_bulan',
        'total_review_awal_bulan',
        'target_bulanan',
        'target_harian',
        'rating_saat_ini',
        'catatan',
    ];

    protected $casts = [
        'bulan' => 'integer',
        'tahun' => 'integer',
        'rating_awal_bulan' => 'float',
        'total_review_awal_bulan' => 'integer',
        'target_bulanan' => 'integer',
        'target_harian' => 'integer',
        'rating_saat_ini' => 'float',
    ];

    protected $appends = [
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

    public function testimonials()
    {
        return $this->hasMany(GoogleRatingTestimonial::class);
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
