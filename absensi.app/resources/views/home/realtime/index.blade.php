@extends('layouts.home.template')
@section('title', 'Realtime | Absensi UII Dalwa')
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

        .select-active.w-100+.select2-container {
            width: 100% !important;
            max-width: 100% !important;
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
                    <span></span> <a href="{{ route('absensi.index') }}">Absensi</a>
                    <span></span> <span class="active" style="color: var(--banat-text-dark); font-weight: 600;">Realtime Presensi</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge" style="background: rgba(224, 82, 117, 0.12); color: var(--banat-primary); border: 1px solid var(--banat-border); border-radius: 30px; padding: 6px 14px; font-weight: 600; font-size: 0.8rem;">
                        <i class="fa-solid fa-circle text-success me-1 animate-pulse" style="font-size: 8px;"></i> Live Biometrik Sync
                    </span>
                </div>
            </div>
        </div>
    </div>

    <section class="py-4 py-md-5 position-relative" style="background: var(--banat-bg-soft); min-height: 80vh;">
        <div class="container">
            <!-- 1. Header Overview Card -->
            <div class="banat-dashboard-card mb-4 p-4 p-md-5">
                <div class="row align-items-center g-3">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge-role">
                                <i class="fa-solid fa-tower-broadcast me-1"></i> Realtime Stream
                            </span>
                            <span class="badge-dept">
                                <i class="fa-solid fa-building-columns me-1"></i> Kampus Banat UII Dalwa
                            </span>
                        </div>
                        <h2 class="fw-800 mb-2 dashboard-user-name">
                            Presensi Realtime Civitas Banat
                        </h2>
                        <p class="text-muted mb-0" style="max-width: 650px; font-size: 0.95rem; line-height: 1.6;">
                            Pantau catatan kehadiran dosen dan staf yang terverifikasi serta tersinkronisasi otomatis dari mesin biometrik fingerspot secara langsung tanpa jeda.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <div class="d-inline-flex flex-column align-items-lg-end gap-2">
                            <div class="dashboard-stat-pill">
                                <div class="stat-pill-icon">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                </div>
                                <div class="text-start">
                                    <span class="stat-pill-num fw-800 d-block" id="realtimeClock">{{ date('H:i:s') }}</span>
                                    <span class="stat-pill-label">Waktu Sistem Server</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Filter Controls Card -->
            <div class="banat-dashboard-card mb-4 p-4">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <h5 class="fw-700 mb-0" style="color: var(--banat-text-dark);">
                        <i class="fa-solid fa-filter me-2" style="color: var(--banat-primary);"></i> Filter Log Realtime
                    </h5>
                    <span class="text-muted small">Filter berdasarkan departemen, lokasi mesin, atau rentang tanggal</span>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="departemenId" class="form-label small fw-600 mb-1" style="color: var(--banat-text-medium);">
                            <i class="fa-solid fa-building-user me-1 text-danger"></i> Departemen:
                        </label>
                        <select class="select-active w-100" name="departemen_id" id="departemenId">
                            <option value="">Semua Departemen</option>
                            @foreach ($departemen as $key => $value)
                                <option value="{{ $value->id }}">{{ $value->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="deviceId" class="form-label small fw-600 mb-1" style="color: var(--banat-text-medium);">
                            <i class="fa-solid fa-fingerprint me-1 text-danger"></i> Lokasi Mesin / Device:
                        </label>
                        <select class="select-active w-100" name="device_id" id="deviceId">
                            <option value="">Semua Lokasi Mesin</option>
                            @foreach ($device as $key => $value)
                                <option value="{{ $value->id }}">{{ $value->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-md-4">
                        <label for="startDate" class="form-label small fw-600 mb-1" style="color: var(--banat-text-medium);">
                            <i class="fa-regular fa-calendar me-1 text-danger"></i> Tanggal Mulai:
                        </label>
                        <input type="date" class="form-control form-control-banat" id="startDate" name="startDate">
                    </div>
                    <div class="col-12 col-sm-6 col-md-4">
                        <label for="endDate" class="form-label small fw-600 mb-1" style="color: var(--banat-text-medium);">
                            <i class="fa-regular fa-calendar-check me-1 text-danger"></i> Tanggal Akhir:
                        </label>
                        <input type="date" class="form-control form-control-banat" id="endDate" name="endDate">
                    </div>
                    <div class="col-12 col-md-4 d-flex align-items-end">
                        <div class="d-flex gap-2 w-100">
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

            <!-- 3. Table Log Card -->
            <div class="banat-dashboard-card p-4">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom" style="border-color: var(--banat-border) !important;">
                    <div>
                        <h4 class="fw-800 mb-1" style="color: var(--banat-text-dark);">
                            <i class="fa-solid fa-table-list me-2" style="color: var(--banat-primary);"></i> Data Kehadiran Realtime
                        </h4>
                        <p class="text-muted small mb-0">Catatan kehadiran tercatat otomatis dari perangkat biometrik</p>
                    </div>
                </div>

                <div class="table-responsive banat-table-container">
                    <table id="example" class="table banat-table w-100">
                        <thead>
                            <tr>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Jam</th>
                                <th>Nama Civitas</th>
                                <th>Departemen</th>
                                <th class="text-center">Lokasi Mesin</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Jam</th>
                                <th>Nama Civitas</th>
                                <th>Departemen</th>
                                <th class="text-center">Lokasi Mesin</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script>
        document.getElementById('startDate').value =
            new Date().toISOString().split('T')[0];
    </script>
    <script>
        let dataTable = $("#example").DataTable({
            autoWidth: true,
            processing: false,
            serverSide: true,
            search: {
                return: true,
            },
            ajax: {
                url: "{{ route('realtime.data') }}",
                method: "GET",
                data: function(d) {
                    d.startDate = $("#startDate").val();
                    d.endDate = $("#endDate").val();
                    d.departemenId = $("#departemenId").val();
                    d.deviceId = $("#deviceId").val();
                },
            },
            columns: [{
                    class: "text-center",
                    data: "tgl_absen",
                    name: "tgl_absen",
                },
                {
                    class: "text-center",
                    data: "pagi",
                    name: "pagi",
                },
                {
                    class: "text-center",
                    data: "user_name",
                    name: "user_name",
                },
                {
                    class: "text-center",
                    data: "departemen_nama",
                    name: "departemen_nama",
                },
                {
                    class: "text-center",
                    data: "device_name",
                    name: "device_name",
                },
            ],
            order: [
                [0, "desc"],
                [1, "desc"]
            ],
        });

        $('#startDate').change(function(e) {
            e.preventDefault();
            dataTable.ajax.reload(null, false);
        });

        $('#endDate').change(function(e) {
            e.preventDefault();
            dataTable.ajax.reload(null, false);
        });

        $('#departemenId').change(function(e) {
            e.preventDefault();
            dataTable.ajax.reload(null, false);
        });

        $('#deviceId').change(function(e) {
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

        setInterval(function() {
            dataTable.ajax.reload(null, false);
        }, 3000);

        setInterval(function() {
            const clockEl = document.getElementById('realtimeClock');
            if (clockEl) {
                const now = new Date();
                clockEl.textContent = now.toTimeString().split(' ')[0];
            }
        }, 1000);
    </script>
@endpush
