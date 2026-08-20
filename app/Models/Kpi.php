<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kpi extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun',
        'bulan',
        'created_by',
    ];

    public function target()
    {
        return $this->hasOne(KpiTarget::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
