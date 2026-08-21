@extends('layouts.app')

@section('title', 'Master User Account')

@section('content')
<div class="card card-custom p-4">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h5 class="fw-bold text-dark m-0"><i class="fas fa-users text-primary me-2"></i> Master Data User Account</h5>
            <div class="text-muted small">Kelola pengguna sistem (Super Admin, Area Manager, & PIC Digital Cabang)</div>
        </div>
        <button class="btn btn-yamaha-primary" id="btn-add-user"><i class="fas fa-plus me-1"></i> Buat User Baru</button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle w-100" id="table-users">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama User</th>
                    <th>Email</th>
                    <th>Role Hak Akses</th>
                    <th>Cabang</th>
                    <th>Status</th>
                    <th style="width: 130px;">Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Modal Form User -->
<div class="modal fade" id="modal-user" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-header-title fw-bold m-0" id="modal-user-title">Buat User Account Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-user">
                <div class="modal-body p-4">
                    <input type="hidden" id="user_id" name="user_id">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Contoh: Dimas Prayoga" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Corporate</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="user@aspacindo.co.id" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password <span class="text-muted small fw-normal" id="password-help">(Minimal 6 karakter)</span></label>
                        <div class="input-group">
                            <input type="password" name="password" id="password" class="form-control" placeholder="••••••••">
                            <button type="button" class="btn btn-outline-secondary" id="btnToggleUserPassword">
                                <i class="fas fa-eye" id="toggleUserPasswordIcon"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Role Hak Akses</label>
                        <select name="role" id="role" class="form-select" required>
                            <option value="">-- Pilih Role --</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->name }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Penugasan Cabang <span class="text-muted small fw-normal">(Opsional)</span></label>
                        <select name="branch_id" id="branch_id_select" class="form-select">
                            <option value="">-- Semua Cabang / Head Office --</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}">{{ $b->nama_cabang }} ({{ $b->kode }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Account</label>
                        <select name="status" id="user_status" class="form-select" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-yamaha-primary rounded-pill px-4">Simpan Account</button>
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

        const table = $('#table-users').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('master.users.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'role_name', name: 'role_name' },
                { data: 'nama_cabang', name: 'nama_cabang' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        $('#btn-add-user').click(function () {
            $('#form-user')[0].reset();
            $('#user_id').val('');
            $('#password').attr('required', true);
            $('#password-help').text('(Wajib diisi)');
            $('#modal-user-title').text('Buat User Account Baru');
            $('#modal-user').modal('show');
        });

        $('#table-users').on('click', '.btn-edit', function () {
            const id = $(this).data('id');
            $.get("{{ url('master/users') }}/" + id, function (data) {
                $('#user_id').val(data.id);
                $('#name').val(data.name);
                $('#email').val(data.email);
                $('#password').removeAttr('required');
                $('#password-help').text('(Kosongkan jika tidak ubah password)');
                $('#role').val(data.role);
                $('#branch_id_select').val(data.branch_id);
                $('#user_status').val(data.status);
                $('#modal-user-title').text('Edit User Account');
                $('#modal-user').modal('show');
            });
        });

        $('#form-user').submit(function (e) {
            e.preventDefault();
            const id = $('#user_id').val();
            const url = id ? "{{ url('master/users') }}/" + id : "{{ route('master.users.store') }}";
            const method = id ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: method,
                data: $(this).serialize(),
                success: function (res) {
                    $('#modal-user').modal('hide');
                    table.ajax.reload();
                    Swal.fire('Sukses', res.message, 'success');
                },
                error: function (xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Terjadi kesalahan.', 'error');
                }
            });
        });

        $('#table-users').on('click', '.btn-delete', function () {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "User account yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('master/users') }}/" + id,
                        type: 'DELETE',
                        success: function (res) {
                            table.ajax.reload();
                            Swal.fire('Dihapus!', res.message, 'success');
                        }
                    });
                }
            });
        });
        $('#btnToggleUserPassword').on('click', function() {
            const passInput = $('#password');
            const toggleIcon = $('#toggleUserPasswordIcon');
            const isPassword = passInput.attr('type') === 'password';
            
            passInput.attr('type', isPassword ? 'text' : 'password');
            if (isPassword) {
                toggleIcon.removeClass('fa-eye').addClass('fa-eye-slash text-primary');
            } else {
                toggleIcon.removeClass('fa-eye-slash text-primary').addClass('fa-eye');
            }
        });
    });
</script>
@endpush
