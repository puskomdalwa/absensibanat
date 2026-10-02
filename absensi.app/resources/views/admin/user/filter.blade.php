<div class="card mb-4 border-0 shadow-sm" style="border-radius: 20px; border: 1px solid rgba(251, 113, 133, 0.2) !important;">
    <div class="card-body p-4">
        <div class="d-flex align-items-center mb-3">
            <i class="ti ti-filter me-2 fs-5" style="color: #e11d48;"></i>
            <h6 class="mb-0 fw-bold" style="color: #e11d48;">Filter Data Civitas</h6>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Role Civitas: </label>
                <select class="select2 form-select" id="role_id">
                    <option value="*">Semua Role</option>
                    @foreach ($role as $item)
                        <option value="{{ $item->id }}">{{ $item->akses }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Departemen: </label>
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
