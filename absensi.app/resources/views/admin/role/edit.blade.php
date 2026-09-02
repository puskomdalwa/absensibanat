<!-- Modal edit record -->
<div class="offcanvas offcanvas-end" id="edit-record">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Edit Record</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body flex-grow-1">
        <form class="record pt-0 row g-2" id="form-edit-record" action="{{ route('admin.role.update') }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="id">
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
            var offCanvasEditRecord = new bootstrap.Offcanvas($('#edit-record'));

            $(document).on('click', '.edit-record-button', function() {
                if (!$('#form-edit-record .remove-image').hasClass('d-none')) {
                    $('#form-edit-record .remove-image').addClass('d-none');
                }

                const id = $(this).data('id');
                const akses = $(this).data('akses');

                $('#form-edit-record [name="id"]').val(id);
                $('#form-edit-record [name="akses"]').val(akses);

                offCanvasEditRecord.show();
                $('#form-edit-record [name="akses"]').focus();
            });

            $(document).on('submit', '#form-edit-record', function(e) {
                e.preventDefault();
                ajaxRequestDt(e, offCanvasEditRecord, typeof dataTable !== 'undefined' ? dataTable : null);
            });
        });
    </script>
@endpush
