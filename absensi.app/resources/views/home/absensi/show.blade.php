@extends('layouts.home.template')
@section('title', 'Detail Presensi - ' . $user->name . ' | Absensi UII Dalwa')
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
            <a href="{{ route('root.index') }}" rel="nofollow"><i class="fi-rs-home mr-5"></i>Home</a>
            <span></span> <a href="{{ route('absensi.index') }}">Absensi</a>
            <span></span> Detail Presensi
        </div>
    </div>
</div>

<section class="mt-40 mb-50">
    <div class="container">
        <!-- User Profile Card -->
        <div class="banat-dashboard-card mb-4">
            <div class="d-flex flex-column flex-md-row align-items-center gap-4">
                <div class="position-relative">
                    <div class="dashboard-avatar-ring">
                        <img src="{{ $user->photo ? asset('photo') . '/'.$user->photo : asset('home/assets/imgs/theme/user.png') }}" 
                             alt="{{ $user->name }}" 
                             style="width: 110px; height: 110px; object-fit: cover; border-radius: 50%;">
                    </div>
                    <span class="position-absolute bottom-0 end-0 badge rounded-pill bg-success p-2 border border-2 border-white">
                        <span class="visually-hidden">Active</span>
                    </span>
                </div>
                <div class="flex-grow-1 text-center text-md-start">
                    <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-2 mb-2">
                        <h3 class="dashboard-user-name mb-0">{{ $user->name }}</h3>
                        <span class="badge-banat-primary">{{ $user->role->akses ?? 'Civitas' }}</span>
                        <span class="badge rounded-pill" style="background: rgba(14, 165, 233, 0.15); color: #0284c7; border: 1px solid rgba(14, 165, 233, 0.3);">
                            {{ $user->departemen->nama ?? '-' }}
                        </span>
                    </div>
                    <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-3 text-muted font-sm mb-3">
                        <span><i class="fi-rs-id-badge mr-5"></i>ID Civitas: <strong style="color: var(--banat-text-dark);">{{ $user->id }}</strong></span>
                        <span><i class="fi-rs-user mr-5"></i>Jenis Kelamin: <strong style="color: var(--banat-text-dark);">{{ $user->jenis_kelamin }}</strong></span>
                        <span><i class="fi-rs-check mr-5"></i>Total Presensi: <strong style="color: var(--banat-text-dark);">{{ $user->absensi->count() }} Hari</strong></span>
                    </div>
                    <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-md-start">
                        <a href="#" id="copyButton" class="btn btn-sm btn-banat-outline" data-bs-toggle="tooltip" data-bs-placement="top" title="Salin Tautan Profil">
                            <i class="fi-rs-copy mr-5"></i> Salin URL Profil
                        </a>
                        <a href="{{ route('absensi.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3" style="display: inline-flex; align-items: center; gap: 5px;">
                            <i class="fi-rs-arrow-left"></i> Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance History Card -->
        <div class="banat-dashboard-card mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4 pb-3" style="border-bottom: 1px solid var(--banat-border);">
                <div>
                    <h4 class="mb-1" style="font-weight: 700; color: var(--banat-text-dark);">
                        <i class="fi-rs-calendar mr-5" style="color: var(--banat-primary);"></i> Riwayat Log Presensi
                    </h4>
                    <p class="text-muted font-sm mb-0">Rincian catatan kehadiran, jam kedatangan, kepulangan, dan surat keterangan.</p>
                </div>
            </div>

            <!-- Filter Controls -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label for="startDate" class="form-label font-sm fw-bold" style="color: var(--banat-text-dark);">Tanggal Mulai</label>
                    <input type="date" class="form-control form-control-banat" id="startDate" name="startDate">
                </div>
                <div class="col-md-4">
                    <label for="endDate" class="form-label font-sm fw-bold" style="color: var(--banat-text-dark);">Tanggal Akhir</label>
                    <input type="date" class="form-control form-control-banat" id="endDate" name="endDate">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" id="filterButton" class="btn btn-banat-primary w-100" style="height: 48px;">
                        <i class="fi-rs-filter mr-5"></i> Filter
                    </button>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" id="resetButton" class="btn btn-outline-secondary w-100 rounded-pill" style="height: 48px; font-weight: 600;">
                        <i class="fi-rs-refresh mr-5"></i> Semua
                    </button>
                </div>
            </div>

            <!-- Table Container -->
            <div class="banat-table-container">
                <div class="table-responsive">
                    <table id="example" class="table banat-table w-100">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;">No.</th>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Jam Datang</th>
                                <th class="text-center">Jam Pulang</th>
                                <th class="text-center">Status Keterangan</th>
                                <th class="text-center" style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Keterangan -->
<div class="modal fade" id="modal-keterangan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content banat-modal-content">
            <div class="modal-header banat-modal-header">
                <h5 class="modal-title fw-bold" id="title_edit" style="color: var(--banat-text-dark);">Detail Keterangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <ol id="list-keterangan" class="list-group list-group-numbered mb-0">
                </ol>
            </div>
            <div class="modal-footer banat-modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
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
            e.preventDefault();
            let currentURL = "{{ route('absensi.show', ['user' => $user]) }}";

            navigator.clipboard.writeText(currentURL).then(function() {
                copyButton.setAttribute("title", "Copied!");
                tooltip.dispose();
                tooltip = new bootstrap.Tooltip(copyButton);
                tooltip.show();

                setTimeout(() => {
                    copyButton.setAttribute("title", "Salin URL Profil");
                    tooltip.dispose();
                    tooltip = new bootstrap.Tooltip(copyButton);
                }, 1500);
            });
        });

        let dataTable = $("#example").DataTable({
            autoWidth: true,
            processing: true,
            serverSide: true,
            search: {
                return: true
            },
            ajax: {
                url: "{{ route('absensi.data', ['user' => $user]) }}",
                method: "GET",
                data: function(d) {
                    d.startDate = $("#startDate").val();
                    d.endDate = $("#endDate").val();
                }
            },
            columns: [
                {
                    class: "text-center",
                    data: "tgl_absen",
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    class: "text-center",
                    data: "tgl_absen",
                    name: "tgl_absen"
                },
                {
                    class: "text-center",
                    data: "pagi",
                    name: "pagi"
                },
                {
                    class: "text-center",
                    data: "sore",
                    name: "sore"
                },
                {
                    class: "text-center",
                    data: "has_keterangan",
                    name: "has_keterangan"
                },
                {
                    data: "action",
                    name: "action",
                    class: "text-center",
                    searchable: false,
                    orderable: false
                }
            ],
            order: [
                [0, "desc"]
            ]
        });

        $('#startDate, #endDate').change(function(e) {
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

            modal.find('#list-keterangan').html('<div class="text-center py-3"><div class="spinner-border text-danger spinner-border-sm" role="status"></div> Loading...</div>');

            loadKeterangan(button.data('id'));
        });

        function loadKeterangan(absensiId) {
            let route = "{{ route('absensi.keterangan', ['user' => $user, 'absensi' => ':id']) }}";
            route = route.replace(':id', absensiId);
            $.get(route)
                .done(function(response) {
                    if (response.length <= 0) {
                        $('#list-keterangan').html('<li class="list-group-item text-center text-muted">Tidak ada data keterangan</li>');
                        return;
                    }
                    let content = ``;
                    response.forEach(element => {
                        content += `
                            <li class="mb-2 list-group-item d-flex justify-content-between align-items-start rounded">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold" style="color: var(--banat-primary);">${element.waktu}</div>
                                    <div>${element.keterangan}</div>
                                </div>
                            </li>
                        `;
                    });
                    $('#list-keterangan').html(content);
                })
                .fail(function(xhr) {
                    console.log(xhr);
                    $('#list-keterangan').html('<li class="list-group-item text-danger text-center">Gagal memuat keterangan</li>');
                });
        }
    });
</script>
@endpush
