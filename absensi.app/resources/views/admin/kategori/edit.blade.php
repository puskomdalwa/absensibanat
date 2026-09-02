<div class="offcanvas offcanvas-end" id="edit-record">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Edit Record</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body flex-grow-1">
        <form class="record pt-0 row g-2" id="form-edit-record" action="{{ route('admin.kategori.update') }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="id">
            @include('admin.kategori.form')
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
                $('#form-edit-record [name="id"]').val($(this).data('id'));
                $('#form-edit-record [name="nama"]').val($(this).data('nama'));
                $('#form-edit-record [name="kode"]').val($(this).data('kode'));
                $('#form-edit-record [name="selisih"]').val($(this).data('selisih'));
                $('#form-edit-record [name="nominal"]').val($(this).data('nominal'));
                $('#form-edit-record [name="keterangan"]').val($(this).data('keterangan'));

                offCanvasEditRecord.show();
                $('#form-edit-record [name="nama"]').focus();
            });

            $(document).on('submit', '#form-edit-record', function(e) {
                e.preventDefault();
                ajaxRequestDt(e, offCanvasEditRecord, typeof dataTable !== 'undefined' ? dataTable : null);
            });
        });
    </script>
@endpush
