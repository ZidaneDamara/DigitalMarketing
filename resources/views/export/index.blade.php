@extends('layouts.app')

@section('title', 'Export Center (PDF & Excel)')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="mb-4">
        <h3 class="fw-bold text-dark mb-1"><i class="fas fa-file-export text-secondary me-2"></i>Export Center Laporan Digital</h3>
        <p class="text-muted mb-0">Unduh dokumen laporan performa digital harian, mingguan (post insight), dan bulanan dengan filter tanggal, cabang, dan periode dalam format PDF atau Excel (CSV).</p>
    </div>

    <div class="row g-4">
        <!-- Export PDF Card -->
        <div class="col-12 col-lg-6">
            <div class="card card-custom p-4 h-100 border-top border-4 border-danger shadow-sm">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="icon-box bg-danger bg-opacity-10 text-danger rounded-circle p-3">
                        <i class="fas fa-file-pdf fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark m-0">Export Dokumen PDF</h5>
                        <div class="text-muted small">Cetak laporan visual eksekutif siap cetak & presentasi</div>
                    </div>
                </div>

                <form action="{{ route('exports.pdf') }}" method="GET" target="_blank">
                    <!-- Jenis Laporan -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Pilih Jenis Laporan <span class="text-danger">*</span></label>
                        <select name="report_type" id="pdf_report_type" class="form-select rounded-3 shadow-none report-type-select" data-target="pdf" required>
                            <option value="daily" selected>Laporan Harian (Daily Report)</option>
                            <option value="tiktok_live">Laporan Live TikTok</option>
                            <option value="weekly">Laporan Mingguan (Post Insight)</option>
                            <option value="monthly">Laporan Bulanan (Monthly Insight)</option>
                        </select>
                    </div>

                    <!-- Pilih Cabang -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Pilih Cabang</label>
                        <select name="branch_id" class="form-select rounded-3 shadow-none">
                            <option value="">-- Semua Cabang --</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}">{{ $b->nama_cabang }} ({{ $b->kode }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Harian (Date Specific & Date Range) -->
                    <div id="pdf_daily_filters" class="filter-group mb-3">
                        <div class="p-3 bg-light rounded-3 border mb-3">
                            <label class="form-label fw-semibold small text-primary mb-1"><i class="fas fa-calendar-day me-1"></i> Filter Tanggal Harian Spesifik (Opsional)</label>
                            <input type="date" name="tanggal" class="form-control rounded-3 shadow-none mb-2" placeholder="Pilih Tanggal Spesifik">
                            <span class="text-muted small d-block" style="font-size: 0.78rem;">Kosongkan jika ingin memilih rentang tanggal atau bulanan di bawah.</span>
                        </div>

                        <div class="p-3 bg-light rounded-3 border">
                            <label class="form-label fw-semibold small text-primary mb-1"><i class="fas fa-calendar-week me-1"></i> Atau Rentang Tanggal Harian (Opsional)</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small text-muted">Tanggal Awal</label>
                                    <input type="date" name="tanggal_awal" class="form-control rounded-3 shadow-none">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small text-muted">Tanggal Akhir</label>
                                    <input type="date" name="tanggal_akhir" class="form-control rounded-3 shadow-none">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Mingguan (Minggu Ke & Rentang Tanggal) -->
                    <div id="pdf_weekly_filters" class="filter-group mb-3 d-none">
                        <div class="p-3 bg-light rounded-3 border mb-3">
                            <label class="form-label fw-semibold small text-warning mb-1"><i class="fas fa-list-ol me-1"></i> Minggu Ke (Opsional)</label>
                            <input type="number" name="minggu_ke" class="form-control rounded-3 shadow-none" placeholder="Contoh: 30" min="1" max="53">
                        </div>
                    </div>

                    <!-- Filter Bulan & Tahun (Default/Fallback) -->
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Tahun</label>
                            <select name="tahun" class="form-select rounded-3 shadow-none">
                                @for($y = 2024; $y <= 2028; $y++)
                                    <option value="{{ $y }}" {{ now()->year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-6" id="pdf_bulan_wrapper">
                            <label class="form-label fw-semibold small">Bulan</label>
                            <select name="bulan" class="form-select rounded-3 shadow-none">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ now()->month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-danger w-100 rounded-pill py-2.5 fw-bold shadow-sm">
                        <i class="fas fa-file-pdf me-2"></i> DOWNLOAD PDF REPORT
                    </button>
                </form>
            </div>
        </div>

        <!-- Export Excel Card -->
        <div class="col-12 col-lg-6">
            <div class="card card-custom p-4 h-100 border-top border-4 border-success shadow-sm">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="icon-box bg-success bg-opacity-10 text-success rounded-circle p-3">
                        <i class="fas fa-file-excel fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark m-0">Export Dokumen Excel (.XLSX)</h5>
                        <div class="text-muted small">Unduh spreadsheet eksekutif terformat rapi (Header Banner, Metric Cards, Hyperlink, & Total) untuk Microsoft Excel</div>
                    </div>
                </div>

                <form action="{{ route('exports.excel') }}" method="GET">
                    <!-- Jenis Laporan -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Pilih Jenis Laporan <span class="text-danger">*</span></label>
                        <select name="report_type" id="excel_report_type" class="form-select rounded-3 shadow-none report-type-select" data-target="excel" required>
                            <option value="daily" selected>Laporan Harian (Daily Report)</option>
                            <option value="tiktok_live">Laporan Live TikTok</option>
                            <option value="weekly">Laporan Mingguan (Post Insight)</option>
                            <option value="monthly">Laporan Bulanan (Monthly Insight)</option>
                        </select>
                    </div>

                    <!-- Pilih Cabang -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Pilih Cabang</label>
                        <select name="branch_id" class="form-select rounded-3 shadow-none">
                            <option value="">-- Semua Cabang --</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}">{{ $b->nama_cabang }} ({{ $b->kode }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Harian (Date Specific & Date Range) -->
                    <div id="excel_daily_filters" class="filter-group mb-3">
                        <div class="p-3 bg-light rounded-3 border mb-3">
                            <label class="form-label fw-semibold small text-success mb-1"><i class="fas fa-calendar-day me-1"></i> Filter Tanggal Harian Spesifik (Opsional)</label>
                            <input type="date" name="tanggal" class="form-control rounded-3 shadow-none mb-2" placeholder="Pilih Tanggal Spesifik">
                            <span class="text-muted small d-block" style="font-size: 0.78rem;">Kosongkan jika ingin memilih rentang tanggal atau bulanan di bawah.</span>
                        </div>

                        <div class="p-3 bg-light rounded-3 border">
                            <label class="form-label fw-semibold small text-success mb-1"><i class="fas fa-calendar-week me-1"></i> Atau Rentang Tanggal Harian (Opsional)</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small text-muted">Tanggal Awal</label>
                                    <input type="date" name="tanggal_awal" class="form-control rounded-3 shadow-none">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small text-muted">Tanggal Akhir</label>
                                    <input type="date" name="tanggal_akhir" class="form-control rounded-3 shadow-none">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Mingguan (Minggu Ke & Rentang Tanggal) -->
                    <div id="excel_weekly_filters" class="filter-group mb-3 d-none">
                        <div class="p-3 bg-light rounded-3 border mb-3">
                            <label class="form-label fw-semibold small text-warning mb-1"><i class="fas fa-list-ol me-1"></i> Minggu Ke (Opsional)</label>
                            <input type="number" name="minggu_ke" class="form-control rounded-3 shadow-none" placeholder="Contoh: 30" min="1" max="53">
                        </div>
                    </div>

                    <!-- Filter Bulan & Tahun -->
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Tahun</label>
                            <select name="tahun" class="form-select rounded-3 shadow-none">
                                @for($y = 2024; $y <= 2028; $y++)
                                    <option value="{{ $y }}" {{ now()->year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-6" id="excel_bulan_wrapper">
                            <label class="form-label fw-semibold small">Bulan</label>
                            <select name="bulan" class="form-select rounded-3 shadow-none">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ now()->month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100 rounded-pill py-2.5 fw-bold shadow-sm">
                        <i class="fas fa-file-excel me-2"></i> DOWNLOAD EXCEL (CSV)
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.report-type-select').on('change', function() {
            const target = $(this).data('target');
            const val = $(this).val();

            if (val === 'daily' || val === 'tiktok_live') {
                $(`#${target}_daily_filters`).removeClass('d-none');
                $(`#${target}_weekly_filters`).addClass('d-none');
            } else if (val === 'weekly') {
                $(`#${target}_daily_filters`).addClass('d-none');
                $(`#${target}_weekly_filters`).removeClass('d-none');
            } else {
                $(`#${target}_daily_filters`).addClass('d-none');
                $(`#${target}_weekly_filters`).addClass('d-none');
            }
        });
    });
</script>
@endpush
