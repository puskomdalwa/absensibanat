<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="mb-1 text-primary fw-bold"><i class="ti ti-users me-2"></i>Manajemen Pengguna di Mesin (Biometrik & Kredensial)</h5>
            <p class="text-muted mb-0 small">
                Sinkronisasi, pendaftaran biometrik online, dan transfer template antar mesin Fingerspot melalui perintah asinkron.
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-outline-primary btn-sm waves-effect" id="btn-fetch-all-pin">
                <i class="ti ti-download me-1"></i> Ambil Semua PIN dari Mesin
            </button>
            <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#modal-push-user">
                <i class="ti ti-user-plus me-1"></i> Daftarkan User ke Mesin
            </button>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-light py-3">
        <div class="row g-2 align-items-center">
            <div class="col-md-4 col-sm-6">
                <label class="form-label small mb-1 fw-semibold">Filter Berdasarkan Mesin:</label>
                <select class="form-select form-select-sm" id="filter-user-cloud-id">
                    <option value="">-- Semua Mesin --</option>
                    @foreach ($devices as $dev)
                        <option value="{{ $dev->cloud_id }}">{{ $dev->name }} ({{ $dev->cloud_id }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-8 col-sm-6 text-end">
                <button type="button" class="btn btn-sm btn-label-secondary waves-effect" id="btn-refresh-device-users">
                    <i class="ti ti-refresh me-1"></i> Refresh Tabel
                </button>
            </div>
        </div>
    </div>
    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table table-hover" id="table-device-users">
            <thead class="table-light">
                <tr>
                    <th style="width: 140px;">Cloud ID / Mesin</th>
                    <th>User & PIN</th>
                    <th>Kredensial / Biometrik</th>
                    <th>Hak Akses</th>
                    <th>Terakhir Sinkron</th>
                    <th class="text-center" style="width: 160px;">Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
