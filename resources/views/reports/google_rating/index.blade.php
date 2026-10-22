@extends('layouts.app')

@section('title', 'Report Google Rating')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fab fa-google text-warning"></i> Report Google Rating & Testimoni Cabang
            </h3>
            <p class="text-muted mb-0">Pencatatan Google Rating awal bulan, target harian (min. 5 review/hari), dan arsip bukti screenshot testimoni konsumen (target 125 rating/bulan).</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-outline-warning rounded-pill px-3 shadow-sm text-dark fw-semibold" id="btnOpenBaselineModal">
                <i class="fas fa-star me-1.5 text-warning"></i> Google Rating Awal Bulan
            </button>
            <button type="button" class="btn btn-yamaha-primary rounded-pill px-4 shadow-sm" id="btnTambahTestimoni">
                <i class="fas fa-camera me-1.5"></i> Tambah Testimoni (Foto)
            </button>
        </div>
    </div>

    <!-- DAILY WARNING / SUCCESS BANNER (Minimal 5 per hari) -->
    @if(!$isTodayTargetMet)
        <div class="alert alert-warning border-0 shadow-sm rounded-4 p-3 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3" style="background: linear-gradient(90deg, #FFF3CD 0%, #FFEBA7 100%);">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-warning text-dark rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; font-size: 1.4rem;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                        Peringatan Target Harian: Minimal 5 Testimoni Per Hari!
                        <span class="badge bg-danger text-white rounded-pill px-2.5 py-1">Kurang {{ $todayDeficiency }} Review</span>
                    </h6>
                    <p class="text-dark text-opacity-75 small m-0 mt-0.5">
                        Hari ini baru di-input <strong>{{ $todayCount }}</strong> dari target minimal <strong>5</strong> testimoni Google Rating. Segera unggah sisa {{ $todayDeficiency }} testimoni konsumen hari ini!
                    </p>
                </div>
            </div>
            <button type="button" class="btn btn-danger btn-sm rounded-pill px-3 fw-semibold shadow-sm" onclick="$('#btnTambahTestimoni').click();">
                <i class="fas fa-plus-circle me-1"></i> Input Testimoni Sekarang
            </button>
        </div>
    @else
        <div class="alert alert-success border-0 shadow-sm rounded-4 p-3 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3" style="background: linear-gradient(90deg, #D1E7DD 0%, #C3E6CB 100%);">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-success text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; font-size: 1.4rem;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-success m-0">
                        🎉 Hebat! Target Harian Tercapai: {{ $todayCount }} Testimoni Di-input Hari Ini
                    </h6>
                    <p class="text-success text-opacity-75 small m-0 mt-0.5">
                        Target minimal 5 Google Rating hari ini telah terpenuhi dengan baik. Pertahankan reputasi digital dealer Yamaha Anda!
                    </p>
                </div>
            </div>
            <span class="badge bg-success rounded-pill px-3 py-2 fw-semibold">
                <i class="fas fa-thumbs-up me-1"></i> Target Harian Terpenuhi
            </span>
        </div>
    @endif

    <!-- Month & Branch Filter Card -->
    <div class="card card-custom p-3 mb-4 border-0 shadow-sm">
        <form action="{{ route('reports.google-rating.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-6 col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Tahun</label>
                <select name="tahun" class="form-select form-select-sm">
                    @for($y = 2024; $y <= 2028; $y++)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Bulan</label>
                <select name="bulan" class="form-select form-select-sm">
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
            @unless(auth()->user()->hasRole('PIC Digital Cabang'))
            <div class="col-8 col-md-4">
                <label class="form-label small fw-semibold text-muted mb-1">Pilih Cabang</label>
                <select name="branch_id" class="form-select form-select-sm">
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>{{ $b->nama_cabang }} ({{ $b->kode }})</option>
                    @endforeach
                </select>
            </div>
            @endunless
            <div class="col-4 col-md-2">
                <button type="submit" class="btn btn-sm btn-yamaha-primary w-100">
                    <i class="fas fa-filter me-1"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>

    <!-- Summary KPI & Baseline Cards Row -->
    <div class="row g-3 mb-4">
        <!-- Baseline: Google Rating Awal Bulan -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card card-custom p-3 border-0 bg-white shadow-sm h-100 border-start border-4 border-warning">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.68rem;">Rating Awal Bulan</div>
                        <h4 class="fw-extrabold text-warning m-0 mt-1 d-flex align-items-center gap-1">
                            <i class="fas fa-star text-warning"></i>
                            {{ $baseline ? number_format($baseline->rating_awal_bulan, 2) : '4.80' }}
                        </h4>
                        <small class="text-muted" style="font-size: 0.72rem;">Baseline: {{ $baseline ? number_format($baseline->total_review_awal_bulan) : '0' }} ulasan</small>
                    </div>
                    <button type="button" class="btn btn-sm btn-light border rounded-circle p-2 text-muted" onclick="$('#btnOpenBaselineModal').click();" title="Edit Baseline Awal Bulan">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Today's Testimonials Counter -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card card-custom p-3 border-0 bg-white shadow-sm h-100 border-start border-4 {{ $isTodayTargetMet ? 'border-success' : 'border-danger' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.68rem;">Input Hari Ini (Target: min. 5)</div>
                        <h4 class="fw-extrabold {{ $isTodayTargetMet ? 'text-success' : 'text-danger' }} m-0 mt-1">
                            {{ $todayCount }} / 5 Testimoni
                        </h4>
                        <small class="{{ $isTodayTargetMet ? 'text-success' : 'text-danger' }} fw-semibold" style="font-size: 0.72rem;">
                            {{ $isTodayTargetMet ? 'Target harian terpenuhi' : 'Kurang ' . $todayDeficiency . ' testimoni lagi' }}
                        </small>
                    </div>
                    <div class="icon-box {{ $isTodayTargetMet ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }} rounded-circle p-3 fs-5">
                        <i class="fas {{ $isTodayTargetMet ? 'fa-check-circle' : 'fa-clock' }}"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Progress (Target 125 Review/Bulan) -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card card-custom p-3 border-0 bg-white shadow-sm h-100 border-start border-4 border-primary">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.68rem;">Target Bulanan (125 Review)</div>
                        <span class="badge bg-primary rounded-pill small">{{ $monthProgressPct }}%</span>
                    </div>
                    <h4 class="fw-extrabold text-primary m-0">
                        {{ $monthCount }} / 125
                    </h4>
                    <div class="progress mt-2" style="height: 6px;">
                        <div class="progress-bar bg-primary" style="width: {{ $monthProgressPct }}%;"></div>
                    </div>
                    <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">Sisa target: {{ $monthDeficiency }} review bulan ini</small>
                </div>
            </div>
        </div>

        <!-- Rating Saat Ini / Target Akhir Bulan -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card card-custom p-3 border-0 bg-white shadow-sm h-100 border-start border-4 border-info">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.68rem;">Rating Saat Ini</div>
                        <h4 class="fw-extrabold text-dark m-0 mt-1 d-flex align-items-center gap-1">
                            <i class="fas fa-award text-warning"></i>
                            {{ $baseline && $baseline->rating_saat_ini ? number_format($baseline->rating_saat_ini, 2) : ($baseline ? number_format($baseline->rating_awal_bulan, 2) : '4.80') }}
                        </h4>
                        <small class="text-success fw-semibold" style="font-size: 0.72rem;">Target Cabang: 4.90 ⭐</small>
                    </div>
                    <div class="icon-box bg-warning bg-opacity-10 text-warning rounded-circle p-3 fs-5">
                        <i class="fab fa-google"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Consistency Calendar Bar (Breakdown 1 to DaysInMonth) -->
    <div class="card card-custom p-4 border-0 bg-white shadow-sm mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
            <div>
                <h6 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                    <i class="fas fa-calendar-alt text-primary"></i> Konsistensi Input Harian Bulan {{ $namaBulanList[$bulan] ?? $bulan }} {{ $tahun }}
                </h6>
                <small class="text-muted">Target harian: minimal 5 testimoni per hari kerja (25 hari x 5 = 125 rating/bulan)</small>
            </div>
            <div class="d-flex align-items-center gap-2 small">
                <span class="d-flex align-items-center gap-1"><span class="badge bg-success rounded-circle p-1"> </span> &ge; 5 Tercapai</span>
                <span class="d-flex align-items-center gap-1"><span class="badge bg-warning rounded-circle p-1"> </span> 1 - 4 Kurang</span>
                <span class="d-flex align-items-center gap-1"><span class="badge bg-light text-muted border rounded-circle p-1"> </span> 0 Belum</span>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2">
            @for($d = 1; $d <= $daysInMonth; $d++)
                @php
                    $cnt = $dailyCounts[$d] ?? 0;
                    $isPassed = Carbon\Carbon::create($tahun, $bulan, $d)->isPast() || Carbon\Carbon::create($tahun, $bulan, $d)->isToday();
                    if ($cnt >= 5) {
                        $badgeClass = 'bg-success text-white shadow-sm';
                    } elseif ($cnt > 0) {
                        $badgeClass = 'bg-warning text-dark shadow-sm';
                    } elseif ($isPassed) {
                        $badgeClass = 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25';
                    } else {
                        $badgeClass = 'bg-light text-muted border';
                    }
                @endphp
                <div class="text-center p-2 rounded-3 {{ $badgeClass }}" style="min-width: 48px; flex: 1 0 45px;">
                    <div style="font-size: 0.72rem;" class="fw-semibold">Tgl {{ $d }}</div>
                    <div class="fw-bold fs-6">{{ $cnt }}</div>
                </div>
            @endfor
        </div>
    </div>

    <!-- Gallery & Table Tabs -->
    <div class="card card-custom border-0 bg-white shadow-sm mb-4">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
            <ul class="nav nav-pills gap-2" id="ratingTab" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active rounded-pill px-4 fw-semibold" id="gallery-tab" data-bs-toggle="pill" data-bs-target="#galleryView" type="button">
                        <i class="fas fa-images me-1.5"></i> Galeri Foto Testimoni ({{ $recentTestimonials->count() }})
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link rounded-pill px-4 fw-semibold" id="table-tab" data-bs-toggle="pill" data-bs-target="#tableView" type="button">
                        <i class="fas fa-table me-1.5"></i> Tabel Data Testimoni
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            <div class="tab-content" id="ratingTabContent">
                <!-- Tab 1: Visual Photo Gallery -->
                <div class="tab-pane fade show active" id="galleryView" role="tabpanel">
                    @if($recentTestimonials->count() > 0)
                        <div class="row g-3">
                            @foreach($recentTestimonials as $item)
                                <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                    <div class="card border rounded-3 overflow-hidden h-100 shadow-sm transition-hover">
                                        <div class="position-relative bg-dark text-center" style="height: 180px;">
                                            <img src="{{ $item->foto_testimoni_url }}" class="img-fluid w-100 h-100 cursor-pointer btn-preview-image" style="object-fit: cover;" data-url="{{ $item->foto_testimoni_url }}" data-title="Testimoni: {{ $item->nama_reviewer }} ({{ $item->formatted_tanggal }})">
                                            <span class="badge bg-dark bg-opacity-75 position-absolute top-0 end-0 m-2">
                                                <i class="fas fa-calendar-day me-1"></i> {{ $item->formatted_tanggal }}
                                            </span>
                                            <span class="badge bg-warning text-dark position-absolute top-0 start-0 m-2 fw-bold">
                                                @for($s = 1; $s <= $item->rating_bintang; $s++)★@endfor {{ $item->rating_bintang }}.0
                                            </span>
                                        </div>
                                        <div class="p-3 d-flex flex-column justify-content-between flex-grow-1">
                                            <div>
                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                    <h6 class="fw-bold text-dark mb-0 text-truncate pe-1">{{ $item->nama_reviewer }}</h6>
                                                    {!! $item->jabatan_badge !!}
                                                </div>
                                                <small class="text-muted d-block mb-2"><i class="fas fa-building me-1"></i> {{ $item->branch?->nama_cabang }}</small>
                                                <p class="text-muted small m-0 fst-italic text-truncate" style="max-height: 40px;">
                                                    "{{ $item->komentar_ulasan ?: 'Pelayanan ramah, dealer Yamaha mantap!' }}"
                                                </p>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mt-3 pt-2 border-top">
                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill btn-preview-image" data-url="{{ $item->foto_testimoni_url }}" data-title="Testimoni {{ $item->nama_reviewer }}">
                                                    <i class="fas fa-search-plus me-1"></i> Perbesar
                                                </button>
                                                @if(auth()->user()->hasRole('Super Admin') || (auth()->user()->hasRole('PIC Digital Cabang') && auth()->user()->branch_id == $item->branch_id))
                                                    <div class="d-flex align-items-center gap-1">
                                                        <button type="button" class="btn btn-sm btn-outline-warning rounded-circle btn-edit-testimonial" data-id="{{ $item->id }}" title="Edit Testimoni" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-circle btn-delete-testimonial" data-id="{{ $item->id }}" title="Hapus Testimoni" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-camera fa-3x mb-3 text-secondary text-opacity-50"></i>
                            <h5 class="fw-bold">Belum Ada Foto Testimoni Bulan Ini</h5>
                            <p class="small text-muted mb-3">Klik tombol "Tambah Testimoni (Foto)" di atas untuk mengunggah bukti ulasan Google Rating.</p>
                            <button type="button" class="btn btn-yamaha-primary rounded-pill px-4" onclick="$('#btnTambahTestimoni').click();">
                                <i class="fas fa-plus me-1"></i> Tambah Testimoni Baru
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Tab 2: DataTable -->
                <div class="tab-pane fade" id="tableView" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle w-100" id="googleRatingTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40px;">No</th>
                                    <th>Cabang</th>
                                    <th>Tanggal Review</th>
                                    <th>Nama Reviewer</th>
                                    <th>Petugas / Divisi</th>
                                    <th>Rating Bintang</th>
                                    <th>Komentar Ulasan</th>
                                    <th>Foto Bukti</th>
                                    <th style="width: 70px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal 1: Form Google Rating Awal Bulan (Baseline) -->
<div class="modal fade" id="modalBaseline" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-star me-2"></i> Set Google Rating Awal Bulan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formBaseline">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info py-2 px-3 small mb-3">
                        <i class="fas fa-info-circle me-1"></i> Masukkan nilai rating bintang dan jumlah review di Google Business Profile pada tanggal 1 awal bulan ini.
                    </div>

                    @if(auth()->user()->hasRole('PIC Digital Cabang'))
                        <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id }}">
                    @else
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Pilih Cabang <span class="text-danger">*</span></label>
                            <select name="branch_id" class="form-select" required>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>{{ $b->nama_cabang }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Bulan</label>
                            <select name="bulan" class="form-select" required>
                                @foreach($namaBulanList as $num => $nama)
                                    <option value="{{ $num }}" {{ $bulan == $num ? 'selected' : '' }}>{{ $nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Tahun</label>
                            <select name="tahun" class="form-select" required>
                                @for($y = 2024; $y <= 2028; $y++)
                                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Rating Awal Bulan <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="1.00" max="5.00" name="rating_awal_bulan" class="form-control" value="{{ $baseline ? $baseline->rating_awal_bulan : '4.80' }}" required>
                            <small class="text-muted" style="font-size: 0.72rem;">Contoh: 4.80 atau 4.85</small>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Total Review Awal Bulan <span class="text-danger">*</span></label>
                            <input type="number" min="0" name="total_review_awal_bulan" class="form-control" value="{{ $baseline ? $baseline->total_review_awal_bulan : '0' }}" required>
                            <small class="text-muted" style="font-size: 0.72rem;">Jumlah ulasan di Google</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Rating Saat Ini (Opsional)</label>
                        <input type="number" step="0.01" min="1.00" max="5.00" name="rating_saat_ini" class="form-control" value="{{ $baseline && $baseline->rating_saat_ini ? $baseline->rating_saat_ini : '' }}" placeholder="Contoh: 4.90">
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Catatan</label>
                        <textarea name="catatan" class="form-control" rows="2" placeholder="Target perbaikan rating atau fokus ulasan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-semibold" id="btnSubmitBaseline">
                        <i class="fas fa-save me-1"></i> Simpan Baseline
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 2: Form Tambah Testimoni Google Rating (Foto) -->
<div class="modal fade" id="modalTestimoni" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient text-white" style="background: linear-gradient(135deg, #002266 0%, #003399 100%);">
                <h5 class="modal-title fw-bold">
                    <i class="fab fa-google text-warning me-2"></i> Tambah Testimoni Google Rating
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formTestimoni" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <!-- Rule Reminder Warning Box -->
                    <div class="alert alert-warning border-0 p-3 rounded-3 mb-4 d-flex align-items-center gap-3">
                        <i class="fas fa-bell text-warning fa-2x"></i>
                        <div class="small">
                            <strong class="text-dark d-block">Penting: Target Minimal 5 Testimoni Per Hari (125 Review Per Bulan)!</strong>
                            Pastikan Anda mengunggah minimal 5 ulasan per hari. Anda dapat memilih beberapa foto screenshot sekaligus dalam satu kali upload di bawah ini.
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        @if(auth()->user()->hasRole('PIC Digital Cabang'))
                            <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id }}">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">Cabang</label>
                                <input type="text" class="form-control" value="{{ auth()->user()->branch?->nama_cabang }}" readonly>
                            </div>
                        @else
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">Pilih Cabang <span class="text-danger">*</span></label>
                                <select name="branch_id" class="form-select" required>
                                    @foreach($branches as $b)
                                        <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>{{ $b->nama_cabang }} ({{ $b->kode }})</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Tanggal Testimoni <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control" value="{{ now()->toDateString() }}" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold">Nama Konsumen / Reviewer</label>
                            <input type="text" name="nama_reviewer" class="form-control" placeholder="Contoh: Bpk. Hendra Gunawan" value="Konsumen Yamaha">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold">Petugas / Divisi <span class="text-danger">*</span></label>
                            <select name="jabatan" class="form-select" required>
                                <option value="PIC Digital" selected>PIC Digital</option>
                                <option value="Sales Counter">Sales Counter</option>
                                <option value="Service Counter">Service Counter</option>
                                <option value="Admin Part">Admin Part</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold">Rating Bintang <span class="text-danger">*</span></label>
                            <select name="rating_bintang" class="form-select" required>
                                <option value="5" selected>⭐⭐⭐⭐⭐ 5 Bintang (Sangat Puas)</option>
                                <option value="4">⭐⭐⭐⭐ 4 Bintang (Puas)</option>
                                <option value="3">⭐⭐⭐ 3 Bintang (Cukup)</option>
                                <option value="2">⭐⭐ 2 Bintang (Kurang)</option>
                                <option value="1">⭐ 1 Bintang (Tidak Puas)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Upload Foto Testimoni (Multiple Support) -->
                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <label class="form-label small fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                            <i class="fas fa-images text-primary"></i> Upload Screenshot Testimoni Google Rating (Bisa Banyak Foto) <span class="text-danger">*</span>
                        </label>
                        <input type="file" name="fotos[]" id="input_fotos" class="form-control mb-2" accept="image/*" multiple required>
                        <div class="text-muted small" style="font-size: 0.75rem;">
                            <i class="fas fa-info-circle me-1"></i> Tahan tombol <code>Ctrl</code> / <code>Shift</code> di laptop Anda untuk memilih beberapa foto review sekaligus (misal: 5 foto sekaligus untuk memenuhi target harian). Format: JPG/PNG maks. 10MB per foto.
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Komentar / Ulasan Konsumen (Opsional)</label>
                        <textarea name="komentar_ulasan" class="form-control" rows="2" placeholder="Salin kutipan review konsumen di Google Maps jika ada..."></textarea>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-yamaha-primary rounded-pill px-4" id="btnSubmitTestimoni">
                        <i class="fas fa-cloud-upload-alt me-1.5"></i> Simpan Testimoni
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit: Form Edit Testimoni Google Rating -->
<div class="modal fade" id="modalEditTestimoni" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient text-white" style="background: linear-gradient(135deg, #002266 0%, #003399 100%);">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-edit text-warning me-2"></i> Edit Testimoni Google Rating
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditTestimoni" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" id="edit_testimoni_id">
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        @if(auth()->user()->hasRole('PIC Digital Cabang'))
                            <input type="hidden" name="branch_id" id="edit_branch_id_val" value="{{ auth()->user()->branch_id }}">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">Cabang</label>
                                <input type="text" class="form-control" value="{{ auth()->user()->branch?->nama_cabang }}" readonly>
                            </div>
                        @else
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">Pilih Cabang <span class="text-danger">*</span></label>
                                <select name="branch_id" id="edit_branch_id" class="form-select" required>
                                    @foreach($branches as $b)
                                        <option value="{{ $b->id }}">{{ $b->nama_cabang }} ({{ $b->kode }})</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold">Tanggal Testimoni <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" id="edit_tanggal" class="form-control" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold">Nama Konsumen / Reviewer <span class="text-danger">*</span></label>
                            <input type="text" name="nama_reviewer" id="edit_nama_reviewer" class="form-control" placeholder="Contoh: Bpk. Hendra Gunawan" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold">Petugas / Divisi <span class="text-danger">*</span></label>
                            <select name="jabatan" id="edit_jabatan" class="form-select" required>
                                <option value="PIC Digital">PIC Digital</option>
                                <option value="Sales Counter">Sales Counter</option>
                                <option value="Service Counter">Service Counter</option>
                                <option value="Admin Part">Admin Part</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold">Rating Bintang <span class="text-danger">*</span></label>
                            <select name="rating_bintang" id="edit_rating_bintang" class="form-select" required>
                                <option value="5">⭐⭐⭐⭐⭐ 5 Bintang (Sangat Puas)</option>
                                <option value="4">⭐⭐⭐⭐ 4 Bintang (Puas)</option>
                                <option value="3">⭐⭐⭐ 3 Bintang (Cukup)</option>
                                <option value="2">⭐⭐ 2 Bintang (Kurang)</option>
                                <option value="1">⭐ 1 Bintang (Tidak Puas)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Foto Screenshot Testimoni Saat Ini & Ganti Foto -->
                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <label class="form-label small fw-bold text-dark mb-2 d-flex align-items-center gap-2">
                            <i class="fas fa-image text-primary"></i> Foto Screenshot Testimoni
                        </label>
                        <div class="row align-items-center g-3">
                            <div class="col-auto" id="edit_current_photo_wrapper">
                                <div class="position-relative border rounded overflow-hidden shadow-xs" style="width: 100px; height: 80px; background: #222;">
                                    <img src="" id="edit_current_photo_img" class="w-100 h-100 cursor-pointer btn-preview-image" style="object-fit: cover;" title="Klik untuk memperbesar">
                                </div>
                            </div>
                            <div class="col">
                                <label class="form-label small text-muted mb-1">Ganti Foto Screenshot (Opsional):</label>
                                <input type="file" name="foto_testimoni" id="edit_foto_testimoni" class="form-control" accept="image/*">
                                <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                                    <i class="fas fa-info-circle me-1"></i> Biarkan kosong jika tidak ingin mengubah foto screenshot. Format: JPG/PNG maks. 10MB.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Komentar / Ulasan Konsumen (Opsional)</label>
                        <textarea name="komentar_ulasan" id="edit_komentar_ulasan" class="form-control" rows="3" placeholder="Salin kutipan review konsumen di Google Maps jika ada..."></textarea>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-yamaha-primary rounded-pill px-4" id="btnSubmitEditTestimoni">
                        <i class="fas fa-save me-1.5"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 3: Lightbox Image Zoom -->
<div class="modal fade" id="modalImagePreview" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow bg-dark text-white">
            <div class="modal-header border-bottom border-secondary py-2">
                <h6 class="modal-title m-0 text-truncate" id="modalImagePreviewTitle">Screenshot Testimoni Google</h6>
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
    // 1. Initialize DataTable
    const table = $('#googleRatingTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('reports.google-rating.index') }}",
            data: function (d) {
                d.action = 'get_testimonials';
                d.tahun = "{{ $tahun }}";
                d.bulan = "{{ $bulan }}";
                d.branch_id = "{{ $branchId }}";
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'cabang', name: 'branch.nama_cabang' },
            { data: 'tanggal', name: 'tanggal' },
            { data: 'nama_reviewer', name: 'nama_reviewer' },
            { data: 'jabatan', name: 'jabatan' },
            { data: 'rating_bintang', name: 'rating_bintang' },
            { data: 'komentar_ulasan', name: 'komentar_ulasan' },
            { data: 'foto_btn', name: 'foto_btn', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[2, 'desc']],
        pageLength: 15,
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Belum ada testimoni Google Rating yang diinput",
            info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ testimoni",
            infoEmpty: "Menampilkan 0 data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            paginate: { first: "Awal", last: "Akhir", next: "Lanjut", previous: "Kembali" }
        }
    });

    // 2. Open Baseline Modal
    $('#btnOpenBaselineModal').on('click', function() {
        $('#modalBaseline').modal('show');
    });

    // 3. Submit Baseline Form
    $('#formBaseline').on('submit', function(e) {
        e.preventDefault();
        const submitBtn = $('#btnSubmitBaseline');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');

        $.ajax({
            url: "{{ route('reports.google-rating.baseline.store') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(res) {
                submitBtn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Simpan Baseline');
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    $('#modalBaseline').modal('hide');
                    setTimeout(() => location.reload(), 1200);
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Simpan Baseline');
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan sistem'
                });
            }
        });
    });

    // 4. Open Testimoni Modal
    $('#btnTambahTestimoni').on('click', function() {
        $('#formTestimoni')[0].reset();
        $('#modalTestimoni').modal('show');
    });

    // 5. Submit Testimoni Form (Supports Multi-Upload)
    $('#formTestimoni').on('submit', function(e) {
        e.preventDefault();
        const submitBtn = $('#btnSubmitTestimoni');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Mengunggah...');

        const formData = new FormData(this);

        $.ajax({
            url: "{{ route('reports.google-rating.testimonial.store') }}",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                submitBtn.prop('disabled', false).html('<i class="fas fa-cloud-upload-alt me-1.5"></i> Simpan Testimoni');
                if (res.success) {
                    Swal.fire({
                        icon: res.is_target_met ? 'success' : 'info',
                        title: res.is_target_met ? 'Luar Biasa!' : 'Testimoni Disimpan!',
                        html: `<p>${res.message}</p><p class="fw-bold mb-0">${res.warning_message}</p>`,
                        timer: 2500,
                        showConfirmButton: false
                    });
                    $('#modalTestimoni').modal('hide');
                    setTimeout(() => location.reload(), 1500);
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html('<i class="fas fa-cloud-upload-alt me-1.5"></i> Simpan Testimoni');
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
                    title: 'Gagal Mengunggah!',
                    html: errHtml
                });
            }
        });
    });

    // 6. Open Edit Testimonial Modal
    $(document).on('click', '.btn-edit-testimonial', function() {
        const id = $(this).data('id');
        const btn = $(this);
        const originalHtml = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: "{{ url('/reports/google-rating/testimonial') }}/" + id,
            type: "GET",
            success: function(res) {
                btn.prop('disabled', false).html(originalHtml);
                if (res.success && res.data) {
                    const item = res.data;
                    $('#edit_testimoni_id').val(item.id);
                    if ($('#edit_branch_id').length) {
                        $('#edit_branch_id').val(item.branch_id);
                    }
                    if ($('#edit_branch_id_val').length) {
                        $('#edit_branch_id_val').val(item.branch_id);
                    }
                    if (item.tanggal) {
                        const dateOnly = item.tanggal.substring(0, 10);
                        $('#edit_tanggal').val(dateOnly);
                    }
                    $('#edit_nama_reviewer').val(item.nama_reviewer);
                    $('#edit_jabatan').val(item.jabatan || 'PIC Digital');
                    $('#edit_rating_bintang').val(item.rating_bintang);
                    $('#edit_komentar_ulasan').val(item.komentar_ulasan || '');
                    $('#edit_foto_testimoni').val('');

                    if (res.foto_url) {
                        $('#edit_current_photo_img').attr('src', res.foto_url).data('url', res.foto_url).data('title', 'Screenshot Testimoni: ' + item.nama_reviewer);
                        $('#edit_current_photo_wrapper').show();
                    } else {
                        $('#edit_current_photo_wrapper').hide();
                    }

                    $('#modalEditTestimoni').modal('show');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html(originalHtml);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memuat Data',
                    text: xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan sistem saat mengambil data ulasan.'
                });
            }
        });
    });

    // 7. Submit Edit Testimonial Form
    $('#formEditTestimoni').on('submit', function(e) {
        e.preventDefault();
        const id = $('#edit_testimoni_id').val();
        const submitBtn = $('#btnSubmitEditTestimoni');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan Perubahan...');

        const formData = new FormData(this);

        $.ajax({
            url: "{{ url('/reports/google-rating/testimonial') }}/" + id + "/update",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                submitBtn.prop('disabled', false).html('<i class="fas fa-save me-1.5"></i> Simpan Perubahan');
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    $('#modalEditTestimoni').modal('hide');
                    setTimeout(() => location.reload(), 1200);
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html('<i class="fas fa-save me-1.5"></i> Simpan Perubahan');
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

    // 8. Delete Testimonial
    $(document).on('click', '.btn-delete-testimonial', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Hapus Testimoni Ini?',
            text: 'Screenshot dan data testimoni akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('/reports/google-rating/testimonial') }}/" + id,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(res) {
                        Swal.fire({ icon: 'success', title: 'Terhapus!', text: res.message, timer: 1500, showConfirmButton: false });
                        setTimeout(() => location.reload(), 1200);
                    },
                    error: function(xhr) {
                        Swal.fire({ icon: 'error', title: 'Gagal!', text: xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan sistem' });
                    }
                });
            }
        });
    });

    // 7. Lightbox Preview
    $(document).on('click', '.btn-preview-image', function() {
        const url = $(this).data('url');
        const title = $(this).data('title') || 'Screenshot Testimoni Google';
        $('#modalImagePreviewTitle').text(title);
        $('#modalImagePreviewImg').attr('src', url);
        $('#modalImagePreview').modal('show');
    });
});
</script>
@endpush
