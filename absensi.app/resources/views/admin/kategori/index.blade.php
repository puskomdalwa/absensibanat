@extends('layouts.admin.template')
@section('title', 'Kategori')
@section('content')
    @php($isStaff = false)
    <div class="card" id="card-kategori">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table table-hover" id="table-1">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Kode</th>
                        <th>Selisih (Menit)</th>
                        <th>Nominal</th>
                        <th>Keterangan</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    @unless ($isStaff)
        @include('admin.kategori.add')
        @include('admin.kategori.edit')
    @endunless
@endsection

@push('scripts')
    <script>
        $(document).on('submit', '.form-delete-record', function(e) {
            e.preventDefault();
            var name = $(e.target).find('input[name="name"]').val();

            Swal.fire({
                title: `Are you sure delete ${name}?`,
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                customClass: {
                    confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                    cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                },
                buttonsStyling: false
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('admin.kategori.delete') }}",
                        data: new FormData($(e.target)[0]),
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            showToastr(response.type, response.type, response.message);
                            dataTable.ajax.reload(null, false);
                        },
                    });
                }
            });
        });

        var dataTable = initDataTables('table-1', 'loader-kategori', 'card-kategori', {!! $isStaff ? 'false' : "'new-record-button'" !!}, false,
            'Kategori', "{{ route('admin.kategori.data') }}",
            [{
                    data: "nama",
                    name: "nama",
                    className: "align-middle",
                },
                {
                    data: "kode",
                    name: "kode",
                    className: "align-middle",
                },
                {
                    data: "selisih",
                    name: "selisih",
                    className: "align-middle",
                },
                {
                    data: "nominal",
                    name: "nominal",
                    className: "align-middle",
                },
                {
                    data: "keterangan",
                    name: "keterangan",
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
        );

        var $syncButton = $(
            '<button type="button" id="synchronize-button" class="btn btn-label-info me-2 waves-effect">' +
                '<i class="ti ti-refresh me-sm-1"></i>' +
                '<span class="d-none d-sm-inline-block">Sinkronisasi</span>' +
            '</button>'
        );
        $('#card-kategori .dt-buttons').prepend($syncButton);

        function updateSyncProgress(processed, total, done) {
            var percentage = total > 0 ? Math.floor((processed / total) * 100) : (done ? 100 : 0);
            percentage = Math.min(100, percentage);

            $('#synchronize-progress')
                .css('width', percentage + '%')
                .attr('aria-valuenow', percentage)
                .text(percentage + '%');
            $('#synchronize-counter').text(
                processed.toLocaleString('id-ID') + ' dari ' + total.toLocaleString('id-ID') + ' data'
            );
        }

        function synchronizeNextBatch(state) {
            $.ajax({
                type: 'POST',
                url: "{{ route('admin.kategori.synchronize') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    cursor: state.cursor,
                    max_id: state.maxId,
                    total: state.total
                },
                success: function(response) {
                    if (!response.status) {
                        synchronizeFailed(response.message);
                        return;
                    }

                    state.cursor = response.next_cursor;
                    state.maxId = response.max_id;
                    state.total = response.total;
                    state.processed += response.batch_processed;
                    state.updated += response.batch_updated;
                    state.failed += response.batch_failed;

                    updateSyncProgress(state.processed, state.total, response.done);

                    if (!response.done) {
                        synchronizeNextBatch(state);
                        return;
                    }

                    $('#synchronize-button').prop('disabled', false);
                    dataTable.ajax.reload(null, false);

                    var resultText = state.processed.toLocaleString('id-ID') + ' data diperiksa dan ' +
                        state.updated.toLocaleString('id-ID') + ' data diperbarui.';

                    if (state.failed > 0) {
                        resultText += ' ' + state.failed.toLocaleString('id-ID') + ' data gagal diproses.';
                    }

                    Swal.fire({
                        icon: state.failed > 0 ? 'warning' : 'success',
                        title: state.failed > 0 ? 'Sinkronisasi selesai dengan catatan' : 'Sinkronisasi berhasil',
                        text: resultText,
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary waves-effect waves-light'
                        },
                        buttonsStyling: false
                    });
                },
                error: function(xhr) {
                    var message = xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message
                        : 'Terjadi kesalahan saat menyinkronkan data absensi.';
                    synchronizeFailed(message);
                }
            });
        }

        function synchronizeFailed(message) {
            $('#synchronize-button').prop('disabled', false);
            Swal.fire({
                icon: 'error',
                title: 'Sinkronisasi gagal',
                text: message,
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn btn-primary waves-effect waves-light'
                },
                buttonsStyling: false
            });
        }

        $(document).on('click', '#synchronize-button', function() {
            Swal.fire({
                title: 'Sinkronisasi data absensi?',
                text: 'Kategori seluruh data absensi akan dihitung ulang menggunakan aturan kategori terbaru.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, sinkronkan!',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                    cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                },
                buttonsStyling: false
            }).then(function(result) {
                if (!result.isConfirmed) {
                    return;
                }

                $('#synchronize-button').prop('disabled', true);

                Swal.fire({
                    title: 'Menyinkronkan data absensi',
                    html: '<p class="text-muted mb-3">Mohon tunggu dan jangan tutup halaman ini.</p>' +
                        '<div class="progress" style="height: 22px;">' +
                            '<div id="synchronize-progress" class="progress-bar progress-bar-striped progress-bar-animated" ' +
                                'role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>' +
                        '</div>' +
                        '<div id="synchronize-counter" class="mt-2 text-muted">Menyiapkan data...</div>',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: function() {
                        synchronizeNextBatch({
                            cursor: 0,
                            maxId: 0,
                            total: null,
                            processed: 0,
                            updated: 0,
                            failed: 0
                        });
                    }
                });
            });
        });
    </script>
@endpush
