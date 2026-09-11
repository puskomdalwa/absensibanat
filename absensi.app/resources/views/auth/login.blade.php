@extends('layouts.home.template')
@section('title', 'Login | Absensi Banat UII Dalwa')
@section('content')
    <section class="banat-ambient-bg py-5 min-vh-100 d-flex align-items-center position-relative">
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
