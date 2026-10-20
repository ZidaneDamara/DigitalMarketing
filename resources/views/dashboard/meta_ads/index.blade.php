@extends('layouts.app')

@section('title', 'Dashboard Meta Ads')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header & Filter -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fab fa-meta text-primary"></i> Executive Dashboard Meta Ads
            </h3>
            <p class="text-muted mb-0">Analisis komprehensif performa iklan berbayar (Facebook & Instagram Ads), efisiensi budget, dan rasio konversi unit (STU).</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('reports.meta-ads.index') }}" class="btn btn-yamaha-primary rounded-pill px-4 shadow-sm">
                <i class="fas fa-list me-1.5"></i> Data Laporan Meta Ads
            </a>
        </div>
    </div>

    <!-- Filter Bar Card -->
    <div class="card card-custom p-3 mb-4 border-0 shadow-sm">
        <form action="{{ route('dashboard.meta-ads') }}" method="GET" class="row g-2 align-items-end">
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
                    <option value="">Semua Bulan (Tahunan)</option>
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
                <label class="form-label small fw-semibold text-muted mb-1">Cabang</label>
                <select name="branch_id" class="form-select form-select-sm">
                    <option value="">-- Semua Cabang (Konsolidasi) --</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>{{ $b->nama_cabang }} ({{ $b->kode }})</option>
                    @endforeach
                </select>
            </div>
            @endunless
            <div class="col-4 col-md-2">
                <button type="submit" class="btn btn-sm btn-yamaha-primary w-100">
                    <i class="fas fa-filter me-1"></i> Terapkan
                </button>
            </div>
        </form>
    </div>

    <!-- Executive KPI Metric Cards -->
    <div class="row g-3 mb-4">
        <!-- Total Budget Spend -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom p-3 border-0 bg-white shadow-sm h-100 border-start border-4 border-primary">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Total Budget Spend</div>
                        <h4 class="fw-extrabold text-primary m-0 mt-1">Rp {{ number_format($totalBudget, 0, ',', '.') }}</h4>
                        <small class="text-muted" style="font-size: 0.72rem;">Anggaran kampanye Meta Ads</small>
                    </div>
                    <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-circle p-3 fs-4">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Views -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom p-3 border-0 bg-white shadow-sm h-100 border-start border-4 border-info">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Total Views / Impresi</div>
                        <h4 class="fw-extrabold text-dark m-0 mt-1">{{ number_format($totalViews) }}</h4>
                        <small class="text-muted" style="font-size: 0.72rem;">Jangkauan tayangan iklan</small>
                    </div>
                    <div class="icon-box bg-info bg-opacity-10 text-info rounded-circle p-3 fs-4">
                        <i class="fas fa-eye"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Percakapan (Leads) -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom p-3 border-0 bg-white shadow-sm h-100 border-start border-4 border-success">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Total Percakapan (Leads)</div>
                        <h4 class="fw-extrabold text-success m-0 mt-1">{{ number_format($totalPercakapan) }} Chat</h4>
                        <small class="text-muted" style="font-size: 0.72rem;">Pesan masuk dari calon konsumen</small>
                    </div>
                    <div class="icon-box bg-success bg-opacity-10 text-success rounded-circle p-3 fs-4">
                        <i class="fas fa-comments"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total STU Closing -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom p-3 border-0 bg-white shadow-sm h-100 border-start border-4 border-danger">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Total STU (Closing Unit)</div>
                        <h4 class="fw-extrabold text-danger m-0 mt-1">{{ number_format($totalStu) }} Unit</h4>
                        <small class="text-danger fw-semibold" style="font-size: 0.72rem;">Sales To Unit Dealer</small>
                    </div>
                    <div class="icon-box bg-danger bg-opacity-10 text-danger rounded-circle p-3 fs-4">
                        <i class="fas fa-motorcycle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Efficiency Metrics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card card-custom p-3 border-0 bg-white shadow-sm text-center">
                <div class="text-muted small fw-semibold">Rata-rata CTR</div>
                <h5 class="fw-bold text-dark m-0 mt-1">{{ number_format($avgCtr, 2) }}%</h5>
                <span class="badge bg-light text-muted mt-1 small">Click-Through Rate</span>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card card-custom p-3 border-0 bg-white shadow-sm text-center">
                <div class="text-muted small fw-semibold">Rata-rata CPC</div>
                <h5 class="fw-bold text-dark m-0 mt-1">Rp {{ number_format($avgCpc, 0, ',', '.') }}</h5>
                <span class="badge bg-light text-muted mt-1 small">Cost Per Click</span>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card card-custom p-3 border-0 bg-white shadow-sm text-center">
                <div class="text-muted small fw-semibold">Rata-rata CPM</div>
                <h5 class="fw-bold text-dark m-0 mt-1">Rp {{ number_format($avgCpm, 0, ',', '.') }}</h5>
                <span class="badge bg-light text-muted mt-1 small">Cost Per Mille</span>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card card-custom p-3 border-0 bg-white shadow-sm text-center">
                <div class="text-muted small fw-semibold">Rata-rata CPL</div>
                <h5 class="fw-bold text-info m-0 mt-1">Rp {{ number_format($avgCpl, 0, ',', '.') }}</h5>
                <span class="badge bg-info-subtle text-info mt-1 small">Cost Per Lead</span>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card card-custom p-3 border-0 bg-white shadow-sm text-center">
                <div class="text-muted small fw-semibold">Cost Per STU (CPA)</div>
                <h5 class="fw-bold text-danger m-0 mt-1">Rp {{ number_format($costPerStu, 0, ',', '.') }}</h5>
                <span class="badge bg-danger-subtle text-danger mt-1 small">Biaya Per Unit</span>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card card-custom p-3 border-0 bg-white shadow-sm text-center">
                <div class="text-muted small fw-semibold">Closing Rate</div>
                <h5 class="fw-bold text-success m-0 mt-1">{{ number_format($conversionRate, 2) }}%</h5>
                <span class="badge bg-success-subtle text-success mt-1 small">Chat ke Closing</span>
            </div>
        </div>
    </div>

    <!-- Charts Row 1: Monthly Spend & Leads vs STU + Product Performance -->
    <div class="row g-3 mb-4">
        <!-- Monthly Trend Chart -->
        <div class="col-12 col-xl-8">
            <div class="card card-custom p-4 border-0 bg-white shadow-sm h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h6 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                            <i class="fas fa-chart-line text-primary"></i> Tren Bulanan Meta Ads Tahun {{ $tahun }}
                        </h6>
                        <small class="text-muted">Perbandingan alokasi Budget (Rp) vs Total Percakapan & STU per bulan</small>
                    </div>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1.5 rounded-pill fw-semibold small">
                        Meta Ads Analytics
                    </span>
                </div>
                <div style="position: relative; height: 320px;">
                    <canvas id="monthlyTrendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Product Breakdown Donut / Polar -->
        <div class="col-12 col-xl-4">
            <div class="card card-custom p-4 border-0 bg-white shadow-sm h-100">
                <div class="mb-3">
                    <h6 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                        <i class="fas fa-chart-pie text-danger"></i> Distribusi STU per Produk Motor
                    </h6>
                    <small class="text-muted">Kontribusi penjualan unit dari iklan berdasarkan tipe motor</small>
                </div>
                <div style="position: relative; height: 320px;">
                    <canvas id="productStuChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2: Branch Comparison & Funnel -->
    <div class="row g-3 mb-4">
        <!-- Branch Comparison Bar Chart -->
        <div class="col-12 col-xl-7">
            <div class="card card-custom p-4 border-0 bg-white shadow-sm h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h6 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                            <i class="fas fa-building text-info"></i> Performa Meta Ads Antar Cabang
                        </h6>
                        <small class="text-muted">Komparasi Budget Spend (Rp) dan Hasil STU Unit antar dealer cabang</small>
                    </div>
                </div>
                <div style="position: relative; height: 300px;">
                    <canvas id="branchComparisonChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Funnel Conversion Analysis -->
        <div class="col-12 col-xl-5">
            <div class="card card-custom p-4 border-0 bg-white shadow-sm h-100">
                <div class="mb-3">
                    <h6 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                        <i class="fas fa-filter text-warning"></i> Funnel Konversi Iklan (Lead to STU)
                    </h6>
                    <small class="text-muted">Perjalanan dari Views hingga Realisasi Pembelian Unit</small>
                </div>
                
                <div class="p-3 bg-light rounded-3 mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-semibold text-muted"><i class="fas fa-eye me-1 text-info"></i> 1. Views / Impresi</span>
                        <span class="small fw-bold text-dark">{{ number_format($totalViews) }}</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-info" style="width: 100%;"></div>
                    </div>
                </div>

                <div class="p-3 bg-light rounded-3 mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-semibold text-muted"><i class="fas fa-comments me-1 text-success"></i> 2. Percakapan / Leads</span>
                        <span class="small fw-bold text-success">{{ number_format($totalPercakapan) }} Chat ({{ $totalViews > 0 ? number_format(($totalPercakapan / $totalViews) * 100, 2) : 0 }}%)</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-success" style="width: {{ $totalViews > 0 ? min(100, max(5, ($totalPercakapan / $totalViews) * 1000)) : 0 }}%;"></div>
                    </div>
                </div>

                <div class="p-3 bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-3 mb-2">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-bold text-danger"><i class="fas fa-motorcycle me-1 text-danger"></i> 3. STU Closing Unit</span>
                        <span class="small fw-extrabold text-danger">{{ number_format($totalStu) }} Unit ({{ $totalPercakapan > 0 ? number_format(($totalStu / $totalPercakapan) * 100, 1) : 0 }}%)</span>
                    </div>
                    <div class="progress" style="height: 12px;">
                        <div class="progress-bar bg-danger" style="width: {{ $totalPercakapan > 0 ? min(100, max(10, ($totalStu / $totalPercakapan) * 100)) : 0 }}%;"></div>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <span class="badge bg-dark rounded-pill px-3 py-2 small">
                        Rata-rata Biaya Akuisisi Closing (CPA): <strong>Rp {{ number_format($costPerStu, 0, ',', '.') }} / Unit</strong>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Spotlight Table: Top Performing Campaigns -->
    <div class="card card-custom p-4 border-0 bg-white shadow-sm mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h6 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                    <i class="fas fa-trophy text-warning"></i> Top 5 Kampanye Iklan Paling Menghasilkan (Highest STU)
                </h6>
                <small class="text-muted">Kampanye Meta Ads dengan penjualan unit tertinggi dan efisiensi leads terbaik</small>
            </div>
            <a href="{{ route('reports.meta-ads.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                Lihat Semua Laporan <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">Rank</th>
                        <th>Nama Iklan</th>
                        <th>Cabang</th>
                        <th>Produk</th>
                        <th>Budget Spend</th>
                        <th>Percakapan</th>
                        <th>CPL</th>
                        <th>STU Closing</th>
                        <th>Conversion</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topCampaigns as $idx => $c)
                    <tr>
                        <td>
                            @if($idx == 0)
                                <span class="badge bg-warning text-dark rounded-circle p-2" style="width: 28px; height: 28px;">🥇</span>
                            @elseif($idx == 1)
                                <span class="badge bg-secondary text-white rounded-circle p-2" style="width: 28px; height: 28px;">🥈</span>
                            @elseif($idx == 2)
                                <span class="badge bg-danger text-white rounded-circle p-2" style="width: 28px; height: 28px;">🥉</span>
                            @else
                                <span class="badge bg-light text-dark rounded-circle p-2" style="width: 28px; height: 28px;">{{ $idx + 1 }}</span>
                            @endif
                        </td>
                        <td><strong>{{ $c->nama_iklan }}</strong></td>
                        <td>{{ $c->branch?->nama_cabang ?? '-' }}</td>
                        <td><span class="badge bg-primary-subtle text-primary">{{ $c->product }}</span></td>
                        <td class="fw-bold">{{ $c->formatted_budget }}</td>
                        <td><span class="badge bg-success-subtle text-success px-2 py-1">{{ number_format($c->total_percakapan) }} Chat</span></td>
                        <td>{{ $c->formatted_cpl }}</td>
                        <td><span class="badge bg-danger text-white fw-bold px-2.5 py-1.5"><i class="fas fa-motorcycle me-1"></i>{{ number_format($c->stu) }} Unit</span></td>
                        <td><span class="fw-bold text-dark">{{ $c->conversion_rate }}%</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">Belum ada data laporan kampanye Meta Ads untuk periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    // 1. Monthly Trend Chart (Line & Bar combo)
    const ctxMonthly = document.getElementById('monthlyTrendChart');
    if (ctxMonthly) {
        const monthLabels = @json($monthNames);
        const dataBudget = @json(array_values($monthlyBudget));
        const dataPercakapan = @json(array_values($monthlyPercakapan));
        const dataStu = @json(array_values($monthlyStu));

        new Chart(ctxMonthly, {
            type: 'bar',
            data: {
                labels: monthLabels,
                datasets: [
                    {
                        label: 'Budget Spend (Juta Rp)',
                        data: dataBudget.map(val => (val / 1000000).toFixed(2)),
                        backgroundColor: 'rgba(0, 51, 153, 0.7)',
                        borderColor: '#003399',
                        borderWidth: 1,
                        yAxisID: 'yBudget',
                        order: 2
                    },
                    {
                        label: 'Total Percakapan (Leads)',
                        data: dataPercakapan,
                        type: 'line',
                        borderColor: '#198754',
                        backgroundColor: 'rgba(25, 135, 84, 0.1)',
                        borderWidth: 3,
                        pointRadius: 4,
                        pointBackgroundColor: '#198754',
                        yAxisID: 'yCounts',
                        tension: 0.3,
                        order: 1
                    },
                    {
                        label: 'STU Closing (Unit)',
                        data: dataStu,
                        type: 'line',
                        borderColor: '#DC3545',
                        backgroundColor: '#DC3545',
                        borderWidth: 3,
                        pointRadius: 5,
                        pointBackgroundColor: '#DC3545',
                        yAxisID: 'yCounts',
                        tension: 0.2,
                        order: 0
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    yBudget: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: { display: true, text: 'Budget (Juta Rp)' },
                        grid: { drawOnChartArea: true }
                    },
                    yCounts: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: { display: true, text: 'Chat / Unit' },
                        grid: { drawOnChartArea: false }
                    }
                },
                plugins: {
                    legend: { position: 'top' }
                }
            }
        });
    }

    // 2. Product STU Donut Chart
    const ctxProduct = document.getElementById('productStuChart');
    if (ctxProduct) {
        const productStats = @json($productStats);
        const prodLabels = productStats.map(p => p.product);
        const prodStu = productStats.map(p => p.stu);

        const colors = ['#E60012', '#003399', '#0D6EFD', '#198754', '#FFC107', '#6F42C1', '#20C997', '#FD7E14'];

        new Chart(ctxProduct, {
            type: 'doughnut',
            data: {
                labels: prodLabels.length > 0 ? prodLabels : ['Belum ada data'],
                datasets: [{
                    data: prodStu.length > 0 ? prodStu : [0],
                    backgroundColor: colors.slice(0, Math.max(1, prodLabels.length)),
                    borderWidth: 2,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.label + ': ' + context.raw + ' Unit Closed';
                            }
                        }
                    }
                }
            }
        });
    }

    // 3. Branch Comparison Bar Chart
    const ctxBranch = document.getElementById('branchComparisonChart');
    if (ctxBranch) {
        const branchStats = @json($branchStats);
        const bLabels = branchStats.map(b => b.nama_cabang);
        const bBudget = branchStats.map(b => (b.budget / 1000000).toFixed(1));
        const bStu = branchStats.map(b => b.stu);

        new Chart(ctxBranch, {
            type: 'bar',
            data: {
                labels: bLabels,
                datasets: [
                    {
                        label: 'Budget (Juta Rp)',
                        data: bBudget,
                        backgroundColor: 'rgba(13, 110, 253, 0.8)',
                        borderRadius: 6
                    },
                    {
                        label: 'STU Closing (Unit)',
                        data: bStu,
                        backgroundColor: 'rgba(220, 53, 69, 0.9)',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
});
</script>
@endpush
