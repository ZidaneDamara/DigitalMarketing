<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiktokLiveReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'user_id',
        'nama_host',
        'jabatan',
        'tanggal_live',
        'durasi_jam',
        'durasi_menit',
        'jumlah_penonton',
        'jumlah_like',
        'jumlah_komentar',
        'jumlah_share',
        'stu',
        'bukti_screenshot',
        'catatan',
    ];

    protected $casts = [
        'tanggal_live' => 'date',
        'durasi_jam' => 'integer',
        'durasi_menit' => 'integer',
        'jumlah_penonton' => 'integer',
        'jumlah_like' => 'integer',
        'jumlah_komentar' => 'integer',
        'jumlah_share' => 'integer',
        'stu' => 'integer',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedDurasiAttribute(): string
    {
        $parts = [];
        if ($this->durasi_jam > 0) {
            $parts[] = $this->durasi_jam . ' Jam';
        }
        if ($this->durasi_menit > 0 || empty($parts)) {
            $parts[] = $this->durasi_menit . ' Menit';
        }
        return implode(' ', $parts);
    }

    protected $appends = ['bukti_screenshot_url'];

    public function getBuktiScreenshotUrlAttribute(): ?string
    {
        $raw = trim($this->bukti_screenshot ?? '');
        if ($raw === '') {
            return null;
        }

        if (preg_match('#https?://#i', $raw)) {
            if (preg_match('#(tiktok_live_screenshots|screenshots)/.+$#i', $raw, $matches)) {
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

    public function getTotalMinutesAttribute(): int
    {
        return ($this->durasi_jam * 60) + $this->durasi_menit;
    }
}
