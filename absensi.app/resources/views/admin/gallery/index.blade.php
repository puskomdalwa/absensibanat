@extends('layouts.admin.template')
@section('title', 'Galeri Foto')

@push('css')
    <style>
        .upload-zone {
            border: 2px dashed #ccc;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .upload-zone:hover,
        .upload-zone.dragover {
            border-color: #7367f0;
            background: #f0efff;
        }

        .upload-zone i {
            font-size: 48px;
            color: #7367f0;
            margin-bottom: 15px;
        }

        .preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }

        .preview-item {
            position: relative;
            width: 100px;
            height: 100px;
        }

        .preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
        }

        .preview-item .remove-preview {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            font-size: 12px;
            cursor: pointer;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .gallery-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .gallery-item img {
            width: 100%;
            height: 120px;
            object-fit: cover;
        }
    </style>
@endpush

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-images me-2"></i>Galeri Foto</h5>
        </div>
        <div class="card-body">
            <!-- Upload Zone -->
            <div class="upload-zone" id="uploadZone">
                <i class="fas fa-cloud-upload-alt"></i>
                <h5>Drag & Drop foto di sini</h5>
                <p class="text-muted mb-2">atau klik untuk memilih file</p>
                <small class="text-muted">Format: JPG, PNG, GIF, WEBP (max 5MB per file)</small>
                <input type="file" id="fileInput" multiple accept="image/*" style="display: none;">
            </div>

            <!-- Preview sebelum upload -->
            <div class="preview-container" id="previewContainer"></div>

            <!-- Tombol upload -->
            <div class="mt-3" id="uploadActions" style="display: none;">
                <button type="button" class="btn btn-primary" id="btnUpload">
                    <i class="fas fa-upload me-1"></i> Upload Foto
                </button>
                <button type="button" class="btn btn-secondary" id="btnClear">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
            </div>
        </div>
    </div>

    <!-- Daftar Foto -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-photo-video me-2"></i>Daftar Foto</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="galleryTable">
                    <thead>
                        <tr>
                            <th>Preview</th>
                            <th>Nama File</th>
                            <th>Tanggal Upload</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function () {
            let selectedFiles = [];

            const $uploadZone = $('#uploadZone');
            const $fileInput = $('#fileInput');
            const $previewContainer = $('#previewContainer');
            const $uploadActions = $('#uploadActions');

            // Click to select files
            $uploadZone.on('click', function () {
                $fileInput.click();
            });

            // File input change
            $fileInput.on('change', function (e) {
                handleFiles(e.target.files);
            });

            // Drag & Drop
            $uploadZone.on('dragover', function (e) {
                e.preventDefault();
                $(this).addClass('dragover');
            });

            $uploadZone.on('dragleave', function (e) {
                e.preventDefault();
                $(this).removeClass('dragover');
            });

            $uploadZone.on('drop', function (e) {
                e.preventDefault();
                $(this).removeClass('dragover');
                handleFiles(e.originalEvent.dataTransfer.files);
            });

            function handleFiles(files) {
                for (let file of files) {
                    if (file.type.startsWith('image/')) {
                        selectedFiles.push(file);
                        showPreview(file, selectedFiles.length - 1);
                    }
                }
                updateUploadActions();
            }

            function showPreview(file, index) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const $item = $(`
                    <div class="preview-item" data-index="${index}">
                        <img src="${e.target.result}" alt="Preview">
                        <button type="button" class="remove-preview">&times;</button>
                    </div>
                `);
                    $previewContainer.append($item);
                };
                reader.readAsDataURL(file);
            }

            function updateUploadActions() {
                if (selectedFiles.length > 0) {
                    $uploadActions.show();
                } else {
                    $uploadActions.hide();
                }
            }

            // Remove preview
            $(document).on('click', '.remove-preview', function () {
                const $item = $(this).parent();
                const index = parseInt($item.data('index'));
                selectedFiles[index] = null;
                $item.remove();

                // Check if any files remain
                if (selectedFiles.filter(f => f !== null).length === 0) {
                    selectedFiles = [];
                    updateUploadActions();
                }
            });

            // Clear all
            $('#btnClear').on('click', function () {
                selectedFiles = [];
                $previewContainer.empty();
                $fileInput.val('');
                updateUploadActions();
            });

            // Upload
            $('#btnUpload').on('click', function () {
                const formData = new FormData();
                let hasFiles = false;

                selectedFiles.forEach(file => {
                    if (file) {
                        formData.append('images[]', file);
                        hasFiles = true;
                    }
                });

                if (!hasFiles) {
                    Swal.fire('Error', 'Pilih foto terlebih dahulu!', 'error');
                    return;
                }

                formData.append('_token', '{{ csrf_token() }}');

                const $btn = $(this);
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Uploading...');

                $.ajax({
                    url: "{{ route('admin.gallery.store') }}",
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        Swal.fire('Sukses', response.message, 'success');
                        selectedFiles = [];
                        $previewContainer.empty();
                        $fileInput.val('');
                        updateUploadActions();
                        galleryTable.ajax.reload();
                    },
                    error: function (xhr) {
                        Swal.fire('Error', 'Gagal upload foto!', 'error');
                    },
                    complete: function () {
                        $btn.prop('disabled', false).html('<i class="fas fa-upload me-1"></i> Upload Foto');
                    }
                });
            });

            // DataTable for gallery list
            const galleryTable = $('#galleryTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.gallery.data') }}",
                columns: [
                    { data: 'image_preview', name: 'image_preview', orderable: false, searchable: false },
                    { data: 'original_name', name: 'original_name' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[2, 'desc']]
            });

            // Delete
            $(document).on('click', '.delete-btn', function () {
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Hapus foto ini?',
                    text: 'Foto akan dihapus permanen!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.gallery.delete') }}",
                            type: 'DELETE',
                            data: {
                                id: id,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (response) {
                                Swal.fire('Dihapus!', response.message, 'success');
                                galleryTable.ajax.reload();
                            },
                            error: function () {
                                Swal.fire('Error', 'Gagal menghapus foto!', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
