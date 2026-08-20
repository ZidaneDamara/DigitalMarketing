@extends('layouts.app')

@section('title', 'Branch Performance Detail')

@section('content')
<div class="card card-custom p-3 mb-4">
    <form action="{{ route('branch.performance') }}" method="GET" class="row g-2 g-md-3 align-items-end">
        <div class="col-12 col-md-5">
            <label class="form-label fw-semibold small text-muted">Pilih Cabang</label>
            <select name="branch_id" class="form-select">
                @foreach($branches as $b)
                    <option value="{{ $b->id }}" {{ $selectedBranchId == $b->id ? 'selected' : '' }}>{{ $b->nama_cabang }} ({{ $b->kode }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-4">
            <label class="form-label fw-semibold small text-muted">Tahun</label>
            <select name="tahun" class="form-select">
                @for($y = 2024; $y <= 2028; $y++)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="col-12 col-md-3">
            <button type="submit" class="btn btn-yamaha-primary w-100"><i class="fas fa-search me-1"></i> Tampilkan Performance</button>
        </div>
    </form>
</div>

@if($selectedBranch)
<div class="card card-custom p-4 mb-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h4 class="fw-bold text-dark m-0">{{ $selectedBranch->nama_cabang }}</h4>
            <div class="text-muted"><i class="fas fa-map-marker-alt text-danger me-1"></i> {{ $selectedBranch->alamat }} | Manager: {{ $selectedBranch->manager_name ?? '-' }}</div>
        </div>
        <span class="badge bg-primary px-3 py-2 fs-6"><i class="fas fa-tag me-1"></i> Kode: {{ $selectedBranch->kode }}</span>
    </div>
</div>

<div class="card card-custom p-4 mb-4">
    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-chart-line text-primary me-2"></i> Timeline Achievement Per Bulan (Tahun {{ $tahun }})</h6>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Bulan</th>
                    <th>Total Post</th>
                    <th>Followers Growth</th>
                    <th>Total Views</th>
                    <th>Achievement KPI</th>
                    <th>Status Badge</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlyPerformance as $perf)
                <tr>
                    <td class="fw-bold text-dark">{{ $perf['bulan_name'] }}</td>
                    <td><span class="badge bg-light text-dark border px-3 py-1.5">{{ $perf['total_posts'] }} Post</span></td>
                    <td><span class="text-success fw-bold">+{{ number_format($perf['followers_gained']) }}</span></td>
                    <td><span class="fw-bold text-dark">{{ number_format($perf['total_views']) }} Views</span></td>
                    <td>
                        <div class="d-flex align-items-center gap-2" style="width: 180px;">
                            <div class="progress flex-grow-1" style="height: 8px;">
                                <div class="progress-bar" style="width: {{ $perf['achv_pct'] }}%; background-color: {{ $perf['badge']['hex'] }};"></div>
                            </div>
                            <span class="fw-bold small">{{ $perf['achv_pct'] }}%</span>
                        </div>
                    </td>
                    <td>
                        <span class="badge {{ $perf['badge']['bg_class'] }} rounded-pill px-3 py-1.5">
                            {{ $perf['badge']['label'] }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
