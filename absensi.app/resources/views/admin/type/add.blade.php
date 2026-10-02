<!-- Modal new record -->
<div class="offcanvas offcanvas-end" id="new-record">
    <div class="offcanvas-header border-bottom pb-3" style="border-color: rgba(251, 113, 133, 0.2) !important;">
        <div class="d-flex align-items-center gap-2">
            <span class="badge rounded p-2" style="background: rgba(225, 29, 72, 0.12); color: #e11d48;">
                <i class="ti ti-tag ti-sm"></i>
            </span>
            <h5 class="offcanvas-title fw-bold">Tambah Tipe User Baru</h5>
        </div>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body flex-grow-1">
        <form class="record pt-0 row g-2" id="form-new-record" action="{{ route('admin.type.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.type.form')
            <div class="col-sm-12 mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary data-submit flex-grow-1"><i class="ti ti-check me-1"></i> Simpan Tipe User</button>
                <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas"><i class="ti ti-x me-1"></i> Batal</button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
    <script>
        $(document).ready(function() {
            var offCanvasNewRecord = new bootstrap.Offcanvas($('#new-record'));

            $(document).on('click', '#new-record-button', function() {
                offCanvasNewRecord.show();
                $('#form-new-record [name="nama"]').focus();
            });

            $(document).on('submit', '#form-new-record', function(e) {
                e.preventDefault();
                ajaxRequestDt(e, offCanvasNewRecord, dataTable);
            });
        });
    </script>
@endpush
