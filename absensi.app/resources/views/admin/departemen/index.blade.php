@extends('layouts.admin.template')
@section('title', 'Departemen')
@section('content')
    @php($isStaff = false)

    <div class="card" id="card-user">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table table-hover" id="table-1">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    @unless ($isStaff)
        @include('admin.departemen.add')
        @include('admin.departemen.edit')
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
                        url: "{{ route('admin.departemen.delete') }}",
                        data: new FormData($(e.target)[0]),
                        // use [0] because inner swal so there are has 2 target, cant use currentTarget
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            showToastr(response.type, response.type, response
                                .message);
                            dataTable.ajax.reload(null, false);
                        },
                    });
                }
            });
        });
    </script>

    <script>
        var dataTable = initDataTables('table-1', 'loader-user', 'card-user', {!! $isStaff ? 'false' : "'new-record-button'" !!}, false,
            'Departemen', "{{ route('admin.departemen.data') }}",
            [{
                    data: "kode",
                    name: "kode",
                    className: "align-middle",
                },
                {
                    data: "nama",
                    name: "nama",
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
    </script>
@endpush
