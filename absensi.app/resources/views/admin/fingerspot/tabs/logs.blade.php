<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="mb-1 text-primary fw-bold"><i class="ti ti-activity me-2"></i>Log Perintah & Callback Webhook</h5>
            <p class="text-muted mb-0 small">
                Audit trail lengkap seluruh aktivitas API, perintah remote mesin, serta callback webhook real-time yang diterima dari server Fingerspot.
            </p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-danger btn-sm waves-effect" id="btn-clear-command-logs">
                <i class="ti ti-trash me-1"></i> Bersihkan Log
            </button>
            <button type="button" class="btn btn-outline-primary btn-sm waves-effect" id="btn-refresh-command-logs">
                <i class="ti ti-refresh me-1"></i> Refresh Log
            </button>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-light py-3">
        <div class="row g-2">
            <div class="col-md-4 col-sm-6">
                <label class="form-label small mb-1 fw-semibold">Filter Mesin:</label>
                <select class="form-select form-select-sm" id="filter-log-cloud-id">
                    <option value="">-- Semua Mesin --</option>
                    @foreach ($devices as $dev)
                        <option value="{{ $dev->cloud_id }}">{{ $dev->name }} ({{ $dev->cloud_id }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 col-sm-6">
                <label class="form-label small mb-1 fw-semibold">Tipe Perintah / Event:</label>
                <select class="form-select form-select-sm" id="filter-log-type">
                    <option value="">-- Semua Tipe --</option>
                    <option value="attlog_realtime">Push Attlog Real-time</option>
                    <option value="get_attlog">get_attlog (Sinkron)</option>
                    <option value="get_device">get_device (Sinkron)</option>
                    <option value="get_userinfo">get_userinfo (Asinkron)</option>
                    <option value="get_all_pin">get_all_pin (Asinkron)</option>
                    <option value="set_userinfo">set_userinfo (Asinkron)</option>
                    <option value="delete_userinfo">delete_userinfo (Asinkron)</option>
                    <option value="set_time">set_time (Asinkron)</option>
                    <option value="reg_online">reg_online (Asinkron)</option>
                    <option value="restart_device">restart_device (Asinkron)</option>
                </select>
            </div>
            <div class="col-md-4 col-sm-12">
                <label class="form-label small mb-1 fw-semibold">Status Eksekusi:</label>
                <select class="form-select form-select-sm" id="filter-log-status">
                    <option value="">-- Semua Status --</option>
                    <option value="success">Sukses</option>
                    <option value="pending">Pending (Menunggu Webhook)</option>
                    <option value="failed">Gagal</option>
                </select>
            </div>
        </div>
    </div>
    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table table-hover" id="table-commands-log">
            <thead class="table-light">
                <tr>
                    <th style="width: 140px;">Waktu</th>
                    <th>Tipe Event</th>
                    <th>Mesin</th>
                    <th>Trans ID</th>
                    <th>Status</th>
                    <th>Pesan / Ringkasan</th>
                    <th class="text-center" style="width: 60px;">Detail</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
