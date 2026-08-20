@extends('layouts.app')

@section('title', 'Master Data Cabang')

@section('content')
<div class="card card-custom p-4">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h5 class="fw-bold text-dark m-0"><i class="fas fa-store text-primary me-2"></i> Master Data Cabang Yamaha</h5>
            <div class="text-muted small">Kelola data seluruh jaringan cabang dealer PT. Aspacindo Kedaton Motor</div>
        </div>
        <button class="btn btn-yamaha-primary" id="btn-add-branch"><i class="fas fa-plus me-1"></i> Tambah Cabang Baru</button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle w-100" id="table-branches">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Kode</th>
                    <th>Nama Cabang</th>
                    <th>Area</th>
                    <th>Alamat</th>
                    <th>Manager</th>
                    <th>Status</th>
                    <th style="width: 130px;">Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Modal Form Branch -->
<div class="modal fade" id="modal-branch" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-header-title fw-bold m-0" id="modal-title">Tambah Cabang Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-branch">
                <div class="modal-body p-4">
                    <input type="hidden" id="branch_id" name="branch_id">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode Cabang</label>
                        <input type="text" name="kode" id="kode" class="form-control" placeholder="Contoh: YMH-KDT-01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Cabang</label>
                        <input type="text" name="nama_cabang" id="nama_cabang" class="form-control" placeholder="Contoh: Kedaton Utama" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Area Marketing</label>
                        <input type="text" name="area" id="area" class="form-control" placeholder="Contoh: Bandar Lampung Central" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat Lengkap</label>
                        <textarea name="alamat" id="alamat" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Manager Cabang</label>
                        <input type="text" name="manager_name" id="manager_name" class="form-control" placeholder="Nama Manager Cabang">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Cabang</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-yamaha-primary rounded-pill px-4">Simpan Cabang</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        const table = $('#table-branches').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('master.branches.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'kode', name: 'kode' },
                { data: 'nama_cabang', name: 'nama_cabang' },
                { data: 'area', name: 'area' },
                { data: 'alamat', name: 'alamat' },
                { data: 'manager_name', name: 'manager_name', defaultContent: '-' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        $('#btn-add-branch').click(function () {
            $('#form-branch')[0].reset();
            $('#branch_id').val('');
            $('#modal-title').text('Tambah Cabang Baru');
            $('#modal-branch').modal('show');
        });

        $('#table-branches').on('click', '.btn-edit', function () {
            const id = $(this).data('id');
            $.get("{{ url('master/branches') }}/" + id, function (data) {
                $('#branch_id').val(data.id);
                $('#kode').val(data.kode);
                $('#nama_cabang').val(data.nama_cabang);
                $('#area').val(data.area);
                $('#alamat').val(data.alamat);
                $('#manager_name').val(data.manager_name);
                $('#status').val(data.status);
                $('#modal-title').text('Edit Data Cabang');
                $('#modal-branch').modal('show');
            });
        });

        $('#form-branch').submit(function (e) {
            e.preventDefault();
            const id = $('#branch_id').val();
            const url = id ? "{{ url('master/branches') }}/" + id : "{{ route('master.branches.store') }}";
            const method = id ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: method,
                data: $(this).serialize(),
                success: function (res) {
                    $('#modal-branch').modal('hide');
                    table.ajax.reload();
                    Swal.fire('Sukses', res.message, 'success');
                },
                error: function (xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Terjadi kesalahan.', 'error');
                }
            });
        });

        $('#table-branches').on('click', '.btn-delete', function () {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Cabang yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('master/branches') }}/" + id,
                        type: 'DELETE',
                        success: function (res) {
                            table.ajax.reload();
                            Swal.fire('Dihapus!', res.message, 'success');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
