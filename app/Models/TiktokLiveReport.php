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

    public function getTotalMinutesAttribute(): int
    {
        return ($this->durasi_jam * 60) + $this->durasi_menit;
    }
}
