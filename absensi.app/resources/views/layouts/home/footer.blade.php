<!-- Footer -->
<footer class="main">
    <section class="section-padding footer-mid">
        <div class="container pt-15 pb-20">
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="widget-about font-md mb-md-5 mb-lg-0">
                        <div class="logo logo-width-1 wow fadeIn animated">
                            <a href="{{ route('root.index') }}"><img src="{{ asset('home/assets/imgs/theme/logo.png') }}"
                                    alt="logo" /></a>
                        </div>
                        <h5 class="mt-20 mb-10 fw-600 text-grey-4 wow fadeIn animated">
                            Kontak
                        </h5>
                        <p class="wow fadeIn animated">
                            <strong>Alamat: </strong>Bangil, Pasuruan, Indonesia
                        </p>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3">
                    <h5 class="widget-title wow fadeIn animated">
                        Menu
                    </h5>
                    <ul class="footer-list wow fadeIn animated mb-sm-5 mb-md-0">
                        <li><a href="#">Home</a></li>
                        <li><a href="#">Absensi</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-3">
                    <h5 class="widget-title wow fadeIn animated">
                        Masuk
                    </h5>
                    <ul class="footer-list wow fadeIn animated">
                        <li><a href="{{ route("login") }}">Login</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-3">
                    <h5 class="widget-title wow fadeIn animated">
                        Informasi
                    </h5>
                    <ul class="footer-list wow fadeIn animated">
                        <li><a href="https://uiidalwa.ac.id">UII Dalwa</a></li>
                        <li><a href="https://play.google.com/store/apps/details?id=com.uiidalwa.absensi">Download Aplikasi</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <div class="container pb-20 wow fadeIn animated">
        <div class="row">
            <div class="col-12 mb-20">
                <div class="footer-bottom"></div>
            </div>
            <div class="col-lg-6">
                <p class="float-md-left font-sm text-muted mb-0">
                    &copy; {{ date('Y') }},
                    <strong class="text-brand">UII Dalwa</strong> - Website Absensi
                </p>
            </div>
            <div class="col-lg-6">
                <p class="text-lg-end text-start font-sm text-muted mb-0">
                    Designed by
                    <a href="{{ route('root.index') }}" target="_blank">Absensi UII Dalwa</a>. All rights
                    reserved
                </p>
            </div>
        </div>
    </div>
</footer>
<!-- End Footer -->
