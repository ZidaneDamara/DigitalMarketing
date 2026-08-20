@extends('layouts.app')

@section('title', 'Audit System Logs')

@section('content')
<div class="card card-custom p-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h5 class="fw-bold text-dark m-0"><i class="fas fa-shield-alt text-danger me-2"></i> Audit Activity Logs System</h5>
            <div class="text-muted small">Rekam jejak seluruh aktivitas pengguna (Login, Logout, Create, Edit, Hapus, Copy KPI)</div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle w-100" id="table-audit-logs">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Waktu (Timestamp)</th>
                    <th>Nama User</th>
                    <th>Aksi</th>
                    <th>Modul</th>
                    <th>Deskripsi Aktivitas</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#table-audit-logs').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('audit-logs.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'user_name', name: 'user_name' },
                { data: 'action', name: 'action' },
                { data: 'module', name: 'module' },
                { data: 'description', name: 'description' },
                { data: 'ip_address', name: 'ip_address', defaultContent: '-' }
            ]
        });
    });
</script>
@endpush
