@extends('layouts.app')

@section('title', 'Monthly Report Meta Ads & Database Leads')

@push('styles')
<style>
    .badge-sumber-instagram {
        background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
        color: #fff;
    }
    .badge-sumber-facebook {
        background-color: #1877f2;
        color: #fff;
    }
    .badge-sumber-whatsapp {
        background-color: #25d366;
        color: #fff;
    }
    .meta-nav-pills .nav-link {
        font-weight: 600;
        color: #495057;
        border: 1px solid #dee2e6;
        background-color: #f8f9fa;
        transition: all 0.2s ease-in-out;
    }
    .meta-nav-pills .nav-link.active {
        background-color: #002266;
        color: #ffffff;
        border-color: #002266;
        box-shadow: 0 4px 10px rgba(0, 34, 102, 0.25);
    }
    .meta-nav-pills .nav-link#tab-prospects-tab.active {
        background-color: #198754;
        color: #ffffff;
        border-color: #198754;
        box-shadow: 0 4px 10px rgba(25, 135, 84, 0.25);
    }
    .card-section-form {
        background-color: #fdfdfd;
        border: 1px solid #e9ecef;
        border-radius: 0.75rem;
        padding: 1.25rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .card-section-title {
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
        color: #002266;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        border-bottom: 1px solid #edf2f7;
        padding-bottom: 0.5rem;
    }
    .pdf-upload-box {
        border: 2px dashed #dc3545;
        border-radius: 0.5rem;
        background-color: #fff5f5;
        padding: 1rem;
        transition: all 0.2s;
    }
    .pdf-upload-box:hover {
        background-color: #ffebeb;
    }
    /* Scrollable Modal Support for Laptop/Desktop */
    .modal-dialog-scrollable {
        max-height: calc(100vh - 2rem);
    }
    .modal-dialog-scrollable .modal-content {
        max-height: calc(100vh - 2.5rem) !important;
        display: flex !important;
        flex-direction: column !important;
        overflow: hidden !important;
    }
    .modal-dialog-scrollable .modal-body,
    .custom-modal-scroll {
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch;
        flex: 1 1 auto;
        scrollbar-width: thin;
        scrollbar-color: #002266 #f1f1f1;
    }
    .custom-modal-scroll::-webkit-scrollbar {
        width: 8px;
    }
    .custom-modal-scroll::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    .custom-modal-scroll::-webkit-scrollbar-thumb {
        background: #002266;
        border-radius: 4px;
    }
    .custom-modal-scroll::-webkit-scrollbar-thumb:hover {
        background: #001a4d;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fab fa-facebook text-primary"></i> Monthly Report Meta Ads (Facebook & Instagram)
            </h3>
            <p class="text-muted mb-0">Laporan performa kampanye iklan bulanan, metrik efisiensi, invoice resmi top up (PDF), dokumentasi closing STU, dan database prospek leads.</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('dashboard.meta-ads') }}" class="btn btn-outline-primary rounded-pill px-3 shadow-sm">
                <i class="fas fa-chart-pie me-1.5"></i> Dashboard Analytics
            </a>
            <!-- Button Input Prospek (Form Terpisah) -->
            <button type="button" class="btn btn-success rounded-pill px-3 shadow-sm" id="btnTambahProspect">
                <i class="fas fa-user-plus me-1.5"></i> Input Data Prospek
            </button>
            <!-- Button Input Laporan Bulanan Meta Ads -->
            <button type="button" class="btn btn-yamaha-primary rounded-pill px-4 shadow-sm" id="btnTambahReport">
                <i class="fas fa-file-invoice me-1.5"></i> Input Laporan Meta Ads
            </button>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="row g-3 mb-4">
        <!-- Budget Spend -->
        <div class="col-6 col-sm-6 col-xl-2">
            <div class="card card-custom p-3 border-0 bg-white shadow-sm h-100 border-start border-4 border-primary">
                <div class="d-flex align-items-center gap-2">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-3 p-2" style="width: 40px; height: 40px; font-size: 1.1rem; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div>
                        <div class="text-muted fw-semibold text-uppercase" style="font-size: 0.65rem;">Budget Spend</div>
                        <h6 class="fw-extrabold text-primary m-0" style="font-size: 0.95rem;">Rp {{ number_format($totalBudget, 0, ',', '.') }}</h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Views -->
        <div class="col-6 col-sm-6 col-xl-2">
            <div class="card card-custom p-3 border-0 bg-white shadow-sm h-100">
                <div class="d-flex align-items-center gap-2">
                    <div class="icon-box bg-info bg-opacity-10 text-info rounded-3 p-2" style="width: 40px; height: 40px; font-size: 1.1rem; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div>
                        <div class="text-muted fw-semibold text-uppercase" style="font-size: 0.65rem;">Total Views</div>
                        <h6 class="fw-extrabold text-dark m-0" style="font-size: 0.95rem;">{{ number_format($totalViews) }}</h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- Percakapan -->
        <div class="col-6 col-sm-6 col-xl-2">
            <div class="card card-custom p-3 border-0 bg-white shadow-sm h-100">
                <div class="d-flex align-items-center gap-2">
                    <div class="icon-box bg-success bg-opacity-10 text-success rounded-3 p-2" style="width: 40px; height: 40px; font-size: 1.1rem; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div>
                        <div class="text-muted fw-semibold text-uppercase" style="font-size: 0.65rem;">Total Percakapan</div>
                        <h6 class="fw-extrabold text-success m-0" style="font-size: 0.95rem;">{{ number_format($totalPercakapan) }} Chat</h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- STU Closing -->
        <div class="col-6 col-sm-6 col-xl-2">
            <div class="card card-custom p-3 border-0 bg-white shadow-sm h-100 border-start border-4 border-danger">
                <div class="d-flex align-items-center gap-2">
                    <div class="icon-box bg-danger bg-opacity-10 text-danger rounded-3 p-2" style="width: 40px; height: 40px; font-size: 1.1rem; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-motorcycle"></i>
                    </div>
                    <div>
                        <div class="text-muted fw-semibold text-uppercase" style="font-size: 0.65rem;">STU (Closing)</div>
                        <h6 class="fw-extrabold text-danger m-0" style="font-size: 0.95rem;">{{ number_format($totalStu) }} Unit</h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rata-rata CTR -->
        <div class="col-6 col-sm-6 col-xl-2">
            <div class="card card-custom p-3 border-0 bg-white shadow-sm h-100">
                <div class="d-flex align-items-center gap-2">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning rounded-3 p-2" style="width: 40px; height: 40px; font-size: 1.1rem; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div>
                        <div class="text-muted fw-semibold text-uppercase" style="font-size: 0.65rem;">Rata-rata CTR</div>
                        <h6 class="fw-extrabold text-dark m-0" style="font-size: 0.95rem;">{{ number_format($avgCtr, 2) }}%</h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- Database Leads / Prospek Summary -->
        <div class="col-6 col-sm-6 col-xl-2">
            <div class="card card-custom p-3 border-0 bg-white shadow-sm h-100 border-start border-4 border-success">
                <div class="d-flex align-items-center gap-2">
                    <div class="icon-box bg-success bg-opacity-10 text-success rounded-3 p-2" style="width: 40px; height: 40px; font-size: 1.1rem; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <div>
                        <div class="text-muted fw-semibold text-uppercase" style="font-size: 0.65rem;">Database Leads</div>
                        <h6 class="fw-extrabold text-success m-0" style="font-size: 0.95rem;">{{ number_format($totalProspects) }} Prospek</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leads by Platform Badge Bar -->
    <div class="card card-custom px-3 py-2.5 mb-4 border-0 shadow-sm bg-white">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="small fw-bold text-muted text-uppercase" style="font-size: 0.72rem;">
                    <i class="fas fa-filter text-primary me-1"></i> Rincian Sumber Leads:
                </span>
                <span class="badge badge-sumber-instagram px-2.5 py-1.5 rounded-pill shadow-xs">
                    <i class="fab fa-instagram me-1"></i> Instagram: <strong>{{ number_format($igProspects) }}</strong>
                </span>
                <span class="badge badge-sumber-facebook px-2.5 py-1.5 rounded-pill shadow-xs">
                    <i class="fab fa-facebook me-1"></i> Facebook: <strong>{{ number_format($fbProspects) }}</strong>
                </span>
                <span class="badge badge-sumber-whatsapp px-2.5 py-1.5 rounded-pill shadow-xs">
                    <i class="fab fa-whatsapp me-1"></i> WhatsApp: <strong>{{ number_format($waProspects) }}</strong>
                </span>
            </div>
            <div class="small text-muted">
                <i class="fas fa-info-circle me-1 text-info"></i> Klik tab <strong>Database Prospek</strong> di bawah untuk follow-up langsung via WhatsApp.
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card card-custom p-3 mb-4 border-0 shadow-sm">
        <form id="filterForm" class="row g-2 align-items-end">
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold text-muted mb-1">Tahun</label>
                <select name="tahun" id="filter_tahun" class="form-select form-select-sm">
                    @for($y = 2024; $y <= 2028; $y++)
                        <option value="{{ $y }}" {{ $activeTahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold text-muted mb-1">Bulan</label>
                <select name="bulan" id="filter_bulan" class="form-select form-select-sm">
                    <option value="">Semua Bulan</option>
                    @php
                        $namaBulanList = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ];
                    @endphp
                    @foreach($namaBulanList as $num => $nama)
                        <option value="{{ $num }}" {{ $activeBulan == $num ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            @unless(auth()->user()->hasRole('PIC Digital Cabang'))
            <div class="col-12 col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Cabang</label>
                <select name="branch_id" id="filter_branch_id" class="form-select form-select-sm">
                    <option value="">-- Semua Cabang --</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}">{{ $b->nama_cabang }}</option>
                    @endforeach
                </select>
            </div>
            @endunless
            <div class="col-8 col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Cari Produk / Iklan</label>
                <input type="text" name="product" id="filter_product" class="form-control form-control-sm" placeholder="Contoh: NMAX Turbo, Aerox...">
            </div>
            <div class="col-4 col-md-2">
                <button type="button" class="btn btn-sm btn-yamaha-primary w-100" id="btnApplyFilter">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Navigation Tabs: Laporan Meta Ads vs Database Prospek -->
    <ul class="nav nav-pills meta-nav-pills mb-3 gap-2" id="metaTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4 py-2" id="tab-reports-tab" data-bs-toggle="pill" data-bs-target="#tab-reports-pane" type="button" role="tab" aria-selected="true">
                <i class="fab fa-facebook me-1.5 text-primary"></i> Laporan Bulanan Meta Ads
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 py-2 position-relative" id="tab-prospects-tab" data-bs-toggle="pill" data-bs-target="#tab-prospects-pane" type="button" role="tab" aria-selected="false">
                <i class="fab fa-whatsapp me-1.5 text-success"></i> Database Prospek (Leads)
                <span class="badge bg-success ms-1.5 rounded-pill" id="badge_total_prospects">{{ $totalProspects }}</span>
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="metaTabsContent">
        <!-- TAB 1: LAPORAN BULANAN META ADS -->
        <div class="tab-pane fade show active" id="tab-reports-pane" role="tabpanel" tabindex="0">
            <div class="card card-custom p-4 border-0 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                        <i class="fas fa-table text-primary"></i> Data Laporan Kampanye Meta Ads
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="$('#btnTambahReport').click()">
                        <i class="fas fa-plus me-1"></i> Tambah Laporan
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="metaAdsTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th>Cabang</th>
                                <th>Periode</th>
                                <th>Nama Iklan & Produk</th>
                                <th>Budget Spend</th>
                                <th>Total Views</th>
                                <th>Percakapan</th>
                                <th>CTR</th>
                                <th>CPC</th>
                                <th>CPM</th>
                                <th>CPL</th>
                                <th>STU Closing</th>
                                <th>Invoice Top Up (PDF)</th>
                                <th>Bukti Closing STU</th>
                                <th style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 2: DATABASE PROSPEK (LEADS META ADS) -->
        <div class="tab-pane fade" id="tab-prospects-pane" role="tabpanel" tabindex="0">
            <div class="card card-custom p-4 border-0 shadow-sm">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3 gap-2">
                    <div>
                        <h5 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                            <i class="fas fa-user-friends text-success"></i> Database Calon Konsumen (Leads Meta Ads)
                        </h5>
                        <p class="text-muted small mb-0">Daftar calon konsumen yang masuk dari iklan Meta Ads (Instagram, Facebook, WhatsApp). Klik tombol WhatsApp untuk chat instan.</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <!-- Quick filter sumber leads -->
                        <select id="filter_sumber_leads" class="form-select form-select-sm" style="width: 170px;">
                            <option value="">Semua Sumber Leads</option>
                            <option value="Instagram">Instagram</option>
                            <option value="Facebook">Facebook</option>
                            <option value="WhatsApp">WhatsApp</option>
                        </select>
                        <button type="button" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm" onclick="$('#btnTambahProspect').click()">
                            <i class="fas fa-user-plus me-1"></i> Input Prospek Baru
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="prospectTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th>Cabang</th>
                                <th>Tanggal Input</th>
                                <th>Nama Calon Konsumen</th>
                                <th>Nomor HP / WhatsApp</th>
                                <th>Sumber Leads</th>
                                <th>Status Follow Up</th>
                                <th>Catatan / Kebutuhan</th>
                                <th style="width: 60px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 1: FORM TERPISAH INPUT DATA PROSPEK META ADS                         -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalProspect" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form id="formProspect" class="modal-content border-0 shadow-lg" style="border-radius: 1rem; overflow: hidden;">
            @csrf
            <div class="modal-header text-white flex-shrink-0" style="background: linear-gradient(135deg, #0d5930 0%, #198754 100%);">
                <h5 class="modal-title fw-bold">
                    <i class="fab fa-whatsapp text-warning me-2 fs-5"></i> Input Data Prospek Meta Ads
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 custom-modal-scroll">
                <div class="alert alert-success border-0 bg-success bg-opacity-10 py-2.5 px-3 rounded-3 small mb-3 text-success-emphasis">
                    <i class="fas fa-info-circle me-1"></i> Masukkan data prospek baru yang didapat dari iklan Meta Ads untuk follow up tim penjualan.
                </div>

                <!-- Branch Selector -->
                @if(auth()->user()->hasRole('PIC Digital Cabang'))
                    <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id }}">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Cabang Pelapor</label>
                        <div class="border rounded-3 bg-white p-2 px-3 d-flex align-items-center justify-content-between shadow-xs" style="min-height: 48px;">
                            <div class="d-flex align-items-center gap-2">
                                <div class="d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-3 me-1" style="width: 36px; height: 36px; min-width: 36px;">
                                    <i class="fas fa-store-alt fs-6"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark lh-sm" style="font-size: 0.95rem;">{{ auth()->user()->branch?->nama_cabang }}</div>
                                    <div class="text-muted" style="font-size: 0.72rem;">Kode: <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle font-monospace px-1.5 py-0">{{ auth()->user()->branch?->kode }}</span></div>
                                </div>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 small fw-semibold">
                                <i class="fas fa-check-circle me-1"></i> Terverifikasi
                            </span>
                        </div>
                    </div>
                @else
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Pilih Cabang <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-success"><i class="fas fa-store-alt"></i></span>
                            <select name="branch_id" id="prospect_branch_id" class="form-select" required>
                                <option value="">-- Pilih Cabang --</option>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}">{{ $b->nama_cabang }} ({{ $b->kode }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif

                <!-- Tanggal Input Prospek -->
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">Tanggal Masuk Prospek</label>
                    <input type="date" name="tanggal_input" id="prospect_tanggal_input" class="form-control" value="{{ now()->toDateString() }}">
                </div>

                <!-- 1. NAMA PROSPEK (Required) -->
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">
                        <i class="fas fa-user text-primary me-1"></i> Nama Calon Konsumen <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="nama" id="prospect_nama" class="form-control form-control-lg fs-6" placeholder="Contoh: Budi Santoso" required autocomplete="off">
                </div>

                <!-- 2. NOMOR HP / WHATSAPP (Required) -->
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">
                        <i class="fab fa-whatsapp text-success me-1"></i> Nomor HP / WhatsApp <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted fw-semibold"><i class="fas fa-phone-alt"></i></span>
                        <input type="tel" name="nomor_hp" id="prospect_nomor_hp" class="form-control form-control-lg fs-6" placeholder="Contoh: 081234567890" required autocomplete="off">
                    </div>
                    <small class="text-muted" style="font-size: 0.72rem;">Masukkan nomor aktif WhatsApp agar tombol chat dapat langsung dibuka.</small>
                </div>

                <!-- 3. DROPDOWN SUMBER LEADS (Instagram, Facebook, WhatsApp) -->
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">
                        <i class="fas fa-share-alt text-info me-1"></i> Sumber Leads <span class="text-danger">*</span>
                    </label>
                    <select name="sumber_leads" id="prospect_sumber_leads" class="form-select form-select-lg fs-6 fw-semibold" required>
                        <option value="">-- Pilih Sumber Leads --</option>
                        <option value="Instagram">📸 Instagram</option>
                        <option value="Facebook">👥 Facebook</option>
                        <option value="WhatsApp">💬 WhatsApp</option>
                    </select>
                </div>

                <!-- Kampanye Terkait (Opsional) -->
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Iklan Terkait (Opsional)</label>
                    <select name="meta_ads_report_id" id="prospect_meta_ads_report_id" class="form-select form-select-sm">
                        <option value="">-- Tanpa Iklan Spesifik / Umum --</option>
                        @foreach($metaCampaigns as $camp)
                            <option value="{{ $camp->id }}">{{ $camp->nama_iklan }} ({{ $camp->product }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Catatan Tambahan (Opsional) -->
                <div class="mb-2">
                    <label class="form-label small fw-semibold text-muted">Catatan Minat Konsumen / Follow Up (Opsional)</label>
                    <textarea name="catatan" id="prospect_catatan" class="form-control" rows="2" placeholder="Contoh: Minat promo NMAX Turbo DP 3jt, mau survey minggu depan..."></textarea>
                </div>
            </div>

            <div class="modal-footer bg-light border-0 py-3 flex-shrink-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm" id="btnSubmitProspect">
                    <i class="fas fa-check-circle me-1.5"></i> Simpan Data Prospek
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 2: FORM INPUT / EDIT MONTHLY REPORT META ADS (RAPI & MODAL-XL)      -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalMetaAds" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <form id="formMetaAds" class="modal-content border-0 shadow-lg" enctype="multipart/form-data" style="border-radius: 1rem; overflow: hidden;">
            @csrf
            <input type="hidden" name="id" id="report_id">

            <div class="modal-header text-white flex-shrink-0" style="background: linear-gradient(135deg, #001a4d 0%, #002b80 100%);">
                <div>
                    <h5 class="modal-title fw-bold" id="modalMetaAdsTitle">
                        <i class="fab fa-facebook me-2 text-info"></i> Form Monthly Report Meta Ads
                    </h5>
                    <small class="text-white-50">Laporan bulanan efisiensi iklan, invoice resmi top up (PDF), dan bukti closing STU</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4 custom-modal-scroll" style="background-color: #fbfcfe;">

                    <!-- SECTION 1: PERIODE & CABANG -->
                    <div class="card-section-form">
                        <div class="card-section-title">
                            <i class="fas fa-calendar-alt text-primary"></i> 1. Identitas Cabang & Periode Laporan
                        </div>
                        <div class="row g-3 align-items-center">
                            @if(auth()->user()->hasRole('PIC Digital Cabang'))
                                <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id }}">
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-bold text-dark mb-1">Cabang Pelapor <span class="text-danger">*</span></label>
                                    <div class="border rounded-3 bg-white p-2 px-3 d-flex align-items-center justify-content-between shadow-xs" style="min-height: 48px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-3 me-1" style="width: 36px; height: 36px; min-width: 36px;">
                                                <i class="fas fa-store-alt fs-6"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark lh-sm" style="font-size: 0.95rem;">{{ auth()->user()->branch?->nama_cabang }}</div>
                                                <div class="text-muted" style="font-size: 0.72rem;">Kode: <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle font-monospace px-1.5 py-0">{{ auth()->user()->branch?->kode }}</span></div>
                                            </div>
                                        </div>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 small fw-semibold">
                                            <i class="fas fa-check-circle me-1"></i> Terverifikasi
                                        </span>
                                    </div>
                                </div>
                            @else
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-bold text-dark mb-1">Pilih Cabang <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-primary"><i class="fas fa-store-alt"></i></span>
                                        <select name="branch_id" id="form_branch_id" class="form-select form-select-lg fs-6" required>
                                            <option value="">-- Pilih Cabang --</option>
                                            @foreach($branches as $b)
                                                <option value="{{ $b->id }}">{{ $b->nama_cabang }} ({{ $b->kode }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif

                            <div class="col-6 col-md-3">
                                <label class="form-label small fw-bold">Bulan Laporan <span class="text-danger">*</span></label>
                                <select name="bulan" id="form_bulan" class="form-select form-select-lg fs-6" required>
                                    @foreach($namaBulanList as $num => $nama)
                                        <option value="{{ $num }}" {{ now()->month == $num ? 'selected' : '' }}>{{ $nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6 col-md-3">
                                <label class="form-label small fw-bold">Tahun Laporan <span class="text-danger">*</span></label>
                                <select name="tahun" id="form_tahun" class="form-select form-select-lg fs-6" required>
                                    @for($y = 2024; $y <= 2028; $y++)
                                        <option value="{{ $y }}" {{ now()->year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: DETAIL IKLAN & PRODUK YAMAHA -->
                    <div class="card-section-form">
                        <div class="card-section-title">
                            <i class="fas fa-bullhorn text-primary"></i> 2. Detail Iklan & Unit Yamaha
                        </div>
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold">Nama Iklan (Meta Campaign) <span class="text-danger">*</span></label>
                                <input type="text" name="nama_iklan" id="form_nama_iklan" class="form-control form-control-lg fs-6" placeholder="Contoh: Promo Gaspol NMAX Turbo Okt 2026" required autocomplete="off">
                                <small class="text-muted" style="font-size: 0.72rem;">Gunakan nama kampanye yang sesuai di Ads Manager.</small>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold">Product Yang di Iklankan <span class="text-danger">*</span></label>
                                <select name="product" id="form_product" class="form-select form-select-lg fs-6" required>
                                    <option value="">-- Pilih Model Motor Yamaha --</option>
                                    <option value="GEAR ULTIMA 125 HYBRID">GEAR ULTIMA 125 HYBRID</option>
                                    <option value="GEAR ULTIMA 125 HYBRID S">GEAR ULTIMA 125 HYBRID S</option>
                                    <option value="GEAR ULTIMA 125 HYBRID SOLID">GEAR ULTIMA 125 HYBRID SOLID</option>
                                    <option value="GEAR ULTIMA 125 HYBRID SMART">GEAR ULTIMA 125 HYBRID SMART</option>
                                    <option value="FAZZIO HYBRID">FAZZIO HYBRID</option>
                                    <option value="FAZZIO HYBRID NEO">FAZZIO HYBRID NEO</option>
                                    <option value="FAZZIO HYBRID LUX">FAZZIO HYBRID LUX</option>
                                    <option value="FAZZIO STARRY NIGHT">FAZZIO STARRY NIGHT</option>
                                    <option value="GRAND FILANO HYBRID NEO">GRAND FILANO HYBRID NEO</option>
                                    <option value="GRAND FILANO HYBRID LUX">GRAND FILANO HYBRID LUX</option>
                                    <option value="FREEGO 125">FREEGO 125</option>
                                    <option value="VEGA FORCE DB CW">VEGA FORCE DB CW</option>
                                    <option value="JUPITER Z CW FI">JUPITER Z CW FI</option>
                                    <option value="MX KING 150">MX KING 150</option>
                                    <option value="MX KING 150 PRIMA PRAMAC">MX KING 150 PRIMA PRAMAC</option>
                                    <option value="LEXI LX 155 C">LEXI LX 155 C</option>
                                    <option value="LEXI LX 155 S C">LEXI LX 155 S C</option>
                                    <option value="ALL NEW AEROX 155">ALL NEW AEROX 155</option>
                                    <option value="ALL NEW AEROX 155 CYBERCITY">ALL NEW AEROX 155 CYBERCITY</option>
                                    <option value="AEROX ALPHA">AEROX ALPHA</option>
                                    <option value="AEROX ALPHA CYBERCITY ABS">AEROX ALPHA CYBERCITY ABS</option>
                                    <option value="AEROX ALPHA TURBO">AEROX ALPHA TURBO</option>
                                    <option value="NMAX NEO">NMAX NEO</option>
                                    <option value="NMAX NEO S">NMAX NEO S</option>
                                    <option value="NMAX TURBO">NMAX TURBO</option>
                                    <option value="NMAX TURBO TECH MAX">NMAX TURBO TECH MAX</option>
                                    <option value="XMAX C">XMAX C</option>
                                    <option value="XMAX TECH MAX">XMAX TECH MAX</option>
                                    <option value="ALL NEW VIXION">ALL NEW VIXION</option>
                                    <option value="WR 155 R">WR 155 R</option>
                                    <option value="ALL NEW XSR 155">ALL NEW XSR 155</option>
                                    <option value="ALL NEW R15 C">ALL NEW R15 C</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 3: METRIK EFISIENSI IKLAN (META ADS) -->
                    <div class="card-section-form">
                        <div class="card-section-title">
                            <i class="fas fa-tachometer-alt text-primary"></i> 3. Metrik Efisiensi Iklan (Meta Ads)
                        </div>
                        <div class="row g-3">
                            <!-- CTR -->
                            <div class="col-6 col-md-3">
                                <label class="form-label small fw-bold">CTR (%) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="ctr" id="form_ctr" class="form-control" placeholder="Contoh: 2.45" required>
                                    <span class="input-group-text bg-light text-muted fw-semibold">%</span>
                                </div>
                                <small class="text-muted" style="font-size: 0.7rem;">Click-Through Rate</small>
                            </div>
                            <!-- CPC -->
                            <div class="col-6 col-md-3">
                                <label class="form-label small fw-bold">CPC (Rp) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted fw-semibold">Rp</span>
                                    <input type="number" step="1" min="0" name="cpc" id="form_cpc" class="form-control" placeholder="Contoh: 1500" required>
                                </div>
                                <small class="text-muted" style="font-size: 0.7rem;">Cost Per Click</small>
                            </div>
                            <!-- CPM -->
                            <div class="col-6 col-md-3">
                                <label class="form-label small fw-bold">CPM (Rp) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted fw-semibold">Rp</span>
                                    <input type="number" step="1" min="0" name="cpm" id="form_cpm" class="form-control" placeholder="Contoh: 15000" required>
                                </div>
                                <small class="text-muted" style="font-size: 0.7rem;">Cost Per Mille (1.000 Views)</small>
                            </div>
                            <!-- CPL -->
                            <div class="col-6 col-md-3">
                                <label class="form-label small fw-bold">CPL (Rp) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted fw-semibold">Rp</span>
                                    <input type="number" step="1" min="0" name="cpl" id="form_cpl" class="form-control" placeholder="Contoh: 25000" required>
                                </div>
                                <small class="text-muted" style="font-size: 0.7rem;">Cost Per Lead (Percakapan)</small>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 4: HASIL KINERJA & PENJUALAN STU -->
                    <div class="card-section-form">
                        <div class="card-section-title">
                            <i class="fas fa-chart-line text-success"></i> 4. Hasil Kinerja, Budget & STU Closing
                        </div>
                        <div class="row g-3">
                            <!-- Total Views -->
                            <div class="col-12 col-sm-6 col-md-3">
                                <label class="form-label small fw-bold">Total Views (Impresi) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="fas fa-eye"></i></span>
                                    <input type="number" min="0" name="total_views" id="form_total_views" class="form-control" placeholder="Contoh: 45000" required>
                                </div>
                                <small class="text-muted" style="font-size: 0.7rem;">Jumlah impresi tayangan iklan</small>
                            </div>
                            <!-- Total Percakapan -->
                            <div class="col-12 col-sm-6 col-md-3">
                                <label class="form-label small fw-bold">Total Percakapan (Leads) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-success"><i class="fas fa-comments"></i></span>
                                    <input type="number" min="0" name="total_percakapan" id="form_total_percakapan" class="form-control" placeholder="Contoh: 120" required>
                                </div>
                                <small class="text-muted" style="font-size: 0.7rem;">Total pesan chat prospek didapat</small>
                            </div>
                            <!-- Budget Spend -->
                            <div class="col-12 col-sm-6 col-md-3">
                                <label class="form-label small fw-bold">Budget Spend (Rp) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-primary fw-bold">Rp</span>
                                    <input type="number" min="0" step="1" name="budget" id="form_budget" class="form-control" placeholder="Contoh: 3000000" required>
                                </div>
                                <small class="text-muted" style="font-size: 0.7rem;">Total saldo yang dibelanjakan</small>
                            </div>
                            <!-- STU Closing Unit -->
                            <div class="col-12 col-sm-6 col-md-3">
                                <label class="form-label small fw-bold text-danger">STU (Closing Unit) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" min="0" name="stu" id="form_stu" class="form-control border-danger fw-bold text-danger" placeholder="Contoh: 15" required>
                                    <span class="input-group-text bg-danger text-white fw-bold">Unit</span>
                                </div>
                                <small class="text-muted" style="font-size: 0.7rem;">Total unit motor terjual dari iklan</small>
                            </div>

                            <!-- Detail Unit Yamaha Terjual (STU) -->
                            <div class="col-12 mt-2">
                                <div class="d-flex align-items-center justify-content-between mb-2 pb-1">
                                    <label class="form-label small fw-bold text-dark m-0">
                                        <i class="fas fa-motorcycle text-danger me-1"></i> Unit Yamaha Terjual <span class="text-muted fw-normal">(Jika ada closing STU)</span>
                                    </label>
                                    <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2.5 rounded-pill fw-semibold shadow-xs" id="btnAddUnitTerjual" style="font-size: 0.78rem; height: 26px;">
                                        <i class="fas fa-plus me-1"></i> Tambah Motor
                                    </button>
                                </div>
                                <div id="unitTerjualContainer" class="d-flex flex-column gap-2 mt-1">
                                    <div class="unit-terjual-item d-flex align-items-center gap-2">
                                        <div class="flex-grow-1">
                                            <select name="unit_terjual[]" class="form-select select-unit-terjual">
                                                <option value="">-- Pilih Model Motor Yamaha --</option>
                                                <option value="GEAR ULTIMA 125 HYBRID">GEAR ULTIMA 125 HYBRID</option>
                                                <option value="GEAR ULTIMA 125 HYBRID S">GEAR ULTIMA 125 HYBRID S</option>
                                                <option value="GEAR ULTIMA 125 HYBRID SOLID">GEAR ULTIMA 125 HYBRID SOLID</option>
                                                <option value="GEAR ULTIMA 125 HYBRID SMART">GEAR ULTIMA 125 HYBRID SMART</option>
                                                <option value="FAZZIO HYBRID">FAZZIO HYBRID</option>
                                                <option value="FAZZIO HYBRID NEO">FAZZIO HYBRID NEO</option>
                                                <option value="FAZZIO HYBRID LUX">FAZZIO HYBRID LUX</option>
                                                <option value="FAZZIO STARRY NIGHT">FAZZIO STARRY NIGHT</option>
                                                <option value="GRAND FILANO HYBRID NEO">GRAND FILANO HYBRID NEO</option>
                                                <option value="GRAND FILANO HYBRID LUX">GRAND FILANO HYBRID LUX</option>
                                                <option value="FREEGO 125">FREEGO 125</option>
                                                <option value="VEGA FORCE DB CW">VEGA FORCE DB CW</option>
                                                <option value="JUPITER Z CW FI">JUPITER Z CW FI</option>
                                                <option value="MX KING 150">MX KING 150</option>
                                                <option value="MX KING 150 PRIMA PRAMAC">MX KING 150 PRIMA PRAMAC</option>
                                                <option value="LEXI LX 155 C">LEXI LX 155 C</option>
                                                <option value="LEXI LX 155 S C">LEXI LX 155 S C</option>
                                                <option value="ALL NEW AEROX 155">ALL NEW AEROX 155</option>
                                                <option value="ALL NEW AEROX 155 CYBERCITY">ALL NEW AEROX 155 CYBERCITY</option>
                                                <option value="AEROX ALPHA">AEROX ALPHA</option>
                                                <option value="AEROX ALPHA CYBERCITY ABS">AEROX ALPHA CYBERCITY ABS</option>
                                                <option value="AEROX ALPHA TURBO">AEROX ALPHA TURBO</option>
                                                <option value="NMAX NEO">NMAX NEO</option>
                                                <option value="NMAX NEO S">NMAX NEO S</option>
                                                <option value="NMAX TURBO">NMAX TURBO</option>
                                                <option value="NMAX TURBO TECH MAX">NMAX TURBO TECH MAX</option>
                                                <option value="XMAX C">XMAX C</option>
                                                <option value="XMAX TECH MAX">XMAX TECH MAX</option>
                                                <option value="ALL NEW VIXION">ALL NEW VIXION</option>
                                                <option value="WR 155 R">WR 155 R</option>
                                                <option value="ALL NEW XSR 155">ALL NEW XSR 155</option>
                                                <option value="ALL NEW R15 C">ALL NEW R15 C</option>
                                            </select>
                                        </div>
                                        <button type="button" class="btn btn-outline-danger btn-sm btn-remove-unit d-none" title="Hapus unit ini" style="min-width: 38px; height: 38px; flex-shrink: 0;">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">Pilih model unit motor Yamaha yang berhasil closing STU dari kampanye iklan ini. Klik tombol <strong>+ Tambah Motor</strong> jika closing lebih dari 1 unit motor.</small>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 5: LAMPIRAN INVOICE (PDF) & BUKTI CLOSING STU -->
                    <div class="card-section-form">
                        <div class="card-section-title">
                            <i class="fas fa-paperclip text-primary"></i> 5. Lampiran Dokumen & Bukti Transaksi
                        </div>
                        <div class="row g-3">
                            <!-- Invoice Top Up Saldo (Wajib PDF) -->
                            <div class="col-12 col-md-6">
                                <div class="pdf-upload-box h-100">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <label class="form-label small fw-bold text-danger m-0 d-flex align-items-center gap-1.5">
                                            <i class="fas fa-file-pdf fs-5 text-danger"></i> Invoice Top Up Saldo (Format PDF)
                                        </label>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">Wajib PDF</span>
                                    </div>
                                    <input type="file" name="bukti_topup" id="form_bukti_topup" class="form-control form-control-sm mb-2" accept=".pdf,application/pdf">
                                    
                                    <!-- Selected file name preview -->
                                    <div id="selected_pdf_info" class="d-none alert alert-light border py-2 px-3 small mb-2 d-flex align-items-center gap-2">
                                        <i class="fas fa-file-pdf text-danger fs-4"></i>
                                        <div class="text-truncate">
                                            <span class="fw-bold text-dark d-block text-truncate" id="selected_pdf_name">file.pdf</span>
                                            <span class="text-muted" id="selected_pdf_size">0 KB</span>
                                        </div>
                                    </div>

                                    <!-- Existing File preview if edit -->
                                    <div id="preview_bukti_topup_container" class="d-none mb-2">
                                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fas fa-file-pdf text-danger fs-4"></i>
                                                <div>
                                                    <div class="small fw-bold text-dark">Invoice PDF Tersimpan</div>
                                                    <small class="text-muted">Klik untuk melihat dokumen</small>
                                                </div>
                                            </div>
                                            <a href="#" target="_blank" id="btn_view_existing_pdf" class="btn btn-xs btn-outline-danger px-2.5 py-1 rounded-pill">
                                                <i class="fas fa-external-link-alt me-1"></i> Buka PDF
                                            </a>
                                        </div>
                                    </div>

                                    <small class="text-muted d-block" style="font-size: 0.72rem;">
                                        Unggah invoice resmi top up saldo Meta Ads dalam <strong>format PDF</strong> (maks. 20MB). Jika tidak mengunggah file baru saat edit, file lama akan tetap dipertahankan.
                                    </small>
                                </div>
                            </div>

                            <!-- Bukti Closing STU (Multiple Images) -->
                            <div class="col-12 col-md-6">
                                <div class="p-3 bg-white rounded-3 border h-100">
                                    <label class="form-label small fw-bold text-success mb-1 d-flex align-items-center gap-1.5">
                                        <i class="fas fa-camera fs-5 text-success"></i> Bukti Closing STU (Bisa Banyak Foto)
                                    </label>
                                    <input type="file" name="bukti_closing_stu[]" id="form_bukti_closing_stu" class="form-control form-control-sm mb-2" accept="image/*" multiple>
                                    <div id="existing_closing_proofs" class="d-flex flex-wrap gap-2 mb-2"></div>
                                    <small class="text-muted d-block" style="font-size: 0.72rem;">
                                        Dapat memilih beberapa foto sekaligus (foto serah terima unit, SPK, struk deal closing). Maksimal 10MB per foto.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 6: CATATAN & ANALISIS IKLAN -->
                    <div class="card-section-form mb-0">
                        <div class="card-section-title">
                            <i class="fas fa-comment-alt text-primary"></i> 6. Catatan & Analisis Performa Iklan
                        </div>
                        <textarea name="catatan" id="form_catatan" class="form-control" rows="2" placeholder="Catatan analisa performa kampanye, target audience, kendala atau rekomendasi untuk optimasi berikutnya..."></textarea>
                    </div>

                </div>

                <div class="modal-footer bg-light border-0 py-3 flex-shrink-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-yamaha-primary rounded-pill px-4 shadow-sm" id="btnSubmitForm">
                        <i class="fas fa-save me-1.5"></i> Simpan Laporan Meta Ads
                    </button>
                </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL DETAIL REPORT (VIEW FULL REPORT DATA)                                -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalDetailReport" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow" style="border-radius: 1rem; overflow: hidden;">
            <div class="modal-header bg-dark text-white flex-shrink-0">
                <h5 class="modal-title fw-bold" id="detail_nama_iklan">
                    <i class="fab fa-meta text-primary me-2"></i> Detail Laporan Meta Ads
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 custom-modal-scroll" id="detailReportBody">
                <!-- Dynamically loaded via AJAX -->
            </div>
            <div class="modal-footer bg-light flex-shrink-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Image Lightbox / Full Preview -->
<div class="modal fade" id="modalImagePreview" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow bg-dark text-white">
            <div class="modal-header border-bottom border-secondary py-2">
                <h6 class="modal-title m-0 text-truncate" id="modalImagePreviewTitle">Preview Foto</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 text-center bg-black">
                <img src="" id="modalImagePreviewImg" class="img-fluid" style="max-height: 80vh; object-fit: contain;">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // ---------------------------------------------------------
    // 1. DATA TABLE: MONTHLY REPORT META ADS
    // ---------------------------------------------------------
    const table = $('#metaAdsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('reports.meta-ads.index') }}",
            data: function (d) {
                d.tahun = $('#filter_tahun').val();
                d.bulan = $('#filter_bulan').val();
                d.branch_id = $('#filter_branch_id').val();
                d.product = $('#filter_product').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'cabang', name: 'branch.nama_cabang' },
            { data: 'periode', name: 'bulan' },
            { data: 'nama_iklan', name: 'nama_iklan' },
            { data: 'budget', name: 'budget' },
            { data: 'total_views', name: 'total_views' },
            { data: 'total_percakapan', name: 'total_percakapan' },
            { data: 'ctr', name: 'ctr' },
            { data: 'cpc', name: 'cpc' },
            { data: 'cpm', name: 'cpm' },
            { data: 'cpl', name: 'cpl' },
            { data: 'stu', name: 'stu' },
            { data: 'bukti_topup_btn', name: 'bukti_topup_btn', orderable: false, searchable: false },
            { data: 'bukti_closing_btn', name: 'bukti_closing_btn', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[2, 'desc']],
        pageLength: 15,
        language: {
            search: "Cari Laporan:",
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Tidak ada data laporan Meta Ads ditemukan",
            info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 s/d 0 dari 0 data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            paginate: { first: "Awal", last: "Akhir", next: "Lanjut", previous: "Kembali" }
        }
    });

    // ---------------------------------------------------------
    // 2. DATA TABLE: PROSPECTS / LEADS META ADS
    // ---------------------------------------------------------
    const prospectTable = $('#prospectTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('reports.meta-ads.prospects') }}",
            data: function (d) {
                d.tahun = $('#filter_tahun').val();
                d.bulan = $('#filter_bulan').val();
                d.branch_id = $('#filter_branch_id').val();
                d.sumber_leads = $('#filter_sumber_leads').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'cabang', name: 'branch.nama_cabang' },
            { data: 'tanggal_input', name: 'tanggal_input' },
            { data: 'nama', name: 'nama' },
            { data: 'nomor_hp', name: 'nomor_hp' },
            { data: 'sumber_leads', name: 'sumber_leads' },
            { data: 'status', name: 'status' },
            { data: 'catatan', name: 'catatan' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[2, 'desc']],
        pageLength: 15,
        language: {
            search: "Cari Prospek:",
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Belum ada data prospek ditemukan",
            info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ prospek",
            infoEmpty: "Menampilkan 0 data prospek",
            infoFiltered: "(disaring dari _MAX_ total prospek)",
            paginate: { first: "Awal", last: "Akhir", next: "Lanjut", previous: "Kembali" }
        }
    });

    // Apply Filter Button
    $('#btnApplyFilter').on('click', function() {
        table.ajax.reload();
        prospectTable.ajax.reload();
    });

    // Quick filter sumber leads in tab 2
    $('#filter_sumber_leads').on('change', function() {
        prospectTable.ajax.reload();
    });

    // ---------------------------------------------------------
    // 3. ACTION: OPEN MODAL PROSPEK (FORM TERPISAH)
    // ---------------------------------------------------------
    $('#btnTambahProspect').on('click', function() {
        $('#formProspect')[0].reset();
        $('#prospect_tanggal_input').val(new Date().toISOString().slice(0, 10));
        $('#modalProspect').modal('show');
    });

    // Submit Form Prospect
    $('#formProspect').on('submit', function(e) {
        e.preventDefault();
        const submitBtn = $('#btnSubmitProspect');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');

        $.ajax({
            url: "{{ route('reports.meta-ads.prospects.store') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(res) {
                submitBtn.prop('disabled', false).html('<i class="fas fa-check-circle me-1.5"></i> Simpan Data Prospek');
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $('#modalProspect').modal('hide');
                    prospectTable.ajax.reload(null, false);

                    // Switch to prospect tab to see newly created lead
                    const tabTrigger = new bootstrap.Tab(document.querySelector('#tab-prospects-tab'));
                    tabTrigger.show();
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html('<i class="fas fa-check-circle me-1.5"></i> Simpan Data Prospek');
                let errHtml = '';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function(key, val) {
                        errHtml += '<p class="m-0 text-start">• ' + val[0] + '</p>';
                    });
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errHtml = xhr.responseJSON.message;
                } else {
                    errHtml = 'Terjadi kesalahan saat menyimpan data prospek.';
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan!',
                    html: errHtml
                });
            }
        });
    });

    // Delete Prospect
    $(document).on('click', '.btn-delete-prospect', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Hapus Data Prospek?',
            text: 'Data prospek konsumen ini akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('/reports/meta-ads/prospects') }}/" + id,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(res) {
                        Swal.fire({ icon: 'success', title: 'Terhapus!', text: res.message, timer: 1500, showConfirmButton: false });
                        prospectTable.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        Swal.fire({ icon: 'error', title: 'Gagal!', text: xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan sistem' });
                    }
                });
            }
        });
    });

    // ---------------------------------------------------------
    // 4. ACTION: OPEN MODAL REPORT META ADS & FILE PDF HANDLING
    // ---------------------------------------------------------
    const yamahaModelOptionsHtml = `
        <option value="">-- Pilih Model Motor Yamaha --</option>
        <option value="GEAR ULTIMA 125 HYBRID">GEAR ULTIMA 125 HYBRID</option>
        <option value="GEAR ULTIMA 125 HYBRID S">GEAR ULTIMA 125 HYBRID S</option>
        <option value="GEAR ULTIMA 125 HYBRID SOLID">GEAR ULTIMA 125 HYBRID SOLID</option>
        <option value="GEAR ULTIMA 125 HYBRID SMART">GEAR ULTIMA 125 HYBRID SMART</option>
        <option value="FAZZIO HYBRID">FAZZIO HYBRID</option>
        <option value="FAZZIO HYBRID NEO">FAZZIO HYBRID NEO</option>
        <option value="FAZZIO HYBRID LUX">FAZZIO HYBRID LUX</option>
        <option value="FAZZIO STARRY NIGHT">FAZZIO STARRY NIGHT</option>
        <option value="GRAND FILANO HYBRID NEO">GRAND FILANO HYBRID NEO</option>
        <option value="GRAND FILANO HYBRID LUX">GRAND FILANO HYBRID LUX</option>
        <option value="FREEGO 125">FREEGO 125</option>
        <option value="VEGA FORCE DB CW">VEGA FORCE DB CW</option>
        <option value="JUPITER Z CW FI">JUPITER Z CW FI</option>
        <option value="MX KING 150">MX KING 150</option>
        <option value="MX KING 150 PRIMA PRAMAC">MX KING 150 PRIMA PRAMAC</option>
        <option value="LEXI LX 155 C">LEXI LX 155 C</option>
        <option value="LEXI LX 155 S C">LEXI LX 155 S C</option>
        <option value="ALL NEW AEROX 155">ALL NEW AEROX 155</option>
        <option value="ALL NEW AEROX 155 CYBERCITY">ALL NEW AEROX 155 CYBERCITY</option>
        <option value="AEROX ALPHA">AEROX ALPHA</option>
        <option value="AEROX ALPHA CYBERCITY ABS">AEROX ALPHA CYBERCITY ABS</option>
        <option value="AEROX ALPHA TURBO">AEROX ALPHA TURBO</option>
        <option value="NMAX NEO">NMAX NEO</option>
        <option value="NMAX NEO S">NMAX NEO S</option>
        <option value="NMAX TURBO">NMAX TURBO</option>
        <option value="NMAX TURBO TECH MAX">NMAX TURBO TECH MAX</option>
        <option value="XMAX C">XMAX C</option>
        <option value="XMAX TECH MAX">XMAX TECH MAX</option>
        <option value="ALL NEW VIXION">ALL NEW VIXION</option>
        <option value="WR 155 R">WR 155 R</option>
        <option value="ALL NEW XSR 155">ALL NEW XSR 155</option>
        <option value="ALL NEW R15 C">ALL NEW R15 C</option>
    `;

    function createUnitTerjualRow(selectedValue = '') {
        const row = $(`
            <div class="unit-terjual-item d-flex align-items-center gap-2">
                <div class="flex-grow-1">
                    <select name="unit_terjual[]" class="form-select select-unit-terjual">
                        ${yamahaModelOptionsHtml}
                    </select>
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm btn-remove-unit" title="Hapus unit ini" style="min-width: 38px; height: 38px; flex-shrink: 0;">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        `);
        if (selectedValue) {
            row.find('.select-unit-terjual').val(selectedValue);
        }
        return row;
    }

    function updateUnitRemoveButtons() {
        const items = $('#unitTerjualContainer .unit-terjual-item');
        if (items.length <= 1) {
            items.find('.btn-remove-unit').addClass('d-none');
        } else {
            items.find('.btn-remove-unit').removeClass('d-none');
        }
    }

    function setUnitTerjualValues(units) {
        $('#unitTerjualContainer').empty();
        if (!units || units.length === 0) {
            $('#unitTerjualContainer').append(createUnitTerjualRow(''));
        } else {
            units.forEach(u => {
                $('#unitTerjualContainer').append(createUnitTerjualRow(u.trim()));
            });
        }
        updateUnitRemoveButtons();
    }

    // Add unit row button
    $('#btnAddUnitTerjual').on('click', function() {
        const newRow = createUnitTerjualRow('');
        $('#unitTerjualContainer').append(newRow);
        updateUnitRemoveButtons();
        newRow.find('.select-unit-terjual').focus();
    });

    // Remove unit row button
    $(document).on('click', '.btn-remove-unit', function() {
        $(this).closest('.unit-terjual-item').remove();
        updateUnitRemoveButtons();
    });

    $('#btnTambahReport').on('click', function() {
        $('#formMetaAds')[0].reset();
        $('#report_id').val('');
        setUnitTerjualValues([]);
        $('#modalMetaAdsTitle').html('<i class="fab fa-facebook me-2 text-info"></i> Input Monthly Report Meta Ads');
        $('#preview_bukti_topup_container').addClass('d-none');
        $('#selected_pdf_info').addClass('d-none');
        $('#existing_closing_proofs').empty();
        $('#modalMetaAds').modal('show');
    });

    // PDF file selection listener in report modal
    $('#form_bukti_topup').on('change', function() {
        const file = this.files[0];
        if (file) {
            if (!file.name.toLowerCase().endsWith('.pdf') && file.type !== 'application/pdf') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Format Harus PDF!',
                    text: 'Invoice bukti top up saldo Meta Ads wajib berformat file PDF (.pdf).'
                });
                $(this).val('');
                $('#selected_pdf_info').addClass('d-none');
                return;
            }
            $('#selected_pdf_name').text(file.name);
            $('#selected_pdf_size').text((file.size / 1024).toFixed(1) + ' KB');
            $('#selected_pdf_info').removeClass('d-none');
        } else {
            $('#selected_pdf_info').addClass('d-none');
        }
    });

    // Submit Report Form (Create / Update)
    $('#formMetaAds').on('submit', function(e) {
        e.preventDefault();
        const submitBtn = $('#btnSubmitForm');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');

        const formData = new FormData(this);

        $.ajax({
            url: "{{ route('reports.meta-ads.store') }}",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                submitBtn.prop('disabled', false).html('<i class="fas fa-save me-1.5"></i> Simpan Laporan Meta Ads');
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $('#modalMetaAds').modal('hide');
                    table.ajax.reload(null, false);
                    setTimeout(() => location.reload(), 1500);
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html('<i class="fas fa-save me-1.5"></i> Simpan Laporan Meta Ads');
                let errHtml = '';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function(key, val) {
                        errHtml += '<p class="m-0 text-start">• ' + val[0] + '</p>';
                    });
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errHtml = xhr.responseJSON.message;
                } else {
                    errHtml = 'Terjadi kesalahan sistem.';
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan!',
                    html: errHtml
                });
            }
        });
    });

    // Edit Report Button Click
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        $.get("{{ url('/reports/meta-ads') }}/" + id, function(data) {
            $('#formMetaAds')[0].reset();
            $('#report_id').val(data.id);
            $('#modalMetaAdsTitle').html('<i class="fas fa-edit me-2 text-warning"></i> Edit Report Meta Ads #' + data.id);
            $('#selected_pdf_info').addClass('d-none');

            if ($('#form_branch_id').length) {
                $('#form_branch_id').val(data.branch_id);
            }
            $('#form_bulan').val(data.bulan);
            $('#form_tahun').val(data.tahun);
            $('#form_nama_iklan').val(data.nama_iklan);
            $('#form_product').val(data.product);
            $('#form_ctr').val(data.ctr);
            $('#form_cpc').val(data.cpc);
            $('#form_cpm').val(data.cpm);
            $('#form_cpl').val(data.cpl);
            $('#form_total_views').val(data.total_views);
            $('#form_total_percakapan').val(data.total_percakapan);
            $('#form_budget').val(data.budget);
            $('#form_stu').val(data.stu);
            let units = [];
            if (data.unit_terjual) {
                units = data.unit_terjual.split(',').map(s => s.trim()).filter(Boolean);
            }
            setUnitTerjualValues(units);
            $('#form_catatan').val(data.catatan);

            // Existing Invoice PDF
            if (data.bukti_topup_url) {
                $('#btn_view_existing_pdf').attr('href', data.bukti_topup_url);
                $('#preview_bukti_topup_container').removeClass('d-none');
            } else {
                $('#preview_bukti_topup_container').addClass('d-none');
            }

            // Existing Closing Proofs Gallery
            $('#existing_closing_proofs').empty();
            if (data.closing_proofs && data.closing_proofs.length > 0) {
                data.closing_proofs.forEach(function(proof) {
                    $('#existing_closing_proofs').append(`
                        <div class="position-relative d-inline-block border rounded p-1 bg-white shadow-xs" id="proof_card_${proof.id}">
                            <img src="${proof.file_url}" class="rounded" style="width: 70px; height: 70px; object-fit: cover;">
                            <button type="button" class="btn btn-danger btn-xs position-absolute top-0 end-0 p-1 py-0 rounded-circle btn-delete-proof" data-id="${proof.id}" title="Hapus foto ini">
                                &times;
                            </button>
                        </div>
                    `);
                });
            }

            $('#modalMetaAds').modal('show');
        });
    });

    // Delete Individual Closing Proof inside Edit Modal
    $(document).on('click', '.btn-delete-proof', function() {
        const proofId = $(this).data('id');
        Swal.fire({
            title: 'Hapus foto ini?',
            text: 'Foto bukti closing ini akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('/reports/meta-ads/closing-proof') }}/" + proofId,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(res) {
                        $('#proof_card_' + proofId).remove();
                        Swal.fire({ icon: 'success', title: 'Terhapus!', text: res.message, timer: 1500, showConfirmButton: false });
                    }
                });
            }
        });
    });

    // Detail Button Click
    $(document).on('click', '.btn-detail', function() {
        const id = $(this).data('id');
        $.get("{{ url('/reports/meta-ads') }}/" + id, function(data) {
            $('#detail_nama_iklan').html('<i class="fab fa-meta text-primary me-2"></i>' + data.nama_iklan);

            let closingHtml = '';
            if (data.closing_proofs && data.closing_proofs.length > 0) {
                closingHtml = '<div class="row g-2">';
                data.closing_proofs.forEach(function(p, i) {
                    closingHtml += `
                        <div class="col-4 col-md-3">
                            <div class="border rounded p-1 bg-light text-center h-100 shadow-xs">
                                <img src="${p.file_url}" class="img-fluid rounded cursor-pointer btn-preview-image" style="height: 90px; object-fit: cover;" data-url="${p.file_url}" data-title="Bukti Closing #${i+1}">
                                <div class="small text-muted mt-1" style="font-size: 0.68rem;">Bukti #${i+1}</div>
                            </div>
                        </div>
                    `;
                });
                closingHtml += '</div>';
            } else {
                closingHtml = '<div class="text-muted fst-italic small">Belum ada foto bukti closing STU yang diunggah.</div>';
            }

            let topupHtml = '';
            if (data.bukti_topup_url) {
                if (data.is_bukti_topup_pdf || (data.bukti_topup && data.bukti_topup.toLowerCase().endsWith('.pdf'))) {
                    topupHtml = `
                        <div class="p-3 border rounded-3 bg-light text-center">
                            <i class="fas fa-file-pdf fa-3x text-danger mb-2 d-block"></i>
                            <div class="fw-bold small text-dark mb-1">Invoice Top Up (Format PDF)</div>
                            <p class="text-muted small mb-2" style="font-size: 0.72rem;">Dokumen resmi invoice top up saldo iklan Meta Ads</p>
                            <a href="${data.bukti_topup_url}" target="_blank" class="btn btn-sm btn-outline-danger px-3 rounded-pill shadow-sm">
                                <i class="fas fa-file-pdf me-1.5"></i> Buka / Download Invoice PDF
                            </a>
                        </div>
                    `;
                } else {
                    topupHtml = `
                        <div class="border rounded p-2 bg-light text-center">
                            <img src="${data.bukti_topup_url}" class="img-fluid rounded cursor-pointer btn-preview-image mb-2" style="max-height: 160px;" data-url="${data.bukti_topup_url}" data-title="Bukti Top Up: ${data.nama_iklan}">
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-primary btn-preview-image rounded-pill px-3" data-url="${data.bukti_topup_url}" data-title="Bukti Top Up: ${data.nama_iklan}">
                                    <i class="fas fa-search-plus me-1"></i> Perbesar Bukti
                                </button>
                            </div>
                        </div>
                    `;
                }
            } else {
                topupHtml = '<div class="text-muted fst-italic small">Tidak melampirkan file invoice top up.</div>';
            }

            let costPerStu = (data.stu > 0 && data.budget > 0) ? 'Rp ' + Number(Math.round(data.budget / data.stu)).toLocaleString('id-ID') : '-';
            let convRate = (data.total_percakapan > 0 && data.stu > 0) ? Number((data.stu / data.total_percakapan) * 100).toFixed(2) + '%' : '0%';

            let html = `
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="p-2 border rounded bg-light">
                            <div class="text-muted small">Cabang</div>
                            <div class="fw-bold">${data.branch ? data.branch.nama_cabang : '-'}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-2 border rounded bg-light">
                            <div class="text-muted small">Periode</div>
                            <div class="fw-bold">${data.nama_bulan} ${data.tahun}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-2 border rounded bg-light">
                            <div class="text-muted small">Produk</div>
                            <div class="fw-bold text-primary">${data.product}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-2 border rounded bg-light">
                            <div class="text-muted small">Budget Spend</div>
                            <div class="fw-bold text-success">${data.formatted_budget}</div>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="fas fa-chart-line text-primary me-2"></i> Metrik Efisiensi Kampanye</h6>
                <div class="row g-2 mb-4 text-center">
                    <div class="col-4 col-md-2">
                        <div class="p-2 border rounded bg-light">
                            <div class="text-muted small">CTR</div>
                            <div class="fw-bold fs-6">${Number(data.ctr).toFixed(2)}%</div>
                        </div>
                    </div>
                    <div class="col-4 col-md-2">
                        <div class="p-2 border rounded bg-light">
                            <div class="text-muted small">CPC</div>
                            <div class="fw-bold fs-6">${data.formatted_cpc}</div>
                        </div>
                    </div>
                    <div class="col-4 col-md-2">
                        <div class="p-2 border rounded bg-light">
                            <div class="text-muted small">CPM</div>
                            <div class="fw-bold fs-6">${data.formatted_cpm}</div>
                        </div>
                    </div>
                    <div class="col-4 col-md-2">
                        <div class="p-2 border rounded bg-light">
                            <div class="text-muted small">CPL</div>
                            <div class="fw-bold fs-6 text-info">${data.formatted_cpl}</div>
                        </div>
                    </div>
                    <div class="col-4 col-md-2">
                        <div class="p-2 border rounded bg-light">
                            <div class="text-muted small">Views</div>
                            <div class="fw-bold fs-6">${Number(data.total_views).toLocaleString('id-ID')}</div>
                        </div>
                    </div>
                    <div class="col-4 col-md-2">
                        <div class="p-2 border rounded bg-light">
                            <div class="text-muted small">Percakapan</div>
                            <div class="fw-bold fs-6 text-success">${Number(data.total_percakapan).toLocaleString('id-ID')}</div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <div class="p-3 border border-danger border-opacity-25 rounded-3 bg-danger bg-opacity-10">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-danger small fw-semibold">STU Closing Unit</div>
                                    <h4 class="fw-extrabold text-danger m-0">${Number(data.stu).toLocaleString('id-ID')} Unit</h4>
                                </div>
                                <div class="text-end">
                                    <div class="text-muted small">Cost Per STU</div>
                                    <div class="fw-bold text-dark">${costPerStu}</div>
                                </div>
                            </div>
                            <div class="mt-2 text-muted small">Conversion Rate Chat-to-Closing: <strong>${convRate}</strong></div>
                            ${data.unit_terjual ? `
                                <div class="mt-2 pt-2 border-top border-danger border-opacity-25 small text-dark">
                                    <div class="text-danger fw-semibold mb-1"><i class="fas fa-motorcycle me-1"></i> Unit Yamaha Terjual:</div>
                                    <div class="d-flex flex-wrap gap-1">
                                        ${data.unit_terjual.split(',').map(u => `<span class="badge bg-white text-danger border border-danger-subtle">${u.trim()}</span>`).join('')}
                                    </div>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <div class="small fw-semibold text-muted">Catatan / Evaluasi</div>
                            <p class="m-0 text-dark small">${data.catatan ? data.catatan : '<em>Tidak ada catatan.</em>'}</p>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-5">
                        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-file-invoice text-danger me-1"></i> Invoice Top Up Saldo</h6>
                        ${topupHtml}
                    </div>
                    <div class="col-12 col-md-7">
                        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-images text-success me-1"></i> Bukti Closing STU (${data.closing_proofs ? data.closing_proofs.length : 0} Foto)</h6>
                        ${closingHtml}
                    </div>
                </div>
            `;

            $('#detailReportBody').html(html);
            $('#modalDetailReport').modal('show');
        });
    });

    // View Closing Proofs Button Click
    $(document).on('click', '.btn-view-closing-proofs', function() {
        const id = $(this).data('id');
        $(`.btn-detail[data-id="${id}"]`).click();
    });

    // Image Preview Lightbox
    $(document).on('click', '.btn-preview-image', function() {
        const url = $(this).data('url');
        const title = $(this).data('title') || 'Preview Foto';
        $('#modalImagePreviewTitle').text(title);
        $('#modalImagePreviewImg').attr('src', url);
        $('#modalImagePreview').modal('show');
    });

    // Delete Report
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Hapus Laporan Meta Ads?',
            text: 'Seluruh data laporan, invoice PDF top up, dan bukti closing STU akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('/reports/meta-ads') }}/" + id,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(res) {
                        Swal.fire({ icon: 'success', title: 'Terhapus!', text: res.message, timer: 1500, showConfirmButton: false });
                        table.ajax.reload(null, false);
                        setTimeout(() => location.reload(), 1500);
                    },
                    error: function(xhr) {
                        Swal.fire({ icon: 'error', title: 'Gagal!', text: xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan sistem' });
                    }
                });
            }
        });
    });
});
</script>
@endpush
