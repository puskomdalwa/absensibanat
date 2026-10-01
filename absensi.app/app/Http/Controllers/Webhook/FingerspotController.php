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

        Log::info('[Fingerspot Webhook] Incoming Request', [
            'ip'         => $request->ip(),
            'user_agent' => $request->userAgent(),
            'raw_body'   => $rawContent,
            'parsed'     => $decodedData,
        ]);

        if (!$decodedData || !isset($decodedData['type'])) {
            Log::warning('[Fingerspot Webhook] Invalid payload or missing event type', [
                'ip'       => $request->ip(),
                'raw_body' => $rawContent,
            ]);
            return response()->json([
                'status'  => false,
                'message' => 'Format payload tidak valid atau type tidak ditemukan.'
            ], 400);
        }

        $type = $decodedData['type'];
        $cloudId = $decodedData['cloud_id'] ?? null;
        $transId = $decodedData['trans_id'] ?? null;

        Log::info("[Fingerspot Webhook] Dispatching event: {$type}", [
            'type'     => $type,
            'cloud_id' => $cloudId,
            'trans_id' => $transId,
        ]);

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
                Log::info("[Fingerspot Webhook] Unhandled event type: {$type}", [
                    'type'     => $type,
                    'cloud_id' => $cloudId,
                    'trans_id' => $transId,
                    'data'     => $decodedData,
                ]);
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

            Log::info('[Fingerspot Webhook: attlog] Processing attendance scan record', [
                'cloud_id'    => $cloudId,
                'pin'         => $pin,
                'scan'        => $scan,
                'verify'      => $verifyCode,
                'status_scan' => $statusScan,
            ]);

            if (!$cloudId || !$pin || !$scan) {
                Log::warning('[Fingerspot Webhook: attlog] Missing required parameters', [
                    'cloud_id' => $cloudId,
                    'pin'      => $pin,
                    'scan'     => $scan,
                ]);
                return [
                    'status'  => false,
                    'message' => 'Parameter attlog tidak lengkap (cloud_id, pin, atau scan kosong).'
                ];
            }

            // Process scan record into local Absensi table
            $result = Fingerspot::processScanRecord($cloudId, $pin, $scan, $verifyCode, $statusScan);

            Log::info('[Fingerspot Webhook: attlog] Process scan result', [
                'cloud_id' => $cloudId,
                'pin'      => $pin,
                'scan'     => $scan,
                'result'   => $result,
            ]);

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
            Log::error('[Fingerspot Webhook: attlog] Processing error: ' . $th->getMessage(), [
                'exception' => $th,
                'payload'   => $decodedData,
            ]);
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

        Log::info('[Fingerspot Webhook: get_userinfo] Callback received', [
            'trans_id' => $transId,
            'cloud_id' => $cloudId,
            'data'     => $data,
        ]);

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

            Log::info("[Fingerspot Webhook: get_userinfo] User cached for PIN {$data['pin']}");
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

        Log::info('[Fingerspot Webhook: get_userid_list] Callback received', [
            'trans_id' => $transId,
            'cloud_id' => $cloudId,
            'total'    => $total,
            'pin_count'=> count($pins),
        ]);

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

        Log::info("[Fingerspot Webhook: {$commandType}] Callback received", [
            'trans_id'   => $transId,
            'status'     => $statusCode,
            'is_success' => $isSuccess,
            'message'    => $message,
            'payload'    => $decodedData,
        ]);

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

        Log::info('[Fingerspot Webhook: delete_userinfo] Callback received', [
            'trans_id'   => $transId,
            'cloud_id'   => $cloudId,
            'status'     => $statusCode,
            'is_success' => $isSuccess,
        ]);

        if ($transId) {
            $cmd = FingerspotCommand::where('trans_id', $transId)->first();
            if ($cmd) {
                $pin = $cmd->payload_request['pin'] ?? null;
                if ($isSuccess && $pin && $cloudId) {
                    FingerspotDeviceUser::where('cloud_id', $cloudId)->where('pin', $pin)->delete();
                    Log::info("[Fingerspot Webhook: delete_userinfo] Deleted cached user PIN {$pin} for device {$cloudId}");
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
