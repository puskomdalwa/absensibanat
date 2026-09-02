<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Webhook\FingerspotController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Services\Fingerspot;
use App\Models\User;
use Illuminate\Support\Facades\File;

class TestingController extends Controller
{
    public function index()
    {
        User::where('role_id', 2)->update([
            'password' => bcrypt('123456')
        ]);
        // $fingerspotController = new FingerspotController();
        // $decodedData = [
        //     'type' => 'attlog',
        //     'cloud_id' => 'C2642CA867122A34',
        //     'data' => [
        //         'pin' => '2',
        //         'scan' => '2025-11-21 15:37:42',
        //         'verify' => 1,
        //         'status_scan' => 0,
        //     ],
        // ];

        // dd($fingerspotController->realtimeAttLog($decodedData));
        // dd(Carbon::parse('2025-11-21')->format('Y-m-d'));

        // $cloudId = Fingerspot::$cloudId[0];
        // // $tes = Fingerspot::getAttLog(1, $cloudId, '2025-11-20', '2025-11-21');
        // // $tes = Fingerspot::getAllPin(1,$cloudId );
        // // $tes = Fingerspot::getUserInfo(1, $cloudId, 1);
        // // $tes = Fingerspot::setUserInfo(1, $cloudId, 3, 'namabaru ku @', 1, '', '', '');
        // // $tes = Fingerspot::deleteUserInfo(1, $cloudId, 3);
        // $tes = Fingerspot::regOnline(1, $cloudId, 2, 5);
        // // $tes = Fingerspot::restartDevice(1, $cloudId);
        // // $tes = Fingerspot::getDevice(1, $cloudId);
        // return response()->json($tes);
        return redirect()->route('root.index');
    }

    public function readLog()
    {
        $logPath = storage_path('logs/laravel.log');

        // Jika file tidak ditemukan
        if (!File::exists($logPath)) {
            return response()->json([
                'message' => 'Log file not found',
            ], 404);
        }

        // Ambil isi file log
        $content = File::get($logPath);

        return response('<pre>' . $content . '</pre>');
    }
}
