<!-- Modal edit record -->
<div class="offcanvas offcanvas-end" id="edit-record">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Update Profile</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body flex-grow-1">
        <form class="record pt-0 row g-2" id="form-edit-record" action="{{ route('admin.profile.update') }}"
            method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="id">
            @include('admin.user.form')
            <small>Kosongi <b>password</b> jika tidak ingin mengganti !!</small>
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
            var croppedImageEdit = $('#form-edit-record [name="photo"]');
            var uploadImageEdit = $('#form-edit-record input[name="upload_photo"]');
            var formRemoveImageEdit = $('#form-edit-record .remove-image');
            var formCroppedImageEdit = $('#form-edit-record .cropped-image');
            var cropperEdit;

            $('#form-edit-record .cropped-image').on('click', function() {
                uploadImageEdit.click();
            });

            $('#form-edit-record .remove-image').on('click', function() {
                uploadImageEdit.val('');
                croppedImageEdit.val('');
                formRemoveImageEdit.addClass('d-none');
                formCroppedImageEdit.attr('src', "{{ asset('admin/assets/img/avatars/profile.png') }}");
            });

            // Handle Image Upload
            uploadImageEdit.on('change', function(e) {
                const file = e.target.files[0]; // Mendapatkan file yang dipilih
                if (file) {
                    // Validasi apakah file adalah gambar
                    if (!file.type.startsWith('image/')) {
                        alert('Please upload a valid image file.');
                        formRemoveImageEdit.click();
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(event) {
                        imageToCrop.attr('src', event.target.result);
                        cropModal.modal('show');

                        cropModal.on('shown.bs.modal', function() {
                            if (cropperEdit) {
                                cropperEdit.destroy(); // Hapus instance sebelumnya
                            }
                            cropperEdit = new Cropper(imageToCrop[0], {
                                aspectRatio: 1, // Aspect ratio (kotak 1:1)
                                viewMode: 1, // Mode tampilan (gambar tidak boleh keluar area crop)
                                movable: true, // Gambar bisa dipindahkan
                                zoomable: false, // Zoom diperbolehkan
                                scalable: true, // Skala diperbolehkan
                                cropBoxResizable: true, // Crop box bisa diubah ukurannya
                            });
                        });
                    };

                    reader.readAsDataURL(file);
                } else {
                    alert('No file selected.');
                    formRemoveImageEdit.click();
                }
            });

            // Handle Crop Button
            cropButton.on('click', function(e) {
                if (cropperEdit) {
                    const canvas = cropperEdit.getCroppedCanvas({
                        width: 300,
                        height: 300,
                    });

                    // var croppedImageURL = canvas.toDataURL();
                    var croppedImageURL = canvas.toDataURL('image/jpeg', 0.8);;
                    croppedImageEdit.val(croppedImageURL);
                    cropModal.modal('hide');

                    formCroppedImageEdit.attr('src', croppedImageURL);
                    formRemoveImageEdit.removeClass('d-none');
                }
            });
        });
    </script>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            var offCanvasEditRecord = new bootstrap.Offcanvas($('#edit-record'));

            $(document).on('click', '.edit-record-button', function() {
                if (!$('#form-edit-record .remove-image').hasClass('d-none')) {
                    $('#form-edit-record .remove-image').addClass('d-none');
                }

                const id = $(this).data('id');
                const name = $(this).data('name');
                const email = $(this).data('email');
                const username = $(this).data('username');
                const sex = $(this).data('sex');
                const photo = $(this).data('photo');
                const affiliate = $(this).data('affiliate');
                const telp = $(this).data('telp');


                $('#form-edit-record [name="id"]').val(id);
                $('#form-edit-record [name="name"]').val(name);
                $('#form-edit-record [name="email"]').val(email);
                $('#form-edit-record [name="username"]').val(username);
                $('#form-edit-record [name="affiliate"]').val(affiliate);
                $('#form-edit-record [name="telp"]').val(telp);
                $('#form-edit-record [name="sex"]').val(sex).change();
                if (photo) {
                    $('#form-edit-record .cropped-image').attr('src', "{{ asset('photo/') }}" + "/" +
                        photo);
                } else {
                    $('#form-edit-record .cropped-image').attr('src',
                        "{{ asset('admin/assets/img/avatars/profile.png') }}");
                }

                offCanvasEditRecord.show();
                $('#form-edit-record [name="username"]').focus();
            });

            $('#form-edit-record').submit(function (e) { 
                $('#form-edit-record button[type="submit"]').attr('disabled', true);
            });

        });
    </script>
@endpush
