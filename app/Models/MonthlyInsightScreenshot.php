<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyInsightScreenshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'monthly_insight_id',
        'kategori',
        'file_path',
        'file_name',
    ];

    public function monthlyInsight()
    {
        return $this->belongsTo(MonthlyInsight::class);
    }
}
