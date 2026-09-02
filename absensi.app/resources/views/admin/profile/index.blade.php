@extends('layouts.admin.template')
@section('title', 'Profile')
@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
@endpush
@section('content')
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-6">
                <div class="user-profile-header-banner">
                    <img src="{{ asset('admin/assets/img/pages/profile-banner.png') }}" alt="Banner image"
                        class="rounded-top w-100" />
                </div>
                <div class="user-profile-header d-flex flex-column flex-lg-row text-sm-start text-center mb-5">
                    <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
                        <img src="{{ $user->photo ? asset('photo') . '/' . $user->photo : asset('admin/assets/img/avatars/profile-2.png') }}"
                            alt="user image" class="d-block h-auto ms-0 ms-sm-6 rounded user-profile-img"
                            style="width: 100px;height: 100px" />
                    </div>
                    <div class="flex-grow-1 mt-3 mt-lg-5">
                        <div
                            class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-5 flex-md-row flex-column gap-4">
                            <div class="user-profile-info">
                                <h4 class="mb-2 mt-lg-6">{{ $user->name }}</h4>
                                <ul
                                    class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-4 my-2">
                                    <li class="list-inline-item d-flex gap-2 align-items-center">
                                        <i class="ti ti-crown ti-lg"></i><span class="fw-medium">{{ $user->role->akses }}</span>
                                    </li>
                                    <li class="list-inline-item d-flex gap-2 align-items-center">
                                        <i class="ti ti-mail ti-lg"></i><span class="fw-medium">{{ $user->email }}</span>
                                    </li>
                                    <li class="list-inline-item d-flex gap-2 align-items-center">
                                        <i class="ti ti-user ti-lg"></i><span class="fw-medium">{{ $user->jenis_kelamin }}</span>
                                    </li>
                                </ul>
                            </div>
                            <button class="btn btn-primary mb-1 edit-record-button" data-id="{{ $user->id }}"
                                data-username="{{ $user->username }}" data-name="{{ $user->name }}"
                                data-email="{{ $user->email }}" data-photo="{{ $user->photo }}"
                                data-affiliate="{{ $user->affiliate }}" data-telp="{{ $user->telp }}"
                                data-jenis_kelamin="{{ $user->jenis_kelamin }}"><i class="ti ti-user-edit ti-xs me-2"></i>Edit
                                Profle</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/ Header -->

    <!-- User Profile Content -->
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12">
            <!-- About User -->
            <div class="card mb-6">
                <div class="card-body">
                    <small class="card-text text-uppercase text-muted small">About</small>
                    <ul class="list-unstyled my-3 py-1">
                        <li class="d-flex align-items-center mb-4">
                            <i class="ti ti-key ti-lg"></i><span class="fw-medium mx-2">Username:</span>
                            <span>{{ $user->username }}</span>
                        </li>
                        <li class="d-flex align-items-center mb-4">
                            <i class="ti ti-user ti-lg"></i><span class="fw-medium mx-2">Full Name:</span>
                            <span>{{ $user->name }}</span>
                        </li>
                        <li class="d-flex align-items-center mb-4">
                            <i class="ti ti-mail ti-lg"></i><span class="fw-medium mx-2">Email:</span>
                            <span>{{ $user->email }}</span>
                        </li>
                        <li class="d-flex align-items-center mb-4">
                            <i class="ti ti-plus ti-lg"></i><span class="fw-medium mx-2">Gender:</span>
                            <span>{{ $user->jenis_kelamin }}</span>
                        </li>
                        <li class="d-flex align-items-center mb-4">
                            <i class="ti ti-plus ti-lg"></i><span class="fw-medium mx-2">Telp:</span>
                            <span>{{ $user->telp }}</span>
                        </li>
                        <li class="d-flex align-items-center mb-4">
                            <i class="ti ti-plus ti-lg"></i><span class="fw-medium mx-2">Affiliate:</span>
                            <span>{{ $user->affiliate }}</span>
                        </li>
                        <li class="d-flex align-items-center mb-4">
                            <i class="ti ti-crown ti-lg"></i><span class="fw-medium mx-2">Role:</span>
                            <span>{{ $user->role->akses }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            <!--/ About User -->
        </div>
        <div class="col-xl-8 col-lg-7 col-md-7">
        </div>
    </div>
    <!--/ User Profile Content -->

    @include('admin.profile.edit')

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
    <!-- Cropper.js Script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script>
        const cropModal = $('#cropModal');
        const imageToCrop = $('#imageToCrop');
        const cropButton = $('#cropButton');
    </script>
@endpush
