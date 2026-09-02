@extends('layouts.home.template')
@section('title', 'Dashboard | Absensi UII Dalwa')
@push('css')
<style>
    .dt-layout-full {
        padding: 0 !important;
    }

    #modalKeteranganBody {
        word-break: break-word;
        white-space: pre-wrap;
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
            <span></span> Dashboard
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
                                        <li>ID : {{ $user->id }} </li>
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
                                <table id="table" class="table table-striped table-hover">
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
                                            <th class="text-center">Jam Datang</th>
                                            <th class="text-center">Jam Pulang</th>
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
                <form id="form-keterangan" method="POST" action="{{ route('dashboard.store') }}">
                    @csrf
                    <input type="hidden" name="absensi_id" id="id_edit">
                    <div class="">
                        <label for="waktu" class="col-form-label">Waktu:</label>
                        <input type="time" class="form-control" id="waktu_edit" name="waktu" required placeholder="Masukkan waktu kehadiran" />
                    </div>
                    <div class="">
                        <label for="keterangan" class="col-form-label">Keterangan:</label>
                        <textarea required type="text" class="form-control" id="keterangan_edit" style="min-height: 75px;" name="keterangan" rows="6" placeholder="Masukkan keterangan kehadiran, seperti alasan ketidakhadiran atau catatan penting lainnya."></textarea>
                    </div>
                    <div class="alert alert-info mt-2" role="alert">
                        Mohon isi keterangan dengan jelas agar dapat membantu kami memahami segala kondisi.
                    </div>
                    <div class="d-flex justify-content-end my-3">
                        <button type="submit" class="btn btn-primary w-100">Simpan</button>
                    </div>
                </form>

                <ol id="list-keterangan" class="list-group list-group-numbered">

                </ol>
                <div class="alert alert-info mt-2" role="alert">
                    Klik list keterangan di atas untuk mengedit keterangan.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="keteranganModal" tabindex="-1" aria-labelledby="keteranganModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="keteranganModalLabel">Keterangan Lengkap</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" id="modalKeteranganBody">
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

    $(document).on('click', '.baca-selengkapnya', function(e) {
        e.preventDefault();

        // Ambil keterangan lengkap dari data-keterangan
        var fullText = $(this).data('keterangan');

        console.log(fullText);

        // Isi konten modal
        $('#modalKeteranganBody').text(fullText);
    });

    let dataTable = $("#table").DataTable({
        autoWidth: true
        , processing: true
        , serverSide: true
        , search: {
            return: true
        , }
        , ajax: {
            url: "{{ route('dashboard.data', ['user' => $user]) }}"
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

    $('#form-keterangan').submit(function(e) {
        e.preventDefault();
        let absensiId = $('#form-keterangan input[name=absensi_id]').val();
        $.ajax({
            type: "POST"
            , url: $(this).attr('action')
            , data: new FormData(this)
            , contentType: false
            , processData: false
            , beforeSend: function() {
                $('#form-keterangan button[type=submit]').attr('disabled', true);
                $('#form-keterangan button[type=submit]').html('Proses...');
            }
            , success: function(response) {
                console.log(response);
                dataTable.ajax.reload(null, false);
                showToastr(response.type, response.type, response.message);
                loadKeterangan(absensiId);
            }
            , complete: function() {
                $('#form-keterangan button[type=submit]').attr('disabled', false);
                $('#form-keterangan button[type=submit]').html('Simpan');
            }
        , });
    });

    function loadKeterangan(absensiId) {
        let route = "{{ route('dashboard.keterangan', ['absensi' => ':id']) }}";
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
                                    <div onclick="showEditKeterangan(this)" 
                                        data-absensi_id=${absensiId}
                                        data-id="${element.id}" 
                                        data-waktu="${element.waktu}"
                                        data-keterangan="${element.keterangan}"
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

    function simpanEditKeterangan(event, element) {
        event.preventDefault();
        let id = $(element).find("input[name=id]").val();
        let absensi_id = $(element).find("input[name=absensi_id]").val();
        $.ajax({
            type: "POST"
            , url: $(this).attr('action')
            , data: new FormData(element)
            , contentType: false
            , processData: false
            , beforeSend: function() {
                $(element).find("button[type=submit]").attr('disabled', true);
                $(element).find("button[type=submit]").html('Proses...');
            }
            , success: function(response) {
                dataTable.ajax.reload(null, false);
                showToastr(response.type, response.type, response.message);
                loadKeterangan(absensi_id);
                batalkanEditKeterangan(id);
            }
            , complete: function() {
                $(element).find("button[type=submit]").html('Simpan Edit');
                $(element).find("button[type=submit]").attr('disabled', false);
            }
        , });
    }

    function showEditKeterangan(event) {
        let absensi_id = $(event).data("absensi_id");
        let id = $(event).data("id");
        let waktu = $(event).data("waktu");
        let keterangan = $(event).data("keterangan");

        if ($('#edit-keterangan-' + id + ' form').length) {
            batalkanEditKeterangan(id);
            return;
        }

        let content = `
            <form action="{{ route('dashboard.update') }}" onsubmit="simpanEditKeterangan(event,this)">
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="absensi_id" value="${absensi_id}">
                <input type="hidden" name="id" value="${id}">
                <div class="my-1">
                    <input type="time" class="form-control" name="waktu"
                        placeholder="Masukkan waktu kehadiran" value="${waktu}" />
                </div>
                <div class="mb-1">
                    <textarea type="text" class="form-control" name="keterangan" style="min-height: 150px;"
                        placeholder="Masukkan keterangan kehadiran, seperti alasan ketidakhadiran atau catatan penting lainnya." >${keterangan}</textarea>
                </div>
                <div class="d-flex flex-wrap gap-2 justify-content-end mb-3">
                    <button onclick="deleteKeterangan('#edit-keterangan-${id} form')" type="button" class="btn btn-danger bg-danger border-0 flex-grow-1">Hapus</button>
                    <button onclick="batalkanEditKeterangan(${id})" type="button" class="btn btn-warning bg-warning border-0 flex-grow-1">Batalkan Edit</button>
                    <button type="submit" class="btn btn-primary flex-grow-1">Simpan Edit</button>
                </div>

            </form>
            `;
        $('#edit-keterangan-' + id).html(content);
    }

    function deleteKeterangan(element) {
        let id = $(element).find("input[name=id]").val();
        let absensi_id = $(element).find("input[name=absensi_id]").val();

        $.ajax({
            type: "GET"
            , url: "{{ route('dashboard.delete') }}"
            , data: {
                _token: "{{ csrf_token() }}",
                // _method: "DELETE", // Laravel akan mengenali sebagai DELETE
                id: id
            }
            , beforeSend: function() {
                $(element).find("button[type=submit]").attr('disabled', true);
                $(element).find("button[type=submit]").html('Proses...');
            }
            , success: function(response) {
                console.log(response);
                dataTable.ajax.reload(null, false);
                showToastr(response.type, response.type, response.message);
                loadKeterangan(absensi_id);
            }
            , complete: function() {
                $(element).find("button[type=submit]").html('Simpan Edit');
                $(element).find("button[type=submit]").attr('disabled', false);
            }
        , });
    }

    function batalkanEditKeterangan(id) {
        $('#edit-keterangan-' + id).empty();
    }

</script>
@endpush
