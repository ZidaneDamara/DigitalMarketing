<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'user_id',
        'tanggal_post',
        'link_content',
        'start_date',
        'end_date',
        'tahun',
        'minggu_ke',
        'views',
        'account_reached',
        'interactions_followers',
        'interactions_non_followers',
        'likes',
        'shares',
        'saves',
        'comments',
        'reposts',
        'profile_visits',
        'external_link_taps',
        'follows',
        'source_feed_pct',
        'source_profile_pct',
        'source_stories_pct',
        'gender_men_pct',
        'gender_women_pct',
        'top_country',
        'top_age',
        'catatan',
    ];

    protected $casts = [
        'tanggal_post' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'source_feed_pct' => 'decimal:2',
        'source_profile_pct' => 'decimal:2',
        'source_stories_pct' => 'decimal:2',
        'gender_men_pct' => 'decimal:2',
        'gender_women_pct' => 'decimal:2',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTotalInteractionsAttribute(): int
    {
        return $this->interactions_followers + $this->interactions_non_followers;
    }

    public function getFollowersRatioPctAttribute(): float
    {
        $total = $this->total_interactions;
        if ($total == 0) return 0.0;
        return round(($this->interactions_followers / $total) * 100, 1);
    }

    public function getNonFollowersRatioPctAttribute(): float
    {
        $total = $this->total_interactions;
        if ($total == 0) return 0.0;
        return round(($this->interactions_non_followers / $total) * 100, 1);
    }
}
