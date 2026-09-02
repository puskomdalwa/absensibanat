<?php
namespace App\Http\Controllers;

use Adrianorosa\GeoLocation\GeoLocation;
use App\Models\AbsensiModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthApiController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $data = AbsensiModel::where('users_id', $request->id)->get();

        return response()->json($data);
    }

    public function profilUser(Request $request)
    {
        $request->validate([
            'id' => 'required|int',
        ]);

        $user = User::find($request->id);
        return response()->json($user);
    }

    public function login(Request $request)
    {

        $validate = Validator::make(
            $request->all(),
            [
                'name'     => 'required|string|max:255',
                'password' => 'required|string|max:255',
            ]
        );

        if (! Auth::attempt(['name' => $request->name, 'password' => $request->password])) {
            return response()->json([
                'status'  => false,
                'message' => 'login gagal',
            ], 500);
        }

        $users = User::where('name', $request->name)->firstOrFail();
        $token = $users->createToken('auth_token')->plainTextToken;

        return response()->json(
            [
                'status'     => true,
                'data'       => $users,
                'token'      => $token,
                'token_type' => 'Bearer',
            ]
        );
    }

    public function simpanAbsensi(Request $request)
    {
        Log::info($request->all());
        $tgl = date('yyyy-MM-dd');
        try {
            $request->validate([
                'users_id'   => 'required',
                'tgl'        => 'required',
                'pagi'       => 'nullable',
                'sore'       => 'nullable',
                'sesi'       => 'nullable',
                'latitude'   => 'required|string',
                'longitude'  => 'required|string',
                'keterangan' => 'required|string',
            ]);
            $masuk = AbsensiModel::where('users_id', $request->users_id)->where('tgl_absen', $request->tgl)->count();
            Log::info($masuk);
            $id = $request->user_id;
            if ($masuk == 0) {
                $data = [
                    'users_id'   => $request->users_id,
                    'tgl_absen'  => $request->tgl,
                    'pagi'       => $request->pagi,
                    'latitude'   => $request->latitude,
                    'longitude'  => $request->longitude,
                    'keterangan' => $request->keterangan,
                ];
                AbsensiModel::create($data);
            } else {
                Log::info('hallo');
                $data_absensi = [
                    'users_id' => $request->users_id,
                    'sore'     => $request->sore,
                ];
                AbsensiModel::where('users_id', $request->users_id)->where('tgl_absen', $request->tgl)->update($data_absensi);
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }

    public function coba()
    {
        // $ip = "192.168.1.6";
        $ip = "8.8.8.8";
        dd(GeoLocation::lookup($ip));
        // dd(Location::get($ip));
    }

    public function keluar(Request $request)
    {
        try {
            $user = $request->user();
            Log::info($user);
            $user->currentAccessToken()->delete();
            $data = [
                'status'  => true,
                'message' => 'berhasil logout',
            ];
            return response()->json($data, 200);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            $data = [
                'status'  => false,
                'message' => 'gagal Logout',
            ];
            return response()->json($data);
        }
    }
}
