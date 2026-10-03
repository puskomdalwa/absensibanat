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

        // Local users list for push to device
        $users = User::select('id', 'name', 'username')->orderBy('name')->limit(500)->get();

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
                return '
                    <div class="d-inline-flex gap-1">
                        <button type="button" class="btn btn-sm btn-icon btn-label-info btn-get-userinfo" data-cloud-id="' . e($row->cloud_id) . '" data-pin="' . e($row->pin) . '" title="Refresh Detail dari Mesin">
                            <i class="ti ti-refresh ti-xs"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-icon btn-label-success btn-reg-online" data-cloud-id="' . e($row->cloud_id) . '" data-pin="' . e($row->pin) . '" data-name="' . e($row->name) . '" title="Registrasi Biometrik Online">
                            <i class="ti ti-fingerprint ti-xs"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-icon btn-label-primary btn-copy-user" data-cloud-id="' . e($row->cloud_id) . '" data-pin="' . e($row->pin) . '" data-name="' . e($row->name) . '" title="Transfer / Copy ke Mesin Lain">
                            <i class="ti ti-copy ti-xs"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-delete-device-user" data-cloud-id="' . e($row->cloud_id) . '" data-pin="' . e($row->pin) . '" data-name="' . e($row->name) . '" title="Hapus dari Mesin">
                            <i class="ti ti-trash ti-xs"></i>
                        </button>
                    </div>';
            })
            ->addColumn('checkbox', function ($row) {
                return '<div class="text-center"><input type="checkbox" class="form-check-input device-user-row-checkbox" data-cloud-id="' . e($row->cloud_id) . '" data-pin="' . e($row->pin) . '" data-name="' . e($row->name) . '"></div>';
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
        ]);

        $res = Fingerspot::deleteUserInfo(null, $request->cloud_id, $request->pin);

        if ($res['success'] ?? false) {
            FingerspotDeviceUser::where('cloud_id', $request->cloud_id)->where('pin', $request->pin)->delete();
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
        ]);

        $sourceUser = FingerspotDeviceUser::where('cloud_id', $request->source_cloud_id)
            ->where('pin', $request->pin)
            ->first();

        if (!$sourceUser) {
            return response()->json([
                'success' => false,
                'message' => 'Data user sumber belum memiliki cache template. Silakan refresh info user dari mesin sumber terlebih dahulu.'
            ], 404);
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

        return response()->json([
            'success' => true,
            'message' => "Perintah duplikasi user PIN {$sourceUser->pin} ke mesin target telah dikirimkan.",
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

        // Get all local users
        $users = User::select('id', 'name', 'username', 'role_id')
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

        $users = User::whereIn('id', $userIds)->get()->keyBy('id');

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
                $failedCount++;
                $logs[] = [
                    'status'   => 'failed',
                    'pin'      => $userId,
                    'name'     => "User #{$userId}",
                    'cloud_id' => 'all',
                    'message'  => "User ID #{$userId} tidak ditemukan di database lokal.",
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
                return '
                    <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill btn-view-command" data-id="' . $row->id . '" title="Lihat Payload & Respons">
                        <i class="ti ti-eye ti-xs"></i>
                    </button>';
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
