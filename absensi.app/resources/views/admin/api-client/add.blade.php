<!-- Modal new record -->
<div class="offcanvas offcanvas-end" id="new-record">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Tambah API Client Baru</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body flex-grow-1">
        <form class="record pt-0 row g-3" id="form-new-record" action="{{ route('admin.api_client.store') }}" method="POST">
            @csrf
            @include('admin.api-client.form')
            
            <div class="col-sm-12 alert alert-info mt-2 mb-0" role="alert">
                <div class="d-flex">
                    <i class="ti ti-info-circle me-2 mt-1"></i>
                    <small><b>Catatan:</b> API Key dan Secret Key akan digenerate otomatis secara acak oleh sistem setelah form ini disimpan.</small>
                </div>
            </div>

            <div class="col-sm-12 mt-4">
                <button type="submit" class="btn btn-primary data-submit me-sm-4 me-1">Submit</button>
                <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Cancel</button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
    <script>
        $(document).ready(function() {
            var offCanvasNewRecord = new bootstrap.Offcanvas($('#new-record'));

            $(document).on('click', '#new-record-button', function() {
                $('#form-new-record')[0].reset();
                $('#form-new-record #is_active').prop('checked', true);
                offCanvasNewRecord.show();
                $('#form-new-record [name="name"]').focus();
            });

            $(document).on('submit', '#form-new-record', function(e) {
                e.preventDefault();
                ajaxRequestDt(e, offCanvasNewRecord, dataTable);
            });
        });
    </script>
@endpush
