<div class="card mb-6 p-0">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-12">
                <label class="form-label">Role: </label>
                <select class="select2 form-select" id="role_id">
                    <option value="*">Semua Role</option>
                    @foreach ($role as $item)
                        <option value="{{ $item->id }}">{{ $item->akses }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-12 mt-3">
                <label class="form-label">Departemen: </label>
                <select class="select2 form-select" id="departemen_id">
                    <option value="*">Semua Departemen</option>
                    @foreach ($departemen as $item)
                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $('#role_id').change(function (e) { 
            dataTable.ajax.reload(null, false);
        });
        $('#departemen_id').change(function (e) { 
            dataTable.ajax.reload(null, false);
        });
    </script>
@endpush
