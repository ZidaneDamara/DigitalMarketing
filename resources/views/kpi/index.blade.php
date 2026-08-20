@extends('layouts.app')

@section('title', 'Target KPI Management')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h5 class="fw-bold text-dark m-0"><i class="fas fa-bullseye text-warning me-2"></i> Pengaturan Target KPI Bulanan Cabang</h5>
        <div class="text-muted small">Target KPI berlaku seragam untuk seluruh cabang dan disesuaikan setiap bulan</div>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#modal-copy-kpi">
            <i class="fas fa-copy me-1"></i> Copy KPI Bulan Sebelumnya
        </button>
    </div>
</div>

<div class="card card-custom p-3 mb-4">
    <form action="{{ route('kpis.index') }}" method="GET" class="row g-2 g-md-3 align-items-end">
        <div class="col-6 col-md-5">
            <label class="form-label fw-semibold small text-muted">Pilih Tahun Target</label>
            <select name="tahun" class="form-select">
                @for($y = 2024; $y <= 2028; $y++)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="col-6 col-md-5">
            <label class="form-label fw-semibold small text-muted">Pilih Bulan Target</label>
            <select name="bulan" class="form-select">
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-2">
            <button type="submit" class="btn btn-yamaha-primary w-100"><i class="fas fa-search me-1"></i> Muat KPI</button>
        </div>
    </form>
</div>

<form action="{{ route('kpis.store') }}" method="POST">
    @csrf
    <input type="hidden" name="tahun" value="{{ $tahun }}">
    <input type="hidden" name="bulan" value="{{ $bulan }}">

    <div class="row g-4 mb-4">
        <!-- Instagram KPI Target -->
        <div class="col-md-6">
            <div class="card card-custom p-4 h-100 border-top border-4 border-danger">
                <h6 class="fw-bold text-danger mb-3"><i class="fab fa-instagram me-2"></i> Target Instagram (Bulanan)</h6>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Feed Post Target</label>
                        <input type="number" name="ig_feed_target" class="form-control" value="{{ old('ig_feed_target', $selectedKpi?->target?->ig_feed_target ?? 30) }}" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Reels Video Target</label>
                        <input type="number" name="ig_reels_target" class="form-control" value="{{ old('ig_reels_target', $selectedKpi?->target?->ig_reels_target ?? 20) }}" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Story Upload Target</label>
                        <input type="number" name="ig_story_target" class="form-control" value="{{ old('ig_story_target', $selectedKpi?->target?->ig_story_target ?? 60) }}" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Penambahan Followers</label>
                        <input type="number" name="ig_followers_target" class="form-control" value="{{ old('ig_followers_target', $selectedKpi?->target?->ig_followers_target ?? 1500) }}" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Facebook KPI Target -->
        <div class="col-md-6">
            <div class="card card-custom p-4 h-100 border-top border-4 border-primary">
                <h6 class="fw-bold text-primary mb-3"><i class="fab fa-facebook me-2"></i> Target Facebook (Bulanan)</h6>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Facebook Post Target</label>
                        <input type="number" name="fb_post_target" class="form-control" value="{{ old('fb_post_target', $selectedKpi?->target?->fb_post_target ?? 30) }}" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Marketplace Listing</label>
                        <input type="number" name="fb_marketplace_target" class="form-control" value="{{ old('fb_marketplace_target', $selectedKpi?->target?->fb_marketplace_target ?? 45) }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Penambahan Followers FB</label>
                        <input type="number" name="fb_followers_target" class="form-control" value="{{ old('fb_followers_target', $selectedKpi?->target?->fb_followers_target ?? 800) }}" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- TikTok KPI Target -->
        <div class="col-md-6">
            <div class="card card-custom p-4 h-100 border-top border-4 border-dark">
                <h6 class="fw-bold text-dark mb-3"><i class="fab fa-tiktok me-2"></i> Target TikTok (Bulanan)</h6>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">TikTok Video Post</label>
                        <input type="number" name="tiktok_post_target" class="form-control" value="{{ old('tiktok_post_target', $selectedKpi?->target?->tiktok_post_target ?? 25) }}" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">TikTok Live Stream (Sesi)</label>
                        <input type="number" name="tiktok_live_target" class="form-control" value="{{ old('tiktok_live_target', $selectedKpi?->target?->tiktok_live_target ?? 15) }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Penambahan Followers TikTok</label>
                        <input type="number" name="tiktok_followers_target" class="form-control" value="{{ old('tiktok_followers_target', $selectedKpi?->target?->tiktok_followers_target ?? 2000) }}" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Google Business Target -->
        <div class="col-md-6">
            <div class="card card-custom p-4 h-100 border-top border-4 border-success">
                <h6 class="fw-bold text-success mb-3"><i class="fab fa-google me-2"></i> Target Google Business (Bulanan)</h6>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Target Rating Minimal</label>
                        <input type="number" step="0.1" name="google_rating_target" class="form-control" value="{{ old('google_rating_target', $selectedKpi?->target?->google_rating_target ?? 4.8) }}" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Penambahan Ulasan Baru</label>
                        <input type="number" name="google_review_target" class="form-control" value="{{ old('google_review_target', $selectedKpi?->target?->google_review_target ?? 50) }}" required>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-end mb-4">
        <button type="submit" class="btn btn-yamaha-primary px-5 py-2.5 fs-6 shadow-sm">
            <i class="fas fa-save me-2"></i> SIMPAN TARGET KPI {{ date('F', mktime(0, 0, 0, $bulan, 1)) }} {{ $tahun }}
        </button>
    </div>
</form>

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
                    <p class="text-muted small">Salin konfigurasi target KPI dari bulan asal ke bulan tujuan secara instan:</p>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Bulan Asal</label>
                            <select name="from_bulan" class="form-select" required>
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ ($bulan == 1 ? 12 : $bulan - 1) == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
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
                                    <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
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
