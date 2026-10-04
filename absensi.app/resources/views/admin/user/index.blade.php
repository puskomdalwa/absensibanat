@extends('layouts.admin.template')
@section('title', 'User')
@section('content')
    <!-- BANAT LUXURY HERO HEADER -->
    <div class="card mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, rgba(225, 29, 72, 0.05) 0%, rgba(251, 113, 133, 0.12) 100%); border-radius: 20px; border: 1px solid rgba(251, 113, 133, 0.25) !important;">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; background: linear-gradient(135deg, #fb7185 0%, #e11d48 100%); border-radius: 16px; box-shadow: 0 4px 14px rgba(225, 29, 72, 0.35);">
                        <i class="ti ti-users text-white" style="font-size: 1.8rem;"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 fw-bold text-heading">Manajemen Data Civitas & Pengguna</h4>
                        <p class="mb-0 text-muted small">Kelola seluruh data akun dosen, staff, dan civitas akademika Banat UII Dalwa secara terstruktur</p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge" style="background: linear-gradient(135deg, #fb7185 0%, #e11d48 100%); color: white; padding: 8px 16px; border-radius: 50px; font-weight: 600;">
                        <i class="ti ti-shield-check me-1"></i> Data Master Pengguna
                    </span>
                </div>
            </div>
        </div>
    </div>

    @unless ($isStaff)
        @include('admin.user.import')
    @endunless
    @include('admin.user.filter')
    <div class="card shadow-sm border-0" id="card-user" style="border-radius: 20px; border: 1px solid rgba(251, 113, 133, 0.2) !important;">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table table-hover" id="table-1">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th style="width: 40px;" class="text-center">
                            <input type="checkbox" class="form-check-input" id="check-all-users" title="Pilih Semua">
                        </th>
                        <th>User</th>
                        <th>Username</th>
                        <th>Gender</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    @include('admin.user.add')
    @include('admin.user.edit')

    <!-- Modal for Cropping -->
    <div class="modal fade" id="cropModal" tabindex="-1" aria-labelledby="cropModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cropModalLabel">Crop Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="img-container">
                        <img id="imageToCrop" alt="Image to Crop" style="max-width: 100%;" />
                    </div>
                </div>
                <div class="modal-footer mt-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="cropButton" class="btn btn-primary">Crop</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const cropModal = $('#cropModal');
        const imageToCrop = $('#imageToCrop');
        const cropButton = $('#cropButton');
    </script>
@endpush

@push('scripts')
    <script>
        $(document).on('submit', '.form-delete-record', function(e) {
            e.preventDefault();
            var form = $(e.target);
            var id = form.find('input[name="id"]').val();
            var name = form.find('input[name="name"]').val() || 'Pengguna Ini';

            Swal.fire({
                title: `Hapus Pengguna: ${name}?`,
                html: `
                    <p class="text-muted small mb-3">Tindakan ini akan menghapus akun pengguna dari sistem.</p>
                    <div class="card p-3 text-start mb-2" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px;">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="swal-delete-absensi-single" style="cursor: pointer;">
                            <label class="form-check-label fw-bold text-danger ms-2" for="swal-delete-absensi-single" style="cursor: pointer;">
                                <i class="ti ti-trash me-1"></i>Ikut hapus riwayat absensi
                            </label>
                        </div>
                        <div class="text-muted small mt-1 ms-4" style="font-size: 0.78rem;">
                            Biarkan <strong>tidak dicentang</strong> agar seluruh data presensi & laporan tetap tersimpan aman.
                        </div>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus Pengguna!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-danger me-3 waves-effect waves-light',
                    cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                },
                buttonsStyling: false,
                preConfirm: () => {
                    return {
                        delete_absensi: $('#swal-delete-absensi-single').is(':checked') ? 1 : 0
                    };
                }
            }).then(function(result) {
                if (result.value) {
                    var deleteAbsensi = result.value.delete_absensi;
                    var formData = new FormData(form[0]);
                    formData.append('delete_absensi', deleteAbsensi);

                    $.ajax({
                        type: "POST",
                        url: "{{ route('admin.user.delete') }}",
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            showToastr(response.type, response.type, response.message);
                            dataTable.ajax.reload(null, false);
                        },
                        error: function(err) {
                            var msg = (err.responseJSON && err.responseJSON.message) ? err.responseJSON.message : 'Gagal menghapus pengguna.';
                            showToastr('error', 'error', msg);
                        }
                    });
                }
            });
        });

        var dataTable = initDataTables('table-1', 'loader-user', 'card-user', 'new-record-button', false,
            'User', "{{ route('admin.user.data') }}",
            [
                {
                    data: "checkbox",
                    name: "checkbox",
                    className: "text-center align-middle",
                    searchable: false,
                    orderable: false,
                },
                {
                    data: "name",
                    name: "name",
                    className: "align-middle",
                },
                {
                    data: "username",
                    name: "username",
                    className: "align-middle",
                },
                {
                    data: "jenis_kelamin",
                    name: "jenis_kelamin",
                    className: "align-middle",
                },
                {
                    data: "action",
                    name: "action",
                    className: "align-middle",
                    searchable: false,
                    orderable: false,
                },
            ],
            ['role_id', 'departemen_id']
        );

        // Inject bulk delete button next to New Record button
        $('#card-user .dt-action-buttons').prepend(`
            <button type="button" id="btn-bulk-delete-users" class="btn btn-danger waves-effect waves-light me-2 d-none">
                <i class="ti ti-trash me-1"></i> Hapus Terpilih (<span id="bulk-user-count">0</span>)
            </button>
        `);

        function updateBulkDeleteButton() {
            var count = $('.user-row-checkbox:checked').length;
            $('#bulk-user-count').text(count);
            if (count > 0) {
                $('#btn-bulk-delete-users').removeClass('d-none');
            } else {
                $('#btn-bulk-delete-users').addClass('d-none');
            }
        }

        $(document).on('change', '#check-all-users', function() {
            var isChecked = $(this).is(':checked');
            $('.user-row-checkbox:not(:disabled)').prop('checked', isChecked);
            updateBulkDeleteButton();
        });

        $(document).on('change', '.user-row-checkbox', function() {
            var total = $('.user-row-checkbox:not(:disabled)').length;
            var checked = $('.user-row-checkbox:checked').length;
            $('#check-all-users').prop('checked', total > 0 && total === checked);
            updateBulkDeleteButton();
        });

        dataTable.on('draw', function() {
            $('#check-all-users').prop('checked', false);
            updateBulkDeleteButton();
        });

        $(document).on('click', '#btn-bulk-delete-users', function() {
            var selectedIds = [];
            var selectedNames = [];
            $('.user-row-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
                var name = $(this).data('name');
                if (name && selectedNames.length < 5) {
                    selectedNames.push(name);
                }
            });

            if (selectedIds.length === 0) return;

            var previewText = selectedNames.join(', ');
            if (selectedIds.length > 5) {
                previewText += ` dan ${selectedIds.length - 5} lainnya`;
            }

            Swal.fire({
                title: `Hapus ${selectedIds.length} Pengguna Terpilih?`,
                html: `
                    <p class="mb-2">Akun yang akan dihapus: <strong>${previewText}</strong>.</p>
                    <div class="card p-3 text-start mb-2" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px;">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="swal-bulk-delete-absensi" style="cursor: pointer;">
                            <label class="form-check-label fw-bold text-danger ms-2" for="swal-bulk-delete-absensi" style="cursor: pointer;">
                                <i class="ti ti-trash me-1"></i>Ikut hapus seluruh data riwayat absensi
                            </label>
                        </div>
                        <div class="text-muted small mt-1 ms-4" style="font-size: 0.78rem;">
                            Biarkan <strong>tidak dicentang</strong> agar seluruh data presensi & laporan pengguna tetap tersimpan aman di sistem.
                        </div>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: `Ya, Hapus (${selectedIds.length})!`,
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-danger me-3 waves-effect waves-light',
                    cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                },
                buttonsStyling: false,
                preConfirm: () => {
                    return {
                        delete_absensi: $('#swal-bulk-delete-absensi').is(':checked') ? 1 : 0
                    };
                }
            }).then(function(result) {
                if (result.value) {
                    var deleteAbsensi = result.value.delete_absensi;
                    var totalUsers = selectedIds.length;
                    var processedCount = 0;
                    var successCount = 0;
                    var failedCount = 0;
                    var chunkSize = 15;
                    var currentIndex = 0;
                    var lastErrorMessage = '';

                    Swal.fire({
                        title: 'Menghapus Pengguna...',
                        html: `
                            <div class="p-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small fw-bold text-dark" id="user-delete-progress-text">Memproses: 0 / ${totalUsers} (0%)</span>
                                    <span class="badge bg-danger" id="user-delete-progress-percent">0%</span>
                                </div>
                                <div class="progress" style="height: 12px; background-color: #f1f1f2;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" id="user-delete-progress-bar" style="width: 0%;"></div>
                                </div>
                                <p class="text-muted small mt-2 mb-0">Mohon tunggu, proses batch penghapusan sedang berlangsung...</p>
                            </div>
                        `,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            function updateUserProgressUI(processed, total) {
                                var pct = Math.min(100, Math.round((processed / total) * 100));
                                $('#user-delete-progress-bar').css('width', pct + '%');
                                $('#user-delete-progress-text').text(`Memproses: ${processed} / ${total} (${pct}%)`);
                                $('#user-delete-progress-percent').text(pct + '%');
                            }

                            function runUserDeleteChunk() {
                                if (currentIndex >= totalUsers) {
                                    var swalIcon = failedCount === 0 ? 'success' : (successCount > 0 ? 'warning' : 'error');
                                    var swalTitle = failedCount === 0 ? 'Penghapusan Selesai!' : (successCount > 0 ? 'Penghapusan Sebagian Berhasil' : 'Penghapusan Gagal!');
                                    var errorNotice = (failedCount > 0 && lastErrorMessage) ? `<div class="alert alert-danger small text-start mt-2 p-2 mb-0"><i class="ti ti-alert-circle me-1"></i>${lastErrorMessage}</div>` : '';

                                    Swal.fire({
                                        icon: swalIcon,
                                        title: swalTitle,
                                        html: `<p class="mb-1">Berhasil memproses <strong>${successCount}</strong> akun pengguna.` + 
                                              (failedCount > 0 ? `<br><span class="text-danger fw-bold">${failedCount} akun gagal diproses.</span>` : '') + 
                                              `</p>` + errorNotice,
                                        customClass: { confirmButton: 'btn btn-primary' },
                                        buttonsStyling: false
                                    });
                                    dataTable.ajax.reload(null, false);
                                    $('#check-all-users').prop('checked', false);
                                    updateBulkDeleteButton();
                                    return;
                                }

                                var chunk = selectedIds.slice(currentIndex, currentIndex + chunkSize);
                                var curSize = chunk.length;

                                $.ajax({
                                    url: "{{ route('admin.user.bulk_delete') }}",
                                    type: "POST",
                                    data: {
                                        ids: chunk,
                                        delete_absensi: deleteAbsensi,
                                        _token: "{{ csrf_token() }}"
                                    },
                                    success: function(response) {
                                        successCount += (response.deleted_count || curSize);
                                        processedCount += curSize;
                                        currentIndex += curSize;

                                        updateUserProgressUI(processedCount, totalUsers);
                                        setTimeout(runUserDeleteChunk, 50);
                                    },
                                    error: function(err) {
                                        lastErrorMessage = (err.responseJSON && err.responseJSON.message) 
                                            ? err.responseJSON.message 
                                            : (err.statusText || 'Terjadi kesalahan pada server');
                                        failedCount += curSize;
                                        processedCount += curSize;
                                        currentIndex += curSize;

                                        updateUserProgressUI(processedCount, totalUsers);
                                        setTimeout(runUserDeleteChunk, 80);
                                    }
                                });
                            }

                            runUserDeleteChunk();
                        }
                    });
                }
            });
        });
    </script>
@endpush
