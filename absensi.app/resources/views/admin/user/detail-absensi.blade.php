@extends('layouts.admin.template')
@section('title', 'Detail Absensi User')

@push('css')
    <style>
        .detail-user-avatar {
            width: 54px;
            height: 54px;
            flex: 0 0 54px;
        }

        .detail-user-avatar img,
        .detail-user-avatar .avatar-initial {
            width: 54px;
            height: 54px;
            object-fit: cover;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 700;
        }

        .summary-icon {
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 22px;
        }

        .summary-value {
            font-size: 22px;
            font-weight: 700;
            color: #2f2b3d;
            line-height: 1.2;
        }

        .summary-label {
            color: #6f6b7d;
            font-size: 13px;
        }

        .chart-box {
            min-height: 300px;
        }

        #table-detail-absensi thead th,
        #table-kategori-summary thead th {
            white-space: nowrap;
        }

        #table-detail-absensi tbody td,
        #table-kategori-summary tbody td {
            vertical-align: middle;
            white-space: nowrap;
        }
    </style>
@endpush

@section('content')
    @php
        $isStaff = Auth::user()->isStaff();
        $assetsPath = asset('photo') . '/';
        $initials = collect(explode(' ', $user->name))
            ->filter()
            ->take(2)
            ->map(fn($name) => strtoupper(substr($name, 0, 1)))
            ->implode('');
    @endphp

    <div class="card mb-4">
        <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3 min-w-0">
                <div class="detail-user-avatar">
                    @if ($user->photo)
                        <img src="{{ $assetsPath . $user->photo }}" alt="Avatar" class="rounded-circle">
                    @else
                        <span class="avatar-initial rounded-circle bg-label-primary">{{ $initials }}</span>
                    @endif
                </div>
                <div class="min-w-0">
                    <h4 class="mb-1 text-truncate">{{ $user->name }}</h4>
                    <div class="text-muted">
                        {{ $user->username }} | {{ optional($user->role)->akses ?? 'Unknown' }} | {{ optional($user->departemen)->nama ?? 'Unknown' }}
                    </div>
                    <small class="text-muted" id="periodLabel">Memuat periode...</small>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" id="downloadExcelButton" class="btn btn-success">
                    <i class="ti ti-file-spreadsheet me-1"></i>Excel
                </button>
                <button type="button" id="downloadPdfButton" class="btn btn-danger">
                    <i class="ti ti-file-type-pdf me-1"></i>PDF
                </button>
                <a href="{{ route('admin.user.index') }}" class="btn btn-label-secondary">
                    <i class="ti ti-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-12">
                    <label class="form-label d-block">Tipe Filter Tanggal</label>
                    <input type="hidden" id="filter_type" value="semua">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="filter_type_radio" id="filter_semua" value="semua" checked>
                        <label class="form-check-label" for="filter_semua">Semua</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="filter_type_radio" id="filter_bulan_tahun" value="bulan_tahun">
                        <label class="form-check-label" for="filter_bulan_tahun">Bulan</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="filter_type_radio" id="filter_rentang" value="rentang_tanggal">
                        <label class="form-check-label" for="filter_rentang">Rentang</label>
                    </div>
                </div>

                <div class="col-md-3 filter-bulan-tahun" style="display: none;">
                    <label class="form-label" for="bulan">Bulan</label>
                    <select class="select2 form-select" id="bulan">
                        <option value="*">Semua Bulan</option>
                        @foreach ([
                            1 => 'Januari',
                            2 => 'Februari',
                            3 => 'Maret',
                            4 => 'April',
                            5 => 'Mei',
                            6 => 'Juni',
                            7 => 'Juli',
                            8 => 'Agustus',
                            9 => 'September',
                            10 => 'Oktober',
                            11 => 'November',
                            12 => 'Desember',
                        ] as $value => $label)
                            <option value="{{ $value }}" {{ now()->month == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 filter-bulan-tahun" style="display: none;">
                    <label class="form-label" for="tahun">Tahun</label>
                    <select class="select2 form-select" id="tahun">
                        <option value="*">Semua Tahun</option>
                        @foreach ($tahunAbsensi as $year)
                            <option value="{{ $year }}" {{ now()->year == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 filter-rentang" style="display: none;">
                    <label class="form-label" for="start_date">Dari Tanggal</label>
                    <input type="date" class="form-control" id="start_date" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                </div>
                <div class="col-md-3 filter-rentang" style="display: none;">
                    <label class="form-label" for="end_date">Sampai Tanggal</label>
                    <input type="date" class="form-control" id="end_date" value="{{ now()->endOfMonth()->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label for="kategori_id" class="form-label">Kategori</label>
                    <select class="select2 form-select" id="kategori_id">
                        <option value="*">Semua Kategori</option>
                        @foreach ($kategori as $item)
                            <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="button" id="filterButton" class="btn btn-primary flex-fill">
                        <i class="ti ti-filter me-1"></i>Filter
                    </button>
                    <button type="button" id="resetFilterButton" class="btn btn-label-secondary btn-icon">
                        <i class="ti ti-restore"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="summary-icon bg-label-primary"><i class="ti ti-calendar-check"></i></span>
                    <div>
                        <div class="summary-value" id="totalAbsensi">0</div>
                        <div class="summary-label">Total Absensi</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="summary-icon bg-label-success"><i class="ti ti-cash"></i></span>
                    <div>
                        <div class="summary-value" id="totalNominal">Rp 0</div>
                        <div class="summary-label">Total Nominal</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="summary-icon bg-label-info"><i class="ti ti-clock-hour-4"></i></span>
                    <div>
                        <div class="summary-value" id="totalDurasi">-</div>
                        <div class="summary-label">Total Durasi</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="summary-icon bg-label-warning"><i class="ti ti-calculator"></i></span>
                    <div>
                        <div class="summary-value" id="rataNominal">Rp 0</div>
                        <div class="summary-label">Rata-rata Per Absensi</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Nominal Per Tanggal</h5>
                </div>
                <div class="card-body">
                    <div id="dailyNominalChart" class="chart-box"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Nominal Per Kategori</h5>
                </div>
                <div class="card-body">
                    <div id="categoryChart" class="chart-box"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Rekap Kategori</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="table-kategori-summary">
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th class="text-end">Nominal Satuan</th>
                        <th class="text-end">Jumlah Absensi</th>
                        <th class="text-end">Total Durasi</th>
                        <th class="text-end">Total Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="5" class="text-center text-muted">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card" id="card-detail-absensi">
        <div class="card-header">
            <h5 class="mb-0">Detail Absensi</h5>
        </div>
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table table-hover" id="table-detail-absensi">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Datang</th>
                        <th>Pulang</th>
                        <th>Kategori</th>
                        <th>Durasi Efektif</th>
                        <th>Nominal</th>
                        <th>Keterangan</th>
                        @unless ($isStaff)
                            <th>Action</th>
                        @endunless
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    @unless ($isStaff)
        @include('admin.absensi.edit')
    @endunless
@endsection

@push('scripts')
    <script>
        var dailyNominalChart = null;
        var categoryChart = null;

        function filterParams() {
            return {
                filter_type: $('#filter_type').val(),
                bulan: $('#bulan').val() || '*',
                tahun: $('#tahun').val() || '*',
                start_date: $('#start_date').val() || '',
                end_date: $('#end_date').val() || '',
                kategori_id: $('#kategori_id').val() || '*',
            };
        }

        function formatRupiah(value) {
            return 'Rp ' + Number(value || 0).toLocaleString('id-ID');
        }

        function escapeHtml(value) {
            return $('<div>').text(value || '').html();
        }

        function renderEmptyChart(selector, text) {
            $(selector).html('<div class="d-flex align-items-center justify-content-center h-100 text-muted">' + text + '</div>');
        }

        function renderDailyChart(rows) {
            if (dailyNominalChart) {
                dailyNominalChart.destroy();
                dailyNominalChart = null;
            }

            if (!rows.length) {
                renderEmptyChart('#dailyNominalChart', 'Tidak ada data');
                return;
            }

            $('#dailyNominalChart').empty();
            dailyNominalChart = new ApexCharts(document.querySelector('#dailyNominalChart'), {
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: { show: false }
                },
                series: [{
                    name: 'Nominal',
                    data: rows.map(function(row) {
                        return row.total_nominal;
                    })
                }],
                xaxis: {
                    categories: rows.map(function(row) {
                        return row.tanggal_label;
                    })
                },
                yaxis: {
                    labels: {
                        formatter: function(value) {
                            return formatRupiah(value);
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return formatRupiah(value);
                        }
                    }
                },
                dataLabels: { enabled: false },
                colors: ['#7367f0'],
                plotOptions: {
                    bar: {
                        borderRadius: 6,
                        columnWidth: '45%'
                    }
                }
            });
            dailyNominalChart.render();
        }

        function renderCategoryChart(rows) {
            if (categoryChart) {
                categoryChart.destroy();
                categoryChart = null;
            }

            if (!rows.length) {
                renderEmptyChart('#categoryChart', 'Tidak ada data');
                return;
            }

            $('#categoryChart').empty();
            categoryChart = new ApexCharts(document.querySelector('#categoryChart'), {
                chart: {
                    type: 'donut',
                    height: 300
                },
                labels: rows.map(function(row) {
                    return row.kode + ' - ' + row.nama;
                }),
                series: rows.map(function(row) {
                    return row.total_nominal;
                }),
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return formatRupiah(value);
                        }
                    }
                },
                dataLabels: {
                    formatter: function(value) {
                        return value.toFixed(1) + '%';
                    }
                },
                colors: ['#28c76f', '#00bad1', '#ff9f43', '#ea5455', '#7367f0']
            });
            categoryChart.render();
        }

        function renderCategoryTable(rows) {
            if (!rows.length) {
                $('#table-kategori-summary tbody').html('<tr><td colspan="5" class="text-center text-muted">Tidak ada data</td></tr>');
                return;
            }

            let html = '';
            rows.forEach(function(row) {
                html += `
                    <tr>
                        <td>
                            <div class="fw-semibold">${escapeHtml(row.kode)} - ${escapeHtml(row.nama)}</div>
                        </td>
                        <td class="text-end">${formatRupiah(row.nominal_per_absensi)}</td>
                        <td class="text-end">${Number(row.total_absensi).toLocaleString('id-ID')}</td>
                        <td class="text-end">${row.durasi_teks || '-'}</td>
                        <td class="text-end fw-semibold">${formatRupiah(row.total_nominal)}</td>
                    </tr>
                `;
            });

            $('#table-kategori-summary tbody').html(html);
        }

        function loadSummary() {
            return $.get("{{ route('admin.user.absensi.summary', $user) }}", filterParams()).done(function(response) {
                $('#periodLabel').text(response.period_label);
                $('#totalAbsensi').text(Number(response.summary.total_absensi).toLocaleString('id-ID'));
                $('#totalNominal').text(response.summary.total_nominal_formatted);
                $('#totalDurasi').text(response.summary.total_durasi_teks || '-');
                $('#rataNominal').text(response.summary.rata_nominal_formatted);

                renderDailyChart(response.daily || []);
                renderCategoryChart(response.categories || []);
                renderCategoryTable(response.categories || []);
            });
        }

        function exportUrl(baseUrl) {
            return baseUrl + '?' + new URLSearchParams(filterParams()).toString();
        }

        var dataTable = $('#table-detail-absensi').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.user.absensi.data', $user) }}",
                method: "GET",
                data: function(d) {
                    $.extend(d, filterParams());
                }
            },
            columns: [{
                    data: 'tgl_absen',
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    searchable: false,
                    orderable: false
                },
                { data: 'tgl_absen', name: 'absensi.tgl_absen' },
                { data: 'pagi', name: 'absensi.pagi' },
                { data: 'sore', name: 'absensi.sore' },
                { data: 'kategori_nama', name: 'kategori.nama' },
                { data: 'durasi_efektif', name: 'durasi_efektif', searchable: false, orderable: false },
                { data: 'kategori_nominal', name: 'kategori.nominal' },
                { data: 'keterangan', name: 'absensi.keterangan', defaultContent: '-' }
                @unless ($isStaff)
                    , { data: 'action', name: 'action', searchable: false, orderable: false }
                @endunless
            ],
            order: [[1, 'desc']],
            scrollX: true
        });

        function reloadDetail() {
            loadSummary();
            dataTable.ajax.reload(null, false);
        }

        function setTanggalFilterMode(mode, reload = true) {
            $('#filter_type').val(mode);

            if (mode === 'bulan_tahun') {
                $('#bulan').val('{{ now()->month }}').trigger('change.select2');
                $('#tahun').val('{{ now()->year }}').trigger('change.select2');
                $('.filter-bulan-tahun').show();
                $('.filter-rentang').hide();
            } else if (mode === 'rentang_tanggal') {
                $('#start_date').val('{{ now()->startOfMonth()->format('Y-m-d') }}');
                $('#end_date').val('{{ now()->endOfMonth()->format('Y-m-d') }}');
                $('.filter-bulan-tahun').hide();
                $('.filter-rentang').show();
            } else {
                $('.filter-bulan-tahun').hide();
                $('.filter-rentang').hide();
            }

            if (reload) {
                reloadDetail();
            }
        }

        $('input[name="filter_type_radio"]').change(function() {
            setTanggalFilterMode($(this).val());
        });

        $('#filterButton, #bulan, #tahun, #start_date, #end_date, #kategori_id').on('click change', function() {
            reloadDetail();
        });

        $('#resetFilterButton').on('click', function() {
            $('#filter_semua').prop('checked', true);
            setTanggalFilterMode('semua', false);
            $('#kategori_id').val('*').trigger('change.select2');
            $('#start_date').val('{{ now()->startOfMonth()->format('Y-m-d') }}');
            $('#end_date').val('{{ now()->endOfMonth()->format('Y-m-d') }}');
            reloadDetail();
        });

        $('#downloadExcelButton').on('click', function() {
            window.location.href = exportUrl("{{ route('admin.user.absensi.export.excel', $user) }}");
        });

        $('#downloadPdfButton').on('click', function() {
            window.location.href = exportUrl("{{ route('admin.user.absensi.export.pdf', $user) }}");
        });

        $(document).ajaxSuccess(function(event, xhr, settings) {
            if (settings.url === "{{ route('admin.absensi.edit') }}") {
                loadSummary();
            }
        });

        loadSummary();
    </script>
@endpush
