@extends('layouts.admin.template')
@section('title', 'Laporan')

@push('css')
    <style>
        .banat-page-header-card {
            background: linear-gradient(135deg, #180f24 0%, #2b143a 50%, #401535 100%) !important;
            border-radius: 20px !important;
            border: 1px solid rgba(251, 113, 133, 0.28) !important;
            box-shadow: 0 14px 35px rgba(18, 9, 28, 0.35) !important;
            color: #ffffff;
            position: relative;
            overflow: hidden;
        }

        .banat-page-header-card::before {
            content: '';
            position: absolute;
            top: -50px;
            right: 10%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(251, 113, 133, 0.25) 0%, rgba(0, 0, 0, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .banat-header-icon-box {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: linear-gradient(135deg, #fb7185 0%, #e11d48 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            box-shadow: 0 4px 14px rgba(225, 29, 72, 0.4);
            flex-shrink: 0;
        }

        .banat-filter-card {
            border-radius: 20px !important;
            border: 1px solid rgba(251, 113, 133, 0.22) !important;
            box-shadow: 0 8px 25px rgba(225, 29, 72, 0.05) !important;
            overflow: hidden;
            background: var(--banat-bg-surface, #ffffff);
        }

        .dark-style .banat-filter-card {
            background: rgba(255, 255, 255, 0.03) !important;
            border-color: rgba(251, 113, 133, 0.28) !important;
        }

        .laporan-title-banner {
            background: linear-gradient(135deg, #180f24 0%, #291438 50%, #3e1635 100%) !important;
            color: #ffffff !important;
            font-size: 1.15rem;
            font-weight: 800;
            letter-spacing: .5px;
            text-align: center;
            padding: 20px 16px;
            border-bottom: 2px solid rgba(251, 113, 133, 0.3) !important;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .laporan-title-banner i {
            color: #fb7185;
            font-size: 1.35rem;
        }

        #table-laporan thead th {
            background: rgba(251, 113, 133, 0.08) !important;
            color: #e11d48 !important;
            font-size: 0.78rem !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.06em !important;
            border-bottom: 2px solid rgba(251, 113, 133, 0.22) !important;
            padding: 14px 16px !important;
            white-space: nowrap;
        }

        .dark-style #table-laporan thead th {
            background: rgba(251, 113, 133, 0.16) !important;
            color: #fda4af !important;
            border-bottom-color: rgba(251, 113, 133, 0.35) !important;
        }

        #table-laporan tbody td {
            vertical-align: middle;
            white-space: nowrap;
            padding: 12px 16px;
        }

        #table-laporan tbody td.nominal {
            color: #e11d48 !important;
            font-weight: 700;
            font-family: monospace, sans-serif;
            font-size: 0.92rem;
        }

        .dark-style #table-laporan tbody td.nominal {
            color: #fda4af !important;
        }
    </style>
@endpush

@section('content')
    <!-- Header Banner -->
    <div class="card banat-page-header-card mb-4">
        <div class="card-body p-4 position-relative" style="z-index: 2;">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="banat-header-icon-box">
                        <i class="ti ti-report-money"></i>
                    </div>
                    <div>
                        <div class="badge mb-1 px-3 py-1" style="background: rgba(251, 113, 133, 0.22); color: #fda4af; border: 1px solid rgba(251, 113, 133, 0.4); font-size: 0.72rem; border-radius: 20px;">
                            <i class="ti ti-chart-bar me-1"></i> REKAPITULASI KEUANGAN &amp; PRESENSI
                        </div>
                        <h4 class="text-white fw-bold mb-0">Laporan Pemasukan &amp; Presensi Banat</h4>
                        <small class="text-white-50">Monitoring realtime pencatatan keuangan dan kehadiran civitas pondok putri</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge px-3 py-2 fw-semibold" style="background: rgba(16, 185, 129, 0.18); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4); border-radius: 20px; font-size: 0.76rem;">
                        <i class="ti ti-circle-check me-1"></i> Terverifikasi Sistem
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card banat-filter-card mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge rounded p-1" style="background: rgba(225, 29, 72, 0.12); color: #e11d48;">
                    <i class="ti ti-adjustments-horizontal ti-sm"></i>
                </span>
                <h5 class="mb-0 fw-bold">Filter Parameter Laporan</h5>
            </div>
            <small class="text-muted d-block mb-3">Sesuaikan periode bulan, departemen, dan kategori untuk menampilkan data rekapitulasi secara akurat.</small>
            
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="month" class="form-label fw-semibold">Pilih Bulan</label>
                    <input type="month" class="form-control" id="month" value="{{ now()->format('Y-m') }}" style="border-radius: 12px;">
                </div>
                <div class="col-md-3">
                    <label for="departemen_id" class="form-label fw-semibold">Departemen</label>
                    <select class="form-select" id="departemen_id" style="border-radius: 12px;">
                        <option value="*">Semua Departemen</option>
                        @foreach ($departemen as $item)
                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="kategori_id" class="form-label fw-semibold">Kategori</label>
                    <select class="form-select" id="kategori_id" style="border-radius: 12px;">
                        <option value="*">Semua Kategori</option>
                        @foreach ($kategori as $item)
                            <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="button" id="filterButton" class="btn btn-primary w-100 py-2">
                        <i class="ti ti-filter me-1"></i> Terapkan Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card banat-filter-card" id="card-laporan">
        <div class="laporan-title-banner">
            <i class="ti ti-chart-bar"></i>
            <span id="laporan-title">PEMASUKAN TUNAI</span>
        </div>
        <div class="card-datatable table-responsive pt-2 px-3 pb-3">
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
