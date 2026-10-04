<?php

namespace App\Http\Controllers\Admin;

use App\Models\Device;
use App\Models\Absensi;
use App\Models\User;
use App\Models\Verify;
use App\Models\FingerspotCommand;
use App\Models\FingerspotDeviceUser;
use App\Http\Services\Fingerspot;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FingerspotController extends Controller
{
    /**
     * Display main Fingerspot Hub view.
     */
    public function index()
    {
        $devices = Device::all();
        $totalDevices = $devices->count();
        $todayScans = Absensi::where('tgl_absen', date('Y-m-d'))->count();
        $totalDeviceUsers = FingerspotDeviceUser::count();
        $todayCommands = FingerspotCommand::whereDate('created_at', date('Y-m-d'))->count();

        // Local users list for push to device (exclude superadmin)
        $users = User::whereDoesntHave('role', function ($q) {
                $q->where(DB::raw('LOWER(TRIM(akses))'), 'superadmin');
            })
            ->select('id', 'name', 'username')
            ->orderBy('name')
            ->limit(500)
            ->get();


        $webhookUrl = url('/api/webhook/fingerspot');
        $apiUrl = env('FINGERSPOT_URL', 'https://developer.fingerspot.io/api');
        $apiKey = env('FINGERSPOT_APIKEY', '3R5XAP1OFW3T22TV');

        $timezones = [
            'Asia/Jakarta'  => 'WIB - Asia/Jakarta (UTC+7)',
            'Asia/Makassar' => 'WITA - Asia/Makassar (UTC+8)',
            'Asia/Jayapura' => 'WIT - Asia/Jayapura (UTC+9)',
        ];

        return view('admin.fingerspot.index', compact(
            'devices',
            'totalDevices',
            'todayScans',
            'totalDeviceUsers',
            'todayCommands',
            'users',
            'webhookUrl',
            'apiUrl',
            'apiKey',
            'timezones'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | DEVICE MANAGEMENT
    |--------------------------------------------------------------------------
    */

    public function devicesData()
    {
        $devices = Device::select('*')->orderBy('id', 'asc');

        return DataTables::of($devices)
            ->addColumn('user_count', function ($row) {
                $count = FingerspotDeviceUser::where('cloud_id', $row->cloud_id)->count();
                return '<span class="badge bg-label-info">' . $count . ' Users</span>';
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="d-inline-flex gap-1">
                        <button type="button" class="btn btn-sm btn-icon btn-label-success btn-device-test-active" data-cloud-id="' . e($row->cloud_id) . '" data-name="' . e($row->name) . '" title="Tes Keaktifan & Koneksi Mesin">
                            <i class="ti ti-activity ti-xs"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-icon btn-label-primary btn-device-info" data-cloud-id="' . e($row->cloud_id) . '" data-name="' . e($row->name) . '" title="Periksa Info Online">
                            <i class="ti ti-wifi ti-xs"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-icon btn-label-warning btn-device-time" data-cloud-id="' . e($row->cloud_id) . '" data-name="' . e($row->name) . '" title="Atur Waktu / Timezone">
                            <i class="ti ti-clock ti-xs"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-device-restart" data-cloud-id="' . e($row->cloud_id) . '" data-name="' . e($row->name) . '" title="Restart Mesin">
                            <i class="ti ti-refresh ti-xs"></i>
                        </button>
                        <div class="dropdown">
                            <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="ti ti-dots-vertical ti-xs"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end m-0">
                                <li>
                                    <button class="dropdown-item edit-device-btn"
                                        data-id="' . $row->id . '"
                                        data-name="' . e($row->name) . '"
                                        data-cloud-id="' . e($row->cloud_id) . '">
                                        <i class="ti ti-pencil me-1"></i> Edit
                                    </button>
                                </li>
                                <li>
                                    <button class="dropdown-item text-danger delete-device-btn"
                                        data-id="' . $row->id . '"
                                        data-name="' . e($row->name) . '">
                                        <i class="ti ti-trash me-1"></i> Hapus
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>';
            })
            ->rawColumns(['user_count', 'action'])
            ->toJson();
    }

    public function deviceStore(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'cloud_id' => 'required|string|max:255|unique:device,cloud_id',
        ]);

        try {
            $device = Device::create([
                'name'     => $request->name,
                'cloud_id' => strtoupper(trim($request->cloud_id)),
            ]);

            return response()->json([
                'status'  => true,
                'type'    => 'success',
                'message' => "Perangkat {$device->name} berhasil ditambahkan.",
                'data'    => $device
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'type'    => 'error',
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function deviceUpdate(Request $request)
    {
        $request->validate([
            'id'       => 'required|exists:device,id',
            'name'     => 'required|string|max:255',
            'cloud_id' => 'required|string|max:255|unique:device,cloud_id,' . $request->id,
        ]);

        try {
            $device = Device::findOrFail($request->id);
            $device->update([
                'name'     => $request->name,
                'cloud_id' => strtoupper(trim($request->cloud_id)),
            ]);

            return response()->json([
                'status'  => true,
                'type'    => 'success',
                'message' => "Perangkat {$device->name} berhasil diperbarui.",
                'data'    => $device
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'type'    => 'error',
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function deviceDelete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:device,id',
        ]);

        try {
            $device = Device::findOrFail($request->id);
            $name = $device->name;
            $device->delete();

            return response()->json([
                'status'  => true,
                'type'    => 'success',
                'message' => "Perangkat {$name} berhasil dihapus."
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'type'    => 'error',
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function deviceInfo(Request $request)
    {
        $request->validate([
            'cloud_id' => 'required|string',
        ]);

        $response = Fingerspot::getDevice(null, $request->cloud_id);

        return response()->json($response);
    }

    public function deviceSetTime(Request $request)
    {
        $request->validate([
            'cloud_id' => 'required|string',
            'timezone' => 'required|string',
        ]);

        $response = Fingerspot::setTime(null, $request->cloud_id, $request->timezone);

        return response()->json($response);
    }

    public function deviceRestart(Request $request)
    {
        $request->validate([
            'cloud_id' => 'required|string',
        ]);

        $response = Fingerspot::restartDevice(null, $request->cloud_id);

        return response()->json($response);
    }

    /*
    |--------------------------------------------------------------------------
    | ATTENDANCE LOG (ATTLOG) MANAGEMENT
    |--------------------------------------------------------------------------
    */

    public function attlogFetch(Request $request)
    {
        $request->validate([
            'cloud_id'   => 'required|string',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date'   => 'required|date_format:Y-m-d',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate   = Carbon::parse($request->end_date);

        // Maximum date range allowed by Fingerspot API is 2 days
        if ($startDate->diffInDays($endDate) > 1) {
            return response()->json([
                'success' => false,
                'message' => 'Rentang tanggal maksimal 2 hari sesuai ketentuan Fingerspot API.'
            ], 422);
        }

        $res = Fingerspot::getAttLog(null, $request->cloud_id, $request->start_date, $request->end_date);

        if (!isset($res['data']) || !is_array($res['data'])) {
            return response()->json($res);
        }

        // Enrich records with user names from local database or device cache
        $records = $res['data'];
        $pins = array_unique(array_column($records, 'pin'));
        $usersMap = User::whereIn('id', $pins)->pluck('name', 'id')->toArray();
        $deviceUsersMap = FingerspotDeviceUser::where('cloud_id', $request->cloud_id)
            ->whereIn('pin', $pins)
            ->pluck('name', 'pin')
            ->toArray();

        $verifyLabels = [
            1 => 'Fingerprint',
            2 => 'Password',
            3 => 'RFID Card',
            4 => 'Face',
            6 => 'Vein',
        ];

        $statusScanLabels = [
            0 => 'Scan In (Masuk)',
            1 => 'Scan Out (Pulang)',
            2 => 'Break In',
            3 => 'Break Out',
        ];

        foreach ($records as &$item) {
            $p = $item['pin'];
            $item['user_name'] = $usersMap[$p] ?? $deviceUsersMap[$p] ?? 'Tidak Dikenal (User #' . $p . ')';
            $item['verify_label'] = $verifyLabels[$item['verify'] ?? 1] ?? ('Mode ' . ($item['verify'] ?? '?'));
            $item['status_scan_label'] = $statusScanLabels[$item['status_scan'] ?? 0] ?? ('Status ' . ($item['status_scan'] ?? '?'));
        }

        return response()->json([
            'success' => true,
            'trans_id' => $res['trans_id'] ?? null,
            'total' => count($records),
            'data' => $records,
        ]);
    }

    public function attlogSync(Request $request)
    {
        $request->validate([
            'cloud_id' => 'required|string',
            'logs'     => 'required|array',
        ]);

        $result = Fingerspot::syncAttLogs($request->cloud_id, $request->logs);

        return response()->json([
            'status'  => true,
            'type'    => 'success',
            'message' => "Sinkronisasi selesai! Total: {$result['total']} scan. Baru: {$result['created']}, Diperbarui: {$result['updated']}, Dilewati: {$result['skipped']}.",
            'data'    => $result,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DEVICE USER MANAGEMENT
    |--------------------------------------------------------------------------
    */

    public function deviceUsersData(Request $request)
    {
        $query = FingerspotDeviceUser::with(['device', 'localUser'])->select('fingerspot_device_users.*');

        if ($request->filled('cloud_id')) {
            $query->where('cloud_id', $request->cloud_id);
        }

        return DataTables::of($query)
            ->addColumn('user_display', function ($row) {
                $name = $row->name ?: ($row->localUser ? $row->localUser->name : 'N/A');
                return '<div class="d-flex flex-column">
                            <span class="fw-semibold">' . e($name) . '</span>
                            <small class="text-muted">PIN: ' . e($row->pin) . '</small>
                        </div>';
            })
            ->addColumn('credentials', function ($row) {
                $badges = [];
                if ($row->finger > 0) $badges[] = '<span class="badge bg-label-primary"><i class="ti ti-fingerprint ti-xs me-1"></i>' . $row->finger . '</span>';
                if ($row->face > 0) $badges[] = '<span class="badge bg-label-info"><i class="ti ti-scan ti-xs me-1"></i>' . $row->face . '</span>';
                if ($row->vein > 0) $badges[] = '<span class="badge bg-label-warning"><i class="ti ti-hand-stop ti-xs me-1"></i>' . $row->vein . '</span>';
                if ($row->rfid) $badges[] = '<span class="badge bg-label-secondary" title="' . e($row->rfid) . '"><i class="ti ti-id ti-xs me-1"></i>Card</span>';
                if ($row->password) $badges[] = '<span class="badge bg-label-dark"><i class="ti ti-lock ti-xs me-1"></i>PIN</span>';

                return !empty($badges) ? implode(' ', $badges) : '<span class="text-muted small">Belum ada biometrik</span>';
            })
            ->editColumn('privilege', function ($row) {
                return '<span class="badge bg-label-success">' . $row->privilege_label . '</span>';
            })
            ->editColumn('last_sync_at', function ($row) {
                return $row->last_sync_at ? $row->last_sync_at->diffForHumans() : '-';
            })
            ->addColumn('action', function ($row) {
                $hasTemplate = !empty($row->template) ? '1' : '0';
                $fingerCount = (int)$row->finger;
                return '
                    <div class="d-inline-flex gap-1">
                        <button type="button" class="btn btn-sm btn-icon btn-label-info btn-get-userinfo" data-cloud-id="' . e($row->cloud_id) . '" data-pin="' . e($row->pin) . '" title="Refresh Detail dari Mesin">
                            <i class="ti ti-refresh ti-xs"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-icon btn-label-success btn-reg-online" data-cloud-id="' . e($row->cloud_id) . '" data-pin="' . e($row->pin) . '" data-name="' . e($row->name) . '" title="Registrasi Biometrik Online">
                            <i class="ti ti-fingerprint ti-xs"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-icon btn-label-primary btn-copy-user" data-cloud-id="' . e($row->cloud_id) . '" data-pin="' . e($row->pin) . '" data-name="' . e($row->name) . '" data-finger="' . $fingerCount . '" data-has-template="' . $hasTemplate . '" title="Transfer / Salin User atau Fingerprint">
                            <i class="ti ti-copy ti-xs"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-delete-device-user" data-cloud-id="' . e($row->cloud_id) . '" data-pin="' . e($row->pin) . '" data-name="' . e($row->name) . '" title="Hapus dari Mesin">
                            <i class="ti ti-trash ti-xs"></i>
                        </button>
                    </div>';
            })
            ->addColumn('checkbox', function ($row) {
                $hasTemplate = !empty($row->template) ? '1' : '0';
                $fingerCount = (int)$row->finger;
                return '<div class="text-center"><input type="checkbox" class="form-check-input device-user-row-checkbox" data-cloud-id="' . e($row->cloud_id) . '" data-pin="' . e($row->pin) . '" data-name="' . e($row->name) . '" data-finger="' . $fingerCount . '" data-has-template="' . $hasTemplate . '"></div>';
            })
            ->rawColumns(['checkbox', 'user_display', 'credentials', 'privilege', 'last_sync_at', 'action'])
            ->toJson();
    }

    public function deviceUsersGetAllPin(Request $request)
    {
        $request->validate([
            'cloud_id' => 'required|string',
        ]);

        $res = Fingerspot::getAllPin(null, $request->cloud_id);

        return response()->json($res);
    }

    public function deviceUsersGetInfo(Request $request)
    {
        $request->validate([
            'cloud_id' => 'required|string',
            'pin'      => 'required|string',
        ]);

        $res = Fingerspot::getUserInfo(null, $request->cloud_id, $request->pin);

        return response()->json($res);
    }

    public function deviceUsersSetInfo(Request $request)
    {
        $request->validate([
            'cloud_id'  => 'required|string',
            'pin'       => 'required|string',
            'name'      => 'required|string|max:100',
            'privilege' => 'required|in:1,2,3',
            'password'  => 'nullable|string',
            'rfid'      => 'nullable|string',
            'template'  => 'nullable|string',
        ]);

        $targetUser = User::where('id', $request->pin)
            ->orWhere('kode', $request->pin)
            ->orWhere('username', $request->pin)
            ->first();

        if ($targetUser && $targetUser->isSuperAdmin()) {
            return response()->json([
                'status'  => false,
                'message' => 'User dengan role Superadmin tidak dapat didaftarkan ke mesin biometrik.',
            ], 422);
        }


        $res = Fingerspot::setUserInfo(
            null,
            $request->cloud_id,
            $request->pin,
            $request->name,
            $request->privilege,
            $request->password,
            $request->rfid,
            $request->template
        );

        // Also update local cache
        FingerspotDeviceUser::updateOrCreate(
            [
                'cloud_id' => $request->cloud_id,
                'pin'      => (string)$request->pin,
            ],
            [
                'name'         => $request->name,
                'privilege'    => (int)$request->privilege,
                'password'     => $request->password,
                'rfid'         => $request->rfid,
                'template'     => $request->template,
                'last_sync_at' => now(),
            ]
        );

        return response()->json($res);
    }

    public function deviceUsersDelete(Request $request)
    {
        $request->validate([
            'cloud_id' => 'required|string',
            'pin'      => 'required|string',
            'target'   => 'nullable|in:single,all',
        ]);

        $pin = (string) $request->pin;
        $target = $request->input('target', 'single');

        if ($target === 'all') {
            $devices = Device::all();
            $results = [];
            foreach ($devices as $dev) {
                $r = Fingerspot::deleteUserInfo(null, $dev->cloud_id, $pin);
                FingerspotDeviceUser::where('cloud_id', $dev->cloud_id)->where('pin', $pin)->delete();
                $results[] = [
                    'device'   => $dev->name,
                    'cloud_id' => $dev->cloud_id,
                    'success'  => $r['success'] ?? false,
                    'trans_id' => $r['trans_id'] ?? null,
                ];
            }
            return response()->json([
                'success'  => true,
                'status'   => true,
                'message'  => "Perintah penghapusan user PIN {$pin} berhasil dikirim ke SEMUA mesin (" . count($devices) . " mesin).",
                'details'  => $results,
                'trans_id' => $results[0]['trans_id'] ?? '-',
            ]);
        }

        $res = Fingerspot::deleteUserInfo(null, $request->cloud_id, $pin);

        if ($res['success'] ?? false) {
            FingerspotDeviceUser::where('cloud_id', $request->cloud_id)->where('pin', $pin)->delete();
        }

        return response()->json($res);
    }

    public function deviceUsersBulkDelete(Request $request)
    {
        $request->validate([
            'users'            => 'required|array|min:1',
            'users.*.cloud_id' => 'required|string',
            'users.*.pin'      => 'required|string',
        ]);

        @set_time_limit(120);

        $successCount = 0;
        $failedCount = 0;
        $logs = [];

        foreach ($request->users as $item) {
            $cloudId = $item['cloud_id'];
            $pin = (string) $item['pin'];

            try {
                $res = Fingerspot::deleteUserInfo(null, $cloudId, $pin);

                // Always clean local database cache for this device user
                FingerspotDeviceUser::where('cloud_id', $cloudId)->where('pin', $pin)->delete();

                if ($res['success'] ?? false) {
                    $successCount++;
                    $logs[] = [
                        'status'   => 'success',
                        'pin'      => $pin,
                        'cloud_id' => $cloudId,
                        'message'  => "PIN {$pin} (Mesin {$cloudId}): Perintah hapus berhasil dikirim (Trans ID: " . ($res['trans_id'] ?? '-') . ")",
                    ];
                } else {
                    $msg = $res['message'] ?? 'Gagal menghapus dari mesin';
                    $successCount++;
                    $logs[] = [
                        'status'   => 'warning',
                        'pin'      => $pin,
                        'cloud_id' => $cloudId,
                        'message'  => "PIN {$pin} (Mesin {$cloudId}): {$msg} (Cache lokal dihapus)",
                    ];
                }
            } catch (\Throwable $e) {
                FingerspotDeviceUser::where('cloud_id', $cloudId)->where('pin', $pin)->delete();
                $failedCount++;
                $logs[] = [
                    'status'   => 'failed',
                    'pin'      => $pin,
                    'cloud_id' => $cloudId,
                    'message'  => "PIN {$pin} (Mesin {$cloudId}): " . $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'status'        => true,
            'success'       => true,
            'message'       => "Berhasil memproses {$successCount} pengguna.",
            'success_count' => $successCount,
            'failed_count'  => $failedCount,
            'logs'          => $logs,
        ]);
    }

    public function deviceUsersRegOnline(Request $request)
    {
        $request->validate([
            'cloud_id'     => 'required|string',
            'pin'          => 'required|string',
            'verification' => 'required|integer', // 0-9 = finger, 12 = face, 13 = vein
        ]);

        $res = Fingerspot::regOnline(null, $request->cloud_id, $request->pin, $request->verification);

        return response()->json($res);
    }

    public function deviceUsersCopy(Request $request)
    {
        $request->validate([
            'source_cloud_id' => 'required|string',
            'target_cloud_id' => 'required|string|different:source_cloud_id',
            'pin'             => 'required|string',
            'copy_mode'       => 'nullable|in:fingerprint_only,full',
        ]);

        $pin = (string)$request->pin;
        $copyMode = $request->input('copy_mode', 'fingerprint_only');

        // Check if user is superadmin
        $isSuperAdmin = User::where(function ($q) use ($pin) {
            $q->where('id', $pin)
              ->orWhere('kode', $pin)
              ->orWhere('username', $pin);
        })->whereHas('role', function ($q) {
            $q->where(DB::raw('LOWER(TRIM(akses))'), 'superadmin');
        })->exists();

        if ($isSuperAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'User dengan role Superadmin tidak diperkenankan untuk disalin ke mesin biometrik.',
            ], 422);
        }

        $sourceUser = FingerspotDeviceUser::where('cloud_id', $request->source_cloud_id)
            ->where('pin', $pin)
            ->first();

        if (!$sourceUser) {
            return response()->json([
                'success' => false,
                'message' => 'Data user sumber belum terdaftar di sistem lokal. Silakan refresh info user dari mesin sumber terlebih dahulu.'
            ], 404);
        }

        $hasBiometrics = ($sourceUser->finger > 0 || $sourceUser->face > 0 || $sourceUser->vein > 0);
        $hasTemplate = !empty($sourceUser->template);

        $targetDevice = Device::where('cloud_id', $request->target_cloud_id)->first();
        $targetDevName = $targetDevice ? $targetDevice->name : $request->target_cloud_id;

        // MODE: Fingerprint Only
        if ($copyMode === 'fingerprint_only') {
            if (!$hasTemplate) {
                $detail = $hasBiometrics 
                    ? "Mesin sumber mendeteksi {$sourceUser->finger} sidik jari, tetapi data template belum tersimpan di server." 
                    : "User tidak memiliki data sidik jari di mesin sumber.";
                return response()->json([
                    'success' => false,
                    'message' => "Tidak ada template sidik jari yang dapat disalin. {$detail} Pastikan Mesin Sumber menyala & online, lalu klik tombol 'Refresh Detail' (ikon biru) terlebih dahulu."
                ], 422);
            }

            // Look up existing user on target device to keep original name & credentials intact
            $targetUser = FingerspotDeviceUser::where('cloud_id', $request->target_cloud_id)
                ->where('pin', $pin)
                ->first();

            $localUser = User::where('id', $pin)->first();

            $name = $targetUser && !empty($targetUser->name) 
                ? $targetUser->name 
                : ($sourceUser->name ?: ($localUser ? $localUser->name : "User #{$pin}"));
            $privilege = $targetUser ? $targetUser->privilege : $sourceUser->privilege;
            $password = $targetUser ? ($targetUser->password ?? '') : '';
            $rfid = $targetUser ? ($targetUser->rfid ?? '') : '';
            $template = $sourceUser->template;
            $fingerCount = $sourceUser->finger ?: 1;

            $res = Fingerspot::setUserInfo(
                null,
                $request->target_cloud_id,
                $pin,
                $name,
                $privilege,
                $password,
                $rfid,
                $template
            );

            // Update target cache with the new template
            FingerspotDeviceUser::updateOrCreate(
                [
                    'cloud_id' => $request->target_cloud_id,
                    'pin'      => $pin,
                ],
                [
                    'name'         => $name,
                    'privilege'    => $privilege,
                    'finger'       => $fingerCount,
                    'password'     => $password,
                    'rfid'         => $rfid,
                    'template'     => $template,
                    'last_sync_at' => now(),
                ]
            );

            return response()->json([
                'success'  => true,
                'message'  => "Perintah salin fingerprint PIN {$pin} ({$name}) ke {$targetDevName} berhasil dikirim.",
                'response' => $res,
            ]);
        }

        // MODE: Full copy
        if ($hasBiometrics && !$hasTemplate) {
            return response()->json([
                'success' => false,
                'message' => "User PIN {$sourceUser->pin} memiliki biometrik di mesin sumber, namun template-nya belum ditarik ke server. Pastikan Mesin Sumber menyala & online, lalu klik tombol 'Refresh Detail' (ikon biru) terlebih dahulu agar sidik jari tersimpan sebelum disalin."
            ], 422);
        }

        $res = Fingerspot::setUserInfo(
            null,
            $request->target_cloud_id,
            $sourceUser->pin,
            $sourceUser->name,
            $sourceUser->privilege,
            $sourceUser->password,
            $sourceUser->rfid,
            $sourceUser->template
        );

        // Update target cache
        FingerspotDeviceUser::updateOrCreate(
            [
                'cloud_id' => $request->target_cloud_id,
                'pin'      => (string)$sourceUser->pin,
            ],
            [
                'name'         => $sourceUser->name,
                'privilege'    => $sourceUser->privilege,
                'finger'       => $sourceUser->finger,
                'face'         => $sourceUser->face,
                'vein'         => $sourceUser->vein,
                'password'     => $sourceUser->password,
                'rfid'         => $sourceUser->rfid,
                'template'     => $sourceUser->template,
                'last_sync_at' => now(),
            ]
        );

        $bioMsg = $hasTemplate ? 'beserta data biometrik (sidik jari)' : '(profil & PIN)';

        return response()->json([
            'success' => true,
            'message' => "Perintah duplikasi user PIN {$sourceUser->pin} {$bioMsg} ke {$targetDevName} telah dikirimkan.",
            'response' => $res,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | BATCH USER PUSH (MASS REGISTER TO DEVICES)
    |--------------------------------------------------------------------------
    */

    /**
     * Pre-check local users against target device(s) before batch push.
     */
    public function batchUsersPrecheck(Request $request)
    {
        $request->validate([
            'target_mode' => 'required|in:all,single',
            'cloud_id'    => 'nullable|string',
        ]);

        if ($request->target_mode === 'single') {
            $devices = Device::where('cloud_id', $request->cloud_id)->get();
        } else {
            $devices = Device::all();
        }

        if ($devices->isEmpty()) {
            return response()->json([
                'status'  => false,
                'message' => 'Tidak ada mesin target yang ditemukan. Pastikan perangkat telah didaftarkan.',
            ], 422);
        }

        $targetCloudIds = $devices->pluck('cloud_id')->toArray();
        $targetDevices = $devices->map(function ($dev) {
            return [
                'id'       => $dev->id,
                'name'     => $dev->name,
                'cloud_id' => $dev->cloud_id,
            ];
        })->values();

        // Get all local users (exclude superadmin)
        $users = User::whereDoesntHave('role', function ($q) {
                $q->where(DB::raw('LOWER(TRIM(akses))'), 'superadmin');
            })
            ->select('id', 'name', 'username', 'role_id')
            ->orderBy('id', 'asc')
            ->get();


        $totalUsers = $users->count();

        // Fetch all existing user pins registered across these target devices
        $existingDeviceUsers = FingerspotDeviceUser::whereIn('cloud_id', $targetCloudIds)
            ->get()
            ->groupBy('cloud_id');

        $devicePinsMap = [];
        foreach ($targetCloudIds as $cid) {
            $pins = $existingDeviceUsers->has($cid)
                ? $existingDeviceUsers[$cid]->pluck('pin')->map(fn($p) => (string)$p)->toArray()
                : [];
            $devicePinsMap[$cid] = array_flip($pins);
        }

        $preview = [];
        $userIds = [];
        $existingCount = 0;
        $pendingCount = 0;

        foreach ($users as $u) {
            $pin = (string)$u->id;
            $userIds[] = $u->id;

            // Check how many target devices already have this PIN
            $existingDevices = [];
            $missingDevices = [];

            foreach ($targetDevices as $dev) {
                $cid = $dev['cloud_id'];
                if (isset($devicePinsMap[$cid][$pin])) {
                    $existingDevices[] = $dev['name'];
                } else {
                    $missingDevices[] = $dev['name'];
                }
            }

            $isFullyRegistered = count($missingDevices) === 0;
            if ($isFullyRegistered) {
                $existingCount++;
            } else {
                $pendingCount++;
            }

            // Preview first 100 users for display
            if (count($preview) < 100) {
                $preview[] = [
                    'id'                  => $u->id,
                    'name'                => $u->name,
                    'username'            => $u->username,
                    'is_fully_registered' => $isFullyRegistered,
                    'missing_count'       => count($missingDevices),
                    'existing_count'      => count($existingDevices),
                    'status_label'        => $isFullyRegistered ? 'Sudah Terdaftar' : (count($missingDevices) === count($targetDevices) ? 'Belum Terdaftar' : 'Parsial (' . count($existingDevices) . '/' . count($targetDevices) . ')'),
                    'status_badge'        => $isFullyRegistered ? 'success' : (count($missingDevices) === count($targetDevices) ? 'warning' : 'info'),
                ];
            }
        }

        return response()->json([
            'status'               => true,
            'total_users'          => $totalUsers,
            'total_target_devices' => $devices->count(),
            'target_devices'       => $targetDevices,
            'target_cloud_ids'     => $targetCloudIds,
            'existing_users_count' => $existingCount,
            'pending_users_count'  => $pendingCount,
            'user_ids'             => $userIds,
            'preview'              => $preview,
        ]);
    }

    /**
     * Process a discrete batch of users to prevent PHP timeout.
     */
    public function batchUsersProcess(Request $request)
    {
        $request->validate([
            'target_cloud_ids'   => 'required|array',
            'target_cloud_ids.*' => 'required|string',
            'user_ids'           => 'required|array',
            'user_ids.*'         => 'required|integer',
            'overwrite'          => 'nullable|boolean',
            'privilege'          => 'nullable|integer|in:1,2,3',
        ]);

        @set_time_limit(120);

        $cloudIds = $request->target_cloud_ids;
        $userIds = $request->user_ids;
        $overwrite = (bool)$request->input('overwrite', false);
        $privilege = (int)$request->input('privilege', 1);

        $users = User::whereDoesntHave('role', function ($q) {
                $q->where(DB::raw('LOWER(TRIM(akses))'), 'superadmin');
            })
            ->whereIn('id', $userIds)
            ->get()
            ->keyBy('id');

        // Existing device users cache for fast duplicate check
        $existingDeviceUsers = FingerspotDeviceUser::whereIn('cloud_id', $cloudIds)
            ->whereIn('pin', array_map('strval', $userIds))
            ->get()
            ->groupBy('cloud_id');

        $devicePinsMap = [];
        foreach ($cloudIds as $cid) {
            $pins = $existingDeviceUsers->has($cid)
                ? $existingDeviceUsers[$cid]->pluck('pin')->map(fn($p) => (string)$p)->toArray()
                : [];
            $devicePinsMap[$cid] = array_flip($pins);
        }

        $deviceNames = Device::whereIn('cloud_id', $cloudIds)->pluck('name', 'cloud_id')->toArray();

        $successCount = 0;
        $skippedCount = 0;
        $failedCount = 0;
        $logs = [];

        foreach ($userIds as $userId) {
            $user = $users->get($userId);
            if (!$user) {
                $skippedCount++;
                $logs[] = [
                    'status'   => 'skipped',
                    'pin'      => $userId,
                    'name'     => "User #{$userId}",
                    'cloud_id' => 'all',
                    'message'  => "Dilewati: User tidak ditemukan atau memiliki role Superadmin.",
                ];
                continue;
            }

            $pin = (string)$user->id;
            $cleanName = trim($user->name);

            foreach ($cloudIds as $cloudId) {
                $devName = $deviceNames[$cloudId] ?? $cloudId;
                $alreadyExists = isset($devicePinsMap[$cloudId][$pin]);

                if ($alreadyExists && !$overwrite) {
                    $skippedCount++;
                    $logs[] = [
                        'status'   => 'skipped',
                        'pin'      => $pin,
                        'name'     => $cleanName,
                        'cloud_id' => $cloudId,
                        'device'   => $devName,
                        'message'  => "Dilewati: Sudah terdaftar di {$devName}.",
                    ];
                    continue;
                }

                try {
                    $res = Fingerspot::setUserInfo(
                        null,
                        $cloudId,
                        $pin,
                        $cleanName,
                        $privilege,
                        '',
                        '',
                        ''
                    );

                    $isSuccess = isset($res['success']) ? $res['success'] : true;

                    if ($isSuccess) {
                        // Update or create local cache
                        FingerspotDeviceUser::updateOrCreate(
                            [
                                'cloud_id' => $cloudId,
                                'pin'      => $pin,
                            ],
                            [
                                'name'         => $cleanName,
                                'privilege'    => $privilege,
                                'last_sync_at' => now(),
                            ]
                        );

                        // Mark as existing in current loop map
                        $devicePinsMap[$cloudId][$pin] = true;
                        $successCount++;
                        $logs[] = [
                            'status'   => 'success',
                            'pin'      => $pin,
                            'name'     => $cleanName,
                            'cloud_id' => $cloudId,
                            'device'   => $devName,
                            'message'  => "Berhasil dikirim ke {$devName} (Trans ID: " . ($res['trans_id'] ?? 'OK') . ").",
                        ];
                    } else {
                        $failedCount++;
                        $logs[] = [
                            'status'   => 'failed',
                            'pin'      => $pin,
                            'name'     => $cleanName,
                            'cloud_id' => $cloudId,
                            'device'   => $devName,
                            'message'  => "Gagal di {$devName}: " . ($res['message'] ?? 'API error'),
                        ];
                    }
                } catch (\Throwable $th) {
                    $failedCount++;
                    $logs[] = [
                        'status'   => 'failed',
                        'pin'      => $pin,
                        'name'     => $cleanName,
                        'cloud_id' => $cloudId,
                        'device'   => $devName,
                        'message'  => "Error di {$devName}: " . $th->getMessage(),
                    ];
                }
            }
        }

        return response()->json([
            'status'        => true,
            'processed'     => count($userIds),
            'success_count' => $successCount,
            'skipped_count' => $skippedCount,
            'failed_count'  => $failedCount,
            'logs'          => $logs,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | BATCH FINGERPRINT COPY (TRANSFER BIOMETRICS BETWEEN DEVICES)
    |--------------------------------------------------------------------------
    */

    /**
     * Pre-check users having biometric fingerprints or user accounts on source device before batch copying to target device.
     */
    public function batchCopyFingerprintPrecheck(Request $request)
    {
        $request->validate([
            'source_cloud_id' => 'required|string',
            'target_cloud_id' => 'required|string|different:source_cloud_id',
            'copy_mode'       => 'nullable|in:fingerprint_only,full',
            'pins'            => 'nullable|array',
            'pins.*'          => 'string',
        ]);

        $sourceCloudId = $request->source_cloud_id;
        $targetCloudId = $request->target_cloud_id;
        $copyMode = $request->input('copy_mode', 'fingerprint_only');
        $pinsFilter = $request->pins;

        $sourceDevice = Device::where('cloud_id', $sourceCloudId)->first();
        $targetDevice = Device::where('cloud_id', $targetCloudId)->first();

        if (!$sourceDevice || !$targetDevice) {
            return response()->json([
                'status'  => false,
                'message' => 'Perangkat sumber atau tujuan tidak valid.',
            ], 422);
        }

        // Superadmin PINs to strictly exclude
        $superadminPins = User::whereHas('role', function ($q) {
            $q->where(DB::raw('LOWER(TRIM(akses))'), 'superadmin');
        })->pluck('id')->map(fn($id) => (string)$id)->toArray();

        // Query source users on this machine
        $query = FingerspotDeviceUser::where('cloud_id', $sourceCloudId);

        if (!empty($pinsFilter)) {
            $query->whereIn('pin', array_map('strval', $pinsFilter));
        } else {
            if ($copyMode === 'fingerprint_only') {
                // Find users who have biometric indicator or template
                $query->where(function ($q) {
                    $q->where('finger', '>', 0)
                      ->orWhere('face', '>', 0)
                      ->orWhere('vein', '>', 0)
                      ->orWhere(function($sq) {
                          $sq->whereNotNull('template')->where('template', '!=', '');
                      });
                });
            }
            // In 'full' mode without specific pins, fetch all users registered on source machine
        }

        $sourceUsers = $query->orderBy('pin', 'asc')->get();

        // Target device users for cross-reference
        $targetUsers = FingerspotDeviceUser::where('cloud_id', $targetCloudId)
            ->get()
            ->keyBy('pin');

        // Local users for name resolution fallback
        $localUsers = User::whereIn('id', $sourceUsers->pluck('pin')->toArray())
            ->get()
            ->keyBy('id');

        $usersList = [];
        $readyPins = [];
        $missingTemplatePins = [];
        $totalReady = 0;
        $totalMissing = 0;
        $totalReadyFp = 0;
        $totalReadyUserOnly = 0;
        $totalWarningTemplate = 0;
        $totalSkippedSuperadmin = 0;

        foreach ($sourceUsers as $u) {
            $pinStr = (string)$u->pin;

            if (in_array($pinStr, $superadminPins)) {
                $totalSkippedSuperadmin++;
                continue;
            }

            $hasTemplate = !empty($u->template);
            $fingerCount = (int)$u->finger;
            $userName = $u->name ?: ($localUsers->has($pinStr) ? $localUsers[$pinStr]->name : "User #{$pinStr}");

            $targetExists = $targetUsers->has($pinStr);
            $targetUser = $targetExists ? $targetUsers[$pinStr] : null;
            $targetHasFp = $targetUser && ($targetUser->finger > 0 || !empty($targetUser->template));

            if ($copyMode === 'full') {
                // In full mode, both user account and biometrics are copied
                if ($hasTemplate) {
                    $totalReady++;
                    $totalReadyFp++;
                    $readyPins[] = $pinStr;
                    $statusType = 'ready';
                    $statusBadge = 'success';
                    $statusLabel = 'User & FP Siap';
                    $statusDetail = $targetExists 
                        ? 'Akun & sidik jari siap ditransfer (akan memperbarui data target)' 
                        : 'Akun baru & sidik jari siap didaftarkan lengkap ke target';
                } elseif ($fingerCount > 0) {
                    // Finger registered on machine, but template not cached yet
                    $totalMissing++;
                    $totalWarningTemplate++;
                    $missingTemplatePins[] = $pinStr;
                    $readyPins[] = $pinStr; // still can copy user account
                    $statusType = 'warning_template';
                    $statusBadge = 'warning';
                    $statusLabel = 'User Siap (FP Butuh Refresh)';
                    $statusDetail = "Ada {$fingerCount} sidik jari di mesin, tetapi template belum tersimpan di server. Akun tetap dapat disalin.";
                } else {
                    $totalReady++;
                    $totalReadyUserOnly++;
                    $readyPins[] = $pinStr;
                    $statusType = 'ready_user_only';
                    $statusBadge = 'info';
                    $statusLabel = 'User Saja (Tanpa FP)';
                    $statusDetail = 'Profil akun siap didaftarkan ke mesin tujuan';
                }
            } else {
                // In fingerprint_only mode
                if ($hasTemplate) {
                    $totalReady++;
                    $readyPins[] = $pinStr;
                    $statusType = 'ready';
                    $statusBadge = 'success';
                    $statusLabel = 'Siap Disalin';
                    $statusDetail = $targetExists 
                        ? ($targetHasFp ? 'User ada di target, sidik jari akan diperbarui' : 'User ada di target, siap pasang sidik jari')
                        : 'User belum ada di target (akan didaftarkan otomatis beserta FP)';
                } else {
                    $totalMissing++;
                    $missingTemplatePins[] = $pinStr;
                    $statusType = 'missing_template';
                    $statusBadge = 'warning';
                    $statusLabel = 'Belum Ada Template';
                    $statusDetail = $fingerCount > 0 
                        ? "Ada {$fingerCount} sidik jari di mesin, tetapi template belum tersimpan di server lokal"
                        : "Belum ada template biometrik di server lokal";
                }
            }

            $usersList[] = [
                'pin'             => $pinStr,
                'name'            => $userName,
                'finger'          => $fingerCount,
                'has_template'    => $hasTemplate,
                'target_exists'   => $targetExists,
                'target_has_fp'   => $targetHasFp,
                'status_type'     => $statusType,
                'status_badge'    => $statusBadge,
                'status_label'    => $statusLabel,
                'status_detail'   => $statusDetail,
            ];
        }

        return response()->json([
            'status'                   => true,
            'copy_mode'                => $copyMode,
            'source_device'            => [
                'name'     => $sourceDevice->name,
                'cloud_id' => $sourceDevice->cloud_id,
            ],
            'target_device'            => [
                'name'     => $targetDevice->name,
                'cloud_id' => $targetDevice->cloud_id,
            ],
            'total_source_candidates'  => count($usersList),
            'total_ready'              => $totalReady,
            'total_missing_template'   => $totalMissing,
            'total_ready_fp'           => $totalReadyFp,
            'total_ready_user_only'    => $totalReadyUserOnly,
            'total_warning_template'   => $totalWarningTemplate,
            'total_skipped_superadmin' => $totalSkippedSuperadmin,
            'ready_pins'               => $readyPins,
            'missing_template_pins'    => $missingTemplatePins,
            'users'                    => $usersList,
        ]);
    }

    /**
     * Process discrete batch of fingerprint copies / full user copies from source device to target device.
     */
    public function batchCopyFingerprintProcess(Request $request)
    {
        $request->validate([
            'source_cloud_id' => 'required|string',
            'target_cloud_id' => 'required|string|different:source_cloud_id',
            'copy_mode'       => 'nullable|in:fingerprint_only,full',
            'pins'            => 'required|array|min:1',
            'pins.*'          => 'required|string',
        ]);

        @set_time_limit(120);

        $sourceCloudId = $request->source_cloud_id;
        $targetCloudId = $request->target_cloud_id;
        $copyMode = $request->input('copy_mode', 'fingerprint_only');
        $pins = array_map('strval', $request->pins);

        $sourceDevice = Device::where('cloud_id', $sourceCloudId)->first();
        $targetDevice = Device::where('cloud_id', $targetCloudId)->first();
        $targetDevName = $targetDevice ? $targetDevice->name : $targetCloudId;

        // Superadmin check
        $superadminPins = User::whereHas('role', function ($q) {
            $q->where(DB::raw('LOWER(TRIM(akses))'), 'superadmin');
        })->pluck('id')->map(fn($id) => (string)$id)->toArray();

        // Source device users
        $sourceUsers = FingerspotDeviceUser::where('cloud_id', $sourceCloudId)
            ->whereIn('pin', $pins)
            ->get()
            ->keyBy('pin');

        // Target users map
        $targetUsers = FingerspotDeviceUser::where('cloud_id', $targetCloudId)
            ->whereIn('pin', $pins)
            ->get()
            ->keyBy('pin');

        $localUsers = User::whereIn('id', $pins)->get()->keyBy('id');

        $successCount = 0;
        $skippedCount = 0;
        $failedCount = 0;
        $logs = [];

        foreach ($pins as $pin) {
            if (in_array($pin, $superadminPins)) {
                $skippedCount++;
                $logs[] = [
                    'status'  => 'skipped',
                    'pin'     => $pin,
                    'name'    => "User #{$pin}",
                    'message' => "Dilewati: User terdeteksi sebagai Superadmin (keamanan).",
                ];
                continue;
            }

            $sourceUser = $sourceUsers->get($pin);
            if (!$sourceUser) {
                $skippedCount++;
                $logs[] = [
                    'status'  => 'skipped',
                    'pin'     => $pin,
                    'name'    => "User #{$pin}",
                    'message' => "Dilewati: User tidak ditemukan di mesin sumber.",
                ];
                continue;
            }

            $targetUser = $targetUsers->get($pin);
            $localUser = $localUsers->get($pin);

            // MODE 1: Full Copy (Akun User + Sidik Jari)
            if ($copyMode === 'full') {
                $cleanName = $sourceUser->name ?: ($localUser ? $localUser->name : ($targetUser ? $targetUser->name : "User #{$pin}"));
                $privilege = $sourceUser->privilege ?: 1;
                $password = $sourceUser->password ?? '';
                $rfid = $sourceUser->rfid ?? '';
                $template = $sourceUser->template ?? '';
                $fingerCount = $sourceUser->finger ?: (!empty($template) ? 1 : 0);

                try {
                    $res = Fingerspot::setUserInfo(
                        null,
                        $targetCloudId,
                        $pin,
                        $cleanName,
                        $privilege,
                        $password,
                        $rfid,
                        $template
                    );

                    $isSuccess = isset($res['success']) ? $res['success'] : true;

                    if ($isSuccess) {
                        FingerspotDeviceUser::updateOrCreate(
                            [
                                'cloud_id' => $targetCloudId,
                                'pin'      => (string)$pin,
                            ],
                            [
                                'name'         => $cleanName,
                                'privilege'    => $privilege,
                                'finger'       => $fingerCount,
                                'password'     => $password,
                                'rfid'         => $rfid,
                                'template'     => $template,
                                'last_sync_at' => now(),
                            ]
                        );

                        $bioInfo = !empty($template) ? "Akun & Sidik Jari" : "Akun (tanpa FP)";
                        $successCount++;
                        $logs[] = [
                            'status'  => 'success',
                            'pin'     => $pin,
                            'name'    => $cleanName,
                            'message' => "{$bioInfo} berhasil disalin ke {$targetDevName} (Trans ID: " . ($res['trans_id'] ?? 'OK') . ").",
                        ];
                    } else {
                        $failedCount++;
                        $logs[] = [
                            'status'  => 'failed',
                            'pin'     => $pin,
                            'name'    => $cleanName,
                            'message' => "Gagal di {$targetDevName}: " . ($res['message'] ?? 'API error'),
                        ];
                    }
                } catch (\Throwable $th) {
                    $failedCount++;
                    $logs[] = [
                        'status'  => 'failed',
                        'pin'     => $pin,
                        'name'    => $cleanName,
                        'message' => "Error: " . $th->getMessage(),
                    ];
                }
                continue;
            }

            // MODE 2: Fingerprint Only
            if (empty($sourceUser->template)) {
                $skippedCount++;
                $logs[] = [
                    'status'  => 'skipped',
                    'pin'     => $pin,
                    'name'    => $sourceUser->name ?: "User #{$pin}",
                    'message' => "Dilewati: Template sidik jari tidak ditemukan di database server lokal. Pastikan Mesin Sumber ON dan telah dilakukan Refresh Detail.",
                ];
                continue;
            }

            $cleanName = $targetUser && !empty($targetUser->name)
                ? $targetUser->name
                : ($sourceUser->name ?: ($localUser ? $localUser->name : "User #{$pin}"));

            $privilege = $targetUser ? $targetUser->privilege : $sourceUser->privilege;
            $password = $targetUser ? ($targetUser->password ?? '') : '';
            $rfid = $targetUser ? ($targetUser->rfid ?? '') : '';
            $template = $sourceUser->template;
            $fingerCount = $sourceUser->finger ?: 1;

            try {
                $res = Fingerspot::setUserInfo(
                    null,
                    $targetCloudId,
                    $pin,
                    $cleanName,
                    $privilege,
                    $password,
                    $rfid,
                    $template
                );

                $isSuccess = isset($res['success']) ? $res['success'] : true;

                if ($isSuccess) {
                    FingerspotDeviceUser::updateOrCreate(
                        [
                            'cloud_id' => $targetCloudId,
                            'pin'      => (string)$pin,
                        ],
                        [
                            'name'         => $cleanName,
                            'privilege'    => $privilege,
                            'finger'       => $fingerCount,
                            'password'     => $password,
                            'rfid'         => $rfid,
                            'template'     => $template,
                            'last_sync_at' => now(),
                        ]
                    );

                    $successCount++;
                    $logs[] = [
                        'status'  => 'success',
                        'pin'     => $pin,
                        'name'    => $cleanName,
                        'message' => "Fingerprint berhasil dikirim ke {$targetDevName} (Trans ID: " . ($res['trans_id'] ?? 'OK') . ").",
                    ];
                } else {
                    $failedCount++;
                    $logs[] = [
                        'status'  => 'failed',
                        'pin'     => $pin,
                        'name'    => $cleanName,
                        'message' => "Gagal di {$targetDevName}: " . ($res['message'] ?? 'API error'),
                    ];
                }
            } catch (\Throwable $th) {
                $failedCount++;
                $logs[] = [
                    'status'  => 'failed',
                    'pin'     => $pin,
                    'name'    => $cleanName,
                    'message' => "Error: " . $th->getMessage(),
                ];
            }
        }

        return response()->json([
            'status'        => true,
            'success'       => true,
            'copy_mode'     => $copyMode,
            'processed'     => count($pins),
            'success_count' => $successCount,
            'skipped_count' => $skippedCount,
            'failed_count'  => $failedCount,
            'logs'          => $logs,
        ]);
    }

    /**
     * Batch trigger get_userinfo to pull biometric templates from machine.
     */
    public function batchFetchTemplates(Request $request)
    {
        $request->validate([
            'cloud_id' => 'required|string',
            'pins'     => 'required|array|min:1',
            'pins.*'   => 'required|string',
        ]);

        @set_time_limit(120);

        $cloudId = $request->cloud_id;
        $pins = array_map('strval', $request->pins);

        $successCount = 0;
        $failedCount = 0;
        $logs = [];

        foreach ($pins as $pin) {
            try {
                $res = Fingerspot::getUserInfo(null, $cloudId, $pin);
                if ($res['success'] ?? false) {
                    $successCount++;
                    $logs[] = [
                        'status'  => 'success',
                        'pin'     => $pin,
                        'message' => "Perintah get_userinfo untuk PIN {$pin} terkirim (Trans ID: " . ($res['trans_id'] ?? '-') . ")",
                    ];
                } else {
                    $failedCount++;
                    $logs[] = [
                        'status'  => 'failed',
                        'pin'     => $pin,
                        'message' => "PIN {$pin}: " . ($res['message'] ?? 'Gagal kirim perintah'),
                    ];
                }
            } catch (\Throwable $th) {
                $failedCount++;
                $logs[] = [
                    'status'  => 'failed',
                    'pin'     => $pin,
                    'message' => "PIN {$pin}: " . $th->getMessage(),
                ];
            }
        }

        return response()->json([
            'status'        => true,
            'success'       => true,
            'processed'     => count($pins),
            'success_count' => $successCount,
            'failed_count'  => $failedCount,
            'logs'          => $logs,
            'message'       => "Berhasil mengirim {$successCount} permintaan penarikan template. Hasil akan diperbarui via callback webhook mesin.",
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | COMMANDS & WEBHOOK LOGS
    |--------------------------------------------------------------------------
    */

    public function commandsData(Request $request)
    {
        $query = FingerspotCommand::select('*')->orderBy('id', 'desc');

        if ($request->filled('cloud_id')) {
            $query->where('cloud_id', $request->cloud_id);
        }

        if ($request->filled('command_type')) {
            $query->where('command_type', $request->command_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return DataTables::of($query)
            ->editColumn('command_type', function ($row) {
                $badges = [
                    'get_device'       => 'bg-label-primary',
                    'get_attlog'       => 'bg-label-success',
                    'get_all_pin'      => 'bg-label-info',
                    'get_userinfo'     => 'bg-label-info',
                    'set_userinfo'     => 'bg-label-warning',
                    'delete_userinfo'  => 'bg-label-danger',
                    'set_time'         => 'bg-label-secondary',
                    'reg_online'       => 'bg-label-primary',
                    'restart_device'   => 'bg-label-danger',
                    'attlog_realtime'  => 'bg-label-success',
                ];
                $badgeClass = $badges[$row->command_type] ?? 'bg-label-secondary';
                return '<span class="badge ' . $badgeClass . '">' . e($row->command_type) . '</span>';
            })
            ->editColumn('status', function ($row) {
                if ($row->status === 'success') {
                    return '<span class="badge bg-label-success"><i class="ti ti-check ti-xs me-1"></i>Sukses</span>';
                } elseif ($row->status === 'failed') {
                    return '<span class="badge bg-label-danger"><i class="ti ti-x ti-xs me-1"></i>Gagal</span>';
                }
                return '<span class="badge bg-label-warning"><i class="ti ti-clock ti-xs me-1"></i>Pending</span>';
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d/m/Y H:i:s');
            })
            ->addColumn('action', function ($row) {
                $markBtn = '';
                if ($row->status === 'pending') {
                    $markBtn = '
                        <button type="button" class="btn btn-sm btn-icon btn-label-success rounded-pill btn-mark-cmd-success" data-id="' . $row->id . '" title="Tandai Sukses / Sudah Dihapus di Mesin">
                            <i class="ti ti-check ti-xs"></i>
                        </button>';
                }
                return '
                    <div class="d-inline-flex gap-1">
                        ' . $markBtn . '
                        <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill btn-view-command" data-id="' . $row->id . '" title="Lihat Payload & Respons">
                            <i class="ti ti-eye ti-xs"></i>
                        </button>
                    </div>';
            })
            ->rawColumns(['command_type', 'status', 'created_at', 'action'])
            ->toJson();
    }

    public function commandDetail(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:fingerspot_commands,id',
        ]);

        $command = FingerspotCommand::findOrFail($request->id);

        return response()->json([
            'status' => true,
            'data'   => $command
        ]);
    }

    public function clearCommands(Request $request)
    {
        try {
            $days = $request->input('days', 30);
            if ($days === 'all') {
                FingerspotCommand::truncate();
                $message = 'Semua riwayat log perintah & webhook berhasil dibersihkan.';
            } else {
                FingerspotCommand::where('created_at', '<', now()->subDays((int)$days))->delete();
                $message = "Riwayat log lebih dari {$days} hari berhasil dibersihkan.";
            }

            return response()->json([
                'status'  => true,
                'type'    => 'success',
                'message' => $message,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'type'    => 'error',
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Reconcile & synchronize pending commands that were already accepted by cloud queue.
     */
    public function syncCommandStatus(Request $request)
    {
        try {
            $pendingCommands = FingerspotCommand::where('status', 'pending')->get();
            $updatedCount = 0;

            foreach ($pendingCommands as $cmd) {
                $resp = $cmd->payload_response;
                $isCloudSuccess = is_array($resp) && ($resp['success'] ?? false) === true;

                if ($cmd->command_type === 'delete_userinfo') {
                    // For delete_userinfo, cloud response success:true confirms queue acceptance
                    if ($isCloudSuccess || $cmd->created_at->diffInMinutes(now()) >= 1) {
                        $pin = $cmd->payload_request['pin'] ?? 'User';
                        $cmd->update([
                            'status'  => 'success',
                            'message' => "Perintah hapus PIN {$pin} berhasil diterima antrean cloud & dieksekusi mesin.",
                        ]);
                        $updatedCount++;
                    }
                } elseif (in_array($cmd->command_type, ['set_time', 'restart_device', 'set_userinfo'])) {
                    if ($isCloudSuccess) {
                        $cmd->update([
                            'status'  => 'success',
                            'message' => "Perintah {$cmd->command_type} berhasil diterima antrean cloud & diproses mesin.",
                        ]);
                        $updatedCount++;
                    }
                }
            }

            return response()->json([
                'status'        => true,
                'type'          => 'success',
                'message'       => "Berhasil menyinkronkan status {$updatedCount} perintah yang sebelumnya pending menjadi Sukses.",
                'updated_count' => $updatedCount,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'type'    => 'error',
                'message' => 'Gagal menyinkronkan status: ' . $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Manually mark a pending command as success.
     */
    public function markCommandSuccess(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:fingerspot_commands,id',
        ]);

        try {
            $cmd = FingerspotCommand::findOrFail($request->id);
            $pin = $cmd->payload_request['pin'] ?? null;
            $pinText = $pin ? " PIN {$pin}" : "";

            $cmd->update([
                'status'  => 'success',
                'message' => "Tandai Sukses: Perintah{$pinText} telah dikonfirmasi selesai di mesin fisik.",
            ]);

            return response()->json([
                'status'  => true,
                'type'    => 'success',
                'message' => "Status perintah #{$cmd->id} ({$cmd->trans_id}) berhasil diubah menjadi Sukses.",
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'type'    => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Diagnose device active connectivity (attlog + cloud info).
     */
    public function deviceTestActive(Request $request)
    {
        $request->validate([
            'cloud_id' => 'required|string',
        ]);

        try {
            $cloudId = $request->cloud_id;
            $device = Device::where('cloud_id', $cloudId)->first();
            $devName = $device ? $device->name : $cloudId;

            // 1. Check get_device from cloud API
            $resDev = Fingerspot::getDevice(null, $cloudId);

            // 2. Check today attlog
            $today = date('Y-m-d');
            $resAtt = Fingerspot::getAttLog(null, $cloudId, $today, $today);

            $hasRecentScans = false;
            $scanCount = 0;
            $lastScanTime = null;

            if (isset($resAtt['data']) && is_array($resAtt['data']) && count($resAtt['data']) > 0) {
                $hasRecentScans = true;
                $scanCount = count($resAtt['data']);
                $lastScan = end($resAtt['data']);
                $lastScanTime = $lastScan['scan_date'] ?? null;
            }

            return response()->json([
                'status'            => true,
                'device_name'       => $devName,
                'cloud_id'          => $cloudId,
                'cloud_registered'  => ($resDev['success'] ?? false),
                'has_recent_scans'  => $hasRecentScans,
                'today_scan_count'  => $scanCount,
                'last_scan_time'    => $lastScanTime,
                'device_info'       => $resDev['data'] ?? null,
                'message'           => $hasRecentScans 
                    ? "Mesin {$devName} AKTIF dan terhubung ke cloud (terdapat {$scanCount} scan hari ini, scan terakhir: {$lastScanTime})."
                    : "Mesin {$devName} terdaftar di cloud API, namun belum ada aktivitas scan hari ini. Jika perintah belum dieksekusi, pastikan mesin terhubung ke WiFi/Internet.",
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'type'    => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | API PLAYGROUND & TESTER
    |--------------------------------------------------------------------------
    */

    public function testApi(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string',
            'cloud_id' => 'required|string',
        ]);

        $endpoint = $request->endpoint;
        $cloudId  = $request->cloud_id;
        $transId  = Fingerspot::generateTransId('TEST');

        $startTime = microtime(true);

        switch ($endpoint) {
            case 'get_device':
                $res = Fingerspot::getDevice($transId, $cloudId);
                break;
            case 'get_attlog':
                $start = $request->input('start_date', date('Y-m-d'));
                $end   = $request->input('end_date', date('Y-m-d'));
                $res = Fingerspot::getAttLog($transId, $cloudId, $start, $end);
                break;
            case 'get_all_pin':
                $res = Fingerspot::getAllPin($transId, $cloudId);
                break;
            case 'get_userinfo':
                $pin = $request->input('pin', '1');
                $res = Fingerspot::getUserInfo($transId, $cloudId, $pin);
                break;
            case 'set_userinfo':
                $pin       = $request->input('pin', '999');
                $name      = $request->input('name', 'Test User');
                $privilege = $request->input('privilege', 1);
                $password  = $request->input('password', '');
                $rfid      = $request->input('rfid', '');
                $template  = $request->input('template', '');
                $res = Fingerspot::setUserInfo($transId, $cloudId, $pin, $name, $privilege, $password, $rfid, $template);
                break;
            case 'delete_userinfo':
                $pin = $request->input('pin', '999');
                $res = Fingerspot::deleteUserInfo($transId, $cloudId, $pin);
                break;
            case 'set_time':
                $tz = $request->input('timezone', 'Asia/Jakarta');
                $res = Fingerspot::setTime($transId, $cloudId, $tz);
                break;
            case 'reg_online':
                $pin  = $request->input('pin', '1');
                $mode = (int)$request->input('verification', 0);
                $res = Fingerspot::regOnline($transId, $cloudId, $pin, $mode);
                break;
            case 'restart_device':
                $res = Fingerspot::restartDevice($transId, $cloudId);
                break;
            default:
                return response()->json([
                    'success' => false,
                    'message' => "Endpoint {$endpoint} tidak didukung."
                ], 400);
        }

        $durationMs = round((microtime(true) - $startTime) * 1000);

        return response()->json([
            'success'     => true,
            'endpoint'    => $endpoint,
            'trans_id'    => $transId,
            'duration_ms' => $durationMs,
            'response'    => $res,
        ]);
    }

    public function testConnection()
    {
        $startTime = microtime(true);
        $firstDevice = Device::first();

        if (!$firstDevice) {
            return response()->json([
                'status'  => false,
                'message' => 'Belum ada perangkat terdaftar untuk pengujian koneksi.'
            ], 400);
        }

        $res = Fingerspot::getDevice(null, $firstDevice->cloud_id);
        $durationMs = round((microtime(true) - $startTime) * 1000);

        $isOk = isset($res['success']) && $res['success'] === true;

        return response()->json([
            'status'      => $isOk,
            'latency_ms'  => $durationMs,
            'device'      => $firstDevice->name,
            'cloud_id'    => $firstDevice->cloud_id,
            'message'     => $isOk ? "Koneksi ke Fingerspot Cloud API Berhasil ({$durationMs} ms)" : "Gagal terhubung ke Fingerspot Cloud API",
            'detail'      => $res,
        ]);
    }
}
