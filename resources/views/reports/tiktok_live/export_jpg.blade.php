<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export JPG - Laporan Live TikTok {{ $report->nama_host }}</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0f172a;
            color: #334155;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .export-card-wrapper {
            width: 100%;
            max-width: 680px;
        }

        .export-card {
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .export-header {
            background: linear-gradient(135deg, #002266 0%, #001540 100%);
            color: #ffffff;
            padding: 25px 30px;
            position: relative;
        }

        .export-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #E60012 0%, #003399 100%);
        }

        .badge-jabatan-pic { background-color: #0d6efd; color: #fff; }
        .badge-jabatan-sales { background-color: #198754; color: #fff; }

        .screenshot-container {
            background-color: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 12px;
            text-align: center;
        }

        .screenshot-container img {
            max-height: 380px;
            width: auto;
            max-width: 100%;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="export-card-wrapper">

    <!-- Action Toolbar (Hidden during canvas render) -->
    <div class="d-flex justify-content-between align-items-center mb-3 text-white">
        <div>
            <h6 class="m-0 fw-bold"><i class="fas fa-file-image me-2 text-success"></i> Export Laporan TikTok Live (JPG)</h6>
            <small class="text-white-50">Tekan tombol di bawah untuk menyimpan gambar JPG.</small>
        </div>
        <button id="btnDownloadJpg" class="btn btn-success rounded-pill px-4 fw-bold shadow">
            <i class="fas fa-download me-1.5"></i> Download Gambar JPG
        </button>
    </div>

    <!-- Target Export Canvas Card -->
    <div id="captureTarget" class="export-card">
        <!-- Header -->
        <div class="export-header d-flex align-items-center justify-content-between">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="fab fa-tiktok fs-4 text-danger"></i>
                    <span class="fw-bold tracking-wider fs-5" style="letter-spacing: 0.5px;">YAMAHA DMPMS</span>
                </div>
                <h5 class="fw-bold m-0 text-white">LAPORAN HARIAN LIVE TIKTOK</h5>
                <small class="text-white-50">PT. Aspacindo Kedaton Motor</small>
            </div>
            <div class="text-end">
                <span class="badge bg-danger rounded-pill px-3 py-1.5 mb-1 fs-6">{{ $report->branch->nama_cabang }}</span>
                <div class="small text-white-50" style="font-size: 0.78rem;">{{ $report->tanggal_live->format('d F Y') }}</div>
            </div>
        </div>

        <!-- Body Details -->
        <div class="p-4">
            <!-- Information Grid -->
            <div class="row g-3 mb-3">
                <div class="col-6">
                    <div class="p-3 bg-light rounded-3">
                        <small class="text-muted d-block fw-semibold" style="font-size: 0.72rem;">NAMA HOST (YANG LIVE)</small>
                        <strong class="text-dark fs-6">{{ $report->nama_host }}</strong>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 bg-light rounded-3">
                        <small class="text-muted d-block fw-semibold" style="font-size: 0.72rem;">JABATAN HOST</small>
                        @if($report->jabatan === 'PIC Digital')
                            <span class="badge badge-jabatan-pic rounded-pill px-3 py-1 mt-1">{{ $report->jabatan }}</span>
                        @else
                            <span class="badge badge-jabatan-sales rounded-pill px-3 py-1 mt-1">{{ $report->jabatan }}</span>
                        @endif
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 bg-light rounded-3">
                        <small class="text-muted d-block fw-semibold" style="font-size: 0.72rem;">DURASI LIVE</small>
                        <strong class="text-primary fs-6"><i class="fas fa-clock me-1"></i> {{ $report->formatted_durasi }}</strong>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 bg-light rounded-3">
                        <small class="text-muted d-block fw-semibold" style="font-size: 0.72rem;">DIINPUT OLEH</small>
                        <strong class="text-dark fs-6">{{ $report->user->name }}</strong>
                    </div>
                </div>
            </div>

            <!-- Performance Metrics Row -->
            <div class="row g-2 mb-3 text-center">
                <div class="col-3">
                    <div class="p-2 border rounded-3 bg-light">
                        <small class="text-muted d-block" style="font-size: 0.68rem;"><i class="fas fa-users text-info me-1"></i> PENONTON</small>
                        <strong class="text-dark fs-6">{{ number_format($report->jumlah_penonton) }}</strong>
                    </div>
                </div>
                <div class="col-3">
                    <div class="p-2 border rounded-3 bg-light">
                        <small class="text-muted d-block" style="font-size: 0.68rem;"><i class="fas fa-heart text-danger me-1"></i> LIKES</small>
                        <strong class="text-dark fs-6">{{ number_format($report->jumlah_like) }}</strong>
                    </div>
                </div>
                <div class="col-3">
                    <div class="p-2 border rounded-3 bg-light">
                        <small class="text-muted d-block" style="font-size: 0.68rem;"><i class="fas fa-comment text-primary me-1"></i> KOMENTAR</small>
                        <strong class="text-dark fs-6">{{ number_format($report->jumlah_komentar) }}</strong>
                    </div>
                </div>
                <div class="col-3">
                    <div class="p-2 border rounded-3 bg-light">
                        <small class="text-muted d-block" style="font-size: 0.68rem;"><i class="fas fa-share text-warning me-1"></i> SHARE</small>
                        <strong class="text-dark fs-6">{{ number_format($report->jumlah_share) }}</strong>
                    </div>
                </div>
                <div class="col-12 mt-2">
                    <div class="p-2 bg-success bg-opacity-10 border border-success rounded-3 d-flex justify-content-between align-items-center px-3">
                        <span class="fw-bold text-success small"><i class="fas fa-motorcycle me-1.5"></i> STU (Sales To Unit):</span>
                        <span class="badge bg-success fs-6 px-3 py-1">{{ $report->stu !== null ? number_format($report->stu) . ' Unit' : 'Tidak Ada' }}</span>
                    </div>
                </div>
            </div>

            @if($report->catatan)
            <div class="mb-3">
                <div class="p-3 bg-light rounded-3">
                    <small class="text-muted d-block fw-semibold" style="font-size: 0.72rem;">CATATAN / KETERANGAN</small>
                    <span class="text-dark small">{{ $report->catatan }}</span>
                </div>
            </div>
            @endif

            <!-- Bukti Screenshot -->
            <div class="mb-2">
                <small class="fw-bold text-dark d-block mb-2"><i class="fas fa-camera me-1.5 text-danger"></i> Bukti Screenshot Live TikTok</small>
                <div class="screenshot-container">
                    @if($report->bukti_screenshot)
                        <img id="ssImg" src="{{ asset($report->bukti_screenshot) }}" crossorigin="anonymous" alt="Bukti Screenshot Live">
                    @else
                        <div class="py-4 text-muted small"><i class="fas fa-image fa-2x d-block mb-2 text-secondary"></i> Tidak ada bukti screenshot.</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-light p-3 px-4 border-top d-flex justify-content-between align-items-center small text-muted" style="font-size: 0.75rem;">
            <div>© Yamaha DMPMS - PT Aspacindo Kedaton Motor</div>
            <div>Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>
</div>

<!-- html2canvas Library -->
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script>
    document.getElementById('btnDownloadJpg').addEventListener('click', function() {
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1.5"></i> Meng-generate JPG...';

        const element = document.getElementById('captureTarget');
        html2canvas(element, {
            scale: 2,
            useCORS: true,
            allowTaint: true,
            backgroundColor: '#ffffff'
        }).then(canvas => {
            const link = document.createElement('a');
            link.download = "Laporan_Live_TikTok_{{ $report->branch->kode }}_{{ $report->tanggal_live->format('Y-m-d') }}.jpg";
            link.href = canvas.toDataURL('image/jpeg', 0.95);
            link.click();

            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check me-1.5"></i> Berhasil Diunduh!';
            setTimeout(() => {
                btn.innerHTML = '<i class="fas fa-download me-1.5"></i> Download Gambar JPG';
            }, 3000);
        }).catch(err => {
            console.error(err);
            alert('Gagal mengexport gambar JPG.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-download me-1.5"></i> Download Gambar JPG';
        });
    });

    // Auto-trigger download on page load
    window.addEventListener('load', function() {
        setTimeout(() => {
            document.getElementById('btnDownloadJpg').click();
        }, 500);
    });
</script>
</body>
</html>
