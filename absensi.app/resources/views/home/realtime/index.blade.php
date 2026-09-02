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
            <div class="breadcrumb">
                <a href="{{ route('root.index') }}" rel="nofollow">Home</a>
                <span></span> Absensi <span></span> Realtime
            </div>
        </div>
    </div>
    <section class="mt-50 mb-50">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="product-detail accordion-detail">

                        <div class="row">
                            <div class="col-12 m-auto entry-main-content">
                                <h2 class="section-title style-1 mb-30">Absensi Realtime</h2>
                                <div class="mb-3">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <select class="select-active w-100" name="departemen_id" id="departemenId">
                                                <option value="">Semua Departemen</option>
                                                @foreach ($departemen as $key => $value)
                                                    <option value="{{ $value->id }}">{{ $value->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <select class="select-active w-100" name="device_id" id="deviceId">
                                                <option value="">Semua Lokasi</option>
                                                @foreach ($device as $key => $value)
                                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="startDate" class="form-label">Tanggal Mulai</label>
                                            <input type="date" class="form-control" id="startDate" name="startDate">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="endDate" class="form-label">Tanggal Akhir</label>
                                            <input type="date" class="form-control" id="endDate" name="endDate">
                                        </div>
                                        <div class="col-md-4 d-flex align-items-end">
                                            <button type="button" id="filterButton"
                                                class="btn btn-primary w-100">Filter</button>
                                        </div>
                                        <div class="col-md-12 mt-3">
                                            <button type="button" id="resetButton"
                                                class="btn btn-secondary w-100">Tampilkan
                                                Semua Tanggal</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="example" class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Tanggal</th>
                                                <th class="text-center">Jam</th>
                                                <th class="text-center">Nama</th>
                                                <th class="text-center">Departemen</th>
                                                <th class="text-center">Lokasi Absen</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th class="text-center">Tanggal</th>
                                                <th class="text-center">Jam</th>
                                                <th class="text-center">Nama</th>
                                                <th class="text-center">Departemen</th>
                                                <th class="text-center">Lokasi Absen</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
        }, 2000);
    </script>
@endpush
