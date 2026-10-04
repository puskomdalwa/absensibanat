@extends('layouts.home.template')
@section('title', 'Absensi Banat - Universitas Islam Internasional Dalwa')

@section('content')
    <!-- Top Scroll Progress Indicator -->
    <div class="scroll-progress-line" id="scrollProgress"></div>

    <!-- Global Fixed 3D Canvas (Transitions smoothly from Hero to Showcase) -->
    <canvas id="canvas3d" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; pointer-events: none; z-index: 1030; transition: opacity 0.3s ease;"></canvas>

    <!-- Hero Wrapper: Inset with curved edges (not full to left, right, top, bottom) -->
    <div class="hero-curved-wrapper px-3 px-md-4 px-lg-5 pt-3 pt-md-4 pb-4 pb-md-5" style="background: var(--banat-bg-soft);">
        <div class="container" style="max-width: 1320px; padding: 0;">
            <!-- Rounded Inset Hero Card -->
            <section class="hero-proceeding-card position-relative" 
                     style="background: linear-gradient(180deg, rgba(13, 17, 28, 0.88) 0%, rgba(26, 20, 35, 0.95) 100%), url('{{ asset('home/assets/imgs/theme/dalwa_bg_hero.png') }}') center center / cover no-repeat !important; color: #ffffff !important; border-radius: 36px; padding: 110px 28px 30px; position: relative; overflow: hidden; box-shadow: 0 25px 60px rgba(0, 0, 0, 0.22); border: 1px solid rgba(255, 255, 255, 0.12);">
                
                <!-- Ambient Decorative Glow -->
                <div style="position: absolute; top: -140px; left: 45%; transform: translateX(-50%); width: 650px; height: 480px; background: radial-gradient(circle, rgba(251, 113, 133, 0.22) 0%, rgba(0,0,0,0) 70%); pointer-events: none; z-index: 1;"></div>

                <div class="container-fluid position-relative" style="z-index: 2; max-width: 1200px;">
                    <!-- Hero Content Row: Text (Left) & 3D Machine Starting Area (Right) -->
                    <div class="row align-items-center g-4 mb-3">
                        <!-- Left Column: Minimal & Punchy Text -->
                        <div class="col-12 col-lg-7 text-center text-lg-start">
                            <!-- Script Tagline (Montez Font) -->
                            <div class="font-montez mb-1" 
                                 style="font-family: 'Montez', cursive !important; color: #fb7185 !important; font-size: 1.85rem !important; text-shadow: 0 0 25px rgba(251, 113, 133, 0.45); letter-spacing: 0.5px;">
                                Islamic Academic Excellence
                            </div>

                            <!-- Clean & Punchy Headline -->
                            <h1 style="color: #ffffff !important; font-weight: 800; font-size: 2.45rem; line-height: 1.25; margin-bottom: 14px; letter-spacing: -0.5px;">
                                Presensi Civitas Banat<br>
                                <span style="background: linear-gradient(135deg, #fed7aa 0%, #fb7185 50%, #f43f5e 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                                    UII Dalwa Bangil
                                </span>
                            </h1>

                            <!-- Minimal 1-Sentence Description -->
                            <p style="color: rgba(255, 255, 255, 0.85) !important; font-size: 0.98rem; line-height: 1.6; max-width: 490px; margin-bottom: 24px; margin-left: auto; margin-right: auto;" class="ms-lg-0 me-lg-auto">
                                Presensi terintegrasi biometrik realtime untuk seluruh dosen dan staf Kampus Banat.
                            </p>

                            <!-- Hero Call-to-Action Buttons -->
                            <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start align-items-center">
                                <a href="{{ \Auth::check() && \Auth::user()->hasRole('superadmin', 'admin', 'staff') ? url('/admin/dashboard') : url('/dashboard') }}" class="btn-banat-primary">
                                    <i class="fa-solid fa-gauge-high"></i> Dashboard
                                </a>
                                @if (!\Auth::check() || \Auth::user()->isAdmin())
                                <a href="{{ url('/realtime') }}" class="btn-banat-outline" 
                                   style="background: rgba(255, 255, 255, 0.15) !important; color: #ffffff !important; border-color: rgba(255, 255, 255, 0.35) !important;">
                                    <i class="fa-solid fa-tower-broadcast me-1"></i> Absensi Realtime
                                </a>
                                @endif
                                <a href="#fitur-3d" class="btn-banat-outline" 
                                   style="background: rgba(255, 255, 255, 0.15) !important; color: #ffffff !important; border-color: rgba(255, 255, 255, 0.35) !important;">
                                    <i class="fa-solid fa-cube me-1"></i> Eksplorasi 3D
                                </a>
                            </div>
                        </div>

                        <!-- Right Column: 3D Model Anchor in Hero -->
                        <div class="col-12 col-lg-5 text-center">
                            <div id="hero3dAnchor" style="height: 320px; width: 100%; position: relative; cursor: grab;" title="Putar 3D Interaktif">
                                <div class="badge-3d-floating" style="background: rgba(30, 25, 42, 0.88); backdrop-filter: blur(10px); color: #ffffff; border: 1px solid rgba(251, 113, 133, 0.4); border-radius: 30px; padding: 6px 14px; font-size: 0.82rem; position: absolute; top: 6px; left: 50%; transform: translateX(-50%); pointer-events: none; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap;">
                                    <i class="fa-solid fa-circle text-success" style="font-size: 8px;"></i>
                                    <span>Mesin Biometrik 3D Realtime</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Restored Content below Buttons: Quick Info Bar with Mobile App Support -->
                    <div class="row justify-content-center pt-2">
                        <div class="col-12">
                            <div class="hero-quick-info-card" style="background: rgba(255, 255, 255, 0.08) !important; backdrop-filter: blur(18px) !important; -webkit-backdrop-filter: blur(18px) !important; border: 1px solid rgba(255, 255, 255, 0.18) !important; border-radius: 20px; padding: 18px 20px; box-shadow: 0 15px 40px rgba(0, 0, 0, 0.35); color: #ffffff !important;">
                                <div class="row align-items-center g-2 g-md-3">
                                    <!-- Item 1: Location -->
                                    <div class="col-12 col-sm-6 col-xl-2">
                                        <div class="info-item-box d-flex align-items-center gap-2 gap-md-3">
                                            <div class="info-item-icon">
                                                <i class="fa-solid fa-location-dot"></i>
                                            </div>
                                            <div class="text-start">
                                                <span class="text-white-50 small d-block" style="font-size: 0.74rem;">Lokasi Kampus</span>
                                                <h6 class="text-white mb-0 fw-700" style="font-size: 0.88rem;">Bangil, Pasuruan</h6>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Item 2: Biometric Sync -->
                                    <div class="col-12 col-sm-6 col-xl-2">
                                        <div class="info-item-box d-flex align-items-center gap-2 gap-md-3">
                                            <div class="info-item-icon">
                                                <i class="fa-solid fa-fingerprint"></i>
                                            </div>
                                            <div class="text-start">
                                                <span class="text-white-50 small d-block" style="font-size: 0.74rem;">Mesin Presensi</span>
                                                <h6 class="text-white mb-0 fw-700" style="font-size: 0.88rem;">Biometrik 3D Sync</h6>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Item 3: Civitas -->
                                    <div class="col-12 col-sm-6 col-xl-3">
                                        <div class="info-item-box d-flex align-items-center gap-2 gap-md-3">
                                            <div class="info-item-icon">
                                                <i class="fa-solid fa-users-gear"></i>
                                            </div>
                                            <div class="text-start">
                                                <span class="text-white-50 small d-block" style="font-size: 0.74rem;">Cakupan Pengguna</span>
                                                <h6 class="text-white mb-0 fw-700" style="font-size: 0.88rem;">Dosen &amp; Staf Banat</h6>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Item 4: Mobile App Support -->
                                    <div class="col-12 col-sm-6 col-xl-2">
                                        <div class="info-item-box d-flex align-items-center gap-2 gap-md-3">
                                            <div class="info-item-icon">
                                                <i class="fa-solid fa-mobile-screen-button"></i>
                                            </div>
                                            <div class="text-start">
                                                <span class="text-white-50 small d-block" style="font-size: 0.74rem;">Aplikasi Mobile</span>
                                                <h6 class="text-white mb-0 fw-700" style="font-size: 0.88rem;">Mobile App Support</h6>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Item 5: Action Buttons (Rekapitulasi & Laporan) -->
                                    <div class="col-12 col-xl-3 text-xl-end">
                                        <div class="d-flex flex-row flex-xl-column gap-2 ms-xl-auto mt-2 mt-xl-0" style="max-width: 100%;">
                                            <a href="#data-absensi" class="btn-banat-primary flex-grow-1 w-100 py-2 text-center" style="font-size: 0.86rem;">
                                                <i class="fa-solid fa-clipboard-user me-1"></i> Rekapitulasi Presensi
                                            </a>
                                            <a href="{{ url('/laporan') }}" class="btn-banat-outline flex-grow-1 w-100 py-2 text-center" 
                                               style="background: rgba(255, 255, 255, 0.14) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.3) !important; font-size: 0.86rem; border-radius: 50px; text-decoration: none;">
                                                <i class="fa-solid fa-file-invoice me-1"></i> Laporan
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Stats Counter Bar (3D Glass Cards) -->
    <section class="py-4 position-relative banat-stats-section" style="background: var(--banat-bg-surface); border-bottom: 1px solid var(--banat-border); z-index: 3;">
        <div class="container">
            <div class="row g-4">
                <div class="col-6 col-md-3">
                    <div class="banat-stat-card card-3d-tilt">
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
                    <div class="banat-stat-card card-3d-tilt">
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
                    <div class="banat-stat-card card-3d-tilt">
                        <div class="banat-stat-icon">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-800" style="color: var(--banat-primary-dark);">Realtime</h4>
                            <span class="small text-muted">Cloud Presensi</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="banat-stat-card card-3d-tilt">
                        <div class="banat-stat-icon">
                            <i class="fa-solid fa-mobile-screen-button"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-800" style="color: var(--banat-primary-dark);">Android</h4>
                            <span class="small text-muted">Mobile App Support</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3D Biometric Interactive Showcase Section (Target destination of 3D Model) -->
    <section class="section-3d-showcase py-5 position-relative" id="fitur-3d">
        <div class="container py-4">
            <div class="text-center max-width-xl mx-auto mb-5">
                <span class="badge-banat-tag mb-2">
                    <i class="fa-solid fa-cube me-1"></i> INTERACTIVE 3D BIOMETRICS
                </span>
                <h2 style="color: var(--banat-text-dark); font-weight: 800; font-size: 2.4rem; letter-spacing: -0.5px;" class="mb-3">
                    Presensi Presisi Berbasis Mesin Fingerspot 3D
                </h2>
                <p class="text-muted lead" style="max-width: 680px; margin: 0 auto; font-size: 1.1rem;">
                    Scroll ke bawah untuk melihat rotasi interaktif 3D dan teknologi sinkronisasi otomatis absensi Banat UII Dalwa.
                </p>
            </div>

            <div class="row align-items-center g-4">
                <!-- 3D Showcase Target Container -->
                <div class="col-12 col-lg-6">
                    <div class="canvas-3d-container" id="canvas3dContainer" 
                         style="height: 480px; position: relative; background: radial-gradient(circle at 50% 35%, #241d31 0%, #120d1c 100%) !important; border: 1.5px solid rgba(251, 113, 133, 0.35) !important; border-radius: 28px; box-shadow: 0 20px 50px rgba(18, 13, 28, 0.4); overflow: hidden; cursor: grab;">
                        
                        <!-- Status Badge -->
                        <div class="badge-3d-floating" style="background: rgba(30, 25, 42, 0.88); backdrop-filter: blur(10px); color: #ffffff; border: 1px solid rgba(251, 113, 133, 0.4); border-radius: 30px; padding: 6px 14px; font-size: 0.82rem; position: absolute; top: 16px; left: 20px; z-index: 2; pointer-events: none; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-circle text-success" style="font-size: 8px;"></i>
                            <span>Mesin Biometrik 3D Aktif</span>
                        </div>

                        <!-- Laser scanning light effect -->
                        <div class="scanner-laser-line"></div>

                        <!-- Instruction hint -->
                        <div class="badge-3d-hint" style="background: rgba(251, 113, 133, 0.2); backdrop-filter: blur(10px); border: 1px solid rgba(251, 113, 133, 0.35); color: #fbcfe8; border-radius: 30px; padding: 6px 16px; font-size: 0.82rem; position: absolute; bottom: 16px; left: 50%; transform: translateX(-50%); z-index: 2; pointer-events: none; white-space: nowrap; display: inline-flex; align-items: center;">
                            <i class="fa-solid fa-arrows-rotate me-1"></i> Putar & Scroll Interaktif
                        </div>
                    </div>
                </div>

                <!-- 3D Feature Highlights -->
                <div class="col-12 col-lg-6">
                    <div class="d-flex flex-column gap-3">
                        <div class="feature-3d-card card-3d-tilt">
                            <div class="d-flex align-items-start gap-3">
                                <div class="feature-3d-icon">
                                    <i class="fa-solid fa-bolt-lightning"></i>
                                </div>
                                <div>
                                    <h5 class="fw-700 mb-1" style="color: var(--banat-text-dark);">Pemindaian Biometrik Ultra Cepat</h5>
                                    <p class="text-muted small mb-0">
                                        Sensor optik presisi tinggi membaca sidik jari dalam waktu kurang dari 0.5 detik, menjamin pencatatan kehadiran yang akurat dan bebas antrean.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="feature-3d-card card-3d-tilt">
                            <div class="d-flex align-items-start gap-3">
                                <div class="feature-3d-icon">
                                    <i class="fa-solid fa-cloud-arrow-up"></i>
                                </div>
                                <div>
                                    <h5 class="fw-700 mb-1" style="color: var(--banat-text-dark);">Sinkronisasi Cloud Realtime</h5>
                                    <p class="text-muted small mb-0">
                                        Setiap ketukan presensi dari mesin fingerspot otomatis ditransmisikan dan terekap di server pusat secara langsung tanpa jeda manual.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="feature-3d-card card-3d-tilt">
                            <div class="d-flex align-items-start gap-3">
                                <div class="feature-3d-icon">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </div>
                                <div>
                                    <h5 class="fw-700 mb-1" style="color: var(--banat-text-dark);">Keamanan & Integritas Terverifikasi</h5>
                                    <p class="text-muted small mb-0">
                                        Enkripsi data berlapis dan pencatatan audit log memastikan integritas presensi dosen, ustadzah, serta civitas akademik selalu terjaga.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Data Absensi Tabs Section (Rekapitulasi Kehadiran) -->
    <section class="py-5 position-relative" id="data-absensi" style="background: var(--banat-bg-surface); z-index: 1040;">
        <div class="container py-2">
            <div class="text-center max-width-xl mx-auto mb-4">
                <span class="badge-banat-tag mb-2">REKAPITULASI DEDIKASI</span>
                <h2 class="fw-800" style="color: var(--banat-text-dark);">Data Kehadiran Pengajar & Staf</h2>
                <p class="text-muted">Pantau rekapitulasi kehadiran civitas akademik UII Dalwa Kampus Banat</p>
            </div>

            <!-- Tab Switcher (Responsive for Desktop & Mobile) -->
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-3 mb-4 pb-3 border-bottom" style="border-color: var(--banat-border) !important;">
                <ul class="nav nav-pills d-flex flex-row flex-wrap gap-2 mb-0" id="myTab" role="tablist">
                    <li class="nav-item flex-fill flex-sm-grow-0" role="presentation">
                        <button class="btn btn-banat-outline w-100 active text-nowrap" id="nav-tab-dosen" data-bs-toggle="tab" data-bs-target="#tab-dosen"
                            type="button" role="tab" aria-controls="tab-dosen" aria-selected="true">
                            <i class="fa-solid fa-chalkboard-user me-1"></i> Dosen & Pengajar
                        </button>
                    </li>
                    <li class="nav-item flex-fill flex-sm-grow-0" role="presentation">
                        <button class="btn btn-banat-outline w-100 text-nowrap" id="nav-tab-staff" data-bs-toggle="tab" data-bs-target="#tab-staff"
                            type="button" role="tab" aria-controls="tab-staff" aria-selected="false">
                            <i class="fa-solid fa-id-card-clip me-1"></i> Staf Akademik
                        </button>
                    </li>
                </ul>
                <a href="{{ route('absensi.index') }}" class="btn-banat-outline text-decoration-none d-inline-flex align-items-center justify-content-center text-nowrap">
                    <span>Lihat Semua</span> <i class="fa-solid fa-arrow-right ms-2"></i>
                </a>
            </div>

            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="tab-dosen" role="tabpanel" aria-labelledby="nav-tab-dosen">
                    <div class="loader-container text-center py-4">
                        <div class="spinner-border text-danger" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab-staff" role="tabpanel" aria-labelledby="nav-tab-staff">
                    <div class="loader-container text-center py-4">
                        <div class="spinner-border text-danger" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Action Button at Bottom of Tab Content -->
            <div class="text-center mt-3 pt-2 d-block d-sm-none">
                <a href="{{ route('absensi.index') }}" class="btn-banat-outline w-100 py-2 text-decoration-none d-inline-flex align-items-center justify-content-center">
                    <span>Lihat Seluruh Civitas Akademika</span> <i class="fa-solid fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Download App Banner Section -->
    <section class="py-5" style="background: var(--banat-bg-soft); z-index: 1040; position: relative;">
        <div class="container">
            <div class="glass-card p-4 p-lg-5 overflow-hidden position-relative" 
                 style="background: linear-gradient(135deg, #1f1929 0%, #2e243d 100%); color: #ffffff; border: none; border-radius: 28px; box-shadow: 0 20px 45px rgba(31, 25, 41, 0.4);">
                <div class="row align-items-center position-relative" style="z-index: 2;">
                    <div class="col-lg-8 mb-4 mb-lg-0">
                        <span class="badge mb-3 px-3 py-2" style="background: rgba(251, 113, 133, 0.2); color: #fb7185; border: 1px solid rgba(251, 113, 133, 0.3); border-radius: 20px; font-weight: 600;">
                            <i class="fa-brands fa-google-play me-1"></i> MOBILITY & CONVENIENCE
                        </span>
                        <h2 class="fw-800 text-white mb-3 display-6">Unduh Aplikasi Mobile Absensi Banat</h2>
                        <p class="lead mb-4" style="color: rgba(255, 255, 255, 0.82); font-size: 1.05rem;">
                            Dapatkan kemudahan akses rekapitulasi kehadiran civitas UII Dalwa secara langsung di genggaman Anda melalui Google Play Store.
                        </p>
                        <a href="https://play.google.com/store/apps/details?id=com.uiidalwa.absensi" 
                           target="_blank" 
                           class="btn-banat-primary">
                            <i class="fa-brands fa-google-play fa-lg"></i> Unduh di Google Play
                        </a>
                    </div>
                    <div class="col-lg-4 text-center">
                        <div class="position-relative d-inline-block">
                            <i class="fa-solid fa-mobile-screen-button display-1 animate-float" style="color: #f472b6; opacity: 0.85; font-size: 7rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <!-- Three.js & GLTFLoader for 3D Fingerspot Machine -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>

    <script>
        // Data loading for tabs (Departemen 3: Dosen, 4: Staff)
        function loadData(departemen_id, element) {
            $.get("{{ route('root.getData') }}", {
                departemen_id: departemen_id
            })
            .done(function(response) {
                $(element).html(response);
            })
            .fail(function() {
                $(element).html('<div class="text-center text-muted py-4"><i class="fa-regular fa-folder-open mb-2 fa-2x"></i><p>Belum ada data presensi untuk kategori ini.</p></div>');
            });
        }

        $(document).ready(function() {
            loadData(3, '#tab-dosen');
            loadData(4, '#tab-staff');
        });

        // Scroll Progress Bar Update
        window.addEventListener('scroll', function() {
            const scrollTop = window.scrollY || document.documentElement.scrollTop;
            const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrollPercent = (scrollTop / (scrollHeight || 1)) * 100;
            const progressBar = document.getElementById('scrollProgress');
            if (progressBar) {
                progressBar.style.width = scrollPercent + '%';
            }
        });

        // =========================================================================
        // THREE.JS SCROLL TRANSITION (HERO TO SHOWCASE WITH ROTATION & ZOOM)
        // =========================================================================
        (function initScrollJourney3D() {
            const canvas = document.getElementById('canvas3d');
            const heroAnchor = document.getElementById('hero3dAnchor');
            const showcaseContainer = document.getElementById('canvas3dContainer');
            if (!canvas || !heroAnchor || !showcaseContainer) return;

            const scene = new THREE.Scene();
            const camera = new THREE.PerspectiveCamera(45, window.innerWidth / window.innerHeight, 0.1, 1000);
            camera.position.set(0, 0, 4.8);

            const renderer = new THREE.WebGLRenderer({
                canvas: canvas,
                alpha: true,
                antialias: true
            });
            renderer.setSize(window.innerWidth, window.innerHeight);
            renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

            // Studio Lights: Crisp contrast and rich textures without white blowout
            const ambientLight = new THREE.AmbientLight(0xffffff, 0.55);
            scene.add(ambientLight);

            const mainLight = new THREE.DirectionalLight(0xffffff, 0.95);
            mainLight.position.set(4, 5, 5);
            scene.add(mainLight);

            const fillLight = new THREE.DirectionalLight(0xfff1f2, 0.35);
            fillLight.position.set(-4, 2, 4);
            scene.add(fillLight);

            const pinkRimLight = new THREE.PointLight(0xfb7185, 2.4, 12);
            pinkRimLight.position.set(-3, 2.5, 2.5);
            scene.add(pinkRimLight);

            const cyanFillLight = new THREE.PointLight(0x38bdf8, 1.6, 10);
            cyanFillLight.position.set(3, -2, 2.5);
            scene.add(cyanFillLight);

            // Holographic platform ring
            const ringGeo = new THREE.RingGeometry(0.85, 0.9, 64);
            const ringMat = new THREE.MeshBasicMaterial({ 
                color: 0xfb7185, 
                side: THREE.DoubleSide, 
                transparent: true, 
                opacity: 0.45 
            });
            const ring = new THREE.Mesh(ringGeo, ringMat);
            ring.rotation.x = Math.PI / 2;
            ring.position.y = -0.68;

            const modelGroup = new THREE.Group();
            modelGroup.add(ring);
            scene.add(modelGroup);

            let model = null;
            let isDragging = false;
            let previousMousePosition = { x: 0, y: 0 };
            let dragOffset = { x: 0, y: 0 };

            // Load 3D Model
            const loader = new THREE.GLTFLoader();
            const modelUrl = "{{ asset('finger3d.glb') }}";

            loader.load(
                modelUrl,
                function(gltf) {
                    model = gltf.scene;

                    // Center model on pivot
                    const box = new THREE.Box3().setFromObject(model);
                    const center = box.getCenter(new THREE.Vector3());
                    const size = box.getSize(new THREE.Vector3());
                    const maxDim = Math.max(size.x, size.y, size.z);
                    const baseScale = 2.0 / maxDim;

                    model.scale.set(baseScale, baseScale, baseScale);
                    model.position.sub(center.multiplyScalar(baseScale));
                    model.position.y += 0.02;

                    modelGroup.add(model);
                },
                undefined,
                function(error) {
                    console.warn('3D Model load error:', error);
                }
            );

            // Responsive Resize
            function onWindowResize() {
                if (!camera || !renderer) return;
                camera.aspect = window.innerWidth / window.innerHeight;
                camera.updateProjectionMatrix();
                renderer.setSize(window.innerWidth, window.innerHeight);
            }
            window.addEventListener('resize', onWindowResize);

            // Drag Interaction on Hero and Showcase
            function setupDragListeners(el) {
                if (!el) return;
                el.addEventListener('mousedown', function(e) {
                    isDragging = true;
                    previousMousePosition = { x: e.clientX, y: e.clientY };
                });
                el.addEventListener('touchstart', function(e) {
                    if (e.touches.length === 1) {
                        isDragging = true;
                        previousMousePosition = { x: e.touches[0].clientX, y: e.touches[0].clientY };
                    }
                }, { passive: true });
            }
            setupDragListeners(heroAnchor);
            setupDragListeners(showcaseContainer);

            window.addEventListener('mousemove', function(e) {
                if (!isDragging) return;
                const deltaX = e.clientX - previousMousePosition.x;
                const deltaY = e.clientY - previousMousePosition.y;
                dragOffset.x += deltaX * 0.012;
                dragOffset.y += deltaY * 0.012;
                dragOffset.y = Math.max(-0.6, Math.min(0.6, dragOffset.y));
                previousMousePosition = { x: e.clientX, y: e.clientY };
            });

            window.addEventListener('touchmove', function(e) {
                if (!isDragging || e.touches.length !== 1) return;
                const deltaX = e.touches[0].clientX - previousMousePosition.x;
                const deltaY = e.touches[0].clientY - previousMousePosition.y;
                dragOffset.x += deltaX * 0.015;
                dragOffset.y += deltaY * 0.015;
                dragOffset.y = Math.max(-0.6, Math.min(0.6, dragOffset.y));
                previousMousePosition = { x: e.touches[0].clientX, y: e.touches[0].clientY };
            }, { passive: true });

            window.addEventListener('mouseup', function() { isDragging = false; });
            window.addEventListener('touchend', function() { isDragging = false; });

            // Helper: 3D visible world dimensions at given Z plane
            function getVisibleDims(cam, z) {
                const vFOV = (cam.fov * Math.PI) / 180;
                const vHeight = 2 * Math.tan(vFOV / 2) * Math.abs(cam.position.z - z);
                const vWidth = vHeight * (window.innerWidth / window.innerHeight);
                return { width: vWidth, height: vHeight };
            }

            // Smooth Interpolation State
            let currentX = 1.38;
            let currentY = 0.32;
            let currentScale = 0.74;
            let currentRotY = -0.42;
            let currentRotX = 0.10;

            function animate() {
                requestAnimationFrame(animate);

                if (ring) {
                    ring.rotation.z += 0.006;
                }

                if (model) {
                    const scrollY = window.pageYOffset || document.documentElement.scrollTop;
                    const isDesktop = window.innerWidth >= 992;
                    const dims = getVisibleDims(camera, 0);

                    // Real-time tracking of DOM elements in 3D world space
                    let heroWorldX = isDesktop ? 1.38 : 0;
                    let heroWorldY = isDesktop ? 0.32 : -0.10;
                    let showcaseWorldX = isDesktop ? -1.38 : 0;
                    let showcaseWorldY = 0;

                    if (heroAnchor && showcaseContainer) {
                        const heroRect = heroAnchor.getBoundingClientRect();
                        const heroCenterX = heroRect.left + heroRect.width / 2;
                        const heroCenterY = heroRect.top + heroRect.height / 2;
                        heroWorldX = (((heroCenterX / window.innerWidth) * 2 - 1) * dims.width) / 2;
                        heroWorldY = (-((heroCenterY / window.innerHeight) * 2 - 1) * dims.height) / 2;

                        const showcaseRect = showcaseContainer.getBoundingClientRect();
                        const showcaseCenterX = showcaseRect.left + showcaseRect.width / 2;
                        const showcaseCenterY = showcaseRect.top + showcaseRect.height / 2;
                        showcaseWorldX = (((showcaseCenterX / window.innerWidth) * 2 - 1) * dims.width) / 2;
                        // Center in container with subtle offset to clear the top badge
                        showcaseWorldY = (-((showcaseCenterY / window.innerHeight) * 2 - 1) * dims.height) / 2 - 0.04;
                    }

                    // Progress from Hero (0) to Showcase Center (1)
                    const showcaseDocTop = showcaseContainer.getBoundingClientRect().top + scrollY;
                    const focusScrollY = Math.max(showcaseDocTop - (window.innerHeight - showcaseContainer.offsetHeight) / 2, 400);
                    const rawProgress = Math.min(Math.max(scrollY / focusScrollY, 0), 1);

                    // Smooth cubic ease in-out
                    const easeT = rawProgress < 0.5 
                        ? 4 * rawProgress * rawProgress * rawProgress 
                        : 1 - Math.pow(-2 * rawProgress + 2, 3) / 2;

                    // Zoom scaling: 0.74 (hero) -> 0.96 (showcase, ~30% zoom in)
                    const startScale = isDesktop ? 0.74 : 0.68;
                    const endScale = isDesktop ? 0.96 : 0.88;

                    const targetX = heroWorldX + (showcaseWorldX - heroWorldX) * easeT;
                    const targetY = heroWorldY + (showcaseWorldY - heroWorldY) * easeT;
                    const targetScale = startScale + (endScale - startScale) * easeT;

                    // 3D Aesthetic Rotation Journey:
                    // Starts facing slightly right/forward, spins 360 (+2*PI) with an elegant tilt, and settles facing the viewer
                    const targetRotY = -0.42 + easeT * (Math.PI * 2 + 0.18) + dragOffset.x;
                    const targetRotX = 0.10 - Math.sin(easeT * Math.PI) * 0.28 + dragOffset.y;

                    // Drag momentum decay
                    if (!isDragging) {
                        dragOffset.x *= 0.94;
                        dragOffset.y *= 0.94;
                    }

                    // Lerp for buttery smoothness
                    currentX += (targetX - currentX) * 0.085;
                    currentY += (targetY - currentY) * 0.085;
                    currentScale += (targetScale - currentScale) * 0.085;
                    currentRotY += (targetRotY - currentRotY) * 0.085;
                    currentRotX += (targetRotX - currentRotX) * 0.085;

                    // Subtle floating breath effect
                    const breathY = Math.sin(Date.now() * 0.0016) * 0.025;

                    modelGroup.position.x = currentX;
                    modelGroup.position.y = currentY + breathY;
                    modelGroup.scale.setScalar(currentScale);
                    modelGroup.rotation.y = currentRotY;
                    modelGroup.rotation.x = currentRotX;

                    // Opacity fadeout when scrolling past showcase into Data Absensi
                    const showcaseRect = showcaseContainer.getBoundingClientRect();
                    if (showcaseRect.bottom < 150) {
                        const fade = Math.max(0, showcaseRect.bottom / 150);
                        canvas.style.opacity = fade;
                        canvas.style.visibility = fade <= 0.01 ? 'hidden' : 'visible';
                    } else {
                        canvas.style.opacity = 1;
                        canvas.style.visibility = 'visible';
                    }
                }

                renderer.render(scene, camera);
            }
            animate();
        })();

        // 3D Card Hover Perspective Effect
        document.querySelectorAll('.card-3d-tilt').forEach(function(card) {
            card.addEventListener('mousemove', function(e) {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;

                const rotateX = ((y - centerY) / centerY) * -5;
                const rotateY = ((x - centerX) / centerX) * 5;

                card.style.transform = `perspective(800px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-4px)`;
            });

            card.addEventListener('mouseleave', function() {
                card.style.transform = '';
            });
        });
    </script>
@endpush
