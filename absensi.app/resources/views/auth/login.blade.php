@extends('layouts.home.template')
@section('title', 'Login | Absensi Banat UII Dalwa')
@push('css')
<style>
/* ==========================================================================
   LOGIN PAGE LIGHT & DARK MODE INPUT AND CARD STYLING
   ========================================================================== */

/* Light Mode: Crisp & High Contrast */
.form-banat-control,
.form-control.form-banat-control,
.input-icon-group input.form-banat-control {
    background: #ffffff !important;
    border: 1.5px solid rgba(224, 82, 117, 0.22) !important;
    color: #1e1926 !important;
    -webkit-text-fill-color: #1e1926 !important;
    font-weight: 500 !important;
    border-radius: 14px !important;
    transition: all 0.25s ease !important;
}

.form-banat-control:focus,
.form-control.form-banat-control:focus,
.input-icon-group input.form-banat-control:focus {
    background: #ffffff !important;
    border-color: #e05275 !important;
    color: #1e1926 !important;
    -webkit-text-fill-color: #1e1926 !important;
    box-shadow: 0 0 0 4px rgba(224, 82, 117, 0.18) !important;
    outline: none !important;
}

.form-banat-control::placeholder {
    color: #94a3b8 !important;
    -webkit-text-fill-color: #94a3b8 !important;
}

/* Dark Mode: Luxury Obsidian & Glowing Rose */
[data-theme="dark"] .glass-card {
    background: rgba(22, 17, 33, 0.94) !important;
    border: 1px solid rgba(251, 113, 133, 0.25) !important;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.75) !important;
}

[data-theme="dark"] .glass-card h3 {
    color: #ffffff !important;
}

[data-theme="dark"] .glass-card .text-muted {
    color: #94a3b8 !important;
}

[data-theme="dark"] .form-label {
    color: #e2e8f0 !important;
}

/* Clearly visible typed text in Dark Mode */
[data-theme="dark"] .form-banat-control,
[data-theme="dark"] .form-control.form-banat-control,
[data-theme="dark"] .input-icon-group input.form-banat-control {
    background: #171124 !important;
    background-color: #171124 !important;
    border: 1.5px solid rgba(251, 113, 133, 0.35) !important;
    color: #ffffff !important;
    -webkit-text-fill-color: #ffffff !important;
    font-weight: 500 !important;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.45) !important;
}

[data-theme="dark"] .form-banat-control:focus,
[data-theme="dark"] .form-control.form-banat-control:focus,
[data-theme="dark"] .input-icon-group input.form-banat-control:focus {
    background: #1e1630 !important;
    background-color: #1e1630 !important;
    border-color: #fb7185 !important;
    color: #ffffff !important;
    -webkit-text-fill-color: #ffffff !important;
    box-shadow: 0 0 0 4px rgba(251, 113, 133, 0.28), inset 0 2px 4px rgba(0, 0, 0, 0.35) !important;
    outline: none !important;
}

/* Placeholder styling in Dark Mode */
[data-theme="dark"] .form-banat-control::placeholder,
[data-theme="dark"] .form-banat-control::-webkit-input-placeholder,
[data-theme="dark"] .form-banat-control::-moz-placeholder,
[data-theme="dark"] .form-banat-control:-ms-input-placeholder {
    color: rgba(226, 232, 240, 0.45) !important;
    -webkit-text-fill-color: rgba(226, 232, 240, 0.45) !important;
    opacity: 1 !important;
}

/* Chrome/Edge/Safari Autofill Dark Mode Override */
[data-theme="dark"] .form-banat-control:-webkit-autofill,
[data-theme="dark"] .form-banat-control:-webkit-autofill:hover, 
[data-theme="dark"] .form-banat-control:-webkit-autofill:focus,
[data-theme="dark"] .form-banat-control:-webkit-autofill:active {
    -webkit-text-fill-color: #ffffff !important;
    -webkit-box-shadow: 0 0 0px 1000px #171124 inset !important;
    box-shadow: 0 0 0px 1000px #171124 inset !important;
    transition: background-color 5000s ease-in-out 0s !important;
}

/* Icons inside input group in Dark Mode */
[data-theme="dark"] .input-icon-group i {
    color: #fb7185 !important;
}

/* Remember me checkbox and label */
[data-theme="dark"] .form-check-label {
    color: #cbd5e1 !important;
}

[data-theme="dark"] .form-check-input {
    background-color: #171124 !important;
    border-color: rgba(251, 113, 133, 0.4) !important;
}

[data-theme="dark"] .form-check-input:checked {
    background-color: #e11d48 !important;
    border-color: #e11d48 !important;
}

/* Back link */
[data-theme="dark"] .glass-card a[href*="root.index"] {
    color: #fb7185 !important;
}
[data-theme="dark"] .glass-card a[href*="root.index"]:hover {
    color: #fda4af !important;
}
</style>
@endpush

@section('content')
    <section class="banat-ambient-bg py-5 min-vh-100 d-flex align-items-center position-relative" style="padding-top: 110px !important;">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-5">
                    <div class="glass-card p-4 p-md-5 position-relative overflow-hidden">
                        <!-- Decorative top accent line -->
                        <div class="position-absolute top-0 start-0 end-0" style="height: 4px; background: var(--banat-rose-gradient);"></div>
                        
                        <div class="text-center mb-4">
                            <a href="{{ route('root.index') }}" class="d-inline-block mb-3">
                                <img src="{{ asset('home/assets/imgs/theme/logo.png') }}" alt="UII Dalwa Logo" style="max-height: 75px; object-fit: contain;" />
                            </a>
                            <h3 class="fw-800 mb-1" style="color: var(--banat-text-dark);">Portal Masuk Banat</h3>
                            <p class="text-muted small">Silakan masukkan kredensial akun Anda untuk mengakses sistem</p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger border-0 rounded-3 shadow-sm mb-4" style="background: #ffe4e6; color: #be123c;" role="alert">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
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
                            <div class="mb-3">
                                <label class="form-label small fw-700 text-uppercase" style="color: var(--banat-text-medium); letter-spacing: 0.5px;">Username</label>
                                <div class="input-icon-group">
                                    <i class="fa-solid fa-user"></i>
                                    <input type="text" 
                                           required 
                                           name="username" 
                                           class="form-control form-banat-control" 
                                           placeholder="Masukkan Username Anda" />
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-700 text-uppercase" style="color: var(--banat-text-medium); letter-spacing: 0.5px;">Password</label>
                                <div class="input-icon-group">
                                    <i class="fa-solid fa-lock"></i>
                                    <input type="password" 
                                           required 
                                           name="password" 
                                           class="form-control form-banat-control" 
                                           placeholder="••••••••" 
                                           autocomplete="off" />
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="checkbox" id="remember-me" />
                                    <label class="form-check-label small" for="remember-me" style="color: var(--banat-text-medium);">
                                        Ingat Saya
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn-banat-primary w-100 py-3 mb-3" name="login">
                                <i class="fa-solid fa-right-to-bracket me-2"></i> Masuk Sekarang
                            </button>

                            <div class="text-center mt-3 pt-3 border-top" style="border-color: var(--banat-border) !important;">
                                <a href="{{ route('root.index') }}" class="small text-decoration-none fw-600" style="color: var(--banat-primary);">
                                    <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Beranda Utama
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            setTimeout(function() {
                $('#form-login [name="username"]').val(Cookies.get('username'));
                $('#form-login [name="password"]').val(Cookies.get('password'));
    
                if (Cookies.get('username') && Cookies.get('password')) {
                    $('#remember-me').prop('checked', true);
                }
            }, 1000);
        });

        $('#form-login').submit(function(e) {
            if ($('#remember-me').is(":checked")) {
                Cookies.set('username', $('#form-login [name="username"]').val());
                Cookies.set('password', $('#form-login [name="password"]').val());
            }
        });
    </script>
@endpush
