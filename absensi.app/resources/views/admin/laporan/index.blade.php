@extends('layouts.admin.template')
@section('title', 'Laporan')

@push('css')
    <style>
        .laporan-title {
            background: #f5f6ff;
            color: #17235a;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: .5px;
            text-align: center;
            padding: 26px 16px;
        }

        .laporan-title i {
            color: #26309a;
            margin-right: 12px;
        }

        #table-laporan thead th {
            background: #24238f;
            color: #fff;
            font-weight: 700;
            white-space: nowrap;
        }

        #table-laporan tbody td {
            vertical-align: middle;
            white-space: nowrap;
        }

        #table-laporan tbody td.nominal {
            color: #7467f0;
            font-weight: 700;
        }

    </style>
@endpush

@section('content')
    <div class="card mb-4">
        <div class="card-body">
            <div class="mb-3">
                <h5 class="mb-1">Filter Laporan</h5>
                <small class="text-muted">Pilih bulan dan kategori untuk menampilkan rekap pemasukan.</small>
            </div>
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="month" class="form-label">Bulan</label>
                    <input type="month" class="form-control" id="month" value="{{ now()->format('Y-m') }}">
                </div>
                <div class="col-md-3">
                    <label for="departemen_id" class="form-label">Departemen</label>
                    <select class="form-select" id="departemen_id">
                        <option value="*">Semua Departemen</option>
                        @foreach ($departemen as $item)
                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="kategori_id" class="form-label">Kategori</label>
                    <select class="form-select" id="kategori_id">
                        <option value="*">Semua Kategori</option>
                        @foreach ($kategori as $item)
                            <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="button" id="filterButton" class="btn btn-primary w-100">Filter</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card" id="card-laporan">
        <div class="laporan-title">
            <i class="ti ti-chart-bar"></i><span id="laporan-title">PEMASUKAN TUNAI</span>
        </div>
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table table-hover" id="table-laporan">
                <thead></thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let dataTable = null;

        function formatRupiah(value) {
            if (value === null || value === undefined) {
                return '-';
            }

            return 'Rp ' + Number(value).toLocaleString('id-ID');
        }

        function loadLaporan() {
            $.get("{{ route('admin.laporan.data') }}", {
                month: $('#month').val(),
                kategori_id: $('#kategori_id').val(),
                departemen_id: $('#departemen_id').val()
            }).done(function(response) {
                $('#laporan-title').text(response.title);

                const selectedKategori = $('#kategori_id').val();
                const categories = selectedKategori === '*'
                    ? response.categories
                    : response.categories.filter(function(item) {
                        return String(item.id) === String(selectedKategori);
                    });

                let header = '<tr><th>NO</th><th>TANGGAL</th>';
                categories.forEach(function(item) {
                    header += `<th>${item.kode}</th>`;
                });
                header += '</tr>';

                $('#table-laporan thead').html(header);

                const rows = response.rows.map(function(row) {
                    const item = {
                        no: row.no,
                        tanggal: row.tanggal
                    };

                    categories.forEach(function(category) {
                        const value = row.values[category.id] || { amount: null };
                        item['kategori_' + category.id] = formatRupiah(value.amount);
                    });

                    return item;
                });

                const columns = [
                    { data: 'no', className: 'text-center' },
                    { data: 'tanggal', className: 'fw-semibold' }
                ];

                categories.forEach(function(category) {
                    columns.push({
                        data: 'kategori_' + category.id,
                        className: 'text-end nominal'
                    });
                });

                if (dataTable) {
                    dataTable.destroy();
                    $('#table-laporan tbody').empty();
                }

                dataTable = $('#table-laporan').DataTable({
                    data: rows,
                    columns: columns,
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    order: [[0, 'asc']],
                    scrollX: true
                });
            });
        }

        $('#filterButton, #month, #departemen_id, #kategori_id').on('click change', function() {
            loadLaporan();
        });

        loadLaporan();
    </script>
@endpush
