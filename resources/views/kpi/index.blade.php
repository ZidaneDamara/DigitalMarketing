@extends('layouts.app')

@section('title', 'Target KPI Management & Evaluasi PIC Digital')

@push('styles')
<style>
    /* Header KPI Metric Cards */
    .kpi-metric-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.25rem 1.4rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        min-height: 125px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .kpi-metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.07);
    }
    .kpi-metric-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 6px;
        border-radius: 16px 0 0 16px;
    }
    .kpi-metric-card.card-accent-warning::before {
        background: linear-gradient(180deg, #f59e0b, #d97706);
    }
    .kpi-metric-card.card-accent-info::before {
        background: linear-gradient(180deg, #0284c7, #0369a1);
    }
    .kpi-metric-card.card-accent-success::before {
        background: linear-gradient(180deg, #10b981, #059669);
    }
    .kpi-metric-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        padding: 0.25rem 0.65rem;
        border-radius: 6px;
        margin-bottom: 0.5rem;
        line-height: 1.2;
    }
    .kpi-pill-warning {
        background-color: #FEF3C7;
        color: #92400E;
        border: 1px solid #FDE68A;
    }
    .kpi-pill-info {
        background-color: #E0F2FE;
        color: #0369A1;
        border: 1px solid #BAE6FD;
    }
    .kpi-pill-success {
        background-color: #DCFCE7;
        color: #166534;
        border: 1px solid #BBF7D0;
    }
    .kpi-metric-val {
        font-size: 1.65rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        line-height: 1.2;
        margin-bottom: 0.25rem;
    }
    .kpi-metric-sub {
        font-size: 0.75rem;
        color: #64748b;
        line-height: 1.35;
        margin: 0;
    }
    .kpi-metric-icon {
        width: 50px;
        height: 50px;
        min-width: 50px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }
    .kpi-icon-warning {
        background-color: #FEF3C7;
        color: #D97706;
    }
    .kpi-icon-info {
        background-color: #E0F2FE;
        color: #0284C7;
    }
    .kpi-icon-success {
        background-color: #DCFCE7;
        color: #16A34A;
    }
    
    /* Syarat Pembayaran Box */
    .syarat-box {
        background: #fffdf5;
        border: 1px solid #fed7aa;
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 4px 16px rgba(245, 158, 11, 0.05);
    }
    .syarat-item-card {
        background: #ffffff;
        border: 1px solid #fef08a;
        border-radius: 10px;
        padding: 0.85rem 1rem;
        height: 100%;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .syarat-num-badge {
        width: 26px;
        height: 26px;
        min-width: 26px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.78rem;
        font-weight: 800;
    }

    /* Tabs */
    .kpi-nav-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .kpi-nav-pills .nav-link {
        font-weight: 600;
        color: #495057;
        border: 1px solid #dee2e6;
        background-color: #f8f9fa;
        padding: 0.6rem 1.25rem;
        border-radius: 50rem;
        transition: all 0.2s ease-in-out;
    }
    .kpi-nav-pills .nav-link.active {
        background-color: #002266;
        color: #ffffff;
        border-color: #002266;
        box-shadow: 0 4px 10px rgba(0, 34, 102, 0.25);
    }
    .kpi-nav-pills .nav-link#tab-evaluation-tab.active {
        background-color: #ffc107;
        color: #000000;
        border-color: #ffc107;
        box-shadow: 0 4px 10px rgba(255, 193, 7, 0.3);
    }
    @media (max-width: 576px) {
        .kpi-nav-pills .nav-item {
            width: 100%;
        }
        .kpi-nav-pills .nav-link {
            width: 100%;
            text-align: center;
            justify-content: center;
        }
    }

    /* Table & Sheet Styling */
    .table-kpi-sheet thead th {
        background-color: #ffe600 !important;
        color: #000000 !important;
        font-weight: 700;
        text-align: center;
        vertical-align: middle;
        border: 1px solid #d4c000 !important;
        padding: 0.65rem 0.6rem;
    }
    .table-kpi-sheet tbody td {
        vertical-align: middle;
        border: 1px solid #dee2e6;
        padding: 0.6rem 0.65rem;
    }
    .table-kpi-sheet tbody tr.subtotal-row {
        background-color: #000000 !important;
        color: #ffffff !important;
        font-weight: 700;
    }
    .table-kpi-sheet tbody tr.subtotal-row td {
        background-color: #000000 !important;
        color: #ffffff !important;
        border: 1px solid #222 !important;
    }
    .badge-bobot {
        font-size: 0.78rem;
        font-weight: 700;
        padding: 0.35rem 0.65rem;
        border-radius: 0.5rem;
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
    }
    .card-kpi-group {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 0.85rem;
        padding: 1.25rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 2px 6px rgba(0,0,0,0.02);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .card-kpi-group:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fas fa-bullseye text-warning"></i> KPI PIC Digital & Target Cabang
            </h3>
            <p class="text-muted mb-0">Konfigurasi target KPI (Proses & Result), monitoring evaluasi otomatis dari data laporan yang berjalan, dan penentuan kelayakan insentif pokok (Rp 500.000,-).</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @role('Super Admin')
            <button class="btn btn-outline-warning text-dark fw-semibold rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modal-copy-kpi">
                <i class="fas fa-copy me-1"></i> Copy Target Bulan Sebelumnya
            </button>
            @endrole
        </div>
    </div>

    <!-- Filter Periode Bulan / Tahun -->
    <div class="card card-custom p-3 mb-4 border-0 shadow-sm">
        <form action="{{ route('kpis.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-6 col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Tahun Target</label>
                <select name="tahun" class="form-select form-select-sm" onchange="this.form.submit()">
                    @for($y = 2024; $y <= 2028; $y++)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Bulan Target</label>
                <select name="bulan" class="form-select form-select-sm" onchange="this.form.submit()">
                    @php
                        $namaBulanList = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ];
                    @endphp
                    @foreach($namaBulanList as $num => $nama)
                        <option value="{{ $num }}" {{ $bulan == $num ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-6 d-flex align-items-center justify-content-start justify-content-md-end gap-2">
                <div class="p-2 px-3 bg-light rounded-3 border small">
                    <span class="text-muted me-1">Periode Aktif:</span>
                    <strong class="text-primary">{{ $namaBulanList[$bulan] ?? $bulan }} {{ $tahun }}</strong>
                </div>
            </div>
        </form>
    </div>

    <!-- Summary KPI Header Card (Insentif & Aturan Pembayaran) -->
    <div class="row g-3 mb-4">
        <!-- Insentif Pokok Card -->
        <div class="col-12 col-md-4">
            <div class="kpi-metric-card card-accent-warning">
                <div class="d-flex align-items-center justify-content-between gap-2">
                    <div class="flex-grow-1">
                        <span class="kpi-metric-pill kpi-pill-warning">
                            <i class="fas fa-coins me-1"></i> Insentif Pokok PIC Digital
                        </span>
                        <div class="kpi-metric-val text-dark">
                            Rp {{ number_format($target?->insentif_pokok ?? 500000, 0, ',', '.') }}
                        </div>
                        <p class="kpi-metric-sub">Dibayarkan penuh jika kedua syarat terpenuhi</p>
                    </div>
                    <div class="kpi-metric-icon kpi-icon-warning">
                        <i class="fas fa-coins"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Syarat 1: Result >= 60 -->
        <div class="col-12 col-md-4">
            <div class="kpi-metric-card card-accent-info">
                <div class="d-flex align-items-center justify-content-between gap-2">
                    <div class="flex-grow-1">
                        <span class="kpi-metric-pill kpi-pill-info">
                            <i class="fas fa-flag-checkered me-1"></i> Syarat 1 (Gerbang Utama)
                        </span>
                        <div class="kpi-metric-val" style="color: #0284C7;">
                            Result &ge; {{ (int)($target?->min_result_score ?? 60) }} Poin
                        </div>
                        <p class="kpi-metric-sub">Jika &lt; 60, Proses dianggap 0 & insentif Rp 0</p>
                    </div>
                    <div class="kpi-metric-icon kpi-icon-info">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Syarat 2: Proses >= 80 -->
        <div class="col-12 col-md-4">
            <div class="kpi-metric-card card-accent-success">
                <div class="d-flex align-items-center justify-content-between gap-2">
                    <div class="flex-grow-1">
                        <span class="kpi-metric-pill kpi-pill-success">
                            <i class="fas fa-check-circle me-1"></i> Syarat 2 (Kinerja Proses)
                        </span>
                        <div class="kpi-metric-val" style="color: #16A34A;">
                            Proses &ge; {{ (int)($target?->min_proses_score ?? 80) }} Poin
                        </div>
                        <p class="kpi-metric-sub">Dihitung hanya jika Result sudah &ge; 60</p>
                    </div>
                    <div class="kpi-metric-icon kpi-icon-success">
                        <i class="fas fa-tasks"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kotak Resmi: SYARAT PEMBAYARAN KPI (Sesuai Gambar User) -->
    <div class="syarat-box mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <h6 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                <i class="fas fa-file-invoice-dollar text-warning fs-5"></i> SYARAT PEMBAYARAN KPI PIC DIGITAL
            </h6>
            <span class="badge bg-warning bg-opacity-25 text-dark fw-bold px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">Aturan Resmi Insentif</span>
        </div>
        <div class="row g-2.5">
            <div class="col-12 col-lg-6">
                <div class="syarat-item-card">
                    <span class="syarat-num-badge bg-warning text-dark">1</span>
                    <div class="small text-dark">
                        <strong>Result harus tercapai minimal 60 terlebih dahulu.</strong> (Gerbang syarat mutlak).
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="syarat-item-card">
                    <span class="syarat-num-badge bg-warning text-dark">2</span>
                    <div class="small text-dark">
                        <strong>Jika Result &ge; 60, maka Proses baru dihitung</strong>, dengan syarat Proses minimal tercapai <strong>80</strong>.
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="syarat-item-card border-danger border-opacity-25">
                    <span class="syarat-num-badge bg-danger text-white">3</span>
                    <div class="small text-dark">
                        <strong>Jika Result &lt; 60, maka Proses TIDAK dihitung / dianggap 0</strong>, walaupun pencapaian Proses sudah tinggi.
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="syarat-item-card border-success border-opacity-25">
                    <span class="syarat-num-badge bg-success text-white">4</span>
                    <div class="small text-dark">
                        <strong>KPI dibayarkan penuh hanya jika kedua syarat</strong> (Result &ge; 60 dan Proses &ge; 80) terpenuhi.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs: Form Target vs Hasil Evaluasi Otomatis -->
    <ul class="nav nav-pills kpi-nav-pills mb-3 gap-2" id="kpiTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4 py-2" id="tab-evaluation-tab" data-bs-toggle="pill" data-bs-target="#tab-evaluation-pane" type="button" role="tab" aria-selected="true">
                <i class="fas fa-chart-pie me-1.5 text-warning"></i> Hasil Evaluasi Otomatis Cabang
                <span class="badge bg-dark ms-1.5 rounded-pill">{{ count($branchEvaluations) }} Cabang</span>
            </button>
        </li>
        @role('Super Admin')
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 py-2" id="tab-form-tab" data-bs-toggle="pill" data-bs-target="#tab-form-pane" type="button" role="tab" aria-selected="false">
                <i class="fas fa-sliders-h me-1.5 text-primary"></i> Form Pengaturan Target KPI PIC Digital
            </button>
        </li>
        @endrole
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="kpiTabsContent">

        <!-- ================================================================= -->
        <!-- TAB 1: HASIL EVALUASI OTOMATIS CABANG (REAL-TIME LIVE DATA)       -->
        <!-- ================================================================= -->
        <div class="tab-pane fade show active" id="tab-evaluation-pane" role="tabpanel" tabindex="0">
            <div class="card card-custom p-4 border-0 shadow-sm">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3 gap-2">
                    <div>
                        <h5 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                            <i class="fas fa-clipboard-check text-success"></i> Scorecard & Kelayakan Insentif PIC Digital Cabang
                        </h5>
                        <p class="text-muted small mb-0">Dihitung otomatis secara real-time dari data laporan Daily Report, TikTok Live, Meta Ads, Google Rating, dan Monthly Insight.</p>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between d-md-none mb-2 text-muted small">
                    <span class="d-flex align-items-center gap-1"><i class="fas fa-arrows-alt-h text-primary me-1"></i> Geser tabel ke kanan untuk melihat status & aksi</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="tableKpiEvaluation" style="min-width: 900px;">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th>Cabang</th>
                                <th class="text-center" style="width: 150px;">Skor Result<br><small class="text-muted">(Min 60 Poin)</small></th>
                                <th class="text-center" style="width: 150px;">Skor Proses<br><small class="text-muted">(Min 80 Poin)</small></th>
                                <th class="text-center">Syarat Result</th>
                                <th class="text-center">Syarat Proses</th>
                                <th class="text-center">Status Kelayakan</th>
                                <th class="text-center">Insentif Diterima</th>
                                <th class="text-center" style="width: 110px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($branchEvaluations as $idx => $ev)
                                <tr class="{{ $userBranchId == $ev['branch']->id ? 'table-warning' : '' }}">
                                    <td>{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $ev['branch']->nama_cabang }}</div>
                                        <small class="text-muted">{{ $ev['branch']->kode }} &bull; {{ $ev['branch']->area }}</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <span class="fw-extrabold fs-6 {{ $ev['is_result_eligible'] ? 'text-info' : 'text-danger' }}">
                                                {{ $ev['skor_result'] }}
                                            </span>
                                            <small class="text-muted">/ 100</small>
                                        </div>
                                        <div class="progress mt-1" style="height: 5px;">
                                            <div class="progress-bar {{ $ev['is_result_eligible'] ? 'bg-info' : 'bg-danger' }}" style="width: {{ min(100, $ev['skor_result']) }}%;"></div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if(!$ev['is_result_eligible'])
                                            <div class="text-muted font-monospace text-decoration-line-through small">{{ $ev['skor_proses_murni'] }} Poin</div>
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary" title="Hangus karena Result < 60">0.00 (Hangus)</span>
                                        @else
                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                <span class="fw-extrabold fs-6 {{ $ev['is_proses_eligible'] ? 'text-success' : 'text-danger' }}">
                                                    {{ $ev['skor_proses_murni'] }}
                                                </span>
                                                <small class="text-muted">/ 100</small>
                                            </div>
                                            <div class="progress mt-1" style="height: 5px;">
                                                <div class="progress-bar {{ $ev['is_proses_eligible'] ? 'bg-success' : 'bg-danger' }}" style="width: {{ min(100, $ev['skor_proses_murni']) }}%;"></div>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($ev['is_result_eligible'])
                                            <span class="badge bg-success-subtle text-success border border-success px-2 py-1">
                                                <i class="fas fa-check-circle me-1"></i> Lolos (&ge; 60)
                                            </span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger px-2 py-1">
                                                <i class="fas fa-times-circle me-1"></i> Gagal (&lt; 60)
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(!$ev['is_result_eligible'])
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary px-2 py-1">
                                                <i class="fas fa-ban me-1"></i> Tidak Dihitung
                                            </span>
                                        @elseif($ev['is_proses_eligible'])
                                            <span class="badge bg-success-subtle text-success border border-success px-2 py-1">
                                                <i class="fas fa-check-circle me-1"></i> Lolos (&ge; 80)
                                            </span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning px-2 py-1">
                                                <i class="fas fa-exclamation-triangle me-1"></i> Belum Capai 80
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $ev['payment_status_badge'] }} px-2.5 py-1.5 shadow-xs">
                                            {{ $ev['payment_status'] }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="fw-bold {{ $ev['is_full_payment_eligible'] ? 'text-success fs-6' : 'text-muted' }}">
                                            Rp {{ number_format($ev['insentif_diterima'], 0, ',', '.') }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1 btn-view-scorecard" 
                                            data-branch-id="{{ $ev['branch']->id }}"
                                            data-branch-name="{{ $ev['branch']->nama_cabang }}"
                                            data-json="{{ json_encode($ev) }}">
                                            <i class="fas fa-eye me-1"></i> Rincian
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">Belum ada cabang terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- TAB 2: FORM PENGATURAN TARGET KPI PIC DIGITAL                      -->
        <!-- ================================================================= -->
        @role('Super Admin')
        <div class="tab-pane fade" id="tab-form-pane" role="tabpanel" tabindex="0">
            <form action="{{ route('kpis.store') }}" method="POST">
                @csrf
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <input type="hidden" name="bulan" value="{{ $bulan }}">

                <!-- Card 1: Insentif Pokok & Ambang Batas Syarat Pembayaran -->
                <div class="card card-custom p-4 mb-4 border-0 shadow-sm border-top border-4 border-warning">
                    <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                        <i class="fas fa-money-check-alt text-warning"></i> Insentif Pokok & Parameter Ambang Batas
                    </h5>
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold">Insentif Pokok PIC Digital (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold text-primary">Rp</span>
                                <input type="number" name="insentif_pokok" class="form-control form-control-lg fs-6 fw-bold text-dark" value="{{ old('insentif_pokok', $target?->insentif_pokok ?? 500000) }}" required>
                            </div>
                            <small class="text-muted" style="font-size: 0.72rem;">Nominal insentif pokok yang dibayarkan jika lolos penuh.</small>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label small fw-bold">Syarat Minimal Skor Result <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.1" name="min_result_score" class="form-control form-control-lg fs-6 fw-bold text-info" value="{{ old('min_result_score', $target?->min_result_score ?? 60.0) }}" required>
                                <span class="input-group-text bg-light text-muted">Poin</span>
                            </div>
                            <small class="text-muted" style="font-size: 0.72rem;">Gerbang syarat 1: Result harus &ge; 60 agar Proses dihitung.</small>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label small fw-bold">Syarat Minimal Skor Proses <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.1" name="min_proses_score" class="form-control form-control-lg fs-6 fw-bold text-success" value="{{ old('min_proses_score', $target?->min_proses_score ?? 80.0) }}" required>
                                <span class="input-group-text bg-light text-muted">Poin</span>
                            </div>
                            <small class="text-muted" style="font-size: 0.72rem;">Syarat 2: Proses harus &ge; 80 agar insentif cair penuh.</small>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Kategori PROSES (Total Bobot: 100) -->
                <div class="card card-custom p-4 mb-4 border-0 shadow-sm border-top border-4 border-primary">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold text-primary m-0 d-flex align-items-center gap-2">
                            <i class="fas fa-cogs"></i> Kategori: Indikator Proses
                        </h5>
                        <span class="badge bg-primary px-3 py-1.5 fs-7 rounded-pill">Total Bobot: 100 Poin</span>
                    </div>
                    <p class="text-muted small mb-3">Konfigurasi indikator aktivitas harian, jadwal posting, durasi live, dan pengelolaan review Google.</p>

                    <div class="row g-3">
                        <!-- 1. Sosial Media Dealer (Bobot 20) -->
                        <div class="col-12 col-md-6">
                            <div class="card-kpi-group h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="fw-bold text-dark m-0 d-flex align-items-center gap-1.5">
                                        <i class="fab fa-instagram text-danger"></i> 1. Sosial Media Dealer
                                    </h6>
                                    <span class="badge-bobot">Bobot: 20</span>
                                </div>
                                <p class="text-muted small mb-2" style="font-size: 0.75rem;">Penilaian: Mengikuti 100% Content Plan AMD Sosmed</p>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light">Target Kepatuhan Content Plan</span>
                                    <input type="number" step="1" name="sosmed_content_plan_pct" class="form-control" value="{{ old('sosmed_content_plan_pct', $target?->sosmed_content_plan_pct ?? 100) }}" required>
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Sales Digital Posting Focus Type & Promo 3S (Bobot 15) -->
                        <div class="col-12 col-md-6">
                            <div class="card-kpi-group h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="fw-bold text-dark m-0 d-flex align-items-center gap-1.5">
                                        <i class="fas fa-camera text-primary"></i> 2. Sales Digital Posting Focus Type
                                    </h6>
                                    <span class="badge-bobot">Bobot: 15</span>
                                </div>
                                <p class="text-muted small mb-2" style="font-size: 0.75rem;">Penilaian: 2 Postingan/SDM Setiap Hari (Foto/ Video Classy, ATM, Promo 3S)</p>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light">Target Postingan Harian per SDM</span>
                                    <input type="number" step="1" name="sales_digital_posting_target" class="form-control" value="{{ old('sales_digital_posting_target', $target?->sales_digital_posting_target ?? 2) }}" required>
                                    <span class="input-group-text">Post / Hari</span>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Live TikTok (Bobot 25) -->
                        <div class="col-12 col-md-6">
                            <div class="card-kpi-group h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="fw-bold text-dark m-0 d-flex align-items-center gap-1.5">
                                        <i class="fab fa-tiktok text-dark"></i> 3. Live TikTok
                                    </h6>
                                    <span class="badge-bobot">Bobot: 25</span>
                                </div>
                                <p class="text-muted small mb-2" style="font-size: 0.75rem;">Penilaian: Total Live 3 Jam/ SDM Setiap Hari</p>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light">Target Durasi Live Harian</span>
                                    <input type="number" step="0.5" name="tiktok_live_daily_hours" class="form-control" value="{{ old('tiktok_live_daily_hours', $target?->tiktok_live_daily_hours ?? 3.0) }}" required>
                                    <span class="input-group-text">Jam / Hari</span>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Google Business (Bobot 20) -->
                        <div class="col-12 col-md-6">
                            <div class="card-kpi-group h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="fw-bold text-dark m-0 d-flex align-items-center gap-1.5">
                                        <i class="fab fa-google text-success"></i> 4. Google Business
                                    </h6>
                                    <span class="badge-bobot">Bobot: 20</span>
                                </div>
                                <p class="text-muted small mb-2" style="font-size: 0.75rem;">Penilaian: Target 100 review/bulan dan rating min 4.8; Update 4 foto 3S setiap hari</p>
                                <div class="row g-2">
                                    <div class="col-4">
                                        <label class="form-label small fw-semibold text-muted" style="font-size: 0.68rem;">Target Review</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="google_review_target" class="form-control" value="{{ old('google_review_target', $target?->google_review_target ?? 100) }}" required>
                                            <span class="input-group-text">Ulasan</span>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-semibold text-muted" style="font-size: 0.68rem;">Rating Min</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="0.1" name="google_rating_target" class="form-control" value="{{ old('google_rating_target', $target?->google_rating_target ?? 4.8) }}" required>
                                            <span class="input-group-text"><i class="fas fa-star text-warning"></i></span>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-semibold text-muted" style="font-size: 0.68rem;">Foto 3S Harian</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="google_foto_daily_target" class="form-control" value="{{ old('google_foto_daily_target', $target?->google_foto_daily_target ?? 4) }}" required>
                                            <span class="input-group-text">Foto</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 5. Iklan Meta Ads (Bobot 15) -->
                        <div class="col-12 col-md-6">
                            <div class="card-kpi-group h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="fw-bold text-dark m-0 d-flex align-items-center gap-1.5">
                                        <i class="fab fa-facebook text-primary"></i> 5. Iklan Meta Ads
                                    </h6>
                                    <span class="badge-bobot">Bobot: 15</span>
                                </div>
                                <p class="text-muted small mb-2" style="font-size: 0.75rem;">Penilaian: Pasang Meta Ads sebelum tanggal 26 setiap bulan nya</p>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light">Batas Tanggal Pemasangan Iklan</span>
                                    <input type="number" min="1" max="31" name="meta_ads_deadline_day" class="form-control" value="{{ old('meta_ads_deadline_day', $target?->meta_ads_deadline_day ?? 26) }}" required>
                                    <span class="input-group-text">Setiap Bulan</span>
                                </div>
                            </div>
                        </div>

                        <!-- 6. Report & Rapat (Bobot Total 5) -->
                        <div class="col-12 col-md-6">
                            <div class="card-kpi-group h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="fw-bold text-dark m-0 d-flex align-items-center gap-1.5">
                                        <i class="fas fa-users-cog text-secondary"></i> 6. Report & Rapat
                                    </h6>
                                    <span class="badge-bobot">Bobot Total: 5</span>
                                </div>
                                <p class="text-muted small mb-2" style="font-size: 0.75rem;">Penilaian: Mengirimkan Report Harian/Mingguan/Bulanan (Bobot 2) & Rapat Performance Review 2x (Bobot 3)</p>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold text-muted" style="font-size: 0.68rem;">Laporan Lengkap (Bobot 2)</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="report_compliance_pct" class="form-control" value="{{ old('report_compliance_pct', $target?->report_compliance_pct ?? 100) }}" required>
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold text-muted" style="font-size: 0.68rem;">Rapat AMD (Bobot 3)</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="rapat_review_target" class="form-control" value="{{ old('rapat_review_target', $target?->rapat_review_target ?? 2) }}" required>
                                            <span class="input-group-text">Kali</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Kategori RESULT (Total Bobot: 100) -->
                <div class="card card-custom p-4 mb-4 border-0 shadow-sm border-top border-4 border-success">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold text-success m-0 d-flex align-items-center gap-2">
                            <i class="fas fa-trophy"></i> Kategori: Indikator Result
                        </h5>
                        <span class="badge bg-success px-3 py-1.5 fs-7 rounded-pill">Total Bobot: 100 Poin</span>
                    </div>
                    <p class="text-muted small mb-3">Konfigurasi indikator output pertumbuhan follower, prospek calon konsumen, efisiensi metrik iklan, dan kontribusi STU unit.</p>

                    <div class="row g-3">
                        <!-- 1. Growth Akun Instagram dan TikTok (Bobot Total 20) -->
                        <div class="col-12 col-md-6">
                            <div class="card-kpi-group h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="fw-bold text-dark m-0 d-flex align-items-center gap-1.5">
                                        <i class="fas fa-user-plus text-primary"></i> 1. Growth Akun Instagram & TikTok
                                    </h6>
                                    <span class="badge-bobot">Bobot Total: 20</span>
                                </div>
                                <p class="text-muted small mb-2" style="font-size: 0.75rem;">Penilaian: Pertumbuhan 150 follower baru Instagram (Bobot 10) & 150 TikTok (Bobot 10)</p>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold text-muted" style="font-size: 0.68rem;">Instagram (Bobot 10)</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="ig_followers_growth_target" class="form-control" value="{{ old('ig_followers_growth_target', $target?->ig_followers_growth_target ?? 150) }}" required>
                                            <span class="input-group-text">Follower</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold text-muted" style="font-size: 0.68rem;">TikTok (Bobot 10)</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="tiktok_followers_growth_target" class="form-control" value="{{ old('tiktok_followers_growth_target', $target?->tiktok_followers_growth_target ?? 150) }}" required>
                                            <span class="input-group-text">Follower</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Prospect Digital (Bobot 20) -->
                        <div class="col-12 col-md-6">
                            <div class="card-kpi-group h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="fw-bold text-dark m-0 d-flex align-items-center gap-1.5">
                                        <i class="fas fa-user-friends text-info"></i> 2. Prospect Digital
                                    </h6>
                                    <span class="badge-bobot">Bobot: 20</span>
                                </div>
                                <p class="text-muted small mb-2" style="font-size: 0.75rem;">Penilaian: 130% dari Target STU Dealer</p>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold text-muted" style="font-size: 0.68rem;">Rasio Target Prospect</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="1" name="prospect_digital_ratio_pct" class="form-control" value="{{ old('prospect_digital_ratio_pct', $target?->prospect_digital_ratio_pct ?? 130) }}" required>
                                            <span class="input-group-text">% dari STU</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold text-muted" style="font-size: 0.68rem;">Base Target STU Dealer</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="dealer_stu_target" class="form-control" value="{{ old('dealer_stu_target', $target?->dealer_stu_target ?? 50) }}" required>
                                            <span class="input-group-text">Unit</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Performa Iklan (Bobot Total 35) -->
                        <div class="col-12 col-md-6">
                            <div class="card-kpi-group h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="fw-bold text-dark m-0 d-flex align-items-center gap-1.5">
                                        <i class="fas fa-bullseye text-primary"></i> 3. Performa Iklan (Meta Ads)
                                    </h6>
                                    <span class="badge-bobot">Bobot Total: 35</span>
                                </div>
                                <p class="text-muted small mb-2" style="font-size: 0.75rem;">Penilaian: CPM maks Rp 30.000 (10), CPC maks Rp 5.000 (10), CTR min 1.5% (15)</p>
                                <div class="row g-2">
                                    <div class="col-4">
                                        <label class="form-label small fw-semibold text-muted" style="font-size: 0.68rem;">CPM Maks (10)</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" step="100" name="ads_cpm_max" class="form-control" value="{{ old('ads_cpm_max', $target?->ads_cpm_max ?? 30000) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-semibold text-muted" style="font-size: 0.68rem;">CPC Maks (10)</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" step="50" name="ads_cpc_max" class="form-control" value="{{ old('ads_cpc_max', $target?->ads_cpc_max ?? 5000) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small fw-semibold text-muted" style="font-size: 0.68rem;">CTR Min (15)</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="0.1" name="ads_ctr_min" class="form-control" value="{{ old('ads_ctr_min', $target?->ads_ctr_min ?? 1.5) }}" required>
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 4. STU Team Digital (Bobot 25) -->
                        <div class="col-12 col-md-6">
                            <div class="card-kpi-group h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="fw-bold text-dark m-0 d-flex align-items-center gap-1.5">
                                        <i class="fas fa-motorcycle text-danger"></i> 4. STU Team Digital
                                    </h6>
                                    <span class="badge-bobot">Bobot: 25</span>
                                </div>
                                <p class="text-muted small mb-2" style="font-size: 0.75rem;">Penilaian: Capai Minimal STU 15% dari target dealer</p>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light">Target STU Digital (% Dealer)</span>
                                    <input type="number" step="0.5" name="stu_digital_ratio_pct" class="form-control" value="{{ old('stu_digital_ratio_pct', $target?->stu_digital_ratio_pct ?? 15.0) }}" required>
                                    <span class="input-group-text">% dari Target Dealer</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end mb-4">
                    <button type="submit" class="btn btn-yamaha-primary px-5 py-2.5 fs-6 shadow-sm">
                        <i class="fas fa-save me-2"></i> SIMPAN TARGET KPI PIC DIGITAL ({{ $namaBulanList[$bulan] ?? $bulan }} {{ $tahun }})
                    </button>
                </div>
            </form>
        </div>
        @endrole
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL RINCIAN SCORECARD CABANG (EXACT MATCH USER SPREADSHEET)             -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalScorecardDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem; overflow: hidden;">
            <div class="modal-header bg-dark text-white">
                <div>
                    <h5 class="modal-title fw-bold m-0" id="scorecardModalTitle">
                        <i class="fas fa-table text-warning me-2"></i> Rincian Lembar Penilaian KPI PIC Digital
                    </h5>
                    <small class="text-white-50" id="scorecardModalSubtitle">Cabang &bull; Periode</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light custom-modal-scroll" id="scorecardModalBody">
                <!-- Dynamically populated via JS -->
            </div>
            <div class="modal-footer bg-white border-top">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Copy KPI -->
<div class="modal fade" id="modal-copy-kpi" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-warning text-dark rounded-top-4">
                <h5 class="fw-bold m-0"><i class="fas fa-copy me-2"></i> Copy Target KPI Bulan Sebelumnya</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('kpis.copy') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted small">Salin konfigurasi target KPI PIC Digital dari bulan asal ke bulan tujuan secara instan:</p>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Bulan Asal</label>
                            <select name="from_bulan" class="form-select" required>
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ ($bulan == 1 ? 12 : $bulan - 1) == $m ? 'selected' : '' }}>{{ $namaBulanList[$m] ?? $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Tahun Asal</label>
                            <input type="number" name="from_tahun" class="form-control" value="{{ $bulan == 1 ? $tahun - 1 : $tahun }}" required>
                        </div>
                    </div>

                    <div class="text-center my-2"><i class="fas fa-arrow-down text-warning fa-2x"></i></div>

                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Bulan Tujuan</label>
                            <select name="to_bulan" class="form-select" required>
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>{{ $namaBulanList[$m] ?? $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Tahun Tujuan</label>
                            <input type="number" name="to_tahun" class="form-control" value="{{ $tahun }}" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">Proses Copy KPI</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Scorecard Detail Modal Click
    $('.btn-view-scorecard').on('click', function() {
        const branchName = $(this).data('branch-name');
        const data = $(this).data('json');

        $('#scorecardModalTitle').html('<i class="fas fa-award text-warning me-2"></i> KPI PIC DIGITAL - ' + branchName);
        $('#scorecardModalSubtitle').text('Periode: ' + '{{ $namaBulanList[$bulan] ?? $bulan }}' + ' ' + '{{ $tahun }}' + ' | Insentif Pokok: Rp ' + Number(data.insentif_pokok).toLocaleString('id-ID'));

        let prosesRowsHtml = '';
        data.proses_items.forEach(function(item, index) {
            prosesRowsHtml += `
                <tr>
                    ${index === 0 ? '<td rowspan="' + data.proses_items.length + '" class="fw-bold text-center bg-light align-middle" style="width: 100px;">Proses</td>' : ''}
                    <td class="fw-semibold">${item.item}</td>
                    <td>${item.penilaian}</td>
                    <td class="text-center small">${item.target_display}</td>
                    <td class="text-center small fw-semibold text-primary">${item.realisasi_display}</td>
                    <td class="text-center fw-bold">${item.bobot}</td>
                    <td class="text-center fw-bold ${item.is_pass ? 'text-success' : 'text-danger'}">${item.skor}</td>
                </tr>
            `;
        });

        let resultRowsHtml = '';
        data.result_items.forEach(function(item, index) {
            resultRowsHtml += `
                <tr>
                    ${index === 0 ? '<td rowspan="' + data.result_items.length + '" class="fw-bold text-center bg-light align-middle" style="width: 100px;">Result</td>' : ''}
                    <td class="fw-semibold">${item.item}</td>
                    <td>${item.penilaian}</td>
                    <td class="text-center small">${item.target_display}</td>
                    <td class="text-center small fw-semibold text-primary">${item.realisasi_display}</td>
                    <td class="text-center fw-bold">${item.bobot}</td>
                    <td class="text-center fw-bold ${item.is_pass ? 'text-success' : 'text-danger'}">${item.skor}</td>
                </tr>
            `;
        });

        const html = `
            <!-- SPREADSHEET TABLE SHEET -->
            <div class="d-flex align-items-center justify-content-between d-md-none mb-2 text-muted small">
                <span class="d-flex align-items-center gap-1"><i class="fas fa-arrows-alt-h text-warning me-1"></i> Geser tabel ke samping untuk rincian lengkap</span>
            </div>
            <div class="table-responsive bg-white rounded-3 shadow-sm border mb-4">
                <table class="table table-bordered table-kpi-sheet m-0" style="min-width: 820px;">
                    <thead>
                        <tr>
                            <th colspan="7" class="py-2.5 fs-5 text-uppercase">
                                KPI PIC DIGITAL &bull; Insentif Pokok: Rp ${Number(data.insentif_pokok).toLocaleString('id-ID')}
                            </th>
                        </tr>
                        <tr>
                            <th style="width: 90px;">Kategori</th>
                            <th style="width: 220px;">Item</th>
                            <th>Penilaian</th>
                            <th style="width: 160px;">Target</th>
                            <th style="width: 180px;">Realisasi</th>
                            <th style="width: 80px;">Bobot KPI</th>
                            <th style="width: 90px;">Skor Didapat</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${prosesRowsHtml}
                        <tr class="subtotal-row">
                            <td colspan="5" class="text-uppercase text-end pe-3">Total Subtotal Proses</td>
                            <td class="text-center">100</td>
                            <td class="text-center">${data.skor_proses_murni}</td>
                        </tr>
                        ${resultRowsHtml}
                        <tr class="subtotal-row">
                            <td colspan="5" class="text-uppercase text-end pe-3">Total Subtotal Result</td>
                            <td class="text-center">100</td>
                            <td class="text-center">${data.skor_result}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- PAYMENT STATUS CARD -->
            <div class="card border-0 shadow-sm p-4 ${data.is_full_payment_eligible ? 'bg-success text-white' : 'bg-white border-start border-4 border-danger'}">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div>
                        <div class="small text-uppercase fw-bold ${data.is_full_payment_eligible ? 'text-white-50' : 'text-danger'}">
                            Keputusan Kelayakan Pembayaran KPI
                        </div>
                        <h4 class="fw-extrabold m-0 mt-1">
                            ${data.payment_status}
                        </h4>
                        <p class="m-0 mt-1 small ${data.is_full_payment_eligible ? 'text-white' : 'text-muted'}">
                            ${data.payment_message}
                        </p>
                    </div>
                    <div class="text-md-end p-3 rounded-3 ${data.is_full_payment_eligible ? 'bg-black bg-opacity-25' : 'bg-light border'}">
                        <div class="small fw-semibold ${data.is_full_payment_eligible ? 'text-white-50' : 'text-muted'}">Hak Insentif Cair</div>
                        <h3 class="fw-extrabold m-0 ${data.is_full_payment_eligible ? 'text-warning' : 'text-danger'}">
                            Rp ${Number(data.insentif_diterima).toLocaleString('id-ID')}
                        </h3>
                    </div>
                </div>

                <hr class="${data.is_full_payment_eligible ? 'border-white-50' : ''} my-3">

                <!-- 4 RULES VERIFICATION STATUS -->
                <div class="row g-2 small">
                    <div class="col-12 col-md-6">
                        <i class="fas ${data.is_result_eligible ? 'fa-check-circle text-success' : 'fa-times-circle text-danger'} me-1"></i>
                        Syarat 1 (Result &ge; ${data.min_result_score}): <strong>${data.skor_result} Poin (${data.is_result_eligible ? 'Lolos' : 'Gagal'})</strong>
                    </div>
                    <div class="col-12 col-md-6">
                        <i class="fas ${data.is_proses_eligible ? 'fa-check-circle text-success' : 'fa-times-circle text-danger'} me-1"></i>
                        Syarat 2 (Proses &ge; ${data.min_proses_score}): <strong>${data.skor_proses_efektif} Poin (${data.is_proses_eligible ? 'Lolos' : 'Belum Memenuhi'})</strong>
                    </div>
                    <div class="col-12 col-md-6">
                        <i class="fas ${data.is_result_eligible ? 'fa-check-circle text-success' : 'fa-times-circle text-danger'} me-1"></i>
                        Syarat 3 (Status Skor Proses): <strong>${data.is_result_eligible ? 'Dihitung Penuh (' + data.skor_proses_murni + ')' : 'Hangus / Dianggap 0'}</strong>
                    </div>
                    <div class="col-12 col-md-6">
                        <i class="fas ${data.is_full_payment_eligible ? 'fa-check-circle text-success' : 'fa-times-circle text-danger'} me-1"></i>
                        Syarat 4 (Pembayaran Penuh Rp ${Number(data.insentif_pokok).toLocaleString('id-ID')}): <strong>${data.is_full_payment_eligible ? 'Berhak Dicairkan' : 'Gugur'}</strong>
                    </div>
                </div>
            </div>
        `;

        $('#scorecardModalBody').html(html);
        $('#modalScorecardDetail').modal('show');
    });
});
</script>
@endpush
