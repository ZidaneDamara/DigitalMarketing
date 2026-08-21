<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\TiktokLiveReport;
use App\Models\User;
use Illuminate\Database\Seeder;

class TiktokLiveReportSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::all();
        $user = User::first();

        if ($branches->isEmpty() || !$user) {
            return;
        }

        $hosts = [
            ['nama' => 'Rina Setyowati', 'jabatan' => 'PIC Digital'],
            ['nama' => 'Budi Pratama', 'jabatan' => 'Sales Digital'],
            ['nama' => 'Dewi Anggraini', 'jabatan' => 'Sales Digital'],
            ['nama' => 'Siti Nurhaliza', 'jabatan' => 'PIC Digital'],
            ['nama' => 'Andi Wijaya', 'jabatan' => 'Sales Digital'],
        ];

        foreach ($branches as $index => $branch) {
            $hostInfo = $hosts[$index % count($hosts)];

            TiktokLiveReport::create([
                'branch_id' => $branch->id,
                'user_id' => $user->id,
                'nama_host' => $hostInfo['nama'],
                'jabatan' => $hostInfo['jabatan'],
                'tanggal_live' => now()->subDays($index)->format('Y-m-d'),
                'durasi_jam' => rand(1, 2),
                'durasi_menit' => rand(10, 50),
                'jumlah_penonton' => rand(350, 2500),
                'jumlah_like' => rand(1200, 15000),
                'jumlah_komentar' => rand(45, 300),
                'jumlah_share' => rand(12, 95),
                'stu' => rand(0, 5),
                'catatan' => 'Sesi Live TikTok promo NMAX Turbo & Aerox Alpha Series. Penonton sangat antusias.',
            ]);
        }
    }
}
