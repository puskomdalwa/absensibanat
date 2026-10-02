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
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="breadcrumb mb-0">
                <a href="{{ route('root.index') }}" rel="nofollow">
                    <i class="fa-solid fa-house-chimney me-1"></i> Home
                </a>
                <span></span> <span class="active" style="color: var(--banat-text-dark); font-weight: 600;">Dashboard Civitas</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge" style="background: rgba(224, 82, 117, 0.12); color: var(--banat-primary); border: 1px solid var(--banat-border); border-radius: 30px; padding: 6px 14px; font-weight: 600; font-size: 0.8rem;">
                    <i class="fa-solid fa-circle text-success me-1" style="font-size: 8px;"></i> Presensi Online Dalwa
                </span>
            </div>
        </div>
    </div>
</div>

<section class="py-4 py-md-5 position-relative" style="background: var(--banat-bg-soft); min-height: 80vh;">
    <div class="container">
        <!-- 1. User Profile Glassmorphism Card -->
        <div class="banat-dashboard-card mb-4 p-4 p-md-5">
            <div class="row align-items-center g-4">
                <!-- Avatar Col -->
                <div class="col-12 col-md-auto text-center text-md-start">
                    <div class="dashboard-avatar-wrapper">
                        <div class="avatar-ring-wrapper">
                            <img src="{{ $user->photo ? asset('photo') . '/'.$user->photo : asset('home/assets/imgs/theme/user.png') }}" 
                                 alt="{{ $user->name }}" 
                                 class="dashboard-avatar-img rounded-circle" />
                        </div>
                        <span class="badge-status-dot" title="Status: Aktif"></span>
                    </div>
                </div>

                <!-- Info Col -->
                <div class="col-12 col-md text-center text-md-start">
                    <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-2 mb-2">
                        <h2 class="fw-800 mb-0 dashboard-user-name">{{ $user->name }}</h2>
                        <span class="badge badge-role">
                            <i class="fa-solid fa-shield-halved me-1"></i> {{ ucfirst($user->role->akses ?? 'User') }}
                        </span>
                        @if($user->departemen)
                            <span class="badge badge-dept">
                                <i class="fa-solid fa-building-user me-1"></i> {{ $user->departemen->nama }}
                            </span>
                        @endif
                    </div>

                    <!-- Meta Tags Row -->
                    <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-2 gap-sm-3 text-muted small mt-2">
                        <span style="color: var(--banat-text-medium);">
                            <i class="fa-solid fa-id-badge text-danger me-1"></i> ID: <strong>#{{ $user->id }}</strong>
                        </span>
                        <span class="d-none d-sm-inline" style="color: var(--banat-border);">•</span>
                        <span style="color: var(--banat-text-medium);">
                            <i class="fa-solid fa-venus-mars text-danger me-1"></i> {{ $user->jenis_kelamin ?? 'Laki-laki' }}
                        </span>
                        <span class="d-none d-sm-inline" style="color: var(--banat-border);">•</span>
                        <span style="color: var(--banat-text-medium);">
                            <i class="fa-solid fa-envelope text-danger me-1"></i> {{ $user->email ?? $user->username }}
                        </span>
                    </div>
                </div>

                <!-- Quick Stats & Share Col -->
                <div class="col-12 col-lg-auto text-center text-lg-end">
                    <div class="d-flex flex-row flex-lg-column align-items-center align-items-lg-end justify-content-center gap-3">
                        <div class="dashboard-stat-pill">
                            <div class="stat-pill-icon">
                                <i class="fa-solid fa-clipboard-check"></i>
                            </div>
                            <div class="text-start">
                                <span class="stat-pill-num fw-800 d-block">{{ $user->absensi->count() }}</span>
                                <span class="stat-pill-label">Total Presensi</span>
                            </div>
                        </div>

                        <button type="button" id="copyButton" class="btn-banat-outline btn-sm py-2 px-3" data-bs-toggle="tooltip" data-bs-placement="top" title="Salin Tautan Profil">
                            <i class="fa-solid fa-share-nodes me-1"></i> Bagikan Profil
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Filter Rentang Tanggal Card -->
        <div class="banat-dashboard-card mb-4 p-4">
            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <h5 class="fw-700 mb-0" style="color: var(--banat-text-dark);">
                    <i class="fa-solid fa-filter me-2" style="color: var(--banat-primary);"></i> Filter Rentang Tanggal
                </h5>
                <span class="text-muted small">Pilih rentang tanggal untuk menyaring data presensi</span>
            </div>

            <div class="row g-3 align-items-end">
                <div class="col-12 col-sm-6 col-md-4">
                    <label for="startDate" class="form-label small fw-600 mb-1" style="color: var(--banat-text-medium);">
                        <i class="fa-regular fa-calendar me-1 text-danger"></i> Tanggal Mulai
                    </label>
                    <input type="date" class="form-control form-control-banat" id="startDate" name="startDate">
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <label for="endDate" class="form-label small fw-600 mb-1" style="color: var(--banat-text-medium);">
                        <i class="fa-regular fa-calendar-check me-1 text-danger"></i> Tanggal Akhir
                    </label>
                    <input type="date" class="form-control form-control-banat" id="endDate" name="endDate">
                </div>
                <div class="col-12 col-md-4">
                    <div class="d-flex gap-2">
                        <button type="button" id="filterButton" class="btn-banat-primary flex-grow-1 py-2" style="font-size: 0.9rem;">
                            <i class="fa-solid fa-magnifying-glass me-1"></i> Filter
                        </button>
                        <button type="button" id="resetButton" class="btn-banat-outline flex-grow-1 py-2" style="font-size: 0.9rem;">
                            <i class="fa-solid fa-rotate-left me-1"></i> Semua
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Attendance Data Table Card -->
        <div class="banat-dashboard-card p-4">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom" style="border-color: var(--banat-border) !important;">
                <div>
                    <h4 class="fw-800 mb-1" style="color: var(--banat-text-dark);">
                        <i class="fa-solid fa-calendar-days me-2" style="color: var(--banat-primary);"></i> Catatan Presensi Saya
                    </h4>
                    <p class="text-muted small mb-0">Daftar waktu kehadiran datang, pulang, dan catatan keterangan presensi</p>
                </div>
            </div>

            <div class="table-responsive banat-table-container">
                <table id="table" class="table banat-table w-100">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">No.</th>
                            <th class="text-center">Tanggal</th>
                            <th class="text-center">Jam Datang</th>
                            <th class="text-center">Jam Pulang</th>
                            <th class="text-center">Status Isi Keterangan</th>
                            <th class="text-center" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th class="text-center" style="width: 50px;">No.</th>
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
    </div>
</section>

<!-- Modal Keterangan Form -->
<div class="modal fade" id="modal-keterangan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content banat-modal-content">
            <div class="modal-header banat-modal-header">
                <h5 class="modal-title fw-700" id="title_edit">Isi Keterangan Presensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="form-keterangan" method="POST" action="{{ route('dashboard.store') }}">
                    @csrf
                    <input type="hidden" name="absensi_id" id="id_edit">
                    <div class="mb-3">
                        <label for="waktu_edit" class="form-label small fw-600" style="color: var(--banat-text-medium);">
                            <i class="fa-regular fa-clock me-1 text-danger"></i> Waktu Kehadiran:
                        </label>
                        <input type="time" class="form-control form-control-banat" id="waktu_edit" name="waktu" required />
                    </div>
                    <div class="mb-3">
                        <label for="keterangan_edit" class="form-label small fw-600" style="color: var(--banat-text-medium);">
                            <i class="fa-regular fa-pen-to-square me-1 text-danger"></i> Keterangan / Catatan Kegiatan:
                        </label>
                        <textarea required class="form-control form-control-banat" id="keterangan_edit" style="min-height: 85px;" name="keterangan" rows="4" placeholder="Masukkan keterangan kehadiran atau catatan kegiatan..."></textarea>
                    </div>
                    <div class="alert banat-alert-info mb-3 d-flex align-items-center gap-2 small p-3">
                        <i class="fa-solid fa-circle-info fa-lg"></i>
                        <span>Mohon isi keterangan dengan jelas agar dapat membantu kami memahami segala kondisi.</span>
                    </div>
                    <button type="submit" class="btn-banat-primary w-100 py-2">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Catatan
                    </button>
                </form>

                <div class="mt-4 pt-3 border-top" style="border-color: var(--banat-border) !important;">
                    <h6 class="fw-700 small mb-2" style="color: var(--banat-text-dark);">
                        <i class="fa-solid fa-list-check me-1 text-danger"></i> Riwayat Keterangan Tercatat:
                    </h6>
                    <ol id="list-keterangan" class="list-group list-group-numbered mb-2"></ol>
                    <div class="alert banat-alert-info small py-2 px-3 mb-0">
                        <i class="fa-solid fa-hand-pointer me-1"></i> Klik item keterangan di atas untuk mengeditnya.
                    </div>
                </div>
            </div>
            <div class="modal-footer banat-modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Keterangan Lengkap -->
<div class="modal fade" id="keteranganModal" tabindex="-1" aria-labelledby="keteranganModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content banat-modal-content">
            <div class="modal-header banat-modal-header">
                <h5 class="modal-title fw-700" id="keteranganModalLabel">
                    <i class="fa-solid fa-file-lines me-2 text-danger"></i> Keterangan Lengkap
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-4" id="modalKeteranganBody" style="line-height: 1.7; color: var(--banat-text-medium);">
            </div>
            <div class="modal-footer banat-modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
                <div class="my-2">
                    <label class="form-label small fw-600 mb-1" style="color: var(--banat-text-medium);">
                        <i class="fa-regular fa-clock me-1 text-danger"></i> Waktu:
                    </label>
                    <input type="time" class="form-control form-control-banat" name="waktu"
                        placeholder="Masukkan waktu kehadiran" value="${waktu}" required />
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-600 mb-1" style="color: var(--banat-text-medium);">
                        <i class="fa-regular fa-pen-to-square me-1 text-danger"></i> Keterangan:
                    </label>
                    <textarea class="form-control form-control-banat" name="keterangan" style="min-height: 100px;"
                        placeholder="Masukkan keterangan kehadiran..." required>${keterangan}</textarea>
                </div>
                <div class="d-flex flex-wrap gap-2 justify-content-end mb-3">
                    <button onclick="deleteKeterangan('#edit-keterangan-${id} form')" type="button" class="btn btn-sm btn-danger py-2 px-3 border-0 flex-grow-1" style="border-radius: 30px;">
                        <i class="fa-solid fa-trash-can me-1"></i> Hapus
                    </button>
                    <button onclick="batalkanEditKeterangan(${id})" type="button" class="btn btn-sm btn-secondary py-2 px-3 border-0 flex-grow-1" style="border-radius: 30px;">
                        <i class="fa-solid fa-xmark me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-sm btn-banat-primary py-2 px-3 flex-grow-1">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Edit
                    </button>
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
