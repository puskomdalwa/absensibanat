<?php

namespace App\Http\Services;

use Carbon\Carbon;
use App\Models\Device;
use App\Models\Verify;
use App\Models\Absensi;
use App\Models\User;
use App\Models\FingerspotCommand;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Fingerspot
{
    /**
     * Default registered Cloud IDs fallback.
     *
     * @var array
     */
    public static $cloudId = ["C2642CA867122A34", "C2642CA867330F2C"];

    /**
     * Get registered Cloud IDs from database or fallback.
     *
     * @return array
     */
    public static function getCloudIds()
    {
        $dbIds = Device::pluck('cloud_id')->toArray();
        return !empty($dbIds) ? $dbIds : self::$cloudId;
    }

    /**
     * Generate unique transaction ID.
     *
     * @param  string  $prefix
     * @return string
     */
    public static function generateTransId($prefix = 'CMD')
    {
        return $prefix . '-' . date('YmdHis') . '-' . strtoupper(Str::random(4));
    }

    /**
     * Send HTTP POST to Fingerspot API and log transaction.
     *
     * @param  string  $endpoint
     * @param  array   $payload
     * @param  string  $commandType
     * @return array
     */
    public static function sendRequest($endpoint, array $payload, $commandType = 'custom')
    {
        $baseUrl = rtrim(env('FINGERSPOT_URL', 'https://developer.fingerspot.io/api'), '/');
        $url     = $baseUrl . '/' . ltrim($endpoint, '/');
        $token   = env('FINGERSPOT_APIKEY', '3R5XAP1OFW3T22TV');

        $transId = $payload['trans_id'] ?? null;
        $cloudId = $payload['cloud_id'] ?? null;

        $device = $cloudId ? Device::where('cloud_id', $cloudId)->first() : null;

        $commandRecord = FingerspotCommand::create([
            'trans_id'        => $transId,
            'cloud_id'        => $cloudId,
            'device_name'     => $device ? $device->name : null,
            'command_type'    => $commandType,
            'payload_request' => $payload,
            'status'          => 'pending',
            'created_at'      => now(),
        ]);

        try {
            $response = Http::withToken($token)
                ->timeout(20)
                ->withHeaders([
                    'Accept' => 'application/json',
                ])
                ->post($url, $payload);

            $responseBody = $response->json();

            // If response is not valid JSON
            if ($responseBody === null) {
                $responseBody = [
                    'success' => false,
                    'status_code' => $response->status(),
                    'raw_body' => $response->body()
                ];
            }

            $isSuccess = ($response->successful() && (!isset($responseBody['success']) || $responseBody['success'] === true));

            // For synchronous commands (get_device, get_attlog), immediate response is final
            if (in_array($commandType, ['get_device', 'get_attlog'])) {
                $commandRecord->update([
                    'payload_response' => $responseBody,
                    'status'           => $isSuccess ? 'success' : 'failed',
                    'message'          => $isSuccess ? 'Berhasil dieksekusi secara sinkron' : ($responseBody['message'] ?? 'API error'),
                ]);
            } elseif ($commandType === 'delete_userinfo') {
                // delete_userinfo: Fingerspot Cloud immediately queues the deletion task to the device.
                // The cloud does not emit separate webhook callbacks for delete, so cloud acknowledgment is considered successful.
                $commandRecord->update([
                    'payload_response' => $responseBody,
                    'status'           => $isSuccess ? 'success' : 'failed',
                    'message'          => $isSuccess ? 'Perintah berhasil diterima antrean cloud & dikirim ke mesin' : ($responseBody['message'] ?? 'API error'),
                ]);
            } elseif (in_array($commandType, ['set_time', 'restart_device'])) {
                $commandRecord->update([
                    'payload_response' => $responseBody,
                    'status'           => $isSuccess ? 'success' : 'failed',
                    'message'          => $isSuccess ? 'Perintah berhasil diterima cloud & dikirim ke mesin' : ($responseBody['message'] ?? 'API error'),
                ]);
            } else {
                // Asynchronous commands waiting for payload data callback (get_all_pin, get_userinfo, reg_online, set_userinfo)
                $commandRecord->update([
                    'payload_response' => $responseBody,
                    'status'           => $isSuccess ? 'pending' : 'failed',
                    'message'          => $isSuccess ? 'Perintah diterima perangkat (menunggu callback webhook)' : ($responseBody['message'] ?? 'API error'),
                ]);
            }

            return $responseBody;
        } catch (\Throwable $th) {
            $commandRecord->update([
                'status'  => 'failed',
                'message' => 'Exception: ' . $th->getMessage(),
            ]);

            return [
                'success' => false,
                'trans_id' => $transId,
                'message' => $th->getMessage(),
            ];
        }
    }

    /**
     * Get attendance log from Fingerspot device (Synchronous).
     *
     * @param  string|null  $transId   Transaction ID.
     * @param  string       $cloudId   Cloud device ID.
     * @param  string       $startDate Start date (YYYY-MM-DD).
     * @param  string       $endDate   End date (YYYY-MM-DD).
     * @return array
     */
    public static function getAttLog($transId, $cloudId, $startDate, $endDate)
    {
        $transId = $transId ?: self::generateTransId('ATTLOG');
        $payload = [
            'trans_id'   => (string)$transId,
            'cloud_id'   => (string)$cloudId,
            'start_date' => $startDate,
            'end_date'   => $endDate,
        ];

        return self::sendRequest('get_attlog', $payload, 'get_attlog');
    }

    /**
     * Get user information by PIN from Fingerspot device (Asynchronous).
     *
     * @param  string|null  $transId Transaction ID.
     * @param  string       $cloudId Cloud device ID.
     * @param  string       $pin     User PIN.
     * @return array
     */
    public static function getUserInfo($transId, $cloudId, $pin)
    {
        $transId = $transId ?: self::generateTransId('GETUSER');
        $payload = [
            'trans_id' => (string)$transId,
            'cloud_id' => (string)$cloudId,
            'pin'      => (string)$pin,
        ];

        return self::sendRequest('get_userinfo', $payload, 'get_userinfo');
    }

    /**
     * Set user information on the Fingerspot device (Asynchronous).
     *
     * @param  string|null  $transId
     * @param  string       $cloudId
     * @param  string       $pin
     * @param  string       $name
     * @param  int|string   $privilege (1 = User, 2 = Admin, 3 = Subadmin)
     * @param  string|null  $password
     * @param  string|null  $rfid
     * @param  string|null  $template
     * @return array
     */
    public static function setUserInfo($transId, $cloudId, $pin, $name, $privilege = 1, $password = '', $rfid = '', $template = '')
    {
        $transId = $transId ?: self::generateTransId('SETUSER');
        $payload = [
            'trans_id' => (string)$transId,
            'cloud_id' => (string)$cloudId,
            'data'     => [
                'pin'       => (string)$pin,
                'name'      => (string)$name,
                'privilege' => (string)$privilege,
                'password'  => (string)($password ?? ''),
                'rfid'      => (string)($rfid ?? ''),
                'template'  => (string)($template ?? ''),
            ],
        ];

        return self::sendRequest('set_userinfo', $payload, 'set_userinfo');
    }

    /**
     * Delete user information based on PIN (Asynchronous).
     *
     * @param  string|null  $transId
     * @param  string       $cloudId
     * @param  string       $pin
     * @return array
     */
    public static function deleteUserInfo($transId, $cloudId, $pin)
    {
        $transId = $transId ?: self::generateTransId('DELUSER');
        $payload = [
            'trans_id' => (string)$transId,
            'cloud_id' => (string)$cloudId,
            'pin'      => (string)$pin,
        ];

        return self::sendRequest('delete_userinfo', $payload, 'delete_userinfo');
    }

    /**
     * Get all registered user PINs from the Fingerspot device (Asynchronous).
     *
     * @param  string|null  $transId
     * @param  string       $cloudId
     * @return array
     */
    public static function getAllPin($transId, $cloudId)
    {
        $transId = $transId ?: self::generateTransId('GETALLPIN');
        $payload = [
            'trans_id' => (string)$transId,
            'cloud_id' => (string)$cloudId,
        ];

        return self::sendRequest('get_all_pin', $payload, 'get_all_pin');
    }

    /**
     * Set device timezone (Asynchronous).
     *
     * @param  string|null  $transId
     * @param  string       $cloudId
     * @param  string       $timezone (e.g., "Asia/Jakarta")
     * @return array
     */
    public static function setTime($transId, $cloudId, $timezone = 'Asia/Jakarta')
    {
        $transId = $transId ?: self::generateTransId('SETTIME');
        $payload = [
            'trans_id' => (string)$transId,
            'cloud_id' => (string)$cloudId,
            'timezone' => (string)$timezone,
        ];

        return self::sendRequest('set_time', $payload, 'set_time');
    }

    /**
     * Register user biometric online (Asynchronous).
     * Verification modes: 0-9 = fingerprint (0=index finger), 12 = face, 13 = vein
     *
     * @param  string|null  $transId
     * @param  string       $cloudId
     * @param  string       $pin
     * @param  int          $verification
     * @return array
     */
    public static function regOnline($transId, $cloudId, $pin, $verification = 0)
    {
        $transId = $transId ?: self::generateTransId('REGONLINE');
        $payload = [
            'trans_id'     => (string)$transId,
            'cloud_id'     => (string)$cloudId,
            'pin'          => (string)$pin,
            'verification' => (int)$verification,
        ];

        return self::sendRequest('reg_online', $payload, 'reg_online');
    }

    /**
     * Restart the Fingerspot device remotely (Asynchronous).
     *
     * @param  string|null  $transId
     * @param  string       $cloudId
     * @return array
     */
    public static function restartDevice($transId, $cloudId)
    {
        $transId = $transId ?: self::generateTransId('RESTART');
        $payload = [
            'trans_id' => (string)$transId,
            'cloud_id' => (string)$cloudId,
        ];

        return self::sendRequest('restart_device', $payload, 'restart_device');
    }

    /**
     * Get device information (Synchronous).
     *
     * @param  string|null  $transId
     * @param  string       $cloudId
     * @return array
     */
    public static function getDevice($transId, $cloudId)
    {
        $transId = $transId ?: self::generateTransId('GETDEV');
        $payload = [
            'trans_id' => (string)$transId,
            'cloud_id' => (string)$cloudId,
        ];

        return self::sendRequest('get_device', $payload, 'get_device');
    }

    /**
     * Process an individual attendance scan record into the local Absensi table.
     *
     * @param  string       $cloudId
     * @param  string       $pin
     * @param  string       $scanDate (e.g., '2026-09-30 14:15:00')
     * @param  int|string   $verifyCode
     * @param  int|string   $statusScan
     * @return array
     */
    public static function processScanRecord($cloudId, $pin, $scanDate, $verifyCode = 1, $statusScan = 0)
    {
        try {
            $scanTime = Carbon::parse($scanDate);
            $tanggal  = $scanTime->format('Y-m-d');
            $waktu    = $scanTime->format('H:i');

            $device = Device::where('cloud_id', $cloudId)->first();
            $verify = Verify::find($verifyCode) ?? Verify::where('name', 'finger')->first();

            // Check if user exists in local database
            $user = User::find($pin);
            if (!$user) {
                // If user doesn't exist by ID, try find by username or kode
                $user = User::where('username', $pin)->orWhere('kode', $pin)->first();
            }

            // Find existing attendance for that user and date
            $absensi = Absensi::where('users_id', $user ? $user->id : $pin)
                ->where('tgl_absen', $tanggal)
                ->first();

            if (!$absensi) {
                // First scan of the day: create record with pagi (morning/in)
                $absensi = new Absensi();
                $absensi->users_id  = $user ? $user->id : $pin;
                $absensi->tgl_absen = $tanggal;
                $absensi->latitude  = $device ? $device->name : 'Fingerspot';
                $absensi->longitude = $device ? $device->name : 'Fingerspot';
                $absensi->verify_id = $verify ? $verify->id : 1;
                $absensi->device_id = $device ? $device->id : null;
                $absensi->pagi      = $waktu;

                RealtimeAbsensi::isiKategori($absensi, $scanTime);
                $absensi->save();

                return [
                    'status' => 'created',
                    'action' => 'datang',
                    'pin'    => $pin,
                    'absensi_id' => $absensi->id,
                ];
            }

            // Existing record: check if pagi is empty
            $jamDatang = $absensi->getRawOriginal('pagi');
            if (!$jamDatang) {
                $absensi->pagi = $waktu;
                RealtimeAbsensi::isiKategori($absensi, $scanTime);
                $absensi->save();

                return [
                    'status' => 'updated',
                    'action' => 'datang',
                    'pin'    => $pin,
                    'absensi_id' => $absensi->id,
                ];
            }

            // Check minimum duration (2 hours) for pulang scan
            if (!RealtimeAbsensi::memenuhiDurasiMinimumPulang($tanggal, $jamDatang, $waktu)) {
                return [
                    'status' => 'skipped',
                    'reason' => 'Belum memenuhi durasi minimum 2 jam dari jam datang (' . $jamDatang . ')',
                    'pin'    => $pin,
                    'absensi_id' => $absensi->id,
                ];
            }

            // Update scan pulang
            $absensi->sore = $waktu;
            RealtimeAbsensi::isiKategori($absensi, $scanTime);
            $absensi->save();

            return [
                'status' => 'updated',
                'action' => 'pulang',
                'pin'    => $pin,
                'absensi_id' => $absensi->id,
            ];
        } catch (\Throwable $th) {
            return [
                'status' => 'error',
                'pin'    => $pin,
                'message' => $th->getMessage(),
            ];
        }
    }

    /**
     * Batch sync attendance log list into local Absensi table.
     *
     * @param  string  $cloudId
     * @param  array   $logs
     * @return array
     */
    public static function syncAttLogs($cloudId, array $logs)
    {
        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors  = [];

        // Sort logs by scan date ascending so that earlier scans are processed as pagi first
        usort($logs, function ($a, $b) {
            $timeA = isset($a['scan_date']) ? strtotime($a['scan_date']) : (isset($a['scan']) ? strtotime($a['scan']) : 0);
            $timeB = isset($b['scan_date']) ? strtotime($b['scan_date']) : (isset($b['scan']) ? strtotime($b['scan']) : 0);
            return $timeA <=> $timeB;
        });

        foreach ($logs as $log) {
            $pin        = $log['pin'] ?? null;
            $scanDate   = $log['scan_date'] ?? $log['scan'] ?? null;
            $verifyCode = $log['verify'] ?? 1;
            $statusScan = $log['status_scan'] ?? 0;

            if (!$pin || !$scanDate) {
                continue;
            }

            $res = self::processScanRecord($cloudId, $pin, $scanDate, $verifyCode, $statusScan);

            if ($res['status'] === 'created') {
                $created++;
            } elseif ($res['status'] === 'updated') {
                $updated++;
            } elseif ($res['status'] === 'skipped') {
                $skipped++;
            } elseif ($res['status'] === 'error') {
                $errors[] = "PIN {$pin} ({$scanDate}): " . $res['message'];
            }
        }

        return [
            'total'   => count($logs),
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors'  => $errors,
        ];
    }
}
