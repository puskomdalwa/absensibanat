@extends('layouts.admin.template')
@section('title', 'Dashboard')
@section('content')
    <div class="row g-6">
        <!-- Menu -->
        <div class="col-lg-8 col-md-12">
            <div class="swiper-container swiper-container-horizontal swiper swiper-card-advance-bg"
                id="swiper-with-pagination-cards">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="row">
                            <div class="col-12">
                                <h5 class="text-white mb-0">Menu Absensi</h5>
                                <small>Silahkan klik menu di bawah ini.</small>
                            </div>
                            <div class="col-lg-7 col-md-9 col-12 order-2 order-md-1 pt-md-2">
                                <h6 class="text-white mt-0 mt-md-3 mb-4">Absensi</h6>
                                <div class="row">
                                    @foreach ($departemen as $item)
                                        <div class="col-6 g-1">
                                            <a href="{{ route('admin.absensi.index') }}/{{ $item->nama }}"
                                                class="btn btn-primary fw-medium flex-grow-1 website-analytics-text-bg w-100">{{ $item->nama }}</a>
                                        </div>
                                    @endforeach
                                </div>

                            </div>
                            <div class="col-lg-5 col-md-3 col-12 order-1 order-md-2 my-4 my-md-0 text-center">
                                <img src="{{ asset('admin') }}/assets/img/illustrations/card-website-analytics-2.png"
                                    alt="Website Analytics" height="150" class="card-website-analytics-img" />
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="row">
                            <div class="col-12">
                                <h5 class="text-white mb-0">Menu Admin</h5>
                                <small>Silahkan klik menu di bawah.</small>
                            </div>
                            <div class="row">
                                <div class="col-lg-7 col-md-9 col-12 order-2 order-md-1 pt-md-2">
                                    <h6 class="text-white mt-0 mt-md-3 mb-4">Data</h6>
                                    <div class="row">
                                        <div class="col-12">
                                            <ul class="list-unstyled mb-0">
                                                <li class="d-flex mb-4 align-items-center">
                                                    <a
                                                        class="mb-0 btn btn-primary fw-medium me-2 website-analytics-text-bg w-100">Role</a>
                                                </li>
                                                <li class="d-flex align-items-center">
                                                    <a
                                                        class="mb-0 btn btn-primary fw-medium me-2 website-analytics-text-bg w-100">Departemen</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-5 col-md-3 col-12 order-1 order-md-2 my-4 my-md-0 text-center">
                                    <img src="{{ asset('admin') }}/assets/img/illustrations/card-website-analytics-1.png"
                                        alt="Website Analytics" height="150" class="card-website-analytics-img" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="row">
                            <div class="col-12">
                                <h5 class="text-white mb-0">Menu Setting</h5>
                                <small>Silahkan klik menu di bawah ini.</small>
                            </div>
                            <div class="col-lg-7 col-md-9 col-12 order-2 order-md-1 pt-md-2">
                                <h6 class="text-white mt-0 mt-md-3 mb-4">Setting</h6>
                                <div class="row">
                                    <a href="{{ route('admin.profile.index') }}"
                                        class="btn btn-primary fw-medium flex-grow-1 website-analytics-text-bg w-100">Profile</a>
                                </div>

                            </div>
                            <div class="col-lg-5 col-md-3 col-12 order-1 order-md-2 my-4 my-md-0 text-center">
                                <img src="{{ asset('admin') }}/assets/img/illustrations/card-website-analytics-2.png"
                                    alt="Website Analytics" height="150" class="card-website-analytics-img" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
        <!--/ Menu -->

        <!-- Welcome -->
        <div class="col-lg-4 col-md-12">
            <div class="card">
                <div class="d-flex align-items-center g-5 row" style="height: 315px">
                    <div class="col-7">
                        <div class="card-body text-nowrap d-flex flex-column gap-4">
                            <h5 class="card-title mb-0">Selamat datang kembali!</h5>
                            <p class="text-primary mb-2">🎉🎉 {{ \Auth::user()->name }} 🎉🎉</p>
                            <p class="text-muted mb-0 text-wrap">Semoga harimu penuh keberkahan dan dimudahkan dalam setiap
                                langkah 🤲
                            </p>
                        </div>

                    </div>
                    <div class="col-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{ asset('admin') }}/assets/img/illustrations/card-advance-sale.png" height="140"
                                alt="view sales" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Welcome -->

        <!-- Statistics -->
        <div class="col-xl-12 col-md-12">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="card-title mb-0">Users</h5>
                    <small class="text-muted">Data Users</small>
                </div>
                <div class="card-body d-flex align-items-end">
                    <div class="w-100">
                        <div class="row gy-3">
                            @foreach ($departemen as $item)
                                <div class="col-md-3 col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="badge rounded bg-label-primary me-4 p-2">
                                            <i class="ti ti-user ti-lg"></i>
                                        </div>
                                        <div class="card-info">
                                            <h5 class="mb-0">{{ $item->user->count() }}</h5>
                                            <small>{{ $item->nama }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Statistics -->

        <div class="col-12">
            <div class="card" id="card-user">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatables-basic table table-hover" id="table-1">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>User</th>
                                <th>Jumlah Kehadiran</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection
@push('scripts')
    <script>
        const swiperWithPagination = document.querySelector('#swiper-with-pagination-cards');
        if (swiperWithPagination) {
            new Swiper(swiperWithPagination, {
                loop: true,
                autoplay: {
                    delay: 2500,
                    disableOnInteraction: false
                },
                pagination: {
                    clickable: true,
                    el: '.swiper-pagination'
                }
            });
        }

        var dataTable = initDataTables('table-1', 'loader-user', 'card-user', false, false,
            'Absensi', "{{ route('admin.dashboard.dataAbsensi') }}",
            [{
                    data: "name",
                    name: "name",
                    className: "align-middle",
                },
                {
                    data: "total_kehadiran",
                    name: "total_kehadiran",
                    className: "align-middle",
                },
            ],
        );
    </script>
@endpush
