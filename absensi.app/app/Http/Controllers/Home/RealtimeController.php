<?php

namespace App\Http\Controllers\Home;

use App\Models\User;
use App\Models\Absensi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\Device;
use Yajra\DataTables\Facades\DataTables;

class RealtimeController extends Controller
{
    public function index() {
        $departemen = Departemen::all();
        $device = Device::all();
        return view('home.realtime.index', compact('departemen', 'device'));
    }

    public function data(User $user, Request $request)
    {
        $search = request('search.value');
        $data   = Absensi::join('users', 'users.id', '=', 'absensi.users_id')
            ->join('departemen', 'departemen.id', '=', 'users.departemen_id')
            ->join('device', 'device.id', '=', 'absensi.device_id')
            ->select('absensi.*', 'users.name as user_name', 'departemen.nama as departemen_nama', 'device.name as device_name');
        return DataTables::of($data)
            ->filter(function ($query) use ($search, $request) {
                $query->where(function ($query) use ($search) {
                    $query->orWhere('latitude', 'LIKE', "%$search%");
                    $query->orWhere('users.name', 'LIKE', "%$search%");
                    $query->orWhere('tgl_absen', 'LIKE', "%$search%");
                    $query->orWhere('pagi', 'LIKE', "%$search%");
                    $query->orWhere('keterangan', 'LIKE', "%$search%");
                    $query->orWhere('departemen.nama', 'LIKE', "%$search%");
                    $query->orWhere('device.name', 'LIKE', "%$search%");
                });
                if ($request->startDate && $request->endDate) {
                    $query->whereBetween('tgl_absen', [$request->startDate, $request->endDate]);
                } elseif ($request->startDate) {
                    $query->where('tgl_absen', '>=', $request->startDate);
                } elseif ($request->endDate) {
                    $query->where('tgl_absen', '<=', $request->endDate);
                }
                $query->when($request->departemenId, function ($q, $departemenId) {
                    $q->where('users.departemen_id', $departemenId);
                });
                $query->when($request->deviceId, function ($q, $deviceId) {
                    $q->where('absensi.device_id', $deviceId);
                });
            })
            ->editColumn('pagi', function($row) {
                $time = explode(':', $row->pagi);
                return $time[0].':'.$time[1];
            })
            ->toJson();
    }
    
    public function indexToday()
{
    return view('home.realtime.today');
}

public function dataToday(Request $request)
{
    $search = request('search.value');

    $data = Absensi::join('users', 'users.id', '=', 'absensi.users_id')
        ->join('departemen', 'departemen.id', '=', 'users.departemen_id')
        ->join('device', 'device.id', '=', 'absensi.device_id')
        ->select(
            'absensi.*',
            'users.name as user_name',
            'departemen.nama as departemen_nama',
            'device.name as device_name'
        )
        ->whereDate('absensi.tgl_absen', today()); // 🔥 HANYA HARI INI

    return DataTables::of($data)
        ->filter(function ($query) use ($search) {

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->orWhere('users.name', 'LIKE', "%$search%");
                    $q->orWhere('tgl_absen', 'LIKE', "%$search%");
                    $q->orWhere('pagi', 'LIKE', "%$search%");
                    $q->orWhere('departemen.nama', 'LIKE', "%$search%");
                    $q->orWhere('device.name', 'LIKE', "%$search%");
                });
            }

        })
        ->editColumn('pagi', function ($row) {
            if (!$row->pagi) return '-';
            $t = explode(':', $row->pagi);
            return $t[0] . ':' . $t[1];
        })
        ->make(true);
}

}
