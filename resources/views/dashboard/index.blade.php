@extends('layouts.app')

@section('title', 'Executive Analytics Dashboard')

@section('content')
<!-- Filter Bar -->
<div class="card card-custom p-3 mb-4">
    <form action="{{ route('dashboard') }}" method="GET" class="row g-2 g-md-3 align-items-end">
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
            <button type="submit" class="btn btn-yamaha-primary w-100">
                <i class="fas fa-filter me-1"></i> Terapkan
            </button>
        </div>
    </form>
</div>

<!-- Quick Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card card-custom stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">TOTAL CABANG AKTIF</div>
                    <div class="fs-3 fw-bold text-dark mt-1">{{ $metrics['total_branches'] }} Cabang</div>
                </div>
                <div class="icon-box bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-store"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card card-custom stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">BELUM INPUT HARI INI</div>
                    <div class="fs-3 fw-bold text-danger mt-1">{{ $metrics['missing_count_today'] }} Cabang</div>
                </div>
                <div class="icon-box bg-danger bg-opacity-10 text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card card-custom stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">ACHIEVEMENT KPI</div>
                    <div class="fs-3 fw-bold text-dark mt-1">{{ $metrics['overall_achievement'] }}%</div>
                </div>
                <div class="icon-box bg-success bg-opacity-10 text-success">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <div class="mt-2">
                <span class="badge {{ $metrics['status_badge']['bg_class'] }} rounded-pill px-2.5 py-1">
                    {{ $metrics['status_badge']['label'] }}
                </span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card card-custom stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">TOTAL CONTENT POSTS</div>
                    <div class="fs-3 fw-bold text-dark mt-1">{{ number_format($metrics['total_posts']) }}</div>
                </div>
                <div class="icon-box bg-info bg-opacity-10 text-info">
                    <i class="fas fa-paper-plane"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card card-custom stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">FOLLOWERS GROWTH</div>
                    <div class="fs-3 fw-bold text-success mt-1">+{{ number_format($metrics['followers_growth']) }}</div>
                </div>
                <div class="icon-box bg-success bg-opacity-10 text-success">
                    <i class="fas fa-user-plus"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card card-custom stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">TOTAL VIEWS</div>
                    <div class="fs-3 fw-bold text-dark mt-1">{{ number_format($metrics['total_views']) }}</div>
                </div>
                <div class="icon-box bg-warning bg-opacity-10 text-warning">
                    <i class="fas fa-eye"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card card-custom stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">GOOGLE BUSINESS RATING</div>
                    <div class="fs-3 fw-bold text-dark mt-1">⭐ {{ $metrics['avg_google_rating'] }} / 5.0</div>
                </div>
                <div class="icon-box bg-primary bg-opacity-10 text-primary">
                    <i class="fab fa-google"></i>
                </div>
            </div>
            <div class="mt-2 text-muted small">+{{ $metrics['google_reviews'] }} Ulasan Baru</div>
        </div>
    </div>
</div>

<!-- Missing Daily Report Alert Widget -->
@if($metrics['missing_count_today'] > 0)
<div class="card card-custom border-start border-4 border-danger p-3 mb-4 bg-danger bg-opacity-10">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h6 class="fw-bold text-danger mb-1"><i class="fas fa-bell me-2"></i> WIDGET PERINGATAN: CABANG BELUM INPUT DAILY REPORT HARI INI</h6>
            <div class="text-dark small">Terdapat <strong>{{ $metrics['missing_count_today'] }} cabang</strong> yang belum mengirimkan Laporan Harian untuk tanggal berjalan:</div>
            <div class="mt-2 d-flex flex-wrap gap-2">
                @foreach($metrics['missing_branches_today'] as $mb)
                    <span class="badge bg-danger px-3 py-2 fs-6"><i class="fas fa-store me-1"></i> {{ $mb->nama_cabang }} ({{ $mb->kode }})</span>
                @endforeach
            </div>
        </div>
        <div>
            <a href="{{ route('reports.daily.index') }}" class="btn btn-danger font-weight-bold px-4 rounded-pill"><i class="fas fa-edit me-1"></i> Form Input Report</a>
        </div>
    </div>
</div>
@endif

<!-- Analytics Charts Section -->
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold text-dark m-0"><i class="fas fa-chart-area text-primary me-2"></i> Trend Activity Posting (Harian)</h6>
                <span class="badge bg-light text-dark">Instagram, Facebook, TikTok</span>
            </div>
            <div style="height: 300px;">
                <canvas id="postingTrendChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card card-custom p-4 h-100">
            <h6 class="fw-bold text-dark mb-3"><i class="fas fa-chart-pie text-danger me-2"></i> Distribusi Platform Views</h6>
            <div style="height: 270px;" class="d-flex align-items-center justify-content-center">
                <canvas id="platformPieChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Leaderboard Summary Preview -->
<div class="card card-custom p-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="fw-bold text-dark m-0"><i class="fas fa-trophy text-warning me-2"></i> Leaderboard Achievement Cabang (Ringkasan Top Ranking)</h6>
        <a href="{{ route('leaderboard.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">Lihat Selengkapnya <i class="fas fa-arrow-right ms-1"></i></a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Rank</th>
                    <th>Kode</th>
                    <th>Nama Cabang</th>
                    <th>Area</th>
                    <th>Total Post</th>
                    <th>Followers Gained</th>
                    <th>Achievement KPI</th>
                    <th>Status Tier</th>
                </tr>
            </thead>
            <tbody>
                @foreach($metrics['leaderboard']->take(5) as $item)
                <tr>
                    <td>
                        @if($item['rank'] == 1)
                            <span class="badge bg-warning text-dark rounded-circle p-2"><i class="fas fa-crown"></i> 1</span>
                        @elseif($item['rank'] == 2)
                            <span class="badge bg-secondary text-white rounded-circle p-2">2</span>
                        @elseif($item['rank'] == 3)
                            <span class="badge bg-danger text-white rounded-circle p-2">3</span>
                        @else
                            <span class="fw-bold text-muted ms-2">{{ $item['rank'] }}</span>
                        @endif
                    </td>
                    <td class="fw-semibold text-primary">{{ $item['kode'] }}</td>
                    <td class="fw-bold text-dark">{{ $item['nama_cabang'] }}</td>
                    <td>{{ $item['area'] }}</td>
                    <td><span class="badge bg-light text-dark border">{{ $item['total_posts'] }} Post</span></td>
                    <td><span class="text-success fw-bold">+{{ number_format($item['total_followers_gained']) }}</span></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height: 8px;">
                                <div class="progress-bar" role="progressbar" style="width: {{ min(100, $item['achievement_pct']) }}%; background-color: {{ $item['badge']['hex'] }};"></div>
                            </div>
                            <span class="fw-bold" style="min-width: 50px;">{{ $item['achievement_pct'] }}%</span>
                        </div>
                    </td>
                    <td>
                        <span class="badge {{ $item['badge']['bg_class'] }} rounded-pill px-3 py-1.5">
                            {{ $item['badge']['label'] }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Line Chart: Posting Trend
        const ctxPosting = document.getElementById('postingTrendChart').getContext('2d');
        new Chart(ctxPosting, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [
                    {
                        label: 'Instagram Posts',
                        data: {!! json_encode($igPostsSeries) !!},
                        borderColor: '#E60012',
                        backgroundColor: 'rgba(230, 0, 18, 0.05)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Facebook Posts',
                        data: {!! json_encode($fbPostsSeries) !!},
                        borderColor: '#0D6EFD',
                        backgroundColor: 'rgba(13, 110, 253, 0.05)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'TikTok Posts',
                        data: {!! json_encode($tiktokPostsSeries) !!},
                        borderColor: '#198754',
                        backgroundColor: 'rgba(25, 135, 84, 0.05)',
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Donut Chart: Views Platform Distribution
        const ctxPie = document.getElementById('platformPieChart').getContext('2d');
        new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: ['Instagram Views', 'Facebook Views', 'TikTok Views'],
                datasets: [{
                    data: [
                        {{ $platformDistribution['Instagram Views'] }},
                        {{ $platformDistribution['Facebook Views'] }},
                        {{ $platformDistribution['TikTok Views'] }}
                    ],
                    backgroundColor: ['#E60012', '#0D6EFD', '#198754']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    });
</script>
@endpush
