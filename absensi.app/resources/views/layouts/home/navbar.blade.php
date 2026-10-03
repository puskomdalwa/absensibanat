<!-- Floating Pill Navbar (UII Dalwa Proceeding Style with Scroll Transition & Transparent Initial State) -->
@php
    $isLandingPage = request()->routeIs('root.index*');
    $isLoginPage = request()->routeIs('login');
@endphp
<header class="dalwa-floating-header {{ $isLandingPage ? 'navbar-landing' : 'navbar-inner-page navbar-scrolled' }}">
    <div class="container" style="max-width: 1240px; padding: 0 16px;">
        <nav class="dalwa-navbar-pill d-flex align-items-center justify-content-between px-3 px-md-4 py-2">
            
            <!-- Brand Logo (UII Dalwa Calligraphy & Emblem) -->
            <a href="{{ route('root.index') }}" class="d-inline-flex align-items-center text-decoration-none py-1">
                <img src="{{ asset('home/assets/imgs/theme/logoDalwa.png') }}" 
                     alt="UII Dalwa" 
                     class="dalwa-nav-logo-img" 
                     style="height: 38px; width: auto; max-width: 250px; object-fit: contain; display: block;" />
            </a>

            <!-- Desktop Menu (Home with black pill & Login / Dashboard) -->
            <div class="d-none d-md-flex align-items-center gap-2">
                <a href="{{ route('root.index') }}" 
                   class="{{ $isLandingPage ? 'dalwa-pill-btn-home active' : 'dalwa-pill-btn-text' }}">
                    Home
                </a>

                <!-- Desktop Search Trigger Button -->
                <button type="button" class="dalwa-pill-btn-search" id="navSearchBtnDesktop" title="Cari Civitas & Presensi (Ctrl+K)" aria-label="Buka Pencarian Civitas">
                    <i class="fa-solid fa-magnifying-glass search-btn-icon"></i>
                    <span class="d-none d-lg-inline ms-1">Cari Civitas</span>
                    <kbd class="d-none d-xl-inline-block ms-1 dalwa-search-kbd">Ctrl+K</kbd>
                </button>

                @if (\Auth::check())
                    @if (\Auth::user()->hasRole('admin', 'staff'))
                        <a href="{{ url('/admin/dashboard') }}" class="dalwa-pill-btn-text">
                            Dashboard
                        </a>
                    @endif
                    <a href="{{ route('logout') }}" 
                       class="dalwa-pill-btn-logout ms-1"
                       title="Keluar"
                       onclick="event.preventDefault(); document.getElementById('navbar-logout-form').submit();">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    </a>
                    <form id="navbar-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                @else
                    <a href="{{ route('login') }}" class="dalwa-pill-btn-text {{ $isLoginPage ? 'active' : '' }}">
                        Login
                    </a>
                @endif

                <!-- Light / Dark Theme Mode Toggle Button (Desktop Only) -->
                <button type="button" class="dalwa-theme-toggle-btn ms-1" id="themeToggleBtn" title="Ganti Mode Terang / Gelap" aria-label="Toggle Theme">
                    <i class="fa-solid fa-moon icon-moon"></i>
                    <i class="fa-solid fa-sun icon-sun"></i>
                </button>
            </div>

            <!-- Mobile Actions (Clean & Compact: Only Login + Matching Rose Hamburger Button) -->
            <div class="d-flex d-md-none align-items-center gap-2" style="pointer-events: auto !important;">
                @if (!\Auth::check())
                    <a href="{{ route('login') }}" class="dalwa-mobile-login-btn {{ $isLoginPage ? 'active' : '' }}" style="pointer-events: auto !important;">
                        Login
                    </a>
                @else
                    @if (\Auth::user()->hasRole('admin', 'staff'))
                        <a href="{{ url('/admin/dashboard') }}" class="dalwa-mobile-login-btn" title="Dashboard" style="pointer-events: auto !important;">
                            <i class="fa-solid fa-gauge-high"></i>
                        </a>
                    @endif
                @endif
                <button type="button" class="dalwa-hamburger-btn" id="banatMobileMenuToggle" aria-label="Toggle Navigation" title="Buka Menu" onclick="if(window.openDrawer) window.openDrawer();" style="pointer-events: auto !important;">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </nav>
    </div>
</header>

<!-- Mobile Navigation Drawer -->
<div id="banatMobileDrawer" style="display: none; position: fixed; inset: 0; z-index: 99999;">
    <!-- Backdrop -->
    <div id="banatDrawerBackdrop" style="position: absolute; inset: 0; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(5px);" onclick="if(window.closeDrawer) window.closeDrawer();"></div>
    
    <!-- Drawer Panel -->
    <div class="banat-drawer-panel" style="position: absolute; top: 0; right: 0; width: 85%; max-width: 320px; height: 100%; background: #ffffff; box-shadow: -10px 0 30px rgba(0, 0, 0, 0.25); display: flex; flex-direction: column; z-index: 2;">
        <!-- Top bar with logo & close -->
        <div class="d-flex align-items-center justify-content-between p-3 border-bottom" style="border-color: rgba(224, 82, 117, 0.15) !important;">
            <img src="{{ asset('home/assets/imgs/theme/logoDalwa.png') }}" alt="Logo" class="dalwa-drawer-logo" style="height: 30px; width: auto; max-width: 175px; object-fit: contain;" />
            <button type="button" class="btn-close" id="banatDrawerClose" aria-label="Close" onclick="if(window.closeDrawer) window.closeDrawer();"></button>
        </div>
        
        <!-- Menu list -->
        <div class="p-3 p-sm-4 d-flex flex-column justify-content-between flex-grow-1 overflow-auto">
            <div class="d-flex flex-column gap-2">
                <!-- 1. Home Link -->
                <a href="{{ route('root.index') }}" 
                   class="banat-mobile-link {{ request()->routeIs('root.index*') && !request()->is('*#data-absensi') ? 'active' : '' }}">
                    <i class="fa-solid fa-house-chimney me-2" style="color: #fb7185; width: 22px; text-align: center;"></i> Home
                </a>

                @if (\Auth::check())
                    @if (!\Auth::user()->hasRole('admin', 'staff'))
                        <!-- 2.1 Dashboard Khusus Role Selain Admin & Staff (Dosen, User, Santri, dll) -->
                        <a href="{{ url('/dashboard') }}" 
                           class="banat-mobile-link {{ request()->is('dashboard*') ? 'active' : '' }}">
                            <i class="fa-solid fa-gauge-high me-2" style="color: #fb7185; width: 22px; text-align: center;"></i> Dashboard
                        </a>

                        <!-- 2.2 Rekapitulasi Presensi -->
                        <a href="{{ url('/#data-absensi') }}" 
                           class="banat-mobile-link"
                           onclick="if(window.closeDrawer) window.closeDrawer();">
                            <i class="fa-solid fa-clipboard-user me-2" style="color: #fb7185; width: 22px; text-align: center;"></i> Rekapitulasi Presensi
                        </a>

                        <!-- 2.3 Laporan -->
                        <a href="{{ url('/laporan') }}" 
                           class="banat-mobile-link {{ request()->is('laporan*') ? 'active' : '' }}">
                            <i class="fa-solid fa-file-invoice me-2" style="color: #fb7185; width: 22px; text-align: center;"></i> Laporan
                        </a>
                    @else
                        <!-- Khusus Admin & Staff -->
                        <a href="{{ url('/admin/dashboard') }}" 
                           class="banat-mobile-link {{ request()->is('admin/dashboard*') ? 'active' : '' }}">
                            <i class="fa-solid fa-gauge-high me-2" style="color: #fb7185; width: 22px; text-align: center;"></i> Dashboard Admin
                        </a>

                        <a href="{{ url('/#data-absensi') }}" 
                           class="banat-mobile-link"
                           onclick="if(window.closeDrawer) window.closeDrawer();">
                            <i class="fa-solid fa-clipboard-user me-2" style="color: #fb7185; width: 22px; text-align: center;"></i> Rekapitulasi Presensi
                        </a>

                        <a href="{{ url('/laporan') }}" 
                           class="banat-mobile-link {{ request()->is('laporan*') ? 'active' : '' }}">
                            <i class="fa-solid fa-file-invoice me-2" style="color: #fb7185; width: 22px; text-align: center;"></i> Laporan
                        </a>
                    @endif
                @else
                    <!-- Tamu / Guest (Belum Login) -->
                    <a href="{{ url('/#data-absensi') }}" 
                       class="banat-mobile-link"
                       onclick="if(window.closeDrawer) window.closeDrawer();">
                        <i class="fa-solid fa-clipboard-user me-2" style="color: #fb7185; width: 22px; text-align: center;"></i> Rekapitulasi Presensi
                    </a>
                @endif

                <!-- Mobile Search Trigger Button inside Drawer -->
                <button type="button" class="btn text-start p-3 rounded-4 border banat-drawer-search-btn mt-2" id="navSearchBtnMobile" aria-label="Buka Pencarian Civitas">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="search-drawer-icon-box">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </div>
                            <div>
                                <div class="fw-700 search-drawer-title" style="font-size: 0.95rem; color: var(--banat-text-dark);">Pencarian Civitas</div>
                                <div class="text-muted small" style="font-size: 0.76rem;">Filter dosen & staf akademik</div>
                            </div>
                        </div>
                        <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-1">
                            <i class="fa-solid fa-arrow-right"></i>
                        </span>
                    </div>
                </button>

                <!-- Theme Mode Toggle inside Drawer (Mobile Only) -->
                <button type="button" class="btn text-start p-2 rounded-3 d-flex align-items-center justify-content-between border banat-drawer-theme-btn mt-1" id="drawerThemeToggleBtn">
                    <span class="small fw-700"><i class="fa-solid fa-circle-half-stroke me-2 text-danger"></i> Mode Tampilan</span>
                    <span class="badge bg-light text-dark theme-mode-text">Light</span>
                </button>
            </div>

            <!-- Bottom Actions -->
            <div class="pt-3 border-top mt-3" style="border-color: rgba(224, 82, 117, 0.15) !important;">
                @if (\Auth::check())
                    <div class="d-flex flex-column gap-2">
                        @if (\Auth::user()->hasRole('admin', 'staff'))
                            <a href="{{ url('/admin/dashboard') }}" class="btn-banat-login w-100 text-center">
                                <i class="fa-solid fa-gauge-high me-1"></i> Dashboard Absensi
                            </a>
                        @endif
                        <a href="{{ route('logout') }}" 
                           class="btn btn-outline-danger w-100 text-center py-2"
                           style="border-radius: 50px; font-weight: 600;"
                           onclick="event.preventDefault(); document.getElementById('mobile-logout-form').submit();">
                            <i class="fa-solid fa-arrow-right-from-bracket me-1"></i> Logout
                        </a>
                        <form id="mobile-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn-banat-login w-100 text-center">
                        <i class="fa-solid fa-arrow-right-to-bracket me-2"></i> Masuk / Login
                    </a>
                @endif
                <div class="text-center mt-3 text-muted small">
                    Portal Presensi Banat • UII Dalwa
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Full Popup Modal Pencarian Civitas (Banat Luxury Fullscreen Modal) -->
<div id="banatSearchModal" class="banat-search-modal-backdrop" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="searchModalTitle">
    <div class="banat-search-modal-container">
        
        <!-- Modal Top Bar / Header -->
        <div class="banat-search-modal-header d-flex align-items-center justify-content-between p-3 p-md-4 border-bottom">
            <div class="d-flex align-items-center gap-3">
                <div class="banat-search-modal-logo-icon">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <div>
                    <h5 class="fw-800 mb-0 search-modal-title" id="searchModalTitle">Pencarian Civitas Akademika</h5>
                    <p class="small text-muted mb-0">UII Dalwa Kampus Banat • Dosen, Pengajar & Staf</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge d-none d-md-inline-flex align-items-center gap-1 bg-light text-muted border px-2 py-1" style="font-size: 0.75rem;">
                    <kbd style="background: transparent; color: inherit; font-family: inherit;">ESC</kbd> untuk tutup
                </span>
                <button type="button" class="btn-close-banat-modal" id="closeSearchModal" aria-label="Tutup Pencarian">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <!-- Search Input Bar & Quick Clear -->
        <div class="p-3 p-md-4 pb-2 pb-md-2 border-bottom banat-search-filter-section">
            <div class="banat-search-input-wrapper position-relative">
                <i class="fa-solid fa-magnifying-glass banat-search-field-icon"></i>
                <input type="text" 
                       id="banatSearchModalInput" 
                       class="form-control banat-search-input-field" 
                       placeholder="Ketik nama pengajar, staf, atau ID civitas..." 
                       autocomplete="off"
                       spellcheck="false" />
                <div class="spinner-border spinner-border-sm text-danger banat-search-spinner d-none" id="searchLoadingSpinner" role="status">
                    <span class="visually-hidden">Mencari...</span>
                </div>
                <button type="button" class="banat-search-clear-btn d-none" id="clearSearchInput" aria-label="Hapus kata kunci">
                    <i class="fa-solid fa-circle-xmark"></i>
                </button>
            </div>

            <!-- Department Filter Pills (Scrollable on Mobile, Responsive Filter) -->
            <div class="mt-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small fw-700 text-muted" style="font-size: 0.76rem; letter-spacing: 0.5px; text-transform: uppercase;">
                        <i class="fa-solid fa-filter me-1 text-danger"></i> Filter Departemen
                    </span>
                    <span class="small text-muted" id="searchResultCountBadge" style="font-size: 0.8rem; font-weight: 600;">
                        Memuat data...
                    </span>
                </div>
                <div class="banat-dept-chips-scroll d-flex align-items-center gap-2 pb-1">
                    <button type="button" class="banat-dept-chip active" data-dept-id="all">
                        <i class="fa-solid fa-layer-group me-1"></i> Semua Departemen
                    </button>
                    @if (isset($departemen) && count($departemen) > 0)
                        @foreach ($departemen as $d)
                            <button type="button" class="banat-dept-chip" data-dept-id="{{ $d->id }}">
                                <i class="fa-solid fa-building-user me-1"></i> {{ $d->nama }}
                            </button>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <!-- Search Results Scroll Area -->
        <div class="banat-search-results-area p-3 p-md-4 pt-3 flex-grow-1 overflow-auto" id="searchResultsArea">
            <div class="text-center py-5" id="searchInitialState">
                <div class="mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 70px; height: 70px; background: rgba(224, 82, 117, 0.1); color: var(--banat-primary);">
                        <i class="fa-solid fa-magnifying-glass fa-2x"></i>
                    </div>
                </div>
                <h6 class="fw-700 mb-1" style="color: var(--banat-text-dark);">Pencarian Direktori Civitas</h6>
                <p class="text-muted small mb-0" style="max-width: 400px; margin: 0 auto;">
                    Ketik nama dosen atau staf, atau pilih departemen di atas untuk menemukan data presensi.
                </p>
            </div>
        </div>

        <!-- Modal Footer / Quick Navigation -->
        <div class="p-3 px-4 border-top d-flex align-items-center justify-content-between flex-wrap gap-2 banat-search-modal-footer">
            <div class="small text-muted d-none d-sm-block">
                <i class="fa-solid fa-circle-info me-1 text-danger"></i> Klik kartu civitas untuk langsung melihat rincian presensi.
            </div>
            <a href="{{ route('absensi.index') }}" class="btn btn-sm btn-banat-outline ms-auto">
                <i class="fa-solid fa-address-book me-1"></i> Buka Direktori Lengkap
            </a>
        </div>
    </div>
</div>

<style>
/* ==========================================================================
   HEADER CONTAINER & FLOATING POSITION
   ========================================================================== */
.dalwa-floating-header {
    position: absolute;
    top: 32px;
    left: 0;
    right: 0;
    width: 100%;
    z-index: 1040;
    pointer-events: none;
    transition: top 0.3s cubic-bezier(0.16, 1, 0.3, 1), transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

@media (max-width: 768px) {
    .dalwa-floating-header {
        top: 14px;
    }
}

/* ==========================================================================
   SCROLLED STATE (FIXED + SLIDE UP TO DOWN ANIMATION)
   ========================================================================== */
.dalwa-floating-header.navbar-scrolled {
    position: fixed !important;
    top: 14px !important;
    z-index: 1050 !important;
}

.dalwa-floating-header.navbar-landing.navbar-scrolled {
    animation: dalwaNavbarSlideDown 0.38s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes dalwaNavbarSlideDown {
    0% {
        transform: translateY(-50px);
        opacity: 0;
    }
    100% {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Base Navbar Pill Structure */
.dalwa-navbar-pill {
    border-radius: 60px;
    pointer-events: auto;
    transition: background 0.3s cubic-bezier(0.16, 1, 0.3, 1), 
                border-color 0.3s cubic-bezier(0.16, 1, 0.3, 1), 
                box-shadow 0.3s cubic-bezier(0.16, 1, 0.3, 1),
                backdrop-filter 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

/* ==========================================================================
   1. INITIAL STATE (KHUSUS LANDING PAGE AWALAN HERO: TRANSPARAN TANPA BACKGROUND)
   ========================================================================== */
.dalwa-floating-header.navbar-landing:not(.navbar-scrolled) .dalwa-navbar-pill {
    background: transparent !important;
    background-color: transparent !important;
    border: 1px solid transparent !important;
    border-color: transparent !important;
    box-shadow: none !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
}

/* Mode Light di Awalan Landing: Memakai logo putih kontras di atas dark hero */
.dalwa-floating-header.navbar-landing:not(.navbar-scrolled) .dalwa-nav-logo-img {
    filter: brightness(0) invert(1) drop-shadow(0 2px 8px rgba(0, 0, 0, 0.5)) !important;
}

/* Teks tombol menu di awalan Landing: putih agar kontras di atas dark hero */
.dalwa-floating-header.navbar-landing:not(.navbar-scrolled) .dalwa-pill-btn-text {
    color: #ffffff !important;
    text-shadow: 0 1px 4px rgba(0, 0, 0, 0.5);
}

.dalwa-floating-header.navbar-landing:not(.navbar-scrolled) .dalwa-pill-btn-text:hover {
    color: #fda4af !important;
    background: rgba(255, 255, 255, 0.15) !important;
}

.dalwa-floating-header.navbar-landing:not(.navbar-scrolled) .dalwa-theme-toggle-btn {
    background: rgba(255, 255, 255, 0.18) !important;
    border: 1px solid rgba(255, 255, 255, 0.35) !important;
    color: #ffffff !important;
    backdrop-filter: blur(8px);
}

.dalwa-floating-header.navbar-landing:not(.navbar-scrolled) .dalwa-theme-toggle-btn:hover {
    background: rgba(255, 255, 255, 0.3) !important;
    color: #ffffff !important;
    transform: rotate(20deg) scale(1.08);
}

.dalwa-floating-header.navbar-landing:not(.navbar-scrolled) .dalwa-mobile-login-btn {
    background: rgba(255, 255, 255, 0.2) !important;
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.35) !important;
    backdrop-filter: blur(8px);
}

.dalwa-floating-header.navbar-landing:not(.navbar-scrolled) .dalwa-pill-btn-home {
    background: rgba(0, 0, 0, 0.75) !important;
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.25) !important;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3);
}

/* ==========================================================================
   2. SOLID STATE (KETIKA SCROLLED ATAU PADA HALAMAN DALAM SEPERTI LOGIN)
   ========================================================================== */

/* Light Mode: Solid White Glass Pill */
.dalwa-floating-header.navbar-scrolled .dalwa-navbar-pill,
.dalwa-floating-header.navbar-inner-page .dalwa-navbar-pill {
    background: rgba(255, 255, 255, 0.95) !important;
    backdrop-filter: blur(16px) saturate(180%) !important;
    -webkit-backdrop-filter: blur(16px) saturate(180%) !important;
    border: 1px solid rgba(224, 82, 117, 0.15) !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08) !important;
}

/* Light Mode: Logo kembali ke versi normal/asli (Penuh Warna Hijau, Emas, Hitam) */
.dalwa-floating-header.navbar-scrolled .dalwa-nav-logo-img,
.dalwa-floating-header.navbar-inner-page .dalwa-nav-logo-img {
    filter: none !important;
}

.dalwa-floating-header.navbar-scrolled .dalwa-pill-btn-text,
.dalwa-floating-header.navbar-inner-page .dalwa-pill-btn-text {
    color: #1e1926 !important;
    text-shadow: none !important;
}

.dalwa-floating-header.navbar-scrolled .dalwa-pill-btn-text:hover,
.dalwa-floating-header.navbar-inner-page .dalwa-pill-btn-text:hover {
    color: #e05275 !important;
    background: #fdf2f4 !important;
}

/* Active Login Pill Button in Navbar (High Contrast & Luxury Rose) */
.dalwa-pill-btn-text.active {
    background: linear-gradient(135deg, #fb7185 0%, #e11d48 100%) !important;
    color: #ffffff !important;
    box-shadow: 0 4px 14px rgba(225, 29, 72, 0.35) !important;
    font-weight: 700 !important;
}
.dalwa-pill-btn-text.active:hover {
    color: #ffffff !important;
    background: linear-gradient(135deg, #f43f5e 0%, #be123c 100%) !important;
}

.dalwa-floating-header.navbar-scrolled .dalwa-theme-toggle-btn,
.dalwa-floating-header.navbar-inner-page .dalwa-theme-toggle-btn {
    background: #fdf2f4 !important;
    border: 1px solid rgba(0, 0, 0, 0.08) !important;
    color: #e05275 !important;
}

.dalwa-floating-header.navbar-scrolled .dalwa-mobile-login-btn,
.dalwa-floating-header.navbar-inner-page .dalwa-mobile-login-btn,
.dalwa-mobile-login-btn {
    background: linear-gradient(135deg, #fb7185 0%, #e11d48 100%) !important;
    color: #ffffff !important;
    border: none !important;
    box-shadow: 0 4px 12px rgba(225, 29, 72, 0.35) !important;
    font-weight: 700 !important;
}

.dalwa-floating-header.navbar-scrolled .dalwa-pill-btn-home,
.dalwa-floating-header.navbar-inner-page .dalwa-pill-btn-home {
    background: #000000 !important;
    color: #ffffff !important;
    border: none !important;
}

/* ==========================================================================
   3. DARK MODE RULES (AWALAN & SCROLLED & INNER PAGES)
   ========================================================================== */
[data-theme="dark"] .dalwa-floating-header.navbar-scrolled .dalwa-navbar-pill,
[data-theme="dark"] .dalwa-floating-header.navbar-inner-page .dalwa-navbar-pill {
    background: rgba(20, 16, 29, 0.95) !important;
    backdrop-filter: blur(20px) !important;
    -webkit-backdrop-filter: blur(20px) !important;
    border: 1px solid rgba(251, 113, 133, 0.28) !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.65) !important;
}

[data-theme="dark"] .dalwa-nav-logo-img,
[data-theme="dark"] .dalwa-drawer-logo {
    filter: brightness(0) invert(1) drop-shadow(0 2px 6px rgba(251, 113, 133, 0.4)) !important;
}

[data-theme="dark"] .dalwa-floating-header.navbar-scrolled .dalwa-nav-logo-img,
[data-theme="dark"] .dalwa-floating-header.navbar-inner-page .dalwa-nav-logo-img {
    filter: brightness(0) invert(1) drop-shadow(0 2px 6px rgba(251, 113, 133, 0.4)) !important;
}

[data-theme="dark"] .dalwa-pill-btn-home {
    background: linear-gradient(135deg, #fb7185 0%, #e11d48 100%) !important;
    color: #ffffff !important;
    box-shadow: 0 4px 15px rgba(225, 29, 72, 0.4) !important;
}

[data-theme="dark"] .dalwa-floating-header.navbar-scrolled .dalwa-pill-btn-text,
[data-theme="dark"] .dalwa-floating-header.navbar-inner-page .dalwa-pill-btn-text,
[data-theme="dark"] .dalwa-floating-header .dalwa-pill-btn-text {
    color: #f1f5f9 !important;
    text-shadow: none !important;
}

[data-theme="dark"] .dalwa-floating-header.navbar-scrolled .dalwa-pill-btn-text:hover,
[data-theme="dark"] .dalwa-floating-header.navbar-inner-page .dalwa-pill-btn-text:hover,
[data-theme="dark"] .dalwa-floating-header .dalwa-pill-btn-text:hover {
    color: #fb7185 !important;
    background: rgba(251, 113, 133, 0.15) !important;
}

[data-theme="dark"] .dalwa-pill-btn-text.active {
    background: linear-gradient(135deg, #fb7185 0%, #e11d48 100%) !important;
    color: #ffffff !important;
    box-shadow: 0 4px 15px rgba(225, 29, 72, 0.45) !important;
}

[data-theme="dark"] .dalwa-floating-header.navbar-scrolled .dalwa-theme-toggle-btn,
[data-theme="dark"] .dalwa-floating-header.navbar-inner-page .dalwa-theme-toggle-btn,
[data-theme="dark"] .dalwa-theme-toggle-btn {
    background: #251d33 !important;
    border: 1px solid rgba(251, 113, 133, 0.25) !important;
    color: #fb7185 !important;
}

[data-theme="dark"] .dalwa-mobile-login-btn {
    background: linear-gradient(135deg, #fb7185 0%, #e11d48 100%) !important;
    box-shadow: 0 4px 12px rgba(225, 29, 72, 0.4) !important;
    color: #ffffff !important;
}

/* ==========================================================================
   HAMBURGER BUTTON (MATCHING BUTTON UP / #scrollUp GRADIENT & SHADOW)
   ========================================================================== */
.dalwa-hamburger-btn {
    width: 38px !important;
    height: 38px !important;
    border-radius: 50% !important;
    background: linear-gradient(135deg, #fb7185 0%, #e11d48 100%) !important;
    color: #ffffff !important;
    border: 1.5px solid rgba(255, 255, 255, 0.45) !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    box-shadow: 0 4px 14px rgba(225, 29, 72, 0.45) !important;
    cursor: pointer !important;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
    padding: 0 !important;
    outline: none !important;
    flex-shrink: 0 !important;
}

.dalwa-hamburger-btn i {
    font-size: 15px !important;
    color: #ffffff !important;
    line-height: 1 !important;
}

.dalwa-hamburger-btn:hover {
    transform: translateY(-1px) scale(1.06);
    box-shadow: 0 6px 18px rgba(225, 29, 72, 0.65) !important;
    color: #ffffff !important;
}

.dalwa-hamburger-btn:active {
    transform: scale(0.96);
}

/* ==========================================================================
   PROMINENT LOGIN BUTTON IN DRAWER (MENCOLOK & HIGH CONTRAST)
   ========================================================================== */
.btn-banat-login {
    background: linear-gradient(135deg, #fb7185 0%, #e11d48 100%) !important;
    color: #ffffff !important;
    border: none !important;
    padding: 13px 22px !important;
    border-radius: 50px !important;
    font-weight: 700 !important;
    font-size: 0.96rem !important;
    letter-spacing: 0.3px;
    box-shadow: 0 6px 20px rgba(225, 29, 72, 0.45) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    text-decoration: none !important;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
}

.btn-banat-login i {
    color: #ffffff !important;
    font-size: 1.05rem !important;
}

.btn-banat-login:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(225, 29, 72, 0.65) !important;
    color: #ffffff !important;
}

/* Drawer Theme Toggle Button */
.banat-drawer-theme-btn {
    background: rgba(224, 82, 117, 0.08) !important;
    border: 1px solid rgba(224, 82, 117, 0.25) !important;
    border-radius: 14px !important;
    padding: 11px 16px !important;
    color: #1e1926 !important;
    transition: all 0.25s ease !important;
    font-size: 0.92rem !important;
    cursor: pointer !important;
}

.banat-drawer-theme-btn:hover {
    background: rgba(224, 82, 117, 0.14) !important;
    border-color: #fb7185 !important;
}

[data-theme="dark"] .banat-drawer-theme-btn {
    background: rgba(251, 113, 133, 0.12) !important;
    border: 1px solid rgba(251, 113, 133, 0.35) !important;
    color: #f8fafc !important;
}

[data-theme="dark"] .banat-drawer-theme-btn:hover {
    background: rgba(251, 113, 133, 0.2) !important;
    border-color: #fb7185 !important;
}

.banat-drawer-theme-btn .theme-mode-text {
    font-size: 0.78rem !important;
    padding: 5px 10px !important;
    border-radius: 20px !important;
    font-weight: 700 !important;
}

/* Drawer Dark Mode Overrides */
[data-theme="dark"] .banat-drawer-panel {
    background: #161124 !important;
    color: #f8fafc !important;
    border-left: 1px solid rgba(251, 113, 133, 0.25) !important;
}

[data-theme="dark"] .banat-drawer-panel .border-bottom,
[data-theme="dark"] .banat-drawer-panel .border-top {
    border-color: rgba(251, 113, 133, 0.2) !important;
}

[data-theme="dark"] #banatDrawerClose {
    filter: invert(1) brightness(2) !important;
    opacity: 0.85 !important;
}

[data-theme="dark"] #banatDrawerClose:hover {
    opacity: 1 !important;
}

.banat-mobile-link {
    font-weight: 600 !important;
    font-size: 0.95rem !important;
    padding: 10px 14px !important;
    border-radius: 12px !important;
    color: #1e1926 !important;
    text-decoration: none !important;
    transition: all 0.2s ease !important;
}

.banat-mobile-link:hover,
.banat-mobile-link.active {
    background: #fdf2f4 !important;
    color: #e05275 !important;
}

[data-theme="dark"] .banat-mobile-link {
    color: #f8fafc !important;
}

[data-theme="dark"] .banat-mobile-link:hover,
[data-theme="dark"] .banat-mobile-link.active {
    background: rgba(251, 113, 133, 0.15) !important;
    color: #fb7185 !important;
}

/* ==========================================================================
   BUTTONS & CONTROLS STYLING
   ========================================================================== */
.dalwa-pill-btn-home {
    border-radius: 50px !important;
    padding: 7px 22px !important;
    font-weight: 600 !important;
    font-size: 0.94rem !important;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.dalwa-pill-btn-home:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
    color: #ffffff !important;
}

.dalwa-pill-btn-text {
    font-weight: 600 !important;
    font-size: 0.94rem !important;
    padding: 7px 18px !important;
    text-decoration: none !important;
    border-radius: 50px;
    transition: color 0.2s ease, background 0.2s ease;
}

.dalwa-pill-btn-logout {
    color: #e11d48;
    background: #fff0f3;
    border: 1px solid rgba(225, 29, 72, 0.2);
    padding: 6px 12px;
    border-radius: 50px;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}
.dalwa-pill-btn-logout:hover {
    background: #ffe4e6;
    color: #be123c;
}

.dalwa-theme-toggle-btn {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    border: 1px solid rgba(0, 0, 0, 0.08);
    background: #fdf2f4;
    color: #e05275;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 15px;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.dalwa-theme-toggle-btn:hover {
    transform: rotate(20deg) scale(1.08);
    box-shadow: 0 4px 12px rgba(224, 82, 117, 0.25);
}

[data-theme="dark"] .dalwa-theme-toggle-btn .icon-moon {
    display: none !important;
}
[data-theme="dark"] .dalwa-theme-toggle-btn .icon-sun {
    display: inline-block !important;
}
[data-theme="light"] .dalwa-theme-toggle-btn .icon-sun,
:root:not([data-theme="dark"]) .dalwa-theme-toggle-btn .icon-sun {
    display: none !important;
}
[data-theme="light"] .dalwa-theme-toggle-btn .icon-moon,
:root:not([data-theme="dark"]) .dalwa-theme-toggle-btn .icon-moon {
    display: inline-block !important;
}

.dalwa-mobile-login-btn {
    font-size: 0.84rem;
    font-weight: 600;
    padding: 6px 16px;
    border-radius: 30px;
    text-decoration: none;
    transition: all 0.2s ease;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

@media (max-width: 576px) {
    .dalwa-nav-logo-img {
        max-width: 150px !important;
        height: 32px !important;
    }
}

@media (max-width: 380px) {
    .dalwa-nav-logo-img {
        max-width: 130px !important;
        height: 28px !important;
    }
    .dalwa-mobile-login-btn {
        padding: 5px 12px;
        font-size: 0.78rem;
    }
}

/* ==========================================================================
   DESKTOP NAVBAR SEARCH BUTTON
   ========================================================================== */
.dalwa-pill-btn-search {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 50px;
    background: rgba(224, 82, 117, 0.08);
    border: 1px solid rgba(224, 82, 117, 0.22);
    color: var(--banat-text-dark, #1e1926);
    font-size: 0.85rem;
    font-weight: 600;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    cursor: pointer;
    white-space: nowrap;
    outline: none;
}

.dalwa-pill-btn-search:hover {
    background: rgba(224, 82, 117, 0.16);
    border-color: rgba(224, 82, 117, 0.4);
    color: #e11d48;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(224, 82, 117, 0.12);
}

.dalwa-pill-btn-search .search-btn-icon {
    color: #e11d48;
    font-size: 0.9rem;
}

.dalwa-search-kbd {
    font-size: 0.68rem;
    padding: 2px 6px;
    border-radius: 6px;
    background: rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(0, 0, 0, 0.1);
    color: var(--banat-text-muted, #8c8296);
    font-family: inherit;
    font-weight: 700;
}

[data-theme="dark"] .dalwa-pill-btn-search {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.15);
    color: #ffffff;
}

[data-theme="dark"] .dalwa-pill-btn-search:hover {
    background: rgba(251, 113, 133, 0.2);
    border-color: rgba(251, 113, 133, 0.4);
    color: #fb7185;
}

[data-theme="dark"] .dalwa-search-kbd {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.2);
    color: rgba(255, 255, 255, 0.7);
}

/* ==========================================================================
   MOBILE HAMBURGER DRAWER SEARCH BUTTON
   ========================================================================== */
.banat-drawer-search-btn {
    width: 100%;
    background: linear-gradient(135deg, rgba(251, 113, 133, 0.08) 0%, rgba(225, 29, 72, 0.04) 100%);
    border: 1px solid rgba(224, 82, 117, 0.22) !important;
    transition: all 0.2s ease;
    cursor: pointer;
}

.banat-drawer-search-btn:hover,
.banat-drawer-search-btn:active {
    background: linear-gradient(135deg, rgba(251, 113, 133, 0.15) 0%, rgba(225, 29, 72, 0.08) 100%);
    border-color: #fb7185 !important;
    transform: translateY(-1px);
}

.search-drawer-icon-box {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    background: linear-gradient(135deg, #fb7185 0%, #e11d48 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    box-shadow: 0 4px 10px rgba(225, 29, 72, 0.25);
    flex-shrink: 0;
}

[data-theme="dark"] .banat-drawer-search-btn {
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(255, 255, 255, 0.1) !important;
}

/* ==========================================================================
   FULL POPUP SEARCH MODAL (DESKTOP & MOBILE RESPONSIVE)
   ========================================================================== */
.banat-search-modal-backdrop {
    position: fixed;
    inset: 0;
    z-index: 100000;
    background: rgba(15, 23, 42, 0.75);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.25s ease, visibility 0.25s ease;
}

.banat-search-modal-backdrop.active {
    opacity: 1;
    visibility: visible;
}

.banat-search-modal-container {
    width: 92vw;
    max-width: 920px;
    height: 86vh;
    max-height: 820px;
    background: var(--banat-bg-surface, #ffffff);
    border-radius: 28px;
    box-shadow: 0 30px 80px rgba(0, 0, 0, 0.35);
    border: 1px solid var(--banat-border, rgba(224, 82, 117, 0.2));
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transform: scale(0.96);
    transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.banat-search-modal-backdrop.active .banat-search-modal-container {
    transform: scale(1);
}

/* Responsive Fullscreen on Mobile Phones */
@media (max-width: 576px) {
    .banat-search-modal-backdrop {
        padding: 0 !important;
    }
    .banat-search-modal-container {
        width: 100vw !important;
        height: 100vh !important;
        max-width: 100vw !important;
        max-height: 100vh !important;
        border-radius: 0 !important;
        border: none !important;
        position: fixed;
        inset: 0;
    }
}

.banat-search-modal-logo-icon {
    width: 44px;
    height: 44px;
    border-radius: 14px;
    background: linear-gradient(135deg, rgba(251, 113, 133, 0.15) 0%, rgba(225, 29, 72, 0.1) 100%);
    color: #e11d48;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    flex-shrink: 0;
}

.btn-close-banat-modal {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: rgba(0, 0, 0, 0.05);
    border: 1px solid rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--banat-text-medium, #4a4154);
    font-size: 1.1rem;
    transition: all 0.2s ease;
    cursor: pointer;
}

.btn-close-banat-modal:hover {
    background: rgba(225, 29, 72, 0.1);
    color: #e11d48;
    border-color: rgba(225, 29, 72, 0.2);
    transform: rotate(90deg);
}

/* Search input field */
.banat-search-field-icon {
    position: absolute;
    left: 18px;
    top: 50%;
    transform: translateY(-50%);
    color: #fb7185;
    font-size: 1.15rem;
    pointer-events: none;
}

.banat-search-input-field {
    width: 100%;
    height: 52px;
    padding: 12px 50px 12px 48px;
    font-size: 1.05rem;
    font-weight: 500;
    border-radius: 16px;
    border: 2px solid rgba(224, 82, 117, 0.2);
    background: #ffffff;
    color: var(--banat-text-dark, #1e1926);
    transition: all 0.2s ease;
}

.banat-search-input-field:focus {
    border-color: #fb7185;
    box-shadow: 0 0 0 4px rgba(251, 113, 133, 0.18);
    background: #ffffff;
    outline: none;
}

.banat-search-spinner {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
}

.banat-search-clear-btn {
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    border: none;
    color: var(--banat-text-muted, #8c8296);
    font-size: 1.25rem;
    cursor: pointer;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color 0.2s ease;
}

.banat-search-clear-btn:hover {
    color: #e11d48;
}

/* Chips filter */
.banat-dept-chips-scroll {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    ms-overflow-style: none;
}

.banat-dept-chips-scroll::-webkit-scrollbar {
    display: none;
}

.banat-dept-chip {
    padding: 6px 14px;
    font-size: 0.82rem;
    font-weight: 600;
    border-radius: 30px;
    border: 1px solid var(--banat-border, rgba(224, 82, 117, 0.2));
    background: #ffffff;
    color: var(--banat-text-medium, #4a4154);
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
    flex-shrink: 0;
    outline: none;
}

.banat-dept-chip:hover {
    border-color: #fb7185;
    color: #e11d48;
    background: rgba(251, 113, 133, 0.08);
}

.banat-dept-chip.active {
    background: linear-gradient(135deg, #fb7185 0%, #e11d48 100%) !important;
    color: #ffffff !important;
    border-color: transparent !important;
    box-shadow: 0 4px 12px rgba(225, 29, 72, 0.25);
}

/* Search result card */
.banat-search-result-card {
    background: #ffffff;
    border: 1px solid var(--banat-border, rgba(224, 82, 117, 0.16));
    box-shadow: 0 4px 12px rgba(224, 82, 117, 0.04);
    transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
    overflow: hidden !important;
    width: 100%;
    box-sizing: border-box;
}

.banat-search-result-card:hover {
    background: #ffffff;
    border-color: #fb7185;
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(224, 82, 117, 0.12);
}

.banat-search-card-body {
    flex: 1 1 0%;
    min-width: 0 !important;
    width: 0 !important;
    overflow: hidden;
}

.banat-search-dept-badge {
    display: inline-flex;
    align-items: center;
    padding: 2px 8px !important;
    font-size: 0.72rem !important;
    font-weight: 600;
    border-radius: 6px !important;
    background: rgba(224, 82, 117, 0.08);
    color: #e11d48;
    border: 1px solid rgba(224, 82, 117, 0.2);
    line-height: 1.25;
    max-width: 130px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.banat-search-civitas-name {
    font-size: 0.92rem;
    font-weight: 700;
    color: var(--banat-text-dark, #1e1926);
    line-height: 1.35;
    margin-bottom: 3px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    word-break: break-word;
    overflow-wrap: anywhere;
}

.banat-search-id-badge {
    font-size: 0.74rem;
    font-weight: 600;
    color: var(--banat-text-muted, #8c8296);
    flex-shrink: 0;
    white-space: nowrap;
}

.banat-search-highlight {
    background: rgba(251, 113, 133, 0.25);
    color: #e11d48;
    padding: 0 3px;
    border-radius: 4px;
    font-weight: 700;
}

/* Dark mode overrides for search modal */
[data-theme="dark"] .banat-search-modal-container {
    background: #1a1622;
    border-color: rgba(255, 255, 255, 0.1);
    color: #ffffff;
}

[data-theme="dark"] .banat-search-modal-header,
[data-theme="dark"] .banat-search-filter-section,
[data-theme="dark"] .banat-search-modal-footer {
    background: #16121d !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
}

[data-theme="dark"] .banat-search-input-field {
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(255, 255, 255, 0.12);
    color: #ffffff;
}

[data-theme="dark"] .banat-search-input-field:focus {
    background: rgba(255, 255, 255, 0.08);
    border-color: #fb7185;
}

[data-theme="dark"] .banat-dept-chip {
    background: rgba(255, 255, 255, 0.06);
    border-color: rgba(255, 255, 255, 0.12);
    color: rgba(255, 255, 255, 0.85);
}

[data-theme="dark"] .btn-close-banat-modal {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.15);
    color: #ffffff;
}

[data-theme="dark"] .banat-search-result-card {
    background: rgba(255, 255, 255, 0.04);
    border-color: rgba(255, 255, 255, 0.08);
}

[data-theme="dark"] .banat-search-result-card:hover {
    background: rgba(255, 255, 255, 0.08);
    border-color: #fb7185;
}

[data-theme="dark"] .banat-search-civitas-name {
    color: #f1f5f9 !important;
}

[data-theme="dark"] .banat-search-dept-badge {
    background: rgba(251, 113, 133, 0.15) !important;
    color: #fda4af !important;
    border-color: rgba(251, 113, 133, 0.3) !important;
}

[data-theme="dark"] .banat-search-id-badge {
    color: rgba(255, 255, 255, 0.5) !important;
}
</style>

<script>
    window.openDrawer = function() {
        const drawerEl = document.getElementById('banatMobileDrawer');
        if (drawerEl) drawerEl.style.display = 'block';
        document.body.style.overflow = 'hidden';
    };

    window.closeDrawer = function() {
        const drawerEl = document.getElementById('banatMobileDrawer');
        if (drawerEl) drawerEl.style.display = 'none';
        document.body.style.overflow = '';
    };

    document.addEventListener('DOMContentLoaded', function() {
        // Mobile Drawer Elements
        const toggleBtn = document.getElementById('banatMobileMenuToggle');
        const closeBtn = document.getElementById('banatDrawerClose');
        const backdrop = document.getElementById('banatDrawerBackdrop');

        if (toggleBtn) toggleBtn.addEventListener('click', window.openDrawer);
        if (closeBtn) closeBtn.addEventListener('click', window.closeDrawer);
        if (backdrop) backdrop.addEventListener('click', window.closeDrawer);

        // Theme Toggle Functionality
        function applyTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            try {
                localStorage.setItem('banat_theme', theme);
            } catch(e) {}

            const modeBadge = document.querySelector('.theme-mode-text');
            if (modeBadge) {
                modeBadge.textContent = theme === 'dark' ? 'Dark' : 'Light';
                modeBadge.className = theme === 'dark' ? 'badge bg-dark text-light theme-mode-text' : 'badge bg-light text-dark theme-mode-text';
            }
        }

        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
            applyTheme(nextTheme);
        }

        const themeBtn = document.getElementById('themeToggleBtn');
        const drawerThemeBtn = document.getElementById('drawerThemeToggleBtn');

        if (themeBtn) themeBtn.addEventListener('click', toggleTheme);
        if (drawerThemeBtn) drawerThemeBtn.addEventListener('click', toggleTheme);

        // Initialize mobile badge state
        const initialTheme = document.documentElement.getAttribute('data-theme') || 'light';
        const modeBadge = document.querySelector('.theme-mode-text');
        if (modeBadge) {
            modeBadge.textContent = initialTheme === 'dark' ? 'Dark' : 'Light';
            modeBadge.className = initialTheme === 'dark' ? 'badge bg-dark text-light theme-mode-text' : 'badge bg-light text-dark theme-mode-text';
        }

        // ======================================================================
        // FULL POPUP SEARCH MODAL LOGIC (DESKTOP & MOBILE RESPONSIVE)
        // ======================================================================
        const searchModal = document.getElementById('banatSearchModal');
        const searchInput = document.getElementById('banatSearchModalInput');
        const searchSpinner = document.getElementById('searchLoadingSpinner');
        const searchClearBtn = document.getElementById('clearSearchInput');
        const searchCountBadge = document.getElementById('searchResultCountBadge');
        const searchResultsArea = document.getElementById('searchResultsArea');
        const closeSearchModalBtn = document.getElementById('closeSearchModal');

        const desktopSearchBtn = document.getElementById('navSearchBtnDesktop');
        const mobileSearchBtn = document.getElementById('navSearchBtnMobile');

        let currentDeptId = 'all';
        let searchDebounceTimer = null;
        let currentSearchXhr = null;

        function openSearchModal() {
            // Close mobile hamburger drawer if open
            closeDrawer();
            
            if (searchModal) {
                searchModal.style.display = 'flex';
                // Trigger reflow for CSS animation
                void searchModal.offsetWidth;
                searchModal.classList.add('active');
                document.body.style.overflow = 'hidden';
                setTimeout(() => {
                    if (searchInput) searchInput.focus();
                }, 150);
                
                // Load initial results
                executeSearch(searchInput ? searchInput.value : '');
            }
        }

        function closeSearchModal() {
            if (searchModal) {
                searchModal.classList.remove('active');
                setTimeout(() => {
                    searchModal.style.display = 'none';
                    document.body.style.overflow = '';
                }, 200);
            }
        }

        if (desktopSearchBtn) desktopSearchBtn.addEventListener('click', openSearchModal);
        if (mobileSearchBtn) mobileSearchBtn.addEventListener('click', openSearchModal);
        if (closeSearchModalBtn) closeSearchModalBtn.addEventListener('click', closeSearchModal);

        // Close on backdrop click (outside container)
        if (searchModal) {
            searchModal.addEventListener('click', function(e) {
                if (e.target === searchModal) {
                    closeSearchModal();
                }
            });
        }

        // Keyboard shortcuts (Ctrl+K or Cmd+K to open, ESC to close)
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                if (searchModal && searchModal.classList.contains('active')) {
                    closeSearchModal();
                } else {
                    openSearchModal();
                }
            } else if (e.key === 'Escape') {
                if (searchModal && searchModal.classList.contains('active')) {
                    closeSearchModal();
                }
            }
        });

        // Clear input button
        if (searchClearBtn) {
            searchClearBtn.addEventListener('click', function() {
                if (searchInput) {
                    searchInput.value = '';
                    searchClearBtn.classList.add('d-none');
                    searchInput.focus();
                    executeSearch('');
                }
            });
        }

        // Input change listener with debounce
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const val = this.value;
                if (val.trim().length > 0) {
                    if (searchClearBtn) searchClearBtn.classList.remove('d-none');
                } else {
                    if (searchClearBtn) searchClearBtn.classList.add('d-none');
                }
                
                clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(() => {
                    executeSearch(val);
                }, 250);
            });
        }

        // Department filter chips click listener
        document.querySelectorAll('.banat-dept-chip').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.banat-dept-chip').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentDeptId = this.getAttribute('data-dept-id') || 'all';
                executeSearch(searchInput ? searchInput.value : '');
            });
        });

        function executeSearch(query) {
            if (searchSpinner) searchSpinner.classList.remove('d-none');
            if (searchCountBadge) searchCountBadge.textContent = 'Mencari...';
            
            if (currentSearchXhr && typeof currentSearchXhr.abort === 'function') {
                currentSearchXhr.abort();
            }
            
            currentSearchXhr = $.ajax({
                url: "{{ route('root.searchCivitas') }}",
                method: "GET",
                data: {
                    q: query,
                    departemen_id: currentDeptId
                },
                dataType: "json",
                success: function(res) {
                    renderSearchResults(res.data, query, res.total);
                },
                error: function(xhr, status) {
                    if (status !== 'abort') {
                        if (searchResultsArea) {
                            searchResultsArea.innerHTML = `
                                <div class="alert banat-alert-danger text-center my-4 py-4">
                                    <i class="fa-solid fa-triangle-exclamation fa-2x mb-2 text-danger"></i>
                                    <p class="mb-0">Gagal memuat data pencarian. Silakan coba kembali.</p>
                                </div>
                            `;
                        }
                        if (searchCountBadge) searchCountBadge.textContent = 'Error';
                    }
                },
                complete: function() {
                    if (searchSpinner) searchSpinner.classList.add('d-none');
                }
            });
        }

        function renderSearchResults(items, query, totalCount) {
            if (!searchResultsArea) return;
            
            if (searchCountBadge) {
                searchCountBadge.textContent = `${totalCount || (items ? items.length : 0)} Civitas Ditemukan`;
            }
            
            if (!items || items.length === 0) {
                searchResultsArea.innerHTML = `
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 70px; height: 70px; background: rgba(224, 82, 117, 0.1); color: var(--banat-primary);">
                                <i class="fa-regular fa-folder-open fa-2x"></i>
                            </div>
                        </div>
                        <h6 class="fw-700 mb-1" style="color: var(--banat-text-dark);">Civitas Tidak Ditemukan</h6>
                        <p class="text-muted small mb-3" style="max-width: 380px; margin: 0 auto;">
                            Tidak ditemukan data civitas dengan kata kunci "<strong>${escapeHtml(query)}</strong>" pada departemen yang dipilih.
                        </p>
                        <button type="button" class="btn btn-sm btn-banat-outline" onclick="window.resetSearchFilters()">
                            <i class="fa-solid fa-rotate-left me-1"></i> Reset Pencarian
                        </button>
                    </div>
                `;
                return;
            }
            
            let html = '<div class="row g-3">';
            items.forEach(user => {
                let displayName = user.name;
                if (query && query.trim()) {
                    const regex = new RegExp(`(${escapeRegex(query.trim())})`, 'gi');
                    displayName = user.name.replace(regex, '<mark class="banat-search-highlight">$1</mark>');
                }
                
                html += `
                    <div class="col-12 col-sm-6 col-lg-4">
                        <a href="${user.url}" class="banat-search-result-card d-flex align-items-center gap-3 p-3 rounded-4 text-decoration-none h-100">
                            <div class="position-relative flex-shrink-0">
                                <img src="${user.photo}" 
                                     alt="${escapeHtml(user.name)}" 
                                     class="rounded-circle shadow-sm" 
                                     style="width: 48px; height: 48px; object-fit: cover; border: 2px solid var(--banat-primary-light);" 
                                     onerror="this.src='{{ asset('home/assets/imgs/theme/user.png') }}'" />
                            </div>
                            <div class="banat-search-card-body">
                                <div class="d-flex align-items-center justify-content-between gap-1 mb-1" style="min-width: 0;">
                                    <span class="banat-search-dept-badge text-nowrap">
                                        ${escapeHtml(user.departemen_nama)}
                                    </span>
                                    <span class="banat-search-id-badge">#${user.id}</span>
                                </div>
                                <div class="banat-search-civitas-name" title="${escapeHtml(user.name)}">
                                    ${displayName}
                                </div>
                                <div class="d-flex align-items-center justify-content-between text-muted small mt-1" style="font-size: 0.76rem; min-width: 0;">
                                    <span class="text-truncate text-muted me-1" style="font-size: 0.75rem;">Catatan Presensi</span>
                                    <span class="d-inline-flex align-items-center gap-1 text-danger fw-600 flex-shrink-0" style="font-size: 0.72rem;">
                                        <span>Lihat</span>
                                        <i class="fa-solid fa-chevron-right" style="font-size: 0.65rem;"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                `;
            });
            html += '</div>';
            
            searchResultsArea.innerHTML = html;
        }

        window.resetSearchFilters = function() {
            if (searchInput) searchInput.value = '';
            if (searchClearBtn) searchClearBtn.classList.add('d-none');
            document.querySelectorAll('.banat-dept-chip').forEach(b => b.classList.remove('active'));
            const allChip = document.querySelector('.banat-dept-chip[data-dept-id="all"]');
            if (allChip) allChip.classList.add('active');
            currentDeptId = 'all';
            executeSearch('');
        };

        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>"']/g, function(m) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
            });
        }

        function escapeRegex(str) {
            return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

        // ======================================================================
        // NAVBAR SCROLL BREAKPOINT HANDLER
        // ======================================================================
        const scrollBreakpoint = 70;
        const header = document.querySelector('.dalwa-floating-header');
        const isLandingPage = header ? header.classList.contains('navbar-landing') : false;

        function handleNavbarScroll() {
            if (!header) return;

            if (!isLandingPage) {
                if (!header.classList.contains('navbar-scrolled')) {
                    header.classList.add('navbar-scrolled');
                }
                return;
            }

            if (window.scrollY > scrollBreakpoint) {
                if (!header.classList.contains('navbar-scrolled')) {
                    header.classList.add('navbar-scrolled');
                }
            } else {
                if (header.classList.contains('navbar-scrolled')) {
                    header.classList.remove('navbar-scrolled');
                }
            }
        }

        window.addEventListener('scroll', handleNavbarScroll, { passive: true });
        handleNavbarScroll();
    });
</script>
