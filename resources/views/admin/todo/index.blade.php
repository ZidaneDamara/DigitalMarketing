@extends('layouts.app')

@section('title', 'Kanban To-Do List (Super Admin)')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h5 class="fw-bold text-dark m-0"><i class="fas fa-tasks text-success me-2"></i> Kanban To-Do Board Personal (AMD)</h5>
        <div class="text-muted small">Kelola daftar tugas harian dan koordinasi cabang digital marketing</div>
    </div>
    <button class="btn btn-yamaha-primary" data-bs-toggle="modal" data-bs-target="#modal-todo">
        <i class="fas fa-plus me-1"></i> Tambah Task Baru
    </button>
</div>

<div class="row g-4">
    <!-- Column: TO DO -->
    <div class="col-12 col-lg-4">
        <div class="card card-custom p-3 bg-light border-top border-4 border-danger h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold text-danger m-0"><i class="fas fa-list-ul me-1"></i> TO DO ({{ $todoList->count() }})</h6>
                <span class="badge bg-danger rounded-pill">Planning</span>
            </div>

            <div class="d-flex flex-column gap-3" style="min-height: 400px;">
                @foreach($todoList as $todo)
                <div class="card card-custom p-3 shadow-sm border-start border-4" style="border-color: {{ $todo->color_badge }} !important;" data-id="{{ $todo->id }}">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge rounded-pill px-2.5 py-1 text-white" style="background-color: {{ $todo->color_badge }};">
                            {{ $todo->priority }} Priority
                        </span>
                        <button class="btn btn-sm btn-link text-danger p-0 btn-delete-todo" data-id="{{ $todo->id }}"><i class="fas fa-trash"></i></button>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">{{ $todo->judul }}</h6>
                    <p class="text-muted small mb-2">{{ $todo->deskripsi }}</p>
                    <div class="d-flex align-items-center justify-content-between text-muted small" style="font-size: 0.75rem;">
                        <span><i class="far fa-calendar-alt me-1"></i> Deadline: {{ $todo->deadline ? $todo->deadline->format('d M Y') : '-' }}</span>
                    </div>

                    <!-- Dropdown Select Move Status -->
                    <div class="mt-3 pt-2 border-top d-flex align-items-center justify-content-between">
                        <span class="small text-muted fw-semibold">Ubah Status:</span>
                        <select class="form-select form-select-sm status-select w-auto fw-bold text-danger border-danger" data-id="{{ $todo->id }}" style="font-size: 0.8rem; padding: 0.2rem 0.6rem; border-radius: 8px;">
                            <option value="To Do" selected>📌 To Do</option>
                            <option value="Progress">⏳ In Progress</option>
                            <option value="Done">✅ Done</option>
                        </select>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Column: IN PROGRESS -->
    <div class="col-12 col-lg-4">
        <div class="card card-custom p-3 bg-light border-top border-4 border-warning h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold text-warning text-dark m-0"><i class="fas fa-spinner fa-spin me-1"></i> IN PROGRESS ({{ $progressList->count() }})</h6>
                <span class="badge bg-warning text-dark rounded-pill">Active</span>
            </div>

            <div class="d-flex flex-column gap-3" style="min-height: 400px;">
                @foreach($progressList as $todo)
                <div class="card card-custom p-3 shadow-sm border-start border-4" style="border-color: {{ $todo->color_badge }} !important;" data-id="{{ $todo->id }}">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge rounded-pill px-2.5 py-1 text-white" style="background-color: {{ $todo->color_badge }};">
                            {{ $todo->priority }} Priority
                        </span>
                        <button class="btn btn-sm btn-link text-danger p-0 btn-delete-todo" data-id="{{ $todo->id }}"><i class="fas fa-trash"></i></button>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">{{ $todo->judul }}</h6>
                    <p class="text-muted small mb-2">{{ $todo->deskripsi }}</p>
                    <div class="d-flex align-items-center justify-content-between text-muted small" style="font-size: 0.75rem;">
                        <span><i class="far fa-calendar-alt me-1"></i> Deadline: {{ $todo->deadline ? $todo->deadline->format('d M Y') : '-' }}</span>
                    </div>

                    <!-- Dropdown Select Move Status -->
                    <div class="mt-3 pt-2 border-top d-flex align-items-center justify-content-between">
                        <span class="small text-muted fw-semibold">Ubah Status:</span>
                        <select class="form-select form-select-sm status-select w-auto fw-bold text-warning border-warning" data-id="{{ $todo->id }}" style="font-size: 0.8rem; padding: 0.2rem 0.6rem; border-radius: 8px;">
                            <option value="To Do">📌 To Do</option>
                            <option value="Progress" selected>⏳ In Progress</option>
                            <option value="Done">✅ Done</option>
                        </select>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Column: DONE -->
    <div class="col-12 col-lg-4">
        <div class="card card-custom p-3 bg-light border-top border-4 border-success h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold text-success m-0"><i class="fas fa-check-circle me-1"></i> DONE ({{ $doneList->count() }})</h6>
                <span class="badge bg-success rounded-pill">Completed</span>
            </div>

            <div class="d-flex flex-column gap-3" style="min-height: 400px;">
                @foreach($doneList as $todo)
                <div class="card card-custom p-3 shadow-sm border-start border-4" style="border-color: {{ $todo->color_badge }} !important;" data-id="{{ $todo->id }}">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge bg-success rounded-pill px-2.5 py-1">Completed</span>
                        <button class="btn btn-sm btn-link text-danger p-0 btn-delete-todo" data-id="{{ $todo->id }}"><i class="fas fa-trash"></i></button>
                    </div>
                    <h6 class="fw-bold text-dark text-decoration-line-through mb-1">{{ $todo->judul }}</h6>
                    <p class="text-muted small mb-2">{{ $todo->deskripsi }}</p>

                    <!-- Dropdown Select Move Status -->
                    <div class="mt-3 pt-2 border-top d-flex align-items-center justify-content-between">
                        <span class="small text-muted fw-semibold">Ubah Status:</span>
                        <select class="form-select form-select-sm status-select w-auto fw-bold text-success border-success" data-id="{{ $todo->id }}" style="font-size: 0.8rem; padding: 0.2rem 0.6rem; border-radius: 8px;">
                            <option value="To Do">📌 To Do</option>
                            <option value="Progress">⏳ In Progress</option>
                            <option value="Done" selected>✅ Done</option>
                        </select>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Modal Form Todo -->
<div class="modal fade" id="modal-todo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="fw-bold m-0"><i class="fas fa-tasks me-2"></i> Tambah Task Kanban Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-todo">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul Task</label>
                        <input type="text" name="judul" class="form-control" placeholder="Contoh: Audit Digital Metro" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi Task</label>
                        <textarea name="deskripsi" class="form-control" rows="3" placeholder="Penjelasan rincian tugas..."></textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Priority</label>
                            <select name="priority" class="form-select" required>
                                <option value="Low">Low</option>
                                <option value="Medium" selected>Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Warna Badge</label>
                            <select name="color_badge" class="form-select" required>
                                <option value="#dc3545" style="color: #dc3545;">Red (High)</option>
                                <option value="#ffc107" style="color: #ffc107;">Yellow (Medium)</option>
                                <option value="#0d6efd" style="color: #0d6efd;">Blue (Normal)</option>
                                <option value="#198754" style="color: #198754;">Green (Done)</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Deadline Target</label>
                            <input type="date" name="deadline" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Status Awal</label>
                            <select name="status" class="form-select" required>
                                <option value="To Do">To Do</option>
                                <option value="Progress">Progress</option>
                                <option value="Done">Done</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-yamaha-primary rounded-pill px-4">Simpan Task</button>
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

        // Status Change Dropdown Selector Handler
        $('.status-select').change(function () {
            const id = $(this).data('id');
            const newStatus = $(this).val();

            $.ajax({
                url: "{{ url('todo') }}/" + id + "/status",
                type: 'PUT',
                data: { status: newStatus },
                success: function (res) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: res.message,
                        showConfirmButton: false,
                        timer: 1000
                    }).then(() => location.reload());
                },
                error: function (xhr) {
                    Swal.fire('Error', 'Gagal memperbarui status task.', 'error');
                }
            });
        });

        $('#form-todo').submit(function (e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('todos.store') }}",
                type: 'POST',
                data: $(this).serialize(),
                success: function (res) {
                    $('#modal-todo').modal('hide');
                    Swal.fire('Sukses', res.message, 'success').then(() => location.reload());
                }
            });
        });

        $('.btn-delete-todo').click(function () {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Hapus Task ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus'
            }).then((res) => {
                if (res.isConfirmed) {
                    $.ajax({
                        url: "{{ url('todo') }}/" + id,
                        type: 'DELETE',
                        success: function () {
                            location.reload();
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
