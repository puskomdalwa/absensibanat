{{-- Modal Tambah Perangkat --}}
<div class="modal fade" id="modal-add-device" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="ti ti-device-mobile-plus me-1 text-primary"></i> Tambah Mesin Absensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-add-device">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Mesin <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" placeholder="Contoh: ABSENSI LOBBY UTAMA" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Cloud ID (Serial Number) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control text-uppercase" name="cloud_id" placeholder="Contoh: C2642CA867122A34" required>
                        <small class="text-muted">Cloud ID dapat ditemukan pada menu info perangkat di mesin atau portal Fingerspot.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-check me-1"></i> Simpan Mesin</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit Perangkat --}}
<div class="modal fade" id="modal-edit-device" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="ti ti-pencil me-1 text-primary"></i> Edit Mesin Absensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-edit-device">
                @csrf
                <input type="hidden" name="id" id="edit-device-id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Mesin <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="edit-device-name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Cloud ID (Serial Number) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control text-uppercase" name="cloud_id" id="edit-device-cloud-id" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-check me-1"></i> Perbarui</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Atur Waktu Perangkat --}}
<div class="modal fade" id="modal-set-time" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="ti ti-clock-cog me-1 text-primary"></i> Sinkronisasi Waktu Mesin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-set-time">
                @csrf
                <input type="hidden" name="cloud_id" id="set-time-cloud-id">
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        Mesin target: <strong class="text-primary" id="set-time-device-name">-</strong>
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Zona Waktu IANA <span class="text-danger">*</span></label>
                        <select class="form-select" name="timezone" id="set-time-timezone" required>
                            @foreach ($timezones as $tzKey => $tzLabel)
                                <option value="{{ $tzKey }}">{{ $tzLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="alert alert-warning py-2 mb-0 small">
                        <i class="ti ti-alert-triangle me-1"></i> Perintah ini dapat memicu restart mesin pada beberapa tipe mesin Fingerspot.
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-send me-1"></i> Kirim Perintah</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Daftarkan User ke Mesin (set_userinfo) --}}
<div class="modal fade" id="modal-push-user" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="ti ti-user-plus me-1 text-primary"></i> Daftarkan / Update User ke Mesin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-push-user">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pilih Mesin Target <span class="text-danger">*</span></label>
                            <select class="form-select" name="cloud_id" id="push-user-cloud-id" required>
                                @foreach ($devices as $dev)
                                    <option value="{{ $dev->cloud_id }}">{{ $dev->name }} ({{ $dev->cloud_id }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pilih User Lokal (Opsional)</label>
                            <select class="form-select" id="select-local-user">
                                <option value="">-- Input Manual atau Pilih User Lokal --</option>
                                @foreach ($users as $u)
                                    <option value="{{ $u->id }}" data-name="{{ $u->name }}">{{ $u->name }} (PIN #{{ $u->id }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">PIN User di Mesin <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="pin" id="push-user-pin" placeholder="Contoh: 1001" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nama Lengkap Pengguna <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="push-user-name" placeholder="Nama yang muncul di layar mesin" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tingkat Hak Akses</label>
                            <select class="form-select" name="privilege" id="push-user-privilege">
                                <option value="1">1 - Pengguna (User / Santri)</option>
                                <option value="2">2 - Administrator</option>
                                <option value="3">3 - Sub-Administrator</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">PIN Password Keypad</label>
                            <input type="password" class="form-control" name="password" id="push-user-password" placeholder="Opsional">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kode Kartu RFID</label>
                            <input type="text" class="form-control" name="rfid" id="push-user-rfid" placeholder="Opsional">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Template Biometrik (Opsional)</label>
                            <textarea class="form-control font-monospace small" name="template" id="push-user-template" rows="2" placeholder="Biarkan kosong jika mendaftarkan user baru. Diisi dengan template hasil get_userinfo jika menduplikasi."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-send me-1"></i> Kirim ke Mesin</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Copy / Transfer User Antar Mesin --}}
<div class="modal fade" id="modal-copy-user" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="ti ti-copy me-1 text-primary"></i> Transfer / Copy User Antar Mesin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-copy-user">
                @csrf
                <input type="hidden" name="source_cloud_id" id="copy-source-cloud-id">
                <input type="hidden" name="pin" id="copy-pin">
                <div class="modal-body">
                    <p class="mb-3">
                        Pengguna: <strong class="text-primary" id="copy-user-name">-</strong> (PIN: <span id="copy-user-pin">-</span>)<br>
                        Mesin Sumber: <code id="copy-source-cloud-id-text">-</code>
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Mesin Tujuan <span class="text-danger">*</span></label>
                        <select class="form-select" name="target_cloud_id" id="copy-target-cloud-id" required>
                            @foreach ($devices as $dev)
                                <option value="{{ $dev->cloud_id }}">{{ $dev->name }} ({{ $dev->cloud_id }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="alert alert-info py-2 mb-0 small">
                        <i class="ti ti-info-circle me-1"></i> Data profil beserta template sidik jari / wajah yang tersimpan di sistem akan dikirim ke mesin target.
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-send me-1"></i> Proses Duplikasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Registrasi Biometrik Online (reg_online) --}}
<div class="modal fade" id="modal-reg-online" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="ti ti-fingerprint me-1 text-primary"></i> Perekaman Biometrik Jarak Jauh</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-reg-online">
                @csrf
                <input type="hidden" name="cloud_id" id="reg-cloud-id">
                <input type="hidden" name="pin" id="reg-pin">
                <div class="modal-body">
                    <p class="mb-3">
                        Pengguna: <strong class="text-primary" id="reg-user-name">-</strong> (PIN: <span id="reg-user-pin">-</span>)
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Bagian Biometrik <span class="text-danger">*</span></label>
                        <select class="form-select" name="verification" required>
                            <optgroup label="Sidik Jari (Fingerprint)">
                                <option value="0">0 - Jari Telunjuk Kanan</option>
                                <option value="1">1 - Ibu Jari Kanan</option>
                                <option value="2">2 - Jari Tengah Kanan</option>
                                <option value="3">3 - Jari Manis Kanan</option>
                                <option value="4">4 - Jari Kelingking Kanan</option>
                                <option value="5">5 - Jari Telunjuk Kiri</option>
                            </optgroup>
                            <optgroup label="Pengenalan Wajah & Vein">
                                <option value="12">12 - Wajah (Face Recognition)</option>
                                <option value="13">13 - Palm / Finger Vein</option>
                            </optgroup>
                        </select>
                    </div>
                    <div class="alert alert-warning py-2 mb-0 small">
                        <i class="ti ti-info-circle me-1"></i> Saat tombol ditekan, mesin absensi fisik akan berbunyi dan meminta santri/user menempelkan jari atau menghadap kamera.
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-broadcast me-1"></i> Mulai Perekaman di Mesin</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Detail Log Perintah / Webhook JSON --}}
<div class="modal fade" id="modal-view-command" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="ti ti-code me-1 text-primary"></i> Detail Transaksi & Payload JSON</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <small class="text-muted d-block">Tipe Perintah</small>
                        <span class="fw-semibold" id="detail-command-type">-</span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Trans ID</small>
                        <code class="fw-semibold" id="detail-trans-id">-</code>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Status</small>
                        <span id="detail-status">-</span>
                    </div>
                </div>

                <ul class="nav nav-tabs nav-fill mb-3" role="tablist">
                    <li class="nav-item">
                        <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-json-request">Request Body</button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-json-response">Response Acknowledgement</button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-json-callback">Webhook Callback</button>
                    </li>
                </ul>

                <div class="tab-content p-0">
                    <div class="tab-pane fade show active" id="tab-json-request">
                        <pre class="bg-dark text-white p-3 rounded" style="max-height: 350px; overflow-y: auto;"><code id="detail-json-request">-</code></pre>
                    </div>
                    <div class="tab-pane fade" id="tab-json-response">
                        <pre class="bg-dark text-white p-3 rounded" style="max-height: 350px; overflow-y: auto;"><code id="detail-json-response">-</code></pre>
                    </div>
                    <div class="tab-pane fade" id="tab-json-callback">
                        <pre class="bg-dark text-white p-3 rounded" style="max-height: 350px; overflow-y: auto;"><code id="detail-json-callback">-</code></pre>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambahkan Semua User ke Mesin (Batch Push Massal) --}}
<div class="modal fade" id="modal-batch-push-users" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0" style="border-radius: 16px; overflow: hidden;">
            <!-- Modal Header Standard & Clean -->
            <div class="modal-header py-3 px-4 bg-white border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar avatar-md rounded-3 bg-label-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;">
                        <i class="ti ti-users-plus fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0">Tambahkan Semua User ke Mesin Biometrik</h5>
                        <small class="text-muted">Pengecekan otomatis & sinkronisasi bertahap anti-timeout</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="btn-close-batch-modal"></button>
            </div>

            <!-- STEP 1: Konfigurasi Target & Pengecekan -->
            <div class="modal-body p-4" id="batch-step-1">
                <div class="alert alert-primary d-flex align-items-center gap-3 p-3 mb-3 border-0 shadow-sm" style="border-radius: 12px; background: linear-gradient(135deg, rgba(115, 103, 240, 0.08) 0%, rgba(115, 103, 240, 0.16) 100%);">
                    <div class="avatar avatar-sm rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                        <i class="ti ti-info-circle fs-4"></i>
                    </div>
                    <div class="small text-dark flex-grow-1">
                        <div class="fw-bold mb-1 text-primary">Informasi Pendaftaran Massal</div>
                        Fitur ini akan mendaftarkan seluruh akun civitas lokal ke mesin biometrik terpilih. Sebelum eksekusi, sistem akan melakukan <strong>analisis pra-pemeriksaan</strong> untuk mendeteksi user yang sudah ada.
                    </div>
                </div>

                <div class="row g-3">
                    <!-- Pilihan Target Mesin -->
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark mb-2">1. Pilih Target Mesin Fingerspot <span class="text-danger">*</span></label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card border border-2 shadow-none cursor-pointer h-100" id="card-target-mode-all" style="border-radius: 12px; border-color: #7367f0 !important; background-color: rgba(115, 103, 240, 0.04); cursor: pointer; transition: all 0.2s ease;">
                                    <div class="card-body p-3">
                                        <div class="form-check mb-1">
                                            <input name="batch_target_mode" class="form-check-input" type="radio" value="all" id="target-mode-all" checked style="cursor: pointer;">
                                            <label class="form-check-label fw-bold text-dark fs-6" for="target-mode-all" style="cursor: pointer;">
                                                <i class="ti ti-devices me-1 text-primary"></i> Semua Mesin Target ({{ $totalDevices }} Perangkat)
                                            </label>
                                        </div>
                                        <div class="text-muted small ps-4">
                                            Kirim data user ke seluruh mesin biometrik yang aktif.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border border-2 shadow-none cursor-pointer h-100" id="card-target-mode-single" style="border-radius: 12px; border-color: #dbdade !important; background-color: #ffffff; cursor: pointer; transition: all 0.2s ease;">
                                    <div class="card-body p-3">
                                        <div class="form-check mb-1">
                                            <input name="batch_target_mode" class="form-check-input" type="radio" value="single" id="target-mode-single" style="cursor: pointer;">
                                            <label class="form-check-label fw-bold text-dark fs-6" for="target-mode-single" style="cursor: pointer;">
                                                <i class="ti ti-device-laptop me-1 text-warning"></i> Pilih 1 Mesin Tertentu
                                            </label>
                                        </div>
                                        <div class="text-muted small ps-4">
                                            Hanya kirim ke 1 perangkat absensi spesifik.
                                        </div>
                                        <div class="mt-2 ps-4" id="box-single-device-select" style="display: none;">
                                            <select class="form-select form-select-sm" id="batch-single-cloud-id">
                                                @foreach ($devices as $dev)
                                                    <option value="{{ $dev->cloud_id }}">{{ $dev->name }} ({{ $dev->cloud_id }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pilihan Hak Akses di Mesin -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark" for="batch-user-privilege">2. Hak Akses User di Mesin</label>
                        <select class="form-select" id="batch-user-privilege">
                            <option value="1" selected>1 - Pengguna Standar (Civitas / Santri / Dosen)</option>
                            <option value="2">2 - Administrator Mesin</option>
                            <option value="3">3 - Sub-Administrator Mesin</option>
                        </select>
                        <small class="text-muted">Tingkat izin yang disematkan saat PIN dibuat di mesin.</small>
                    </div>

                    <!-- Aturan User yang Sudah Ada -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark" for="batch-overwrite-mode">3. Jika User Sudah Terdaftar di Mesin</label>
                        <select class="form-select" id="batch-overwrite-mode">
                            <option value="skip" selected>Lewati / Jangan Timpa (Dianjurkan & Cepat)</option>
                            <option value="overwrite">Perbarui / Timpa Data (Kirim ulang nama & hak akses)</option>
                        </select>
                        <small class="text-muted">Mencegah penumpukan perintah jika user sudah ada di mesin.</small>
                    </div>
                </div>
            </div>

            <!-- STEP 2: Hasil Pre-check & Konfirmasi (ACC) -->
            <div class="modal-body p-4 d-none" id="batch-step-2">
                <!-- Summary Metric Cards (Modern & Harmonious) -->
                <div class="row g-3 mb-3">
                    <!-- Card 1: Total Civitas -->
                    <div class="col-md-3 col-6">
                        <div class="card h-100 border-0 shadow-sm" style="border-radius: 12px; background: #f8f9fa; border: 1px solid #e9ecef !important;">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-muted small fw-semibold">Total Civitas</span>
                                    <div class="avatar avatar-xs rounded-circle bg-label-primary d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                        <i class="ti ti-users" style="font-size: 14px;"></i>
                                    </div>
                                </div>
                                <div class="d-flex align-items-baseline justify-content-between">
                                    <h3 class="mb-0 fw-bold text-primary" id="precheck-total-users">0</h3>
                                    <span class="badge bg-label-primary rounded-pill font-monospace" style="font-size: 10px;">Lokal DB</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Target Mesin -->
                    <div class="col-md-3 col-6">
                        <div class="card h-100 border-0 shadow-sm" style="border-radius: 12px; background: #f8f9fa; border: 1px solid #e9ecef !important;">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-muted small fw-semibold">Target Mesin</span>
                                    <div class="avatar avatar-xs rounded-circle bg-label-secondary d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                        <i class="ti ti-devices" style="font-size: 14px;"></i>
                                    </div>
                                </div>
                                <div class="d-flex align-items-baseline justify-content-between">
                                    <h3 class="mb-0 fw-bold text-dark" id="precheck-total-devices">0 Mesin</h3>
                                    <span class="badge bg-label-secondary rounded-pill" style="font-size: 10px;">Hardware</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: Belum Ada (Akan Ditambah) -->
                    <div class="col-md-3 col-6">
                        <div class="card h-100 border-0 shadow-sm" style="border-radius: 12px; background: rgba(40, 199, 111, 0.06); border: 1px solid rgba(40, 199, 111, 0.25) !important;">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-success small fw-bold">Akan Ditambahkan</span>
                                    <div class="avatar avatar-xs rounded-circle bg-label-success d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                        <i class="ti ti-user-plus" style="font-size: 14px;"></i>
                                    </div>
                                </div>
                                <div class="d-flex align-items-baseline justify-content-between">
                                    <h3 class="mb-0 fw-bold text-success" id="precheck-pending-users">0</h3>
                                    <span class="badge bg-label-success rounded-pill" style="font-size: 10px;">User Baru</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 4: Sudah Ada di Mesin -->
                    <div class="col-md-3 col-6">
                        <div class="card h-100 border-0 shadow-sm" style="border-radius: 12px; background: rgba(0, 207, 232, 0.06); border: 1px solid rgba(0, 207, 232, 0.25) !important;">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-info small fw-bold">Sudah di Mesin</span>
                                    <div class="avatar avatar-xs rounded-circle bg-label-info d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                        <i class="ti ti-user-check" style="font-size: 14px;"></i>
                                    </div>
                                </div>
                                <div class="d-flex align-items-baseline justify-content-between">
                                    <h3 class="mb-0 fw-bold text-info" id="precheck-existing-users">0</h3>
                                    <span class="badge bg-label-info rounded-pill" style="font-size: 10px;">Terdata</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preview Table User List -->
                <div class="card border shadow-sm mb-3" style="border-radius: 12px; overflow: hidden; border-color: #e9ecef !important;">
                    <div class="card-header bg-white py-2 px-3 d-flex justify-content-between align-items-center border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-list-check me-2 text-primary fs-5"></i>
                            <span class="fw-bold text-dark small">Pratinjau Pengguna (<span id="precheck-action-count" class="text-primary">0</span> Civitas)</span>
                        </div>
                        <span class="badge bg-label-primary rounded-pill px-3 py-1 fw-semibold" id="precheck-mode-badge">
                            <i class="ti ti-devices me-1"></i>Target: Semua Mesin
                        </span>
                    </div>
                    <div class="table-responsive" style="max-height: 230px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0" id="table-precheck-preview">
                            <thead style="background-color: #f8f9fa; position: sticky; top: 0; z-index: 10;">
                                <tr>
                                    <th style="width: 85px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #566a7f !important; padding: 10px 14px; border-bottom: 2px solid #e7e7e8;">PIN</th>
                                    <th style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #566a7f !important; padding: 10px 14px; border-bottom: 2px solid #e7e7e8;">Nama Civitas</th>
                                    <th style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #566a7f !important; padding: 10px 14px; border-bottom: 2px solid #e7e7e8;">Username</th>
                                    <th style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #566a7f !important; padding: 10px 14px; border-bottom: 2px solid #e7e7e8;">Status di Mesin Target</th>
                                </tr>
                            </thead>
                            <tbody id="precheck-preview-tbody">
                                <!-- Dynamic rows -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Sleek Action Confirmation Banner -->
                <div class="alert alert-primary d-flex align-items-center gap-3 p-3 mb-0 border-0 shadow-sm" style="border-radius: 12px; background: linear-gradient(135deg, rgba(115, 103, 240, 0.08) 0%, rgba(115, 103, 240, 0.16) 100%);">
                    <div class="avatar avatar-sm rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                        <i class="ti ti-shield-check fs-4"></i>
                    </div>
                    <div class="small text-dark flex-grow-1">
                        <div class="fw-bold mb-1 text-primary">Validasi Duplikasi Berhasil & Siap Dijalankan</div>
                        Sistem akan memproses data pengguna secara bertahap (batch 5 civitas/request) agar server dan mesin tidak mengalami timeout. Klik <strong>"ACC & Mulai Tambahkan ke Mesin"</strong> untuk mengeksekusi antrean.
                    </div>
                </div>
            </div>

            <!-- STEP 3: Progress & Live Execution -->
            <div class="modal-body p-4 d-none" id="batch-step-3">
                <div class="text-center py-2 mb-3">
                    <div class="avatar avatar-xl rounded-circle bg-label-primary mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <div class="spinner-border text-primary" role="status" id="batch-live-spinner" style="width: 2rem; height: 2rem;">
                            <span class="visually-hidden">Memproses...</span>
                        </div>
                    </div>
                    <h5 class="fw-bold text-dark mb-1" id="batch-status-title">Sedang Menambahkan Pengguna ke Mesin...</h5>
                    <p class="text-muted small mb-0" id="batch-status-subtitle">Mohon jangan menutup jendela browser ini hingga seluruh batch selesai.</p>
                </div>

                <!-- Progress Bar -->
                <div class="mb-4 bg-light p-3 rounded-3 border">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold text-dark small" id="batch-progress-text">Memproses: 0 / 0 Pengguna</span>
                        <span class="badge bg-primary rounded-pill px-3 py-1 fw-bold fs-6" id="batch-progress-percent">0%</span>
                    </div>
                    <div class="progress" style="height: 12px; border-radius: 6px; background-color: #e9ecef;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" 
                             id="batch-progress-bar" style="width: 0%;" 
                             aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>

                <!-- Counters Badge -->
                <div class="row g-3 mb-3 text-center">
                    <div class="col-4">
                        <div class="p-3 border rounded-3 shadow-none" style="background: rgba(40, 199, 111, 0.08); border-color: rgba(40, 199, 111, 0.25) !important;">
                            <small class="text-success fw-bold d-block mb-1"><i class="ti ti-check me-1"></i> Berhasil</small>
                            <span class="fs-4 fw-bold text-success" id="batch-count-success">0</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 border rounded-3 shadow-none" style="background: rgba(0, 207, 232, 0.08); border-color: rgba(0, 207, 232, 0.25) !important;">
                            <small class="text-info fw-bold d-block mb-1"><i class="ti ti-arrow-forward me-1"></i> Dilewati</small>
                            <span class="fs-4 fw-bold text-info" id="batch-count-skipped">0</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 border rounded-3 shadow-none" style="background: rgba(234, 84, 85, 0.08); border-color: rgba(234, 84, 85, 0.25) !important;">
                            <small class="text-danger fw-bold d-block mb-1"><i class="ti ti-x me-1"></i> Gagal</small>
                            <span class="fs-4 fw-bold text-danger" id="batch-count-failed">0</span>
                        </div>
                    </div>
                </div>

                <!-- Terminal-style Live Log Console -->
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <label class="form-label small fw-bold text-dark mb-0"><i class="ti ti-terminal me-1 text-primary"></i> Live Activity Log:</label>
                    <span class="badge bg-label-secondary font-monospace" style="font-size: 10px;">Autoscroll Active</span>
                </div>
                <div class="p-3 rounded-3 font-monospace small text-light shadow-inner" id="batch-live-log" 
                     style="background-color: #1e1e2d; height: 180px; overflow-y: auto; font-size: 11.5px; line-height: 1.6; border: 1px solid #2b2b40;">
                    <div class="text-muted">[Sistem] Siap memulai pengiriman batch massal...</div>
                </div>
            </div>

            <!-- Unified Modal Footer without Conflicting d-flex wrappers -->
            <div class="modal-footer bg-white border-top py-3 px-4 d-flex justify-content-between align-items-center">
                <div id="batch-footer-left">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal" id="btn-batch-cancel" style="border-radius: 8px;">
                        Batal
                    </button>
                    <button type="button" class="btn btn-outline-secondary d-none fw-semibold" id="btn-back-to-step-1" style="border-radius: 8px;">
                        <i class="ti ti-arrow-left me-1"></i> Kembali / Ubah Opsi
                    </button>
                    <button type="button" class="btn btn-outline-danger d-none fw-semibold" id="btn-pause-batch" style="border-radius: 8px;">
                        <i class="ti ti-player-pause me-1"></i> Hentikan Sementara
                    </button>
                </div>
                <div id="batch-footer-right">
                    <button type="button" class="btn btn-primary shadow-sm fw-semibold px-4" id="btn-precheck-batch" style="border-radius: 8px;">
                        <i class="ti ti-search me-1"></i> Periksa & Analisis Pengguna
                    </button>
                    <button type="button" class="btn btn-success d-none shadow-sm fw-semibold px-4 py-2" id="btn-start-batch-push" style="border-radius: 8px; background: linear-gradient(135deg, #28c76f 0%, #1f9d55 100%); border: none;">
                        <i class="ti ti-circle-check me-1 fs-5"></i> ACC & Mulai Tambahkan ke Mesin
                    </button>
                    <button type="button" class="btn btn-primary d-none shadow-sm fw-semibold px-4 py-2" id="btn-finish-batch" style="border-radius: 8px;">
                        <i class="ti ti-check-double me-1"></i> Selesai & Perbarui Tabel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Hapus Massal Pengguna di Mesin (Batch Delete Anti-Timeout) --}}
<div class="modal fade" id="modal-batch-delete-device-users" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0" style="border-radius: 16px; overflow: hidden;">
            <!-- Modal Header -->
            <div class="modal-header py-3 px-4 bg-white border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar avatar-md rounded-3 bg-label-danger d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;">
                        <i class="ti ti-trash-x fs-3 text-danger"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0">Hapus Pengguna dari Mesin Biometrik</h5>
                        <small class="text-muted">Proses penghapusan bertahap (batch) anti-timeout & sinkronisasi live</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="btn-close-batch-delete-modal"></button>
            </div>

            <!-- Modal Body: Confirmation & Progress -->
            <div class="modal-body p-4">
                <!-- Info Alert -->
                <div class="alert alert-danger d-flex align-items-center gap-3 p-3 mb-3 border-0 shadow-sm" style="border-radius: 12px; background: rgba(234, 84, 85, 0.08);">
                    <div class="avatar avatar-sm rounded-circle bg-danger text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                        <i class="ti ti-alert-triangle fs-4"></i>
                    </div>
                    <div class="small text-dark flex-grow-1">
                        <div class="fw-bold mb-1 text-danger">Peringatan Penghapusan dari Mesin Fisik</div>
                        Perintah <code>delete_userinfo</code> akan dikirimkan secara bertahap ke mesin bersangkutan untuk menghapus PIN, nama, dan seluruh template biometrik (sidik jari, wajah, RFID) pada mesin fisik.
                    </div>
                </div>

                <!-- Execution Status Area -->
                <div class="text-center py-2 mb-3" id="batch-delete-header-status">
                    <div class="avatar avatar-xl rounded-circle bg-label-danger mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <div class="spinner-border text-danger" role="status" id="batch-delete-live-spinner" style="width: 2rem; height: 2rem; display: none;">
                            <span class="visually-hidden">Memproses...</span>
                        </div>
                        <i class="ti ti-trash text-danger fs-1" id="batch-delete-static-icon"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1" id="batch-delete-status-title">Siap Menghapus Pengguna Terpilih</h5>
                    <p class="text-muted small mb-0" id="batch-delete-status-subtitle">Total <strong class="text-danger" id="batch-delete-total-preview">0</strong> akun user dipilih untuk dihapus dari mesin.</p>
                </div>

                <!-- Progress Bar -->
                <div class="mb-3 bg-light p-3 rounded-3 border">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold text-dark small" id="batch-delete-progress-text">Menunggu konfirmasi...</span>
                        <span class="badge bg-danger rounded-pill px-3 py-1 fw-bold fs-6" id="batch-delete-progress-percent">0%</span>
                    </div>
                    <div class="progress" style="height: 12px; border-radius: 6px; background-color: #e9ecef;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" 
                             id="batch-delete-progress-bar" style="width: 0%;" 
                             aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>

                <!-- Counters Badge -->
                <div class="row g-3 mb-3 text-center">
                    <div class="col-4">
                        <div class="p-2 border rounded-3 shadow-none bg-light">
                            <small class="text-muted fw-bold d-block mb-1">Total Target</small>
                            <span class="fs-5 fw-bold text-dark" id="batch-delete-count-total">0</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 border rounded-3 shadow-none" style="background: rgba(40, 199, 111, 0.08); border-color: rgba(40, 199, 111, 0.25) !important;">
                            <small class="text-success fw-bold d-block mb-1"><i class="ti ti-check me-1"></i> Terhapus</small>
                            <span class="fs-5 fw-bold text-success" id="batch-delete-count-success">0</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 border rounded-3 shadow-none" style="background: rgba(234, 84, 85, 0.08); border-color: rgba(234, 84, 85, 0.25) !important;">
                            <small class="text-danger fw-bold d-block mb-1"><i class="ti ti-x me-1"></i> Gagal/Offline</small>
                            <span class="fs-5 fw-bold text-danger" id="batch-delete-count-failed">0</span>
                        </div>
                    </div>
                </div>

                <!-- Live Execution Console Log -->
                <div class="card border shadow-none" style="border-radius: 10px; background-color: #0f172a; border-color: #1e293b !important;">
                    <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center" style="background-color: #1e293b; border-bottom: 1px solid #334155;">
                        <span class="font-monospace text-light small fw-bold">
                            <i class="ti ti-terminal me-1 text-danger"></i> Log Eksekusi Mesin (Real-time Batch Console)
                        </span>
                        <span class="badge bg-secondary font-monospace" style="font-size: 10px;">Anti-Timeout Active</span>
                    </div>
                    <div class="card-body p-3 font-monospace small" id="batch-delete-live-log" 
                         style="max-height: 180px; overflow-y: auto; color: #cbd5e1; line-height: 1.6; font-size: 0.78rem;">
                        <div class="text-secondary">[Sistem] Menunggu Anda mengklik tombol "Mulai Hapus Sekarang"...</div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer py-3 px-4 bg-light border-top d-flex justify-content-between">
                <div>
                    <button type="button" class="btn btn-outline-warning btn-sm d-none" id="btn-pause-batch-delete">
                        <i class="ti ti-player-pause me-1"></i> Jeda
                    </button>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal" id="btn-cancel-batch-delete">Batal</button>
                    <button type="button" class="btn btn-danger" id="btn-start-batch-delete">
                        <i class="ti ti-trash me-1"></i> Ya, Mulai Hapus dari Mesin Sekarang
                    </button>
                    <button type="button" class="btn btn-primary d-none" data-bs-dismiss="modal" id="btn-finish-batch-delete">
                        <i class="ti ti-check me-1"></i> Selesai & Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

