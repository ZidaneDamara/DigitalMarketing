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

    protected $appends = ['file_url'];

    public function getFileUrlAttribute(): ?string
    {
        $raw = trim($this->file_path ?? '');
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

    public function monthlyInsight()
    {
        return $this->belongsTo(MonthlyInsight::class);
    }
}
