@extends('layouts.admin.template')
@section('title', 'Absensi')
@section('content')
    @unless ($isStaff)
        @include('admin.absensi.import')
    @endunless
    @include('admin.absensi.filter')
    @include('admin.absensi.export')

    <div class="card" id="card-user">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table table-hover" id="table-2">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Tanggal</th>
                        <th>Jam Datang</th>
                        <th>Jam Pulang</th>
                        @unless ($isStaff)
                            <th>Action</th>
                        @endunless
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    @unless ($isStaff)
        @include('admin.absensi.add')
        @include('admin.absensi.edit')
    @endunless

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
        // Initialize autocomplete when the offcanvas is opened
        $('#new-record').on('shown.bs.offcanvas', function() {
            initializeAutocomplete("#new-record input[name='user_name']", "#new-record");
        });

        $('#edit-record').on('shown.bs.offcanvas', function() {
            initializeAutocomplete("#edit-record input[name='user_name']", "#edit-record");
        });

        $(document).on('submit', '.form-delete-record', function(e) {
            e.preventDefault();
            var tgl_absen = $(e.target).find('input[name="tgl_absen"]').val();
            Swal.fire({
                title: `Are you sure delete ${tgl_absen} ?`,
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
                        url: "{{ route('admin.absensi.delete') }}",
                        data: new FormData($(e.target)[0]),
                        // use [0] because inner swal, so there are has 2 target, cant use currentTarget
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
        var dataTable = initDataTables('table-2', 'loader-user', 'card-user', {!! $isStaff ? 'false' : "'new-record-button'" !!}, false,
            'Absensi', "{{ route('admin.absensi.data') }}",
            [{
                    data: "name",
                    name: "users.name",
                    className: "align-middle",
                },
                {
                    data: "tgl_absen",
                    name: "tgl_absen",
                    className: "align-middle",
                },
                {
                    data: "pagi",
                    name: "pagi",
                    className: "align-middle",
                },
                {
                    data: "sore",
                    name: "sore",
                    className: "align-middle",
                },
                @unless ($isStaff)
                    {
                        data: "action",
                        name: "action",
                        className: "align-middle",
                    },
                @endunless
            ],
            ['role_id', 'departemen_id', 'filter_type', 'bulan', 'tahun', 'start_date', 'end_date'],
            false,
            2
        );

        function initializeAutocomplete(selector, offcanvasID) {
            $(selector).autocomplete({
                appendTo: offcanvasID,
                source: function(request, response) {
                    var url = "{{ route('operasi.user.autocomplete', ['query' => 'query']) }}";
                    url = url.replace('query', request.term);

                    $.ajax({
                        type: "get",
                        url: url,
                        success: function(data) {
                            response(data.map(item => ({
                                label: item.label,
                                value: item.value
                            })));
                        }
                    });
                },
                select: function(event, ui) {
                    // Set the label in the user input
                    $(selector).val(ui.item.label);

                    // Store the value (user ID) in the hidden input
                    $(offcanvasID).find("input[name='user_id']").val(ui.item.value);
                    return false;
                }
            }).data("ui-autocomplete")._renderItem = function(ul, item) {
                return $("<li>")
                    .append(`<div style="padding: 5px; font-size: 14px;">${item.label}</div>`)
                    .appendTo(ul);
            };
        }
    </script>
@endpush
