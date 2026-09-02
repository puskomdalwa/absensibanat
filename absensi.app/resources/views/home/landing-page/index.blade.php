@extends('layouts.home.template')
@section('title', 'Absensi UII Dalwa')
@section('content')
    <section class="home-slider position-relative pt-50">
        <div class="hero-slider-1 dot-style-1 dot-style-1-position-1">
            <div class="single-hero-slider single-animation-wrap">
                <div class="container">
                    <div class="row align-items-center slider-animated-1">
                        <div class="col-lg-5 col-md-6">
                            <div class="hero-slider-content-2">
                                <h4 class="animated">
                                    Selamat Datang di
                                </h4>
                                <h2 class="animated fw-900">
                                    Website Absensi Banat
                                </h2>
                                <h1 class="animated fw-900 text-brand">
                                    UII Dalwa
                                </h1>
                                <p class="animated">
                                    Berisi informasi absensi seluruh
                                    civitas akademik UII Dalwa
                                </p>
                                @if (Auth::check())
                                    <a class="animated btn btn-brush btn-brush-3" href="{{ route('dashboard.index') }}">
                                        Dashboard
                                    </a>
                                @else
                                    <a class="animated btn btn-brush btn-brush-3" href="{{ route('login') }}">
                                        Login
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-7 col-md-6">
                            <div class="single-slider-img single-slider-img-1">
                                <img class="animated slider-1-1" src="{{ asset('home/assets/imgs/slider/slider-1.png') }}"
                                    alt="" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="single-hero-slider single-animation-wrap">
                <div class="container">
                    <div class="row align-items-center slider-animated-1">
                        <div class="col-lg-5 col-md-6">
                            <div class="hero-slider-content-2">
                                <h4 class="animated">
                                    Selamat Datang di
                                </h4>
                                <h2 class="animated fw-900">
                                    Website Absensi
                                </h2>
                                <h1 class="animated fw-900 text-brand">
                                    UII Dalwa
                                </h1>
                                <p class="animated">
                                    Berisi informasi absensi seluruh
                                    civitas akademik UII Dalwa
                                </p>
                                <a class="animated btn btn-brush btn-brush-3" href="{{ route('absensi.index') }}">
                                    Cari Absensi
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-7 col-md-6">
                            <div class="single-slider-img single-slider-img-1">
                                <img class="animated slider-1-3" src="{{ asset('home/assets/imgs/slider/slider-2.png') }}"
                                    alt="" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="slider-arrow hero-slider-1-arrow"></div>
    </section>
    <section class="product-tabs section-padding position-relative wow fadeIn animated">
        <div class="bg-square"></div>
        <div class="container">
            <div class="tab-header">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="nav-tab-one" data-bs-toggle="tab" data-bs-target="#tab-one"
                            type="button" role="tab" aria-controls="tab-one" aria-selected="true">
                            Dosen
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="nav-tab-three" data-bs-toggle="tab" data-bs-target="#tab-three"
                            type="button" role="tab" aria-controls="tab-three" aria-selected="false">
                            Staff
                        </button>
                    </li>
                </ul>
                <a href="{{ route('absensi.index') }}" class="view-more d-none d-md-flex">View More<i
                        class="fi-rs-angle-double-small-right"></i></a>
            </div>
            <div class="tab-content wow fadeIn animated" id="myTabContent">
                <div class="tab-pane fade show active" id="tab-one" role="tabpanel" aria-labelledby="tab-one">
                </div>
                <div class="tab-pane fade" id="tab-three" role="tabpanel" aria-labelledby="tab-three">
                </div>
            </div>
        </div>
    </section>
    <section class="banner-2 section-padding pb-0">
        <div class="container">
            <div class="banner-img banner-big wow fadeIn animated f-none">
                <img src="{{ asset('home/assets/imgs/banner/banner-4.png') }}" alt="" />
                <div class="banner-text d-md-block d-none">
                    <h4 class="mb-15 mt-40 text-brand">Aplikasi Absensi UII Dalwa</h4>
                    <h1 class="fw-600 mb-20">
                        Silahkan download dengan klik tombol di bawah ini <br />
                    </h1>
                    <a href="https://play.google.com/store/apps/details?id=com.uiidalwa.absensi" class="btn">Download <i class="fi-rs-arrow-right"></i></a>
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
