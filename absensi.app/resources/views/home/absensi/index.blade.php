@extends('layouts.home.template')
@section('title', 'Absensi | Absensi UII Dalwa')
@section('content')
    <div class="page-header breadcrumb-wrap">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="breadcrumb mb-0">
                    <a href="{{ route('root.index') }}" rel="nofollow">
                        <i class="fa-solid fa-house-chimney me-1"></i> Home
                    </a>
                    <span></span> <span class="active" style="color: var(--banat-text-dark); font-weight: 600;">Direktori Civitas</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge" style="background: rgba(224, 82, 117, 0.12); color: var(--banat-primary); border: 1px solid var(--banat-border); border-radius: 30px; padding: 6px 14px; font-weight: 600; font-size: 0.8rem;">
                        <i class="fa-solid fa-users me-1"></i> Civitas Banat Dalwa
                    </span>
                </div>
            </div>
        </div>
    </div>

    <section class="py-4 py-md-5 position-relative" style="background: var(--banat-bg-soft); min-height: 80vh;">
        <div class="container">
            <!-- Overview Header Card -->
            <div class="banat-dashboard-card mb-4 p-4 p-md-5">
                <div class="row align-items-center g-3">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge-role">
                                <i class="fa-solid fa-users-gear me-1"></i> Direktori Civitas
                            </span>
                            <span class="badge-dept">
                                <i class="fa-solid fa-graduation-cap me-1"></i> Dosen & Staf Akademik
                            </span>
                        </div>
                        <h2 class="fw-800 mb-2 dashboard-user-name">
                            Pencarian & Rekap Presensi Civitas
                        </h2>
                        <p class="text-muted mb-0" style="max-width: 650px; font-size: 0.95rem; line-height: 1.6;">
                            Telusuri profil presensi dosen, ustadzah, dan staf Kampus Banat UII Dalwa. Klik kartu civitas untuk melihat catatan kehadiran harian secara mendalam.
                        </p>
                    </div>
                    @if (!\Auth::check() || \Auth::user()->isAdmin())
                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ url('/realtime') }}" class="btn-banat-outline py-2 px-3 text-decoration-none">
                            <i class="fa-solid fa-tower-broadcast me-1"></i> Lihat Absensi Realtime
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Dynamic AJAX Content -->
            <div class="position-relative" id="data">
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        let params = new URLSearchParams(window.location.search);

        function loadData(page) {
            let dataSort = $('#data-sort').val() ?? 'name';
            let dataShow = $('#data-show').val();

            $('#data').append(`
                <div class="loader-container">
                    <div class="loader-item"></div>
                </div>
            `);

            $.get("{{ route('absensi.index') }}", {
                    page: page,
                    sort: dataSort,
                    show: dataShow,
                    search: params.get('search'),
                    departemen_id: params.get('departemen_id')
                })
                .done(function(response) {
                    $('#data').empty();
                    $("#data").html(response);

                    updateUrl('page', page);

                    if (typeof dataSort !== 'undefined' && typeof dataShow !== 'undefined') {
                        $('#data-sort').val(dataSort);
                        $('#data-show').val(dataShow);
                    }
                })
                .always(function() {
                    $('#data .load').remove();
                });
        }

        function setFilterElements() {
            var departemenId = params.get('departemen_id');
            if (departemenId) {
                $('#desktop_departemen_id').val(departemenId).change();
                $('#mobile_departemen_id').val(departemenId);

                $(`#dep-${departemenId}`).prop('checked', true);
                $("#btn-filter").append(`
                    <span id="filter-checked"
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <i class="fi-rs-check"></i>
                    </span>
                `);
            }

            var search = params.get('search');
            if (search) {
                $('#desktop_search').val(search);
                $('#mobile_search').val(search);
            }
        }

        function updateUrl(type, value) {
            let url = new URL(window.location.href);
            url.searchParams.set(type, value);
            window.history.pushState({}, "", url);
        }

        $(document).ready(function() {
            setFilterElements();

            let currentPage = params.get("page") || 1;

            loadData(currentPage);

            $(document).on("click", ".pagination a", function(e) {
                e.preventDefault();
                let page = $(this).attr("href").split("page=")[1]; // Ambil nomor halaman
                loadData(page);
            });

            // $('input[name="mobile_departemen_id"]').on('change', function() {
            //     updateUrl('departemen_id', this.value);
            // });

        });
    </script>
@endpush
