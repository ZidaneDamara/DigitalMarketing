<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kode',
        'nama_cabang',
        'alamat',
        'area',
        'manager_name',
        'status',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class);
    }

    public function monthlyInsights()
    {
        return $this->hasMany(MonthlyInsight::class);
    }
}
