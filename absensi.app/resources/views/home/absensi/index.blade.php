@extends('layouts.home.template')
@section('title', 'Absensi | Absensi UII Dalwa')
@section('content')
    <div class="page-header breadcrumb-wrap">
        <div class="container">
            <div class="breadcrumb">
                <a href="{{ route('root.index') }}" rel="nofollow">Home</a>
                <span></span> Absensi
            </div>
        </div>
    </div>

    <section class="mt-50 mb-50">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 position-relative" id="data">
                </div>
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
