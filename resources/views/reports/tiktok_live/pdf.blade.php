<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Live TikTok - {{ $report->branch->nama_cabang }} - {{ $report->tanggal_live->format('d M Y') }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #1e293b;
            margin: 0;
            padding: 15px;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #003399;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            color: #003399;
            text-transform: uppercase;
            font-size: 18px;
            letter-spacing: 0.5px;
        }
        .header h3 {
            margin: 4px 0 0 0;
            color: #e60012;
            font-size: 14px;
            font-weight: bold;
        }
        .header p {
            margin: 4px 0 0 0;
            color: #64748b;
            font-size: 10px;
        }
        .card-detail {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #f8fafc;
        }
        .table-info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .table-info td {
            padding: 6px 8px;
            vertical-align: top;
        }
        .table-info td.label {
            font-weight: bold;
            color: #475569;
            width: 30%;
        }
        .table-info td.value {
            color: #0f172a;
            width: 70%;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            color: #ffffff;
        }
        .badge-pic { background-color: #0d6efd; }
        .badge-sales { background-color: #198754; }
        .badge-stu { background-color: #198754; color: #fff; padding: 2px 6px; border-radius: 3px; font-weight: bold; }
        
        .screenshot-box {
            text-align: center;
            margin-top: 15px;
            border: 1px solid #e2e8f0;
            padding: 10px;
            border-radius: 8px;
            background: #ffffff;
        }
        .screenshot-box img {
            max-width: 100%;
            max-height: 400px;
            object-fit: contain;
            border-radius: 6px;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>PT. ASPACINDO KEDATON MOTOR</h2>
        <h3>LAPORAN HARIAN LIVE TIKTOK</h3>
        <p>Digital Marketing Performance Management System (Yamaha DMPMS)</p>
    </div>

    <div class="card-detail">
        <table class="table-info">
            <tr>
                <td class="label">Nama Cabang</td>
                <td class="value">: <strong>{{ $report->branch->nama_cabang }}</strong> (Kode: {{ $report->branch->kode }})</td>
            </tr>
            <tr>
                <td class="label">Tanggal Live</td>
                <td class="value">: {{ $report->tanggal_live->format('d F Y') }}</td>
            </tr>
            <tr>
                <td class="label">Nama Yang Live (Host)</td>
                <td class="value">: <strong>{{ $report->nama_host }}</strong></td>
            </tr>
            <tr>
                <td class="label">Jabatan Host</td>
                <td class="value">: 
                    @if($report->jabatan === 'PIC Digital')
                        <span class="badge badge-pic">PIC Digital</span>
                    @else
                        <span class="badge badge-sales">Sales Digital</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">Durasi Live</td>
                <td class="value">: <strong>{{ $report->formatted_durasi }}</strong> ({{ $report->total_minutes }} Menit)</td>
            </tr>
            <tr>
                <td class="label">Jumlah Penonton</td>
                <td class="value">: <strong>{{ number_format($report->jumlah_penonton) }}</strong> Viewer</td>
            </tr>
            <tr>
                <td class="label">Jumlah Likes</td>
                <td class="value">: <strong>{{ number_format($report->jumlah_like) }}</strong> Likes</td>
            </tr>
            <tr>
                <td class="label">Jumlah Komentar & Share</td>
                <td class="value">: {{ number_format($report->jumlah_komentar) }} Komentar | {{ number_format($report->jumlah_share) }} Shares</td>
            </tr>
            <tr>
                <td class="label">STU (Sales To Unit)</td>
                <td class="value">: 
                    @if($report->stu !== null)
                        <span class="badge-stu">{{ number_format($report->stu) }} Unit</span>
                    @else
                        <span style="color: #94a3b8;">- (Tidak Ada / Null)</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">Diinput Oleh User</td>
                <td class="value">: {{ $report->user->name }} ({{ $report->user->email }})</td>
            </tr>
            <tr>
                <td class="label">Catatan / Keterangan</td>
                <td class="value">: {{ $report->catatan ?: '-' }}</td>
            </tr>
        </table>
    </div>

    <h4 style="margin-bottom: 8px; color: #003399;">Bukti Screenshot Live TikTok:</h4>
    <div class="screenshot-box">
        @if($report->bukti_screenshot && file_exists(public_path($report->bukti_screenshot)))
            <img src="{{ public_path($report->bukti_screenshot) }}" alt="Bukti Screenshot Live TikTok">
        @elseif($report->bukti_screenshot)
            <img src="{{ asset($report->bukti_screenshot) }}" alt="Bukti Screenshot Live TikTok">
        @else
            <p style="color: #94a3b8; font-style: italic; padding: 20px;">Tidak ada bukti screenshot yang diunggah.</p>
        @endif
    </div>

    <div class="footer">
        Dokumen dicetak secara otomatis oleh Sistem Yamaha DMPMS pada: {{ now()->format('d F Y H:i:s') }}
    </div>

</body>
</html>
