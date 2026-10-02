@extends('layouts.admin.template')
@section('title', 'Profile')
@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
@endpush
@section('content')
    <!-- BANAT LUXURY PROFILE HEADER -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 20px; border: 1px solid rgba(251, 113, 133, 0.25) !important; overflow: hidden;">
                <div class="user-profile-header-banner" style="height: 160px; background: linear-gradient(135deg, rgba(225, 29, 72, 0.2) 0%, rgba(251, 113, 133, 0.35) 100%); position: relative;">
                    <div class="position-absolute end-0 bottom-0 p-3 opacity-25">
                        <i class="ti ti-crown" style="font-size: 8rem; color: #e11d48;"></i>
                    </div>
                </div>
                <div class="user-profile-header d-flex flex-column flex-lg-row text-sm-start text-center px-4 pb-4">
                    <div class="flex-shrink-0 mt-n5 mx-sm-0 mx-auto" style="position: relative; z-index: 2;">
                        <img src="{{ $user->photo ? asset('photo') . '/' . $user->photo : asset('admin_assets/img/avatars/profile-2.png') }}"
                            alt="user image" class="d-block h-auto ms-0 ms-sm-2 rounded-circle user-profile-img"
                            style="width: 120px; height: 120px; object-fit: cover; border: 4px solid #ffffff; box-shadow: 0 4px 18px rgba(225, 29, 72, 0.35);" />
                    </div>
                    <div class="flex-grow-1 mt-3 mt-lg-4 ms-sm-4">
                        <div class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start flex-md-row flex-column gap-3">
                            <div class="user-profile-info">
                                <h4 class="mb-1 fw-bold text-heading">{{ $user->name }}</h4>
                                <div class="d-flex align-items-center flex-wrap gap-2 my-2 justify-content-sm-start justify-content-center">
                                    <span class="badge" style="background: linear-gradient(135deg, #fb7185 0%, #e11d48 100%); color: white; padding: 6px 14px; border-radius: 50px; font-weight: 600;">
                                        <i class="ti ti-crown me-1"></i> {{ $user->role->akses }}
                                    </span>
                                    <span class="badge" style="background: rgba(225, 29, 72, 0.1); color: #e11d48; border: 1px solid rgba(225, 29, 72, 0.25); padding: 6px 14px; border-radius: 50px;">
                                        <i class="ti ti-mail me-1"></i> {{ $user->email }}
                                    </span>
                                    <span class="badge" style="background: rgba(225, 29, 72, 0.08); color: #be123c; border: 1px solid rgba(225, 29, 72, 0.2); padding: 6px 14px; border-radius: 50px;">
                                        <i class="ti ti-gender-femme me-1"></i> {{ $user->jenis_kelamin }}
                                    </span>
                                </div>
                            </div>
                            <button class="btn btn-primary edit-record-button d-flex align-items-center gap-2 px-4 py-2" data-id="{{ $user->id }}"
                                data-username="{{ $user->username }}" data-name="{{ $user->name }}"
                                data-email="{{ $user->email }}" data-photo="{{ $user->photo }}"
                                data-affiliate="{{ $user->affiliate }}" data-telp="{{ $user->telp }}"
                                data-jenis_kelamin="{{ $user->jenis_kelamin }}">
                                <i class="ti ti-user-edit fs-5"></i>
                                <span>Edit Profil</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/ Header -->

    <!-- User Profile Content -->
    <div class="row">
        <div class="col-12">
            <!-- About User -->
            <div class="card border-0 shadow-sm" style="border-radius: 20px; border: 1px solid rgba(251, 113, 133, 0.2) !important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
                        <div class="d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px; background: linear-gradient(135deg, #fb7185 0%, #e11d48 100%); border-radius: 12px; box-shadow: 0 4px 10px rgba(225, 29, 72, 0.3);">
                            <i class="ti ti-user-check text-white fs-4"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold" style="color: #e11d48;">Informasi Kredensial & Biodata Akun</h5>
                            <small class="text-muted">Data lengkap akun terdaftar pada sistem presensi Banat UII Dalwa</small>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 rounded-3" style="background: rgba(225, 29, 72, 0.04); border: 1px solid rgba(251, 113, 133, 0.18);">
                                <small class="text-muted text-uppercase d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.05em;">Username</small>
                                <div class="d-flex align-items-center">
                                    <i class="ti ti-key me-2" style="color: #e11d48;"></i>
                                    <span class="fw-bold">{{ $user->username }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 rounded-3" style="background: rgba(225, 29, 72, 0.04); border: 1px solid rgba(251, 113, 133, 0.18);">
                                <small class="text-muted text-uppercase d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.05em;">Nama Lengkap</small>
                                <div class="d-flex align-items-center">
                                    <i class="ti ti-user me-2" style="color: #e11d48;"></i>
                                    <span class="fw-bold">{{ $user->name }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 rounded-3" style="background: rgba(225, 29, 72, 0.04); border: 1px solid rgba(251, 113, 133, 0.18);">
                                <small class="text-muted text-uppercase d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.05em;">Email</small>
                                <div class="d-flex align-items-center">
                                    <i class="ti ti-mail me-2" style="color: #e11d48;"></i>
                                    <span class="fw-bold">{{ $user->email }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 rounded-3" style="background: rgba(225, 29, 72, 0.04); border: 1px solid rgba(251, 113, 133, 0.18);">
                                <small class="text-muted text-uppercase d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.05em;">Jenis Kelamin</small>
                                <div class="d-flex align-items-center">
                                    <i class="ti ti-gender-femme me-2" style="color: #e11d48;"></i>
                                    <span class="fw-bold">{{ $user->jenis_kelamin }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 rounded-3" style="background: rgba(225, 29, 72, 0.04); border: 1px solid rgba(251, 113, 133, 0.18);">
                                <small class="text-muted text-uppercase d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.05em;">No. Telepon</small>
                                <div class="d-flex align-items-center">
                                    <i class="ti ti-phone me-2" style="color: #e11d48;"></i>
                                    <span class="fw-bold">{{ $user->telp ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 rounded-3" style="background: rgba(225, 29, 72, 0.04); border: 1px solid rgba(251, 113, 133, 0.18);">
                                <small class="text-muted text-uppercase d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.05em;">Role Hak Akses</small>
                                <div class="d-flex align-items-center">
                                    <i class="ti ti-shield-check me-2" style="color: #e11d48;"></i>
                                    <span class="fw-bold">{{ $user->role->akses }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ About User -->
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
