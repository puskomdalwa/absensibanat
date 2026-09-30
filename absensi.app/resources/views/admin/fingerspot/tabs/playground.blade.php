<div class="row mb-4">
    <div class="col-12">
        <h5 class="mb-1 text-primary fw-bold"><i class="ti ti-terminal-2 me-2"></i>API Playground & Interactive Tester</h5>
        <p class="text-muted mb-0 small">
            Uji coba seluruh endpoint REST API Fingerspot secara langsung dengan parameter custom. Panel ini menampilkan waktu respons (latensi) serta format JSON lengkap.
        </p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-light py-3">
                <h6 class="mb-0 fw-bold"><i class="ti ti-send me-1"></i> Parameter Permintaan (Request)</h6>
            </div>
            <div class="card-body pt-3">
                <form id="form-api-tester">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="test-endpoint">Pilih Endpoint API <span class="text-danger">*</span></label>
                        <select class="form-select" id="test-endpoint" name="endpoint" required>
                            <optgroup label="Operasi Sinkron">
                                <option value="get_device">POST /api/get_device (Informasi Mesin)</option>
                                <option value="get_attlog">POST /api/get_attlog (Tarik Log Absensi)</option>
                            </optgroup>
                            <optgroup label="Operasi Asinkron (Device Commands)">
                                <option value="get_all_pin">POST /api/get_all_pin (Ambil Semua PIN)</option>
                                <option value="get_userinfo">POST /api/get_userinfo (Ambil Info User PIN)</option>
                                <option value="set_userinfo">POST /api/set_userinfo (Tambah / Ubah Info User)</option>
                                <option value="delete_userinfo">POST /api/delete_userinfo (Hapus User dari Mesin)</option>
                                <option value="set_time">POST /api/set_time (Sinkronisasi Zona Waktu)</option>
                                <option value="reg_online">POST /api/reg_online (Pendaftaran Biometrik Jarak Jauh)</option>
                                <option value="restart_device">POST /api/restart_device (Restart Mesin)</option>
                            </optgroup>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="test-cloud-id">Target Mesin (Cloud ID) <span class="text-danger">*</span></label>
                        <select class="form-select" id="test-cloud-id" name="cloud_id" required>
                            @foreach ($devices as $dev)
                                <option value="{{ $dev->cloud_id }}">{{ $dev->name }} ({{ $dev->cloud_id }})</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Dynamic parameter groups --}}
                    {{-- Dates for get_attlog --}}
                    <div class="dynamic-param-group d-none" id="param-group-attlog">
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-semibold">Start Date</label>
                                <input type="text" class="form-control form-control-sm date-picker" name="start_date" value="{{ date('Y-m-d', strtotime('-1 day')) }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold">End Date</label>
                                <input type="text" class="form-control form-control-sm date-picker" name="end_date" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>

                    {{-- PIN for user endpoints --}}
                    <div class="dynamic-param-group d-none" id="param-group-pin">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">User PIN</label>
                            <input type="text" class="form-control form-control-sm" name="pin" value="1" placeholder="Nomor PIN di mesin">
                        </div>
                    </div>

                    {{-- Full user params for set_userinfo --}}
                    <div class="dynamic-param-group d-none" id="param-group-setuser">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Nama Tampilan</label>
                            <input type="text" class="form-control form-control-sm" name="name" value="Santri Test" placeholder="Nama user di mesin">
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-semibold">Hak Akses (Privilege)</label>
                                <select class="form-select form-select-sm" name="privilege">
                                    <option value="1">1 - Pengguna (User)</option>
                                    <option value="2">2 - Admin</option>
                                    <option value="3">3 - Sub-Admin</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold">PIN Password</label>
                                <input type="password" class="form-control form-control-sm" name="password" placeholder="Opsional">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">RFID Card Number</label>
                            <input type="text" class="form-control form-control-sm" name="rfid" placeholder="Opsional">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Template Biometrik (Opsional)</label>
                            <textarea class="form-control form-control-sm" name="template" rows="2" placeholder="Diperoleh dari get_userinfo atau kosongkan"></textarea>
                        </div>
                    </div>

                    {{-- Timezone for set_time --}}
                    <div class="dynamic-param-group d-none" id="param-group-time">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Zona Waktu (IANA)</label>
                            <select class="form-select form-select-sm" name="timezone">
                                <option value="Asia/Jakarta">Asia/Jakarta (WIB)</option>
                                <option value="Asia/Makassar">Asia/Makassar (WITA)</option>
                                <option value="Asia/Jayapura">Asia/Jayapura (WIT)</option>
                            </select>
                        </div>
                    </div>

                    {{-- Verification for reg_online --}}
                    <div class="dynamic-param-group d-none" id="param-group-regonline">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Tipe Biometrik (Verification Mode)</label>
                            <select class="form-select form-select-sm" name="verification">
                                <option value="0">0 - Sidik Jari (Telunjuk Kanan)</option>
                                <option value="1">1 - Sidik Jari (Ibu Jari Kanan)</option>
                                <option value="2">2 - Sidik Jari (Jari Tengah Kanan)</option>
                                <option value="3">3 - Sidik Jari (Jari Manis Kanan)</option>
                                <option value="4">4 - Sidik Jari (Kelingking Kanan)</option>
                                <option value="5">5 - Sidik Jari (Telunjuk Kiri)</option>
                                <option value="12">12 - Wajah (Face Recognition)</option>
                                <option value="13">13 - Palm Vein</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 waves-effect waves-light" id="btn-run-tester">
                        <i class="ti ti-player-play me-1"></i> Eksekusi API
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Response View --}}
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                <div class="d-flex align-items-center gap-2">
                    <h6 class="mb-0 fw-bold"><i class="ti ti-code me-1"></i> Respons Server (Response Body)</h6>
                    <span class="badge bg-label-secondary d-none" id="badge-test-latency">0 ms</span>
                </div>
                <button type="button" class="btn btn-xs btn-label-secondary btn-copy" id="btn-copy-test-response" data-clipboard-text="">
                    <i class="ti ti-copy ti-xs me-1"></i> Salin JSON
                </button>
            </div>
            <div class="card-body p-0">
                <pre class="m-0 p-3 bg-dark text-white rounded-bottom" style="max-height: 480px; overflow-y: auto; font-size: 0.85rem;"><code id="code-test-response">// Pilih endpoint di sebelah kiri dan klik "Eksekusi API" untuk melihat output live JSON di sini.</code></pre>
            </div>
        </div>
    </div>
</div>
