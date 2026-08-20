@extends('layouts.app')

@section('title', 'Leaderboard Antar Cabang')

@section('content')
<div class="card card-custom p-3 mb-4">
    <form action="{{ route('leaderboard.index') }}" method="GET" class="row g-2 g-md-3 align-items-end">
        <div class="col-6 col-md-4">
            <label class="form-label fw-semibold small text-muted">Tahun</label>
            <select name="tahun" class="form-select">
                @for($y = 2024; $y <= 2028; $y++)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="col-6 col-md-5">
            <label class="form-label fw-semibold small text-muted">Bulan</label>
            <select name="bulan" class="form-select">
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-3">
            <button type="submit" class="btn btn-yamaha-primary w-100"><i class="fas fa-filter me-1"></i> Terapkan Filter</button>
        </div>
    </form>
</div>

<!-- Ranking Podium Cards Top 3 -->
<div class="row g-3 mb-4">
    @foreach($leaderboard->take(3) as $topItem)
    <div class="col-12 col-md-4">
        <div class="card card-custom p-4 text-center border-top border-4 {{ $topItem['rank'] == 1 ? 'border-warning shadow-lg' : ($topItem['rank'] == 2 ? 'border-secondary' : 'border-danger') }}">
            <div class="fs-1 text-warning mb-2">
                @if($topItem['rank'] == 1) 👑 @elseif($topItem['rank'] == 2) 🥈 @else 🥉 @endif
            </div>
            <h5 class="fw-bold text-dark mb-1">{{ $topItem['nama_cabang'] }}</h5>
            <div class="text-muted small mb-3">{{ $topItem['kode'] }} | {{ $topItem['area'] }}</div>
            
            <div class="fs-2 fw-bold text-primary mb-2">{{ $topItem['achievement_pct'] }}%</div>
            <div>
                <span class="badge {{ $topItem['badge']['bg_class'] }} rounded-pill px-3 py-2 fs-6">
                    {{ $topItem['badge']['label'] }}
                </span>
            </div>

            <hr class="my-3">
            <div class="d-flex justify-content-around text-start small">
                <div>
                    <div class="text-muted">Total Posts</div>
                    <div class="fw-bold text-dark">{{ $topItem['total_posts'] }} Post</div>
                </div>
                <div>
                    <div class="text-muted">Followers Growth</div>
                    <div class="fw-bold text-success">+{{ number_format($topItem['total_followers_gained']) }}</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Full Ranking Table -->
<div class="card card-custom p-4">
    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-list-ol text-primary me-2"></i> Peringkat Lengkap Seluruh Cabang</h6>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 80px;">Peringkat</th>
                    <th>Kode</th>
                    <th>Nama Cabang</th>
                    <th>Area Marketing</th>
                    <th>Total Posting</th>
                    <th>Followers Growth</th>
                    <th style="width: 250px;">Progress Achievement</th>
                    <th>Status Tier</th>
                </tr>
            </thead>
            <tbody>
                @foreach($leaderboard as $item)
                <tr>
                    <td>
                        <span class="fw-bold fs-5 text-dark">#{{ $item['rank'] }}</span>
                    </td>
                    <td class="fw-bold text-primary">{{ $item['kode'] }}</td>
                    <td class="fw-bold text-dark fs-6">{{ $item['nama_cabang'] }}</td>
                    <td>{{ $item['area'] }}</td>
                    <td><span class="badge bg-light text-dark border px-3 py-2 fs-6">{{ $item['total_posts'] }} Post</span></td>
                    <td><span class="text-success fw-bold fs-6">+{{ number_format($item['total_followers_gained']) }}</span></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height: 10px;">
                                <div class="progress-bar" role="progressbar" style="width: {{ min(100, $item['achievement_pct']) }}%; background-color: {{ $item['badge']['hex'] }};"></div>
                            </div>
                            <span class="fw-bold text-dark">{{ $item['achievement_pct'] }}%</span>
                        </div>
                    </td>
                    <td>
                        <span class="badge {{ $item['badge']['bg_class'] }} rounded-pill px-3 py-2">
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
