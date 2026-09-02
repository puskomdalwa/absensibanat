@extends('layouts.admin.template')
@section('title', 'User')
@section('content')
    @unless ($isStaff)
        @include('admin.user.import')
    @endunless
    @include('admin.user.filter')
    <div class="card" id="card-user">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table table-hover" id="table-1">
                <thead>
                    <tr>
                        <th>No</th>
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
                        url: "{{ route('admin.user.delete') }}",
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
        var dataTable = initDataTables('table-1', 'loader-user', 'card-user', 'new-record-button', false,
            'User', "{{ route('admin.user.data') }}",
            [{
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
    </script>
@endpush
