@extends('layouts.admin.template')
@section('title', 'Dashboard')

@php
    $isStaff = Auth::user()->isStaff();
    $deptConfigs = [
        'dosen'      => ['icon' => 'ti-chalkboard',    'bg' => 'linear-gradient(135deg, #6366f1 0%, #4338ca 100%)', 'color' => '#818cf8', 'border' => 'rgba(99, 102, 241, 0.35)'],
        'staff'      => ['icon' => 'ti-id-badge',      'bg' => 'linear-gradient(135deg, #ec4899 0%, #be185d 100%)', 'color' => '#f472b6', 'border' => 'rgba(236, 72, 153, 0.35)'],
        'santri'     => ['icon' => 'ti-user-check',    'bg' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)', 'color' => '#fbbf24', 'border' => 'rgba(245, 158, 11, 0.35)'],
        'pengasuhan' => ['icon' => 'ti-home-heart',    'bg' => 'linear-gradient(135deg, #f43f5e 0%, #be123c 100%)', 'color' => '#fb7185', 'border' => 'rgba(244, 63, 94, 0.35)'],
        'akademik'   => ['icon' => 'ti-school',        'bg' => 'linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%)', 'color' => '#a78bfa', 'border' => 'rgba(139, 92, 246, 0.35)'],
        'tahfidz'    => ['icon' => 'ti-book-2',        'bg' => 'linear-gradient(135deg, #10b981 0%, #047857 100%)', 'color' => '#34d399', 'border' => 'rgba(16, 185, 129, 0.35)'],
        'dakwah'     => ['icon' => 'ti-speakerphone',  'bg' => 'linear-gradient(135deg, #06b6d4 0%, #0e7490 100%)', 'color' => '#22d3ee', 'border' => 'rgba(6, 182, 212, 0.35)'],
        'admin'      => ['icon' => 'ti-shield-lock',   'bg' => 'linear-gradient(135deg, #e11d48 0%, #9f1239 100%)', 'color' => '#fda4af', 'border' => 'rgba(225, 29, 72, 0.35)'],
    ];
@endphp

@push('css')
<style>
/* ==========================================================================
   BANAT LUXURY ADMIN DASHBOARD STYLES
   ========================================================================== */

/* Banat Luxury Dashboard Advance Banner */
.banat-banner-swiper {
    background: linear-gradient(135deg, #180f24 0%, #2b143a 50%, #401535 100%) !important;
    border-radius: 20px !important;
    border: 1px solid rgba(251, 113, 133, 0.28) !important;
    box-shadow: 0 14px 40px rgba(18, 9, 28, 0.45) !important;
    position: relative;
    overflow: hidden;
}

.banat-banner-swiper::before {
    content: '';
    position: absolute;
    top: -50px;
    right: 15%;
    width: 280px;
    height: 280px;
    background: radial-gradient(circle, rgba(251, 113, 133, 0.25) 0%, rgba(0, 0, 0, 0) 70%);
    border-radius: 50%;
    pointer-events: none;
    z-index: 1;
}

.banat-banner-swiper::after {
    content: '';
    position: absolute;
    bottom: -60px;
    left: 10%;
    width: 220px;
    height: 220px;
    background: radial-gradient(circle, rgba(244, 114, 182, 0.16) 0%, rgba(0, 0, 0, 0) 70%);
    border-radius: 50%;
    pointer-events: none;
    z-index: 1;
}

.banat-banner-swiper .swiper-slide {
    padding: 1.6rem 1.6rem 2.2rem 1.6rem !important;
    position: relative;
    z-index: 2;
}

/* Department Quick Button Grid */
.banat-dept-btn {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.16);
    color: #ffffff !important;
    border-radius: 12px;
    padding: 8px 12px;
    font-size: 0.83rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    transition: all 0.24s cubic-bezier(0.16, 1, 0.3, 1);
    text-decoration: none !important;
    backdrop-filter: blur(8px);
}

.banat-dept-btn:hover {
    background: linear-gradient(135deg, #fb7185 0%, #e11d48 100%) !important;
    border-color: rgba(255, 255, 255, 0.4) !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(225, 29, 72, 0.45);
    color: #ffffff !important;
}

.banat-dept-btn-all {
    background: rgba(251, 113, 133, 0.22) !important;
    border-color: rgba(251, 113, 133, 0.45) !important;
}

.banat-dept-icon {
    font-size: 1.08rem;
    color: #fda4af;
    transition: color 0.2s;
}

.banat-dept-btn:hover .banat-dept-icon {
    color: #ffffff;
}

.banat-dept-text {
    flex-grow: 1;
    text-align: left;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.banat-dept-count {
    background: rgba(255, 255, 255, 0.18);
    font-size: 0.70rem;
    padding: 2px 8px;
    border-radius: 20px;
    font-weight: 700;
}

.banat-dept-btn:hover .banat-dept-count {
    background: rgba(0, 0, 0, 0.28);
    color: #ffffff;
}

/* Quick Action Cards */
.banat-action-card {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.16);
    border-radius: 12px;
    padding: 10px 14px;
    color: #ffffff !important;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: all 0.24s cubic-bezier(0.16, 1, 0.3, 1);
    text-decoration: none !important;
    backdrop-filter: blur(8px);
}

.banat-action-card:hover {
    background: rgba(251, 113, 133, 0.22) !important;
    border-color: rgba(251, 113, 133, 0.5) !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.35);
    color: #ffffff !important;
}

.banat-action-icon-wrap {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, #fb7185 0%, #e11d48 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(225, 29, 72, 0.4);
}

.banat-action-title {
    font-size: 0.88rem;
    font-weight: 700;
    color: #ffffff;
    line-height: 1.2;
    margin-bottom: 2px;
}

.banat-action-sub {
    font-size: 0.72rem;
    color: #cbd5e1;
    line-height: 1.2;
}

/* Swiper Pagination Bullets */
.banat-banner-swiper .swiper-pagination-bullet {
    background: rgba(255, 255, 255, 0.4) !important;
    opacity: 1 !important;
}

.banat-banner-swiper .swiper-pagination-bullet-active {
    background: #fb7185 !important;
    width: 22px !important;
    border-radius: 8px !important;
    box-shadow: 0 0 10px rgba(251, 113, 133, 0.6) !important;
}

/* Right Illustration */
.card-website-analytics-img {
    filter: drop-shadow(0 12px 25px rgba(0, 0, 0, 0.45));
    max-height: 155px;
    object-fit: contain;
}

/* ==========================================================================
   BANAT LUXURY WELCOME CARD
   ========================================================================== */
.banat-welcome-card {
    background: linear-gradient(145deg, #ffffff 0%, #fff2f5 55%, #ffe6ee 100%) !important;
    border-radius: 20px !important;
    border: 1px solid rgba(251, 113, 133, 0.3) !important;
    box-shadow: 0 14px 35px rgba(225, 29, 72, 0.08), 0 2px 6px rgba(0, 0, 0, 0.02) !important;
    position: relative;
    overflow: hidden;
    transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}

.banat-welcome-card:hover {
    box-shadow: 0 20px 45px rgba(225, 29, 72, 0.15) !important;
    transform: translateY(-2px);
}

.dark-style .banat-welcome-card {
    background: linear-gradient(145deg, #241533 0%, #190e24 60%, #30132b 100%) !important;
    border: 1px solid rgba(251, 113, 133, 0.38) !important;
    box-shadow: 0 16px 40px rgba(10, 4, 16, 0.6) !important;
}

.dark-style .banat-welcome-card:hover {
    box-shadow: 0 20px 50px rgba(225, 29, 72, 0.25) !important;
}

.banat-welcome-glow {
    position: absolute;
    top: -40px;
    right: -20px;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(251, 113, 133, 0.35) 0%, rgba(244, 63, 94, 0) 70%);
    border-radius: 50%;
    pointer-events: none;
    z-index: 1;
}

.banat-welcome-glow-2 {
    position: absolute;
    bottom: -30px;
    left: 20%;
    width: 140px;
    height: 140px;
    background: radial-gradient(circle, rgba(244, 114, 182, 0.2) 0%, rgba(0, 0, 0, 0) 70%);
    border-radius: 50%;
    pointer-events: none;
    z-index: 1;
}

.banat-welcome-img {
    max-height: 165px;
    width: auto;
    object-fit: contain;
    filter: drop-shadow(0 12px 22px rgba(225, 29, 72, 0.25));
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    animation: banatFloat 4.5s ease-in-out infinite;
    transform-origin: bottom center;
}

.banat-welcome-card:hover .banat-welcome-img {
    transform: scale(1.05) translateY(-4px);
    filter: drop-shadow(0 16px 28px rgba(225, 29, 72, 0.35));
}

@keyframes banatFloat {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-6px);
    }
}

.banat-welcome-badge {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 5px 14px;
    background: linear-gradient(135deg, rgba(225, 29, 72, 0.12) 0%, rgba(244, 63, 94, 0.18) 100%);
    border: 1px solid rgba(225, 29, 72, 0.32);
    color: #e11d48;
    border-radius: 20px;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    box-shadow: 0 2px 8px rgba(225, 29, 72, 0.08);
}

.dark-style .banat-welcome-badge {
    background: rgba(251, 113, 133, 0.18);
    border-color: rgba(251, 113, 133, 0.45);
    color: #fda4af;
}

.banat-badge-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #10b981;
    box-shadow: 0 0 10px #10b981, 0 0 4px #10b981;
    display: inline-block;
    animation: banatPulse 2s infinite;
}

@keyframes banatPulse {
    0% { transform: scale(0.95); opacity: 0.8; }
    50% { transform: scale(1.2); opacity: 1; }
    100% { transform: scale(0.95); opacity: 0.8; }
}

.banat-welcome-title {
    font-size: 1.18rem;
    font-weight: 800;
    letter-spacing: -0.02em;
    color: #1e1926;
    line-height: 1.25;
}

.dark-style .banat-welcome-title {
    color: #ffffff;
}

.banat-welcome-name {
    font-size: 1.1rem;
    font-weight: 800;
    background: linear-gradient(135deg, #e11d48 0%, #fb7185 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    display: flex;
    align-items: center;
    gap: 4px;
    letter-spacing: -0.01em;
}

.banat-welcome-desc {
    font-size: 0.79rem;
    line-height: 1.5;
    color: #64748b;
}

.dark-style .banat-welcome-desc {
    color: #cbd5e1;
}

/* ==========================================================================
   BANAT LUXURY CARDS & STATISTICS
   ========================================================================== */
.banat-stat-container-card,
.banat-table-card {
    border-radius: 20px !important;
    border: 1px solid rgba(251, 113, 133, 0.22) !important;
    box-shadow: 0 8px 25px rgba(225, 29, 72, 0.05) !important;
    overflow: hidden;
    transition: all 0.3s ease;
}

.dark-style .banat-stat-container-card,
.dark-style .banat-table-card {
    border-color: rgba(251, 113, 133, 0.3) !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35) !important;
}

.banat-header-icon-box {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: linear-gradient(135deg, #fb7185 0%, #e11d48 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    box-shadow: 0 4px 14px rgba(225, 29, 72, 0.35);
    flex-shrink: 0;
}

.banat-total-badge {
    background: linear-gradient(135deg, rgba(225, 29, 72, 0.1) 0%, rgba(244, 63, 94, 0.18) 100%) !important;
    color: #e11d48 !important;
    border: 1px solid rgba(225, 29, 72, 0.25) !important;
    border-radius: 20px !important;
    font-size: 0.78rem !important;
    font-weight: 700 !important;
}

.dark-style .banat-total-badge {
    background: rgba(251, 113, 133, 0.18) !important;
    color: #fda4af !important;
    border-color: rgba(251, 113, 133, 0.4) !important;
}

.btn-banat-luxury {
    background: linear-gradient(135deg, #fb7185 0%, #e11d48 100%) !important;
    color: #ffffff !important;
    border: none !important;
    border-radius: 20px !important;
    font-weight: 600 !important;
    font-size: 0.8rem !important;
    box-shadow: 0 4px 12px rgba(225, 29, 72, 0.3) !important;
    transition: all 0.22s ease !important;
}

.btn-banat-luxury:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(225, 29, 72, 0.45) !important;
    color: #ffffff !important;
}

.banat-dept-stat-card {
    background: var(--banat-bg-surface, #ffffff);
    border: 1px solid rgba(251, 113, 133, 0.22);
    border-radius: 16px;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
    overflow: hidden;
}

.dark-style .banat-dept-stat-card {
    background: rgba(255, 255, 255, 0.04);
    border-color: rgba(251, 113, 133, 0.26);
}

.banat-dept-stat-card:hover {
    transform: translateY(-4px);
    border-color: #fb7185 !important;
    box-shadow: 0 10px 24px rgba(225, 29, 72, 0.16);
}

.banat-dept-stat-icon-wrap {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 1.25rem;
    flex-shrink: 0;
    transition: transform 0.25s ease;
}

.banat-dept-stat-card:hover .banat-dept-stat-icon-wrap {
    transform: scale(1.08) rotate(3deg);
}

.banat-stat-number {
    font-size: 1.45rem;
    font-weight: 800;
    letter-spacing: -0.02em;
    color: #1e1926;
    line-height: 1.2;
}

.dark-style .banat-stat-number {
    color: #ffffff;
}

.banat-stat-label {
    font-size: 0.78rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #8c8296;
    display: block;
    margin-top: 2px;
}

.banat-dept-stat-arrow {
    color: #cbd5e1;
    font-size: 1rem;
    transition: all 0.22s ease;
}

.banat-dept-stat-card:hover .banat-dept-stat-arrow {
    color: #e11d48;
    transform: translateX(3px);
}

/* ==========================================================================
   BANAT LUXURY TABLE & BADGES
   ========================================================================== */
.banat-realtime-badge {
    background: rgba(16, 185, 129, 0.12) !important;
    color: #059669 !important;
    border: 1px solid rgba(16, 185, 129, 0.3) !important;
    border-radius: 20px !important;
    font-size: 0.76rem !important;
    font-weight: 600 !important;
}

.dark-style .banat-realtime-badge {
    background: rgba(16, 185, 129, 0.2) !important;
    color: #34d399 !important;
    border-color: rgba(16, 185, 129, 0.4) !important;
}

.btn-outline-banat {
    background: transparent !important;
    border: 1px solid rgba(225, 29, 72, 0.35) !important;
    color: #e11d48 !important;
    border-radius: 20px !important;
    font-size: 0.8rem !important;
    font-weight: 600 !important;
    transition: all 0.22s ease !important;
}

.btn-outline-banat:hover {
    background: linear-gradient(135deg, #fb7185 0%, #e11d48 100%) !important;
    color: #ffffff !important;
    border-color: transparent !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 14px rgba(225, 29, 72, 0.3) !important;
}

.dark-style .btn-outline-banat {
    border-color: rgba(251, 113, 133, 0.45) !important;
    color: #fda4af !important;
}

.banat-admin-table thead th {
    background: rgba(251, 113, 133, 0.08) !important;
    color: #e11d48 !important;
    font-size: 0.78rem !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.06em !important;
    border-bottom: 2px solid rgba(251, 113, 133, 0.22) !important;
    padding: 14px 16px !important;
}

.dark-style .banat-admin-table thead th {
    background: rgba(251, 113, 133, 0.16) !important;
    color: #fda4af !important;
    border-bottom-color: rgba(251, 113, 133, 0.35) !important;
}

.banat-admin-table tbody tr {
    transition: background 0.2s ease;
}

.banat-admin-table tbody tr:hover {
    background: rgba(251, 113, 133, 0.04) !important;
}

.dark-style .banat-admin-table tbody tr:hover {
    background: rgba(251, 113, 133, 0.08) !important;
}

.banat-avatar-circle {
    background: linear-gradient(135deg, rgba(225, 29, 72, 0.14) 0%, rgba(251, 113, 133, 0.25) 100%) !important;
    color: #e11d48 !important;
    border: 1.5px solid rgba(225, 29, 72, 0.3) !important;
}

.banat-table-name {
    font-size: 0.88rem;
    letter-spacing: -0.01em;
}

.banat-count-badge {
    background: linear-gradient(135deg, rgba(225, 29, 72, 0.1) 0%, rgba(244, 63, 94, 0.18) 100%) !important;
    color: #e11d48 !important;
    border: 1px solid rgba(225, 29, 72, 0.25) !important;
    border-radius: 20px !important;
    font-size: 0.82rem !important;
}

.dark-style .banat-count-badge {
    background: rgba(251, 113, 133, 0.18) !important;
    color: #fda4af !important;
    border-color: rgba(251, 113, 133, 0.4) !important;
}

@media (max-width: 767.98px) {
    .banat-welcome-img {
        max-height: 135px;
    }
    .banat-stat-number {
        font-size: 1.25rem;
    }
    .banat-dept-stat-icon-wrap {
        width: 38px;
        height: 38px;
        font-size: 1.05rem;
    }
}
</style>
@endpush

@section('content')
    <div class="row g-6">
        <!-- Menu Slider Banner -->
        <div class="col-lg-8 col-md-12">
            <div class="swiper-container swiper-container-horizontal swiper swiper-card-advance-bg banat-banner-swiper"
                id="swiper-with-pagination-cards">
                <div class="swiper-wrapper">
                    
                    <!-- Slide 1: Menu Presensi Civitas (Departemen) -->
                    <div class="swiper-slide">
                        <div class="row align-items-center">
                            <div class="col-lg-7 col-md-8 col-12 order-2 order-md-1">
                                <span class="badge mb-2 px-3 py-1" style="background: rgba(251, 113, 133, 0.22); color: #fda4af; border: 1px solid rgba(251, 113, 133, 0.4); font-size: 0.72rem; border-radius: 20px;">
                                    <i class="ti ti-checklist me-1"></i> MONITORING PRESENSI
                                </span>
                                <h5 class="text-white fw-bold mb-1">Presensi Per Departemen</h5>
                                <small class="text-white-50 d-block mb-3">Pilih kategori departemen untuk memantau catatan kehadiran civitas Banat.</small>

                                <div class="row g-2">
                                    @foreach ($departemen as $item)
                                        @php
                                            $deptKey = strtolower($item->nama);
                                            $deptIcon = $deptConfigs[$deptKey]['icon'] ?? 'ti-building';
                                            $deptColor = $deptConfigs[$deptKey]['color'] ?? '#fda4af';
                                        @endphp
                                        <div class="col-6">
                                            <a href="{{ route('admin.absensi.index', ['departemen' => $item->id]) }}" 
                                               class="banat-dept-btn" 
                                               title="Lihat Presensi {{ $item->nama }}">
                                                <i class="ti {{ $deptIcon }} banat-dept-icon" style="color: {{ $deptColor }};"></i>
                                                <span class="banat-dept-text">{{ $item->nama }}</span>
                                                <span class="banat-dept-count">{{ $item->user->count() }}</span>
                                            </a>
                                        </div>
                                    @endforeach
                                    <div class="col-12">
                                        <a href="{{ route('admin.absensi.index') }}" 
                                           class="banat-dept-btn banat-dept-btn-all" 
                                           title="Lihat Semua Presensi Civitas">
                                            <i class="ti ti-layout-grid banat-dept-icon"></i>
                                            <span class="banat-dept-text">Tampilkan Semua Data Presensi</span>
                                            <span class="banat-dept-count">Semua</span>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-5 col-md-4 col-12 order-1 order-md-2 text-center my-3 my-md-0">
                                <img src="{{ asset('admin_assets/img/illustrations/card-website-analytics-2.png') }}"
                                    alt="Presensi Banat" class="card-website-analytics-img" />
                            </div>
                        </div>
                    </div>

                    @if ($isStaff)
                        <!-- Slide 2 (Staff): Pintasan Operasional Harian -->
                        <div class="swiper-slide">
                            <div class="row align-items-center">
                                <div class="col-lg-7 col-md-8 col-12 order-2 order-md-1">
                                    <span class="badge mb-2 px-3 py-1" style="background: rgba(251, 113, 133, 0.22); color: #fda4af; border: 1px solid rgba(251, 113, 133, 0.4); font-size: 0.72rem; border-radius: 20px;">
                                        <i class="ti ti-bolt me-1"></i> PINTASAN KERJA STAFF
                                    </span>
                                    <h5 class="text-white fw-bold mb-1">Aksi &amp; Operasional Harian</h5>
                                    <small class="text-white-50 d-block mb-3">Kelola administrasi civitas dan monitoring kehadiran realtime.</small>

                                    <div class="row g-2">
                                        <div class="col-6">
                                            <a href="{{ route('admin.user.index') }}" class="banat-action-card">
                                                <div class="banat-action-icon-wrap">
                                                    <i class="ti ti-users"></i>
                                                </div>
                                                <div>
                                                    <div class="banat-action-title">Data Civitas</div>
                                                    <div class="banat-action-sub">Pengajar &amp; Staf Banat</div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-6">
                                            <a href="{{ route('admin.absensi.index') }}" class="banat-action-card">
                                                <div class="banat-action-icon-wrap">
                                                    <i class="ti ti-calendar-stats"></i>
                                                </div>
                                                <div>
                                                    <div class="banat-action-title">Log Presensi</div>
                                                    <div class="banat-action-sub">Rekapitulasi Kehadiran</div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-6">
                                            <a href="{{ url('/realtime') }}" class="banat-action-card">
                                                <div class="banat-action-icon-wrap">
                                                    <i class="ti ti-broadcast"></i>
                                                </div>
                                                <div>
                                                    <div class="banat-action-title">Realtime Sync</div>
                                                    <div class="banat-action-sub">Live Biometrik Cloud</div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-6">
                                            <a href="{{ url('/') }}" target="_blank" class="banat-action-card">
                                                <div class="banat-action-icon-wrap">
                                                    <i class="ti ti-world"></i>
                                                </div>
                                                <div>
                                                    <div class="banat-action-title">Portal Utama</div>
                                                    <div class="banat-action-sub">Landing Page Banat</div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-5 col-md-4 col-12 order-1 order-md-2 text-center my-3 my-md-0">
                                    <img src="{{ asset('admin_assets/img/illustrations/card-website-analytics-1.png') }}"
                                        alt="Operasional Staff" class="card-website-analytics-img" />
                                </div>
                            </div>
                        </div>

                        <!-- Slide 3 (Staff): Profil & Informasi Staff -->
                        <div class="swiper-slide">
                            <div class="row align-items-center">
                                <div class="col-lg-7 col-md-8 col-12 order-2 order-md-1">
                                    <span class="badge mb-2 px-3 py-1" style="background: rgba(251, 113, 133, 0.22); color: #fda4af; border: 1px solid rgba(251, 113, 133, 0.4); font-size: 0.72rem; border-radius: 20px;">
                                        <i class="ti ti-user-cog me-1"></i> AKUN &amp; LAYANAN
                                    </span>
                                    <h5 class="text-white fw-bold mb-1">Profil &amp; Informasi Staff</h5>
                                    <small class="text-white-50 d-block mb-3">Kelola kredensial akun dan catatan kehadiran pribadi Anda.</small>

                                    <div class="row g-2">
                                        <div class="col-6">
                                            <a href="{{ route('admin.profile.index') }}" class="banat-action-card">
                                                <div class="banat-action-icon-wrap">
                                                    <i class="ti ti-user-edit"></i>
                                                </div>
                                                <div>
                                                    <div class="banat-action-title">Profil Saya</div>
                                                    <div class="banat-action-sub">Ganti Password &amp; Biodata</div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-6">
                                            <a href="{{ url('/dashboard') }}" class="banat-action-card">
                                                <div class="banat-action-icon-wrap">
                                                    <i class="ti ti-calendar-user"></i>
                                                </div>
                                                <div>
                                                    <div class="banat-action-title">Presensi Pribadi</div>
                                                    <div class="banat-action-sub">Catatan Kehadiran Staf</div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-5 col-md-4 col-12 order-1 order-md-2 text-center my-3 my-md-0">
                                    <img src="{{ asset('admin_assets/img/illustrations/card-website-analytics-3.png') }}"
                                        alt="Profil Staff" class="card-website-analytics-img" />
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Slide 2 (Admin): Master Data -->
                        <div class="swiper-slide">
                            <div class="row align-items-center">
                                <div class="col-lg-7 col-md-8 col-12 order-2 order-md-1">
                                    <span class="badge mb-2 px-3 py-1" style="background: rgba(251, 113, 133, 0.22); color: #fda4af; border: 1px solid rgba(251, 113, 133, 0.4); font-size: 0.72rem; border-radius: 20px;">
                                        <i class="ti ti-database me-1"></i> MASTER DATA
                                    </span>
                                    <h5 class="text-white fw-bold mb-1">Manajemen Data Master</h5>
                                    <small class="text-white-50 d-block mb-3">Kelola konfigurasi role, departemen, tipe civitas, dan kategori.</small>

                                    <div class="row g-2">
                                        <div class="col-6">
                                            <a href="{{ route('admin.role.index') }}" class="banat-dept-btn">
                                                <i class="ti ti-key banat-dept-icon"></i>
                                                <span class="banat-dept-text">Role User</span>
                                                <span class="banat-dept-count"><i class="ti ti-chevron-right"></i></span>
                                            </a>
                                        </div>
                                        <div class="col-6">
                                            <a href="{{ route('admin.departemen.index') }}" class="banat-dept-btn">
                                                <i class="ti ti-building banat-dept-icon"></i>
                                                <span class="banat-dept-text">Departemen</span>
                                                <span class="banat-dept-count"><i class="ti ti-chevron-right"></i></span>
                                            </a>
                                        </div>
                                        <div class="col-6">
                                            <a href="{{ route('admin.type.index') }}" class="banat-dept-btn">
                                                <i class="ti ti-tag banat-dept-icon"></i>
                                                <span class="banat-dept-text">Type User</span>
                                                <span class="banat-dept-count"><i class="ti ti-chevron-right"></i></span>
                                            </a>
                                        </div>
                                        <div class="col-6">
                                            <a href="{{ route('admin.kategori.index') }}" class="banat-dept-btn">
                                                <i class="ti ti-category banat-dept-icon"></i>
                                                <span class="banat-dept-text">Kategori</span>
                                                <span class="banat-dept-count"><i class="ti ti-chevron-right"></i></span>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-5 col-md-4 col-12 order-1 order-md-2 text-center my-3 my-md-0">
                                    <img src="{{ asset('admin_assets/img/illustrations/card-website-analytics-1.png') }}"
                                        alt="Master Data" class="card-website-analytics-img" />
                                </div>
                            </div>
                        </div>

                        <!-- Slide 3 (Admin): Integrasi & Pengaturan -->
                        <div class="swiper-slide">
                            <div class="row align-items-center">
                                <div class="col-lg-7 col-md-8 col-12 order-2 order-md-1">
                                    <span class="badge mb-2 px-3 py-1" style="background: rgba(251, 113, 133, 0.22); color: #fda4af; border: 1px solid rgba(251, 113, 133, 0.4); font-size: 0.72rem; border-radius: 20px;">
                                        <i class="ti ti-settings me-1"></i> SISTEM &amp; INTEGRASI
                                    </span>
                                    <h5 class="text-white fw-bold mb-1">Pengaturan &amp; Cloud Presensi</h5>
                                    <small class="text-white-50 d-block mb-3">Konfigurasi sinkronisasi biometrik fingerspot dan akses API.</small>

                                    <div class="row g-2">
                                        @if (auth()->user()->isSuperAdmin())
                                        <div class="col-6">
                                            <a href="{{ route('admin.fingerspot.index') }}" class="banat-dept-btn">
                                                <i class="ti ti-fingerprint banat-dept-icon"></i>
                                                <span class="banat-dept-text">Fingerspot Cloud</span>
                                                <span class="banat-dept-count"><i class="ti ti-chevron-right"></i></span>
                                            </a>
                                        </div>
                                        <div class="col-6">
                                            <a href="{{ route('admin.api_client.index') }}" class="banat-dept-btn">
                                                <i class="ti ti-key banat-dept-icon"></i>
                                                <span class="banat-dept-text">API Client</span>
                                                <span class="banat-dept-count"><i class="ti ti-chevron-right"></i></span>
                                            </a>
                                        </div>
                                        @endif
                                        <div class="col-12">
                                            <a href="{{ route('admin.profile.index') }}" class="banat-dept-btn">
                                                <i class="ti ti-user-cog banat-dept-icon"></i>
                                                <span class="banat-dept-text">Profil Administrator</span>
                                                <span class="banat-dept-count"><i class="ti ti-chevron-right"></i></span>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-5 col-md-4 col-12 order-1 order-md-2 text-center my-3 my-md-0">
                                    <img src="{{ asset('admin_assets/img/illustrations/card-website-analytics-3.png') }}"
                                        alt="Pengaturan Sistem" class="card-website-analytics-img" />
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
        <!--/ Menu Slider Banner -->

        <!-- Welcome Card -->
        <div class="col-lg-4 col-md-12">
            <div class="card h-100 banat-welcome-card">
                <div class="banat-welcome-glow"></div>
                <div class="banat-welcome-glow-2"></div>
                <div class="d-flex align-items-center row h-100 g-0 position-relative" style="z-index: 2;">
                    <div class="col-7 col-sm-7">
                        <div class="card-body pe-0 d-flex flex-column justify-content-between h-100 py-4 ps-4">
                            <div>
                                <div class="banat-welcome-badge mb-2">
                                    <span class="banat-badge-dot"></span>
                                    <span>{{ ucfirst(Auth::user()->role->akses ?? 'Staff') }} Banat</span>
                                </div>
                                <h5 class="banat-welcome-title mb-1">Selamat datang kembali!</h5>
                                <div class="banat-welcome-name mb-2">
                                    <span>🎉</span> {{ \Auth::user()->name }}
                                </div>
                            </div>
                            <p class="banat-welcome-desc mb-0">
                                Semoga harimu penuh keberkahan dan dimudahkan dalam setiap tugas kepengurusan absensi 🤲
                            </p>
                        </div>
                    </div>
                    <div class="col-5 col-sm-5 text-center position-relative">
                        <div class="card-body p-0 pe-2 d-flex align-items-end justify-content-center h-100">
                            <img src="{{ asset('admin_assets/img/illustrations/muslimah-welcome.png') }}"
                                alt="Staff Banat" class="banat-welcome-img" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Welcome Card -->

        <!-- Statistics -->
        <div class="col-xl-12 col-md-12">
            <div class="card banat-stat-container-card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3 pb-3 border-bottom" style="border-color: rgba(251, 113, 133, 0.16) !important;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="banat-header-icon-box">
                            <i class="ti ti-users"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0 fw-bold">Distribusi Civitas Banat</h5>
                            <small class="text-muted">Statistik civitas terdaftar aktif per departemen akademika</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge banat-total-badge px-3 py-2">
                            <i class="ti ti-chart-pie me-1"></i> Total {{ $departemen->sum(fn($d) => $d->user->count()) }} Civitas
                        </span>
                        <a href="{{ route('admin.user.index') }}" class="btn btn-sm btn-banat-luxury px-3 py-2">
                            Kelola Semua Civitas <i class="ti ti-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body pt-4">
                    <div class="row g-3">
                        @foreach ($departemen as $item)
                            @php
                                $deptKey = strtolower($item->nama);
                                $cfg = $deptConfigs[$deptKey] ?? [
                                    'icon'   => 'ti-building',
                                    'bg'     => 'linear-gradient(135deg, #fb7185 0%, #e11d48 100%)',
                                    'color'  => '#fda4af',
                                    'border' => 'rgba(251, 113, 133, 0.35)'
                                ];
                            @endphp
                            <div class="col-lg-3 col-md-4 col-sm-6 col-6">
                                <a href="{{ route('admin.absensi.index', ['departemen' => $item->id]) }}" class="text-decoration-none">
                                    <div class="banat-dept-stat-card d-flex align-items-center p-3 h-100">
                                        <div class="banat-dept-stat-icon-wrap me-3" style="background: {{ $cfg['bg'] }}; box-shadow: 0 4px 14px {{ $cfg['border'] }};">
                                            <i class="ti {{ $cfg['icon'] }}"></i>
                                        </div>
                                        <div class="card-info flex-grow-1 overflow-hidden">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <h4 class="mb-0 fw-bold banat-stat-number">{{ $item->user->count() }}</h4>
                                                <i class="ti ti-chevron-right banat-dept-stat-arrow"></i>
                                            </div>
                                            <span class="banat-stat-label text-truncate">{{ $item->nama }}</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <!--/ Statistics -->

        <!-- Attendance Table -->
        <div class="col-12">
            <div class="card banat-table-card" id="card-user">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3 border-bottom pb-3" style="border-color: rgba(251, 113, 133, 0.16) !important;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="banat-header-icon-box">
                            <i class="ti ti-calendar-check"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0 fw-bold">Catatan Kehadiran Civitas Terkini</h5>
                            <small class="text-muted">Rekapitulasi total riwayat kehadiran civitas Banat UII Dalwa</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge banat-realtime-badge px-3 py-2">
                            <i class="ti ti-circle-check me-1"></i> Data Terverifikasi
                        </span>
                        <a href="{{ route('admin.absensi.index') }}" class="btn btn-sm btn-outline-banat px-3 py-2">
                            <i class="ti ti-list-details me-1"></i> Seluruh Rekap
                        </a>
                    </div>
                </div>
                <div class="card-datatable table-responsive pt-2 px-3 pb-3">
                    <table class="datatables-basic table table-hover banat-admin-table" id="table-1">
                        <thead>
                            <tr>
                                <th style="width: 70px;">No</th>
                                <th>Civitas Akademika</th>
                                <th style="width: 200px;" class="text-center">Total Kehadiran</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <!--/ Attendance Table -->

    </div>
@endsection
@push('scripts')
    <script>
        const swiperWithPagination = document.querySelector('#swiper-with-pagination-cards');
        if (swiperWithPagination) {
            new Swiper(swiperWithPagination, {
                loop: true,
                autoplay: {
                    delay: 2500,
                    disableOnInteraction: false
                },
                pagination: {
                    clickable: true,
                    el: '.swiper-pagination'
                }
            });
        }

        var dataTable = initDataTables('table-1', 'loader-user', 'card-user', false, false,
            'Absensi', "{{ route('admin.dashboard.dataAbsensi') }}",
            [
                {
                    data: "name",
                    name: "name",
                    className: "align-middle",
                },
                {
                    data: "total_kehadiran",
                    name: "total_kehadiran",
                    className: "align-middle text-center",
                    render: function(data, type, full, meta) {
                        var count = parseInt(data) || 0;
                        return `
                            <span class="badge banat-count-badge px-3 py-2 fw-bold">
                                <i class="ti ti-check me-1"></i> ${count} Sesi Hadir
                            </span>
                        `;
                    }
                },
            ],
        );
    </script>
@endpush
