<div class="row mb-4">
    <div class="col-12">
        <h5 class="mb-1 text-primary fw-bold"><i class="ti ti-calendar-time me-2"></i>Tarik Log Absensi (Synchronous AttLog)</h5>
        <p class="text-muted mb-0 small">
            Mengambil log absensi tersimpan langsung dari server cloud Fingerspot (tersedia hingga 60 hari ke belakang, maksimal 2 hari per tarikan).
            Hasil tarikan dapat langsung dipratinjau dan disinkronkan ke tabel data absensi utama.
        </p>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form id="form-fetch-attlog">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold" for="attlog-cloud-id">Pilih Mesin Absensi <span class="text-danger">*</span></label>
                    <select class="form-select" id="attlog-cloud-id" name="cloud_id" required>
                        @foreach ($devices as $dev)
                            <option value="{{ $dev->cloud_id }}">{{ $dev->name }} ({{ $dev->cloud_id }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label fw-semibold" for="attlog-start-date">Tanggal Mulai <span class="text-danger">*</span></label>
                    <input type="text" class="form-control date-picker" id="attlog-start-date" name="start_date" value="{{ date('Y-m-d', strtotime('-1 day')) }}" required placeholder="YYYY-MM-DD">
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label fw-semibold" for="attlog-end-date">Tanggal Selesai <span class="text-danger">*</span></label>
                    <input type="text" class="form-control date-picker" id="attlog-end-date" name="end_date" value="{{ date('Y-m-d') }}" required placeholder="YYYY-MM-DD">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 waves-effect waves-light" id="btn-submit-attlog">
                        <i class="ti ti-download me-1"></i> Tarik Log
                    </button>
                </div>
            </div>
            <div class="form-text text-muted mt-2">
                <i class="ti ti-info-circle me-1 text-primary"></i>Ketentuan API: Jarak <code>start_date</code> ke <code>end_date</code> maksimal 2 hari.
            </div>
        </form>
    </div>
</div>

{{-- Results Area --}}
<div class="card shadow-sm border-0 d-none" id="card-attlog-results">
    <div class="card-header bg-light d-flex justify-content-between align-items-center py-3 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary fs-6 px-3 py-2" id="badge-total-attlog">0 Scan Ditemukan</span>
            <small class="text-muted" id="attlog-query-info"></small>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-success btn-sm waves-effect waves-light" id="btn-sync-all-attlog">
                <i class="ti ti-database-import me-1"></i> Sinkronkan ke Data Absensi Utama
            </button>
        </div>
    </div>
    <div class="card-body pt-3">
        <div class="table-responsive">
            <table class="table table-sm table-striped table-hover align-middle" id="table-attlog-results">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>PIN</th>
                        <th>Nama Pengguna</th>
                        <th>Waktu Scan</th>
                        <th>Metode Verifikasi</th>
                        <th>Status Scan</th>
                    </tr>
                </thead>
                <tbody id="attlog-results-tbody">
                    {{-- populated via js --}}
                </tbody>
            </table>
        </div>
    </div>
</div>
