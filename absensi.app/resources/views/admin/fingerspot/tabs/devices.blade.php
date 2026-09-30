<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="mb-1 text-primary fw-bold"><i class="ti ti-devices me-2"></i>Daftar Perangkat Mesin Absensi</h5>
            <p class="text-muted mb-0 small">Kelola mesin absensi biometrik Fingerspot yang terhubung ke cloud platform (REVO, VIDA, VEGA, VIVO, DS/DT Series).</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary btn-sm waves-effect" id="btn-refresh-devices">
                <i class="ti ti-refresh me-1"></i> Refresh Tabel
            </button>
            <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#modal-add-device">
                <i class="ti ti-plus me-1"></i> Tambah Perangkat
            </button>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table table-hover" id="table-devices">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th>Nama Perangkat</th>
                    <th>Cloud ID (Serial Number)</th>
                    <th>User Terdaftar</th>
                    <th class="text-center" style="width: 180px;">Aksi & Kontrol</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

{{-- Live Device Info Card --}}
<div class="card border border-primary border-opacity-25 shadow-sm d-none" id="card-live-device-info">
    <div class="card-header bg-label-primary d-flex justify-content-between align-items-center py-2">
        <h6 class="mb-0 fw-bold text-primary"><i class="ti ti-info-circle me-1"></i> Status Online Perangkat (<span id="live-info-device-name">-</span>)</h6>
        <button type="button" class="btn-close" aria-label="Close" onclick="$('#card-live-device-info').addClass('d-none');"></button>
    </div>
    <div class="card-body pt-3">
        <div class="row g-3">
            <div class="col-md-3 col-sm-6">
                <div class="p-3 bg-light rounded border">
                    <small class="text-muted d-block mb-1">Nama di Cloud</small>
                    <span class="fw-bold fs-6" id="live-info-cloud-name">-</span>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="p-3 bg-light rounded border">
                    <small class="text-muted d-block mb-1">Cloud ID</small>
                    <code class="fw-bold fs-6 text-primary" id="live-info-cloud-id">-</code>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="p-3 bg-light rounded border">
                    <small class="text-muted d-block mb-1">Aktivitas Terakhir</small>
                    <span class="badge bg-label-info" id="live-info-last-act">-</span>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="p-3 bg-light rounded border">
                    <small class="text-muted d-block mb-1">Tanggal Didaftarkan</small>
                    <span class="text-secondary small" id="live-info-created-at">-</span>
                </div>
            </div>
            <div class="col-12">
                <div class="p-3 bg-light rounded border">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted">Target Webhook URL Terdaftar di Mesin:</small>
                        <button type="button" class="btn btn-xs btn-label-secondary btn-copy" id="btn-copy-live-webhook" data-clipboard-text="">
                            <i class="ti ti-copy ti-xs me-1"></i> Salin URL
                        </button>
                    </div>
                    <code class="text-break" id="live-info-webhook-url">-</code>
                </div>
            </div>
        </div>
    </div>
</div>
