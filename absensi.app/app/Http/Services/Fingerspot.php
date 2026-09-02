<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;

class Fingerspot
{
    /**
     * List of registered Cloud IDs.
     *
     * @var array
     */
    public static $cloudId = ["C2642CA867122A34", "C2642CA867330F2C"];

    /**
     * Get attendance log from Fingerspot device.
     *
     * @param  string  $transId   Transaction ID.
     * @param  string  $cloudId   Cloud device ID.
     * @param  string  $startDate Start date (YYYY-MM-DD).
     * @param  string  $endDate   End date (YYYY-MM-DD).
     * @return array   JSON response from Fingerspot API.
     */
    public static function getAttLog($transId, $cloudId, $startDate, $endDate)
    {
        $url = env('FINGERSPOT_URL') . '/get_attlog';
        $token = env('FINGERSPOT_APIKEY');

        $payload = [
            'trans_id'   => $transId,
            'cloud_id'   => $cloudId,
            'start_date' => $startDate,
            'end_date'   => $endDate,
        ];

        $response = Http::withToken($token)->post($url, $payload);

        return $response->json();
    }

    /**
     * Get user information by PIN from Fingerspot device.
     *
     * @param  string  $transId Transaction ID.
     * @param  string  $cloudId Cloud device ID.
     * @param  string  $pin     User PIN.
     * @return array   JSON response from Fingerspot API.
     */
    public static function getUserInfo($transId, $cloudId, $pin)
    {
        $url = env('FINGERSPOT_URL') . '/get_userinfo';
        $token = env('FINGERSPOT_APIKEY');

        $payload = [
            'trans_id' => $transId,
            'cloud_id' => $cloudId,
            'pin'      => $pin,
        ];

        $response = Http::withToken($token)->post($url, $payload);

        return $response->json();
    }

    /**
     * Set user information on the Fingerspot device.
     *
     * @param  string  $transId   Transaction ID.   :required
     * @param  string  $cloudId   Cloud device ID.  :required
     * @param  string  $pin       User PIN.         :required
     * @param  string  $name      User full name.   :required
     * @param  string  $privilege User privilege level (0 = User, 1 = Admin).   :required
     * @param  string  $password  User password.
     * @param  string|null  $rfid     RFID card number.
     * @param  string|null  $template Fingerprint template data.
     * @return array   JSON response from Fingerspot API.
     */
    public static function setUserInfo($transId, $cloudId, $pin, $name, $privilege, $password, $rfid, $template)
    {
        $url = env('FINGERSPOT_URL') . '/set_userinfo';
        $token = env('FINGERSPOT_APIKEY');

        $payload = [
            'trans_id' => $transId,
            'cloud_id' => $cloudId,
            'data' => [
                'pin'       => $pin,
                'name'      => $name,
                'privilege' => $privilege,
                'password'  => $password,
                'rfid'      => $rfid,
                'template'  => $template,
            ],
        ];

        $response = Http::withToken($token)->post($url, $payload);

        return $response->json();
    }

    /**
     * Delete user information based on PIN.
     *
     * @param  string  $transId   Transaction ID.
     * @param  string  $cloudId   Cloud device ID.
     * @param  string  $pin       User PIN.
     * @return array   JSON response from Fingerspot API.
     */
    public static function deleteUserInfo($transId, $cloudId, $pin)
    {
        $url = env('FINGERSPOT_URL') . '/delete_userinfo';
        $token = env('FINGERSPOT_APIKEY');

        $payload = [
            'trans_id' => $transId,
            'cloud_id' => $cloudId,
            'pin'      => $pin,
        ];

        $response = Http::withToken($token)->post($url, $payload);

        return $response->json();
    }

    /**
     * Get all registered user PINs from the Fingerspot device.
     *
     * @param  string  $transId Transaction ID.
     * @param  string  $cloudId Cloud device ID.
     * @return array   JSON response from Fingerspot API.
     */
    public static function getAllPin($transId, $cloudId)
    {
        $url = env('FINGERSPOT_URL') . '/get_all_pin';
        $token = env('FINGERSPOT_APIKEY');

        $payload = [
            'trans_id' => $transId,
            'cloud_id' => $cloudId,
        ];

        $response = Http::withToken($token)->post($url, $payload);

        return $response->json();
    }

    /**
     * Set device timezone.
     *
     * @param  string  $transId Transaction ID.
     * @param  string  $cloudId Cloud device ID.
     * @param  string  $timezone Valid timezone (e.g., "Asia/Jakarta").
     * @return array   JSON response from Fingerspot API.
     */
    public static function setTime($transId, $cloudId, $timezone)
    {
        $url = env('FINGERSPOT_URL') . '/set_time';
        $token = env('FINGERSPOT_APIKEY');

        $payload = [
            'trans_id' => $transId,
            'cloud_id' => $cloudId,
            'timezone' => $timezone,
        ];

        $response = Http::withToken($token)->post($url, $payload);

        return $response->json();
    }

    /**
     * Register user fingerprint online (start online capture).
     *
     * @param  string  $transId      Transaction ID.
     * @param  string  $cloudId      Cloud device ID.
     * @param  string  $pin          User PIN.
     * @param  string  $verification Verification mode (0 or 1).
     * @return array   JSON response from Fingerspot API.
     */
    public static function regOnline($transId, $cloudId, $pin, $verification)
    {
        $url = env('FINGERSPOT_URL') . '/reg_online';
        $token = env('FINGERSPOT_APIKEY');

        $payload = [
            'trans_id'     => $transId,
            'cloud_id'     => $cloudId,
            'pin'          => $pin,
            'verification' => $verification,
        ];

        $response = Http::withToken($token)->post($url, $payload);

        return $response->json();
    }

    /**
     * Restart the Fingerspot device remotely.
     *
     * @param  string  $transId Transaction ID.
     * @param  string  $cloudId Cloud device ID.
     * @return array   JSON response from Fingerspot API.
     */
    public static function restartDevice($transId, $cloudId)
    {
        $url = env('FINGERSPOT_URL') . '/restart_device';
        $token = env('FINGERSPOT_APIKEY');

        $payload = [
            'trans_id' => $transId,
            'cloud_id' => $cloudId,
        ];

        $response = Http::withToken($token)->post($url, $payload);

        return $response->json();
    }

    /**
     * Get device information.
     *
     * @param  string  $transId Transaction ID.
     * @param  string  $cloudId Cloud device ID.
     * @return array   JSON response from Fingerspot API.
     */
    public static function getDevice($transId, $cloudId)
    {
        $url = env('FINGERSPOT_URL') . '/get_device';
        $token = env('FINGERSPOT_APIKEY');

        $payload = [
            'trans_id' => $transId,
            'cloud_id' => $cloudId,
        ];

        $response = Http::withToken($token)->post($url, $payload);

        return $response->json();
    }
}
