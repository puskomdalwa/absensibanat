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

                @if (\Auth::check())
                    <a href="{{ url('/admin/dashboard') }}" class="dalwa-pill-btn-text">
                        Dashboard
                    </a>
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
            <div class="d-flex d-md-none align-items-center gap-2">
                @if (!\Auth::check())
                    <a href="{{ route('login') }}" class="dalwa-mobile-login-btn {{ $isLoginPage ? 'active' : '' }}">
                        Login
                    </a>
                @else
                    <a href="{{ url('/admin/dashboard') }}" class="dalwa-mobile-login-btn" title="Dashboard">
                        <i class="fa-solid fa-gauge-high"></i>
                    </a>
                @endif
                <button type="button" class="dalwa-hamburger-btn" id="banatMobileMenuToggle" aria-label="Toggle Navigation" title="Buka Menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </nav>
    </div>
</header>

<!-- Mobile Navigation Drawer -->
<div id="banatMobileDrawer" style="display: none; position: fixed; inset: 0; z-index: 99999;">
    <!-- Backdrop -->
    <div id="banatDrawerBackdrop" style="position: absolute; inset: 0; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(5px);"></div>
    
    <!-- Drawer Panel -->
    <div class="banat-drawer-panel" style="position: absolute; top: 0; right: 0; width: 82%; max-width: 310px; height: 100%; background: #ffffff; box-shadow: -10px 0 30px rgba(0, 0, 0, 0.25); display: flex; flex-direction: column; z-index: 2;">
        <!-- Top bar with logo & close -->
        <div class="d-flex align-items-center justify-content-between p-3 border-bottom" style="border-color: rgba(224, 82, 117, 0.15) !important;">
            <img src="{{ asset('home/assets/imgs/theme/logoDalwa.png') }}" alt="Logo" class="dalwa-drawer-logo" style="height: 30px; width: auto; max-width: 175px; object-fit: contain;" />
            <button type="button" class="btn-close" id="banatDrawerClose" aria-label="Close"></button>
        </div>
        
        <!-- Menu list -->
        <div class="p-4 d-flex flex-column justify-content-between flex-grow-1">
            <div class="d-flex flex-column gap-3">
                <a href="{{ route('root.index') }}" 
                   class="banat-mobile-link {{ request()->routeIs('root.index*') ? 'active' : '' }}">
                    <i class="fa-solid fa-house-chimney me-2" style="color: #fb7185;"></i> Home
                </a>

                <!-- Theme Mode Toggle inside Drawer (Mobile Only) -->
                <button type="button" class="btn text-start p-2 rounded-3 d-flex align-items-center justify-content-between border banat-drawer-theme-btn" id="drawerThemeToggleBtn">
                    <span class="small fw-700"><i class="fa-solid fa-circle-half-stroke me-2 text-danger"></i> Mode Tampilan</span>
                    <span class="badge bg-light text-dark theme-mode-text">Light</span>
                </button>
            </div>

            <!-- Bottom Actions -->
            <div class="pt-3 border-top" style="border-color: rgba(224, 82, 117, 0.15) !important;">
                @if (\Auth::check())
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ url('/admin/dashboard') }}" class="btn-banat-login w-100 text-center">
                            <i class="fa-solid fa-gauge-high me-1"></i> Dashboard Absensi
                        </a>
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
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile Drawer Elements
        const toggleBtn = document.getElementById('banatMobileMenuToggle');
        const drawer = document.getElementById('banatMobileDrawer');
        const closeBtn = document.getElementById('banatDrawerClose');
        const backdrop = document.getElementById('banatDrawerBackdrop');

        function openDrawer() {
            if (drawer) drawer.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeDrawer() {
            if (drawer) drawer.style.display = 'none';
            document.body.style.overflow = '';
        }

        if (toggleBtn) toggleBtn.addEventListener('click', openDrawer);
        if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
        if (backdrop) backdrop.addEventListener('click', closeDrawer);

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
