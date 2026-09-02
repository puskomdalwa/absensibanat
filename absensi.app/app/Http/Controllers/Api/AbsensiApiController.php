<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Keterangan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AbsensiApiController extends Controller
{
    // public function index()
    // {

    //     $absensi = Absensi::join('users', 'users.id', '=', 'absensi.users_id')->paginate(10);

    //     return response()->json([
    //         'data'       => $absensi->items(),
    //         'pagination' => [
    //             'current_page'  => $absensi->currentPage(), // HARUS UPDATE!
    //             'last_page'     => $absensi->lastPage(),
    //             'next_page_url' => $absensi->nextPageUrl(),
    //             'prev_page_url' => $absensi->previousPageUrl(),
    //         ],
    //     ]);
    // }
    public function index(Request $request)
    {

        $id = $request->user()->id;
        $absensi = Absensi::join('users', 'users.id', '=', 'absensi.users_id')
            ->select('absensi.*', 'users.username', 'users.name', 'users.email', 'users.jenis_kelamin', 'users.kode as kode_user', 'users.kode')
            ->where('users_id', $id)->paginate(10);

        return response()->json([
            'data' => $absensi->items(),
            'pagination' => [
                'current_page' => $absensi->currentPage(),  // HARUS UPDATE!
                'last_page' => $absensi->lastPage(),
                'next_page_url' => $absensi->nextPageUrl(),
                'prev_page_url' => $absensi->previousPageUrl(),
            ]
        ]);
    }
    public function absensiByTgl(Request $request)
    {
        try {

            $request->validate([
                'tgl' => 'required',
                'cari' => 'nullable'
            ]);

            $cari = $request->cari;
            $data = Absensi::join('users', 'users.id', '=', 'absensi.users_id')
                ->select('absensi.*', 'users.username', 'users.name', 'users.email', 'users.jenis_kelamin', 'users.kode as kode_user', 'users.kode')
                ->where('tgl_absen', $request->tgl)
                // ->where('absensi.users_id', '=', $request->user()->id)
                ->when($cari, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where("users.name", 'like', "%{$search}%");
                    });
                })
                ->orderByRaw('absensi.users_id = ? DESC', [$request->user()->id])
                ->paginate(10);

            if ($data->total() != 0) {
                return response()->json([
                    'status'     => true,
                    'data'       => $data->items(),
                    'pagination' => [
                        'current_page'  => $data->currentPage(),
                        'last_page'     => $data->lastPage(),
                        'next_page_url' => $data->nextPageUrl(),
                        'prev_page_url' => $data->previousPageUrl(),
                    ],
                    'message'    => $data->total() == 0 ? 'kosong' : 'berhasil',
                ]);
            } else {
                return response()->json([
                    'status'     => false,
                    'message'    => 'Tidak ada data',
                    'data'       => [],
                    'pagination' => [
                        'current_page'  => 1,
                        'last_page'     => 1,
                        'next_page_url' => null,
                        'prev_page_url' => null,
                    ],
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status'     => false,
                'message'    => $th->getMessage(),
                'data'       => [],
                'pagination' => [
                    'current_page'  => 1,
                    'last_page'     => 1,
                    'next_page_url' => null,
                    'prev_page_url' => null,
                ],
            ]);
        }
    }

    public function absensiByTglNotUser(Request $request)
    {
        try {
            $request->validate([
                'tgl' => 'required',
            ]);

            $data = Absensi::join('users', 'users.id', '=', 'absensi.users_id')
                ->where('tgl_absen', $request->tgl)
                ->where('absensi.users_id', '!=', $request->user()->id)
                ->select('absensi.*', 'users.username', 'users.name', 'users.email', 'users.jenis_kelamin', 'users.kode as kode_user', 'users.kode')
                ->paginate(10);

            if ($data->total() != 0) {
                return response()->json([
                    'status'     => true,
                    'data'       => $data->items(),
                    'pagination' => [
                        'current_page'  => $data->currentPage(),
                        'last_page'     => $data->lastPage(),
                        'next_page_url' => $data->nextPageUrl(),
                        'prev_page_url' => $data->previousPageUrl(),
                    ],
                    'message'    => $data->total() == 0 ? 'kosong' : 'berhasil',
                ]);
            } else {
                return response()->json([
                    'status'     => false,
                    'message'    => 'Tidak ada data',
                    'data'       => [],
                    'pagination' => [
                        'current_page'  => 1,
                        'last_page'     => 1,
                        'next_page_url' => null,
                        'prev_page_url' => null,
                    ],
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status'     => false,
                'message'    => $th->getMessage(),
                'data'       => [],
                'pagination' => [
                    'current_page'  => 1,
                    'last_page'     => 1,
                    'next_page_url' => null,
                    'prev_page_url' => null,
                ],
            ]);
        }
    }

    public function belumAbsen(Request $request)
    {
        try {
            // Log::info($request->all());

            $request->validate([
                'tgl' => 'required',
                'cari' => 'nullable'
            ]);

            $tgl = \Carbon\Carbon::parse($request->tgl)->format('Y-m-d');
            $cari = $request->cari;

            Log::info('TGL FIXED: ' . $tgl);
            Log::info('CARI: ' . $cari);
            $data = User::leftJoin('absensi', function ($join) use ($tgl) {
                $join->on('users.id', '=', 'absensi.users_id')
                    // ->whereDate('absensi.tgl_absen', $tgl);
                    ->whereRaw("DATE(absensi.tgl_absen) = ?", [$tgl]);
            })
                // ->whereNull('absensi.id')
                ->when($cari, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('users.name', 'like', "%{$search}%");
                    });
                })
                ->select('users.id', 'users.name', 'users.email')
                ->paginate(10);
            Log::info($data);

            return response()->json([
                'status'     => true,
                'data'       => $data->items(),
                'pagination' => [
                    'current_page'  => $data->currentPage(),
                    'last_page'     => $data->lastPage(),
                    'next_page_url' => $data->nextPageUrl(),
                    'prev_page_url' => $data->previousPageUrl(),
                ],
                'message'    => 'berhasil',
            ]);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }
    function destroy(Request $request)
    {
        Log::info($request->all());
        try {
            $request->validate([
                'id' => 'required'
            ]);
            $id = $request->id;
            if ($id != null) {
                Keterangan::find($id)->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'data berhasil dihapus'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'data gagal dihapus'
                ]);
            }
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }
    function absensiDetail(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required',
                'tgl'=>'required',
                'cari' => 'nullable'
            ]);

            $cari = $request->cari;
            $data = Keterangan::join('absensi', 'absensi.id', '=', 'keterangan.absensi_id')
                ->join('users', 'users.id', '=', 'absensi.users_id')
                ->where('absensi.users_id', $request->id)
                ->where('absensi.tgl_absen', $request->tgl)
                ->select('keterangan.waktu', 'keterangan.id', 'keterangan.keterangan', 'absensi.tgl_absen', 'users.name')
                // ->where('users_id', $request->id)
                // ->where('tgl_absen', $request->tgl)
                ->when($cari, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where("users.name", 'like', "%{$search}%");
                    });
                })
                //  ->orderByRaw('absensi.users_id = ? DESC', [$request->user()->id])
                ->paginate(10);
            if ($data->total() != 0) {
                Log::info('berhasil');
                return response()->json([
                    'status' => true,
                    'data' => $data->items(),
                    'pagination' => [
                        'current_page' => $data->currentPage(),
                        'last_page' => $data->lastPage(),
                        'next_page_url' => $data->nextPageUrl(),
                        'prev_page_url' => $data->previousPageUrl()
                    ],
                    'message' => $data->total() == 0 ? 'kosong' : 'berhasil'
                ]);
            } else {
                Log::info('salah');
                return response()->json([
                    'status' => false,
                    'message' => 'gagal',
                    'data' => [],
                    'pagination' => [
                        'current_page' => 1,
                        'last_page' => 1,
                        'next_page_url' => null,
                        'prev_page_url' => null
                    ],
                ]);
            }
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }
    public function absenHariIni(Request $request)
    {
        try {
            $request->validate([
                'id'  => 'required',
                'tgl' => 'required',
            ]);
            $data = Absensi::where('users_id', $request->id)->where('tgl_absen', $request->tgl)->first();
            return response()->json([
                'status'  => true,
                'data'    => $data,
                'message' => 'berhasil',
            ]);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return response()->json([
                'status'  => false,
                'data'    => null,
                'message' => $th->getMessage(),
            ]);
        }
    }
    function addKeterangan(Request $request)
    {
        Log::info($request->all());
        try {
            $request->validate([
                'ket' => 'required',
                'waktu' => 'required',
                'id'=>'required'
            ]);
            if ($request->ket != null && $request->waktu != null && $request->id != null) {
                // $id = Absensi::where('users_id',$request->user()->id)->where('tgl_absen',$request->tgl)->first();
                $waktu = strval($request->waktu);
                
                $start = \Carbon\Carbon::createFromTimeString($waktu);
                $correctTime = $start->addMinutes(70)->format('H:i:s');
                $data = new Keterangan();
                $data->absensi_id = $request->id;
                $data->keterangan = $request->ket;
                $data->waktu = $correctTime;
                $data->save();
                Log::info('lancar jaya gaes');
                return response()->json([
                    'status' => true,
                    'message' => 'data berhasil ditambahkan'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'data gagal ditambahkan'
                ]);
            }
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }
    public function updateKeterangan(Request $request)
    {
       try {
         $request->validate([
            'id'  => 'required',
            'ket' => 'required',
            'waktu' => 'required'
        ]);

        $data = Keterangan::where('id', $request->id)->first();
        if ($data) {
            $data->keterangan = $request->ket;
            $data->waktu = $request->waktu;
            $data->save();
            return response([
                'status'  => true,
                'message' => 'data berhasil disimpan',
            ]);
        }else {
            return response([
                'status'  => false,
                'message' => 'data gagal disimpan',
            ]);
        }
       } catch (\Throwable $th) {
        Log::info($th->getMessage());
       }
    }
    public function getDataAbsensiPerId(Request $request)
    {
        $request->validate([
            'id'  => 'required',
            'tgl' => 'required',
        ]);

        $data = Absensi::join('users', 'users.id', '=', 'absensi.users_id')
            ->where('absensi.users_id', $request->id)->where('absensi.tgl_absen', $request->tgl)
            ->select('users.username as nama_users', 'users.kode as kode_user', 'users.kode', 'keterangan', 'pagi', 'tgl_absen')
            ->orderBy('absensi.created_at', 'desc')
            ->first();

        return response()->json([
            'status'  => true,
            'data'    => $data,
            'message' => 'berhasil',
        ]);
    }
    function listData(Request $request)
    {
        try {
            $data = Absensi::where('users_id', $request->user()->id)->latest()->limit(5)->get();
            return response()->json([
                'status'  => true,
                'data'    => $data,
                'message' => 'berhasil',
            ]);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }
}
