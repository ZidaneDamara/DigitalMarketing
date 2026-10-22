@extends('layouts.app')

@section('title', 'Executive Sales & Digital Marketing Dashboard')

@section('content')
<style>
    /* ========================================================= */
    /* EXECUTIVE DASHBOARD UNIFIED STYLING & RESPONSIVENESS     */
    /* ========================================================= */
    .icon-box-unified {
        width: 44px;
        height: 44px;
        min-width: 44px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.25rem;
    }
    .icon-box-unified.box-danger { background-color: #fee2e2; color: #dc2626; }
    .icon-box-unified.box-dark { background-color: #0f172a; color: #ffffff; }
    .icon-box-unified.box-primary { background-color: #dbeafe; color: #2563eb; }
    .icon-box-unified.box-warning { background-color: #fef3c7; color: #b45309; }
    .icon-box-unified.box-info { background-color: #e0f2fe; color: #0284c7; }
    .icon-box-unified.box-success { background-color: #dcfce7; color: #16a34a; }

    .icon-box-sm {
        width: 34px;
        height: 34px;
        min-width: 34px;
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1rem;
    }
    .icon-box-sm.box-danger { background-color: #fee2e2; color: #dc2626; }
    .icon-box-sm.box-dark { background-color: #0f172a; color: #ffffff; }
    .icon-box-sm.box-primary { background-color: #dbeafe; color: #2563eb; }
    .icon-box-sm.box-warning { background-color: #fef3c7; color: #b45309; }
    .icon-box-sm.box-info { background-color: #e0f2fe; color: #0284c7; }
    .icon-box-sm.box-success { background-color: #dcfce7; color: #16a34a; }
    .card-executive {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
        background: #ffffff;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card-executive:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.07);
    }
    .display-stat {
        font-size: clamp(1.6rem, 2.5vw, 2.2rem);
        font-weight: 800;
        line-height: 1.1;
        letter-spacing: -0.5px;
    }
    .table-executive {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }
    .table-executive th {
        font-size: 0.74rem;
        font-weight: 700;
        letter-spacing: 0.4px;
        text-transform: uppercase;
        vertical-align: middle;
        white-space: nowrap;
        padding: 0.85rem 0.65rem;
        border-bottom: 2px solid #e2e8f0;
    }
    .table-executive td {
        vertical-align: middle;
        font-size: 0.86rem;
        padding: 0.8rem 0.65rem;
        white-space: nowrap;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-executive tbody tr:hover td {
        background-color: #f8fafc;
    }
    .badge-trend-up {
        background-color: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
        font-weight: 700;
        font-size: 0.74rem;
        padding: 0.25rem 0.55rem;
        border-radius: 50rem;
    }
    .badge-trend-down {
        background-color: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
        font-weight: 700;
        font-size: 0.74rem;
        padding: 0.25rem 0.55rem;
        border-radius: 50rem;
    }
    .badge-trend-neutral {
        background-color: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        font-weight: 700;
        font-size: 0.74rem;
        padding: 0.25rem 0.55rem;
        border-radius: 50rem;
    }
    .badge-diff-positive {
        background-color: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 0.25rem 0.55rem;
        border-radius: 0.5rem;
    }
    .badge-diff-negative {
        background-color: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 0.25rem 0.55rem;
        border-radius: 0.5rem;
    }
    @media (max-width: 768px) {
        .table-executive th, .table-executive td {
            padding: 0.55rem 0.45rem;
            font-size: 0.78rem;
        }
    }
</style>

<div class="container-fluid px-0">

    <!-- TOP EXECUTIVE BANNER & PERIODE FILTER -->
    <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 mb-4" style="background: linear-gradient(135deg, #002266 0%, #001540 100%); color: #ffffff;">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1.5 flex-wrap">
                    <span class="badge bg-danger px-2.5 py-1 text-uppercase fw-bold letter-spacing-1">
                        <i class="fas fa-crown me-1 text-warning"></i> Executive Business View
                    </span>
                    <span class="badge px-2.5 py-1 text-white border border-white border-opacity-25" style="background-color: rgba(255, 255, 255, 0.18);">PT Aspacindo Kedaton Motor</span>
                    <span class="badge px-2.5 py-1 text-white border border-info border-opacity-50 small" style="background-color: rgba(14, 165, 233, 0.25);">
                        <i class="fas fa-calendar-day me-1"></i> Data s/d Hari Ini (Tgl {{ $metrics['cutoff_day'] }})
                    </span>
                </div>
                <h3 class="fw-bold text-white mb-1">Executive Analytics & Sales Performance Dashboard</h3>
                <p class="text-white-50 mb-0 small">
                    Monitoring Realisasi STU Tim Digital (Target 15%), Pipeline Prospek (Target 130%), Lineup Produk Yamaha, dan Efisiensi Bisnis Dealer.
                </p>
            </div>
            <div>
                <form action="{{ route('dashboard') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2 bg-white bg-opacity-10 p-2 rounded-3 border border-white border-opacity-15">
                    <div>
                        <select name="tahun" class="form-select form-select-sm bg-white text-dark fw-semibold border-0 shadow-none">
                            @for($y = 2024; $y <= 2028; $y++)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <select name="bulan" class="form-select form-select-sm bg-white text-dark fw-semibold border-0 shadow-none">
                            @php
                                $bulanList = [
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                ];
                            @endphp
                            @foreach($bulanList as $m => $namaM)
                                <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>{{ $namaM }}</option>
                            @endforeach
                        </select>
                    </div>
                    @unless(auth()->user()->hasRole('PIC Digital Cabang'))
                    <div>
                        <select name="branch_id" class="form-select form-select-sm bg-white text-dark fw-semibold border-0 shadow-none">
                            <option value="">-- Semua Cabang (Head Office) --</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>{{ $b->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endunless
                    <div>
                        <button type="submit" class="btn btn-sm btn-danger fw-bold px-3 shadow-sm">
                            <i class="fas fa-filter me-1.5"></i> Terapkan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Missing Daily Report Alert Widget (If Any) -->
    @if($metrics['missing_count_today'] > 0)
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 border-start border-danger border-4 bg-danger bg-opacity-10">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h6 class="fw-bold text-danger mb-1">
                    <i class="fas fa-exclamation-triangle me-2"></i> PERINGATAN: CABANG BELUM INPUT DAILY REPORT HARI INI
                </h6>
                <div class="text-dark small">Terdapat <strong>{{ $metrics['missing_count_today'] }} cabang</strong> yang belum mengirimkan Laporan Harian untuk tanggal berjalan:</div>
                <div class="mt-2 d-flex flex-wrap gap-2">
                    @foreach($metrics['missing_branches_today'] as $mb)
                        <span class="badge bg-danger text-white px-2.5 py-1.5 fw-semibold shadow-xs">
                            <i class="fas fa-store me-1"></i> {{ $mb->nama_cabang }} ({{ $mb->kode }})
                        </span>
                    @endforeach
                </div>
            </div>
            <div>
                <a href="{{ route('reports.daily.index') }}" class="btn btn-sm btn-danger font-weight-bold px-3 py-2 rounded-pill shadow-sm">
                    <i class="fas fa-edit me-1"></i> Form Input Report
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- ========================================================================= -->
    <!-- SECTION 1: HERO KPI METRIC CARDS (UNIFIED ICON ALIGNMENT)                 -->
    <!-- ========================================================================= -->
    <div class="row g-3 mb-4">
        <!-- 1. TOTAL DIGITAL STU & TARGET 15% -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card card-executive p-3 h-100 position-relative overflow-hidden border-top border-4 border-danger d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="icon-box-unified box-danger">
                                <i class="fas fa-motorcycle"></i>
                            </div>
                            <div>
                                <span class="badge bg-danger bg-opacity-10 text-danger fw-bold px-2 py-0.5" style="font-size: 0.7rem;">TOTAL REALISASI</span>
                                <div class="fw-bold text-dark" style="font-size: 0.95rem;">STU Tim Digital</div>
                            </div>
                        </div>
                        <span class="badge bg-light text-muted border small">{{ $metrics['nama_bulan'] }} {{ $tahun }}</span>
                    </div>

                    <div class="d-flex align-items-baseline gap-2 mt-2">
                        <h2 class="display-stat text-dark mb-0">{{ number_format($metrics['grand_total_stu']) }}</h2>
                        <span class="fs-6 text-muted fw-bold">/ {{ number_format($metrics['total_digital_stu_target']) }} Unit (15%)</span>
                        <span class="badge {{ $metrics['overall_digital_stu_achievement_pct'] >= 100 ? 'bg-success' : 'bg-danger' }} ms-auto fw-bold px-2.5 py-1">
                            {{ $metrics['overall_digital_stu_achievement_pct'] }}% Target
                        </span>
                    </div>
                    <p class="text-muted small mt-1 mb-2">Target Tim Digital ditetapkan <strong>15%</strong> dari target STU masing-masing dealer.</p>

                    <!-- Breakdown Channels -->
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        <span class="badge bg-dark text-white px-2.5 py-1 small rounded-pill">
                            <i class="fab fa-tiktok text-danger me-1"></i> Live: <strong>{{ $metrics['stu_tiktok_live'] }} Unit</strong> ({{ $metrics['stu_live_pct'] }}%)
                        </span>
                        <span class="badge bg-primary text-white px-2.5 py-1 small rounded-pill">
                            <i class="fab fa-facebook text-white me-1"></i> Ads: <strong>{{ $metrics['stu_meta_ads'] }} Unit</strong> ({{ $metrics['stu_ads_pct'] }}%)
                        </span>
                    </div>
                </div>

                <!-- Progress vs Dealer Target -->
                <div class="pt-2 border-top mt-auto">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Pencapaian vs Total STU Dealer ({{ $metrics['total_dealer_stu_target'] }} Unit):</span>
                        <strong class="text-dark">{{ $metrics['overall_dealer_stu_achievement_pct'] }}%</strong>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-danger" style="width: {{ min(100, $metrics['overall_dealer_stu_achievement_pct']) }}%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. STU TIKTOK LIVE -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card card-executive p-3 h-100 position-relative overflow-hidden border-top border-4 border-dark d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="icon-box-unified box-dark">
                                <i class="fab fa-tiktok"></i>
                            </div>
                            <div>
                                <span class="badge bg-dark text-white fw-bold px-2 py-0.5" style="font-size: 0.7rem;">STREAMING SALES</span>
                                <div class="fw-bold text-dark" style="font-size: 0.95rem;">STU Live TikTok</div>
                            </div>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success fw-bold small">
                            <i class="fas fa-video me-1"></i> Live Stream
                        </span>
                    </div>

                    <div class="d-flex align-items-baseline gap-2 mt-2">
                        <h2 class="display-stat text-dark mb-0">{{ number_format($metrics['stu_tiktok_live']) }}</h2>
                        <span class="fs-6 text-muted fw-bold">Unit STU</span>
                        <span class="badge bg-light text-dark border ms-auto small">{{ $metrics['stu_live_pct'] }}% Kontribusi</span>
                    </div>
                    <p class="text-muted small mt-1 mb-2">Closing unit langsung dari siaran streaming TikTok Live sales.</p>
                </div>

                <!-- Live Stream Stats Grid -->
                <div class="row g-2 pt-2 border-top mt-auto">
                    <div class="col-6">
                        <div class="p-2 rounded-3 bg-light text-center">
                            <div class="text-muted small">Total Jam Live</div>
                            <div class="fw-bold text-dark fs-6">{{ $metrics['total_live_hours'] }} Jam</div>
                            <div class="text-muted" style="font-size: 10px;">{{ $metrics['total_live_sessions'] }} Sesi Siaran</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 rounded-3 bg-light text-center">
                            <div class="text-muted small">Total Penonton</div>
                            <div class="fw-bold text-primary fs-6">{{ number_format($metrics['total_live_viewers']) }}</div>
                            <div class="text-muted" style="font-size: 10px;">{{ $metrics['avg_stu_per_live_session'] }} STU/Sesi</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. STU META ADS -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card card-executive p-3 h-100 position-relative overflow-hidden border-top border-4 border-primary d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="icon-box-unified box-primary">
                                <i class="fab fa-facebook"></i>
                            </div>
                            <div>
                                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-2 py-0.5" style="font-size: 0.7rem;">PAID ADS</span>
                                <div class="fw-bold text-dark" style="font-size: 0.95rem;">STU Meta Ads</div>
                            </div>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary fw-bold small">
                            <i class="fab fa-instagram me-1"></i> IG & FB Ads
                        </span>
                    </div>

                    <div class="d-flex align-items-baseline gap-2 mt-2">
                        <h2 class="display-stat text-dark mb-0">{{ number_format($metrics['stu_meta_ads']) }}</h2>
                        <span class="fs-6 text-muted fw-bold">Unit STU</span>
                        <span class="badge bg-light text-dark border ms-auto small">{{ $metrics['stu_ads_pct'] }}% Kontribusi</span>
                    </div>
                    <p class="text-muted small mt-1 mb-2">Closing unit dari kampanye iklan bersponsor Instagram & Facebook.</p>
                </div>

                <!-- Ads Conversion Stats Grid -->
                <div class="row g-2 pt-2 border-top mt-auto">
                    <div class="col-6">
                        <div class="p-2 rounded-3 bg-light text-center">
                            <div class="text-muted small">Cost per STU</div>
                            <div class="fw-bold text-danger fs-6">Rp {{ number_format($metrics['cost_per_stu'], 0, ',', '.') }}</div>
                            <div class="text-muted" style="font-size: 10px;">Budget / STU</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 rounded-3 bg-light text-center">
                            <div class="text-muted small">Conversion Rate</div>
                            <div class="fw-bold text-success fs-6">{{ $metrics['conversion_rate_prospect_to_stu'] }}%</div>
                            <div class="text-muted" style="font-size: 10px;">Prospek -> STU</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. TOTAL PROSPEK DIGITAL (TARGET 130%) -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card card-executive p-3 h-100 position-relative overflow-hidden border-top border-4 border-warning d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="icon-box-unified box-warning">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <span class="badge fw-bold px-2 py-0.5" style="font-size: 0.7rem; background-color: #fef3c7; color: #b45309;">PIPELINE LEADS</span>
                                <div class="fw-bold text-dark" style="font-size: 0.95rem;">Target Prospek (130%)</div>
                            </div>
                        </div>
                        <span class="badge {{ $metrics['overall_prospect_achievement_pct'] >= 100 ? 'bg-success' : 'bg-warning text-dark' }} fw-bold small">
                            {{ $metrics['overall_prospect_achievement_pct'] }}% Tercapai
                        </span>
                    </div>

                    <div class="d-flex align-items-baseline gap-2 mt-2">
                        <h2 class="display-stat text-dark mb-0">{{ number_format($metrics['total_prospects_digital']) }}</h2>
                        <span class="fs-6 text-muted fw-bold">/ {{ number_format($metrics['total_prospect_target']) }} Leads</span>
                    </div>
                    <p class="text-muted small mt-1 mb-2">Target prospek digital ditetapkan <strong>130%</strong> dari target STU masing-masing dealer.</p>
                </div>

                <!-- Progress Bar & Status -->
                <div class="pt-2 border-top mt-auto">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Pencapaian Target Prospek 130%:</span>
                        <strong class="{{ $metrics['overall_prospect_achievement_pct'] >= 100 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($metrics['total_prospects_digital']) }} dari {{ number_format($metrics['total_prospect_target']) }} Leads
                        </strong>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar {{ $metrics['overall_prospect_achievement_pct'] >= 100 ? 'bg-success' : 'bg-warning' }}" 
                             style="width: {{ min(100, $metrics['overall_prospect_achievement_pct']) }}%;"></div>
                    </div>
                    <div class="text-muted small mt-1.5 d-flex justify-content-between">
                        <span>Percakapan Iklan: <strong>{{ number_format($metrics['total_ads_conversations']) }}</strong></span>
                        <span>Avg CPL: <strong>Rp {{ number_format($metrics['avg_ads_cpl'], 0, ',', '.') }}</strong></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. FINANCIAL & ADS ROI EFFICIENCY -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card card-executive p-3 h-100 position-relative overflow-hidden border-top border-4 border-info d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="icon-box-unified box-info">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div>
                                <span class="badge fw-bold px-2 py-0.5" style="font-size: 0.7rem; background-color: #e0f2fe; color: #0284c7;">BUDGET SPEND</span>
                                <div class="fw-bold text-dark" style="font-size: 0.95rem;">Spend & Ads Efficiency</div>
                            </div>
                        </div>
                        <span class="badge bg-light text-muted border small">Meta Ads</span>
                    </div>

                    <div class="d-flex align-items-baseline gap-2 mt-2">
                        <h2 class="display-stat text-dark mb-0">Rp {{ number_format($metrics['total_ads_budget'], 0, ',', '.') }}</h2>
                    </div>
                    <p class="text-muted small mt-1 mb-2">Total belanja kampanye iklan digital berbayar seluruh cabang.</p>
                </div>

                <!-- Metric Pills -->
                <div class="row g-2 pt-2 border-top text-center mt-auto">
                    <div class="col-4">
                        <div class="p-1.5 rounded-2 bg-light">
                            <div class="text-muted" style="font-size: 11px;">CPM</div>
                            <div class="fw-bold text-dark small">Rp {{ number_format($metrics['avg_ads_cpm'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-1.5 rounded-2 bg-light">
                            <div class="text-muted" style="font-size: 11px;">CPC</div>
                            <div class="fw-bold text-dark small">Rp {{ number_format($metrics['avg_ads_cpc'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-1.5 rounded-2 bg-light">
                            <div class="text-muted" style="font-size: 11px;">CTR</div>
                            <div class="fw-bold text-success small">{{ $metrics['avg_ads_ctr'] }}%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. SYSTEM PRODUCTIVITY & REPUTATION -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card card-executive p-3 h-100 position-relative overflow-hidden border-top border-4 border-success d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="icon-box-unified box-success">
                                <i class="fas fa-award"></i>
                            </div>
                            <div>
                                <span class="badge fw-bold px-2 py-0.5" style="font-size: 0.7rem; background-color: #dcfce7; color: #16a34a;">KPI SCORE</span>
                                <div class="fw-bold text-dark" style="font-size: 0.95rem;">Productivity & Score</div>
                            </div>
                        </div>
                        <span class="badge {{ $metrics['status_badge']['bg_class'] }} rounded-pill px-2.5 py-1">
                            {{ $metrics['status_badge']['label'] }}
                        </span>
                    </div>

                    <div class="d-flex align-items-baseline gap-2 mt-2">
                        <h2 class="display-stat text-dark mb-0">{{ $metrics['overall_achievement'] }}%</h2>
                        <span class="fs-6 text-muted fw-semibold">Overall KPI Score</span>
                    </div>
                    <p class="text-muted small mt-1 mb-2">Indeks pencapaian komprehensif seluruh KPI dealer aktif.</p>
                </div>

                <!-- Highlights -->
                <div class="row g-2 pt-2 border-top text-center mt-auto">
                    <div class="col-4">
                        <div class="p-1.5 rounded-2 bg-light">
                            <div class="text-muted" style="font-size: 11px;">Konten Post</div>
                            <div class="fw-bold text-dark small">{{ number_format($metrics['total_posts']) }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-1.5 rounded-2 bg-light">
                            <div class="text-muted" style="font-size: 11px;">Followers</div>
                            <div class="fw-bold text-success small">+{{ number_format($metrics['followers_growth']) }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-1.5 rounded-2 bg-light">
                            <div class="text-muted" style="font-size: 11px;">Rating Google</div>
                            <div class="fw-bold text-primary small">⭐ {{ $metrics['avg_google_rating'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 2: EXECUTIVE AI & BUSINESS INSIGHTS BRIEFING                      -->
    <!-- ========================================================================= -->
    <div class="card card-executive p-3 p-md-4 mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2.5">
                <div class="icon-box-unified box-danger">
                    <i class="fas fa-brain"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-0">Executive Business Insights & Actionable Takeaways</h5>
                    <p class="text-muted small mb-0">Ringkasan analitik cerdas berdasarkan realisasi STU, funnel prospek dealer, dan evaluasi omni-channel.</p>
                </div>
            </div>
            <span class="badge bg-light text-dark border px-2.5 py-1.5 small">
                <i class="fas fa-bolt text-warning me-1"></i> Auto-Generated AI Briefing
            </span>
        </div>

        <div class="row g-3">
            @foreach($metrics['executive_insights'] as $insight)
            <div class="col-12 col-md-6 col-xl">
                <div class="p-3 rounded-3 h-100 border border-opacity-25 
                    @if($insight['type'] == 'success') bg-success bg-opacity-10 border-success
                    @elseif($insight['type'] == 'info') bg-info bg-opacity-10 border-info
                    @elseif($insight['type'] == 'warning') bg-warning bg-opacity-10 border-warning
                    @elseif($insight['type'] == 'danger') bg-danger bg-opacity-10 border-danger
                    @else bg-primary bg-opacity-10 border-primary @endif">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="{{ $insight['icon'] }} 
                            @if($insight['type'] == 'success') text-success
                            @elseif($insight['type'] == 'info') text-info
                            @elseif($insight['type'] == 'warning') text-warning-emphasis
                            @elseif($insight['type'] == 'danger') text-danger
                            @else text-primary @endif"></i>
                        <span class="fw-bold small text-dark">{{ $insight['title'] }}</span>
                    </div>
                    <div class="text-dark small lh-sm">{!! $insight['text'] !!}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 3: TABEL 1 - MONITORING REALISASI STU TIM DIGITAL (TARGET 15%)    -->
    <!-- ========================================================================= -->
    <div class="card card-executive p-3 p-md-4 mb-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-3">
            <div class="d-flex align-items-center gap-2.5">
                <div class="icon-box-unified box-danger">
                    <i class="fas fa-bullseye"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-danger px-2 py-0.5 small text-uppercase">Tabel 1</span>
                        <h5 class="fw-bold text-dark mb-0">Monitoring Kinerja STU (Sales To Unit) Tim Digital vs Target Dealer</h5>
                    </div>
                    <p class="text-muted small mb-0">
                        Target Tim Digital adalah <strong>15%</strong> dari Target STU Dealer. Kolom perbandingan MTD menunjukkan pencapaian s/d tanggal hari ini (Tgl {{ $metrics['cutoff_day'] }}) dibandingkan bulan lalu.
                    </p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-dark border px-3 py-2 fw-semibold">
                    <i class="fas fa-store me-1 text-primary"></i> {{ count($metrics['stu_scorecard']) }} Dealer Terdata
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-executive align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 text-center" style="width: 50px;">Rank</th>
                        <th>Dealer / Cabang</th>
                        <th class="text-center" style="background-color: #f8fafc;">Target STU Dealer</th>
                        <th class="text-center fw-bold" style="background-color: #fee2e2;">Target Tim Digital (15%)</th>
                        <th class="text-center" style="background-color: #f0fdf4;">STU Live TikTok</th>
                        <th class="text-center" style="background-color: #eff6ff;">STU Meta Ads</th>
                        <th class="text-center fw-extrabold text-danger" style="background-color: #fee2e2;">Realisasi STU (Bulan Ini)</th>
                        <th class="text-center">% Capaian Digital (15%)</th>
                        <th class="text-center fw-bold">+/- vs Target Digital</th>
                        <th class="text-center" style="background-color: #f1f5f9;">VS Last Month (Per Hari Ini)</th>
                        <th class="text-center">+/- LM</th>
                        <th class="text-center">Keterangan</th>
                        <th class="text-center pe-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($metrics['stu_scorecard'] as $row)
                    <tr>
                        <td class="ps-3 text-center">
                            @if($row['rank'] == 1)
                                <span class="badge bg-warning text-dark rounded-circle p-2 shadow-xs"><i class="fas fa-crown"></i> 1</span>
                            @elseif($row['rank'] == 2)
                                <span class="badge bg-secondary text-white rounded-circle p-2">2</span>
                            @elseif($row['rank'] == 3)
                                <span class="badge bg-danger text-white rounded-circle p-2">3</span>
                            @else
                                <span class="fw-bold text-muted">{{ $row['rank'] }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $row['nama_cabang'] }}</div>
                            <div class="text-muted small">
                                <span class="badge bg-light text-primary border me-1">{{ $row['kode'] }}</span>
                                <span class="d-none d-sm-inline">{{ $row['area'] }}</span>
                            </div>
                        </td>
                        <td class="text-center fw-bold text-dark" style="background-color: #f8fafc;">
                            {{ $row['dealer_stu_target'] }} <span class="small text-muted fw-normal">Unit</span>
                        </td>
                        <td class="text-center fw-bold text-danger fs-6" style="background-color: #fee2e2;">
                            {{ $row['digital_stu_target'] }} <span class="small text-muted fw-normal">Unit</span>
                        </td>
                        <td class="text-center fw-bold text-dark" style="background-color: #f0fdf4;">
                            <span class="badge bg-dark text-white px-2 py-1">
                                <i class="fab fa-tiktok me-1 text-danger"></i> {{ $row['stu_tiktok_live'] }}
                            </span>
                        </td>
                        <td class="text-center fw-bold text-dark" style="background-color: #eff6ff;">
                            <span class="badge bg-primary text-white px-2 py-1">
                                <i class="fab fa-facebook me-1"></i> {{ $row['stu_meta_ads'] }}
                            </span>
                        </td>
                        <td class="text-center fw-extrabold text-danger fs-5" style="background-color: #fee2e2;">
                            {{ $row['total_stu'] }} Unit
                        </td>
                        <td class="text-center" style="min-width: 110px;">
                            <div class="d-flex align-items-center justify-content-center gap-1 mb-1">
                                <strong class="small {{ $row['digital_stu_achievement_pct'] >= 100 ? 'text-success' : 'text-danger' }}">
                                    {{ $row['digital_stu_achievement_pct'] }}%
                                </strong>
                            </div>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar {{ $row['digital_stu_achievement_pct'] >= 100 ? 'bg-success' : ($row['digital_stu_achievement_pct'] >= 50 ? 'bg-primary' : 'bg-danger') }}" 
                                     style="width: {{ min(100, $row['digital_stu_achievement_pct']) }}%;"></div>
                            </div>
                        </td>
                        <td class="text-center">
                            @if($row['diff_vs_digital'] >= 0)
                                <span class="badge-diff-positive">
                                    <i class="fas fa-plus me-0.5"></i>{{ $row['diff_vs_digital'] }} Unit
                                </span>
                            @else
                                <span class="badge-diff-negative">
                                    {{ $row['diff_vs_digital'] }} Unit
                                </span>
                            @endif
                        </td>
                        <td class="text-center text-muted fw-semibold" style="background-color: #f1f5f9;">
                            {{ $row['last_month_stu_mtd'] }} <span class="small">Unit</span>
                        </td>
                        <td class="text-center fw-bold">
                            @if($row['diff_vs_last_month'] > 0)
                                <span class="text-success"><i class="fas fa-caret-up me-1"></i>+{{ $row['diff_vs_last_month'] }}</span>
                            @elseif($row['diff_vs_last_month'] < 0)
                                <span class="text-danger"><i class="fas fa-caret-down me-1"></i>{{ $row['diff_vs_last_month'] }}</span>
                            @else
                                <span class="text-muted">0</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($row['keterangan'] === 'Naik')
                                <span class="badge-trend-up">
                                    <i class="fas fa-arrow-trend-up me-1"></i> Naik
                                </span>
                            @elseif($row['keterangan'] === 'Turun')
                                <span class="badge-trend-down">
                                    <i class="fas fa-arrow-trend-down me-1"></i> Turun
                                </span>
                            @else
                                <span class="badge-trend-neutral">
                                    <i class="fas fa-minus me-1"></i> Stabil
                                </span>
                            @endif
                        </td>
                        <td class="text-center pe-3">
                            <span class="badge {{ $row['status_badge']['bg_class'] }} rounded-pill px-2.5 py-1 small">
                                {{ $row['status_badge']['label'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-dark fw-bold">
                    <tr>
                        <td colspan="2" class="ps-3 text-uppercase">TOTAL KONSOLIDASI TIM DIGITAL</td>
                        <td class="text-center text-white">{{ number_format($metrics['total_dealer_stu_target']) }} Unit</td>
                        <td class="text-center text-danger bg-danger bg-opacity-25">{{ number_format($metrics['total_digital_stu_target']) }} Unit</td>
                        <td class="text-center text-success">{{ number_format($metrics['stu_tiktok_live']) }} Unit</td>
                        <td class="text-center text-info">{{ number_format($metrics['stu_meta_ads']) }} Unit</td>
                        <td class="text-center fs-5 text-white bg-danger bg-opacity-50">{{ number_format($metrics['grand_total_stu']) }} Unit</td>
                        <td class="text-center text-white">{{ $metrics['overall_digital_stu_achievement_pct'] }}%</td>
                        <td class="text-center text-white">
                            @php $netStuDiff = $metrics['grand_total_stu'] - $metrics['total_digital_stu_target']; @endphp
                            {{ ($netStuDiff >= 0 ? '+' : '') . $netStuDiff }} Unit
                        </td>
                        <td class="text-center text-white-50">
                            {{ array_sum(array_column($metrics['stu_scorecard'], 'last_month_stu_mtd')) }} Unit
                        </td>
                        <td class="text-center text-white">
                            @php $netLmDiff = $metrics['grand_total_stu'] - array_sum(array_column($metrics['stu_scorecard'], 'last_month_stu_mtd')); @endphp
                            {{ ($netLmDiff >= 0 ? '+' : '') . $netLmDiff }}
                        </td>
                        <td class="text-center text-white">
                            @if($netLmDiff > 0)
                                <span class="badge bg-success rounded-pill px-2 py-1"><i class="fas fa-arrow-up me-1"></i> Naik</span>
                            @elseif($netLmDiff < 0)
                                <span class="badge bg-danger rounded-pill px-2 py-1"><i class="fas fa-arrow-down me-1"></i> Turun</span>
                            @else
                                <span class="badge bg-secondary rounded-pill px-2 py-1">Stabil</span>
                            @endif
                        </td>
                        <td class="text-center pe-3">
                            <span class="badge {{ $metrics['status_badge']['bg_class'] }} rounded-pill px-2.5 py-1">
                                {{ $metrics['status_badge']['label'] }}
                            </span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 4: TABEL 2 - MONITORING PIPELINE PROSPEK (TARGET 130%) PER DEALER -->
    <!-- ========================================================================= -->
    <div class="card card-executive p-3 p-md-4 mb-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-3">
            <div class="d-flex align-items-center gap-2.5">
                <div class="icon-box-unified box-warning">
                    <i class="fas fa-users-line"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-warning text-dark px-2 py-0.5 small text-uppercase fw-bold">Tabel 2</span>
                        <h5 class="fw-bold text-dark mb-0">Monitoring Pipeline Prospek Digital (Target 130% STU Dealer)</h5>
                    </div>
                    <p class="text-muted small mb-0">
                        Target Prospek adalah <strong>130%</strong> dari Target STU Dealer untuk menjamin volume pipeline closing penjualan sepeda motor.
                    </p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-dark border px-3 py-2 fw-semibold">
                    <i class="fas fa-users text-warning me-1"></i> {{ count($metrics['prospect_scorecard']) }} Cabang Dievaluasi
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-executive align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 text-center" style="width: 50px;">Rank</th>
                        <th>Dealer / Cabang</th>
                        <th class="text-center" style="background-color: #f8fafc;">Target STU Dealer</th>
                        <th class="text-center fw-bold" style="background-color: #fef9c3;">Target Prospek (130%)</th>
                        <th class="text-center fw-extrabold text-primary" style="background-color: #eff6ff;">Realisasi Prospek (Bulan Ini)</th>
                        <th class="text-center">% Capaian Prospek (130%)</th>
                        <th class="text-center fw-bold">+/- vs Target Prospek</th>
                        <th class="text-center" style="background-color: #f1f5f9;">VS Last Month (Per Hari Ini)</th>
                        <th class="text-center">+/- LM</th>
                        <th class="text-center">Keterangan</th>
                        <th class="text-center pe-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($metrics['prospect_scorecard'] as $row)
                    <tr>
                        <td class="ps-3 text-center">
                            @if($row['rank'] == 1)
                                <span class="badge bg-warning text-dark rounded-circle p-2 shadow-xs"><i class="fas fa-crown"></i> 1</span>
                            @elseif($row['rank'] == 2)
                                <span class="badge bg-secondary text-white rounded-circle p-2">2</span>
                            @elseif($row['rank'] == 3)
                                <span class="badge bg-danger text-white rounded-circle p-2">3</span>
                            @else
                                <span class="fw-bold text-muted">{{ $row['rank'] }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $row['nama_cabang'] }}</div>
                            <div class="text-muted small">
                                <span class="badge bg-light text-primary border me-1">{{ $row['kode'] }}</span>
                                <span class="d-none d-sm-inline">{{ $row['area'] }}</span>
                            </div>
                        </td>
                        <td class="text-center fw-bold text-dark" style="background-color: #f8fafc;">
                            {{ $row['dealer_stu_target'] }} <span class="small text-muted fw-normal">Unit</span>
                        </td>
                        <td class="text-center fw-bold text-dark fs-6" style="background-color: #fef9c3;">
                            {{ $row['prospect_target'] }} <span class="small text-muted fw-normal">Leads</span>
                        </td>
                        <td class="text-center fw-extrabold text-primary fs-5" style="background-color: #eff6ff;">
                            {{ number_format($row['realisasi_prospek']) }} Leads
                        </td>
                        <td class="text-center" style="min-width: 110px;">
                            <div class="d-flex align-items-center justify-content-center gap-1 mb-1">
                                <strong class="small {{ $row['prospek_achievement_pct'] >= 100 ? 'text-success' : 'text-danger' }}">
                                    {{ $row['prospek_achievement_pct'] }}%
                                </strong>
                            </div>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar {{ $row['prospek_achievement_pct'] >= 100 ? 'bg-success' : ($row['prospek_achievement_pct'] >= 75 ? 'bg-primary' : 'bg-warning') }}" 
                                     style="width: {{ min(100, $row['prospek_achievement_pct']) }}%;"></div>
                            </div>
                        </td>
                        <td class="text-center">
                            @if($row['diff_vs_target'] >= 0)
                                <span class="badge-diff-positive">
                                    <i class="fas fa-plus me-0.5"></i>{{ number_format($row['diff_vs_target']) }} Leads
                                </span>
                            @else
                                <span class="badge-diff-negative">
                                    {{ number_format($row['diff_vs_target']) }} Leads
                                </span>
                            @endif
                        </td>
                        <td class="text-center text-muted fw-semibold" style="background-color: #f1f5f9;">
                            {{ number_format($row['last_month_prospect_mtd']) }} <span class="small">Leads</span>
                        </td>
                        <td class="text-center fw-bold">
                            @if($row['diff_vs_last_month'] > 0)
                                <span class="text-success"><i class="fas fa-caret-up me-1"></i>+{{ number_format($row['diff_vs_last_month']) }}</span>
                            @elseif($row['diff_vs_last_month'] < 0)
                                <span class="text-danger"><i class="fas fa-caret-down me-1"></i>{{ number_format($row['diff_vs_last_month']) }}</span>
                            @else
                                <span class="text-muted">0</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($row['keterangan'] === 'Naik')
                                <span class="badge-trend-up">
                                    <i class="fas fa-arrow-trend-up me-1"></i> Naik
                                </span>
                            @elseif($row['keterangan'] === 'Turun')
                                <span class="badge-trend-down">
                                    <i class="fas fa-arrow-trend-down me-1"></i> Turun
                                </span>
                            @else
                                <span class="badge-trend-neutral">
                                    <i class="fas fa-minus me-1"></i> Stabil
                                </span>
                            @endif
                        </td>
                        <td class="text-center pe-3">
                            <span class="badge {{ $row['status_badge']['bg_class'] }} rounded-pill px-2.5 py-1 small">
                                {{ $row['status_badge']['label'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-dark fw-bold">
                    <tr>
                        <td colspan="2" class="ps-3 text-uppercase">TOTAL PIPELINE SELURUH CABANG</td>
                        <td class="text-center text-white">{{ number_format($metrics['total_dealer_stu_target']) }} Unit</td>
                        <td class="text-center text-warning bg-warning bg-opacity-25">{{ number_format($metrics['total_prospect_target']) }} Leads</td>
                        <td class="text-center fs-5 text-white bg-primary bg-opacity-50">{{ number_format($metrics['total_prospects_digital']) }} Leads</td>
                        <td class="text-center text-white">{{ $metrics['overall_prospect_achievement_pct'] }}%</td>
                        <td class="text-center text-white">
                            @php $netProspectDiff = $metrics['total_prospects_digital'] - $metrics['total_prospect_target']; @endphp
                            {{ ($netProspectDiff >= 0 ? '+' : '') . number_format($netProspectDiff) }} Leads
                        </td>
                        <td class="text-center text-white-50">
                            {{ number_format(array_sum(array_column($metrics['prospect_scorecard'], 'last_month_prospect_mtd'))) }} Leads
                        </td>
                        <td class="text-center text-white">
                            @php $netProspectLmDiff = $metrics['total_prospects_digital'] - array_sum(array_column($metrics['prospect_scorecard'], 'last_month_prospect_mtd')); @endphp
                            {{ ($netProspectLmDiff >= 0 ? '+' : '') . number_format($netProspectLmDiff) }}
                        </td>
                        <td class="text-center text-white">
                            @if($netProspectLmDiff > 0)
                                <span class="badge bg-success rounded-pill px-2 py-1"><i class="fas fa-arrow-up me-1"></i> Naik</span>
                            @elseif($netProspectLmDiff < 0)
                                <span class="badge bg-danger rounded-pill px-2 py-1"><i class="fas fa-arrow-down me-1"></i> Turun</span>
                            @else
                                <span class="badge bg-secondary rounded-pill px-2 py-1">Stabil</span>
                            @endif
                        </td>
                        <td class="text-center pe-3">
                            <span class="badge {{ $metrics['overall_prospect_achievement_pct'] >= 100 ? 'bg-success' : 'bg-warning text-dark' }} rounded-pill px-2.5 py-1">
                                {{ $metrics['overall_prospect_achievement_pct'] >= 100 ? 'Achieved' : 'Need Boost' }}
                            </span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 5: TABEL 3 - PRODUK YAMAHA (PENJUALAN & EFISIENSI PER MODEL)       -->
    <!-- ========================================================================= -->
    <div class="card card-executive p-3 p-md-4 mb-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-3">
            <div class="d-flex align-items-center gap-2.5">
                <div class="icon-box-unified box-primary">
                    <i class="fas fa-motorcycle"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary px-2 py-0.5 small text-uppercase">Tabel 3</span>
                        <h5 class="fw-bold text-dark mb-0">Tabel Produk Yamaha: Realisasi STU & Efisiensi Iklan per Model</h5>
                    </div>
                    <p class="text-muted small mb-0">
                        Rincian kontribusi model sepeda motor Yamaha yang terjual (STU), penyerapan budget iklan Meta Ads, dan efisiensi biaya closing.
                    </p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-dark border px-3 py-2 fw-semibold">
                    <i class="fas fa-tags me-1 text-danger"></i> {{ count($metrics['yamaha_products_table']) }} Model Yamaha Terdaftar
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-executive align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 text-center" style="width: 50px;">No</th>
                        <th>Kategori & Model Yamaha</th>
                        <th class="text-center" style="background-color: #f0fdf4;">STU Live TikTok</th>
                        <th class="text-center" style="background-color: #eff6ff;">STU Meta Ads</th>
                        <th class="text-center fw-extrabold text-danger" style="background-color: #fee2e2;">Total STU Terjual</th>
                        <th class="text-center">Share of Sales (%)</th>
                        <th class="text-center">Budget Iklan Terkait</th>
                        <th class="text-center">Prospek Masuk</th>
                        <th class="text-center">Cost / STU (Rp)</th>
                        <th class="text-center">Conversion Rate</th>
                        <th class="text-center pe-3">Status Permintaan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($metrics['yamaha_products_table'] as $idx => $pRow)
                    <tr>
                        <td class="ps-3 text-center fw-semibold text-muted">{{ $idx + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge {{ $pRow['category_badge'] }} px-2 py-1" style="font-size: 0.72rem;">{{ $pRow['category'] }}</span>
                                <strong class="text-dark">{{ $pRow['model'] }}</strong>
                            </div>
                        </td>
                        <td class="text-center fw-bold" style="background-color: #f0fdf4;">
                            @if($pRow['stu_live'] > 0)
                                <span class="badge bg-dark text-white px-2 py-1"><i class="fab fa-tiktok text-danger me-1"></i>{{ $pRow['stu_live'] }} Unit</span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="text-center fw-bold" style="background-color: #eff6ff;">
                            @if($pRow['stu_ads'] > 0)
                                <span class="badge bg-primary text-white px-2 py-1"><i class="fab fa-facebook me-1"></i>{{ $pRow['stu_ads'] }} Unit</span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="text-center fw-extrabold fs-6 text-danger" style="background-color: #fee2e2;">
                            {{ $pRow['total_stu'] }} Unit
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border px-2 py-1 fw-bold">
                                {{ $pRow['stu_share_pct'] }}%
                            </span>
                        </td>
                        <td class="text-center small fw-semibold text-dark">
                            Rp {{ number_format($pRow['budget'], 0, ',', '.') }}
                        </td>
                        <td class="text-center fw-bold text-dark">
                            {{ number_format($pRow['percakapan']) }} Leads
                        </td>
                        <td class="text-center small text-muted">
                            Rp {{ number_format($pRow['cost_per_stu'], 0, ',', '.') }}
                        </td>
                        <td class="text-center small fw-bold text-success">
                            {{ $pRow['conversion_rate'] }}%
                        </td>
                        <td class="text-center pe-3">
                            @if($pRow['status'] === 'Best Seller')
                                <span class="badge bg-danger rounded-pill px-2.5 py-1 small"><i class="fas fa-fire me-1"></i> Best Seller</span>
                            @elseif($pRow['status'] === 'High Demand')
                                <span class="badge bg-success rounded-pill px-2.5 py-1 small"><i class="fas fa-chart-line me-1"></i> High Demand</span>
                            @elseif($pRow['status'] === 'High Interest')
                                <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1 small"><i class="fas fa-comments me-1"></i> High Interest</span>
                            @else
                                <span class="badge bg-secondary rounded-pill px-2.5 py-1 small">Reguler</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted py-4">Belum ada data produk Yamaha yang tercatat pada periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-dark fw-bold">
                    <tr>
                        <td colspan="2" class="ps-3 text-uppercase">TOTAL PENJUALAN PRODUK DIGITAL</td>
                        <td class="text-center text-success">{{ number_format($metrics['stu_tiktok_live']) }} Unit</td>
                        <td class="text-center text-info">{{ number_format($metrics['stu_meta_ads']) }} Unit</td>
                        <td class="text-center fs-5 text-danger bg-danger bg-opacity-50">{{ number_format($metrics['grand_total_stu']) }} Unit</td>
                        <td class="text-center text-white">100%</td>
                        <td class="text-center text-white">Rp {{ number_format($metrics['total_ads_budget'], 0, ',', '.') }}</td>
                        <td class="text-center text-white">{{ number_format($metrics['total_ads_conversations']) }} Leads</td>
                        <td class="text-center text-white">Rp {{ number_format($metrics['cost_per_stu'], 0, ',', '.') }}</td>
                        <td class="text-center text-white">{{ $metrics['conversion_rate_prospect_to_stu'] }}%</td>
                        <td class="text-center pe-3"><span class="badge bg-secondary rounded-pill px-2.5 py-1">Consolidated</span></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 6: CHARTS & VISUAL ANALYTICS                                      -->
    <!-- ========================================================================= -->
    <div class="row g-3 mb-4">
        <!-- CHART 1: MONTHLY STU TREND (Jan - Dec) -->
        <div class="col-12 col-xl-7">
            <div class="card card-executive p-3 p-md-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="icon-box-sm box-danger">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark m-0">Tren Penjualan STU Bulanan (Januari - Desember {{ $tahun }})</h6>
                            <span class="text-muted small">Total STU, Live TikTok, dan Meta Ads per bulan.</span>
                        </div>
                    </div>
                    <span class="badge bg-light text-dark border">Tahun Berjalan</span>
                </div>
                <div style="height: 310px;">
                    <canvas id="monthlyStuTrendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- CHART 2: STU COMPARISON PER DEALER (TARGET 15% VS REALISASI) -->
        <div class="col-12 col-xl-5">
            <div class="card card-executive p-3 p-md-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="icon-box-sm box-primary">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark m-0">Realisasi STU vs Target Digital 15%</h6>
                            <span class="text-muted small">Breakdown STU TikTok Live vs Meta Ads per cabang</span>
                        </div>
                    </div>
                    <span class="badge bg-light text-dark border">{{ $metrics['nama_bulan'] }}</span>
                </div>
                <div style="height: 310px;">
                    <canvas id="dealerStuBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW CHARTS: PROSPECTS (130%) & PRODUCT BREAKDOWN -->
    <div class="row g-3 mb-4">
        <!-- CHART 3: TARGET PROSPEK 130% VS REALISASI -->
        <div class="col-12 col-lg-7">
            <div class="card card-executive p-3 p-md-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="icon-box-sm box-warning">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark m-0">Target Prospek (130%) vs Realisasi per Dealer</h6>
                            <span class="text-muted small">Evaluasi kecukupan volume prospek digital terhadap target STU dealer.</span>
                        </div>
                    </div>
                </div>
                <div style="height: 300px;">
                    <canvas id="prospectTargetChart"></canvas>
                </div>
            </div>
        </div>

        <!-- CHART 4: MOTORCYCLE MODEL SALES (STU BY PRODUCT) -->
        <div class="col-12 col-lg-5">
            <div class="card card-executive p-3 p-md-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="icon-box-sm box-danger">
                            <i class="fas fa-motorcycle"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark m-0">Kontribusi STU per Model Yamaha</h6>
                            <span class="text-muted small">Distribusi unit sepeda motor terjual dari channel digital</span>
                        </div>
                    </div>
                </div>
                <div style="height: 300px;" class="d-flex align-items-center justify-content-center">
                    <canvas id="productModelPieChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 7: OPERATIONAL DEEP-DIVE & DETAIL PANELS                          -->
    <!-- ========================================================================= -->
    <div class="card card-executive p-3 p-md-4 mb-4">
        <ul class="nav nav-pills mb-3 border-bottom pb-2 gap-2" id="deepDiveTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold small text-uppercase rounded-pill px-3 py-2" id="tiktok-live-tab" data-bs-toggle="pill" data-bs-target="#tiktok-live-pane" type="button" role="tab">
                    <i class="fab fa-tiktok me-1.5 text-danger"></i> TikTok Live Deep-Dive
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold small text-uppercase rounded-pill px-3 py-2" id="meta-ads-tab" data-bs-toggle="pill" data-bs-target="#meta-ads-pane" type="button" role="tab">
                    <i class="fab fa-facebook me-1.5 text-primary"></i> Meta Ads Campaigns
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold small text-uppercase rounded-pill px-3 py-2" id="social-prod-tab" data-bs-toggle="pill" data-bs-target="#social-prod-pane" type="button" role="tab">
                    <i class="fas fa-share-nodes me-1.5 text-info"></i> Sosmed Posting & Views
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold small text-uppercase rounded-pill px-3 py-2" id="google-rep-tab" data-bs-toggle="pill" data-bs-target="#google-rep-pane" type="button" role="tab">
                    <i class="fab fa-google me-1.5 text-warning"></i> Google Rating & Reputation
                </button>
            </li>
        </ul>

        <div class="tab-content" id="deepDiveTabContent">
            <!-- TAB 1: TIKTOK LIVE -->
            <div class="tab-pane fade show active" id="tiktok-live-pane" role="tabpanel">
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <div class="p-3 rounded-3 bg-light h-100">
                            <h6 class="fw-bold text-dark mb-2"><i class="fas fa-video text-danger me-1.5"></i> Ringkasan Streaming Live</h6>
                            <ul class="list-unstyled mb-0 small">
                                <li class="d-flex justify-content-between py-1 border-bottom">
                                    <span class="text-muted">Total Sesi Siaran:</span>
                                    <strong class="text-dark">{{ $metrics['total_live_sessions'] }} Sesi</strong>
                                </li>
                                <li class="d-flex justify-content-between py-1 border-bottom">
                                    <span class="text-muted">Total Jam Siaran:</span>
                                    <strong class="text-dark">{{ $metrics['total_live_hours'] }} Jam</strong>
                                </li>
                                <li class="d-flex justify-content-between py-1 border-bottom">
                                    <span class="text-muted">Total Penonton:</span>
                                    <strong class="text-primary">{{ number_format($metrics['total_live_viewers']) }}</strong>
                                </li>
                                <li class="d-flex justify-content-between py-1 border-bottom">
                                    <span class="text-muted">Total Likes:</span>
                                    <strong class="text-danger">{{ number_format($metrics['total_live_likes']) }}</strong>
                                </li>
                                <li class="d-flex justify-content-between py-1 border-bottom">
                                    <span class="text-muted">Total Komentar:</span>
                                    <strong class="text-dark">{{ number_format($metrics['total_live_comments']) }}</strong>
                                </li>
                                <li class="d-flex justify-content-between py-1">
                                    <span class="text-muted">STU Dihasilkan:</span>
                                    <strong class="text-success fs-6">{{ $metrics['stu_tiktok_live'] }} Unit STU</strong>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-12 col-md-8">
                        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-trophy text-warning me-1.5"></i> Top 5 Sesi Live Berkinerja Tertinggi</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle">
                                <thead class="table-light">
                                    <tr class="small text-muted">
                                        <th>Cabang</th>
                                        <th>Nama Host</th>
                                        <th>Tanggal</th>
                                        <th>Durasi</th>
                                        <th>Penonton</th>
                                        <th>STU</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($metrics['top_live_sessions'] as $session)
                                    <tr>
                                        <td class="fw-semibold text-dark">{{ $session->branch->nama_cabang ?? '-' }}</td>
                                        <td>{{ $session->nama_host }} <span class="badge bg-light text-dark border">{{ $session->jabatan }}</span></td>
                                        <td class="small">{{ $session->tanggal_live->format('d M Y') }}</td>
                                        <td class="small">{{ $session->durasi_jam }}j {{ $session->durasi_menit }}m</td>
                                        <td class="small">{{ number_format($session->jumlah_penonton) }}</td>
                                        <td>
                                            <span class="badge bg-success px-2 py-1">{{ $session->stu ?? 0 }} Unit</span>
                                            @if($session->unit_terjual)
                                                <small class="d-block text-muted" style="font-size: 10px;">{{ $session->unit_terjual }}</small>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">Belum ada sesi live streaming pada periode ini.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: META ADS -->
            <div class="tab-pane fade" id="meta-ads-pane" role="tabpanel">
                <h6 class="fw-bold text-dark mb-2"><i class="fab fa-facebook text-primary me-1.5"></i> Top Performing Kampanye Meta Ads</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle">
                        <thead class="table-light">
                            <tr class="small text-muted">
                                <th>Cabang</th>
                                <th>Nama Iklan & Model</th>
                                <th>Spend (Rp)</th>
                                <th>Impressions</th>
                                <th>Leads Masuk</th>
                                <th>CPL</th>
                                <th>CTR</th>
                                <th>STU Closing</th>
                                <th>CR %</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($metrics['top_campaigns'] as $camp)
                            <tr>
                                <td class="fw-semibold text-dark">{{ $camp->branch->nama_cabang ?? '-' }}</td>
                                <td>
                                    <div class="fw-bold text-primary">{{ $camp->nama_iklan }}</div>
                                    <span class="badge bg-light text-dark border">{{ $camp->product }}</span>
                                </td>
                                <td class="small fw-semibold">{{ $camp->formatted_budget }}</td>
                                <td class="small">{{ number_format($camp->total_views) }}</td>
                                <td class="small fw-bold text-dark">{{ number_format($camp->total_percakapan) }}</td>
                                <td class="small">{{ $camp->formatted_cpl }}</td>
                                <td class="small text-success fw-semibold">{{ $camp->ctr }}%</td>
                                <td>
                                    <span class="badge bg-danger text-white px-2 py-1 fw-bold">{{ $camp->stu }} Unit</span>
                                    @if($camp->unit_terjual)
                                        <small class="d-block text-muted" style="font-size: 10px;">{{ $camp->unit_terjual }}</small>
                                    @endif
                                </td>
                                <td class="small fw-bold">{{ $camp->conversion_rate }}%</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-3">Belum ada kampanye Meta Ads untuk periode ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 3: SOCIAL MEDIA & VIEWS -->
            <div class="tab-pane fade" id="social-prod-pane" role="tabpanel">
                <div class="row g-3">
                    <div class="col-12 col-md-8">
                        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-chart-area text-primary me-1.5"></i> Tren Aktivitas Posting Harian Seluruh Channel</h6>
                        <div style="height: 240px;">
                            <canvas id="postingTrendChart"></canvas>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-chart-pie text-danger me-1.5"></i> Distribusi Platform Views</h6>
                        <div style="height: 240px;" class="d-flex align-items-center justify-content-center">
                            <canvas id="platformPieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 4: GOOGLE REPUTATION -->
            <div class="tab-pane fade" id="google-rep-pane" role="tabpanel">
                <div class="row g-3 align-items-center">
                    <div class="col-12 col-md-4 text-center p-3 border-end">
                        <div class="display-4 fw-extrabold text-warning mb-1">⭐ {{ $metrics['avg_google_rating'] }}</div>
                        <div class="text-muted fw-semibold">Rata-rata Rating Google Business Cabang</div>
                        <div class="badge bg-success text-white px-3 py-1.5 mt-2 fs-6">+{{ $metrics['google_reviews'] }} Ulasan Konsumen Baru</div>
                    </div>
                    <div class="col-12 col-md-8">
                        <h6 class="fw-bold text-dark mb-2">Standar Reputasi Digital Yamaha Aspacindo</h6>
                        <p class="text-muted small">
                            Target penilaian Google Business mencakup perolehan minimal 100 review per bulan dengan rating bintang minimal 4.8, 
                            serta dokumentasi 4 foto aktivitas 3S (Sales, Service, Sparepart) setiap hari untuk menjaga kepercayaan konsumen.
                        </p>
                        <a href="{{ route('reports.google-rating.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                            <i class="fas fa-star me-1"></i> Buka Modul Google Rating & Testimoni Cabang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {

    // Helper formatter
    const formatNumber = (num) => new Intl.NumberFormat('id-ID').format(num);

    // ==========================================
    // 1. CHART: MONTHLY STU TREND (LINE CHART)
    // ==========================================
    const ctxMonthlyStu = document.getElementById('monthlyStuTrendChart');
    if (ctxMonthlyStu) {
        new Chart(ctxMonthlyStu.getContext('2d'), {
            type: 'line',
            data: {
                labels: {!! json_encode($metrics['monthly_trend_months']) !!},
                datasets: [
                    {
                        label: 'Total STU Digital',
                        data: {!! json_encode($metrics['monthly_stu_total']) !!},
                        borderColor: '#E60012',
                        backgroundColor: 'rgba(230, 0, 18, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#E60012',
                        pointRadius: 4,
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'STU Meta Ads',
                        data: {!! json_encode($metrics['monthly_stu_ads']) !!},
                        borderColor: '#0D6EFD',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        pointBackgroundColor: '#0D6EFD',
                        pointRadius: 3,
                        tension: 0.3
                    },
                    {
                        label: 'STU TikTok Live',
                        data: {!! json_encode($metrics['monthly_stu_live']) !!},
                        borderColor: '#198754',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        pointBackgroundColor: '#198754',
                        pointRadius: 3,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + formatNumber(context.raw) + ' Unit';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(val) { return val + ' Unit'; }
                        }
                    }
                }
            }
        });
    }

    // ==========================================
    // 2. CHART: REALISASI STU PER DEALER (BAR)
    // ==========================================
    const ctxDealerStu = document.getElementById('dealerStuBarChart');
    if (ctxDealerStu) {
        const dealerLabels = {!! json_encode(array_column($metrics['stu_scorecard'], 'nama_cabang')) !!};
        const dealerLive = {!! json_encode(array_column($metrics['stu_scorecard'], 'stu_tiktok_live')) !!};
        const dealerAds = {!! json_encode(array_column($metrics['stu_scorecard'], 'stu_meta_ads')) !!};
        const digitalTargets = {!! json_encode(array_column($metrics['stu_scorecard'], 'digital_stu_target')) !!};

        new Chart(ctxDealerStu.getContext('2d'), {
            type: 'bar',
            data: {
                labels: dealerLabels.map(name => name.replace('AKM ', '')),
                datasets: [
                    {
                        label: 'STU Meta Ads',
                        data: dealerAds,
                        backgroundColor: '#0D6EFD',
                        borderRadius: 4
                    },
                    {
                        label: 'STU TikTok Live',
                        data: dealerLive,
                        backgroundColor: '#198754',
                        borderRadius: 4
                    },
                    {
                        type: 'line',
                        label: 'Target Digital (15%)',
                        data: digitalTargets,
                        borderColor: '#E60012',
                        borderWidth: 2,
                        pointBackgroundColor: '#E60012',
                        pointRadius: 5,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { stacked: true },
                    y: {
                        stacked: false,
                        beginAtZero: true,
                        ticks: {
                            callback: function(val) { return val + ' Unit'; }
                        }
                    }
                },
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // ==========================================
    // 3. CHART: TARGET PROSPEK 130% VS REALISASI
    // ==========================================
    const ctxProspect = document.getElementById('prospectTargetChart');
    if (ctxProspect) {
        const dealerLabels = {!! json_encode(array_column($metrics['prospect_scorecard'], 'nama_cabang')) !!};
        const targetsProspect = {!! json_encode(array_column($metrics['prospect_scorecard'], 'prospect_target')) !!};
        const actualProspects = {!! json_encode(array_column($metrics['prospect_scorecard'], 'realisasi_prospek')) !!};

        new Chart(ctxProspect.getContext('2d'), {
            type: 'bar',
            data: {
                labels: dealerLabels.map(name => name.replace('AKM ', '')),
                datasets: [
                    {
                        label: 'Target Prospek (130%)',
                        data: targetsProspect,
                        backgroundColor: '#e2e8f0',
                        borderColor: '#94a3b8',
                        borderWidth: 1,
                        borderRadius: 4
                    },
                    {
                        label: 'Realisasi Prospek',
                        data: actualProspects,
                        backgroundColor: '#ffc107',
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(val) { return val + ' Leads'; }
                        }
                    }
                },
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // ==========================================
    // 4. CHART: MOTORCYCLE MODEL SALES BREAKDOWN
    // ==========================================
    const ctxProduct = document.getElementById('productModelPieChart');
    if (ctxProduct) {
        const yamahaProducts = {!! json_encode(array_slice($metrics['yamaha_products_table'], 0, 6)) !!};
        const productLabels = yamahaProducts.map(p => p.model);
        const productStu = yamahaProducts.map(p => p.total_stu);

        new Chart(ctxProduct.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: productLabels.length > 0 ? productLabels : ['NMAX Turbo', 'Aerox 155', 'Grand Filano', 'Fazzio', 'XMAX'],
                datasets: [{
                    data: productStu.length > 0 ? productStu : [40, 30, 25, 20, 15],
                    backgroundColor: [
                        '#E60012', '#0D6EFD', '#198754', '#ffc107', '#6f42c1', '#0dcaf0'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // ==========================================
    // 5. CHART: DAILY POSTING TREND
    // ==========================================
    const ctxPosting = document.getElementById('postingTrendChart');
    if (ctxPosting) {
        new Chart(ctxPosting.getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [
                    {
                        label: 'Instagram',
                        data: {!! json_encode($igPostsSeries) !!},
                        backgroundColor: '#E1306C',
                        borderRadius: 3
                    },
                    {
                        label: 'Facebook',
                        data: {!! json_encode($fbPostsSeries) !!},
                        backgroundColor: '#1877F2',
                        borderRadius: 3
                    },
                    {
                        label: 'TikTok',
                        data: {!! json_encode($tiktokPostsSeries) !!},
                        backgroundColor: '#000000',
                        borderRadius: 3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { stacked: true },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                },
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // ==========================================
    // 6. CHART: PLATFORM VIEWS DISTRIBUTION
    // ==========================================
    const ctxPlatform = document.getElementById('platformPieChart');
    if (ctxPlatform) {
        new Chart(ctxPlatform.getContext('2d'), {
            type: 'pie',
            data: {
                labels: ['Instagram', 'Facebook', 'TikTok'],
                datasets: [{
                    data: [
                        {{ $platformDistribution['Instagram Views'] ?? 0 }},
                        {{ $platformDistribution['Facebook Views'] ?? 0 }},
                        {{ $platformDistribution['TikTok Views'] ?? 0 }}
                    ],
                    backgroundColor: ['#E1306C', '#1877F2', '#000000']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

});
</script>
@endpush
