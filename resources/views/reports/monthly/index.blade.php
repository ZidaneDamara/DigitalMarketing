@extends('layouts.app')

@section('title', 'Monthly Insight & Screenshot Center')

@section('content')
<div class="card card-custom p-3 mb-4">
    <form action="{{ route('reports.monthly.index') }}" method="GET" class="row g-2 g-md-3 align-items-end">
        <div class="col-6 col-md-3">
            <label class="form-label fw-semibold small text-muted">Tahun</label>
            <select name="tahun" class="form-select">
                @for($y = 2024; $y <= 2028; $y++)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="col-6 col-md-3">
            <label class="form-label fw-semibold small text-muted">Bulan</label>
            <select name="bulan" class="form-select">
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                    </option>
                @endforeach
            </select>
        </div>
        @unless(auth()->user()->hasRole('PIC Digital Cabang'))
        <div class="col-12 col-md-4">
            <label class="form-label fw-semibold small text-muted">Filter Cabang</label>
            <select name="branch_id" class="form-select">
                <option value="">-- Semua Cabang --</option>
                @foreach($branches as $b)
                    <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>{{ $b->nama_cabang }}</option>
                @endforeach
            </select>
        </div>
        @endunless
        <div class="col-12 col-md-2">
            <button type="submit" class="btn btn-yamaha-primary w-100"><i class="fas fa-filter me-1"></i> Terapkan</button>
        </div>
    </form>
</div>

<!-- Form Input Monthly Insight (For PIC Digital or Admin) -->
@if(auth()->user()->hasRole('PIC Digital Cabang') || auth()->user()->hasRole('Super Admin'))
<div class="card card-custom p-4 mb-4 border-top border-4 border-danger">
    <h5 class="fw-bold text-dark mb-3"><i class="fas fa-file-invoice text-danger me-2"></i> Input Monthly Insight & Upload Screenshot (Akhir Bulan)</h5>

    <form action="{{ route('reports.monthly.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="tahun" value="{{ $tahun }}">
        <input type="hidden" name="bulan" value="{{ $bulan }}">

        @if(auth()->user()->hasRole('Super Admin'))
        <div class="mb-3">
            <label class="form-label fw-semibold">Pilih Cabang Target</label>
            <select name="branch_id" class="form-select" required>
                <option value="">-- Pilih Cabang --</option>
                @foreach($branches as $b)
                    <option value="{{ $b->id }}">{{ $b->nama_cabang }} ({{ $b->kode }})</option>
                @endforeach
            </select>
        </div>
        @else
        <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id }}">
        @endif

        <div class="row g-4 mb-4">
            <!-- Instagram Insight -->
            <div class="col-12 col-lg-6">
                <div class="p-3 bg-light rounded-3 border">
                    <h6 class="fw-bold text-danger mb-3"><i class="fab fa-instagram me-1"></i> Instagram Insight</h6>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label small">Total Views</label>
                            <input type="number" name="ig_views" class="form-control" value="{{ $selectedInsight?->ig_views ?? 0 }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Reach</label>
                            <input type="number" name="ig_reach" class="form-control" value="{{ $selectedInsight?->ig_reach ?? 0 }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Accounts Reached</label>
                            <input type="number" name="ig_accounts_reached" class="form-control" value="{{ $selectedInsight?->ig_accounts_reached ?? 0 }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Profile Visit</label>
                            <input type="number" name="ig_profile_visits" class="form-control" value="{{ $selectedInsight?->ig_profile_visits ?? 0 }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Total Followers IG</label>
                            <input type="number" name="ig_total_followers" class="form-control" value="{{ $selectedInsight?->ig_total_followers ?? 0 }}" required>
                        </div>
                        <div class="col-3">
                            <label class="form-label small">Male %</label>
                            <input type="number" step="0.1" name="ig_male_pct" class="form-control" value="{{ $selectedInsight?->ig_male_pct ?? 60 }}" required>
                        </div>
                        <div class="col-3">
                            <label class="form-label small">Female %</label>
                            <input type="number" step="0.1" name="ig_female_pct" class="form-control" value="{{ $selectedInsight?->ig_female_pct ?? 40 }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Top 3 Demografi Usia</label>
                            <input type="text" name="ig_top_age" class="form-control" placeholder="18-24 (40%), 25-34 (35%)" value="{{ $selectedInsight?->ig_top_age ?? '' }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Top 3 Kota Demografi</label>
                            <input type="text" name="ig_top_cities" class="form-control" placeholder="Bandar Lampung, Metro" value="{{ $selectedInsight?->ig_top_cities ?? '' }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-danger"><i class="fas fa-upload me-1"></i> Upload Screenshot Instagram Insight</label>
                            <input type="file" name="screenshot_ig" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Facebook & TikTok Insight -->
            <div class="col-12 col-lg-6">
                <div class="p-3 bg-light rounded-3 border mb-3">
                    <h6 class="fw-bold text-primary mb-3"><i class="fab fa-facebook me-1"></i> Facebook Insight</h6>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label small">Total Views FB</label>
                            <input type="number" name="fb_views" class="form-control" value="{{ $selectedInsight?->fb_views ?? 0 }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Total Followers FB</label>
                            <input type="number" name="fb_total_followers" class="form-control" value="{{ $selectedInsight?->fb_total_followers ?? 0 }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-primary"><i class="fas fa-upload me-1"></i> Upload Screenshot Facebook Insight</label>
                            <input type="file" name="screenshot_fb" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>

                <div class="p-3 bg-light rounded-3 border">
                    <h6 class="fw-bold text-dark mb-3"><i class="fab fa-tiktok me-1"></i> TikTok Analytics</h6>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label small">Total Views TikTok</label>
                            <input type="number" name="tiktok_views" class="form-control" value="{{ $selectedInsight?->tiktok_views ?? 0 }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Total Followers TikTok</label>
                            <input type="number" name="tiktok_total_followers" class="form-control" value="{{ $selectedInsight?->tiktok_total_followers ?? 0 }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-dark"><i class="fas fa-upload me-1"></i> Upload Screenshot TikTok Analytics</label>
                            <input type="file" name="screenshot_tiktok" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Google Business Profile -->
            <div class="col-12">
                <div class="p-3 bg-light rounded-3 border">
                    <h6 class="fw-bold text-success mb-3"><i class="fab fa-google me-1"></i> Google Business Profile</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small">Rating Akumulasi</label>
                            <input type="number" step="0.1" name="google_total_rating" class="form-control" value="{{ $selectedInsight?->google_total_rating ?? 4.9 }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Jumlah Total Review</label>
                            <input type="number" name="google_total_reviews" class="form-control" value="{{ $selectedInsight?->google_total_reviews ?? 0 }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-success"><i class="fas fa-upload me-1"></i> Screenshot Google Business</label>
                            <input type="file" name="screenshot_google" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-yamaha-primary px-5 py-2.5 rounded-pill shadow-sm">
            <i class="fas fa-save me-2"></i> SIMPAN MONTHLY INSIGHT & SCREENSHOTS
        </button>
    </form>
</div>
@endif

<!-- List of Monthly Insights & Screenshot Gallery Lightbox -->
<div class="card card-custom p-4">
    <h6 class="fw-bold text-dark mb-4"><i class="fas fa-images text-primary me-2"></i> Screenshot Center & Monthly Insight Galeri Cabang ({{ date('F', mktime(0, 0, 0, $bulan, 1)) }} {{ $tahun }})</h6>

    <div class="row g-4">
        @forelse($insights as $ins)
        <div class="col-md-6 col-lg-4">
            <div class="card card-custom h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="fw-bold text-dark m-0">{{ $ins->branch->nama_cabang }}</h6>
                    <span class="badge bg-light text-dark border">{{ $ins->branch->kode }}</span>
                </div>

                <div class="small text-muted mb-3">
                    IG Views: <strong>{{ number_format($ins->ig_views) }}</strong> | TikTok Views: <strong>{{ number_format($ins->tiktok_views) }}</strong>
                </div>

                <!-- Screenshots Gallery Thumbnails -->
                <div class="row g-2">
                    @forelse($ins->screenshots as $ss)
                    <div class="col-6">
                        <div class="position-relative border rounded-3 overflow-hidden bg-light" style="height: 110px;">
                            <img src="https://picsum.photos/300/200?random={{ $ss->id }}" class="w-100 h-100 object-fit-cover btn-preview-img" data-img="https://picsum.photos/800/600?random={{ $ss->id }}" data-title="{{ $ss->kategori }} - {{ $ins->branch->nama_cabang }}" style="cursor: pointer;">
                            <span class="position-absolute bottom-0 start-0 w-100 bg-dark bg-opacity-75 text-white text-truncate p-1 small" style="font-size: 0.65rem;">
                                {{ $ss->kategori }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center text-muted py-3 small">Belum ada screenshot diupload</div>
                    @endforelse
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center text-muted py-5">
            <i class="fas fa-info-circle fa-2x mb-2 d-block"></i> Belum ada laporan Monthly Insight untuk periode ini.
        </div>
        @endforelse
    </div>
</div>

<!-- Modal Lightbox Preview Screenshot -->
<div class="modal fade" id="modal-lightbox" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 bg-dark text-white rounded-4">
            <div class="modal-header border-bottom border-secondary">
                <h5 class="modal-title fw-bold" id="lightbox-title">Preview Screenshot</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 text-center">
                <img id="lightbox-img" src="" class="img-fluid rounded-bottom-4" style="max-height: 80vh;">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('.btn-preview-img').click(function () {
            const imgSrc = $(this).data('img');
            const title = $(this).data('title');

            $('#lightbox-img').attr('src', imgSrc);
            $('#lightbox-title').text(title);
            $('#modal-lightbox').modal('show');
        });
    });
</script>
@endpush
