@extends('layouts.home.template')
@section('title', 'Absensi UII Dalwa')
@section('content')
    <!-- Hero Section -->
    <section class="banat-ambient-bg py-4 py-lg-5 position-relative overflow-hidden">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-12 col-lg-6">
                    <div class="hero-content">
                        <div class="badge-banat-tag mb-2 animate-glow">
                            <i class="fa-solid fa-sparkles"></i> ABSENSI BANAT UII DALWA
                        </div>
                        <h1 class="banat-hero-title fw-800 mb-3">
                            Sistem Absensi <span style="background: var(--banat-rose-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Civitas BANAT</span>
                        </h1>
                        <p class="banat-hero-subtitle lead mb-4">
                            Portal resmi rekapitulasi kehadiran dosen, dan staf UII Dalwa BANAT. Terintegrasi secara realtime, presisi, dan elegan.
                        </p>
                        <div class="hero-action-buttons d-flex flex-wrap gap-2 align-items-center mb-4">
                            @if (Auth::check())
                                <a href="{{ route('dashboard.index') }}" class="btn-banat-primary">
                                    <i class="fa-solid fa-gauge-high"></i> Dashboard Absensi
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn-banat-primary">
                                    <i class="fa-solid fa-right-to-bracket"></i> Masuk Sistem
                                </a>
                            @endif
                            <a href="{{ route('absensi.index') }}" class="btn-banat-outline">
                                <i class="fa-solid fa-magnifying-glass"></i> Cari Data Absensi
                            </a>
                        </div>
                        <!-- Features quick list -->
                        <div class="hero-features-list d-flex flex-wrap gap-4 pt-3 border-top" style="border-color: var(--banat-border) !important;">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-shield-halved" style="color: var(--banat-primary);"></i>
                                <span class="small font-weight-600" style="color: var(--banat-text-medium);">Keamanan Terjamin</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-bolt" style="color: var(--banat-primary);"></i>
                                <span class="small font-weight-600" style="color: var(--banat-text-medium);">Realtime Cloud Sync</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6 text-center">
                    <div class="hero-image-wrapper position-relative d-inline-block w-100">
                        <img src="{{ asset('home/assets/imgs/theme/banat_hero.png') }}" 
                             alt="Absensi Banat UII Dalwa Hero" 
                             class="img-fluid rounded-4 shadow-lg animate-float banat-hero-img" />
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Counter Bar -->
    <section class="py-4" style="background: #ffffff; border-top: 1px solid var(--banat-border); border-bottom: 1px solid var(--banat-border);">
        <div class="container">
            <div class="row g-4">
                <div class="col-6 col-md-3">
                    <div class="banat-stat-card">
                        <div class="banat-stat-icon">
                            <i class="fa-solid fa-users-between-lines"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-800" style="color: var(--banat-primary-dark);">100%</h4>
                            <span class="small text-muted">Civitas Banat</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="banat-stat-card">
                        <div class="banat-stat-icon">
                            <i class="fa-solid fa-building-columns"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-800" style="color: var(--banat-primary-dark);">UII Dalwa</h4>
                            <span class="small text-muted">Bangil Pasuruan</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="banat-stat-card">
                        <div class="banat-stat-icon">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-800" style="color: var(--banat-primary-dark);">Realtime</h4>
                            <span class="small text-muted">Update Presensi</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="banat-stat-card">
                        <div class="banat-stat-icon">
                            <i class="fa-solid fa-mobile-screen-button"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-800" style="color: var(--banat-primary-dark);">Android</h4>
                            <span class="small text-muted">App Support</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Data Absensi Tabs Section -->
    <section class="section-padding py-5 position-relative">
        <div class="container">
            <div class="text-center max-width-xl mx-auto mb-5">
                <span class="badge-banat-tag mb-2">REKAPITULASI DEDIKASI</span>
                <h2 class="fw-800 text-dark">Data Kehadiran Pengajar & Staf</h2>
                <p class="text-muted">Lihat riwayat kehadiran serta performa civitas akademik secara cepat</p>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom" style="border-color: var(--banat-border) !important;">
                <ul class="nav nav-pills gap-2" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="btn btn-banat-outline active" id="nav-tab-one" data-bs-toggle="tab" data-bs-target="#tab-one"
                            type="button" role="tab" aria-controls="tab-one" aria-selected="true">
                            <i class="fa-solid fa-chalkboard-user me-1"></i> Dosen
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="btn btn-banat-outline" id="nav-tab-three" data-bs-toggle="tab" data-bs-target="#tab-three"
                            type="button" role="tab" aria-controls="tab-three" aria-selected="false">
                            <i class="fa-solid fa-id-card-clip me-1"></i> Staff
                        </button>
                    </li>
                </ul>
                <a href="{{ route('absensi.index') }}" class="btn-banat-outline text-decoration-none">
                    Lihat Semua <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>

            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="tab-one" role="tabpanel" aria-labelledby="tab-one">
                </div>
                <div class="tab-pane fade" id="tab-three" role="tabpanel" aria-labelledby="tab-three">
                </div>
            </div>
        </div>
    </section>

    <!-- Download App Banner Section -->
    <section class="py-5">
        <div class="container">
            <div class="glass-card p-5 overflow-hidden position-relative" 
                 style="background: linear-gradient(135deg, #2d283e 0%, #1e1b2e 100%); color: #ffffff; border: none;">
                <div class="row align-items-center position-relative" style="z-index: 2;">
                    <div class="col-lg-8 mb-4 mb-lg-0">
                        <span class="badge mb-3 px-3 py-2" style="background: rgba(251, 113, 133, 0.2); color: #fb7185; border: 1px solid rgba(251, 113, 133, 0.3); border-radius: 20px;">
                            <i class="fa-brands fa-google-play me-1"></i> MOBILITY & CONVENIENCE
                        </span>
                        <h2 class="fw-800 text-white mb-3">Unduh Aplikasi Mobile Absensi Banat</h2>
                        <p class="lead mb-4" style="color: rgba(255, 255, 255, 0.8);">
                            Dapatkan kemudahan akses rekapitulasi kehadiran mahasiswi UII Dalwa secara langsung di genggaman Anda melalui Google Play Store.
                        </p>
                        <a href="https://play.google.com/store/apps/details?id=com.uiidalwa.absensi" 
                           target="_blank" 
                           class="btn-banat-primary">
                            <i class="fa-brands fa-google-play fa-lg"></i> Unduh di Play Store
                        </a>
                    </div>
                    <div class="col-lg-4 text-center">
                        <i class="fa-solid fa-mobile-screen-button display-1 opacity-50" style="color: #f472b6;"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script>
        function loadData(departemen_id, element, empty = true) {
            $(element).append(`
                <div class="loader-container">
                    <div class="loader-item"></div>
                </div>
            `);

            $.get("{{ route('root.getData') }}", {
                    departemen_id: departemen_id
                })
                .done(function(response) {
                    console.log(response);
                    if (empty) {
                        $(element).html(response);
                    } else {
                        $(element).append(response);
                    }
                })
                .always(function() {
                    $(element + ' .load').remove();
                });
        }

        loadData(1, '#tab-one');
        loadData(2, '#tab-three');
    </script>
@endpush
