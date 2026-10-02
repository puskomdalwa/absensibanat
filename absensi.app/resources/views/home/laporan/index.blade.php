@extends('layouts.home.template')
@section('title', 'Laporan Presensi & Honorarium | Absensi UII Dalwa')

@push('css')
<style>
    .dt-layout-full {
        padding: 0 !important;
    }
</style>
@endpush

@section('content')
<div class="page-header breadcrumb-wrap">
    <div class="container">
        <div class="breadcrumb">
            <a href="{{ route('root.index') }}" rel="nofollow"><i class="fa-solid fa-house-chimney me-1"></i> Home</a>
            <span></span> <span class="active" style="color: var(--banat-text-dark); font-weight: 600;">Laporan Presensi & Honorarium</span>
        </div>
    </div>
</div>

<section class="mt-40 mb-50">
    <div class="container">
        <!-- Overview Header Card -->
        <div class="banat-dashboard-card mb-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge-banat-primary"><i class="fa-regular fa-file-lines me-1"></i> Rekapitulasi</span>
                        <span class="badge rounded-pill" style="background: rgba(16, 185, 129, 0.15); color: #059669; border: 1px solid rgba(16, 185, 129, 0.3);">
                            <i class="fa-solid fa-circle-check me-1"></i> Terverifikasi Sistem
                        </span>
                    </div>
                    <h3 class="dashboard-user-name mb-1">Laporan Presensi & Honorarium</h3>
                    <p class="text-muted font-sm mb-0">
                        Rekapitulasi catatan presensi harian civitas akademika, durasi jam kerja efektif, dan kalkulasi honorarium per kategori.
                    </p>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <a href="{{ route('realtime.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3" style="display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-chart-line"></i> Presensi Realtime
                    </a>
                    <a href="{{ route('absensi.index') }}" class="btn btn-sm btn-banat-outline" style="display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-users"></i> Data Civitas
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="banat-dashboard-card mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4 pb-3" style="border-bottom: 1px solid var(--banat-border);">
                <div>
                    <h4 class="mb-1" style="font-weight: 700; color: var(--banat-text-dark);">
                        <i class="fa-solid fa-sliders me-2" style="color: var(--banat-primary);"></i> Filter Rentang Laporan
                    </h4>
                    <p class="text-muted font-sm mb-0">Tentukan periode tanggal dan kategori untuk menampilkan data rekapitulasi presensi.</p>
                </div>
            </div>

            <!-- Filter Controls Grid -->
            <div class="row g-3 mb-0">
                <div class="col-12 col-md-3">
                    <label for="startDate" class="form-label font-sm fw-bold mb-1" style="color: var(--banat-text-dark);">
                        <i class="fa-regular fa-calendar me-1 text-danger"></i> Tanggal Mulai
                    </label>
                    <input type="date" class="form-control form-control-banat" id="startDate" name="startDate">
                </div>
                <div class="col-12 col-md-3">
                    <label for="endDate" class="form-label font-sm fw-bold mb-1" style="color: var(--banat-text-dark);">
                        <i class="fa-regular fa-calendar-check me-1 text-danger"></i> Tanggal Akhir
                    </label>
                    <input type="date" class="form-control form-control-banat" id="endDate" name="endDate">
                </div>
                @if ($canFilterKategori)
                <div class="col-12 col-md-3">
                    <label for="kategori_id" class="form-label font-sm fw-bold mb-1" style="color: var(--banat-text-dark);">
                        <i class="fa-solid fa-tags me-1 text-danger"></i> Kategori Honorarium
                    </label>
                    <select class="form-control form-control-banat" id="kategori_id">
                        <option value="*">Semua Kategori</option>
                        @foreach ($kategori as $item)
                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="{{ $canFilterKategori ? 'col-12 col-md-3' : 'col-12 col-md-6' }} d-flex align-items-end gap-2">
                    <button type="button" id="filterButton" class="btn btn-banat-primary flex-grow-1" style="height: 48px;">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Terapkan Filter
                    </button>
                    <button type="button" id="resetLaporanBtn" class="btn btn-outline-secondary rounded-pill px-3" style="height: 48px; font-weight: 600;" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Data Table Card -->
        <div class="banat-dashboard-card mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4 pb-3" style="border-bottom: 1px solid var(--banat-border);">
                <div>
                    <h4 class="mb-1" style="font-weight: 700; color: var(--banat-text-dark);">
                        <i class="fa-solid fa-table-list me-2" style="color: var(--banat-primary);"></i> Data Rekapitulasi Presensi
                    </h4>
                    <p class="text-muted font-sm mb-0">Daftar kehadiran, kalkulasi durasi jam kerja efektif, dan nominal honorarium.</p>
                </div>
                <div>
                    <span class="badge" style="background: rgba(224, 82, 117, 0.1); color: var(--banat-primary); border: 1px solid var(--banat-border); border-radius: 30px; padding: 6px 14px; font-weight: 600; font-size: 0.82rem;">
                        <i class="fa-solid fa-database me-1"></i> Data Terintegrasi
                    </span>
                </div>
            </div>

            <!-- Table Container without duplicate border -->
            <div class="table-responsive my-2">
                <table id="table-laporan" class="table banat-table w-100">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">No.</th>
                            <th class="text-center">Tanggal</th>
                            <th class="text-center">Jam Datang</th>
                            <th class="text-center">Jam Pulang</th>
                            <th class="text-center">Kategori</th>
                            <th class="text-center">Durasi Efektif</th>
                            <th class="text-center">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    let dataTable = $("#table-laporan").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('laporan.data') }}",
            method: "GET",
            data: function(d) {
                d.startDate = $("#startDate").val();
                d.endDate = $("#endDate").val();
                d.kategori_id = $("#kategori_id").length ? $("#kategori_id").val() : '*';
            }
        },
        dom: '<"dt-top-controls d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3"lf>rt<"dt-bottom-controls d-flex flex-column flex-md-row justify-content-between align-items-md-center mt-4 pt-3 border-top gap-3"ip>',
        language: {
            search: "",
            searchPlaceholder: "Cari riwayat presensi...",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            zeroRecords: '<div class="py-4 text-center text-muted"><i class="fa-regular fa-folder-open fa-2x mb-2 d-block opacity-50"></i>Tidak ada data presensi yang ditemukan</div>',
            paginate: {
                first: '<i class="fa-solid fa-angles-left"></i>',
                previous: '<i class="fa-solid fa-chevron-left"></i>',
                next: '<i class="fa-solid fa-chevron-right"></i>',
                last: '<i class="fa-solid fa-angles-right"></i>'
            }
        },
        columns: [
            {
                data: "tgl_absen",
                class: "text-center",
                render: function(data, type, row, meta) {
                    return '<span class="badge rounded-pill" style="background: rgba(0,0,0,0.04); color: var(--banat-text-medium); font-weight: 600; padding: 4px 10px;">' + (meta.row + meta.settings._iDisplayStart + 1) + '</span>';
                },
                searchable: false,
                orderable: false
            },
            { 
                data: "tgl_absen", 
                name: "absensi.tgl_absen", 
                class: "text-center fw-600" 
            },
            { 
                data: "pagi", 
                name: "absensi.pagi", 
                class: "text-center",
                render: function(data) {
                    return data ? '<span class="badge rounded-pill" style="background: rgba(16, 185, 129, 0.12); color: #059669; font-weight: 600; padding: 6px 12px;"><i class="fa-solid fa-sun me-1"></i>' + data + '</span>' : '<span class="text-muted">-</span>';
                }
            },
            { 
                data: "sore", 
                name: "absensi.sore", 
                class: "text-center",
                render: function(data) {
                    return data ? '<span class="badge rounded-pill" style="background: rgba(225, 29, 72, 0.1); color: #e11d48; font-weight: 600; padding: 6px 12px;"><i class="fa-solid fa-moon me-1"></i>' + data + '</span>' : '<span class="text-muted">-</span>';
                }
            },
            { 
                data: "kategori_nama", 
                name: "kategori.nama", 
                class: "text-center",
                render: function(data) {
                    return data ? '<span class="badge rounded-pill" style="background: rgba(14, 165, 233, 0.12); color: #0284c7; font-weight: 600; padding: 6px 12px;">' + data + '</span>' : '<span class="text-muted">-</span>';
                }
            },
            { 
                data: "durasi_efektif", 
                name: "durasi_efektif", 
                searchable: false, 
                orderable: false, 
                class: "text-center fw-600" 
            },
            { 
                data: "kategori_nominal", 
                name: "kategori.nominal", 
                class: "text-center fw-700",
                render: function(data) {
                    return data ? '<span style="color: var(--banat-primary); font-weight: 700;">' + data + '</span>' : '<span class="text-muted">-</span>';
                }
            }
        ],
        order: [[1, "desc"]]
    });

    $('#filterButton, #startDate, #endDate, #kategori_id').on('click change', function() {
        dataTable.ajax.reload(null, false);
    });

    $('#resetLaporanBtn').on('click', function(e) {
        e.preventDefault();
        $('#startDate').val('');
        $('#endDate').val('');
        if ($('#kategori_id').length) {
            $('#kategori_id').val('*');
        }
        dataTable.ajax.reload(null, false);
    });
</script>
@endpush
