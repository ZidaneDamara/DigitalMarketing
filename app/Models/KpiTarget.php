<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiTarget extends Model
{
    use HasFactory;

    protected $fillable = [
        'kpi_id',
        'ig_feed_target',
        'ig_reels_target',
        'ig_story_target',
        'ig_followers_target',
        'fb_post_target',
        'fb_marketplace_target',
        'fb_followers_target',
        'tiktok_post_target',
        'tiktok_live_target',
        'tiktok_followers_target',
        'google_rating_target',
        'google_review_target',
    ];

    public function kpi()
    {
        return $this->belongsTo(Kpi::class);
    }
}
