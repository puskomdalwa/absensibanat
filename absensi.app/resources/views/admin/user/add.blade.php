<!-- Modal new record -->
<div class="offcanvas offcanvas-end" id="new-record">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Daftar User Baru</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body flex-grow-1">
        <form class="record pt-0 row g-2" id="form-new-record" action="{{ route('admin.user.store') }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <div class="col-sm-12">
                <label class="form-label" for="id">ID</label>
                <div class="input-group input-group-merge">
                    <input type="text" class="form-control" name="id" placeholder="Type here..."
                        aria-label="Type here..." aria-describedby="id2" required />
                </div>
            </div>
            @include('admin.user.form', ['showPassword' => true, 'passwordRequired' => true])
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
            var croppedImageNew = $('#form-new-record [name="photo"]');
            var uploadImageNew = $('#form-new-record input[name="upload_photo"]');
            var formRemoveImageNew = $('#form-new-record .remove-image');
            var formCroppedImageNew = $('#form-new-record .cropped-image');
            var cropperNew;

            $('#form-new-record .cropped-image').on('click', function() {
                uploadImageNew.click();
            });

            $('#form-new-record .remove-image').on('click', function() {
                uploadImageNew.val('');
                croppedImageNew.val('');
                formRemoveImageNew.addClass('d-none');
                formCroppedImageNew.attr('src', "{{ asset('admin_assets/img/avatars/profile.png') }}");
            });

            // Handle Image Upload
            uploadImageNew.on('change', function(e) {
                const file = e.target.files[0]; // Mendapatkan file yang dipilih
                if (file) {
                    // Validasi apakah file adalah gambar
                    if (!file.type.startsWith('image/')) {
                        alert('Please upload a valid image file.');
                        formRemoveImageNew.click();
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(event) {
                        imageToCrop.attr('src', event.target.result);
                        cropModal.modal('show');

                        cropModal.on('shown.bs.modal', function() {
                            if (cropperNew) {
                                cropperNew.destroy(); // Hapus instance sebelumnya
                            }
                            cropperNew = new Cropper(imageToCrop[0], {
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
                    formRemoveImageNew.click();
                }
            });

            // Handle Crop Button
            cropButton.on('click', function(e) {
                if (cropperNew) {
                    const canvas = cropperNew.getCroppedCanvas({
                        width: 300,
                        height: 300,
                    });

                    // var croppedImageURL = canvas.toDataURL();
                    var croppedImageURL = canvas.toDataURL('image/jpeg', 0.8);;
                    croppedImageNew.val(croppedImageURL);
                    cropModal.modal('hide');

                    formCroppedImageNew.attr('src', croppedImageURL);
                    formRemoveImageNew.removeClass('d-none');
                }
            });
        });
    </script>
@endpush
@push('scripts')
    <script>
        $(document).ready(function() {
            var offCanvasNewRecord = new bootstrap.Offcanvas($('#new-record'));

            $(document).on('click', '#new-record-button', function() {
                // $('#form-new-record [name="name"]').val('Tes');
                // $('#form-new-record [name="email"]').val('tes@gmail.com');
                // $('#form-new-record [name="username"]').val('tes');
                // $('#form-new-record [name="jenis_kelamin"]').val('Laki-laki').change();
                // $('#form-new-record [name="role_id"]').val(2).change();

                offCanvasNewRecord.show();
                $('#form-new-record [name="username"]').focus();
            });

            $(document).on('submit', '#form-new-record', function(e) {
                e.preventDefault();
                ajaxRequestDt(e, offCanvasNewRecord, dataTable);
            });
        });
    </script>
@endpush
