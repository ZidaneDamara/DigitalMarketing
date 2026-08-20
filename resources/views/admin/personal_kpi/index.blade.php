@extends('layouts.app')

@section('title', 'KPI Pribadi Area Marketing Development')

@section('content')
<div class="card card-custom p-3 mb-4">
    <form action="{{ route('personal-kpis.index') }}" method="GET" class="row g-2 g-md-3 align-items-end">
        <div class="col-6 col-md-5">
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
        <div class="col-12 col-md-2">
            <button type="submit" class="btn btn-yamaha-primary w-100"><i class="fas fa-filter me-1"></i> Terapkan</button>
        </div>
    </form>
</div>

<!-- Header Stats Card -->
<div class="card card-custom p-4 mb-4 border-top border-4 border-primary">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h5 class="fw-bold text-dark m-0"><i class="fas fa-user-check text-primary me-2"></i> Performance KPI Pribadi Area Marketing Development Digital</h5>
            <div class="text-muted small">Tracking pencapaian target kerja pribadi (Training, Audit, Kunjungan Cabang, Campaign, SOP, DLL)</div>
        </div>
        <div class="text-end">
            <div class="fs-2 fw-bold text-primary">{{ $overallAchievement }}%</div>
            <span class="badge {{ $statusBadge['bg_class'] }} rounded-pill px-3 py-1.5 fs-6">{{ $statusBadge['label'] }}</span>
        </div>
    </div>
</div>

<div class="card card-custom p-4 mb-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="fw-bold text-dark m-0"><i class="fas fa-list text-warning me-2"></i> Rincian Target vs Realisasi</h6>
        <button class="btn btn-sm btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#modal-personal-kpi">
            <i class="fas fa-plus me-1"></i> Tambah Inisiatif KPI
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Kategori Inisiatif Digital</th>
                    <th>Target</th>
                    <th>Realisasi</th>
                    <th style="width: 250px;">Progress Achievement</th>
                    <th>Status</th>
                    <th style="width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($personalKpis as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="fw-bold text-dark fs-6">{{ $item->kategori }}</td>
                    <td><span class="badge bg-light text-dark border px-3 py-1.5 fs-6">{{ $item->target }}</span></td>
                    <td><span class="badge bg-primary px-3 py-1.5 fs-6">{{ $item->realisasi }}</span></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height: 10px;">
                                <div class="progress-bar" style="width: {{ min(100, $item->achievement) }}%; background-color: {{ \App\Services\KpiService::getStatusBadge($item->achievement)['hex'] }};"></div>
                            </div>
                            <span class="fw-bold">{{ $item->achievement }}%</span>
                        </div>
                    </td>
                    <td>
                        <span class="badge {{ \App\Services\KpiService::getStatusBadge($item->achievement)['bg_class'] }} rounded-pill px-3 py-1">
                            {{ \App\Services\KpiService::getStatusBadge($item->achievement)['label'] }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-outline-warning btn-edit-personal-kpi" 
                                    data-id="{{ $item->id }}"
                                    data-kategori="{{ $item->kategori }}"
                                    data-target="{{ $item->target }}"
                                    data-realisasi="{{ $item->realisasi }}"
                                    data-url="{{ route('personal-kpis.update', $item->id) }}"
                                    title="Edit Realisasi">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('personal-kpis.destroy', $item->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus KPI ini?')" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Belum ada target KPI pribadi untuk periode ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Create Personal KPI -->
<div class="modal fade" id="modal-personal-kpi" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="fw-bold m-0"><i class="fas fa-plus me-2"></i> Tambah Inisiatif KPI Pribadi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('personal-kpis.store') }}" method="POST">
                @csrf
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <input type="hidden" name="bulan" value="{{ $bulan }}">

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kategori Inisiatif</label>
                        <select name="kategori" class="form-select" required>
                            <option value="Training PIC Cabang">Training PIC Cabang</option>
                            <option value="Audit Digital Cabang">Audit Digital Cabang</option>
                            <option value="Kunjungan Lapangan">Kunjungan Lapangan</option>
                            <option value="Campaign Promo Digital">Campaign Promo Digital</option>
                            <option value="Meeting Coordinasi Bulanan">Meeting Coordinasi Bulanan</option>
                            <option value="SOP & Guideline Content">SOP & Guideline Content</option>
                            <option value="Project Digital Optimization">Project Digital Optimization</option>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Target</label>
                            <input type="number" name="target" class="form-control" value="1" min="1" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Realisasi awal</label>
                            <input type="number" name="realisasi" class="form-control" value="0" min="0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-yamaha-primary rounded-pill px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Personal KPI -->
<div class="modal fade" id="modal-edit-personal-kpi" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-warning text-dark rounded-top-4">
                <h5 class="fw-bold m-0"><i class="fas fa-edit me-2"></i> Update Progress Realisasi KPI</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-edit-personal-kpi" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <input type="hidden" name="bulan" value="{{ $bulan }}">

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kategori Inisiatif</label>
                        <input type="text" name="kategori" id="edit_kategori" class="form-control" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Target</label>
                            <input type="number" name="target" id="edit_target" class="form-control" min="1" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Realisasi (Bertahap)</label>
                            <input type="number" name="realisasi" id="edit_realisasi" class="form-control" min="0" required>
                        </div>
                    </div>
                    <div class="form-text mt-2 text-muted small">
                        <i class="fas fa-info-circle me-1"></i> Update angka realisasi secara bertahap seiring berjalannya aktivitas di bulan ini.
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">Update Progress</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('.btn-edit-personal-kpi').click(function () {
            const url = $(this).data('url');
            const kategori = $(this).data('kategori');
            const target = $(this).data('target');
            const realisasi = $(this).data('realisasi');

            $('#form-edit-personal-kpi').attr('action', url);
            $('#edit_kategori').val(kategori);
            $('#edit_target').val(target);
            $('#edit_realisasi').val(realisasi);

            $('#modal-edit-personal-kpi').modal('show');
        });
    });
</script>
@endpush
