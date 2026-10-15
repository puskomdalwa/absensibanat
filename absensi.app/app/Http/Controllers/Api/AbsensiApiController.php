<?php

namespace App\Http\Controllers\Api;

use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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

    //     $absensi = Absensi::with('keterangans')->join('users', 'users.id', '=', 'absensi.users_id')->paginate(10);

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
        $absensi = Absensi::with('keterangans')->join('users', 'users.id', '=', 'absensi.users_id')
            ->select('absensi.*', 'users.username', 'users.name', 'users.email', 'users.jenis_kelamin', 'users.kode as kode_user', 'users.kode')->addSelect(\Illuminate\Support\Facades\DB::raw('EXISTS (SELECT 1 FROM keterangan WHERE keterangan.absensi_id = absensi.id) as has_keterangan'))
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
            $authUser = $request->user();
            $isExecutive = false;
            if ($authUser) {
                $roleName = strtolower($authUser->role ? ($authUser->role->akses ?? '') : '');
                $isExecutive = in_array($roleName, ['admin', 'superadmin']) || ($authUser->role_id == 1 || $authUser->role_id == 2);
            }

            $cari = $request->cari;
            $query = Absensi::with('keterangans')->join('users', 'users.id', '=', 'absensi.users_id')
                ->select('absensi.*', 'users.username', 'users.name', 'users.email', 'users.jenis_kelamin', 'users.kode as kode_user', 'users.kode')
                ->addSelect(\Illuminate\Support\Facades\DB::raw('EXISTS (SELECT 1 FROM keterangan WHERE keterangan.absensi_id = absensi.id) as has_keterangan'));

            // 1. Role-based scoping: Non-admin only sees their own attendance records
            if (!$isExecutive && $authUser) {
                $query->where('absensi.users_id', $authUser->id);
            } elseif ($request->filled('user_id')) {
                $query->where('absensi.users_id', $request->user_id);
            }

            // 2. Date Filtering
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('absensi.tgl_absen', [$request->start_date, $request->end_date]);
            } elseif ($request->filled('bulan') || $request->filled('tahun')) {
                if ($request->filled('bulan') && $request->bulan !== '*') {
                    $query->whereMonth('absensi.tgl_absen', $request->bulan);
                }
                if ($request->filled('tahun') && $request->tahun !== '*') {
                    $query->whereYear('absensi.tgl_absen', $request->tahun);
                }
            } elseif ($request->filled('tgl')) {
                $query->where('absensi.tgl_absen', $request->tgl);
            } else {
                // Default: Current month (1 bulan penuh)
                $query->whereMonth('absensi.tgl_absen', date('m'))
                      ->whereYear('absensi.tgl_absen', date('Y'));
            }

            if ($cari) {
                $query->where(function ($q) use ($cari) {
                    $q->where("users.name", 'like', "%{$cari}%")
                      ->orWhere("users.username", 'like', "%{$cari}%");
                });
            }

            $perPage = $request->input('per_page', 100);
            $data = $query->orderBy('absensi.tgl_absen', 'DESC')
                          ->orderBy('absensi.pagi', 'DESC')
                          ->paginate($perPage);

            return response()->json([
                'status'     => true,
                'data'       => $data->items(),
                'pagination' => [
                    'current_page'  => $data->currentPage(),
                    'last_page'     => $data->lastPage(),
                    'next_page_url' => $data->nextPageUrl(),
                    'prev_page_url' => $data->previousPageUrl(),
                    'total'         => $data->total(),
                ],
                'message'    => $data->total() == 0 ? 'Tidak ada data presensi pada periode ini' : 'berhasil',
            ]);
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
                    'total'         => 0,
                ],
            ], 500);
        }
    }

    public function absensiByTglNotUser(Request $request)
    {
        try {
            $request->validate([
                'tgl' => 'required',
            ]);

            $data = Absensi::with('keterangans')->join('users', 'users.id', '=', 'absensi.users_id')
                ->where('tgl_absen', $request->tgl)
                ->where('absensi.users_id', '!=', $request->user()->id)
                ->select('absensi.*', 'users.username', 'users.name', 'users.email', 'users.jenis_kelamin', 'users.kode as kode_user', 'users.kode')->addSelect(\Illuminate\Support\Facades\DB::raw('EXISTS (SELECT 1 FROM keterangan WHERE keterangan.absensi_id = absensi.id) as has_keterangan'))
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
                    ->whereRaw("DATE(absensi.tgl_absen) = ?", [$tgl]);
            })
                ->where(function ($q) {
                    $q->whereNull('absensi.id')
                      ->orWhereNull('absensi.pagi')
                      ->orWhere('absensi.pagi', '=', '-');
                })
                ->when($cari, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('users.name', 'like', "%{$search}%");
                    });
                })
                ->select('users.id', 'users.name', 'users.email')
                ->paginate(50);
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

        $data = Absensi::with('keterangans')->join('users', 'users.id', '=', 'absensi.users_id')
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
    /**
     * Export Excel Riwayat Presensi
     * Khusus role admin & superadmin: mengekspor semua user sesuai filter (kecuali superadmin)
     * Untuk role lain (dosen/user/santri): hanya mengekspor presensi dirinya sendiri
     */
    public function exportAbsensiExcel(Request $request)
    {
        try {
            $authUser = $request->user();
            $isExecutive = false;
            if ($authUser) {
                if (method_exists($authUser, 'isAdmin') && $authUser->isAdmin()) {
                    $isExecutive = true;
                } else {
                    $roleName = strtolower($authUser->role ? ($authUser->role->akses ?? '') : '');
                    $isExecutive = in_array($roleName, ['admin', 'superadmin']) || ($authUser->role_id == 1 || $authUser->role_id == 2 || $authUser->role_id == 16);
                }
            }

            $query = Absensi::join('users', 'users.id', '=', 'absensi.users_id')
                ->leftJoin('role', 'role.id', '=', 'users.role_id')
                ->leftJoin('departemen', 'departemen.id', '=', 'users.departemen_id')
                ->leftJoin('kategori', 'kategori.id', '=', 'absensi.kategori_id');

            // 1. Role-based scoping: Admin/Superadmin mengekspor semua user KECUALI superadmin
            if ($isExecutive) {
                $query->where(function ($q) {
                    $q->whereNull('role.akses')
                      ->orWhereRaw('LOWER(role.akses) NOT LIKE ?', ['%superadmin%']);
                })->where(function ($q) {
                    $q->whereNull('users.role_id')
                      ->orWhere('users.role_id', '!=', 16);
                });
            } else {
                $query->where('absensi.users_id', $authUser ? $authUser->id : 0);
            }

            // 2. Filter Tanggal (Bulan, Rentang, atau Tanggal Spesifik)
            $filterLabel = 'Semua';
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('absensi.tgl_absen', [$request->start_date, $request->end_date]);
                $filterLabel = $request->start_date . '_sd_' . $request->end_date;
            } elseif ($request->filled('bulan') || $request->filled('tahun')) {
                if ($request->filled('bulan') && $request->bulan !== '*') {
                    $query->whereMonth('absensi.tgl_absen', $request->bulan);
                }
                if ($request->filled('tahun') && $request->tahun !== '*') {
                    $query->whereYear('absensi.tgl_absen', $request->tahun);
                }
                $filterLabel = ($request->tahun ?: date('Y')) . '_' . ($request->bulan ?: date('m'));
            } elseif ($request->filled('tgl')) {
                $query->where('absensi.tgl_absen', $request->tgl);
                $filterLabel = $request->tgl;
            } else {
                $query->whereMonth('absensi.tgl_absen', date('m'))
                      ->whereYear('absensi.tgl_absen', date('Y'));
                $filterLabel = date('Y_m');
            }

            if ($request->filled('search')) {
                $cari = $request->search;
                $query->where(function ($q) use ($cari) {
                    $q->where('users.name', 'like', "%{$cari}%")
                      ->orWhere('users.username', 'like', "%{$cari}%");
                });
            }

            $records = $query->orderBy('absensi.tgl_absen', 'desc')
                ->orderBy('users.name', 'asc')
                ->select(
                    'absensi.id',
                    'absensi.tgl_absen',
                    'users.username',
                    'users.name as user_name',
                    'role.akses as role_name',
                    'departemen.nama as departemen_name',
                    'absensi.pagi',
                    'absensi.sore',
                    'absensi.selisih',
                    'kategori.nama as kategori_nama',
                    'kategori.nominal as kategori_nominal',
                    'absensi.keterangan'
                )
                ->get();

            $absensiIds = $records->pluck('id')->filter()->all();
            $keterangansMap = [];
            if (!empty($absensiIds)) {
                $kets = DB::table('keterangan')->whereIn('absensi_id', $absensiIds)->get();
                foreach ($kets as $ket) {
                    $keterangansMap[$ket->absensi_id][] = $ket->keterangan;
                }
            }

            $exportData = collect();
            foreach ($records as $index => $r) {
                $hasCheckIn = !empty($r->pagi) && $r->pagi !== '-';
                
                $menit = (int)($r->selisih ?? 0);
                if ($menit <= 0 && $r->pagi && $r->sore) {
                    try {
                        $pagi = Carbon::parse($r->pagi);
                        $sore = Carbon::parse($r->sore);
                        $menit = max(0, $pagi->diffInMinutes($sore));
                    } catch (\Throwable $t) {
                        $menit = 0;
                    }
                }

                $ketsList = $keterangansMap[$r->id] ?? [];
                $ketsJoined = !empty($ketsList) ? implode('; ', $ketsList) : ($r->keterangan ?: '-');

                if (!$hasCheckIn) {
                    $durasiTeks = '-';
                    $nominalFormatted = 'Rp 0';
                    $ketLower = strtolower($ketsJoined);
                    if (str_contains($ketLower, 'sakit')) {
                        $kategoriTeks = 'Sakit';
                    } elseif (str_contains($ketLower, 'izin')) {
                        $kategoriTeks = 'Izin';
                    } elseif (!empty($r->keterangan)) {
                        $kategoriTeks = 'Izin / Keterangan';
                    } else {
                        $kategoriTeks = 'Tidak Hadir';
                    }
                } else {
                    $jam = floor($menit / 60);
                    $sisaMenit = $menit % 60;
                    $durasiTeks = $menit > 0 ? "{$jam}j {$sisaMenit}m" : '-';
                    $nominal = (float)($r->kategori_nominal ?? 0);
                    $nominalFormatted = 'Rp ' . number_format($nominal, 0, ',', '.');
                    $kategoriTeks = $r->kategori_nama ?: 'Hadir';
                }

                $exportData->push([
                    'NO' => $index + 1,
                    'TANGGAL' => Carbon::parse($r->tgl_absen)->translatedFormat('d-m-Y'),
                    'USERNAME' => $r->username,
                    'NAMA' => $r->user_name,
                    'ROLE' => strtoupper($r->role_name ?: 'USER'),
                    'DEPARTEMEN' => $r->departemen_name ?: '-',
                    'JAM_MASUK' => $r->pagi ? substr($r->pagi, 0, 5) . ' WIB' : '-',
                    'JAM_PULANG' => $r->sore ? substr($r->sore, 0, 5) . ' WIB' : '-',
                    'DURASI' => $durasiTeks,
                    'KATEGORI' => $kategoriTeks,
                    'NOMINAL' => $nominalFormatted,
                    'KETERANGAN' => $ketsJoined,
                ]);
            }

            $headings = [
                'NO',
                'TANGGAL',
                'USERNAME / NIP',
                'NAMA',
                'ROLE',
                'DEPARTEMEN',
                'JAM MASUK',
                'JAM PULANG',
                'DURASI',
                'KATEGORI',
                'NOMINAL',
                'KETERANGAN',
            ];

            $prefix = $isExecutive ? 'Rekap_Presensi_Semua_User' : 'Presensi_Pribadi';
            $filename = "{$prefix}_{$filterLabel}_" . date('Ymd_His') . '.xlsx';

            return Excel::download(new ExcelExport($exportData, $headings), $filename);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal membuat file excel: ' . $th->getMessage(),
            ], 500);
        }
    }
}
