<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyInsight extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'user_id',
        'tahun',
        'bulan',
        'ig_views',
        'ig_reach',
        'ig_accounts_reached',
        'ig_profile_visits',
        'ig_total_followers',
        'ig_male_pct',
        'ig_female_pct',
        'ig_top_age',
        'ig_top_cities',
        'fb_views',
        'fb_total_followers',
        'tiktok_views',
        'tiktok_total_followers',
        'google_total_rating',
        'google_total_reviews',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function screenshots()
    {
        return $this->hasMany(MonthlyInsightScreenshot::class);
    }
}
