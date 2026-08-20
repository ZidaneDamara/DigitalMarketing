<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'user_id',
        'tanggal',
        'ig_feed',
        'ig_reels',
        'ig_story',
        'ig_followers_gained',
        'fb_post',
        'fb_marketplace',
        'fb_followers_gained',
        'tiktok_post',
        'tiktok_live',
        'tiktok_followers_gained',
        'google_rating',
        'google_review_gained',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'google_rating' => 'decimal:1',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isLocked(): bool
    {
        // Daily Report only editable on current day (00:00 - 23:59)
        return $this->tanggal->format('Y-m-d') !== now()->format('Y-m-d');
    }
}
