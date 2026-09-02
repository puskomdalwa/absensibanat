@extends('layouts.admin.template')
@section('title', 'API Client')
@section('content')
    @php($isStaff = false)

    <div class="card" id="card-api-client">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Daftar API Client & Kredensial</h5>
        </div>
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table table-hover" id="table-api-client">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Client</th>
                        <th>API Key</th>
                        <th>Secret Key</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                        <th>Terakhir Digunakan</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    @include('admin.api-client.docs')

    @unless ($isStaff)
        @include('admin.api-client.add')
        @include('admin.api-client.edit')
    @endunless
@endsection

@push('scripts')
    <script>
        $(document).on('submit', '.form-delete-record', function(e) {
            e.preventDefault();
            var id = $(e.target).find('input[name="id"]').val();
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
                        url: "{{ route('admin.api_client.delete') }}",
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

        // Copy button handler
        $(document).on('click', '.btn-copy', function() {
            var text = $(this).data('clipboard-text');
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(function() {
                    showToastr('success', 'success', 'Berhasil disalin ke clipboard!');
                }).catch(function() {
                    showToastr('error', 'error', 'Gagal menyalin teks.');
                });
            } else {
                var temp = $("<input>");
                $("body").append(temp);
                temp.val(text).select();
                document.execCommand("copy");
                temp.remove();
                showToastr('success', 'success', 'Berhasil disalin ke clipboard!');
            }
        });

        // Toggle Secret Key visibility
        $(document).on('click', '.btn-toggle-secret', function() {
            var codeEl = $(this).siblings('.secret-text');
            var icon = $(this).find('i');
            var secret = codeEl.data('secret');
            
            if (codeEl.text() === '••••••••••••••••••••••••••••••••') {
                codeEl.text(secret);
                icon.removeClass('ti-eye').addClass('ti-eye-off');
            } else {
                codeEl.text('••••••••••••••••••••••••••••••••');
                icon.removeClass('ti-eye-off').addClass('ti-eye');
            }
        });

        var dataTable = initDataTables('table-api-client', 'loader-api-client', 'card-api-client', {!! $isStaff ? 'false' : "'new-record-button'" !!}, false,
            'API Client', "{{ route('admin.api_client.data') }}",
            [
                {
                    data: "name",
                    name: "name",
                    className: "align-middle fw-semibold",
                },
                {
                    data: "api_key",
                    name: "api_key",
                    className: "align-middle",
                },
                {
                    data: "secret_key",
                    name: "secret_key",
                    className: "align-middle",
                },
                {
                    data: "is_active",
                    name: "is_active",
                    className: "align-middle text-center",
                },
                {
                    data: "description",
                    name: "description",
                    className: "align-middle",
                },
                {
                    data: "last_used_at",
                    name: "last_used_at",
                    className: "align-middle text-center",
                },
                {
                    data: "action",
                    name: "action",
                    className: "align-middle text-center",
                    searchable: false,
                    orderable: false,
                },
            ],
        );
    </script>
@endpush
