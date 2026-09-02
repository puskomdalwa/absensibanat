@extends('layouts.home.template')
@section('title', 'Laporan | Absensi UII Dalwa')

@section('content')
<div class="page-header breadcrumb-wrap">
    <div class="container">
        <div class="breadcrumb">
            <a href="{{ route('root.index') }}" rel="nofollow">Home</a>
            <span></span> Laporan
        </div>
    </div>
</div>

<section class="mt-50 mb-50">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="section-title style-1 mb-30">Laporan Absensi</h2>
                <div class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="startDate" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="startDate">
                        </div>
                        <div class="col-md-3">
                            <label for="endDate" class="form-label">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="endDate">
                        </div>
                        @if ($canFilterKategori)
                            <div class="col-md-3">
                                <label for="kategori_id" class="form-label">Kategori</label>
                                <select class="form-control" id="kategori_id">
                                    <option value="*">Semua Kategori</option>
                                    @foreach ($kategori as $item)
                                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" id="filterButton" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="table-laporan" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Tanggal</th>
                                <th>Jam Datang</th>
                                <th>Jam Pulang</th>
                                <th>Kategori</th>
                                <th>Durasi Efektif</th>
                                <th>Nominal</th>
                            </tr>
                        </thead>
                    </table>
                </div>
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
        columns: [{
                data: "tgl_absen",
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
                searchable: false,
                orderable: false
            },
            { data: "tgl_absen", name: "absensi.tgl_absen" },
            { data: "pagi", name: "absensi.pagi" },
            { data: "sore", name: "absensi.sore" },
            { data: "kategori_nama", name: "kategori.nama" },
            { data: "durasi_efektif", name: "durasi_efektif", searchable: false, orderable: false },
            { data: "kategori_nominal", name: "kategori.nominal" }
        ],
        order: [[1, "desc"]]
    });

    $('#filterButton, #startDate, #endDate, #kategori_id').on('click change', function() {
        dataTable.ajax.reload(null, false);
    });
</script>
@endpush
