@extends('layouts.home.template')
@section('title', 'Login | Absensi Banat UII Dalwa')

@push('css')
<style>
/* ==========================================================================
   COMPACT SPLIT-CARD LOGIN - MATCHING BANAT FEMININE LUXURY THEME
   ========================================================================== */

/* Page Viewport & Background */
.dalwa-login-viewport {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 85px 16px 30px;
    position: relative;
    background: var(--banat-soft-bg-gradient);
    overflow-x: hidden;
}

/* Ambient Radial Glows (Matching Landing Page) */
.dalwa-login-viewport::before {
    content: '';
    position: absolute;
    top: 5%;
    left: 8%;
    width: 340px;
    height: 340px;
    background: radial-gradient(circle, rgba(251, 113, 133, 0.14) 0%, rgba(253, 248, 249, 0) 70%);
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
}

.dalwa-login-viewport::after {
    content: '';
    position: absolute;
    bottom: 5%;
    right: 8%;
    width: 360px;
    height: 360px;
    background: radial-gradient(circle, rgba(244, 114, 182, 0.12) 0%, rgba(253, 248, 249, 0) 70%);
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
}

/* Compact Split Card Container (Downscaled & Balanced) */
.dalwa-split-login-card {
    position: relative;
    z-index: 1;
    width: 100%;
    max-width: 820px;
    border-radius: 22px;
    overflow: hidden;
    display: flex;
    flex-direction: row;
    box-shadow: 0 16px 45px rgba(224, 82, 117, 0.12), 
                0 0 0 1px rgba(224, 82, 117, 0.15);
    background: #ffffff;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

/* ==========================================================================
   LEFT PANEL: BRANDING & 3D INTERACTIVE SHOWCASE (DESKTOP)
   ========================================================================== */
.dalwa-login-brand-panel {
    width: 48%;
    background: linear-gradient(155deg, #241a2e 0%, #1a1222 55%, #130d1b 100%);
    padding: 28px 24px;
    position: relative;
    border-right: 1px solid rgba(251, 113, 133, 0.15);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.dalwa-login-brand-panel::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at top left, rgba(251, 113, 133, 0.18), transparent 50%),
                radial-gradient(circle at bottom right, rgba(244, 114, 182, 0.12), transparent 60%);
    pointer-events: none;
}

.dalwa-brand-sub {
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 1.5px;
    color: #fda4af;
    text-transform: uppercase;
}

.dalwa-brand-title {
    font-size: 1.08rem;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: 0.4px;
    margin: 0;
}

.dalwa-secure-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 3px 10px;
    border-radius: 50px;
    background: rgba(251, 113, 133, 0.12);
    border: 1px solid rgba(251, 113, 133, 0.25);
    color: #fecdd3;
    font-size: 0.72rem;
    font-weight: 600;
}

.dalwa-secure-badge i {
    font-size: 0.68rem;
    color: #fb7185;
}

.dalwa-login-headline {
    font-size: 1.28rem;
    font-weight: 800;
    line-height: 1.32;
    color: #ffffff;
    letter-spacing: -0.3px;
}

.dalwa-login-desc {
    color: #cbd5e1;
    font-size: 0.78rem;
    line-height: 1.5;
    margin-bottom: 8px;
}

/* 3D Model Interactive Container in Left Panel */
.dalwa-3d-box {
    width: 100%;
    height: 160px;
    border-radius: 14px;
    background: radial-gradient(circle at center, rgba(251, 113, 133, 0.14) 0%, rgba(15, 10, 22, 0.45) 80%);
    border: 1px solid rgba(251, 113, 133, 0.22);
    overflow: hidden;
    position: relative;
    cursor: grab;
    touch-action: none;
}

.dalwa-3d-box:active {
    cursor: grabbing;
}

#login3dCanvas {
    width: 100% !important;
    height: 100% !important;
    display: block;
}

.dalwa-3d-hint {
    position: absolute;
    bottom: 6px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(19, 13, 27, 0.78);
    border: 1px solid rgba(251, 113, 133, 0.3);
    color: #fda4af;
    font-size: 0.64rem;
    font-weight: 600;
    padding: 2px 9px;
    border-radius: 20px;
    pointer-events: none;
    backdrop-filter: blur(6px);
    white-space: nowrap;
}

/* Bottom Features Grid */
.dalwa-login-features-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
}

.dalwa-feature-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(251, 113, 133, 0.15);
    border-radius: 11px;
    padding: 8px 6px;
    transition: all 0.25s ease;
}

.dalwa-feature-card:hover {
    background: rgba(251, 113, 133, 0.12);
    border-color: rgba(251, 113, 133, 0.4);
    transform: translateY(-2px);
}

.dalwa-feature-icon {
    width: 24px;
    height: 24px;
    border-radius: 6px;
    background: rgba(251, 113, 133, 0.18);
    color: #fb7185;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.78rem;
    margin-bottom: 5px;
}

.dalwa-feature-name {
    font-size: 0.7rem;
    font-weight: 700;
    color: #ffffff;
    line-height: 1.2;
    margin-bottom: 1px;
}

.dalwa-feature-sub {
    font-size: 0.62rem;
    color: #94a3b8;
    line-height: 1.15;
}

/* ==========================================================================
   RIGHT PANEL: LOGIN FORM (LIGHT MODE)
   ========================================================================== */
.dalwa-login-form-panel {
    width: 52%;
    background: #ffffff;
    padding: 30px 28px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    position: relative;
}

.dalwa-portal-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 3px 11px;
    border-radius: 50px;
    background: #fff0f3;
    border: 1px solid rgba(225, 29, 72, 0.18);
    color: #e11d48;
    font-size: 0.74rem;
    font-weight: 700;
    width: fit-content;
}

.dalwa-form-title {
    font-size: 1.48rem;
    font-weight: 800;
    color: var(--banat-text-dark, #1e1926);
    letter-spacing: -0.3px;
}

.dalwa-form-subtitle {
    color: var(--banat-text-muted, #8c8296);
    font-size: 0.8rem;
    line-height: 1.45;
}

/* Compact Modern Input Controls */
.dalwa-input-label {
    display: block;
    font-size: 0.78rem;
    font-weight: 700;
    color: #334155;
    margin-bottom: 5px;
    text-transform: capitalize;
}

.dalwa-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.dalwa-input-icon {
    position: absolute;
    left: 14px;
    color: #e05275;
    font-size: 0.95rem;
    pointer-events: none;
    transition: color 0.25s ease;
    z-index: 5;
}

.dalwa-modern-input {
    width: 100% !important;
    height: 42px !important;
    padding-left: 40px !important;
    padding-right: 40px !important;
    background: #fdf8f9 !important;
    border: 1.5px solid rgba(224, 82, 117, 0.2) !important;
    border-radius: 11px !important;
    font-size: 0.88rem !important;
    font-weight: 500 !important;
    color: #1e1926 !important;
    -webkit-text-fill-color: #1e1926 !important;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
}

.dalwa-modern-input::placeholder {
    color: #94a3b8 !important;
    -webkit-text-fill-color: #94a3b8 !important;
    opacity: 1 !important;
}

.dalwa-modern-input:focus {
    background: #ffffff !important;
    border-color: #e05275 !important;
    color: #1e1926 !important;
    -webkit-text-fill-color: #1e1926 !important;
    box-shadow: 0 0 0 3px rgba(224, 82, 117, 0.16) !important;
    outline: none !important;
}

/* Password Visibility Toggle Button */
.dalwa-pwd-toggle-btn {
    position: absolute;
    right: 8px;
    background: transparent;
    border: none;
    color: #94a3b8;
    font-size: 0.92rem;
    padding: 5px 7px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    transition: all 0.2s ease;
    z-index: 6;
}

.dalwa-pwd-toggle-btn:hover {
    color: #e05275;
    background: rgba(224, 82, 117, 0.08);
}

/* Checkbox & Links */
.custom-check .form-check-input {
    width: 1.05em;
    height: 1.05em;
    margin-top: 0.18em;
    border-radius: 5px;
    border: 1.5px solid #cbd5e1;
    cursor: pointer;
}

.custom-check .form-check-input:checked {
    background-color: #e11d48;
    border-color: #e11d48;
}

.custom-check .form-check-label {
    color: #475569;
    cursor: pointer;
    font-size: 0.8rem;
}

.dalwa-back-link {
    color: #e11d48;
    text-decoration: none;
    font-size: 0.78rem;
    font-weight: 600;
    transition: color 0.2s ease;
}

.dalwa-back-link:hover {
    color: #be123c;
    text-decoration: underline;
}

/* Submit Button: Matches Landing Page Rose Gradient */
.dalwa-btn-submit {
    height: 43px;
    border-radius: 11px !important;
    background: linear-gradient(135deg, #fb7185 0%, #e11d48 100%) !important;
    color: #ffffff !important;
    border: none !important;
    font-weight: 700 !important;
    font-size: 0.92rem !important;
    box-shadow: 0 6px 18px rgba(225, 29, 72, 0.35) !important;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
    cursor: pointer;
}

.dalwa-btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 9px 24px rgba(225, 29, 72, 0.48) !important;
    background: linear-gradient(135deg, #f43f5e 0%, #be123c 100%) !important;
    color: #ffffff !important;
}

.dalwa-btn-submit:active {
    transform: translateY(0);
}

/* Footer text inside form */
.dalwa-login-footer {
    border-top: 1px solid #f1f5f9;
}

.dalwa-footer-copy {
    font-size: 0.72rem;
    color: #64748b;
    font-weight: 500;
}

.dalwa-footer-dev {
    font-size: 0.68rem;
    color: #94a3b8;
    margin-top: 2px;
}

/* Alert Error */
.dalwa-login-alert {
    background: #fff1f2;
    border: 1px solid #fecdd3;
    color: #be123c;
    border-radius: 10px;
    padding: 10px 14px;
}

/* ==========================================================================
   DARK MODE OVERRIDES (EXACTLY MATCHING LANDING PAGE OBSIDIAN PLUM)
   ========================================================================== */
[data-theme="dark"] .dalwa-login-viewport {
    background: linear-gradient(180deg, #120d1e 0%, #0c0914 100%) !important;
}

[data-theme="dark"] .dalwa-login-viewport::before {
    background: radial-gradient(circle, rgba(251, 113, 133, 0.12) 0%, rgba(12, 9, 20, 0) 70%) !important;
}

[data-theme="dark"] .dalwa-login-viewport::after {
    background: radial-gradient(circle, rgba(168, 85, 247, 0.1) 0%, rgba(12, 9, 20, 0) 70%) !important;
}

[data-theme="dark"] .dalwa-split-login-card {
    background: #171124;
    box-shadow: 0 25px 70px rgba(0, 0, 0, 0.75), 
                0 0 0 1px rgba(251, 113, 133, 0.22);
}

[data-theme="dark"] .dalwa-login-brand-panel {
    background: linear-gradient(155deg, #1c1326 0%, #140d1c 55%, #0b0710 100%);
    border-right: 1px solid rgba(251, 113, 133, 0.16);
}

[data-theme="dark"] .dalwa-login-form-panel {
    background: #171124;
}

[data-theme="dark"] .dalwa-portal-pill {
    background: rgba(251, 113, 133, 0.12);
    border-color: rgba(251, 113, 133, 0.26);
    color: #fb7185;
}

[data-theme="dark"] .dalwa-form-title {
    color: #ffffff;
}

[data-theme="dark"] .dalwa-form-subtitle {
    color: #94a3b8;
}

[data-theme="dark"] .dalwa-input-label {
    color: #e2e8f0;
}

/* Input Fields in Dark Mode: Clearly Visible & High Contrast */
[data-theme="dark"] .dalwa-modern-input {
    background: #1d162e !important;
    background-color: #1d162e !important;
    border: 1.5px solid rgba(251, 113, 133, 0.3) !important;
    color: #ffffff !important;
    -webkit-text-fill-color: #ffffff !important;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.4) !important;
}

[data-theme="dark"] .dalwa-modern-input::placeholder {
    color: rgba(226, 232, 240, 0.45) !important;
    -webkit-text-fill-color: rgba(226, 232, 240, 0.45) !important;
}

[data-theme="dark"] .dalwa-modern-input:focus {
    background: #251c3a !important;
    background-color: #251c3a !important;
    border-color: #fb7185 !important;
    color: #ffffff !important;
    -webkit-text-fill-color: #ffffff !important;
    box-shadow: 0 0 0 3px rgba(251, 113, 133, 0.25), inset 0 2px 4px rgba(0, 0, 0, 0.4) !important;
}

[data-theme="dark"] .dalwa-input-icon {
    color: #fb7185;
}

[data-theme="dark"] .dalwa-pwd-toggle-btn {
    color: #cbd5e1;
}

[data-theme="dark"] .dalwa-pwd-toggle-btn:hover {
    color: #fb7185;
    background: rgba(251, 113, 133, 0.15);
}

[data-theme="dark"] .dalwa-modern-input:-webkit-autofill,
[data-theme="dark"] .dalwa-modern-input:-webkit-autofill:hover, 
[data-theme="dark"] .dalwa-modern-input:-webkit-autofill:focus {
    -webkit-text-fill-color: #ffffff !important;
    -webkit-box-shadow: 0 0 0px 1000px #1d162e inset !important;
    box-shadow: 0 0 0px 1000px #1d162e inset !important;
    transition: background-color 5000s ease-in-out 0s !important;
}

[data-theme="dark"] .custom-check .form-check-input {
    background-color: #1d162e;
    border-color: rgba(251, 113, 133, 0.35);
}

[data-theme="dark"] .custom-check .form-check-input:checked {
    background-color: #e11d48;
    border-color: #e11d48;
}

[data-theme="dark"] .custom-check .form-check-label {
    color: #cbd5e1;
}

[data-theme="dark"] .dalwa-back-link {
    color: #fb7185;
}

[data-theme="dark"] .dalwa-back-link:hover {
    color: #fda4af;
}

[data-theme="dark"] .dalwa-login-footer {
    border-top: 1px solid rgba(251, 113, 133, 0.12);
}

[data-theme="dark"] .dalwa-footer-copy {
    color: #94a3b8;
}

[data-theme="dark"] .dalwa-footer-dev {
    color: #64748b;
}

/* ==========================================================================
   MOBILE RESPONSIVENESS & COMPACT MINIMALIST MOBILE LAYOUT
   ========================================================================== */
@media (max-width: 991.98px) {
    .dalwa-login-viewport {
        padding: 78px 14px 24px;
    }

    .dalwa-split-login-card {
        max-width: 375px;
        flex-direction: column;
        border-radius: 20px;
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.25);
    }

    .dalwa-login-form-panel {
        width: 100%;
        padding: 26px 20px;
    }

    .dalwa-form-title {
        font-size: 1.35rem;
    }

    .dalwa-form-subtitle {
        font-size: 0.78rem;
        margin-bottom: 16px !important;
    }

    .dalwa-modern-input {
        height: 40px !important;
        font-size: 0.85rem !important;
    }

    .dalwa-btn-submit {
        height: 40px;
        font-size: 0.88rem !important;
    }
}

.badge-mobile-portal {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    border-radius: 20px;
    background: #fff0f3;
    color: #e11d48;
    border: 1px solid rgba(225, 29, 72, 0.18);
    font-size: 0.72rem;
    font-weight: 700;
}

[data-theme="dark"] .badge-mobile-portal {
    background: #1d162e;
    color: #fb7185;
    border: 1px solid rgba(251, 113, 133, 0.25);
}

/* On Login Page, keep focus on split card and hide the big landing footer */
footer.py-5 {
    display: none !important;
}
</style>
@endpush

@section('content')
    <section class="dalwa-login-viewport">
        <!-- Main Split Card -->
        <div class="dalwa-split-login-card">
            
            <!-- LEFT PANEL: BRANDING & 3D INTERACTIVE SHOWCASE (DESKTOP ONLY) -->
            <div class="dalwa-login-brand-panel d-none d-lg-flex">
                <div>
                    <!-- Top Brand Header (Clean layout without the previous icon box) -->
                    <div class="mb-2">
                        <div class="dalwa-brand-sub mb-1"><i class="fa-solid fa-fingerprint me-1" style="color: #fb7185;"></i> PORTAL PRESENSI</div>
                        <h4 class="dalwa-brand-title mb-2">UII DALWA BANAT</h4>
                        <div class="dalwa-secure-badge mb-2">
                            <i class="fa-solid fa-shield-halved"></i>
                            <span>Sistem Presensi Biometrik Terpadu</span>
                        </div>
                    </div>

                    <!-- Main Headline -->
                    <h2 class="dalwa-login-headline mb-1">
                        Presensi Civitas BANAT Lebih Cepat & Akurat.
                    </h2>

                    <!-- Subtitle Description -->
                    <p class="dalwa-login-desc">
                        Akses realtime monitoring kehadiran dosen maupun staff banat rekapitulasi presensi, dan layanan biometrik dalam satu portal terpadu.
                    </p>
                </div>

                <!-- 3D Biometric Fingerspot Machine Interactive Canvas -->
                <div class="dalwa-3d-box position-relative my-2" id="login3dWrapper">
                    <canvas id="login3dCanvas"></canvas>
                    <div class="dalwa-3d-hint">
                        <i class="fa-solid fa-arrows-rotate me-1"></i> Biometrik Fingerspot 3D
                    </div>
                </div>

                <!-- Bottom 3 Feature Mini-Cards (dosen focused) -->
                <div class="dalwa-login-features-grid">
                    <div class="dalwa-feature-card">
                        <div class="dalwa-feature-icon">
                            <i class="fa-solid fa-fingerprint"></i>
                        </div>
                        <div class="dalwa-feature-name">Presensi Realtime</div>
                        <div class="dalwa-feature-sub">Biometrik Otomatis</div>
                    </div>

                    <div class="dalwa-feature-card">
                        <div class="dalwa-feature-icon">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                        <div class="dalwa-feature-name">Rekapitulasi Cepat</div>
                        <div class="dalwa-feature-sub">Laporan Dosen</div>
                    </div>

                    <div class="dalwa-feature-card">
                        <div class="dalwa-feature-icon">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div class="dalwa-feature-name">Keamanan Data</div>
                        <div class="dalwa-feature-sub">Enkripsi Aman</div>
                    </div>
                </div>
            </div>

            <!-- RIGHT PANEL: LOGIN FORM (DESKTOP & CLEAN MINIMALIST MOBILE) -->
            <div class="dalwa-login-form-panel">
                
                <!-- Mobile-Only Minimalist Brand Header -->
                <div class="d-lg-none mb-2">
                    <div class="badge-mobile-portal">
                        <i class="fa-solid fa-shield-halved me-1"></i> Presensi Banat • UII DALWA
                    </div>
                </div>

                <!-- Desktop Pill Badge -->
                <div class="d-none d-lg-inline-flex dalwa-portal-pill mb-2">
                    <i class="fa-solid fa-user-lock"></i>
                    <span>Portal Masuk</span>
                </div>

                <!-- Welcome Heading -->
                <h2 class="dalwa-form-title mb-1">Selamat Datang</h2>
                <p class="dalwa-form-subtitle mb-3">
                    Silakan masukkan username dan password Anda untuk mengakses portal absensi.
                </p>

                @if ($errors->any())
                    <div class="alert dalwa-login-alert mb-3" role="alert">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-triangle-exclamation fs-6"></i>
                            <div>
                                @foreach ($errors->all() as $item)
                                    <span class="d-block small fw-600">{{ $item }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <form method="post" action="{{ route('login') }}" id="form-login">
                    @csrf
                    
                    <!-- Username Input -->
                    <div class="mb-3">
                        <label class="dalwa-input-label" for="login-username">Username</label>
                        <div class="dalwa-input-wrapper">
                            <i class="fa-solid fa-user dalwa-input-icon"></i>
                            <input type="text" 
                                   id="login-username"
                                   required 
                                   name="username" 
                                   class="form-control dalwa-modern-input" 
                                   placeholder="Masukkan username" 
                                   autocomplete="username" />
                        </div>
                    </div>

                    <!-- Password Input with Show/Hide Toggle -->
                    <div class="mb-3">
                        <label class="dalwa-input-label" for="login-password">Password</label>
                        <div class="dalwa-input-wrapper">
                            <i class="fa-solid fa-lock dalwa-input-icon"></i>
                            <input type="password" 
                                   id="login-password"
                                   required 
                                   name="password" 
                                   class="form-control dalwa-modern-input" 
                                   placeholder="••••••••••••••••" 
                                   autocomplete="current-password" />
                            <button type="button" class="dalwa-pwd-toggle-btn" id="togglePasswordBtn" title="Tampilkan / Sembunyikan Password" tabindex="-1">
                                <i class="fa-solid fa-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me Checkbox & Back to Home -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check custom-check">
                            <input class="form-check-input" type="checkbox" name="checkbox" id="remember-me" />
                            <label class="form-check-label small fw-600" for="remember-me">
                                Ingat Saya
                            </label>
                        </div>
                        <a href="{{ route('root.index') }}" class="dalwa-back-link">
                            <i class="fa-solid fa-arrow-left me-1"></i> Beranda
                        </a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn dalwa-btn-submit w-100" name="login">
                        <i class="fa-solid fa-arrow-right-to-bracket me-2"></i> Masuk Sekarang
                    </button>

                    <!-- Footer Copyright inside Form Panel (Changed 2020-2026 to 2026) -->
                    <div class="dalwa-login-footer text-center mt-3 pt-2">
                        <div class="dalwa-footer-copy">Copyright &copy; UII DALWA; 2026.</div>
                        <div class="dalwa-footer-dev">Portal Presensi Banat</div>
                    </div>
                </form>
            </div>

        </div>
    </section>
@endsection

@push('scripts')
    <!-- Three.js & GLTFLoader for 3D Biometric Machine in Login Left Panel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>

    <script>
        // ======================================================================
        // 3D FINGERSPOT MACHINE VIEWER FOR LOGIN LEFT PANEL
        // ======================================================================
        (function initLogin3D() {
            const wrapper = document.getElementById('login3dWrapper');
            const canvas = document.getElementById('login3dCanvas');
            if (!wrapper || !canvas || typeof THREE === 'undefined') return;

            const width = wrapper.clientWidth || 340;
            const height = wrapper.clientHeight || 160;

            const scene = new THREE.Scene();
            const camera = new THREE.PerspectiveCamera(40, width / height, 0.1, 100);
            camera.position.set(0, 0.1, 3.4);

            const renderer = new THREE.WebGLRenderer({
                canvas: canvas,
                alpha: true,
                antialias: true
            });
            renderer.setSize(width, height);
            renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

            // Studio Lights matching Banat theme
            const ambientLight = new THREE.AmbientLight(0xffffff, 0.65);
            scene.add(ambientLight);

            const mainLight = new THREE.DirectionalLight(0xffffff, 0.95);
            mainLight.position.set(3, 4, 4);
            scene.add(mainLight);

            const fillLight = new THREE.DirectionalLight(0xfff1f2, 0.4);
            fillLight.position.set(-3, 2, 3);
            scene.add(fillLight);

            const pinkRimLight = new THREE.PointLight(0xfb7185, 2.2, 8);
            pinkRimLight.position.set(-2, 2, 2);
            scene.add(pinkRimLight);

            const cyanFillLight = new THREE.PointLight(0x38bdf8, 1.4, 8);
            cyanFillLight.position.set(2, -1.5, 2);
            scene.add(cyanFillLight);

            // Glowing platform ring
            const ringGeo = new THREE.RingGeometry(0.8, 0.86, 48);
            const ringMat = new THREE.MeshBasicMaterial({ 
                color: 0xfb7185, 
                side: THREE.DoubleSide, 
                transparent: true, 
                opacity: 0.5 
            });
            const ring = new THREE.Mesh(ringGeo, ringMat);
            ring.rotation.x = Math.PI / 2;
            ring.position.y = -0.65;

            const modelGroup = new THREE.Group();
            modelGroup.add(ring);
            scene.add(modelGroup);

            let model = null;
            let isDragging = false;
            let previousMousePosition = { x: 0, y: 0 };
            let dragVelocity = { x: 0, y: 0 };

            // Load 3D Model
            if (typeof THREE.GLTFLoader !== 'undefined') {
                const loader = new THREE.GLTFLoader();
                const modelUrl = "{{ asset('finger3d.glb') }}";
                loader.load(
                    modelUrl,
                    function(gltf) {
                        model = gltf.scene;
                        const box = new THREE.Box3().setFromObject(model);
                        const center = box.getCenter(new THREE.Vector3());
                        const size = box.getSize(new THREE.Vector3());
                        const maxDim = Math.max(size.x, size.y, size.z);
                        const baseScale = 1.6 / maxDim;

                        model.scale.set(baseScale, baseScale, baseScale);
                        model.position.sub(center.multiplyScalar(baseScale));
                        model.position.y += 0.02;

                        modelGroup.add(model);
                    },
                    undefined,
                    function(err) {
                        console.warn('3D Login error:', err);
                    }
                );
            }

            // Drag Interaction on Wrapper
            wrapper.addEventListener('mousedown', function(e) {
                isDragging = true;
                previousMousePosition = { x: e.clientX, y: e.clientY };
            });

            wrapper.addEventListener('touchstart', function(e) {
                if (e.touches.length === 1) {
                    isDragging = true;
                    previousMousePosition = { x: e.touches[0].clientX, y: e.touches[0].clientY };
                }
            }, { passive: true });

            window.addEventListener('mousemove', function(e) {
                if (!isDragging) return;
                const deltaX = e.clientX - previousMousePosition.x;
                const deltaY = e.clientY - previousMousePosition.y;
                dragVelocity.x = deltaX * 0.008;
                dragVelocity.y = deltaY * 0.005;
                modelGroup.rotation.y += dragVelocity.x;
                modelGroup.rotation.x = Math.max(-0.4, Math.min(0.4, modelGroup.rotation.x + dragVelocity.y));
                previousMousePosition = { x: e.clientX, y: e.clientY };
            });

            window.addEventListener('touchmove', function(e) {
                if (!isDragging || e.touches.length !== 1) return;
                const deltaX = e.touches[0].clientX - previousMousePosition.x;
                const deltaY = e.touches[0].clientY - previousMousePosition.y;
                dragVelocity.x = deltaX * 0.008;
                dragVelocity.y = deltaY * 0.005;
                modelGroup.rotation.y += dragVelocity.x;
                modelGroup.rotation.x = Math.max(-0.4, Math.min(0.4, modelGroup.rotation.x + dragVelocity.y));
                previousMousePosition = { x: e.touches[0].clientX, y: e.touches[0].clientY };
            });

            window.addEventListener('mouseup', function() { isDragging = false; });
            window.addEventListener('touchend', function() { isDragging = false; });

            // Resize observer for container
            const resizeObserver = new ResizeObserver(() => {
                const w = wrapper.clientWidth;
                const h = wrapper.clientHeight;
                if (w > 0 && h > 0) {
                    camera.aspect = w / h;
                    camera.updateProjectionMatrix();
                    renderer.setSize(w, h);
                }
            });
            resizeObserver.observe(wrapper);

            // Animation Loop
            let clock = new THREE.Clock();
            function animate() {
                requestAnimationFrame(animate);
                const elapsedTime = clock.getElapsedTime();

                if (!isDragging) {
                    modelGroup.rotation.y += 0.008;
                    modelGroup.rotation.x += (0 - modelGroup.rotation.x) * 0.05;
                }

                modelGroup.position.y = Math.sin(elapsedTime * 1.8) * 0.04;
                ring.rotation.z += 0.005;

                renderer.render(scene, camera);
            }
            animate();
        })();

        // Password Show/Hide Toggle
        $(document).ready(function() {
            const toggleBtn = document.getElementById('togglePasswordBtn');
            const passwordInput = document.getElementById('login-password');
            const toggleIcon = document.getElementById('togglePasswordIcon');

            if (toggleBtn && passwordInput && toggleIcon) {
                toggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    if (isPassword) {
                        toggleIcon.classList.remove('fa-eye');
                        toggleIcon.classList.add('fa-eye-slash');
                    } else {
                        toggleIcon.classList.remove('fa-eye-slash');
                        toggleIcon.classList.add('fa-eye');
                    }
                });
            }

            // Remember Me Cookie Handler
            setTimeout(function() {
                if (typeof Cookies !== 'undefined') {
                    $('#form-login [name="username"]').val(Cookies.get('username'));
                    $('#form-login [name="password"]').val(Cookies.get('password'));
        
                    if (Cookies.get('username') && Cookies.get('password')) {
                        $('#remember-me').prop('checked', true);
                    }
                }
            }, 800);
        });

        $('#form-login').submit(function(e) {
            if (typeof Cookies !== 'undefined') {
                if ($('#remember-me').is(":checked")) {
                    Cookies.set('username', $('#form-login [name="username"]').val());
                    Cookies.set('password', $('#form-login [name="password"]').val());
                } else {
                    Cookies.remove('username');
                    Cookies.remove('password');
                }
            }
        });
    </script>
@endpush
