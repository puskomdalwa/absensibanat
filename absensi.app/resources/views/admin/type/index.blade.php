@extends('layouts.admin.template')
@section('title', 'Type User')
@section('content')
    @php($canManage = auth()->user()->isSuperAdmin())

    <!-- Header Banner -->
    <div class="card banat-page-header-card mb-4" style="background: linear-gradient(135deg, #180f24 0%, #2b143a 50%, #401535 100%) !important; border-radius: 20px !important; border: 1px solid rgba(251, 113, 133, 0.28) !important; box-shadow: 0 14px 35px rgba(18, 9, 28, 0.35) !important; color: #ffffff; position: relative; overflow: hidden;">
        <div class="card-body p-4 position-relative" style="z-index: 2;">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 46px; height: 46px; border-radius: 14px; background: linear-gradient(135deg, #fb7185 0%, #e11d48 100%); color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; box-shadow: 0 4px 14px rgba(225, 29, 72, 0.4); flex-shrink: 0;">
                        <i class="ti ti-tag"></i>
                    </div>
                    <div>
                        <div class="badge mb-1 px-3 py-1" style="background: rgba(251, 113, 133, 0.22); color: #fda4af; border: 1px solid rgba(251, 113, 133, 0.4); font-size: 0.72rem; border-radius: 20px;">
                            <i class="ti ti-tags me-1"></i> MANAGEMENT DATA MASTER
                        </div>
                        <h4 class="text-white fw-bold mb-0">Tipe &amp; Klasifikasi Civitas</h4>
                        <small class="text-white-50">Pengelompokan status civitas pondok putri (Dosen Tetap, Dosen LB, Staf Reguler, Santriwati, dll.)</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge px-3 py-2 fw-semibold" style="background: rgba(16, 185, 129, 0.18); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4); border-radius: 20px; font-size: 0.76rem;">
                        <i class="ti ti-circle-check me-1"></i> Klasifikasi Aktif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card" id="card-user" style="border-radius: 20px !important; border: 1px solid rgba(251, 113, 133, 0.22) !important; box-shadow: 0 8px 25px rgba(225, 29, 72, 0.05) !important; overflow: hidden;">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3 border-bottom pb-3" style="border-color: rgba(251, 113, 133, 0.16) !important;">
            <div class="d-flex align-items-center gap-3">
                <div style="width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg, #fb7185 0%, #e11d48 100%); color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; box-shadow: 0 4px 12px rgba(225, 29, 72, 0.35); flex-shrink: 0;">
                    <i class="ti ti-tags"></i>
                </div>
                <div>
                    <h5 class="card-title mb-0 fw-bold">Daftar Tipe User Civitas</h5>
                    <small class="text-muted">Master kategori klasifikasi pengguna untuk manajemen hak akses dan absensi</small>
                </div>
            </div>
        </div>
        <div class="card-datatable table-responsive pt-2 px-3 pb-3">
            <table class="datatables-basic table table-hover" id="table-1">
                <thead>
                    <tr>
                        <th style="width: 70px;">No</th>
                        <th>Tipe User</th>
                        <th style="width: 120px;" class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    @if ($canManage)
        @include('admin.type.add')
        @include('admin.type.edit')
    @endif

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
                        url: "{{ route('admin.type.delete') }}",
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
        var dataTable = initDataTables('table-1', 'loader-user', 'card-user', {!! $canManage ? "'new-record-button'" : 'false' !!}, false,
            'Type User', "{{ route('admin.type.data') }}",
            [
                {
                    data: "nama",
                    name: "nama",
                    className: "align-middle",
                    render: function(data, type, full, meta) {
                        return `
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2 flex-shrink-0">
                                    <span class="avatar-initial rounded-circle fw-bold" style="background: linear-gradient(135deg, rgba(225, 29, 72, 0.12) 0%, rgba(251, 113, 133, 0.22) 100%); color: #e11d48; border: 1px solid rgba(225, 29, 72, 0.25);">
                                        <i class="ti ti-tag" style="font-size: 0.95rem;"></i>
                                    </span>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-heading" style="letter-spacing: 0.01em;">${data || '-'}</span>
                                    <small class="text-muted" style="font-size: 0.72rem;">Klasifikasi Civitas</small>
                                </div>
                            </div>
                        `;
                    }
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
