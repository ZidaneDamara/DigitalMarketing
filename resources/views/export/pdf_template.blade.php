<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Performance Digital Marketing - Yamaha DMPMS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #333;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #003399;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h2 {
            margin: 0;
            color: #003399;
            text-transform: uppercase;
            font-size: 16px;
        }
        .header p {
            margin: 3px 0 0 0;
            color: #666;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th {
            background-color: #003399;
            color: #ffffff;
            padding: 6px 4px;
            font-size: 9px;
            text-align: center;
        }
        td {
            padding: 5px 4px;
            font-size: 9px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 9px;
            color: #777;
        }
        .badge-f { background-color: #198754; color: #fff; padding: 2px 4px; border-radius: 3px; }
        .badge-nf { background-color: #ffc107; color: #000; padding: 2px 4px; border-radius: 3px; }
    </style>
</head>
<body>

    <div class="header">
        <h2>PT. ASPACINDO KEDATON MOTOR</h2>
        <p>LAPORAN DIGITAL MARKETING PERFORMANCE MANAGEMENT SYSTEM (DMPMS)</p>
        <p><strong>JENIS LAPORAN: {{ strtoupper($type) }} REPORT 
        @if(!empty($tanggal))
            | Tanggal: {{ date('d F Y', strtotime($tanggal)) }}
        @elseif(!empty($tanggalAwal) && !empty($tanggalAkhir))
            | Periode: {{ date('d/m/Y', strtotime($tanggalAwal)) }} s/d {{ date('d/m/Y', strtotime($tanggalAkhir)) }}
        @elseif(!empty($mingguKe))
            | Minggu Ke-{{ $mingguKe }} Tahun {{ $tahun }}
        @else
            | Periode: {{ date('F', mktime(0, 0, 0, $bulan, 1)) }} {{ $tahun }}
        @endif
        </strong></p>
    </div>

    @if($type === 'daily')
        <h4>Detail Laporan Harian (Daily Reports)</h4>
        <table>
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th>Tanggal</th>
                    <th>Kode</th>
                    <th>Nama Cabang</th>
                    <th class="text-center">IG Feed</th>
                    <th class="text-center">IG Reels</th>
                    <th class="text-center">IG Story</th>
                    <th class="text-center">FB Post</th>
                    <th class="text-center">TikTok Post</th>
                    <th class="text-center">Foll Gained</th>
                    <th class="text-center">Google Rating</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dailyReports as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row->tanggal ? $row->tanggal->format('d/m/Y') : '-' }}</td>
                    <td>{{ $row->branch->kode ?? '-' }}</td>
                    <td>{{ $row->branch->nama_cabang ?? '-' }}</td>
                    <td class="text-center">{{ $row->ig_feed }}</td>
                    <td class="text-center">{{ $row->ig_reels }}</td>
                    <td class="text-center">{{ $row->ig_story }}</td>
                    <td class="text-center">{{ $row->fb_post }}</td>
                    <td class="text-center">{{ $row->tiktok_post }}</td>
                    <td class="text-center">+{{ number_format($row->ig_followers_gained + $row->fb_followers_gained + $row->tiktok_followers_gained) }}</td>
                    <td class="text-center">{{ $row->google_rating }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center">Tidak ada data laporan harian pada filter ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

    @elseif($type === 'tiktok_live')
        <h4>Detail Laporan Harian Live TikTok</h4>
        <table>
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th>Tanggal Live</th>
                    <th>Kode</th>
                    <th>Nama Cabang</th>
                    <th>Nama Host (Yang Live)</th>
                    <th class="text-center">Jabatan</th>
                    <th class="text-center">Durasi</th>
                    <th class="text-center">Penonton</th>
                    <th class="text-center">Likes</th>
                    <th class="text-center">Komentar / Share</th>
                    <th class="text-center">STU</th>
                    <th>Bukti SS Live</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tiktokLiveReports as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $row->tanggal_live ? $row->tanggal_live->format('d/m/Y') : '-' }}</td>
                    <td>{{ $row->branch->kode ?? '-' }}</td>
                    <td>{{ $row->branch->nama_cabang ?? '-' }}</td>
                    <td><strong>{{ $row->nama_host }}</strong></td>
                    <td class="text-center">
                        <span class="{{ $row->jabatan === 'PIC Digital' ? 'badge-f' : 'badge-nf' }}">{{ $row->jabatan }}</span>
                    </td>
                    <td class="text-center">{{ $row->formatted_durasi }}</td>
                    <td class="text-center">{{ number_format($row->jumlah_penonton) }}</td>
                    <td class="text-center">{{ number_format($row->jumlah_like) }}</td>
                    <td class="text-center">{{ number_format($row->jumlah_komentar) }} / {{ number_format($row->jumlah_share) }}</td>
                    <td class="text-center"><strong>{{ $row->stu !== null ? number_format($row->stu) . ' Unit' : '-' }}</strong></td>
                    <td class="text-center">
                        @if($row->bukti_screenshot_url)
                            <a href="{{ $row->bukti_screenshot_url }}" target="_blank" style="color: #003399; font-weight: bold; text-decoration: underline;">Buka SS</a>
                        @else
                            <span style="color: #999;">-</span>
                        @endif
                    </td>
                    <td>{{ $row->catatan ?: '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="13" class="text-center">Tidak ada data laporan Live TikTok pada filter ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

    @elseif($type === 'weekly')
        <h4>Detail Laporan Mingguan (Post Insight)</h4>
        <table>
            <thead>
                <tr>
                    <th style="width: 20px;">No</th>
                    <th>Cabang</th>
                    <th>Tgl Post</th>
                    <th>Views</th>
                    <th>Reach</th>
                    <th>Interaksi (F / NF)</th>
                    <th>Likes / Shares / Saves</th>
                    <th>Profile Visits / Follows</th>
                    <th>Top Sources</th>
                    <th>Audience (Gender/Age)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($weeklyReports as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row->branch->nama_cabang ?? '-' }}</td>
                    <td class="text-center">{{ $row->tanggal_post ? $row->tanggal_post->format('d/m/Y') : '-' }}</td>
                    <td class="text-right">{{ number_format($row->views) }}</td>
                    <td class="text-right">{{ number_format($row->account_reached) }}</td>
                    <td class="text-center">
                        {{ number_format($row->total_interactions) }}<br>
                        <small>F: {{ $row->followers_ratio_pct }}% | NF: {{ $row->non_followers_ratio_pct }}%</small>
                    </td>
                    <td class="text-center">{{ $row->likes }} / {{ $row->shares }} / {{ $row->saves }}</td>
                    <td class="text-center">{{ number_format($row->profile_visits) }} / {{ number_format($row->follows) }}</td>
                    <td class="text-center"><small>Feed {{ $row->source_feed_pct }}% | Prof {{ $row->source_profile_pct }}% | St {{ $row->source_stories_pct }}%</small></td>
                    <td><small>Men {{ $row->gender_men_pct }}% / Wmn {{ $row->gender_women_pct }}%<br>{{ $row->top_age }}</small></td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center">Tidak ada data laporan mingguan pada filter ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

    @else
        <h4>Ringkasan Monthly Insights</h4>
        <table>
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th>Kode</th>
                    <th>Nama Cabang</th>
                    <th class="text-right">IG Views</th>
                    <th class="text-right">IG Reach</th>
                    <th class="text-right">IG Followers</th>
                    <th class="text-right">FB Views</th>
                    <th class="text-right">TikTok Views</th>
                    <th class="text-center">Google Rating</th>
                </tr>
            </thead>
            <tbody>
                @forelse($monthlyInsights as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row->branch->kode ?? '-' }}</td>
                    <td>{{ $row->branch->nama_cabang ?? '-' }}</td>
                    <td class="text-right">{{ number_format($row->ig_views) }}</td>
                    <td class="text-right">{{ number_format($row->ig_reach) }}</td>
                    <td class="text-right">{{ number_format($row->ig_total_followers) }}</td>
                    <td class="text-right">{{ number_format($row->fb_total_followers) }}</td>
                    <td class="text-right">{{ number_format($row->tiktok_views) }}</td>
                    <td class="text-center">{{ $row->google_total_rating }} ({{ number_format($row->google_total_reviews) }})</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center">Tidak ada data monthly insight pada filter ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    <div class="footer">
        Dicetak otomatis oleh Sistem Yamaha DMPMS pada: {{ now()->format('d M Y H:i:s') }}
    </div>

</body>
</html>
