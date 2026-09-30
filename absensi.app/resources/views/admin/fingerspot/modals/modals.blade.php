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
