<!-- Modal edit record -->
<div class="offcanvas offcanvas-end" id="edit-record">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Edit API Client</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body flex-grow-1">
        <form class="record pt-0 row g-3" id="form-edit-record" action="{{ route('admin.api_client.update') }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="id">
            @include('admin.api-client.form')

            <div class="col-sm-12 border-top pt-3 mt-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="regenerate_secret" name="regenerate_secret" value="1">
                    <label class="form-check-label text-danger fw-semibold" for="regenerate_secret">Regenerate Secret Key Baru</label>
                </div>
                <small class="text-muted d-block mt-1">Centang jika ingin mengganti Secret Key dengan yang baru (kredensial lama tidak akan berlaku lagi).</small>
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
            var offCanvasEditRecord = new bootstrap.Offcanvas($('#edit-record'));

            $(document).on('click', '.edit-record-button', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const description = $(this).data('description');
                const isActive = $(this).data('is_active');

                $('#form-edit-record [name="id"]').val(id);
                $('#form-edit-record [name="name"]').val(name);
                $('#form-edit-record [name="description"]').val(description);
                $('#form-edit-record [name="is_active"]').prop('checked', isActive == '1');
                $('#form-edit-record #regenerate_secret').prop('checked', false);

                offCanvasEditRecord.show();
                $('#form-edit-record [name="name"]').focus();
            });

            $(document).on('submit', '#form-edit-record', function(e) {
                e.preventDefault();
                ajaxRequestDt(e, offCanvasEditRecord, typeof dataTable !== 'undefined' ? dataTable : null);
            });
        });
    </script>
@endpush
