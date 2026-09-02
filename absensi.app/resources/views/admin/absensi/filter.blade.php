<div class="card mb-6 p-0">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <label class="form-label">Role: </label>
                <select class="select2 form-select" id="role_id">
                    <option value="*">Semua Role</option>
                    @foreach ($role as $item)
                        <option value="{{ $item->id }}">{{ $item->akses }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Departemen: </label>
                <select class="select2 form-select" id="departemen_id">
                    <option value="*">Semua Departemen</option>
                    @foreach ($departemen as $item)
                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-12 mt-3">
                <label class="form-label d-block">Tipe Filter Tanggal: </label>
                <input type="hidden" id="filter_type" value="semua">
                <div class="form-check form-check-inline mt-2">
                    <input class="form-check-input" type="radio" name="filter_type_radio" id="filter_semua" value="semua" checked>
                    <label class="form-check-label" for="filter_semua">Semua</label>
                </div>
                <div class="form-check form-check-inline mt-2">
                    <input class="form-check-input" type="radio" name="filter_type_radio" id="filter_bulan_tahun" value="bulan_tahun">
                    <label class="form-check-label" for="filter_bulan_tahun">Bulan</label>
                </div>
                <div class="form-check form-check-inline mt-2">
                    <input class="form-check-input" type="radio" name="filter_type_radio" id="filter_rentang" value="rentang_tanggal">
                    <label class="form-check-label" for="filter_rentang">Rentang</label>
                </div>
            </div>

            <div class="col-md-6 mt-3 filter-bulan-tahun" style="display: none;">
                <label class="form-label">Bulan: </label>
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
            <div class="col-md-6 mt-3 filter-bulan-tahun" style="display: none;">
                <label class="form-label">Tahun: </label>
                <select class="select2 form-select" id="tahun">
                    <option value="*">Semua Tahun</option>
                    @foreach ($tahunAbsensi as $year)
                        <option value="{{ $year }}" {{ now()->year == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 mt-3 filter-rentang" style="display: none;">
                <label class="form-label">Dari Tanggal: </label>
                <input type="date" class="form-control" id="start_date" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
            </div>
            <div class="col-md-6 mt-3 filter-rentang" style="display: none;">
                <label class="form-label">Sampai Tanggal: </label>
                <input type="date" class="form-control" id="end_date" value="{{ now()->endOfMonth()->format('Y-m-d') }}">
            </div>

        </div>
    </div>
</div>

@push('scripts')
    <script>
        var departemenId = params.get('departemen');
        if (departemenId) {
            $('#departemen_id').val(departemenId).change();
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
                dataTable.ajax.reload(null, false);
            }
        }

        $('input[name="filter_type_radio"]').change(function() {
            setTanggalFilterMode($(this).val());
        });

        $('#role_id, #departemen_id, #bulan, #tahun, #start_date, #end_date').change(function(e) {
            dataTable.ajax.reload(null, false);
        });
    </script>
@endpush
