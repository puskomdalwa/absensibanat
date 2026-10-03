@extends('layouts.admin.template')
@section('title', 'Fingerspot Cloud API Hub')

@push('css')
    <style>
        .nav-tabs .nav-link {
            font-weight: 600;
            padding: 0.75rem 1.25rem;
        }
        .nav-tabs .nav-link i {
            margin-right: 0.5rem;
        }
        .stat-card-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
        }
        .cursor-pointer {
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
    {{-- Header Banner & Stats --}}
    <div class="card mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, rgba(225, 29, 72, 0.05) 0%, rgba(251, 113, 133, 0.12) 100%); border-radius: 20px; border: 1px solid rgba(251, 113, 133, 0.25) !important;">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-lg-8 col-md-7 mb-3 mb-md-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center" style="width: 58px; height: 58px; background: linear-gradient(135deg, #fb7185 0%, #e11d48 100%); border-radius: 16px; box-shadow: 0 4px 14px rgba(225, 29, 72, 0.35);">
                            <i class="ti ti-fingerprint ti-xl text-white" style="font-size: 2rem;"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 fw-bold text-heading">Fingerspot Developer API Hub</h4>
                            <p class="mb-0 text-muted small">
                                Gateway kontrol REST API & Webhook dua arah untuk integrasi mesin biometrik Banat UII Dalwa (Online SDK)
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-5 text-md-end">
                    <div class="d-inline-flex flex-column align-items-md-end gap-2">
                        <button type="button" class="btn btn-primary btn-sm fw-bold shadow-sm d-inline-flex align-items-center gap-1 px-3 py-2" id="btn-test-connection">
                            <i class="ti ti-activity me-1"></i> Cek Koneksi Cloud API
                        </button>
                        <span class="badge" style="background: rgba(225, 29, 72, 0.1); color: #e11d48; border: 1px solid rgba(225, 29, 72, 0.25); border-radius: 50px; font-weight: 500;" id="connection-status-text">Base URL: {{ $apiUrl }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Overview Stats Row --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 18px; border: 1px solid rgba(251, 113, 133, 0.18) !important;">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <span class="text-muted small d-block">Mesin Terdaftar</span>
                        <h4 class="mb-0 fw-bold mt-1" style="color: #e11d48;">{{ $totalDevices }}</h4>
                        <small class="text-primary"><i class="ti ti-cpu me-1"></i>Active Hardware</small>
                    </div>
                    <div class="stat-card-icon bg-label-primary" style="width: 48px; height: 48px; border-radius: 12px;">
                        <i class="ti ti-devices ti-md"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 18px; border: 1px solid rgba(251, 113, 133, 0.18) !important;">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <span class="text-muted small d-block">Scan Hari Ini (Live)</span>
                        <h4 class="mb-0 fw-bold text-success mt-1">{{ $todayScans }}</h4>
                        <small class="text-success"><i class="ti ti-check me-1"></i>Absensi Tercatat</small>
                    </div>
                    <div class="stat-card-icon bg-label-success" style="width: 48px; height: 48px; border-radius: 12px;">
                        <i class="ti ti-calendar-event ti-md"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 18px; border: 1px solid rgba(251, 113, 133, 0.18) !important;">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <span class="text-muted small d-block">User di Mesin (Cache)</span>
                        <h4 class="mb-0 fw-bold text-info mt-1">{{ $totalDeviceUsers }}</h4>
                        <small class="text-info"><i class="ti ti-users me-1"></i>Terdata di Mesin</small>
                    </div>
                    <div class="stat-card-icon bg-label-info" style="width: 48px; height: 48px; border-radius: 12px;">
                        <i class="ti ti-user-check ti-md"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 18px; border: 1px solid rgba(251, 113, 133, 0.18) !important;">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <span class="text-muted small d-block">Perintah & Webhook Hari Ini</span>
                        <h4 class="mb-0 fw-bold text-warning mt-1">{{ $todayCommands }}</h4>
                        <small class="text-muted"><i class="ti ti-history me-1"></i>Audit Trail</small>
                    </div>
                    <div class="stat-card-icon bg-label-warning" style="width: 48px; height: 48px; border-radius: 12px;">
                        <i class="ti ti-broadcast ti-md"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Tabbed Container --}}
    <div class="nav-align-top mb-4">
        <ul class="nav nav-tabs nav-fill shadow-sm rounded-top" role="tablist">
            <li class="nav-item">
                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#tab-devices" aria-controls="tab-devices" aria-selected="true">
                    <i class="ti ti-devices"></i> Perangkat Mesin
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-attlog" aria-controls="tab-attlog" aria-selected="false">
                    <i class="ti ti-calendar-time"></i> Tarik & Sinkron Absensi
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-users" aria-controls="tab-users" aria-selected="false">
                    <i class="ti ti-users"></i> Pengguna Mesin
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-logs" aria-controls="tab-logs" aria-selected="false">
                    <i class="ti ti-activity"></i> Log & Webhook
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-playground" aria-controls="tab-playground" aria-selected="false">
                    <i class="ti ti-terminal-2"></i> API Playground
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-docs" aria-controls="tab-docs" aria-selected="false">
                    <i class="ti ti-book"></i> Dokumentasi
                </button>
            </li>
        </ul>

        <div class="tab-content bg-white p-4 shadow-sm rounded-bottom border-top-0">
            {{-- Tab 1: Perangkat --}}
            <div class="tab-pane fade show active" id="tab-devices" role="tabpanel">
                @include('admin.fingerspot.tabs.devices')
            </div>

            {{-- Tab 2: Tarik Absensi --}}
            <div class="tab-pane fade" id="tab-attlog" role="tabpanel">
                @include('admin.fingerspot.tabs.attlog')
            </div>

            {{-- Tab 3: Pengguna Mesin --}}
            <div class="tab-pane fade" id="tab-users" role="tabpanel">
                @include('admin.fingerspot.tabs.users')
            </div>

            {{-- Tab 4: Log & Webhook --}}
            <div class="tab-pane fade" id="tab-logs" role="tabpanel">
                @include('admin.fingerspot.tabs.logs')
            </div>

            {{-- Tab 5: API Playground --}}
            <div class="tab-pane fade" id="tab-playground" role="tabpanel">
                @include('admin.fingerspot.tabs.playground')
            </div>

            {{-- Tab 6: Dokumentasi --}}
            <div class="tab-pane fade" id="tab-docs" role="tabpanel">
                @include('admin.fingerspot.tabs.docs')
            </div>
        </div>
    </div>

    {{-- Include All Modals --}}
    @include('admin.fingerspot.modals.modals')
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // ----------------------------------------------------
    // SETUP CSRF TOKEN FOR ALL AJAX
    // ----------------------------------------------------
    var csrfToken = $('meta[name="csrf-token"]').attr('content') || "{{ csrf_token() }}";
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    });

    // ----------------------------------------------------
    // INITIALIZE FLATPICKR DATEPICKERS
    // ----------------------------------------------------
    if (typeof flatpickr !== 'undefined') {
        $('.date-picker').flatpickr({
            dateFormat: 'Y-m-d',
            allowInput: true
        });
    }

    // ----------------------------------------------------
    // COPY TO CLIPBOARD HELPER
    // ----------------------------------------------------
    $(document).on('click', '.btn-copy', function() {
        var text = $(this).attr('data-clipboard-text') || $(this).data('clipboard-text');
        if (!text) return;

        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(function() {
                showToastr('success', 'Berhasil', 'Teks telah disalin ke clipboard.');
            }).catch(function() {
                fallbackCopy(text);
            });
        } else {
            fallbackCopy(text);
        }
    });

    function fallbackCopy(text) {
        var temp = $("<textarea>");
        $("body").append(temp);
        temp.val(text).select();
        document.execCommand("copy");
        temp.remove();
        showToastr('success', 'Berhasil', 'Teks telah disalin ke clipboard.');
    }

    // ----------------------------------------------------
    // TEST CONNECTION BUTTON
    // ----------------------------------------------------
    $('#btn-test-connection').on('click', function() {
        var btn = $(this);
        var origHtml = btn.html();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> Menguji...');

        $.ajax({
            url: "{{ route('admin.fingerspot.test_connection') }}",
            type: "GET",
            success: function(res) {
                btn.prop('disabled', false).html(origHtml);
                if (res.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Koneksi Cloud API Terhubung!',
                        html: `<p class="mb-1">${res.message}</p>
                               <div class="text-start bg-light p-2 rounded small font-monospace">
                                   <strong>Mesin Pengujian:</strong> ${res.device} (${res.cloud_id})<br>
                                   <strong>Latensi Jaringan:</strong> ${res.latency_ms} ms<br>
                                   <strong>Status:</strong> Terhubung ke Cloud Fingerspot
                               </div>`,
                        customClass: { confirmButton: 'btn btn-primary' },
                        buttonsStyling: false
                    });
                    $('#connection-status-text').html(`<span class="text-white"><i class="ti ti-circle-check text-success me-1"></i>Terhubung (${res.latency_ms} ms)</span>`);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Koneksi Gagal',
                        text: res.message,
                        customClass: { confirmButton: 'btn btn-primary' },
                        buttonsStyling: false
                    });
                }
            },
            error: function(err) {
                btn.prop('disabled', false).html(origHtml);
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Jaringan / Server',
                    text: err.responseJSON ? err.responseJSON.message : 'Tidak dapat menghubungi server Fingerspot.',
                    customClass: { confirmButton: 'btn btn-primary' },
                    buttonsStyling: false
                });
            }
        });
    });

    // ----------------------------------------------------
    // TAB 1: DATATABLES PERANGKAT
    // ----------------------------------------------------
    var tableDevices = $('#table-devices').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.fingerspot.devices.data') }}",
        columns: [
            { data: 'id', name: 'id' },
            { 
                data: 'name', 
                name: 'name',
                render: function(data, type, row) {
                    return `<div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2 bg-label-primary rounded p-1 d-flex align-items-center justify-content-center">
                                    <i class="ti ti-device-laptop ti-xs"></i>
                                </div>
                                <span class="fw-semibold">${data}</span>
                            </div>`;
                }
            },
            { 
                data: 'cloud_id', 
                name: 'cloud_id',
                render: function(data) {
                    return `<div class="d-flex align-items-center">
                                <code class="me-2 text-primary fw-bold">${data}</code>
                                <button type="button" class="btn btn-xs btn-icon btn-text-secondary rounded-pill btn-copy" data-clipboard-text="${data}" title="Salin Cloud ID">
                                    <i class="ti ti-copy ti-xs"></i>
                                </button>
                            </div>`;
                }
            },
            { data: 'user_count', name: 'user_count', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[0, 'asc']]
    });

    $('#btn-refresh-devices').on('click', function() {
        tableDevices.ajax.reload(null, false);
    });

    // Tambah Perangkat
    $('#form-add-device').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

        $.ajax({
            url: "{{ route('admin.fingerspot.devices.store') }}",
            type: "POST",
            data: form.serialize(),
            success: function(res) {
                submitBtn.prop('disabled', false).html('<i class="ti ti-check me-1"></i> Simpan Mesin');
                $('#modal-add-device').modal('hide');
                form[0].reset();
                showToastr('success', 'Sukses', res.message);
                tableDevices.ajax.reload(null, false);
            },
            error: function(err) {
                submitBtn.prop('disabled', false).html('<i class="ti ti-check me-1"></i> Simpan Mesin');
                var msg = err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan sistem.';
                Swal.fire({ icon: 'error', title: 'Gagal Menambah Perangkat', text: msg, customClass: { confirmButton: 'btn btn-primary' }, buttonsStyling: false });
            }
        });
    });

    // Edit Perangkat Modal Open
    $(document).on('click', '.edit-device-btn', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var cloudId = $(this).data('cloud-id');

        $('#edit-device-id').val(id);
        $('#edit-device-name').val(name);
        $('#edit-device-cloud-id').val(cloudId);
        $('#modal-edit-device').modal('show');
    });

    // Edit Perangkat Submit
    $('#form-edit-device').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Memperbarui...');

        $.ajax({
            url: "{{ route('admin.fingerspot.devices.update') }}",
            type: "PUT",
            data: form.serialize(),
            success: function(res) {
                submitBtn.prop('disabled', false).html('<i class="ti ti-check me-1"></i> Perbarui');
                $('#modal-edit-device').modal('hide');
                showToastr('success', 'Sukses', res.message);
                tableDevices.ajax.reload(null, false);
            },
            error: function(err) {
                submitBtn.prop('disabled', false).html('<i class="ti ti-check me-1"></i> Perbarui');
                var msg = err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan sistem.';
                Swal.fire({ icon: 'error', title: 'Gagal Memperbarui Perangkat', text: msg, customClass: { confirmButton: 'btn btn-primary' }, buttonsStyling: false });
            }
        });
    });

    // Hapus Perangkat
    $(document).on('click', '.delete-device-btn', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');

        Swal.fire({
            title: `Hapus perangkat "${name}"?`,
            text: "Data perangkat akan dihapus dari sistem lokal!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger me-3',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
        }).then(function(result) {
            if (result.value) {
                $.ajax({
                    url: "{{ route('admin.fingerspot.devices.delete') }}",
                    type: "DELETE",
                    data: { id: id, _token: csrfToken },
                    success: function(res) {
                        showToastr('success', 'Sukses', res.message);
                        tableDevices.ajax.reload(null, false);
                    },
                    error: function(err) {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: err.responseJSON ? err.responseJSON.message : 'Gagal menghapus perangkat.' });
                    }
                });
            }
        });
    });

    // Info Online Mesin (get_device)
    $(document).on('click', '.btn-device-info', function() {
        var cloudId = $(this).data('cloud-id');
        var name = $(this).data('name');
        var btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: "{{ route('admin.fingerspot.devices.info') }}",
            type: "POST",
            data: { cloud_id: cloudId, _token: csrfToken },
            success: function(res) {
                btn.prop('disabled', false);
                if (res.success && res.data) {
                    var d = res.data;
                    $('#live-info-device-name').text(name);
                    $('#live-info-cloud-name').text(d.device_name || name);
                    $('#live-info-cloud-id').text(d.cloud_id);
                    $('#live-info-last-act').text(d.last_activity || 'N/A');
                    $('#live-info-created-at').text(d.created_at || '-');
                    $('#live-info-webhook-url').text(d.webhook_url || '-');
                    $('#btn-copy-live-webhook').attr('data-clipboard-text', d.webhook_url || '');

                    $('#card-live-device-info').removeClass('d-none');
                    $('html, body').animate({ scrollTop: $('#card-live-device-info').offset().top - 100 }, 400);
                    showToastr('success', 'Info Diterima', 'Status online perangkat berhasil dimuat.');
                } else {
                    Swal.fire({ icon: 'warning', title: 'Respon Perangkat', text: res.message || 'Perangkat tidak merespons.' });
                }
            },
            error: function(err) {
                btn.prop('disabled', false);
                Swal.fire({ icon: 'error', title: 'Gagal Menghubungi Perangkat', text: err.responseJSON ? err.responseJSON.message : 'Koneksi gagal.' });
            }
        });
    });

    // Atur Waktu Perangkat Modal
    $(document).on('click', '.btn-device-time', function() {
        var cloudId = $(this).data('cloud-id');
        var name = $(this).data('name');

        $('#set-time-cloud-id').val(cloudId);
        $('#set-time-device-name').text(`${name} (${cloudId})`);
        $('#modal-set-time').modal('show');
    });

    $('#form-set-time').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Mengirim...');

        $.ajax({
            url: "{{ route('admin.fingerspot.devices.set_time') }}",
            type: "POST",
            data: form.serialize(),
            success: function(res) {
                submitBtn.prop('disabled', false).html('<i class="ti ti-send me-1"></i> Kirim Perintah');
                $('#modal-set-time').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Perintah Dikirim!',
                    html: `<p>Perintah sinkronisasi zona waktu berhasil dikirim ke server Fingerspot.</p>
                           <p class="small text-muted mb-0">Trans ID: <code>${res.trans_id}</code>. Mesin akan menerapkan waktu dan mengirimkan callback konfirmasi ke webhook.</p>`,
                    customClass: { confirmButton: 'btn btn-primary' },
                    buttonsStyling: false
                });
                tableCommands.ajax.reload(null, false);
            },
            error: function(err) {
                submitBtn.prop('disabled', false).html('<i class="ti ti-send me-1"></i> Kirim Perintah');
                Swal.fire({ icon: 'error', title: 'Gagal Mengirim Perintah', text: err.responseJSON ? err.responseJSON.message : 'Kesalahan jaringan.' });
            }
        });
    });

    // Restart Mesin
    $(document).on('click', '.btn-device-restart', function() {
        var cloudId = $(this).data('cloud-id');
        var name = $(this).data('name');

        Swal.fire({
            title: `Restart mesin "${name}"?`,
            text: "Perintah restart akan dikirimkan ke perangkat melalui cloud!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Restart Sekarang!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger me-3',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
        }).then(function(result) {
            if (result.value) {
                $.ajax({
                    url: "{{ route('admin.fingerspot.devices.restart') }}",
                    type: "POST",
                    data: { cloud_id: cloudId, _token: csrfToken },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Perintah Restart Terkirim!',
                            html: `<p>Perintah restart perangkat berhasil dikirim ke cloud.</p>
                                   <p class="small text-muted mb-0">Trans ID: <code>${res.trans_id}</code></p>`,
                            customClass: { confirmButton: 'btn btn-primary' },
                            buttonsStyling: false
                        });
                        tableCommands.ajax.reload(null, false);
                    },
                    error: function(err) {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: err.responseJSON ? err.responseJSON.message : 'Gagal mengirim perintah restart.' });
                    }
                });
            }
        });
    });

    // ----------------------------------------------------
    // TAB 2: TARIK & SINKRON LOG ABSENSI (ATTLOG)
    // ----------------------------------------------------
    var currentFetchedLogs = [];
    var currentFetchedCloudId = '';

    $('#form-fetch-attlog').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = $('#btn-submit-attlog');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menarik Data...');

        currentFetchedCloudId = $('#attlog-cloud-id').val();

        $.ajax({
            url: "{{ route('admin.fingerspot.attlog.fetch') }}",
            type: "POST",
            data: form.serialize(),
            success: function(res) {
                submitBtn.prop('disabled', false).html('<i class="ti ti-download me-1"></i> Tarik Log');

                if (res.success && res.data) {
                    currentFetchedLogs = res.data;
                    var total = res.total || currentFetchedLogs.length;

                    $('#badge-total-attlog').text(`${total} Scan Ditemukan`);
                    $('#attlog-query-info').text(`Cloud ID: ${currentFetchedCloudId} | Trans ID: ${res.trans_id || '-'}`);

                    var tbody = $('#attlog-results-tbody');
                    tbody.empty();

                    if (currentFetchedLogs.length === 0) {
                        tbody.append(`<tr><td colspan="6" class="text-center py-4 text-muted">Tidak ada riwayat scan pada rentang tanggal tersebut.</td></tr>`);
                    } else {
                        $.each(currentFetchedLogs, function(idx, item) {
                            var verifyBadge = '';
                            if (item.verify == 1) verifyBadge = '<span class="badge bg-label-primary"><i class="ti ti-fingerprint me-1"></i>Fingerprint</span>';
                            else if (item.verify == 4) verifyBadge = '<span class="badge bg-label-info"><i class="ti ti-scan me-1"></i>Face</span>';
                            else if (item.verify == 3) verifyBadge = '<span class="badge bg-label-secondary"><i class="ti ti-id me-1"></i>RFID Card</span>';
                            else verifyBadge = `<span class="badge bg-label-dark">${item.verify_label}</span>`;

                            var statusBadge = '';
                            if (item.status_scan == 0) statusBadge = '<span class="badge bg-label-success">Scan In (Masuk)</span>';
                            else if (item.status_scan == 1) statusBadge = '<span class="badge bg-label-danger">Scan Out (Pulang)</span>';
                            else statusBadge = `<span class="badge bg-label-warning">${item.status_scan_label}</span>`;

                            var scanTime = item.scan_date || item.scan || '-';

                            tbody.append(`
                                <tr>
                                    <td>${idx + 1}</td>
                                    <td><code class="fw-bold">${item.pin}</code></td>
                                    <td><strong>${item.user_name}</strong></td>
                                    <td>${scanTime}</td>
                                    <td>${verifyBadge}</td>
                                    <td>${statusBadge}</td>
                                </tr>
                            `);
                        });
                    }

                    $('#card-attlog-results').removeClass('d-none');
                    $('html, body').animate({ scrollTop: $('#card-attlog-results').offset().top - 100 }, 400);
                    showToastr('success', 'Berhasil', `${total} data log absensi berhasil diambil.`);
                } else {
                    Swal.fire({ icon: 'warning', title: 'Hasil Tarik Log', text: res.message || 'Tidak ada data absensi.' });
                }
            },
            error: function(err) {
                submitBtn.prop('disabled', false).html('<i class="ti ti-download me-1"></i> Tarik Log');
                var msg = err.responseJSON ? err.responseJSON.message : 'Gagal mengambil data dari cloud.';
                Swal.fire({ icon: 'error', title: 'Gagal Menarik Data', text: msg });
            }
        });
    });

    // Sinkronkan ke Database Absensi Utama
    $('#btn-sync-all-attlog').on('click', function() {
        if (!currentFetchedLogs || currentFetchedLogs.length === 0) {
            Swal.fire({ icon: 'info', title: 'Tidak Ada Data', text: 'Silakan tarik log absensi terlebih dahulu.' });
            return;
        }

        var btn = $(this);
        Swal.fire({
            title: `Sinkronkan ${currentFetchedLogs.length} data scan?`,
            text: "Sistem akan mencatat scan datang/pulang dan menghitung durasi kategori otomatis di database lokal!",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Sinkronkan Sekarang!',
            cancelButtonText: 'Batal',
            customClass: { confirmButton: 'btn btn-success me-3', cancelButton: 'btn btn-label-secondary' },
            buttonsStyling: false
        }).then(function(result) {
            if (result.value) {
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyinkronkan...');

                $.ajax({
                    url: "{{ route('admin.fingerspot.attlog.sync') }}",
                    type: "POST",
                    data: {
                        cloud_id: currentFetchedCloudId,
                        logs: currentFetchedLogs,
                        _token: csrfToken
                    },
                    success: function(res) {
                        btn.prop('disabled', false).html('<i class="ti ti-database-import me-1"></i> Sinkronkan ke Data Absensi Utama');
                        Swal.fire({
                            icon: 'success',
                            title: 'Sinkronisasi Berhasil!',
                            html: `<p class="mb-2">${res.message}</p>
                                   <div class="text-start bg-light p-2 rounded small">
                                       <strong>Baru Dicatat:</strong> ${res.data.created}<br>
                                       <strong>Diperbarui (Scan Pulang):</strong> ${res.data.updated}<br>
                                       <strong>Dilewati (Belum 2 Jam):</strong> ${res.data.skipped}
                                   </div>`,
                            customClass: { confirmButton: 'btn btn-primary' },
                            buttonsStyling: false
                        });
                    },
                    error: function(err) {
                        btn.prop('disabled', false).html('<i class="ti ti-database-import me-1"></i> Sinkronkan ke Data Absensi Utama');
                        Swal.fire({ icon: 'error', title: 'Gagal Sinkronisasi', text: err.responseJSON ? err.responseJSON.message : 'Kesalahan server.' });
                    }
                });
            }
        });
    });

    // ----------------------------------------------------
    // TAB 3: PENGGUNA MESIN (DEVICE USERS)
    // ----------------------------------------------------
    var tableDeviceUsers = $('#table-device-users').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.fingerspot.users.data') }}",
            data: function(d) {
                d.cloud_id = $('#filter-user-cloud-id').val();
            }
        },
        columns: [
            {
                data: 'checkbox',
                name: 'checkbox',
                orderable: false,
                searchable: false,
                className: 'text-center align-middle',
                width: '40px'
            },
            { 
                data: 'cloud_id', 
                name: 'cloud_id',
                render: function(data, type, row) {
                    var devName = row.device ? row.device.name : 'Unknown';
                    return `<div><strong>${devName}</strong><br><small class="text-muted font-monospace">${data}</small></div>`;
                }
            },
            { data: 'user_display', name: 'user_display' },
            { data: 'credentials', name: 'credentials', orderable: false, searchable: false },
            { data: 'privilege', name: 'privilege', className: 'text-center' },
            { data: 'last_sync_at', name: 'last_sync_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[1, 'asc']]
    });

    function updateBulkDeviceUserBtn() {
        var count = $('.device-user-row-checkbox:checked').length;
        $('#bulk-device-user-count').text(count);
        if (count > 0) {
            $('#btn-bulk-delete-device-users').removeClass('d-none');
        } else {
            $('#btn-bulk-delete-device-users').addClass('d-none');
        }
    }

    $(document).on('change', '#check-all-device-users', function() {
        var isChecked = $(this).is(':checked');
        $('.device-user-row-checkbox').prop('checked', isChecked);
        updateBulkDeviceUserBtn();
    });

    $(document).on('change', '.device-user-row-checkbox', function() {
        var total = $('.device-user-row-checkbox').length;
        var checked = $('.device-user-row-checkbox:checked').length;
        $('#check-all-device-users').prop('checked', total > 0 && total === checked);
        updateBulkDeviceUserBtn();
    });

    tableDeviceUsers.on('draw', function() {
        $('#check-all-device-users').prop('checked', false);
        updateBulkDeviceUserBtn();
    });

    $('#filter-user-cloud-id').on('change', function() {
        tableDeviceUsers.ajax.reload();
    });

    $('#btn-refresh-device-users').on('click', function() {
        tableDeviceUsers.ajax.reload(null, false);
    });

    // Ambil Semua PIN dari Mesin (get_all_pin)
    $('#btn-fetch-all-pin').on('click', function() {
        var cloudId = $('#filter-user-cloud-id').val();
        if (!cloudId) {
            cloudId = "{{ $devices->first() ? $devices->first()->cloud_id : '' }}";
        }

        if (!cloudId) {
            Swal.fire({ icon: 'warning', title: 'Belum Ada Mesin', text: 'Daftarkan mesin absensi terlebih dahulu.' });
            return;
        }

        Swal.fire({
            title: 'Ambil Semua PIN Pengguna?',
            text: `Perintah akan dikirim ke mesin (${cloudId}). Mesin akan merespons melalui webhook dengan daftar seluruh PIN terdaftar.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Kirim Perintah!',
            cancelButtonText: 'Batal',
            customClass: { confirmButton: 'btn btn-primary me-3', cancelButton: 'btn btn-label-secondary' },
            buttonsStyling: false
        }).then(function(result) {
            if (result.value) {
                $.ajax({
                    url: "{{ route('admin.fingerspot.users.get_all_pin') }}",
                    type: "POST",
                    data: { cloud_id: cloudId, _token: csrfToken },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Perintah get_all_pin Dikirim!',
                            html: `<p>Perintah telah diterima oleh server cloud.</p>
                                   <p class="small text-muted mb-0">Trans ID: <code>${res.trans_id}</code>. Hasilnya akan tiba via callback webhook.</p>`,
                            customClass: { confirmButton: 'btn btn-primary' },
                            buttonsStyling: false
                        });
                        tableCommands.ajax.reload(null, false);
                    },
                    error: function(err) {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: err.responseJSON ? err.responseJSON.message : 'Gagal mengirim perintah.' });
                    }
                });
            }
        });
    });

    // Refresh User Info dari Mesin (get_userinfo)
    $(document).on('click', '.btn-get-userinfo', function() {
        var cloudId = $(this).data('cloud-id');
        var pin = $(this).data('pin');

        $.ajax({
            url: "{{ route('admin.fingerspot.users.get_info') }}",
            type: "POST",
            data: { cloud_id: cloudId, pin: pin, _token: csrfToken },
            success: function(res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Perintah get_userinfo Dikirim!',
                    html: `<p>Perintah pengambilan detail user PIN <strong>${pin}</strong> berhasil dikirim.</p>
                           <p class="small text-muted mb-0">Trans ID: <code>${res.trans_id}</code>. Data profil dan template biometrik akan diperbarui via callback webhook.</p>`,
                    customClass: { confirmButton: 'btn btn-primary' },
                    buttonsStyling: false
                });
                tableCommands.ajax.reload(null, false);
            },
            error: function(err) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: err.responseJSON ? err.responseJSON.message : 'Gagal mengirim perintah.' });
            }
        });
    });

    // Pilih User Lokal auto-fill di modal push user
    $('#select-local-user').on('change', function() {
        var val = $(this).val();
        if (val) {
            var name = $(this).find(':selected').data('name');
            $('#push-user-pin').val(val);
            $('#push-user-name').val(name);
        }
    });

    // Submit Push User (set_userinfo)
    $('#form-push-user').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Mengirim...');

        $.ajax({
            url: "{{ route('admin.fingerspot.users.set_info') }}",
            type: "POST",
            data: form.serialize(),
            success: function(res) {
                submitBtn.prop('disabled', false).html('<i class="ti ti-send me-1"></i> Kirim ke Mesin');
                $('#modal-push-user').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Perintah set_userinfo Dikirim!',
                    html: `<p>Data pengguna telah dikirimkan ke mesin absensi.</p>
                           <p class="small text-muted mb-0">Trans ID: <code>${res.trans_id}</code></p>`,
                    customClass: { confirmButton: 'btn btn-primary' },
                    buttonsStyling: false
                });
                tableDeviceUsers.ajax.reload(null, false);
                tableCommands.ajax.reload(null, false);
            },
            error: function(err) {
                submitBtn.prop('disabled', false).html('<i class="ti ti-send me-1"></i> Kirim ke Mesin');
                Swal.fire({ icon: 'error', title: 'Gagal', text: err.responseJSON ? err.responseJSON.message : 'Gagal mengirim perintah ke mesin.' });
            }
        });
    });

    // Registrasi Biometrik Online (reg_online)
    $(document).on('click', '.btn-reg-online', function() {
        var cloudId = $(this).data('cloud-id');
        var pin = $(this).data('pin');
        var name = $(this).data('name') || `User #${pin}`;

        $('#reg-cloud-id').val(cloudId);
        $('#reg-pin').val(pin);
        $('#reg-user-pin').text(pin);
        $('#reg-user-name').text(name);
        $('#modal-reg-online').modal('show');
    });

    $('#form-reg-online').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Mengirim...');

        $.ajax({
            url: "{{ route('admin.fingerspot.users.reg_online') }}",
            type: "POST",
            data: form.serialize(),
            success: function(res) {
                submitBtn.prop('disabled', false).html('<i class="ti ti-broadcast me-1"></i> Mulai Perekaman di Mesin');
                $('#modal-reg-online').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Perekaman Biometrik Dimulai!',
                    html: `<p>Mesin absensi fisik sedang menunggu input sidik jari/wajah untuk PIN tersebut.</p>
                           <p class="small text-muted mb-0">Trans ID: <code>${res.trans_id}</code></p>`,
                    customClass: { confirmButton: 'btn btn-primary' },
                    buttonsStyling: false
                });
                tableCommands.ajax.reload(null, false);
            },
            error: function(err) {
                submitBtn.prop('disabled', false).html('<i class="ti ti-broadcast me-1"></i> Mulai Perekaman di Mesin');
                Swal.fire({ icon: 'error', title: 'Gagal', text: err.responseJSON ? err.responseJSON.message : 'Gagal memulai perekaman.' });
            }
        });
    });

    // Copy User Modal Open
    $(document).on('click', '.btn-copy-user', function() {
        var cloudId = $(this).data('cloud-id');
        var pin = $(this).data('pin');
        var name = $(this).data('name') || `User #${pin}`;

        $('#copy-source-cloud-id').val(cloudId);
        $('#copy-source-cloud-id-text').text(cloudId);
        $('#copy-pin').val(pin);
        $('#copy-user-pin').text(pin);
        $('#copy-user-name').text(name);
        $('#modal-copy-user').modal('show');
    });

    $('#form-copy-user').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Memproses...');

        $.ajax({
            url: "{{ route('admin.fingerspot.users.copy') }}",
            type: "POST",
            data: form.serialize(),
            success: function(res) {
                submitBtn.prop('disabled', false).html('<i class="ti ti-send me-1"></i> Proses Duplikasi');
                $('#modal-copy-user').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Duplikasi Terkirim!',
                    text: res.message,
                    customClass: { confirmButton: 'btn btn-primary' },
                    buttonsStyling: false
                });
                tableDeviceUsers.ajax.reload(null, false);
                tableCommands.ajax.reload(null, false);
            },
            error: function(err) {
                submitBtn.prop('disabled', false).html('<i class="ti ti-send me-1"></i> Proses Duplikasi');
                Swal.fire({ icon: 'error', title: 'Gagal Duplikasi', text: err.responseJSON ? err.responseJSON.message : 'Kesalahan server.' });
            }
        });
    });

    // Hapus User dari Mesin (delete_userinfo)
    $(document).on('click', '.btn-delete-device-user', function() {
        var cloudId = $(this).data('cloud-id');
        var pin = $(this).data('pin');
        var name = $(this).data('name') || `User #${pin}`;

        Swal.fire({
            title: `Hapus user "${name}" dari mesin?`,
            text: `Perintah delete_userinfo untuk PIN ${pin} akan dikirimkan ke mesin (${cloudId})!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus dari Mesin!',
            cancelButtonText: 'Batal',
            customClass: { confirmButton: 'btn btn-danger me-3', cancelButton: 'btn btn-label-secondary' },
            buttonsStyling: false
        }).then(function(result) {
            if (result.value) {
                $.ajax({
                    url: "{{ route('admin.fingerspot.users.delete') }}",
                    type: "DELETE",
                    data: { cloud_id: cloudId, pin: pin, _token: csrfToken },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Perintah Hapus Dikirim!',
                            html: `<p>Perintah penghapusan user telah dikirim ke mesin.</p>
                                   <p class="small text-muted mb-0">Trans ID: <code>${res.trans_id}</code></p>`,
                            customClass: { confirmButton: 'btn btn-primary' },
                            buttonsStyling: false
                        });
                        tableCommands.ajax.reload(null, false);
                        tableDeviceUsers.ajax.reload(null, false);
                        updateBulkDeviceUserBtn();
                    },
                    error: function(err) {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: err.responseJSON ? err.responseJSON.message : 'Gagal mengirim perintah hapus.' });
                    }
                });
            }
        });
    });

    // Hapus Massal User dari Mesin (Bulk delete_userinfo)
    $(document).on('click', '#btn-bulk-delete-device-users', function() {
        var selectedUsers = [];
        var previewList = [];

        $('.device-user-row-checkbox:checked').each(function() {
            var cloudId = $(this).data('cloud-id');
            var pin = $(this).data('pin');
            var name = $(this).data('name') || `User #${pin}`;

            selectedUsers.push({
                cloud_id: String(cloudId),
                pin: String(pin)
            });

            if (previewList.length < 5) {
                previewList.push(`${name} (PIN: ${pin})`);
            }
        });

        if (selectedUsers.length === 0) return;

        var previewText = previewList.join(', ');
        if (selectedUsers.length > 5) {
            previewText += ` dan ${selectedUsers.length - 5} lainnya`;
        }

        Swal.fire({
            title: `Hapus ${selectedUsers.length} Pengguna dari Mesin?`,
            html: `<p>Daftar pengguna yang akan dihapus dari mesin:</p>
                   <div class="alert alert-danger text-start py-2 px-3 mb-2 small">
                       <strong>${previewText}</strong>
                   </div>
                   <p class="text-danger small mb-0"><i class="ti ti-alert-triangle me-1"></i>Perintah <code>delete_userinfo</code> akan dikirimkan ke mesin bersangkutan dan data biometrik/kredensial pada mesin akan dihapus!</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: `Ya, Hapus (${selectedUsers.length}) dari Mesin!`,
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger me-3 waves-effect waves-light',
                cancelButton: 'btn btn-label-secondary waves-effect waves-light'
            },
            buttonsStyling: false
        }).then(function(result) {
            if (result.value) {
                Swal.fire({
                    title: 'Mengirim perintah hapus ke mesin...',
                    text: 'Mohon tunggu, proses sedang dikirim ke cloud Fingerspot',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                $.ajax({
                    url: "{{ route('admin.fingerspot.users.bulk_delete') }}",
                    type: "POST",
                    data: {
                        users: selectedUsers,
                        _token: csrfToken
                    },
                    success: function(res) {
                        Swal.close();
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Memproses!',
                                text: res.message,
                                customClass: { confirmButton: 'btn btn-primary' },
                                buttonsStyling: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Perhatian',
                                text: res.message,
                                customClass: { confirmButton: 'btn btn-primary' },
                                buttonsStyling: false
                            });
                        }
                        tableDeviceUsers.ajax.reload(null, false);
                        tableCommands.ajax.reload(null, false);
                        $('#check-all-device-users').prop('checked', false);
                        updateBulkDeviceUserBtn();
                    },
                    error: function(err) {
                        Swal.close();
                        var msg = err.responseJSON ? err.responseJSON.message : 'Gagal mengirim perintah hapus massal ke mesin.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: msg,
                            customClass: { confirmButton: 'btn btn-primary' },
                            buttonsStyling: false
                        });
                    }
                });
            }
        });
    });

    // ----------------------------------------------------
    // TAB 3: BATCH TAMBAHKAN SEMUA USER KE MESIN (ANTI-TIMEOUT & CHUNKED)
    // ----------------------------------------------------
    var batchState = {
        userIds: [],
        targetCloudIds: [],
        privilege: 1,
        overwrite: false,
        total: 0,
        processed: 0,
        success: 0,
        skipped: 0,
        failed: 0,
        isPaused: false,
        isProcessing: false,
        currentIndex: 0,
        chunkSize: 5
    };

    function resetBatchModal() {
        batchState = {
            userIds: [],
            targetCloudIds: [],
            privilege: 1,
            overwrite: false,
            total: 0,
            processed: 0,
            success: 0,
            skipped: 0,
            failed: 0,
            isPaused: false,
            isProcessing: false,
            currentIndex: 0,
            chunkSize: 5
        };

        // Reset step visibility
        $('#batch-step-1').removeClass('d-none');
        $('#batch-step-2').addClass('d-none');
        $('#batch-step-3').addClass('d-none');

        // Reset footer buttons
        $('#btn-batch-cancel').removeClass('d-none');
        $('#btn-back-to-step-1').addClass('d-none');
        $('#btn-pause-batch').addClass('d-none').removeClass('btn-outline-success').addClass('btn-outline-danger').html('<i class="ti ti-player-pause me-1"></i> Hentikan Sementara');
        $('#btn-precheck-batch').removeClass('d-none').prop('disabled', false).html('<i class="ti ti-search me-1"></i> Periksa & Analisis Pengguna');
        $('#btn-start-batch-push').addClass('d-none');
        $('#btn-finish-batch').addClass('d-none');

        // Reset Step 1 inputs
        updateTargetModeUI('all');
        $('#batch-user-privilege').val('1');
        $('#batch-overwrite-mode').val('skip');

        // Reset Step 3 elements
        $('#batch-live-spinner').show();
        $('#batch-status-title').text('Sedang Menambahkan Pengguna ke Mesin...');
        $('#batch-status-subtitle').text('Mohon jangan menutup jendela ini hingga seluruh batch selesai.');
        $('#batch-progress-bar').css('width', '0%').attr('aria-valuenow', 0);
        $('#batch-progress-text').text('Memproses: 0 / 0 Pengguna');
        $('#batch-progress-percent').text('0%');
        $('#batch-count-success').text('0');
        $('#batch-count-skipped').text('0');
        $('#batch-count-failed').text('0');
        $('#batch-live-log').html('<div class="text-muted">[Sistem] Siap memulai pengiriman batch massal...</div>');
        $('#btn-close-batch-modal').prop('disabled', false);
    }

    function updateTargetModeUI(mode) {
        if (mode === 'all') {
            $('#target-mode-all').prop('checked', true);
            $('#card-target-mode-all').attr('style', 'border-color: #7367f0 !important; background-color: rgba(115, 103, 240, 0.04); cursor: pointer;');
            $('#card-target-mode-single').attr('style', 'border-color: #dbdade !important; background-color: #ffffff; cursor: pointer;');
            $('#box-single-device-select').slideUp(200);
        } else {
            $('#target-mode-single').prop('checked', true);
            $('#card-target-mode-single').attr('style', 'border-color: #7367f0 !important; background-color: rgba(115, 103, 240, 0.04); cursor: pointer;');
            $('#card-target-mode-all').attr('style', 'border-color: #dbdade !important; background-color: #ffffff; cursor: pointer;');
            $('#box-single-device-select').slideDown(200);
        }
    }

    // Buka Modal Batch
    $('#btn-batch-push-users').on('click', function() {
        resetBatchModal();
        $('#modal-batch-push-users').modal('show');
    });

    // Toggle pilihan target mode
    $('input[name="batch_target_mode"]').on('change', function() {
        updateTargetModeUI($(this).val());
    });

    // Click on custom option card to toggle radio
    $('#card-target-mode-all').on('click', function(e) {
        if (!$(e.target).is('input')) {
            updateTargetModeUI('all');
        }
    });
    $('#card-target-mode-single').on('click', function(e) {
        if (!$(e.target).is('input') && !$(e.target).is('select')) {
            updateTargetModeUI('single');
        }
    });

    // STEP 1 -> STEP 2: Jalankan Precheck
    $('#btn-precheck-batch').on('click', function() {
        var btn = $(this);
        var targetMode = $('input[name="batch_target_mode"]:checked').val();
        var singleCloudId = $('#batch-single-cloud-id').val();

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menganalisis Pengguna...');

        $.ajax({
            url: "{{ route('admin.fingerspot.users.batch_precheck') }}",
            type: "POST",
            data: {
                target_mode: targetMode,
                cloud_id: singleCloudId,
                _token: csrfToken
            },
            success: function(res) {
                btn.prop('disabled', false).html('<i class="ti ti-search me-1"></i> Periksa & Analisis Pengguna');

                if (!res.status) {
                    Swal.fire({ icon: 'warning', title: 'Perhatian', text: res.message || 'Gagal melakukan analisis.' });
                    return;
                }

                // Simpan state
                batchState.userIds = res.user_ids || [];
                batchState.targetCloudIds = res.target_cloud_ids || [];
                batchState.privilege = parseInt($('#batch-user-privilege').val()) || 1;
                batchState.overwrite = $('#batch-overwrite-mode').val() === 'overwrite';
                batchState.total = batchState.userIds.length;

                // Tampilkan data KPI summary
                $('#precheck-total-users').text(res.total_users);
                $('#precheck-total-devices').text(res.total_target_devices + ' Mesin');
                $('#precheck-pending-users').text(res.pending_users_count);
                $('#precheck-existing-users').text(res.existing_users_count);
                $('#precheck-action-count').text(res.total_users);
                $('#precheck-mode-badge').text(targetMode === 'all' ? 'Target: Seluruh Mesin (' + res.total_target_devices + ')' : 'Target: 1 Mesin Terpilih');

                // Render preview table
                var tbody = $('#precheck-preview-tbody');
                tbody.empty();

                if (res.preview && res.preview.length > 0) {
                    $.each(res.preview, function(idx, user) {
                        var statusBadgeHtml = '';
                        if (user.status_badge === 'success') {
                            statusBadgeHtml = `<span class="badge rounded-pill bg-label-success px-3 py-1 fw-bold"><i class="ti ti-check me-1"></i>${user.status_label}</span>`;
                        } else if (user.status_badge === 'info') {
                            statusBadgeHtml = `<span class="badge rounded-pill bg-label-info px-3 py-1 fw-bold"><i class="ti ti-arrows-diff me-1"></i>${user.status_label}</span>`;
                        } else {
                            statusBadgeHtml = `<span class="badge rounded-pill px-3 py-1 fw-bold" style="color: #b35b00 !important; background-color: #fff4e5 !important; border: 1px solid rgba(255, 171, 0, 0.4);"><i class="ti ti-clock me-1"></i>${user.status_label}</span>`;
                        }

                        var initial = (user.name && user.name.length > 0) ? user.name.charAt(0).toUpperCase() : 'U';

                        var row = `<tr>
                            <td class="align-middle py-2 px-3"><span class="badge bg-label-primary font-monospace px-2 py-1 fw-bold">#${user.id}</span></td>
                            <td class="align-middle py-2 px-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xs rounded-circle bg-label-primary me-2 d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 11px;">
                                        ${initial}
                                    </div>
                                    <span class="fw-bold text-dark">${user.name}</span>
                                </div>
                            </td>
                            <td class="align-middle py-2 px-3"><code class="bg-light px-2 py-1 rounded text-secondary font-monospace">${user.username}</code></td>
                            <td class="align-middle py-2 px-3">${statusBadgeHtml}</td>
                        </tr>`;
                        tbody.append(row);
                    });
                    if (res.total_users > res.preview.length) {
                        tbody.append(`<tr><td colspan="4" class="text-center text-muted small py-3 bg-light"><em>... dan ${res.total_users - res.preview.length} civitas lainnya siap disinkronisasikan.</em></td></tr>`);
                    }
                } else {
                    tbody.append('<tr><td colspan="4" class="text-center text-muted py-4">Tidak ada data civitas ditemukan.</td></tr>');
                }

                // Ganti view ke Step 2
                $('#batch-step-1').addClass('d-none');
                $('#batch-step-2').removeClass('d-none');

                $('#btn-batch-cancel').addClass('d-none');
                $('#btn-back-to-step-1').removeClass('d-none');
                $('#btn-precheck-batch').addClass('d-none');
                $('#btn-start-batch-push').removeClass('d-none');
            },
            error: function(err) {
                btn.prop('disabled', false).html('<i class="ti ti-search me-1"></i> Periksa & Analisis Pengguna');
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memeriksa Data',
                    text: err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan saat memeriksa pengguna.'
                });
            }
        });
    });

    // STEP 2 -> STEP 1: Kembali
    $('#btn-back-to-step-1').on('click', function() {
        $('#batch-step-2').addClass('d-none');
        $('#batch-step-1').removeClass('d-none');

        $('#btn-back-to-step-1').addClass('d-none');
        $('#btn-batch-cancel').removeClass('d-none');
        $('#btn-start-batch-push').addClass('d-none');
        $('#btn-precheck-batch').removeClass('d-none');
    });

    // STEP 2 -> STEP 3: ACC & Mulai Proses Batch
    $('#btn-start-batch-push').on('click', function() {
        if (!batchState.userIds || batchState.userIds.length === 0) {
            Swal.fire({ icon: 'warning', title: 'Tidak Ada Data', text: 'Tidak ada civitas yang dapat ditambahkan.' });
            return;
        }

        // Tampilkan Step 3
        $('#batch-step-2').addClass('d-none');
        $('#batch-step-3').removeClass('d-none');

        $('#btn-back-to-step-1').addClass('d-none');
        $('#btn-start-batch-push').addClass('d-none');
        $('#btn-pause-batch').removeClass('d-none');
        $('#btn-close-batch-modal').prop('disabled', true); // Kunci modal agar tidak sengaja tertutup

        // Inisialisasi progress
        batchState.isProcessing = true;
        batchState.isPaused = false;
        batchState.currentIndex = 0;
        batchState.processed = 0;
        batchState.success = 0;
        batchState.skipped = 0;
        batchState.failed = 0;

        $('#batch-live-log').html('<div class="text-info"><i class="ti ti-clock me-1"></i> [' + new Date().toLocaleTimeString() + '] Memulai sinkronisasi massal ' + batchState.total + ' pengguna ke ' + batchState.targetCloudIds.length + ' mesin...</div>');

        // Jalankan eksekutor chunk
        executeNextBatchChunk();
    });

    function appendBatchLog(type, message) {
        var logBox = $('#batch-live-log');
        var color = type === 'success' ? '#28c76f' : (type === 'skipped' ? '#00cfe8' : (type === 'failed' ? '#ea5455' : '#ff9f43'));
        var icon = type === 'success' ? 'ti ti-check' : (type === 'skipped' ? 'ti ti-arrow-forward' : (type === 'failed' ? 'ti ti-x' : 'ti ti-info-circle'));
        var time = new Date().toLocaleTimeString();

        var entry = $(`<div style="color: ${color}; margin-bottom: 2px;">
            <span class="text-muted">[${time}]</span> <i class="${icon} me-1"></i> ${message}
        </div>`);

        logBox.append(entry);
        if (logBox[0]) {
            logBox.scrollTop(logBox[0].scrollHeight);
        }
    }

    function updateBatchProgressUI() {
        var pct = batchState.total > 0 ? Math.min(100, Math.round((batchState.processed / batchState.total) * 100)) : 100;
        $('#batch-progress-bar').css('width', pct + '%').attr('aria-valuenow', pct);
        $('#batch-progress-percent').text(pct + '%');
        $('#batch-progress-text').text(`Memproses: ${batchState.processed} / ${batchState.total} Pengguna`);
        $('#batch-count-success').text(batchState.success);
        $('#batch-count-skipped').text(batchState.skipped);
        $('#batch-count-failed').text(batchState.failed);
    }

    function executeNextBatchChunk() {
        if (batchState.isPaused) {
            appendBatchLog('info', 'Proses dijeda oleh pengguna.');
            return;
        }

        if (batchState.currentIndex >= batchState.userIds.length) {
            // Selesai seluruh batch!
            finishBatchPush();
            return;
        }

        var chunk = batchState.userIds.slice(batchState.currentIndex, batchState.currentIndex + batchState.chunkSize);

        $.ajax({
            url: "{{ route('admin.fingerspot.users.batch_process') }}",
            type: "POST",
            data: {
                target_cloud_ids: batchState.targetCloudIds,
                user_ids: chunk,
                overwrite: batchState.overwrite ? 1 : 0,
                privilege: batchState.privilege,
                _token: csrfToken
            },
            timeout: 60000,
            success: function(res) {
                if (res.logs && res.logs.length > 0) {
                    $.each(res.logs, function(i, item) {
                        appendBatchLog(item.status, `${item.name} (PIN ${item.pin}) &rarr; ${item.message}`);
                    });
                }

                batchState.success += (res.success_count || 0);
                batchState.skipped += (res.skipped_count || 0);
                batchState.failed += (res.failed_count || 0);
                batchState.processed += chunk.length;
                batchState.currentIndex += chunk.length;

                updateBatchProgressUI();

                // Lanjut ke chunk berikutnya dengan jeda 250ms agar browser & server tetap responsif
                if (!batchState.isPaused) {
                    setTimeout(executeNextBatchChunk, 250);
                }
            },
            error: function(xhr, status, error) {
                // Jangan sampai macet / gagal total; catat chunk ini dan lanjutkan!
                appendBatchLog('failed', `Batch PIN [${chunk.join(', ')}] mengalami kendala: ${error || 'Network error'}. Melanjutkan chunk berikutnya...`);
                batchState.failed += (chunk.length * batchState.targetCloudIds.length);
                batchState.processed += chunk.length;
                batchState.currentIndex += chunk.length;

                updateBatchProgressUI();

                if (!batchState.isPaused) {
                    setTimeout(executeNextBatchChunk, 500);
                }
            }
        });
    }

    // Pause / Resume Process
    $('#btn-pause-batch').on('click', function() {
        if (!batchState.isPaused) {
            batchState.isPaused = true;
            $(this).removeClass('btn-outline-danger').addClass('btn-outline-success')
                   .html('<i class="ti ti-player-play me-1"></i> Lanjutkan Proses');
            $('#batch-status-title').text('Proses Dijeda');
            $('#batch-live-spinner').hide();
        } else {
            batchState.isPaused = false;
            $(this).removeClass('btn-outline-success').addClass('btn-outline-danger')
                   .html('<i class="ti ti-player-pause me-1"></i> Hentikan Sementara');
            $('#batch-status-title').text('Sedang Menambahkan Pengguna ke Mesin...');
            $('#batch-live-spinner').show();
            executeNextBatchChunk();
        }
    });

    function finishBatchPush() {
        batchState.isProcessing = false;
        $('#batch-live-spinner').hide();
        $('#batch-status-title').html('<i class="ti ti-circle-check text-success me-1"></i> Sinkronisasi Massal Selesai!');
        $('#batch-status-subtitle').text('Seluruh daftar pengguna telah selesai diproses ke mesin target.');
        $('#btn-pause-batch').addClass('d-none');
        $('#btn-finish-batch').removeClass('d-none');
        $('#btn-close-batch-modal').prop('disabled', false);

        appendBatchLog('success', `[SELESAI] Total Berhasil: ${batchState.success}, Dilewati: ${batchState.skipped}, Gagal: ${batchState.failed}.`);

        // Refresh tabel di background
        tableDeviceUsers.ajax.reload(null, false);
        tableCommands.ajax.reload(null, false);
    }

    // Selesai & Tutup Modal
    $('#btn-finish-batch').on('click', function() {
        $('#modal-batch-push-users').modal('hide');
        Swal.fire({
            icon: 'success',
            title: 'Sinkronisasi Selesai!',
            html: `<p>Proses penambahan civitas ke mesin biometrik telah tuntas dikirimkan.</p>
                   <div class="row text-center g-2 mt-2">
                       <div class="col-4"><span class="badge bg-success w-100 py-2">Berhasil: ${batchState.success}</span></div>
                       <div class="col-4"><span class="badge bg-info w-100 py-2">Dilewati: ${batchState.skipped}</span></div>
                       <div class="col-4"><span class="badge bg-danger w-100 py-2">Gagal: ${batchState.failed}</span></div>
                   </div>`,
            customClass: { confirmButton: 'btn btn-primary' },
            buttonsStyling: false
        });
    });

    // ----------------------------------------------------
    // TAB 4: LOG PERINTAH & WEBHOOK
    // ----------------------------------------------------
    var tableCommands = $('#table-commands-log').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.fingerspot.commands.data') }}",
            data: function(d) {
                d.cloud_id = $('#filter-log-cloud-id').val();
                d.command_type = $('#filter-log-type').val();
                d.status = $('#filter-log-status').val();
            }
        },
        columns: [
            { data: 'created_at', name: 'created_at' },
            { data: 'command_type', name: 'command_type' },
            { 
                data: 'cloud_id', 
                name: 'cloud_id',
                render: function(data, type, row) {
                    var dev = row.device_name ? `<div>${row.device_name}</div>` : '';
                    return `${dev}<code class="small text-muted">${data || '-'}</code>`;
                }
            },
            { 
                data: 'trans_id', 
                name: 'trans_id',
                render: function(data) {
                    return data ? `<code class="small">${data}</code>` : '<span class="text-muted">-</span>';
                }
            },
            { data: 'status', name: 'status', className: 'text-center' },
            { 
                data: 'message', 
                name: 'message',
                render: function(data) {
                    return `<span class="small text-truncate d-inline-block" style="max-width: 250px;" title="${data || ''}">${data || '-'}</span>`;
                }
            },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[0, 'desc']]
    });

    $('#filter-log-cloud-id, #filter-log-type, #filter-log-status').on('change', function() {
        tableCommands.ajax.reload();
    });

    $('#btn-refresh-command-logs').on('click', function() {
        tableCommands.ajax.reload(null, false);
    });

    // Modal View Detail Command / JSON
    $(document).on('click', '.btn-view-command', function() {
        var id = $(this).data('id');

        $.ajax({
            url: "{{ route('admin.fingerspot.commands.detail') }}",
            type: "GET",
            data: { id: id },
            success: function(res) {
                if (res.status && res.data) {
                    var d = res.data;
                    $('#detail-command-type').text(d.command_type);
                    $('#detail-trans-id').text(d.trans_id || '-');
                    $('#detail-status').html(
                        d.status === 'success' ? '<span class="badge bg-label-success">Sukses</span>' :
                        (d.status === 'failed' ? '<span class="badge bg-label-danger">Gagal</span>' : '<span class="badge bg-label-warning">Pending</span>')
                    );

                    $('#detail-json-request').text(d.payload_request ? JSON.stringify(d.payload_request, null, 2) : '// Tidak ada payload request tersimpan.');
                    $('#detail-json-response').text(d.payload_response ? JSON.stringify(d.payload_response, null, 2) : '// Tidak ada respons tersimpan.');
                    $('#detail-json-callback').text(d.callback_payload ? JSON.stringify(d.callback_payload, null, 2) : '// Belum ada callback webhook diterima.');

                    $('#modal-view-command').modal('show');
                }
            }
        });
    });

    // Bersihkan Log
    $('#btn-clear-command-logs').on('click', function() {
        Swal.fire({
            title: 'Bersihkan Riwayat Log?',
            text: 'Pilih opsi pembersihan riwayat log perintah & webhook:',
            icon: 'warning',
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonText: 'Bersihkan > 30 Hari',
            denyButtonText: 'Bersihkan Semua (Kosongkan)',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-warning me-2',
                denyButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
        }).then(function(result) {
            if (result.isConfirmed || result.isDenied) {
                var days = result.isDenied ? 'all' : 30;
                $.ajax({
                    url: "{{ route('admin.fingerspot.commands.clear') }}",
                    type: "POST",
                    data: { days: days, _token: csrfToken },
                    success: function(res) {
                        showToastr('success', 'Sukses', res.message);
                        tableCommands.ajax.reload(null, false);
                    }
                });
            }
        });
    });

    // ----------------------------------------------------
    // TAB 5: API PLAYGROUND & TESTER
    // ----------------------------------------------------
    function updatePlaygroundFields() {
        var ep = $('#test-endpoint').val();
        $('.dynamic-param-group').addClass('d-none');

        if (ep === 'get_attlog') {
            $('#param-group-attlog').removeClass('d-none');
        } else if (ep === 'get_userinfo' || ep === 'delete_userinfo') {
            $('#param-group-pin').removeClass('d-none');
        } else if (ep === 'set_userinfo') {
            $('#param-group-setuser').removeClass('d-none');
        } else if (ep === 'set_time') {
            $('#param-group-time').removeClass('d-none');
        } else if (ep === 'reg_online') {
            $('#param-group-pin').removeClass('d-none');
            $('#param-group-regonline').removeClass('d-none');
        }
    }

    $('#test-endpoint').on('change', updatePlaygroundFields);
    updatePlaygroundFields();

    $('#form-api-tester').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = $('#btn-run-tester');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menjalankan...');

        $('#code-test-response').text('// Mengirim permintaan ke Fingerspot API...');

        $.ajax({
            url: "{{ route('admin.fingerspot.api.test') }}",
            type: "POST",
            data: form.serialize(),
            success: function(res) {
                submitBtn.prop('disabled', false).html('<i class="ti ti-player-play me-1"></i> Eksekusi API');

                var latency = res.duration_ms || 0;
                $('#badge-test-latency').text(`${latency} ms`).removeClass('d-none');

                var pretty = JSON.stringify(res, null, 2);
                $('#code-test-response').text(pretty);
                $('#btn-copy-test-response').attr('data-clipboard-text', pretty);

                tableCommands.ajax.reload(null, false);
            },
            error: function(err) {
                submitBtn.prop('disabled', false).html('<i class="ti ti-player-play me-1"></i> Eksekusi API');
                var errObj = err.responseJSON || { error: 'Network / server error' };
                var pretty = JSON.stringify(errObj, null, 2);
                $('#code-test-response').text(pretty);
                $('#btn-copy-test-response').attr('data-clipboard-text', pretty);
            }
        });
    });
});
</script>
@endpush
