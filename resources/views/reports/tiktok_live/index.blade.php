@extends('layouts.app')

@section('title', 'Laporan Harian Live TikTok')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header & Action Bar -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fab fa-tiktok text-danger"></i> Laporan Harian Live TikTok
            </h3>
            <p class="text-muted mb-0">Pencatatan aktivitas Live TikTok harian, penonton, likes, komentar, share, STU, dan bukti screenshot.</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @role('Super Admin')
            <button type="button" class="btn btn-outline-danger rounded-pill px-3 shadow-sm" id="btnExportPdf">
                <i class="fas fa-file-pdf me-1.5"></i> Export PDF
            </button>
            <button type="button" class="btn btn-outline-success rounded-pill px-3 shadow-sm" id="btnExportExcel">
                <i class="fas fa-file-excel me-1.5"></i> Export Excel (CSV)
            </button>
            @endrole
            <button type="button" class="btn btn-yamaha-primary rounded-pill px-4 shadow-sm" id="btnTambahLaporan">
                <i class="fas fa-plus-circle me-1.5"></i> Input Live TikTok Baru
            </button>
        </div>
    </div>

    <!-- Summary Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-sm-6 col-xl-2">
            <div class="card card-custom p-3 border-0 bg-white shadow-sm h-100">
                <div class="d-flex align-items-center gap-2">
                    <div class="icon-box bg-danger bg-opacity-10 text-danger rounded-3 p-2" style="width: 42px; height: 42px; font-size: 1.2rem;">
                        <i class="fab fa-tiktok"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Total Sesi</div>
                        <h5 class="fw-extrabold text-dark m-0">{{ number_format($totalSesi) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-sm-6 col-xl-2">
            <div class="card card-custom p-3 border-0 bg-white shadow-sm h-100">
                <div class="d-flex align-items-center gap-2">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-3 p-2" style="width: 42px; height: 42px; font-size: 1.2rem;">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Total Durasi</div>
                        <h5 class="fw-extrabold text-dark m-0">{{ $totalJamFormat }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-sm-6 col-xl-3">
            <div class="card card-custom p-3 border-0 bg-white shadow-sm h-100">
                <div class="d-flex align-items-center gap-2">
                    <div class="icon-box bg-info bg-opacity-10 text-info rounded-3 p-2" style="width: 42px; height: 42px; font-size: 1.2rem;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Total Penonton</div>
                        <h5 class="fw-extrabold text-dark m-0">{{ number_format($totalPenonton) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-sm-6 col-xl-3">
            <div class="card card-custom p-3 border-0 bg-white shadow-sm h-100">
                <div class="d-flex align-items-center gap-2">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning rounded-3 p-2" style="width: 42px; height: 42px; font-size: 1.2rem;">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Total Likes</div>
                        <h5 class="fw-extrabold text-dark m-0">{{ number_format($totalLikes) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-2">
            <div class="card card-custom p-3 border-0 bg-white shadow-sm h-100 border-start border-4 border-success">
                <div class="d-flex align-items-center gap-2">
                    <div class="icon-box bg-success bg-opacity-10 text-success rounded-3 p-2" style="width: 42px; height: 42px; font-size: 1.2rem;">
                        <i class="fas fa-motorcycle"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Total STU</div>
                        <h5 class="fw-extrabold text-success m-0">{{ number_format($totalStu) }} Unit</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Table Card -->
    <div class="card card-custom p-4 border-0 shadow-sm">
        <div class="row g-3 align-items-center mb-4">
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold small text-muted">Filter Cabang</label>
                <select id="filter_branch" class="form-select rounded-3 shadow-none">
                    @if(auth()->user()->hasRole('PIC Digital Cabang'))
                        <option value="{{ auth()->user()->branch_id }}">{{ auth()->user()->branch->nama_cabang }}</option>
                    @else
                        <option value="">-- Semua Cabang --</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->nama_cabang }} ({{ $b->kode }})</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold small text-muted">Tanggal Spesifik</label>
                <input type="date" id="filter_tanggal" class="form-control rounded-3 shadow-none">
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold small text-muted">Rentang S/D Tanggal</label>
                <input type="date" id="filter_tanggal_akhir" class="form-control rounded-3 shadow-none">
            </div>

            <div class="col-12 col-md-2 d-flex align-items-end">
                <button type="button" id="btnResetFilter" class="btn btn-outline-secondary w-100 rounded-3">
                    <i class="fas fa-undo me-1"></i> Reset
                </button>
            </div>
        </div>

        <!-- Data Table -->
        <div class="table-responsive">
            <table id="tiktokTable" class="table table-hover align-middle w-100 border">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 4%;">No</th>
                        <th>Tanggal</th>
                        <th>Cabang</th>
                        <th>Host (Yang Live)</th>
                        <th>Jabatan</th>
                        <th>Durasi</th>
                        <th>Penonton</th>
                        <th>Likes</th>
                        <th>Komentar</th>
                        <th>Share</th>
                        <th>STU</th>
                        <th class="text-center">SS</th>
                        <th class="text-end">Aksi Export & Opsi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form Input / Edit -->
<div class="modal fade" id="modalFormLive" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-primary text-white p-4 rounded-top-4">
                <h5 class="modal-title fw-bold" id="modalTitle"><i class="fab fa-tiktok me-2"></i> Input Laporan Live TikTok</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formLiveTikTok" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" id="report_id">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Pilih Cabang -->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">Cabang <span class="text-danger">*</span></label>
                            <select name="branch_id" id="form_branch_id" class="form-select rounded-3 shadow-none" required>
                                @if(auth()->user()->hasRole('PIC Digital Cabang'))
                                    <option value="{{ auth()->user()->branch_id }}" selected>{{ auth()->user()->branch->nama_cabang }}</option>
                                @else
                                    <option value="">-- Pilih Cabang --</option>
                                    @foreach($branches as $b)
                                        <option value="{{ $b->id }}">{{ $b->nama_cabang }} ({{ $b->kode }})</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Tanggal Live -->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">Tanggal Live <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_live" id="form_tanggal_live" class="form-control rounded-3 shadow-none" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <!-- Nama Host -->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">Nama Yang Live (Host) <span class="text-danger">*</span></label>
                            <input type="text" name="nama_host" id="form_nama_host" class="form-control rounded-3 shadow-none" placeholder="Contoh: Rina Setyowati / Team Sales Pekanbaru" required>
                        </div>

                        <!-- Jabatan -->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">Jabatan Host <span class="text-danger">*</span></label>
                            <select name="jabatan" id="form_jabatan" class="form-select rounded-3 shadow-none" required>
                                <option value="PIC Digital">PIC Digital</option>
                                <option value="Sales Digital">Sales Digital</option>
                            </select>
                        </div>

                        <!-- Durasi Live -->
                        <div class="col-6 col-md-3">
                            <label class="form-label fw-semibold small">Durasi Jam <span class="text-danger">*</span></label>
                            <input type="number" name="durasi_jam" id="form_durasi_jam" class="form-control rounded-3 shadow-none" min="0" max="24" value="1" required>
                        </div>

                        <div class="col-6 col-md-3">
                            <label class="form-label fw-semibold small">Durasi Menit <span class="text-danger">*</span></label>
                            <input type="number" name="durasi_menit" id="form_durasi_menit" class="form-control rounded-3 shadow-none" min="0" max="59" value="0" required>
                        </div>

                        <!-- Metric Fields -->
                        <div class="col-6 col-md-3">
                            <label class="form-label fw-semibold small">Jumlah Penonton <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah_penonton" id="form_jumlah_penonton" class="form-control rounded-3 shadow-none" min="0" value="0" required>
                        </div>

                        <div class="col-6 col-md-3">
                            <label class="form-label fw-semibold small">Jumlah Likes <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah_like" id="form_jumlah_like" class="form-control rounded-3 shadow-none" min="0" value="0" required>
                        </div>

                        <div class="col-6 col-md-4">
                            <label class="form-label fw-semibold small">Jumlah Komentar <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah_komentar" id="form_jumlah_komentar" class="form-control rounded-3 shadow-none" min="0" value="0" required>
                        </div>

                        <div class="col-6 col-md-4">
                            <label class="form-label fw-semibold small">Jumlah Share <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah_share" id="form_jumlah_share" class="form-control rounded-3 shadow-none" min="0" value="0" required>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small">STU (Sales To Unit) <span class="text-muted">(Opsional)</span></label>
                            <input type="number" name="stu" id="form_stu" class="form-control rounded-3 shadow-none" min="0" placeholder="Contoh: 2">
                        </div>

                        <!-- Upload Bukti Screenshot -->
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Upload Bukti Screenshot Sesi Live <span class="text-muted">(Format JPG, PNG, max 5MB)</span></label>
                            <input type="file" name="bukti_screenshot" id="form_bukti_screenshot" class="form-control rounded-3 shadow-none" accept="image/*">
                            <div id="previewContainer" class="mt-3 d-none">
                                <span class="text-muted small d-block mb-1">Preview Screenshot Terpilih:</span>
                                <img id="imagePreview" src="" class="img-thumbnail rounded-3 shadow-sm" style="max-height: 180px; object-fit: contain;">
                            </div>
                        </div>

                        <!-- Catatan -->
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Catatan / Keterangan Tambahan</label>
                            <textarea name="catatan" id="form_catatan" class="form-control rounded-3 shadow-none" rows="2" placeholder="Catatan topik live, promo menarik, atau kendala teknis..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-yamaha-primary rounded-pill px-4 shadow-sm" id="btnSimpan">
                        <i class="fas fa-save me-1.5"></i> Simpan Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal View Screenshot Fullscreen -->
<div class="modal fade" id="modalViewSS" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg bg-dark text-white rounded-4">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold"><i class="fas fa-image me-2"></i> Bukti Screenshot Live TikTok</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-3">
                <img id="fullSSImage" src="" class="img-fluid rounded-3 shadow" style="max-height: 80vh;">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const table = $('#tiktokTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('reports.tiktok-live.index') }}",
                data: function(d) {
                    d.branch_id = $('#filter_branch').val();
                    d.tanggal = $('#filter_tanggal').val();
                    d.tanggal_akhir = $('#filter_tanggal_akhir').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'tanggal_live', name: 'tanggal_live' },
                { data: 'nama_cabang', name: 'branch.nama_cabang' },
                { data: 'nama_host', name: 'nama_host', render: function(data) {
                    return `<strong>${data}</strong>`;
                }},
                { data: 'jabatan', name: 'jabatan', render: function(data) {
                    const badgeClass = data === 'PIC Digital' ? 'bg-primary' : 'bg-success';
                    return `<span class="badge ${badgeClass} rounded-pill px-2 py-1">${data}</span>`;
                }},
                { data: 'durasi_formatted', name: 'durasi_formatted', render: function(data) {
                    return `<span class="badge bg-secondary rounded-pill">${data}</span>`;
                }},
                { data: 'jumlah_penonton', name: 'jumlah_penonton' },
                { data: 'jumlah_like', name: 'jumlah_like' },
                { data: 'jumlah_komentar', name: 'jumlah_komentar' },
                { data: 'jumlah_share', name: 'jumlah_share' },
                { data: 'stu', name: 'stu', render: function(data) {
                    return data !== '-' ? `<span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1">${data}</span>` : '<span class="text-muted">-</span>';
                }},
                { data: 'bukti_screenshot_url', name: 'bukti_screenshot', className: 'text-center', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end' }
            ],
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
            }
        });

        // Filter events
        $('#filter_branch, #filter_tanggal, #filter_tanggal_akhir').on('change', function() {
            table.draw();
        });

        $('#btnResetFilter').on('click', function() {
            $('#filter_tanggal').val('');
            $('#filter_tanggal_akhir').val('');
            @if(!auth()->user()->hasRole('PIC Digital Cabang'))
                $('#filter_branch').val('');
            @endif
            table.draw();
        });

        // Global Export Handlers
        $('#btnExportPdf').on('click', function() {
            const branchId = $('#filter_branch').val() || '';
            const tanggal = $('#filter_tanggal').val() || '';
            const tanggalAkhir = $('#filter_tanggal_akhir').val() || '';
            
            let url = "{{ route('exports.pdf') }}?report_type=tiktok_live";
            if (branchId) url += "&branch_id=" + encodeURIComponent(branchId);
            if (tanggal && !tanggalAkhir) url += "&tanggal=" + encodeURIComponent(tanggal);
            if (tanggal && tanggalAkhir) url += "&tanggal_awal=" + encodeURIComponent(tanggal) + "&tanggal_akhir=" + encodeURIComponent(tanggalAkhir);
            
            window.open(url, '_blank');
        });

        $('#btnExportExcel').on('click', function() {
            const branchId = $('#filter_branch').val() || '';
            const tanggal = $('#filter_tanggal').val() || '';
            const tanggalAkhir = $('#filter_tanggal_akhir').val() || '';
            
            let url = "{{ route('exports.excel') }}?report_type=tiktok_live";
            if (branchId) url += "&branch_id=" + encodeURIComponent(branchId);
            if (tanggal && !tanggalAkhir) url += "&tanggal=" + encodeURIComponent(tanggal);
            if (tanggal && tanggalAkhir) url += "&tanggal_awal=" + encodeURIComponent(tanggal) + "&tanggal_akhir=" + encodeURIComponent(tanggalAkhir);
            
            window.location.href = url;
        });

        // Open Modal Tambah
        $('#btnTambahLaporan').on('click', function() {
            $('#formLiveTikTok')[0].reset();
            $('#report_id').val('');
            $('#modalTitle').html('<i class="fab fa-tiktok me-2"></i> Input Laporan Live TikTok');
            $('#previewContainer').addClass('d-none');
            $('#imagePreview').attr('src', '');
            $('#modalFormLive').modal('show');
        });

        // Live Image File Preview
        $('#form_bukti_screenshot').on('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').attr('src', e.target.result);
                    $('#previewContainer').removeClass('d-none');
                }
                reader.readAsDataURL(file);
            }
        });

        // Submit Form AJAX
        $('#formLiveTikTok').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            $('#btnSimpan').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');

            $.ajax({
                url: "{{ route('reports.tiktok-live.store') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#btnSimpan').prop('disabled', false).html('<i class="fas fa-save me-1.5"></i> Simpan Laporan');
                    if (response.success) {
                        $('#modalFormLive').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        table.draw();
                    }
                },
                error: function(xhr) {
                    $('#btnSimpan').prop('disabled', false).html('<i class="fas fa-save me-1.5"></i> Simpan Laporan');
                    let errorMsg = 'Terjadi kesalahan saat menyimpan data.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errs = Object.values(xhr.responseJSON.errors).flat();
                        errorMsg = errs.join('<br>');
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        html: errorMsg
                    });
                }
            });
        });

        // Edit Button Click
        $(document).on('click', '.btn-edit', function() {
            const id = $(this).data('id');
            $.get("{{ url('reports/tiktok-live') }}/" + id, function(data) {
                $('#report_id').val(data.id);
                $('#form_branch_id').val(data.branch_id);
                $('#form_tanggal_live').val(data.tanggal_live.split('T')[0]);
                $('#form_nama_host').val(data.nama_host);
                $('#form_jabatan').val(data.jabatan);
                $('#form_durasi_jam').val(data.durasi_jam);
                $('#form_durasi_menit').val(data.durasi_menit);
                $('#form_jumlah_penonton').val(data.jumlah_penonton);
                $('#form_jumlah_like').val(data.jumlah_like);
                $('#form_jumlah_komentar').val(data.jumlah_komentar);
                $('#form_jumlah_share').val(data.jumlah_share);
                $('#form_stu').val(data.stu);
                $('#form_catatan').val(data.catatan);

                if (data.bukti_screenshot) {
                    $('#imagePreview').attr('src', data.bukti_screenshot);
                    $('#previewContainer').removeClass('d-none');
                } else {
                    $('#previewContainer').addClass('d-none');
                }

                $('#modalTitle').html('<i class="fas fa-edit me-2"></i> Edit Laporan Live TikTok');
                $('#modalFormLive').modal('show');
            });
        });

        // Delete Button Click
        $(document).on('click', '.btn-delete', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Hapus Laporan ini?',
                text: "Data laporan Live TikTok beserta bukti screenshot akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('reports/tiktok-live') }}/" + id,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                table.draw();
                            }
                        }
                    });
                }
            });
        });

        // View Screenshot Modal
        $(document).on('click', '.btn-view-ss', function() {
            const url = $(this).data('url');
            $('#fullSSImage').attr('src', url);
            $('#modalViewSS').modal('show');
        });
    });
</script>
@endpush
