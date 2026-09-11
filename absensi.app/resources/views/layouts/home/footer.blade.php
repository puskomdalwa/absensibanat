<!-- Footer -->
<footer class="py-5" style="background: linear-gradient(180deg, #1e1b2e 0%, #171424 100%); color: rgba(255, 255, 255, 0.85); border-top: 2px solid var(--banat-primary);">
    <div class="container py-4">
        <div class="row g-4">
            <div class="col-lg-5 col-md-6">
                <div class="pe-lg-4">
                    <a href="{{ route('root.index') }}" class="d-inline-block mb-3">
                        <img src="{{ asset('home/assets/imgs/theme/logo.png') }}" alt="UII Dalwa Logo" style="max-height: 60px; filter: brightness(1.1);" />
                    </a>
                    <p class="small mb-3" style="color: rgba(255, 255, 255, 0.7); max-width: 400px; line-height: 1.6;">
                        Portal Resmi Rekapitulasi Presensi Banat UII Dalwa Bangil Pasuruan. Menghadirkan sistem absensi realtime yang presisi, modern, dan terpercaya.
                    </p>
                    <div class="d-flex align-items-center gap-2 text-white small">
                        <i class="fa-solid fa-location-dot" style="color: var(--banat-primary);"></i>
                        <span>Bangil, Pasuruan, Jawa Timur, Indonesia</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <h6 class="fw-700 text-white mb-3 text-uppercase small" style="letter-spacing: 0.8px;">Navigasi Utama</h6>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
                    <li><a href="{{ route('root.index') }}" class="text-white-50 text-decoration-none hover-white"><i class="fa-solid fa-chevron-right me-1 small" style="color: var(--banat-primary);"></i> Beranda</a></li>
                    <li><a href="{{ route('absensi.index') }}" class="text-white-50 text-decoration-none hover-white"><i class="fa-solid fa-chevron-right me-1 small" style="color: var(--banat-primary);"></i> Cari Absensi</a></li>
                    <li><a href="{{ route('realtime.index') }}" class="text-white-50 text-decoration-none hover-white"><i class="fa-solid fa-chevron-right me-1 small" style="color: var(--banat-primary);"></i> Absensi Realtime</a></li>
                    <li><a href="{{ route('laporan.index') }}" class="text-white-50 text-decoration-none hover-white"><i class="fa-solid fa-chevron-right me-1 small" style="color: var(--banat-primary);"></i> Laporan</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <h6 class="fw-700 text-white mb-3 text-uppercase small" style="letter-spacing: 0.8px;">Akses Sistem</h6>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
                    <li><a href="{{ route('login') }}" class="text-white-50 text-decoration-none hover-white"><i class="fa-solid fa-chevron-right me-1 small" style="color: var(--banat-primary);"></i> Login Banat</a></li>
                    <li><a href="{{ route('dashboard.index') }}" class="text-white-50 text-decoration-none hover-white"><i class="fa-solid fa-chevron-right me-1 small" style="color: var(--banat-primary);"></i> Dashboard</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h6 class="fw-700 text-white mb-3 text-uppercase small" style="letter-spacing: 0.8px;">Informasi Kampus</h6>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
                    <li><a href="https://uiidalwa.ac.id" target="_blank" class="text-white-50 text-decoration-none hover-white"><i class="fa-solid fa-globe me-1" style="color: var(--banat-primary);"></i> Website UII Dalwa</a></li>
                    <li><a href="https://play.google.com/store/apps/details?id=com.uiidalwa.absensi" target="_blank" class="text-white-50 text-decoration-none hover-white"><i class="fa-brands fa-google-play me-1" style="color: var(--banat-primary);"></i> Download App Android</a></li>
                </ul>
            </div>
        </div>

        <div class="row pt-4 mt-4 border-top" style="border-color: rgba(255, 255, 255, 0.1) !important;">
            <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                <p class="small text-white-50 mb-0">
                    &copy; {{ date('Y') }} <strong style="color: #fb7185;">UII Dalwa</strong> — Absensi Banat. All rights reserved.
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="small text-white-50 mb-0">
                    Designed for <span style="color: #fb7185;">Mahasiswi & Civitas Banat UII Dalwa</span>
                </p>
            </div>
        </div>
    </div>
</footer>
<!-- End Footer -->
