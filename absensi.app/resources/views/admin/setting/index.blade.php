@extends('layouts.admin.template')
@section('title', 'Pengaturan Sistem - Visibilitas Nominal')

@section('content')
    <!-- BANAT LUXURY HERO HEADER -->
    <div class="card mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, rgba(225, 29, 72, 0.06) 0%, rgba(251, 113, 133, 0.15) 100%); border-radius: 20px; border: 1px solid rgba(251, 113, 133, 0.25) !important;">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: linear-gradient(135deg, #fb7185 0%, #e11d48 100%); border-radius: 16px; box-shadow: 0 4px 14px rgba(225, 29, 72, 0.35);">
                        <i class="ti ti-settings text-white" style="font-size: 1.85rem;"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 fw-bold text-heading">Pengaturan Sistem & Visibilitas Nominal</h4>
                        <p class="mb-0 text-muted small">Kelola hak tampil atau sembunyikan nominal honorarium untuk semua pengguna di seluruh halaman sistem</p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge" style="background: linear-gradient(135deg, #fb7185 0%, #e11d48 100%); color: white; padding: 8px 16px; border-radius: 50px; font-weight: 600;">
                        <i class="ti ti-shield-check me-1"></i> Khusus Superadmin
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- STATUS BANNER SAAT INI -->
    <div class="card mb-4 border-0 shadow-sm" style="border-radius: 16px; border-left: 5px solid {{ $mode === 'hide' ? '#e11d48' : ($mode === 'admin_only' ? '#ff9f43' : '#28c76f') }} !important;">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-3 py-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle p-2 d-flex align-items-center justify-content-center"
                     style="width: 44px; height: 44px; background: {{ $mode === 'hide' ? 'rgba(225, 29, 72, 0.12)' : ($mode === 'admin_only' ? 'rgba(255, 159, 67, 0.12)' : 'rgba(40, 199, 111, 0.12)') }}; color: {{ $mode === 'hide' ? '#e11d48' : ($mode === 'admin_only' ? '#ff9f43' : '#28c76f') }};">
                    <i class="ti {{ $mode === 'hide' ? 'ti-eye-off' : ($mode === 'admin_only' ? 'ti-shield-lock' : 'ti-eye') }} fs-3"></i>
                </div>
                <div>
                    <div class="small text-muted fw-semibold text-uppercase">Status Visibilitas Nominal Saat Ini:</div>
                    <h5 class="mb-0 fw-bold" id="current-status-text">
                        @if ($mode === 'hide')
                            <span class="text-danger"><i class="ti ti-circle-x me-1"></i>DISEMBUNYIKAN UNTUK SEMUA PENGGUNA</span>
                        @elseif ($mode === 'admin_only')
                            <span class="text-warning"><i class="ti ti-alert-circle me-1"></i>HANYA TAMPIL UNTUK ADMIN & SUPERADMIN</span>
                        @else
                            <span class="text-success"><i class="ti ti-circle-check me-1"></i>AKTIF & DITAMPILKAN KE SEMUA PENGGUNA</span>
                        @endif
                    </h5>
                </div>
            </div>
            <div>
                <button type="button" id="btn-quick-toggle" class="btn {{ $mode === 'hide' ? 'btn-success' : 'btn-danger' }} waves-effect waves-light">
                    <i class="ti {{ $mode === 'hide' ? 'ti-eye' : 'ti-eye-off' }} me-1"></i>
                    {{ $mode === 'hide' ? 'Aktifkan / Tampilkan Nominal' : 'Sembunyikan Nominal Sekarang' }}
                </button>
            </div>
        </div>
    </div>

    <!-- FORM PENGATURAN PILIHAN -->
    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 18px;">
                <div class="card-header bg-transparent pb-2 border-bottom">
                    <h5 class="card-title mb-1 fw-bold text-heading">
                        <i class="ti ti-adjustments-horizontal text-primary me-2"></i>Pilih Mode Tampilan Nominal
                    </h5>
                    <p class="text-muted small mb-0">Tentukan cakupan visibilitas nominal di seluruh halaman absensi, laporan, dan dokumen export.</p>
                </div>
                <div class="card-body pt-4">
                    <form id="form-setting-nominal" action="{{ route('admin.setting.nominal.update') }}" method="POST">
                        @csrf
                        <div class="d-flex flex-column gap-3">
                            <!-- Opsi 1: Tampilkan ke Semua -->
                            <label class="setting-option-card d-flex align-items-start gap-3 p-3 rounded-3 border cursor-pointer {{ $mode === 'show' ? 'border-primary bg-label-primary' : '' }}" style="cursor: pointer; transition: all 0.2s ease;">
                                <input class="form-check-input mt-1" type="radio" name="mode" value="show" id="mode-show" {{ $mode === 'show' ? 'checked' : '' }}>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <div class="fw-bold text-dark fs-6">
                                            <i class="ti ti-eye text-success me-1"></i> Tampilkan Nominal (Semua Pengguna & Semua Halaman)
                                        </div>
                                        <span class="badge bg-label-success">Default</span>
                                    </div>
                                    <p class="text-muted small mb-0">
                                        Nominal honorarium presensi akan tampil secara normal di seluruh halaman: Laporan Civitas, Detail Presensi Civitas, Rekapitulasi Presensi Admin, serta Export PDF & Excel.
                                    </p>
                                </div>
                            </label>

                            <!-- Opsi 2: Sembunyikan untuk Civitas (Khusus Admin/Superadmin) -->
                            <label class="setting-option-card d-flex align-items-start gap-3 p-3 rounded-3 border cursor-pointer {{ $mode === 'admin_only' ? 'border-warning bg-label-warning' : '' }}" style="cursor: pointer; transition: all 0.2s ease;">
                                <input class="form-check-input mt-1" type="radio" name="mode" value="admin_only" id="mode-admin" {{ $mode === 'admin_only' ? 'checked' : '' }}>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <div class="fw-bold text-dark fs-6">
                                            <i class="ti ti-shield-lock text-warning me-1"></i> Sembunyikan Untuk Civitas Saja (Hanya Admin & Superadmin Melihat)
                                        </div>
                                        <span class="badge bg-label-warning">Privasi Civitas</span>
                                    </div>
                                    <p class="text-muted small mb-0">
                                        Civitas/User biasa tidak akan melihat nominal honorarium di riwayat maupun halaman mereka. Hanya akun dengan hak akses Superadmin & Admin yang dapat melihat dan merekap nominal.
                                    </p>
                                </div>
                            </label>

                            <!-- Opsi 3: Sembunyikan untuk SEMUA Pengguna -->
                            <label class="setting-option-card d-flex align-items-start gap-3 p-3 rounded-3 border cursor-pointer {{ $mode === 'hide' ? 'border-danger bg-label-danger' : '' }}" style="cursor: pointer; transition: all 0.2s ease;">
                                <input class="form-check-input mt-1" type="radio" name="mode" value="hide" id="mode-hide" {{ $mode === 'hide' ? 'checked' : '' }}>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <div class="fw-bold text-dark fs-6">
                                            <i class="ti ti-eye-off text-danger me-1"></i> Sembunyikan Total (Untuk SEMUA Pengguna di Seluruh Halaman)
                                        </div>
                                        <span class="badge bg-label-danger">Sembunyikan Total</span>
                                    </div>
                                    <p class="text-muted small mb-0">
                                        Nominal disembunyikan sepenuhnya dari seluruh sistem. Kolom nominal di tabel, kartu total nominal, grafik honorarium, dan rekap export dokumen tidak akan menampilkan nominal rupiah kepada siapapun.
                                    </p>
                                </div>
                            </label>
                        </div>

                        <div class="d-flex align-items-center justify-content-end gap-2 mt-4 pt-3 border-top">
                            <button type="submit" id="btn-save-setting" class="btn btn-primary px-4 py-2 waves-effect waves-light">
                                <i class="ti ti-device-floppy me-1"></i> Simpan Pengaturan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- INFORMASI CAKUPAN HALAMAN YANG TERPENGARUH -->
        <div class="col-xl-4">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 18px;">
                <div class="card-header bg-transparent pb-2 border-bottom">
                    <h5 class="card-title mb-1 fw-bold text-heading">
                        <i class="ti ti-list-check text-info me-2"></i>Halaman Yang Terpengaruh
                    </h5>
                    <p class="text-muted small mb-0">Saat nominal disembunyikan, halaman-halaman berikut otomatis menyesuaikan:</p>
                </div>
                <div class="card-body pt-3">
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex align-items-start gap-3 mb-3 pb-2 border-bottom">
                            <span class="badge bg-label-primary rounded p-2"><i class="ti ti-table"></i></span>
                            <div>
                                <span class="fw-semibold d-block text-dark">Laporan Presensi Civitas</span>
                                <small class="text-muted">Kolom nominal & catatan honorarium di tabel rekap presensi civitas dihilangkan.</small>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3 mb-3 pb-2 border-bottom">
                            <span class="badge bg-label-success rounded p-2"><i class="ti ti-id"></i></span>
                            <div>
                                <span class="fw-semibold d-block text-dark">Detail Absensi Civitas</span>
                                <small class="text-muted">Kartu total nominal, rata-rata nominal, grafik nominal harian, dan kolom nominal tabel ditiadakan.</small>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3 mb-3 pb-2 border-bottom">
                            <span class="badge bg-label-warning rounded p-2"><i class="ti ti-file-text"></i></span>
                            <div>
                                <span class="fw-semibold d-block text-dark">Export Dokumen (PDF & Excel)</span>
                                <small class="text-muted">File export PDF dan Excel hanya memuat data absensi & durasi tanpa mencantumkan nominal uang.</small>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3 mb-3 pb-2 border-bottom">
                            <span class="badge bg-label-danger rounded p-2"><i class="ti ti-chart-bar"></i></span>
                            <div>
                                <span class="fw-semibold d-block text-dark">Rekapitulasi Bulanan Admin</span>
                                <small class="text-muted">Tabel menampilkan jumlah kehadiran civitas per kategori durasi (bukan nominal rupiah pemasukan).</small>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3">
                            <span class="badge bg-label-info rounded p-2"><i class="ti ti-api"></i></span>
                            <div>
                                <span class="fw-semibold d-block text-dark">Layanan API Terintegrasi</span>
                                <small class="text-muted">Respon API absensi otomatis memfilter field nominal berdasarkan status yang aktif.</small>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Highlight active radio card
        $('input[name="mode"]').on('change', function() {
            $('.setting-option-card').removeClass('border-primary bg-label-primary border-warning bg-label-warning border-danger bg-label-danger');
            
            const selected = $(this).val();
            if (selected === 'show') {
                $(this).closest('.setting-option-card').addClass('border-primary bg-label-primary');
            } else if (selected === 'admin_only') {
                $(this).closest('.setting-option-card').addClass('border-warning bg-label-warning');
            } else if (selected === 'hide') {
                $(this).closest('.setting-option-card').addClass('border-danger bg-label-danger');
            }
        });

        // Submit form via AJAX with Toastr & SweetAlert
        $('#form-setting-nominal').on('submit', function(e) {
            e.preventDefault();
            const $btn = $('#btn-save-setting');
            const originalText = $btn.html();
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(res) {
                    $btn.prop('disabled', false).html(originalText);
                    Swal.fire({
                        icon: 'success',
                        title: 'Pengaturan Disimpan!',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function(err) {
                    $btn.prop('disabled', false).html(originalText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan saat menyimpan pengaturan.'
                    });
                }
            });
        });

        // Quick Toggle Button
        $('#btn-quick-toggle').on('click', function(e) {
            e.preventDefault();
            const $btn = $(this);
            $btn.prop('disabled', true);

            $.ajax({
                url: "{{ route('admin.setting.nominal.toggle') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                dataType: 'json',
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Status Diperbarui!',
                        text: res.message,
                        timer: 1800,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function(err) {
                    $btn.prop('disabled', false);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Terjadi kesalahan saat beralih status nominal.'
                    });
                }
            });
        });
    });
</script>
@endpush
