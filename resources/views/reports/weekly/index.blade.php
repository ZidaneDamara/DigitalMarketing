@extends('layouts.app')

@section('title', 'Weekly Report (Post Insight)')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1"><i class="fas fa-chart-line text-primary me-2"></i>Weekly Report - Post Insight</h3>
            <p class="text-muted mb-0">Laporan mingguan performa postingan digital, sumber trafik, interaksi audience, dan demografi.</p>
        </div>
        <div>
            <button type="button" class="btn btn-yamaha-primary shadow-sm rounded-pill px-4" id="btnTambahReport">
                <i class="fas fa-plus-circle me-2"></i>Input Post Insight Mingguan
            </button>
        </div>
    </div>

    <!-- Overview Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom stat-card border-0 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Total Post Recorded</span>
                        <h3 class="fw-bold text-dark mt-1 mb-0">{{ number_format($totalPosts) }}</h3>
                    </div>
                    <div class="icon-box bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-photo-video"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom stat-card border-0 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Total Post Views</span>
                        <h3 class="fw-bold text-success mt-1 mb-0">{{ number_format($totalViews) }}</h3>
                    </div>
                    <div class="icon-box bg-success bg-opacity-10 text-success">
                        <i class="fas fa-eye"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom stat-card border-0 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Account Reached</span>
                        <h3 class="fw-bold text-info mt-1 mb-0">{{ number_format($totalReach) }}</h3>
                    </div>
                    <div class="icon-box bg-info bg-opacity-10 text-info">
                        <i class="fas fa-users-viewfinder"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom stat-card border-0 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Total Interactions</span>
                        <h3 class="fw-bold text-warning mt-1 mb-0">{{ number_format($totalInteractions) }}</h3>
                    </div>
                    <div class="icon-box bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-comments"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Table Card -->
    <div class="card card-custom border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-0 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <h5 class="fw-bold m-0 text-dark"><i class="fas fa-list text-danger me-2"></i>Daftar Post Insight Mingguan</h5>

            <div class="d-flex flex-wrap gap-2 align-items-center ms-auto">
                @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Area Manager'))
                <select id="filter_branch_id" class="form-select form-select-sm rounded-3 shadow-none" style="width: 180px;">
                    <option value="">-- Semua Cabang --</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}">{{ $b->nama_cabang }}</option>
                    @endforeach
                </select>
                @endif

                <select id="filter_tahun" class="form-select form-select-sm rounded-3 shadow-none" style="width: 120px;">
                    <option value="2026" selected>2026</option>
                    <option value="2025">2025</option>
                </select>

                <button class="btn btn-sm btn-light border rounded-3" id="btnFilterReset">
                    <i class="fas fa-sync-alt me-1"></i> Reset
                </button>
            </div>
        </div>

        <div class="card-body p-3 p-md-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle w-100" id="weeklyReportTable">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Tanggal Post</th>
                            <th>Cabang</th>
                            <th>Link Content</th>
                            <th>Views</th>
                            <th>Reach</th>
                            <th>Total Interaksi</th>
                            <th>Followers Ratio</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    #modalWeeklyReport .modal-dialog-scrollable .modal-content,
    #modalViewDetail .modal-dialog-scrollable .modal-content {
        max-height: 85vh !important;
    }
    #modalWeeklyReport form {
        display: flex;
        flex-direction: column;
        height: 100%;
        max-height: 85vh;
        overflow: hidden;
    }
    #modalWeeklyReport .modal-body,
    #modalViewDetail .modal-body {
        max-height: calc(85vh - 130px) !important;
        overflow-y: auto !important;
    }
</style>
@endpush

<!-- Modal Form Input/Edit Post Insight -->
<div class="modal fade" id="modalWeeklyReport" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form id="formWeeklyReport" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <input type="hidden" name="id" id="report_id">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold" id="modalTitle"><i class="fas fa-plus-circle me-2"></i>Input Post Insight Mingguan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="max-height: calc(85vh - 130px); overflow-y: auto;">

                    <!-- Section 1: Header Meta Info -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3"><i class="fas fa-info-circle me-2"></i>Informasi Konten & Periode</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Pilih Cabang <span class="text-danger">*</span></label>
                                <select name="branch_id" id="branch_id" class="form-select rounded-3" required {{ auth()->user()->hasRole('PIC Digital Cabang') ? 'disabled' : '' }}>
                                    @foreach($branches as $b)
                                        <option value="{{ $b->id }}" {{ auth()->user()->branch_id == $b->id ? 'selected' : '' }}>{{ $b->nama_cabang }}</option>
                                    @endforeach
                                </select>
                                @if(auth()->user()->hasRole('PIC Digital Cabang'))
                                    <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id }}">
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Post <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_post" id="tanggal_post" class="form-control rounded-3" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Link Content (Instagram/TikTok/FB URL) <span class="text-danger">*</span></label>
                                <input type="url" name="link_content" id="link_content" class="form-control rounded-3" placeholder="https://www.instagram.com/p/..." required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-semibold">Tahun <span class="text-danger">*</span></label>
                                <input type="number" name="tahun" id="tahun" class="form-control rounded-3" value="2026" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-semibold">Minggu Ke <span class="text-danger">*</span></label>
                                <input type="number" name="minggu_ke" id="minggu_ke" class="form-control rounded-3" value="30" min="1" max="53" required>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Views & Reached -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-success border-bottom pb-2 mb-3"><i class="fas fa-chart-bar me-2"></i>Performa Views & Account Reached</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Views Postingan <span class="text-danger">*</span></label>
                                <input type="number" name="views" id="views" class="form-control rounded-3" min="0" value="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Account Reached <span class="text-danger">*</span></label>
                                <input type="number" name="account_reached" id="account_reached" class="form-control rounded-3" min="0" value="0" required>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Detailed Interactions -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-warning border-bottom pb-2 mb-3"><i class="fas fa-heart me-2"></i>Interactions Breakdown & Rasio Followers</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Interaksi dari Followers <span class="text-danger">*</span></label>
                                <input type="number" name="interactions_followers" id="interactions_followers" class="form-control rounded-3" min="0" value="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Interaksi dari Non-Followers <span class="text-danger">*</span></label>
                                <input type="number" name="interactions_non_followers" id="interactions_non_followers" class="form-control rounded-3" min="0" value="0" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Likes</label>
                                <input type="number" name="likes" id="likes" class="form-control rounded-3" min="0" value="0" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Shares</label>
                                <input type="number" name="shares" id="shares" class="form-control rounded-3" min="0" value="0" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Saves</label>
                                <input type="number" name="saves" id="saves" class="form-control rounded-3" min="0" value="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Comments</label>
                                <input type="number" name="comments" id="comments" class="form-control rounded-3" min="0" value="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Reposts</label>
                                <input type="number" name="reposts" id="reposts" class="form-control rounded-3" min="0" value="0" required>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Profile Activity -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-info border-bottom pb-2 mb-3"><i class="fas fa-user-circle me-2"></i>Profile Activity</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Profile Visits</label>
                                <input type="number" name="profile_visits" id="profile_visits" class="form-control rounded-3" min="0" value="0" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">External Link Taps</label>
                                <input type="number" name="external_link_taps" id="external_link_taps" class="form-control rounded-3" min="0" value="0" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Follows</label>
                                <input type="number" name="follows" id="follows" class="form-control rounded-3" min="0" value="0" required>
                            </div>
                        </div>
                    </div>

                    <!-- Section 5: Top Sources (%) -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-danger border-bottom pb-2 mb-3"><i class="fas fa-compass me-2"></i>Top Sources (%)</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Feed (%)</label>
                                <input type="number" step="0.1" name="source_feed_pct" id="source_feed_pct" class="form-control rounded-3" min="0" max="100" value="0.0" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Profile (%)</label>
                                <input type="number" step="0.1" name="source_profile_pct" id="source_profile_pct" class="form-control rounded-3" min="0" max="100" value="0.0" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Stories (%)</label>
                                <input type="number" step="0.1" name="source_stories_pct" id="source_stories_pct" class="form-control rounded-3" min="0" max="100" value="0.0" required>
                            </div>
                        </div>
                    </div>

                    <!-- Section 6: Audience Demographics -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="fas fa-users me-2"></i>Audience Breakdown (Gender, Country, Age)</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Gender Men (%)</label>
                                <input type="number" step="0.1" name="gender_men_pct" id="gender_men_pct" class="form-control rounded-3" min="0" max="100" value="50.0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Gender Women (%)</label>
                                <input type="number" step="0.1" name="gender_women_pct" id="gender_women_pct" class="form-control rounded-3" min="0" max="100" value="50.0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Top Country</label>
                                <input type="text" name="top_country" id="top_country" class="form-control rounded-3" placeholder="Indonesia (95%), Malaysia (3%)" value="Indonesia">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Age Distribution</label>
                                <input type="text" name="top_age" id="top_age" class="form-control rounded-3" placeholder="18-24 (45%), 25-34 (40%)" value="18-24 (45%), 25-34 (40%)">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Catatan / Keterangan Insight</label>
                                <textarea name="catatan" id="catatan" class="form-control rounded-3" rows="2" placeholder="Catatan mengenai performa postingan..."></textarea>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-yamaha-primary rounded-pill px-4" id="btnSubmit"><i class="fas fa-save me-1"></i> Simpan Data</button>
                </div>
        </form>
    </div>
</div>

<!-- Modal View Detail Post Insight -->
<div class="modal fade" id="modalViewDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title fw-bold"><i class="fas fa-eye me-2"></i>Detail Post Insight Performa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="viewDetailBody" style="max-height: calc(85vh - 130px); overflow-y: auto;">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const table = $('#weeklyReportTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('reports.weekly.index') }}",
                data: function(d) {
                    d.branch_id = $('#filter_branch_id').val();
                    d.tahun = $('#filter_tahun').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'tanggal_post', name: 'tanggal_post' },
                { data: 'nama_cabang', name: 'branch.nama_cabang' },
                { data: 'link_badge', name: 'link_content', orderable: false },
                { data: 'views_formatted', name: 'views' },
                { data: 'reach_formatted', name: 'account_reached' },
                { data: 'total_interactions', name: 'total_interactions', orderable: false },
                { data: 'followers_ratio', name: 'followers_ratio', orderable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[1, 'desc']],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entry",
                paginate: { first: "Awal", last: "Akhir", next: "→", previous: "←" }
            }
        });

        $('#filter_branch_id, #filter_tahun').on('change', function() {
            table.draw();
        });

        $('#btnFilterReset').on('click', function() {
            $('#filter_branch_id').val('');
            $('#filter_tahun').val('2026');
            table.draw();
        });

        $('#btnTambahReport').on('click', function() {
            $('#formWeeklyReport')[0].reset();
            $('#report_id').val('');
            $('#modalTitle').html('<i class="fas fa-plus-circle me-2"></i>Input Post Insight Mingguan');
            $('#modalWeeklyReport').modal('show');
        });

        $('#formWeeklyReport').on('submit', function(e) {
            e.preventDefault();
            const btn = $('#btnSubmit');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');

            $.ajax({
                url: "{{ route('reports.weekly.store') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    $('#modalWeeklyReport').modal('hide');
                    table.draw();
                    Swal.fire('Berhasil!', response.message, 'success');
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan saat menyimpan data.';
                    Swal.fire('Error!', msg, 'error');
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Simpan Data');
                }
            });
        });

        $(document).on('click', '.btn-edit', function() {
            const id = $(this).data('id');
            $.get("{{ url('/reports/weekly') }}/" + id, function(data) {
                $('#report_id').val(data.id);
                $('#branch_id').val(data.branch_id);
                $('#tanggal_post').val(data.tanggal_post ? data.tanggal_post.substring(0, 10) : '');
                $('#link_content').val(data.link_content);
                $('#tahun').val(data.tahun);
                $('#minggu_ke').val(data.minggu_ke);
                $('#views').val(data.views);
                $('#account_reached').val(data.account_reached);
                $('#interactions_followers').val(data.interactions_followers);
                $('#interactions_non_followers').val(data.interactions_non_followers);
                $('#likes').val(data.likes);
                $('#shares').val(data.shares);
                $('#saves').val(data.saves);
                $('#comments').val(data.comments);
                $('#reposts').val(data.reposts);
                $('#profile_visits').val(data.profile_visits);
                $('#external_link_taps').val(data.external_link_taps);
                $('#follows').val(data.follows);
                $('#source_feed_pct').val(data.source_feed_pct);
                $('#source_profile_pct').val(data.source_profile_pct);
                $('#source_stories_pct').val(data.source_stories_pct);
                $('#gender_men_pct').val(data.gender_men_pct);
                $('#gender_women_pct').val(data.gender_women_pct);
                $('#top_country').val(data.top_country);
                $('#top_age').val(data.top_age);
                $('#catatan').val(data.catatan);

                $('#modalTitle').html('<i class="fas fa-edit me-2"></i>Edit Post Insight Mingguan');
                $('#modalWeeklyReport').modal('show');
            });
        });

        $(document).on('click', '.btn-view', function() {
            const id = $(this).data('id');
            $('#modalViewDetail').modal('show');
            $('#viewDetailBody').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>');

            $.get("{{ url('/reports/weekly') }}/" + id, function(data) {
                const totalInteractions = parseInt(data.interactions_followers) + parseInt(data.interactions_non_followers);
                const html = `
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <span class="text-muted small">Cabang</span>
                            <h6 class="fw-bold text-primary fs-5 mb-0">${data.branch ? data.branch.nama_cabang : '-'}</h6>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <span class="text-muted small">Tanggal Post</span>
                            <h6 class="fw-bold text-dark fs-5 mb-0">${data.tanggal_post ? data.tanggal_post.substring(0, 10) : '-'} (Minggu ke-${data.minggu_ke})</h6>
                        </div>
                        <div class="col-12">
                            <a href="${data.link_content}" target="_blank" class="text-truncate d-inline-block w-100 text-decoration-none fw-semibold"><i class="fas fa-link me-1"></i> ${data.link_content}</a>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <span class="text-muted small d-block">Views</span>
                                <span class="fs-4 fw-bold text-success">${data.views.toLocaleString()}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <span class="text-muted small d-block">Account Reached</span>
                                <span class="fs-4 fw-bold text-info">${data.account_reached.toLocaleString()}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <span class="text-muted small d-block">Total Interaksi</span>
                                <span class="fs-4 fw-bold text-warning">${totalInteractions.toLocaleString()}</span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="card card-custom p-3">
                                <h6 class="fw-bold text-dark border-bottom pb-2"><i class="fas fa-heart text-danger me-2"></i>Interaksi Breakdown</h6>
                                <ul class="list-unstyled mb-0">
                                    <li class="d-flex justify-content-between py-1 border-bottom"><span>Followers</span><span class="fw-bold text-success">${parseInt(data.interactions_followers).toLocaleString()}</span></li>
                                    <li class="d-flex justify-content-between py-1 border-bottom"><span>Non-Followers</span><span class="fw-bold text-warning">${parseInt(data.interactions_non_followers).toLocaleString()}</span></li>
                                    <li class="d-flex justify-content-between py-1"><span>Likes / Shares / Saves</span><span class="fw-bold">${data.likes} / ${data.shares} / ${data.saves}</span></li>
                                    <li class="d-flex justify-content-between py-1"><span>Comments / Reposts</span><span class="fw-bold">${data.comments} / ${data.reposts}</span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-custom p-3">
                                <h6 class="fw-bold text-dark border-bottom pb-2"><i class="fas fa-user-check text-primary me-2"></i>Profile Activity</h6>
                                <ul class="list-unstyled mb-0">
                                    <li class="d-flex justify-content-between py-1 border-bottom"><span>Profile Visits</span><span class="fw-bold">${data.profile_visits.toLocaleString()}</span></li>
                                    <li class="d-flex justify-content-between py-1 border-bottom"><span>External Link Taps</span><span class="fw-bold">${data.external_link_taps.toLocaleString()}</span></li>
                                    <li class="d-flex justify-content-between py-1"><span>Follows</span><span class="fw-bold text-success">${data.follows.toLocaleString()}</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card card-custom p-3">
                                <h6 class="fw-bold text-dark border-bottom pb-2"><i class="fas fa-compass text-info me-2"></i>Top Sources</h6>
                                <ul class="list-unstyled mb-0">
                                    <li class="d-flex justify-content-between py-1"><span>Feed</span><span class="fw-bold">${data.source_feed_pct}%</span></li>
                                    <li class="d-flex justify-content-between py-1"><span>Profile</span><span class="fw-bold">${data.source_profile_pct}%</span></li>
                                    <li class="d-flex justify-content-between py-1"><span>Stories</span><span class="fw-bold">${data.source_stories_pct}%</span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-custom p-3">
                                <h6 class="fw-bold text-dark border-bottom pb-2"><i class="fas fa-users text-secondary me-2"></i>Audience Demographics</h6>
                                <ul class="list-unstyled mb-0">
                                    <li class="d-flex justify-content-between py-1"><span>Gender</span><span class="fw-bold">Men ${data.gender_men_pct}% / Women ${data.gender_women_pct}%</span></li>
                                    <li class="d-flex justify-content-between py-1"><span>Country</span><span class="fw-bold">${data.top_country || '-'}</span></li>
                                    <li class="d-flex justify-content-between py-1"><span>Age</span><span class="fw-bold">${data.top_age || '-'}</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                `;
                $('#viewDetailBody').html(html);
            });
        });

        $(document).on('click', '.btn-delete', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data report mingguan akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('/reports/weekly') }}/" + id,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            table.draw();
                            Swal.fire('Terhapus!', response.message, 'success');
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', 'Gagal menghapus data.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
