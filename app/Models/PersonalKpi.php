<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalKpi extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tahun',
        'bulan',
        'kategori',
        'target',
        'realisasi',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getAchievementAttribute(): float
    {
        if ($this->target <= 0) return 0;
        return round(($this->realisasi / $this->target) * 100, 1);
    }
}
