<?php

namespace App\Http\Controllers\Webhook;

use Carbon\Carbon;
use App\Models\Verify;
use App\Models\Absensi;
use App\Models\Device;
use App\Models\FingerspotCommand;
use App\Models\FingerspotDeviceUser;
use App\Http\Services\Fingerspot;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class FingerspotController extends Controller
{
    /**
     * Handle incoming webhook requests from Fingerspot Cloud platform.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $rawContent = file_get_contents('php://input');
        $decodedData = json_decode($rawContent, true);

        if (!$decodedData || !isset($decodedData['type'])) {
            return response()->json([
                'status'  => false,
                'message' => 'Format payload tidak valid atau type tidak ditemukan.'
            ], 400);
        }

        $type = $decodedData['type'];
        $cloudId = $decodedData['cloud_id'] ?? null;
        $transId = $decodedData['trans_id'] ?? null;

        // Process based on event type
        switch ($type) {
            case 'attlog':
                return response()->json($this->handleAttLog($decodedData));

            case 'get_userinfo':
                return response()->json($this->handleGetUserInfo($decodedData));

            case 'get_userid_list':
                return response()->json($this->handleGetUserIdList($decodedData));

            case 'set_userinfo':
                return response()->json($this->handleCommandCallback($decodedData, 'set_userinfo'));

            case 'delete_userinfo':
                return response()->json($this->handleDeleteUserInfoCallback($decodedData));

            case 'set_time':
                return response()->json($this->handleCommandCallback($decodedData, 'set_time'));

            case 'register_online':
                return response()->json($this->handleCommandCallback($decodedData, 'register_online'));

            case 'restart_device':
                return response()->json($this->handleCommandCallback($decodedData, 'restart_device'));

            default:
                // Log unknown event
                if ($transId) {
                    FingerspotCommand::where('trans_id', $transId)->update([
                        'callback_payload' => $decodedData,
                        'status'           => 'success',
                        'message'          => "Menerima event callback: {$type}",
                    ]);
                }
                return response()->json([
                    'status'  => true,
                    'message' => "Event {$type} diterima.",
                    'data'    => $decodedData
                ]);
        }
    }

    /**
     * Backward-compatible alias for existing code
     */
    public function realtimeAttLog($decodedData)
    {
        return $this->handleAttLog($decodedData);
    }

    /**
     * Handle real-time push attendance log (spontaneous).
     */
    protected function handleAttLog(array $decodedData)
    {
        try {
            $cloudId = $decodedData['cloud_id'] ?? null;
            $data    = $decodedData['data'] ?? [];

            $pin        = $data['pin'] ?? null;
            $scan       = $data['scan'] ?? null;
            $verifyCode = $data['verify'] ?? 1;
            $statusScan = $data['status_scan'] ?? 0;

            if (!$cloudId || !$pin || !$scan) {
                return [
                    'status'  => false,
                    'message' => 'Parameter attlog tidak lengkap (cloud_id, pin, atau scan kosong).'
                ];
            }

            // Process scan record into local Absensi table
            $result = Fingerspot::processScanRecord($cloudId, $pin, $scan, $verifyCode, $statusScan);

            // Log real-time attlog event
            $device = Device::where('cloud_id', $cloudId)->first();
            FingerspotCommand::create([
                'trans_id'         => null,
                'cloud_id'         => $cloudId,
                'device_name'      => $device ? $device->name : null,
                'command_type'     => 'attlog_realtime',
                'callback_payload' => $decodedData,
                'status'           => ($result['status'] === 'error') ? 'failed' : 'success',
                'message'          => "Scan PIN {$pin} ({$result['status']}: " . ($result['action'] ?? $result['reason'] ?? 'ok') . ")",
            ]);

            return [
                'status'  => ($result['status'] !== 'error'),
                'message' => 'Attlog berhasil diproses: ' . ($result['action'] ?? $result['reason'] ?? 'sukses'),
                'data'    => $result,
            ];
        } catch (\Throwable $th) {
            Log::error('Fingerspot Webhook AttLog Error: ' . $th->getMessage());
            return [
                'status'  => false,
                'message' => $th->getMessage()
            ];
        }
    }

    /**
     * Handle asynchronous callback for get_userinfo.
     */
    protected function handleGetUserInfo(array $decodedData)
    {
        $transId = $decodedData['trans_id'] ?? null;
        $cloudId = $decodedData['cloud_id'] ?? null;
        $data    = $decodedData['data'] ?? [];

        if (!empty($data['pin']) && $cloudId) {
            // Update or create cached device user
            FingerspotDeviceUser::updateOrCreate(
                [
                    'cloud_id' => $cloudId,
                    'pin'      => (string)$data['pin'],
                ],
                [
                    'name'         => $data['name'] ?? null,
                    'privilege'    => (int)($data['privilege'] ?? 1),
                    'finger'       => (int)($data['finger'] ?? 0),
                    'face'         => (int)($data['face'] ?? 0),
                    'vein'         => (int)($data['vein'] ?? 0),
                    'password'     => $data['password'] ?? null,
                    'rfid'         => $data['rfid'] ?? null,
                    'template'     => $data['template'] ?? null,
                    'last_sync_at' => now(),
                ]
            );
        }

        if ($transId) {
            FingerspotCommand::where('trans_id', $transId)->update([
                'callback_payload' => $decodedData,
                'status'           => 'success',
                'message'          => 'Informasi user PIN ' . ($data['pin'] ?? '') . ' berhasil diterima dari mesin.',
            ]);
        }

        return [
            'status'  => true,
            'message' => 'Userinfo callback berhasil disimpan.',
            'data'    => $data,
        ];
    }

    /**
     * Handle asynchronous callback for get_userid_list.
     */
    protected function handleGetUserIdList(array $decodedData)
    {
        $transId = $decodedData['trans_id'] ?? null;
        $cloudId = $decodedData['cloud_id'] ?? null;
        $data    = $decodedData['data'] ?? [];
        $pins    = $data['pin_arr'] ?? [];
        $total   = $data['total'] ?? count($pins);

        // Populate placeholders in device users table if they don't exist yet
        if ($cloudId && is_array($pins)) {
            foreach ($pins as $pin) {
                FingerspotDeviceUser::firstOrCreate(
                    [
                        'cloud_id' => $cloudId,
                        'pin'      => (string)$pin,
                    ],
                    [
                        'last_sync_at' => now(),
                    ]
                );
            }
        }

        if ($transId) {
            FingerspotCommand::where('trans_id', $transId)->update([
                'callback_payload' => $decodedData,
                'status'           => 'success',
                'message'          => "Daftar PIN berhasil diterima ({$total} user).",
            ]);
        }

        return [
            'status'  => true,
            'message' => 'Daftar user PIN berhasil diproses.',
            'total'   => $total,
        ];
    }

    /**
     * Handle generic command callback (set_userinfo, set_time, register_online, restart_device).
     * Format: { "type": "...", "cloud_id": "...", "trans_id": "...", "status": "1" }
     * Status 1 = sukses, 2 = gagal
     */
    protected function handleCommandCallback(array $decodedData, $commandType)
    {
        $transId    = $decodedData['trans_id'] ?? null;
        $statusCode = (string)($decodedData['status'] ?? '1');
        $isSuccess  = ($statusCode === '1');

        $message = $isSuccess ? "Perintah {$commandType} berhasil dieksekusi oleh mesin." : "Perintah {$commandType} gagal dieksekusi oleh mesin.";

        if ($transId) {
            FingerspotCommand::where('trans_id', $transId)->update([
                'callback_payload' => $decodedData,
                'status'           => $isSuccess ? 'success' : 'failed',
                'status_code'      => $statusCode,
                'message'          => $message,
            ]);
        }

        return [
            'status'  => $isSuccess,
            'message' => $message,
        ];
    }

    /**
     * Handle delete_userinfo callback.
     */
    protected function handleDeleteUserInfoCallback(array $decodedData)
    {
        $transId    = $decodedData['trans_id'] ?? null;
        $cloudId    = $decodedData['cloud_id'] ?? null;
        $statusCode = (string)($decodedData['status'] ?? '1');
        $isSuccess  = ($statusCode === '1');

        if ($transId) {
            $cmd = FingerspotCommand::where('trans_id', $transId)->first();
            if ($cmd) {
                $pin = $cmd->payload_request['pin'] ?? null;
                if ($isSuccess && $pin && $cloudId) {
                    FingerspotDeviceUser::where('cloud_id', $cloudId)->where('pin', $pin)->delete();
                }

                $cmd->update([
                    'callback_payload' => $decodedData,
                    'status'           => $isSuccess ? 'success' : 'failed',
                    'status_code'      => $statusCode,
                    'message'          => $isSuccess ? "Pengguna PIN {$pin} berhasil dihapus dari mesin." : "Gagal menghapus pengguna dari mesin.",
                ]);
            }
        }

        return [
            'status'  => $isSuccess,
            'message' => $isSuccess ? 'Pengguna berhasil dihapus di mesin.' : 'Gagal menghapus pengguna di mesin.',
        ];
    }
}
