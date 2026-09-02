@php
    $baseUrl = url('/api/client/v1');
    $helperUrl = url('/api/client/v1/signature-helper');
    $absensiUrl = url('/api/client/v1/absensi');
    $rekapUrl = url('/api/client/v1/absensi/rekap');

    // === CONTOH RESPONS JSON GET DATA ABSENSI ===
    $jsonAbsensi = '{
  "status": true,
  "message": "Data absensi berhasil diambil",
  "periode": {
    "mode": "bulan_tahun",
    "start_date": "2026-07-01",
    "end_date": "2026-07-31",
    "bulan": 7,
    "tahun": 2026
  },
  "data": [
    {
      "id": 125,
      "tgl_absen": "2026-07-15",
      "pagi": "07:45",
      "sore": "16:15",
      "selisih_menit": 510,
      "durasi_jam": 8.5,
      "durasi_teks": "8 jam 30 menit",
      "keterangan": "Hadir",
      "kode_user": "KD-1001",
      "user": {
        "id": 10,
        "kode": "KD-1001",
        "kode_user": "KD-1001",
        "name": "Budi Santoso",
        "username": "budi",
        "email": "budi@example.com",
        "departemen": "IT"
      },
      "kategori": {
        "id": 1,
        "kode": "KAT-A",
        "nama": "Kategori A (5 Jam)",
        "nominal": 25000
      },
      "perolehan_dana": 25000
    }
  ],
  "pagination": {
    "total": 45,
    "per_page": 15,
    "current_page": 1,
    "last_page": 3,
    "next_page_url": "' . $absensiUrl . '?page=2",
    "prev_page_url": null
  }
}';

    // === CONTOH RESPONS JSON REKAP DATA ABSENSI ===
    $jsonRekap = '{
  "status": true,
  "message": "Rekap absensi berhasil diambil",
  "periode": {
    "mode": "bulan_tahun",
    "start_date": "2026-07-01",
    "end_date": "2026-07-31",
    "bulan": 7,
    "tahun": 2026
  },
  "data": [
    {
      "kode_user": "KD-1001",
      "user": {
        "id": 10,
        "kode": "KD-1001",
        "kode_user": "KD-1001",
        "name": "Budi Santoso",
        "username": "budi",
        "email": "budi@example.com",
        "departemen": "IT"
      },
      "rekap_per_kategori": [
        {
          "kategori_id": 1,
          "kode": "KAT-A",
          "nama": "Kategori A (5 Jam)",
          "nominal": 25000,
          "jumlah": 18,
          "perolehan_dana": 450000
        },
        {
          "kategori_id": 2,
          "kode": "KAT-B",
          "nama": "Kategori B (4 Jam)",
          "nominal": 20000,
          "jumlah": 4,
          "perolehan_dana": 80000
        },
        {
          "kategori_id": 3,
          "kode": "KAT-C",
          "nama": "Kategori C (3 Jam)",
          "nominal": 15000,
          "jumlah": 0,
          "perolehan_dana": 0
        }
      ],
      "total_jam_keseluruhan": {
        "total_menit": 9900,
        "total_jam": 165,
        "format_teks": "165 jam"
      },
      "total_perolehan_dana": 530000
    }
  ],
  "pagination": {
    "total": 50,
    "per_page": 15,
    "current_page": 1,
    "last_page": 4,
    "next_page_url": "' . $rekapUrl . '?page=2",
    "prev_page_url": null
  }
}';

    // === CONTOH KODE UNTUK GET DATA ABSENSI BIASA ===
    $curlAbsensi = '#!/bin/bash
API_KEY="ISI_API_KEY_ANDA"
SECRET_KEY="ISI_SECRET_KEY_ANDA"
TIMESTAMP=$(date +%s)
METHOD="GET"
PATH_ENDPOINT="api/client/v1/absensi"

# 1. Buat String to Sign (METHOD:PATH:TIMESTAMP)
STRING_TO_SIGN="${METHOD}:${PATH_ENDPOINT}:${TIMESTAMP}"

# 2. Generate HMAC SHA256 Signature
SIGNATURE=$(echo -n "$STRING_TO_SIGN" | openssl dgst -sha256 -hmac "$SECRET_KEY" | awk \'{print $2}\')

# 3. Kirim Request
curl -X GET "' . $absensiUrl . '?start_date=2026-07-01&end_date=2026-07-15&limit=15" \
  -H "X-Api-Key: $API_KEY" \
  -H "X-Timestamp: $TIMESTAMP" \
  -H "X-Signature: $SIGNATURE" \
  -H "Accept: application/json"';

    $phpAbsensi = '<?php
$apiKey    = "ISI_API_KEY_ANDA";
$secretKey = "ISI_SECRET_KEY_ANDA";
$timestamp = time();
$method    = "GET";
$path      = "api/client/v1/absensi";

// 1. Buat String to Sign
$stringToSign = strtoupper($method) . ":" . $path . ":" . $timestamp;

// 2. Generate HMAC SHA256 Signature
$signature = hash_hmac("sha256", $stringToSign, $secretKey);

// 3. Request menggunakan cURL Native
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "' . $absensiUrl . '?start_date=2026-07-01&end_date=2026-07-15&limit=15");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-Api-Key: " . $apiKey,
    "X-Timestamp: " . $timestamp,
    "X-Signature: " . $signature,
    "Accept: application/json"
]);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
print_r($data);';

    $jsAbsensi = 'const crypto = require("crypto");
const axios = require("axios");

const apiKey = "ISI_API_KEY_ANDA";
const secretKey = "ISI_SECRET_KEY_ANDA";
const timestamp = Math.floor(Date.now() / 1000);
const method = "GET";
const pathEndpoint = "api/client/v1/absensi";

// 1. Buat String to Sign
const stringToSign = `${method.toUpperCase()}:${pathEndpoint}:${timestamp}`;

// 2. Generate HMAC SHA256 Signature
const signature = crypto.createHmac("sha256", secretKey)
                        .update(stringToSign)
                        .digest("hex");

// 3. Kirim Request
axios.get("' . $absensiUrl . '", {
    params: { start_date: "2026-07-01", end_date: "2026-07-15", limit: 15 },
    headers: {
        "X-Api-Key": apiKey,
        "X-Timestamp": timestamp.toString(),
        "X-Signature": signature,
        "Accept": "application/json"
    }
})
.then(response => {
    console.log("Response:", response.data);
})
.catch(error => {
    console.error("Error:", error.response ? error.response.data : error.message);
});';

    $pythonAbsensi = 'import time
import hmac
import hashlib
import requests

api_key = "ISI_API_KEY_ANDA"
secret_key = "ISI_SECRET_KEY_ANDA"
timestamp = int(time.time())
method = "GET"
path_endpoint = "api/client/v1/absensi"

# 1. Buat String to Sign
string_to_sign = f"{method.upper()}:{path_endpoint}:{timestamp}"

# 2. Generate HMAC SHA256 Signature
signature = hmac.new(
    secret_key.encode("utf-8"),
    string_to_sign.encode("utf-8"),
    hashlib.sha256
).hexdigest()

# 3. Kirim Request
url = "' . $absensiUrl . '"
headers = {
    "X-Api-Key": api_key,
    "X-Timestamp": str(timestamp),
    "X-Signature": signature,
    "Accept": "application/json"
}
params = {
    "start_date": "2026-07-01",
    "end_date": "2026-07-15",
    "limit": 15
}

response = requests.get(url, headers=headers, params=params)
print("Status Code:", response.status_code)
print("Response JSON:", response.json())';


    // === CONTOH KODE UNTUK REKAP DATA ABSENSI ===
    $curlRekap = '#!/bin/bash
API_KEY="ISI_API_KEY_ANDA"
SECRET_KEY="ISI_SECRET_KEY_ANDA"
TIMESTAMP=$(date +%s)
METHOD="GET"
PATH_ENDPOINT="api/client/v1/absensi/rekap"

# 1. Buat String to Sign (METHOD:PATH:TIMESTAMP)
STRING_TO_SIGN="${METHOD}:${PATH_ENDPOINT}:${TIMESTAMP}"

# 2. Generate HMAC SHA256 Signature
SIGNATURE=$(echo -n "$STRING_TO_SIGN" | openssl dgst -sha256 -hmac "$SECRET_KEY" | awk \'{print $2}\')

# 3. Kirim Request
curl -X GET "' . $rekapUrl . '?mode=bulan_tahun&bulan=7&tahun=2026" \
  -H "X-Api-Key: $API_KEY" \
  -H "X-Timestamp: $TIMESTAMP" \
  -H "X-Signature: $SIGNATURE" \
  -H "Accept: application/json"';

    $phpRekap = '<?php
$apiKey    = "ISI_API_KEY_ANDA";
$secretKey = "ISI_SECRET_KEY_ANDA";
$timestamp = time();
$method    = "GET";
$path      = "api/client/v1/absensi/rekap";

// 1. Buat String to Sign
$stringToSign = strtoupper($method) . ":" . $path . ":" . $timestamp;

// 2. Generate HMAC SHA256 Signature
$signature = hash_hmac("sha256", $stringToSign, $secretKey);

// 3. Request menggunakan cURL Native
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "' . $rekapUrl . '?mode=bulan_tahun&bulan=7&tahun=2026");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-Api-Key: " . $apiKey,
    "X-Timestamp: " . $timestamp,
    "X-Signature: " . $signature,
    "Accept: application/json"
]);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
print_r($data);';

    $jsRekap = 'const crypto = require("crypto");
const axios = require("axios");

const apiKey = "ISI_API_KEY_ANDA";
const secretKey = "ISI_SECRET_KEY_ANDA";
const timestamp = Math.floor(Date.now() / 1000);
const method = "GET";
const pathEndpoint = "api/client/v1/absensi/rekap";

// 1. Buat String to Sign
const stringToSign = `${method.toUpperCase()}:${pathEndpoint}:${timestamp}`;

// 2. Generate HMAC SHA256 Signature
const signature = crypto.createHmac("sha256", secretKey)
                        .update(stringToSign)
                        .digest("hex");

// 3. Kirim Request
axios.get("' . $rekapUrl . '", {
    params: { mode: "bulan_tahun", bulan: 7, tahun: 2026 },
    headers: {
        "X-Api-Key": apiKey,
        "X-Timestamp": timestamp.toString(),
        "X-Signature": signature,
        "Accept": "application/json"
    }
})
.then(response => {
    console.log("Response:", response.data);
})
.catch(error => {
    console.error("Error:", error.response ? error.response.data : error.message);
});';

    $pythonRekap = 'import time
import hmac
import hashlib
import requests

api_key = "ISI_API_KEY_ANDA"
secret_key = "ISI_SECRET_KEY_ANDA"
timestamp = int(time.time())
method = "GET"
path_endpoint = "api/client/v1/absensi/rekap"

# 1. Buat String to Sign
string_to_sign = f"{method.upper()}:{path_endpoint}:{timestamp}"

# 2. Generate HMAC SHA256 Signature
signature = hmac.new(
    secret_key.encode("utf-8"),
    string_to_sign.encode("utf-8"),
    hashlib.sha256
).hexdigest()

# 3. Kirim Request
url = "' . $rekapUrl . '"
headers = {
    "X-Api-Key": api_key,
    "X-Timestamp": str(timestamp),
    "X-Signature": signature,
    "Accept": "application/json"
}
params = {
    "mode": "bulan_tahun",
    "bulan": 7,
    "tahun": 2026
}

response = requests.get(url, headers=headers, params=params)
print("Status Code:", response.status_code)
print("Response JSON:", response.json())';
@endphp

<!-- Highlight.js Atom One Dark Theme CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css">

<style>
    #card-api-docs .custom-doc-pills .nav-link {
        font-weight: 600;
        border-radius: 0.5rem;
        transition: all 0.2s ease-in-out;
        color: #566a7f;
        background-color: #f8f9fa;
        border: 1px solid #d9dee3;
        padding: 0.6rem 1.2rem;
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
    }
    #card-api-docs .custom-doc-pills .nav-link:hover {
        background-color: #e2e6eb;
        color: #384c61;
    }
    #card-api-docs .custom-doc-pills .nav-link.active {
        background-color: #696cff !important;
        color: #ffffff !important;
        border-color: #696cff !important;
        box-shadow: 0 0.125rem 0.25rem 0 rgba(105, 108, 255, 0.4);
    }

    /* One Dark IDE Window Container */
    .one-dark-window {
        background: #282c34 !important;
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
        border: 1px solid #181a1f;
        overflow: hidden;
        margin-bottom: 1.5rem;
        font-family: 'Fira Code', 'JetBrains Mono', Consolas, Monaco, monospace;
    }
    .one-dark-header {
        background: #21252b !important;
        padding: 0.65rem 1rem;
        border-bottom: 1px solid #181a1f;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .one-dark-header .mac-buttons {
        display: flex;
        gap: 6px;
        align-items: center;
    }
    .one-dark-header .mac-btn {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
    }
    .mac-btn.close-btn { background: #ff5f56; }
    .mac-btn.min-btn { background: #ffbd2e; }
    .mac-btn.max-btn { background: #27c93f; }
    .one-dark-header .file-title {
        color: #abb2bf;
        font-size: 0.825rem;
        font-weight: 500;
        letter-spacing: 0.3px;
        display: flex;
        align-items: center;
    }
    .one-dark-header .btn-copy-code {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #abb2bf;
        font-size: 0.75rem;
        padding: 0.3rem 0.75rem;
        border-radius: 4px;
        transition: all 0.2s;
        cursor: pointer;
        display: flex;
        align-items: center;
    }
    .one-dark-header .btn-copy-code:hover {
        background: rgba(255, 255, 255, 0.18);
        color: #ffffff;
    }
    .one-dark-window pre {
        background: #282c34 !important;
        margin: 0 !important;
        padding: 1.25rem !important;
        max-height: 420px;
        overflow-y: auto;
        overflow-x: auto;
        color: #abb2bf;
    }
    .one-dark-window pre code {
        background: transparent !important;
        font-size: 0.875rem !important;
        line-height: 1.65 !important;
        text-shadow: none !important;
        padding: 0 !important;
        font-family: 'Fira Code', 'JetBrains Mono', Consolas, Monaco, monospace !important;
    }
    /* Pastikan warna sintaks One Dark dari Highlight.js menyala terang */
    .one-dark-window pre code .hljs-keyword,
    .one-dark-window pre code .hljs-selector-tag,
    .one-dark-window pre code .hljs-subst { color: #e06c75 !important; font-weight: 600; }
    .one-dark-window pre code .hljs-string,
    .one-dark-window pre code .hljs-doctag { color: #98c379 !important; }
    .one-dark-window pre code .hljs-title,
    .one-dark-window pre code .hljs-section,
    .one-dark-window pre code .hljs-selector-id { color: #61afef !important; font-weight: 600; }
    .one-dark-window pre code .hljs-number,
    .one-dark-window pre code .hljs-literal,
    .one-dark-window pre code .hljs-variable,
    .one-dark-window pre code .hljs-template-variable { color: #d19a66 !important; }
    .one-dark-window pre code .hljs-comment,
    .one-dark-window pre code .hljs-quote { color: #5c6370 !important; font-style: italic; }
    .one-dark-window pre code .hljs-built_in,
    .one-dark-window pre code .hljs-type { color: #e5c07b !important; }
    .one-dark-window pre code .hljs-attr,
    .one-dark-window pre code .hljs-attribute { color: #d19a66 !important; }
    /* Custom dark scrollbars inside One Dark Window */
    .one-dark-window pre::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    .one-dark-window pre::-webkit-scrollbar-track {
        background: #21252b;
    }
    .one-dark-window pre::-webkit-scrollbar-thumb {
        background: #4b5263;
        border-radius: 4px;
    }
    .one-dark-window pre::-webkit-scrollbar-thumb:hover {
        background: #5c6370;
    }
</style>

<div class="card mt-4" id="card-api-docs">
    <div class="card-header border-bottom d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0 d-flex align-items-center">
            <i class="ti ti-book me-2 text-primary"></i> Dokumentasi & Panduan Integrasi API Absensi
        </h5>
        <span class="badge bg-label-info">v1.0</span>
    </div>
    <div class="card-body pt-4">
        <ul class="nav nav-pills custom-doc-pills flex-column flex-md-row gap-2 pb-4 border-bottom" role="tablist">
            <li class="nav-item">
                <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#doc-intro" role="tab">
                    <i class="ti ti-shield-lock me-2"></i> Keamanan & HMAC Signature
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#doc-absensi" role="tab">
                    <i class="ti ti-list-check me-2"></i> Get Data Absensi Biasa
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#doc-rekap" role="tab">
                    <i class="ti ti-report-analytics me-2"></i> Rekap Data Absensi
                </button>
            </li>
        </ul>

        <div class="tab-content pt-4 px-0 pb-0">
            <!-- TAB 1: INTRO & SIGNATURE -->
            <div class="tab-pane fade show active" id="doc-intro" role="tabpanel">
                <h6 class="fw-bold mb-3"><i class="ti ti-info-circle me-1"></i> Informasi Dasar</h6>
                <p>
                    Seluruh permintaan (request) ke API Eksternal Absensi wajib menggunakan protokol HTTP/HTTPS dan menyertakan header autentikasi keamanan ganda berbasis <b>API Key</b> dan <b>HMAC-SHA256 Signature</b>. Hal ini melindungi data dari pengaksesan tanpa izin serta mencegah serangan ulangan (<i>Replay Attack</i>).
                </p>

                <div class="alert alert-primary d-flex align-items-center" role="alert">
                    <i class="ti ti-link ti-sm me-2"></i>
                    <div><b>Base URL API:</b> <code class="bg-white px-2 py-1 rounded ms-1">{{ $baseUrl }}</code></div>
                </div>

                <h6 class="fw-bold mt-4 mb-3"><i class="ti ti-key me-1"></i> Header Wajib (Required Headers)</h6>
                <div class="table-responsive border rounded mb-4">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 20%;">Header Name</th>
                                <th style="width: 15%;">Tipe</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>X-Api-Key</code></td>
                                <td><span class="badge bg-label-danger">Required</span></td>
                                <td>API Key unik Anda yang didapatkan dari tabel di atas.</td>
                            </tr>
                            <tr>
                                <td><code>X-Timestamp</code></td>
                                <td><span class="badge bg-label-danger">Required</span></td>
                                <td>Unix timestamp (dalam hitungan detik) saat request dikirim. Maksimal toleransi selisih waktu adalah <b>5 menit (300 detik)</b> dari waktu server.</td>
                            </tr>
                            <tr>
                                <td><code>X-Signature</code></td>
                                <td><span class="badge bg-label-danger">Required</span></td>
                                <td>String hasil enkripsi HMAC SHA256 dalam format Hexadecimal menggunakan Secret Key Anda.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h6 class="fw-bold mt-4 mb-2"><i class="ti ti-lock me-1"></i> Rumus Perhitungan Signature (String-to-Sign)</h6>
                <p>Sistem mendukung 2 format rumus <code>String-to-Sign</code> yang dapat Anda pilih salah satunya:</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card bg-label-secondary border">
                            <div class="card-body p-3">
                                <span class="badge bg-primary mb-2">Opsi 1 (Rekomendasi Utama)</span>
                                <h6><code>HTTP_METHOD:PATH:TIMESTAMP</code></h6>
                                <p class="small mb-2">Menggabungkan metode HTTP (huruf besar), path endpoint relatif terhadap domain, dan timestamp.</p>
                                <div class="bg-dark text-light p-2 rounded small">
                                    <code>GET:api/client/v1/absensi/rekap:1784191830</code>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-label-secondary border">
                            <div class="card-body p-3">
                                <span class="badge bg-info mb-2">Opsi 2 (Alternative / Fallback)</span>
                                <h6><code>API_KEY:TIMESTAMP</code></h6>
                                <p class="small mb-2">Menggabungkan string API Key Anda dan timestamp. Sangat cocok jika proxy server Anda mengubah path URL.</p>
                                <div class="bg-dark text-light p-2 rounded small">
                                    <code>abcdef1234567890abcdef1234567890:1784191830</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning mt-4 mb-0" role="alert">
                    <div class="d-flex">
                        <i class="ti ti-bulb me-2 mt-1"></i>
                        <div>
                            <b>Tips Pengujian & Postman:</b><br>
                            Anda dapat menguji atau melihat simulasi pembuatan signature secara langsung tanpa otentikasi melalui URL Helper berikut:<br>
                            <code class="text-dark bg-warning px-2 py-1 rounded d-inline-block mt-1">{{ $helperUrl }}?api_key=KEY_ANDA&secret_key=SECRET_ANDA&method=GET&path=api/client/v1/absensi</code>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: GET DATA ABSENSI BIASA -->
            <div class="tab-pane fade" id="doc-absensi" role="tabpanel">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold mb-0"><i class="ti ti-list-check me-1"></i> Get Data Absensi Biasa (Daftar Baris/Record)</h6>
                    <span class="badge bg-success fs-6">GET {{ $absensiUrl }}</span>
                </div>
                <p>Endpoint ini digunakan untuk mengambil data riwayat absensi harian pengguna secara rinci dengan dukungan paginasi dan filter.</p>

                <h6 class="fw-bold mt-4 mb-2">Query Parameters (Optional)</h6>
                <div class="table-responsive border rounded mb-4">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 20%;">Parameter</th>
                                <th style="width: 15%;">Tipe</th>
                                <th style="width: 20%;">Contoh</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>mode</code></td>
                                <td>String</td>
                                <td><code>bulan_tahun</code></td>
                                <td>Pilih mode filter periode: <code>bulan_tahun</code> atau <code>rentang_tanggal</code>.</td>
                            </tr>
                            <tr>
                                <td><code>bulan</code></td>
                                <td>Integer (1-12)</td>
                                <td><code>7</code></td>
                                <td>Berlaku jika mode=bulan_tahun. Default: bulan berjalan saat ini.</td>
                            </tr>
                            <tr>
                                <td><code>tahun</code></td>
                                <td>Integer</td>
                                <td><code>2026</code></td>
                                <td>Berlaku jika mode=bulan_tahun. Default: tahun berjalan saat ini.</td>
                            </tr>
                            <tr>
                                <td><code>start_date</code></td>
                                <td>String (YYYY-MM-DD)</td>
                                <td><code>2026-07-01</code></td>
                                <td>Filter tanggal absen mulai dari tanggal spesifik (atau jika mode=rentang_tanggal).</td>
                            </tr>
                            <tr>
                                <td><code>end_date</code></td>
                                <td>String (YYYY-MM-DD)</td>
                                <td><code>2026-07-15</code></td>
                                <td>Filter tanggal absen sampai dengan tanggal spesifik (atau jika mode=rentang_tanggal).</td>
                            </tr>
                            <tr>
                                <td><code>tgl_absen</code></td>
                                <td>String (YYYY-MM-DD)</td>
                                <td><code>2026-07-15</code></td>
                                <td>Filter spesifik hanya pada 1 tanggal absen.</td>
                            </tr>
                            <tr>
                                <td><code>user_id</code></td>
                                <td>Integer</td>
                                <td><code>10</code></td>
                                <td>Filter absensi khusus untuk ID user tertentu.</td>
                            </tr>
                            <tr>
                                <td><code>kode_user</code> / <code>kode</code></td>
                                <td>String</td>
                                <td><code>KD-1001</code></td>
                                <td>Filter absensi khusus untuk Kode User tertentu.</td>
                            </tr>
                            <tr>
                                <td><code>kategori_id</code></td>
                                <td>Integer</td>
                                <td><code>1</code></td>
                                <td>Filter berdasarkan ID Kategori durasi (cth: Kategori A/B/C).</td>
                            </tr>
                            <tr>
                                <td><code>departemen_id</code></td>
                                <td>Integer</td>
                                <td><code>2</code></td>
                                <td>Filter absensi untuk seluruh user pada departemen tertentu.</td>
                            </tr>
                            <tr>
                                <td><code>search</code></td>
                                <td>String</td>
                                <td><code>Budi</code></td>
                                <td>Cari berdasarkan Nama User, Username, Kode User, atau Keterangan absen.</td>
                            </tr>
                            <tr>
                                <td><code>limit</code> & <code>page</code></td>
                                <td>Integer</td>
                                <td><code>15</code></td>
                                <td>Jumlah data per halaman (paginasi). Default: 15.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h6 class="fw-bold mt-4 mb-2">Contoh Respons JSON (Success 200 OK)</h6>
                <div class="one-dark-window">
                    <div class="one-dark-header">
                        <div class="mac-buttons">
                            <span class="mac-btn close-btn"></span>
                            <span class="mac-btn min-btn"></span>
                            <span class="mac-btn max-btn"></span>
                            <span class="file-title ms-2"><i class="ti ti-file-code me-1 text-warning"></i> response-absensi.json</span>
                        </div>
                        <button type="button" class="btn-copy-code" data-clipboard-text="{{ $jsonAbsensi }}"><i class="ti ti-copy me-1"></i> Salin JSON</button>
                    </div>
                    <pre><code class="language-json">{{ $jsonAbsensi }}</code></pre>
                </div>

                <!-- IMPLEMENTASI KODE ABSENSI -->
                <div class="border-top pt-4 mt-4">
                    <h6 class="fw-bold mb-3"><i class="ti ti-code me-1"></i> Contoh Implementasi Kode untuk Get Data Absensi Biasa</h6>
                    <ul class="nav nav-pills custom-doc-pills flex-wrap gap-2 mb-3" role="tablist">
                        <li class="nav-item">
                            <button type="button" class="nav-link active btn-sm py-1 px-3" data-bs-toggle="tab" data-bs-target="#absensi-code-curl" role="tab">
                                <i class="ti ti-terminal me-1"></i> cURL / Terminal
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link btn-sm py-1 px-3" data-bs-toggle="tab" data-bs-target="#absensi-code-php" role="tab">
                                <i class="ti ti-brand-php me-1"></i> PHP (Native / Laravel)
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link btn-sm py-1 px-3" data-bs-toggle="tab" data-bs-target="#absensi-code-js" role="tab">
                                <i class="ti ti-brand-javascript me-1"></i> Node.js (Axios)
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link btn-sm py-1 px-3" data-bs-toggle="tab" data-bs-target="#absensi-code-python" role="tab">
                                <i class="ti ti-brand-python me-1"></i> Python (Requests)
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content p-0 bg-transparent border-0">
                        <div class="tab-pane fade show active" id="absensi-code-curl" role="tabpanel">
                            <div class="one-dark-window mb-0">
                                <div class="one-dark-header">
                                    <div class="mac-buttons">
                                        <span class="mac-btn close-btn"></span>
                                        <span class="mac-btn min-btn"></span>
                                        <span class="mac-btn max-btn"></span>
                                        <span class="file-title ms-2"><i class="ti ti-terminal me-1 text-warning"></i> request-absensi.sh</span>
                                    </div>
                                    <button type="button" class="btn-copy-code" data-clipboard-text="{{ $curlAbsensi }}"><i class="ti ti-copy me-1"></i> Salin Kode</button>
                                </div>
                                <pre><code class="language-bash">{{ $curlAbsensi }}</code></pre>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="absensi-code-php" role="tabpanel">
                            <div class="one-dark-window mb-0">
                                <div class="one-dark-header">
                                    <div class="mac-buttons">
                                        <span class="mac-btn close-btn"></span>
                                        <span class="mac-btn min-btn"></span>
                                        <span class="mac-btn max-btn"></span>
                                        <span class="file-title ms-2"><i class="ti ti-brand-php me-1 text-primary"></i> request-absensi.php</span>
                                    </div>
                                    <button type="button" class="btn-copy-code" data-clipboard-text="{{ $phpAbsensi }}"><i class="ti ti-copy me-1"></i> Salin Kode</button>
                                </div>
                                <pre><code class="language-php">{{ $phpAbsensi }}</code></pre>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="absensi-code-js" role="tabpanel">
                            <div class="one-dark-window mb-0">
                                <div class="one-dark-header">
                                    <div class="mac-buttons">
                                        <span class="mac-btn close-btn"></span>
                                        <span class="mac-btn min-btn"></span>
                                        <span class="mac-btn max-btn"></span>
                                        <span class="file-title ms-2"><i class="ti ti-brand-javascript me-1 text-warning"></i> request-absensi.js</span>
                                    </div>
                                    <button type="button" class="btn-copy-code" data-clipboard-text="{{ $jsAbsensi }}"><i class="ti ti-copy me-1"></i> Salin Kode</button>
                                </div>
                                <pre><code class="language-javascript">{{ $jsAbsensi }}</code></pre>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="absensi-code-python" role="tabpanel">
                            <div class="one-dark-window mb-0">
                                <div class="one-dark-header">
                                    <div class="mac-buttons">
                                        <span class="mac-btn close-btn"></span>
                                        <span class="mac-btn min-btn"></span>
                                        <span class="mac-btn max-btn"></span>
                                        <span class="file-title ms-2"><i class="ti ti-brand-python me-1 text-info"></i> request-absensi.py</span>
                                    </div>
                                    <button type="button" class="btn-copy-code" data-clipboard-text="{{ $pythonAbsensi }}"><i class="ti ti-copy me-1"></i> Salin Kode</button>
                                </div>
                                <pre><code class="language-python">{{ $pythonAbsensi }}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: REKAP DATA ABSENSI -->
            <div class="tab-pane fade" id="doc-rekap" role="tabpanel">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold mb-0"><i class="ti ti-report-analytics me-1"></i> Rekap Data Absensi Pengguna</h6>
                    <span class="badge bg-success fs-6">GET {{ $rekapUrl }}</span>
                </div>
                <p>Endpoint ini merekap akumulasi absensi setiap pengguna dalam periode tertentu, merincikan jumlah perolehan per kategori (dari awal hingga akhir), total jam kerja, serta total perolehan dana tunai.</p>

                <h6 class="fw-bold mt-4 mb-2">Query Parameters (Optional)</h6>
                <div class="table-responsive border rounded mb-4">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 20%;">Parameter</th>
                                <th style="width: 15%;">Tipe</th>
                                <th style="width: 20%;">Contoh</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>mode</code></td>
                                <td>String</td>
                                <td><code>bulan_tahun</code></td>
                                <td>Pilih mode filter periode: <code>bulan_tahun</code> (default) atau <code>rentang_tanggal</code>.</td>
                            </tr>
                            <tr>
                                <td><code>bulan</code></td>
                                <td>Integer (1-12)</td>
                                <td><code>7</code></td>
                                <td>Berlaku jika mode=bulan_tahun. Default: bulan berjalan saat ini.</td>
                            </tr>
                            <tr>
                                <td><code>tahun</code></td>
                                <td>Integer</td>
                                <td><code>2026</code></td>
                                <td>Berlaku jika mode=bulan_tahun. Default: tahun berjalan saat ini.</td>
                            </tr>
                            <tr>
                                <td><code>start_date</code></td>
                                <td>String (YYYY-MM-DD)</td>
                                <td><code>2026-07-01</code></td>
                                <td>Berlaku jika mode=rentang_tanggal. Tanggal awal periode rekap.</td>
                            </tr>
                            <tr>
                                <td><code>end_date</code></td>
                                <td>String (YYYY-MM-DD)</td>
                                <td><code>2026-07-15</code></td>
                                <td>Berlaku jika mode=rentang_tanggal. Tanggal akhir periode rekap.</td>
                            </tr>
                            <tr>
                                <td><code>user_id</code></td>
                                <td>Integer</td>
                                <td><code>10</code></td>
                                <td>Jika diisi, hanya merespons rekap untuk 1 user tersebut. Jika tidak diisi, merespons rekap seluruh user.</td>
                            </tr>
                            <tr>
                                <td><code>kode_user</code> / <code>kode</code></td>
                                <td>String</td>
                                <td><code>KD-1001</code></td>
                                <td>Jika diisi, hanya merespons rekap untuk Kode User tersebut.</td>
                            </tr>
                            <tr>
                                <td><code>departemen_id</code></td>
                                <td>Integer</td>
                                <td><code>2</code></td>
                                <td>Filter rekap user hanya pada departemen spesifik.</td>
                            </tr>
                            <tr>
                                <td><code>search</code></td>
                                <td>String</td>
                                <td><code>Budi</code></td>
                                <td>Cari berdasarkan Nama User, Username, atau Kode User.</td>
                            </tr>
                            <tr>
                                <td><code>limit</code> & <code>page</code></td>
                                <td>Integer</td>
                                <td><code>15</code></td>
                                <td>Paginasi daftar pengguna yang direkap.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h6 class="fw-bold mt-4 mb-2">Contoh Respons JSON (Success 200 OK)</h6>
                <div class="one-dark-window">
                    <div class="one-dark-header">
                        <div class="mac-buttons">
                            <span class="mac-btn close-btn"></span>
                            <span class="mac-btn min-btn"></span>
                            <span class="mac-btn max-btn"></span>
                            <span class="file-title ms-2"><i class="ti ti-file-code me-1 text-warning"></i> response-rekap.json</span>
                        </div>
                        <button type="button" class="btn-copy-code" data-clipboard-text="{{ $jsonRekap }}"><i class="ti ti-copy me-1"></i> Salin JSON</button>
                    </div>
                    <pre><code class="language-json">{{ $jsonRekap }}</code></pre>
                </div>

                <!-- IMPLEMENTASI KODE REKAP -->
                <div class="border-top pt-4 mt-4">
                    <h6 class="fw-bold mb-3"><i class="ti ti-code me-1"></i> Contoh Implementasi Kode untuk Rekap Data Absensi</h6>
                    <ul class="nav nav-pills custom-doc-pills flex-wrap gap-2 mb-3" role="tablist">
                        <li class="nav-item">
                            <button type="button" class="nav-link active btn-sm py-1 px-3" data-bs-toggle="tab" data-bs-target="#rekap-code-curl" role="tab">
                                <i class="ti ti-terminal me-1"></i> cURL / Terminal
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link btn-sm py-1 px-3" data-bs-toggle="tab" data-bs-target="#rekap-code-php" role="tab">
                                <i class="ti ti-brand-php me-1"></i> PHP (Native / Laravel)
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link btn-sm py-1 px-3" data-bs-toggle="tab" data-bs-target="#rekap-code-js" role="tab">
                                <i class="ti ti-brand-javascript me-1"></i> Node.js (Axios)
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link btn-sm py-1 px-3" data-bs-toggle="tab" data-bs-target="#rekap-code-python" role="tab">
                                <i class="ti ti-brand-python me-1"></i> Python (Requests)
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content p-0 bg-transparent border-0">
                        <div class="tab-pane fade show active" id="rekap-code-curl" role="tabpanel">
                            <div class="one-dark-window mb-0">
                                <div class="one-dark-header">
                                    <div class="mac-buttons">
                                        <span class="mac-btn close-btn"></span>
                                        <span class="mac-btn min-btn"></span>
                                        <span class="mac-btn max-btn"></span>
                                        <span class="file-title ms-2"><i class="ti ti-terminal me-1 text-warning"></i> request-rekap.sh</span>
                                    </div>
                                    <button type="button" class="btn-copy-code" data-clipboard-text="{{ $curlRekap }}"><i class="ti ti-copy me-1"></i> Salin Kode</button>
                                </div>
                                <pre><code class="language-bash">{{ $curlRekap }}</code></pre>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="rekap-code-php" role="tabpanel">
                            <div class="one-dark-window mb-0">
                                <div class="one-dark-header">
                                    <div class="mac-buttons">
                                        <span class="mac-btn close-btn"></span>
                                        <span class="mac-btn min-btn"></span>
                                        <span class="mac-btn max-btn"></span>
                                        <span class="file-title ms-2"><i class="ti ti-brand-php me-1 text-primary"></i> request-rekap.php</span>
                                    </div>
                                    <button type="button" class="btn-copy-code" data-clipboard-text="{{ $phpRekap }}"><i class="ti ti-copy me-1"></i> Salin Kode</button>
                                </div>
                                <pre><code class="language-php">{{ $phpRekap }}</code></pre>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="rekap-code-js" role="tabpanel">
                            <div class="one-dark-window mb-0">
                                <div class="one-dark-header">
                                    <div class="mac-buttons">
                                        <span class="mac-btn close-btn"></span>
                                        <span class="mac-btn min-btn"></span>
                                        <span class="mac-btn max-btn"></span>
                                        <span class="file-title ms-2"><i class="ti ti-brand-javascript me-1 text-warning"></i> request-rekap.js</span>
                                    </div>
                                    <button type="button" class="btn-copy-code" data-clipboard-text="{{ $jsRekap }}"><i class="ti ti-copy me-1"></i> Salin Kode</button>
                                </div>
                                <pre><code class="language-javascript">{{ $jsRekap }}</code></pre>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="rekap-code-python" role="tabpanel">
                            <div class="one-dark-window mb-0">
                                <div class="one-dark-header">
                                    <div class="mac-buttons">
                                        <span class="mac-btn close-btn"></span>
                                        <span class="mac-btn min-btn"></span>
                                        <span class="mac-btn max-btn"></span>
                                        <span class="file-title ms-2"><i class="ti ti-brand-python me-1 text-info"></i> request-rekap.py</span>
                                    </div>
                                    <button type="button" class="btn-copy-code" data-clipboard-text="{{ $pythonRekap }}"><i class="ti ti-copy me-1"></i> Salin Kode</button>
                                </div>
                                <pre><code class="language-python">{{ $pythonRekap }}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Highlight.js Core & Highlighting Script -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>

<script>
    function applyOneDarkHighlighting() {
        if (typeof hljs !== 'undefined') {
            document.querySelectorAll('.one-dark-window pre code').forEach(function(block) {
                // Hapus penanda agar hljs merender ulang warna sintaks secara sempurna saat tab dibuka
                delete block.dataset.highlighted;
                hljs.highlightElement(block);
            });
        }
    }

    // Jalankan seketika
    applyOneDarkHighlighting();

    // Jalankan saat dokumen selesai dimuat
    document.addEventListener('DOMContentLoaded', function() {
        applyOneDarkHighlighting();

        // Jalankan setiap kali tab utama atau sub-tab bahasa diklik/ditampilkan
        const tabElms = document.querySelectorAll('button[data-bs-toggle="tab"]');
        tabElms.forEach(function(tab) {
            tab.addEventListener('shown.bs.tab', function() {
                applyOneDarkHighlighting();
            });
        });

        // Copy button action
        const copyBtns = document.querySelectorAll('.btn-copy-code');
        copyBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                const text = btn.getAttribute('data-clipboard-text');
                if (!text) return;
                
                navigator.clipboard.writeText(text).then(function() {
                    const originalHtml = btn.innerHTML;
                    btn.innerHTML = '<i class="ti ti-check me-1 text-success"></i> Tersalin!';
                    btn.style.borderColor = '#27c93f';
                    setTimeout(function() {
                        btn.innerHTML = originalHtml;
                        btn.style.borderColor = 'rgba(255, 255, 255, 0.15)';
                    }, 2000);
                }).catch(function(err) {
                    console.error('Gagal menyalin:', err);
                });
            });
        });
    });
</script>
