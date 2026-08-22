@extends('layouts.app')

@section('title', 'Laporan Harian (Daily Report)')

@section('content')
<div class="card card-custom p-4 mb-4 border-top border-4 border-primary">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
        <div>
            <h5 class="fw-bold text-dark m-0"><i class="fas fa-calendar-day text-primary me-2"></i> Form Daily Report Activity</h5>
            <div class="text-muted small">
                Tanggal Berjalan: <strong>{{ date('d F Y', strtotime($today)) }}</strong> | Status Entry: 
                <span class="badge bg-success px-3 py-1"><i class="fas fa-unlock me-1"></i> OPEN (00:00 - 23:59)</span>
            </div>
        </div>
        @if(auth()->user()->hasRole('PIC Digital Cabang') || auth()->user()->hasRole('Super Admin'))
        <button class="btn btn-yamaha-primary" id="btn-input-daily"><i class="fas fa-plus-circle me-1"></i> Input Report Hari Ini</button>
        @endif
    </div>

    <div class="alert alert-info small mb-0 rounded-3">
        <i class="fas fa-info-circle me-1"></i> <strong>Ketentuan Sistem:</strong> Laporan harian hanya dapat diisi dan diperbarui untuk tanggal berjalan hari ini. Laporan hari-hari sebelumnya secara otomatis terkunci oleh sistem.
    </div>
</div>

<div class="card card-custom p-4">
    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-history text-secondary me-2"></i> Riwayat Daily Report Cabang</h6>

    <div class="table-responsive">
        <table class="table table-hover align-middle w-100" id="table-daily-reports">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Tanggal</th>
                    <th>Cabang</th>
                    <th>Total Post</th>
                    <th>Followers Growth</th>
                    <th>Google Rating</th>
                    <th>Catatan</th>
                    <th style="width: 100px;">Status</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Modal Form Daily Report -->
<div class="modal fade" id="modal-daily" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-header-title fw-bold m-0" id="modal-daily-title"><i class="fas fa-edit me-2"></i> Input Daily Report (Tanggal {{ date('d F Y', strtotime($today)) }})</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-daily">
                <div class="modal-body p-4">
                    <input type="hidden" name="tanggal" id="daily_tanggal" value="{{ $today }}">
                    
                    @if(auth()->user()->hasRole('Super Admin'))
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Cabang</label>
                        <select name="branch_id" id="daily_branch_id" class="form-select" required>
                            <option value="">-- Pilih Cabang --</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}">{{ $b->nama_cabang }} ({{ $b->kode }})</option>
                            @endforeach
                        </select>
                    </div>
                    @else
                    <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id }}">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Cabang Anda</label>
                        <input type="text" class="form-control" value="{{ $userBranch?->nama_cabang }} ({{ $userBranch?->kode }})" readonly>
                    </div>
                    @endif

                    <!-- Instagram -->
                    <h6 class="fw-bold text-danger mt-3 mb-2"><i class="fab fa-instagram me-1"></i> Aktivitas Instagram</h6>
                    <div class="row g-2 g-md-3 mb-3">
                        <div class="col-6 col-md-3">
                            <label class="form-label small">Feed Post</label>
                            <input type="number" name="ig_feed" id="ig_feed" class="form-control" value="0" min="0" required>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small">Reels Video</label>
                            <input type="number" name="ig_reels" id="ig_reels" class="form-control" value="0" min="0" required>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small">Story Upload</label>
                            <input type="number" name="ig_story" id="ig_story" class="form-control" value="0" min="0" required>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small">Followers Bertambah</label>
                            <input type="number" name="ig_followers_gained" id="ig_followers_gained" class="form-control" value="0" min="0" required>
                        </div>
                    </div>

                    <!-- Facebook -->
                    <h6 class="fw-bold text-primary mb-2"><i class="fab fa-facebook me-1"></i> Aktivitas Facebook</h6>
                    <div class="row g-2 g-md-3 mb-3">
                        <div class="col-6 col-md-4">
                            <label class="form-label small">FB Post</label>
                            <input type="number" name="fb_post" id="fb_post" class="form-control" value="0" min="0" required>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label small">Marketplace Listing</label>
                            <input type="number" name="fb_marketplace" id="fb_marketplace" class="form-control" value="0" min="0" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small">FB Followers Bertambah</label>
                            <input type="number" name="fb_followers_gained" id="fb_followers_gained" class="form-control" value="0" min="0" required>
                        </div>
                    </div>

                    <!-- TikTok -->
                    <h6 class="fw-bold text-dark mb-2"><i class="fab fa-tiktok me-1"></i> Aktivitas TikTok</h6>
                    <div class="row g-2 g-md-3 mb-3">
                        <div class="col-6 col-md-4">
                            <label class="form-label small">TikTok Video</label>
                            <input type="number" name="tiktok_post" id="tiktok_post" class="form-control" value="0" min="0" required>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label small">TikTok Live (Sesi)</label>
                            <input type="number" name="tiktok_live" id="tiktok_live" class="form-control" value="0" min="0" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small">TikTok Followers Bertambah</label>
                            <input type="number" name="tiktok_followers_gained" id="tiktok_followers_gained" class="form-control" value="0" min="0" required>
                        </div>
                    </div>

                    <!-- Google Business Profile -->
                    <h6 class="fw-bold text-success mb-2"><i class="fab fa-google me-1"></i> Google Business Profile</h6>
                    <div class="row g-2 g-md-3 mb-3">
                        <div class="col-6 col-md-6">
                            <label class="form-label small">Update Rating</label>
                            <input type="number" step="0.1" name="google_rating" id="google_rating" class="form-control" value="4.9" min="0" max="5" required>
                        </div>
                        <div class="col-6 col-md-6">
                            <label class="form-label small">Ulasan Baru Bertambah</label>
                            <input type="number" name="google_review_gained" id="google_review_gained" class="form-control" value="0" min="0" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan Activity / Kendala Cabang</label>
                        <textarea name="catatan" id="catatan" class="form-control" rows="2" placeholder="Catatan promosi harian cabang..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-yamaha-primary rounded-pill px-4">Simpan Laporan</button>
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

        const table = $('#table-daily-reports').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('reports.daily.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'tanggal', name: 'tanggal' },
                { data: 'nama_cabang', name: 'nama_cabang' },
                { data: 'total_post', name: 'total_post' },
                { data: 'followers_gained', name: 'followers_gained' },
                { data: 'google_rating', name: 'google_rating' },
                { data: 'catatan', name: 'catatan', defaultContent: '-' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        $('#btn-input-daily').click(function () {
            $('#form-daily')[0].reset();
            $('#daily_tanggal').val('{{ $today }}');
            $('#modal-daily-title').html('<i class="fas fa-plus-circle me-2"></i> Input Daily Report (Tanggal {{ date('d F Y', strtotime($today)) }})');
            $('#modal-daily').modal('show');
        });

        $(document).on('click', '.btn-edit', function () {
            const id = $(this).data('id');
            $.get("{{ url('/reports/daily') }}/" + id, function (data) {
                if (data) {
                    const tgl = data.tanggal ? data.tanggal.substring(0, 10) : '{{ $today }}';
                    $('#daily_tanggal').val(tgl);
                    
                    if ($('#daily_branch_id').is('select')) {
                        $('#daily_branch_id').val(data.branch_id);
                    }
                    
                    $('#ig_feed').val(data.ig_feed);
                    $('#ig_reels').val(data.ig_reels);
                    $('#ig_story').val(data.ig_story);
                    $('#ig_followers_gained').val(data.ig_followers_gained);
                    
                    $('#fb_post').val(data.fb_post);
                    $('#fb_marketplace').val(data.fb_marketplace);
                    $('#fb_followers_gained').val(data.fb_followers_gained);
                    
                    $('#tiktok_post').val(data.tiktok_post);
                    $('#tiktok_live').val(data.tiktok_live);
                    $('#tiktok_followers_gained').val(data.tiktok_followers_gained);
                    
                    $('#google_rating').val(data.google_rating);
                    $('#google_review_gained').val(data.google_review_gained);
                    
                    $('#catatan').val(data.catatan);
                    
                    $('#modal-daily-title').html('<i class="fas fa-edit me-2"></i> Edit Daily Report');
                    $('#modal-daily').modal('show');
                }
            });
        });

        $('#form-daily').submit(function (e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('reports.daily.store') }}",
                type: 'POST',
                data: $(this).serialize(),
                success: function (res) {
                    $('#modal-daily').modal('hide');
                    table.ajax.reload();
                    Swal.fire('Sukses', res.message, 'success');
                },
                error: function (xhr) {
                    Swal.fire('Terperinci Error', xhr.responseJSON?.message || 'Gagal menyimpan laporan.', 'error');
                }
            });
        });
    });
</script>
@endpush
