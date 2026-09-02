@extends('layouts.home.template')
@section('title', 'Detail | Absensi UII Dalwa')
@push('css')
<style>
    .dt-layout-full {
        padding: 0 !important;
    }

    @media only screen and (max-width: 480px) {
        .table td {
            display: table-cell !important;
            width: 100%;
            text-align: center;
        }
    }

</style>
@endpush
@section('content')
<div class="page-header breadcrumb-wrap">
    <div class="container">
        <div class="breadcrumb">
            <a href="{{ route('root.index') }}" rel="nofollow">Home</a>
            <span></span> Absensi <span></span> Detail
        </div>
    </div>
</div>
<section class="mt-50 mb-50">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="product-detail accordion-detail">
                    <div class="row mb-50">
                        <div class="col-md-6 col-sm-12 col-xs-12">
                            <div class="detail-gallery d-flex justify-content-center bg-brand h-100 border-radius-10">
                                <img src="{{ $user->photo ? asset('photo') . '/'.$user->photo : asset('home/assets/imgs/theme/user.png') }}" alt="User Profile" />
                            </div>
                            <!-- End Gallery -->
                        </div>
                        <div class="col-md-6 col-sm-12 col-xs-12">
                            <div class="detail-info mt-3 mt-md-0">
                                <h2 class="title-detail">
                                    {{ $user->name }}
                                </h2>
                                <div class="product-detail-rating">
                                    <div class="pro-details-brand">
                                        <span>
                                            Departemen:
                                            <a href="products.html">{{ $user->departemen->nama }}</a></span>
                                    </div>
                                    <div class="product-rate-cover text-end">
                                        <span class="font-small ml-5 text-muted">
                                            {{ $user->absensi->count() }} Absensi</span>
                                    </div>
                                </div>
                                <div class="bt-1 border-color-1 mt-15 mb-15"></div>
                                <div class="short-desc mb-30 px-4">
                                    <p class="text-bold">
                                        <li>ID : {{ $user->id }}</li>
                                        <li>Nama : {{ $user->name }}</li>
                                        <li>Departemen : {{ $user->departemen->nama }}</li>
                                        <li>Jenis Kelamin : {{ $user->jenis_kelamin }}</li>
                                    </p>
                                </div>

                                <ul class="product-meta font-xs color-grey mt-50">
                                    <li class="mb-5">Departemen: <a href="#">{{ $user->departemen->nama }}</a>
                                    </li>
                                    <li class="mb-5">
                                        Role: <a href="#" rel="tag">{{ $user->role->akses }}</a>,
                                    </li>
                                </ul>
                            </div>
                            <!-- Detail Info -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 m-auto entry-main-content">
                            <h2 class="section-title style-1 mb-30">Absensi</h2>
                            <div class="mb-3">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="startDate" class="form-label">Tanggal Mulai</label>
                                        <input type="date" class="form-control" id="startDate" name="startDate">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="endDate" class="form-label">Tanggal Akhir</label>
                                        <input type="date" class="form-control" id="endDate" name="endDate">
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button type="button" id="filterButton" class="btn btn-primary w-100">Filter</button>
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <button type="button" id="resetButton" class="btn btn-secondary w-100">Tampilkan
                                            Semua Tanggal</button>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="example" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No.</th>
                                            <th class="text-center">Tanggal</th>
                                            <th class="text-center">Jam Datang</th>
                                            <th class="text-center">Jam Pulang</th>
                                            <th class="text-center">Status Isi Keterangan</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th class="text-center" style="width: 5px">No.</th>
                                            <th class="text-center">Tanggal</th>
                                            <th class="text-center">Jam</th>
                                            <th class="text-center">Status Isi Keterangan</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="social-icons single-share">
                            <ul class="text-grey-5 d-inline-block">
                                <li><strong class="mr-10">Bagikan :</strong></li>
                                <li>
                                    <a href="#" id="copyButton" class="hover-up" data-bs-toggle="tooltip" data-bs-placement="top" title="Copy">
                                        <i class="fi-rs-copy"></i>
                                    </a>
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</section>

<!-- Modal -->
<div class="modal fade" id="modal-keterangan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title_edit">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ol id="list-keterangan" class="list-group list-group-numbered">

                </ol>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

    document.addEventListener("DOMContentLoaded", function() {
        let copyButton = document.getElementById("copyButton");

        // Inisialisasi Tooltip Bootstrap
        var tooltip = new bootstrap.Tooltip(copyButton);

        copyButton.addEventListener("click", function(e) {
            e.preventDefault(); // Mencegah navigasi default
            let currentURL = "{{ route('absensi.show', ['user' => $user]) }}"; // Ambil URL saat ini

            navigator.clipboard.writeText(currentURL).then(function() {
                copyButton.setAttribute("title", "Copied!");
                tooltip.dispose(); // Hapus tooltip lama
                tooltip = new bootstrap.Tooltip(copyButton); // Buat tooltip baru
                tooltip.show(); // Tampilkan tooltip

                setTimeout(() => {
                    copyButton.setAttribute("title", "Copy URL");
                    tooltip.dispose();
                    tooltip = new bootstrap.Tooltip(copyButton);
                }, 1500);
            });
        });

        let dataTable = $("#example").DataTable({
            autoWidth: true
            , processing: true
            , serverSide: true
            , search: {
                return: true
            , }
            , ajax: {
                url: "{{ route('absensi.data', ['user' => $user]) }}"
                , method: "GET"
                , data: function(d) {
                    d.startDate = $("#startDate").val();
                    d.endDate = $("#endDate").val();
                }
            , }
            , columns: [{
                    class: "text-center"
                    , data: "tgl_absen"
                    , render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                , }, {
                    class: "text-center"
                    , data: "tgl_absen"
                    , name: "tgl_absen"
                , }
                , {
                    class: "text-center"
                    , data: "pagi"
                    , name: "pagi"
                , }
                , {
                    class: "text-center"
                    , data: "sore"
                    , name: "sore"
                , }
                , {
                    class: "text-center"
                    , data: "has_keterangan"
                    , name: "has_keterangan"
                , }
                , {
                    data: "action"
                    , name: "action"
                    , class: "text-center"
                    , searchable: false
                    , orderable: false
                , }
            , ]
            , order: [
                [0, "desc"]
            ]
        , });

        $('#startDate').change(function(e) {
            e.preventDefault();
            dataTable.ajax.reload(null, false);
        });

        $('#endDate').change(function(e) {
            e.preventDefault();
            dataTable.ajax.reload(null, false);
        });

        $('#resetButton').click(function(e) {
            e.preventDefault();
            $('#startDate').val('');
            $('#endDate').val('');
            dataTable.ajax.reload(null, false);
        });

        $('#filterButton').click(function(e) {
            e.preventDefault();
            dataTable.ajax.reload(null, false);
        });

        $('#modal-keterangan').on('show.bs.modal', function(event) {
            $('#keterangan_edit').focus();
            var button = $(event.relatedTarget);

            var modal = $(this);
            modal.find('#title_edit').html(button.data('tgl_absen') + ' (' + button.data('pagi') + ')');
            modal.find('#id_edit').val(button.data('id'));
            modal.find('#keterangan_edit').val('');

            modal.find('#list-keterangan').html('Loading...');

            loadKeterangan(button.data('id'));
        })

        function loadKeterangan(absensiId) {
            let route = "{{ route('absensi.keterangan', ['user' => $user, 'absensi' => ':id']) }}";
            route = route.replace(':id', absensiId);
            $.get(route)
                .done(function(response) {
                    if (response.length <= 0) {
                        $('#list-keterangan').html('Tidak ada data keterangan');
                        return;
                    }
                    let content = ``;
                    response.forEach(element => {
                        content += `
                                <li class="mb-1">
                                    <div 
                                        style="cursor: pointer"
                                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-start">
                                        <div class="ms-2 me-auto">
                                            <div class="fw-bold">${element.waktu}</div>${element.keterangan}
                                        </div>
                                    </div>
                                    <div id="edit-keterangan-${element.id}"></div>
                                </li>
                                `;
                    });
                    $('#list-keterangan').html(content);
                })
                .fail(function(xhr) {
                    console.log(xhr);
                    $('#list-keterangan').html('Error');
                });
        }
    });

</script>
@endpush
