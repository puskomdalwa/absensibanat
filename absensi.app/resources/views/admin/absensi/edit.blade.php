<!-- Modal edit record -->
<div class="offcanvas offcanvas-end" id="edit-record">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Edit Record</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body flex-grow-1">
        <form class="record pt-0 row g-2" id="form-edit-record" action="{{ route('admin.absensi.edit') }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="id">
            @include('admin.absensi.form')
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

            $(document).on('click', '.Btnedit', function() {
                const id = $(this).data('id');
                const users_id = $(this).data('users_id');
                const user_name = $(this).data('user_name');
                const tgl = $(this).data('tgl');
                const pagi = $(this).data('pagi');
                const sore = $(this).data('sore');
                const device_id = $(this).data('device_id');
                const verify_id = $(this).data('verify_id');
                const latitude = $(this).data('latitude');
                const longitude = $(this).data('longitude');
                const ket = $(this).data('ket');

                $('#form-edit-record .user_name').hide();
                $('#form-edit-record [name="id"]').val(id);
                $('#form-edit-record [name="user_id"]').val(users_id);
                $('#form-edit-record [name="user_name"]').val(user_name);
                $('#form-edit-record [name="tgl"]').val(tgl);
                $('#form-edit-record [name="jam"]').val(pagi);
                $('#form-edit-record [name="jam_pulang"]').val(sore);
                $('#form-edit-record [name="device_id"]').val(device_id).change();
                $('#form-edit-record [name="verify_id"]').val(verify_id).change();
                $('#form-edit-record [name="latitude"]').val(latitude);
                $('#form-edit-record [name="longitude"]').val(longitude);
                $('#form-edit-record [name="ket"]').val(ket);

                offCanvasEditRecord.show();
            });

            $(document).on('submit', '#form-edit-record', function(e) {
                e.preventDefault();
                ajaxRequestDt(e, offCanvasEditRecord, typeof dataTable !== 'undefined' ? dataTable : null);
            });
        });
    </script>
@endpush
