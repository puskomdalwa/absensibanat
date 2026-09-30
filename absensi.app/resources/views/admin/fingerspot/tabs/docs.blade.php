<div class="row mb-4">
    <div class="col-12">
        <h5 class="mb-1 text-primary fw-bold"><i class="ti ti-book me-2"></i>Dokumentasi Integrasi Fingerspot Developer API</h5>
        <p class="text-muted mb-0 small">
            Panduan teknis dan referensi lengkap berdasarkan standar resmi <a href="https://developer.fingerspot.io/docs/introduction" target="_blank" class="fw-semibold text-primary">developer.fingerspot.io <i class="ti ti-external-link ti-xs"></i></a>.
        </p>
    </div>
</div>

{{-- Overview & Flow Cards --}}
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-label-primary py-3">
                <h6 class="mb-0 fw-bold text-primary"><i class="ti ti-arrows-left-right me-2"></i>1. Pola Respons: Sinkron vs Asinkron</h6>
            </div>
            <div class="card-body pt-3">
                <div class="mb-3">
                    <span class="badge bg-label-success me-1">Operasi Sinkron</span>
                    <p class="small text-muted mb-2 mt-1">
                        Endpoint seperti <code>get_attlog</code> dan <code>get_device</code> mengembalikan payload data lengkap secara instan langsung di dalam body respons HTTP.
                    </p>
                </div>
                <hr class="my-2">
                <div>
                    <span class="badge bg-label-warning me-1">Operasi Asinkron (Device Commands)</span>
                    <p class="small text-muted mb-2 mt-1">
                        Endpoint perintah perangkat seperti <code>get_userinfo</code>, <code>set_userinfo</code>, <code>set_time</code>, dll., mengembalikan acknowledgement langsung berupa:
                    </p>
                    <pre class="bg-light p-2 rounded mb-2 font-monospace small"><code>{"success": true, "trans_id": "CMD-12345"}</code></pre>
                    <p class="small text-muted mb-0">
                        Hasil eksekusi sesungguhnya akan dikirimkan oleh mesin absensi secara asinkron ke <strong>URL Webhook</strong> yang terdaftar. Nilai <code>trans_id</code> pada callback selalu sama dengan permintaan awal sehingga aplikasi dapat mengaitkannya secara otomatis.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-label-info py-3">
                <h6 class="mb-0 fw-bold text-info"><i class="ti ti-webhook me-2"></i>2. Konfigurasi Webhook & Push Real-Time</h6>
            </div>
            <div class="card-body pt-3">
                <p class="small text-muted mb-2">
                    URL Webhook lokal aplikasi yang bertugas menerima seluruh event scan dan callback dari Fingerspot Cloud:
                </p>
                <div class="input-group input-group-merge mb-3">
                    <span class="input-group-text"><i class="ti ti-link"></i></span>
                    <input type="text" class="form-control font-monospace text-primary bg-light" id="input-webhook-url" value="{{ $webhookUrl }}" readonly>
                    <button class="btn btn-outline-primary btn-copy" type="button" data-clipboard-text="{{ $webhookUrl }}">
                        <i class="ti ti-copy me-1"></i> Salin URL
                    </button>
                </div>

                <div class="alert alert-primary d-flex align-items-center p-2 mb-0" role="alert">
                    <i class="ti ti-broadcast ti-md me-2"></i>
                    <div class="small">
                        <strong>Push Attlog Real-time:</strong> Setiap kali santri/user melakukan scan sidik jari atau wajah di mesin, mesin secara spontan mengirimkan event webhook <code>type: "attlog"</code> ke URL di atas tanpa perlu trigger API.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Event Webhook Types Table --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-light py-3">
        <h6 class="mb-0 fw-bold text-dark"><i class="ti ti-list-check me-2"></i>Tipe Event Callback Webhook</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 180px;">Event Type</th>
                    <th>Deskripsi</th>
                    <th style="width: 130px;">Korelasi ID</th>
                    <th>Struktur Data Callback</th>
                </tr>
            </thead>
            <tbody class="small">
                <tr>
                    <td><span class="badge bg-label-success">attlog</span></td>
                    <td>Event pemindaian kehadiran real-time spontan dari mesin.</td>
                    <td><span class="badge bg-label-secondary">Spontan (N/A)</span></td>
                    <td><code>{"pin": "...", "scan": "YYYY-MM-DD HH:mm:ss", "verify": 1, "status_scan": 0}</code></td>
                </tr>
                <tr>
                    <td><span class="badge bg-label-info">get_userinfo</span></td>
                    <td>Hasil pembacaan detail data & template biometrik pengguna dari mesin.</td>
                    <td><span class="badge bg-label-primary">trans_id</span></td>
                    <td><code>{"pin": "...", "name": "...", "privilege": 1, "finger": 1, "template": "..."}</code></td>
                </tr>
                <tr>
                    <td><span class="badge bg-label-info">get_userid_list</span></td>
                    <td>Daftar seluruh ID pengguna (PIN) yang terdaftar di mesin.</td>
                    <td><span class="badge bg-label-primary">trans_id</span></td>
                    <td><code>{"total": 25, "pin_arr": ["1", "2", "3", ...]}</code></td>
                </tr>
                <tr>
                    <td><span class="badge bg-label-warning">set_userinfo</span></td>
                    <td>Konfirmasi hasil pendaftaran / update data pengguna ke mesin.</td>
                    <td><span class="badge bg-label-primary">trans_id</span></td>
                    <td><code>{"status": "1"}</code> (1 = Sukses, 2 = Gagal)</td>
                </tr>
                <tr>
                    <td><span class="badge bg-label-danger">delete_userinfo</span></td>
                    <td>Konfirmasi penghapusan data pengguna dari mesin.</td>
                    <td><span class="badge bg-label-primary">trans_id</span></td>
                    <td><code>{"status": "1"}</code> (1 = Sukses, 2 = Gagal)</td>
                </tr>
                <tr>
                    <td><span class="badge bg-label-secondary">set_time</span></td>
                    <td>Konfirmasi sinkronisasi zona waktu mesin.</td>
                    <td><span class="badge bg-label-primary">trans_id</span></td>
                    <td><code>{"status": "1"}</code> (1 = Sukses, 2 = Gagal)</td>
                </tr>
                <tr>
                    <td><span class="badge bg-label-primary">register_online</span></td>
                    <td>Hasil perintah enroll biometrik online (fingerprint / face).</td>
                    <td><span class="badge bg-label-primary">trans_id</span></td>
                    <td><code>{"status": "1"}</code> (1 = Sukses, 2 = Gagal)</td>
                </tr>
                <tr>
                    <td><span class="badge bg-label-danger">restart_device</span></td>
                    <td>Konfirmasi perintah restart mesin absensi.</td>
                    <td><span class="badge bg-label-primary">trans_id</span></td>
                    <td><code>{"status": "1"}</code> (1 = Sukses, 2 = Gagal)</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Reference Codes (Verify & Status Scan) --}}
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-light py-3">
                <h6 class="mb-0 fw-bold"><i class="ti ti-fingerprint me-2"></i>Kode Metode Verifikasi (Verify)</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Kode</th>
                            <th>Metode</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td><code>1</code></td><td><strong>Fingerprint</strong></td><td>Pemindaian sidik jari</td></tr>
                        <tr><td><code>2</code></td><td><strong>Password</strong></td><td>Input kode PIN pada keypad</td></tr>
                        <tr><td><code>3</code></td><td><strong>Card</strong></td><td>RFID / Kartu akses</td></tr>
                        <tr><td><code>4</code></td><td><strong>Face</strong></td><td>Pengenalan wajah</td></tr>
                        <tr><td><code>6</code></td><td><strong>Vein</strong></td><td>Pengenalan pembuluh darah (Palm/Finger Vein)</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-light py-3">
                <h6 class="mb-0 fw-bold"><i class="ti ti-switch-horizontal me-2"></i>Kode Status Scan (status_scan)</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Nilai</th>
                            <th>Status Scan</th>
                            <th>Tipe Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td><code>0</code></td><td><span class="badge bg-label-success">Scan In</span></td><td>Absensi Datang / Masuk</td></tr>
                        <tr><td><code>1</code></td><td><span class="badge bg-label-danger">Scan Out</span></td><td>Absensi Pulang / Keluar</td></tr>
                        <tr><td><code>2</code></td><td><span class="badge bg-label-warning">Break In</span></td><td>Masuk Istirahat</td></tr>
                        <tr><td><code>3</code></td><td><span class="badge bg-label-info">Break Out</span></td><td>Keluar Istirahat</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- All Endpoints Detailed Reference --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-light py-3">
        <h6 class="mb-0 fw-bold"><i class="ti ti-code me-2"></i>Katalog Lengkap REST API Endpoints</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>Metode & Endpoint</th>
                    <th>Sifat</th>
                    <th>Parameter Utama</th>
                    <th>Fungsi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>POST /api/get_device</code></td>
                    <td><span class="badge bg-label-success">Sinkron</span></td>
                    <td><code>trans_id, cloud_id</code></td>
                    <td>Cek status konfigurasi, webhook URL, dan last activity mesin.</td>
                </tr>
                <tr>
                    <td><code>POST /api/get_attlog</code></td>
                    <td><span class="badge bg-label-success">Sinkron</span></td>
                    <td><code>trans_id, cloud_id, start_date, end_date</code></td>
                    <td>Tarik riwayat kehadiran (maks 2 hari per tarikan, hingga 60 hari ke belakang).</td>
                </tr>
                <tr>
                    <td><code>POST /api/get_all_pin</code></td>
                    <td><span class="badge bg-label-warning">Asinkron</span></td>
                    <td><code>trans_id, cloud_id</code></td>
                    <td>Mengambil daftar seluruh user ID (PIN) yang terdaftar di mesin absensi.</td>
                </tr>
                <tr>
                    <td><code>POST /api/get_userinfo</code></td>
                    <td><span class="badge bg-label-warning">Asinkron</span></td>
                    <td><code>trans_id, cloud_id, pin</code></td>
                    <td>Mengambil nama, privilege, dan data biometrik template pengguna.</td>
                </tr>
                <tr>
                    <td><code>POST /api/set_userinfo</code></td>
                    <td><span class="badge bg-label-warning">Asinkron</span></td>
                    <td><code>trans_id, cloud_id, data: {pin, name, privilege, ...}</code></td>
                    <td>Mendaftarkan atau memperbarui pengguna beserta template biometrik ke mesin.</td>
                </tr>
                <tr>
                    <td><code>POST /api/delete_userinfo</code></td>
                    <td><span class="badge bg-label-warning">Asinkron</span></td>
                    <td><code>trans_id, cloud_id, pin</code></td>
                    <td>Menghapus data pengguna dari mesin absensi secara remote.</td>
                </tr>
                <tr>
                    <td><code>POST /api/set_time</code></td>
                    <td><span class="badge bg-label-warning">Asinkron</span></td>
                    <td><code>trans_id, cloud_id, timezone</code></td>
                    <td>Menyetel zona waktu mesin (contoh: <code>Asia/Jakarta</code>).</td>
                </tr>
                <tr>
                    <td><code>POST /api/reg_online</code></td>
                    <td><span class="badge bg-label-warning">Asinkron</span></td>
                    <td><code>trans_id, cloud_id, pin, verification</code></td>
                    <td>Memulai proses perekaman sidik jari/wajah secara live di mesin.</td>
                </tr>
                <tr>
                    <td><code>POST /api/restart_device</code></td>
                    <td><span class="badge bg-label-warning">Asinkron</span></td>
                    <td><code>trans_id, cloud_id</code></td>
                    <td>Merestart mesin absensi dari jarak jauh.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
