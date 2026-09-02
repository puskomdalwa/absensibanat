<!-- Modal new record -->
<div class="offcanvas offcanvas-end" id="new-record">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">New Record</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body flex-grow-1">
        <form class="record pt-0 row g-2" id="form-new-record" action="{{ route('admin.role.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.role.form')
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
                offCanvasNewRecord.show();
                $('#form-new-record [name="akses"]').focus();
            });

            $(document).on('submit', '#form-new-record', function(e) {
                e.preventDefault();
                ajaxRequestDt(e, offCanvasNewRecord, dataTable);
            });
        });
    </script>
@endpush
