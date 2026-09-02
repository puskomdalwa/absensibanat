<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ExcelExport;
use App\Http\Services\RealtimeAbsensi;
use App\Imports\AbsensiImport;
use App\Models\Role;
use App\Models\User;
use App\Models\Absensi;
use App\Models\Departemen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Verify;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class AbsensiController extends Controller
{

    public function index()
    {
        $isStaff = auth()->user()->isStaff();
        $user = User::all();
        $departemen = Departemen::all();
        $role = Role::all();
        $tahunAbsensi = Absensi::selectRaw('YEAR(tgl_absen) as tahun')
            ->whereNotNull('tgl_absen')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun')
            ->filter()
            ->push(now()->year)
            ->unique()
            ->sortDesc()
            ->values();

        $device = Device::all();
        $verify = Verify::all();
        return view('admin.absensi.index', compact('user', 'departemen', 'role', 'device', 'verify', 'tahunAbsensi', 'isStaff'));
    }
    public function data(Request $request)
    {
        $isStaff = $request->user()->isStaff();
        $data = Absensi::join('users', 'users.id', '=', 'absensi.users_id') // Pastikan join ke tabel users
            ->select('absensi.*', 'users.name as user_name', 'users.role_id', 'users.departemen_id');
        return DataTables::of($data)
            ->filter(function ($query) use ($request) {
                $this->applyAbsensiFilters($query, $request);

                $search = $request->input('search.value');
                if ($search !== null && $search !== '') {
                    $query->where(function ($query) use ($search) {
                        $query->orWhere('latitude', 'LIKE', "%$search%");
                        $query->orWhere('users.name', 'LIKE', "%$search%");
                        $query->orWhere('tgl_absen', 'LIKE', "%$search%");
                        $query->orWhere('pagi', 'LIKE', "%$search%");
                        $query->orWhere('keterangan', 'LIKE', "%$search%");
                    });
                }
            })
            ->editColumn('name', function ($row) {
                $assetsPath = asset('photo') . '/';
                $detailUrl = route('admin.user.absensi.detail', $row->users_id);
                if ($row->user->photo) {
                    // Untuk photo gambar
                    $output = '<img src="' . $assetsPath . $row->user->photo . '" alt="Avatar" class="rounded-circle">';
                } else {
                    // Untuk photo badge dengan inisial
                    $stateNum = rand(0, 5);
                    $states = ['success', 'danger', 'warning', 'info', 'primary', 'secondary'];
                    $state = $states[$stateNum];

                    // Ambil inisial dari nama lengkap
                    preg_match_all('/\b\w/', $row->user->name, $matches);
                    $initials = isset($matches[0]) ? strtoupper($matches[0][0] . end($matches[0])) : '';

                    $output = '<span class="avatar-initial rounded-circle bg-label-' . $state . '">' . $initials . '</span>';
                }
                return '
                <a href="' . $detailUrl . '" class="d-flex justify-content-start align-items-center user-name text-body">
                    <div class="avatar-wrapper">
                        <div class="avatar me-2">
                            ' . $output . '
                        </div>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="emp_name text-truncate">' . htmlspecialchars($row->user->name) . '</span>
                        <small class="emp_post text-truncate text-muted">' . htmlspecialchars($row->user->email ?? 'Unknown') . '</small>
                        <small class="emp_post text-truncate text-muted">' . htmlspecialchars($row->user->departemen->nama ?? 'Unknown') . '</small>
                    </div>
                </a>
            ';
            })
            ->addColumn('action', function ($row) use ($isStaff) {
                if ($isStaff) {
                    return '';
                }

                $actionButtons = '
                    <div class="d-inline-block">
                        <a href="javascript:;" class="btn btn-sm btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="ti ti-dots-vertical ti-md"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end m-0">
                            <li>
                                <button class="dropdown-item Btnedit"
                                    data-id="' . $row->id . '"
                                    data-users_id="' . $row->users_id . '"
                                    data-user_name="' . e($row->user_name) . '"
                                    data-tgl="' . e($row->tgl_absen) . '"
                                    data-pagi="' . e($row->pagi) . '"
                                    data-sore="' . e($row->sore) . '"
                                    data-device_id="' . $row->device_id . '"
                                    data-verify_id="' . $row->verify_id . '"
                                    data-latitude="' . e($row->latitude) . '"
                                    data-longitude="' . e($row->longitude) . '"
                                    data-ket="' . e($row->keterangan) . '"
                                    >Edit</button></li>
                                <div class="dropdown-divider"></div>
                            <li>
                                <form class="form-delete-record">
                                ' . method_field('DELETE') . csrf_field() . '
                                    <input type="hidden" name="id" value="' . $row->id . '">
                                    <input type="hidden" name="tgl_absen" value="' . e($row->tgl_absen) . '">
                                    <button type="submit" class="dropdown-item text-danger">
                                        Delete
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>';
                return $actionButtons;
            })
            ->rawColumns(['action', 'name'])
            ->toJson();
    }
    public function store(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required',
                'device_id' => 'required',
                'verify_id' => 'required',
                'tgl' => 'required',
                'latitude' => 'nullable',
                'longitude' => 'nullable',
                'jam' => 'required',
                'jam_pulang' => 'required',
                'ket' => 'nullable',
            ]);

            $tanggal = $request->tgl;
            $waktu = $request->jam;
            $waktuPulang = $request->jam_pulang;

            $absensi = Absensi::where('users_id', $request->user_id)
                ->where('tgl_absen', $tanggal)
                ->exists();

            if ($absensi) {
                return [
                    'status' => false,
                    'message' => 'Sudah absensi hari ini'
                ];
            }

            $verify = Verify::find($request->verify_id);
            $device = Device::find($request->device_id);

            $absensi = new Absensi();
            $absensi->users_id = $request->user_id;
            $absensi->tgl_absen = $tanggal;
            $absensi->pagi = $waktu;
            $absensi->sore = $waktuPulang;
            $absensi->latitude = $device->name;
            $absensi->longitude = $device->name;
            $absensi->verify_id = $verify->id;
            $absensi->device_id = $device->id;
            RealtimeAbsensi::isiKategori($absensi, \Carbon\Carbon::parse($tanggal . ' ' . $waktuPulang));
            $absensi->save();

            return [
                'status'  => true,
                'type'    => 'success',
                'message' => 'Success',
            ];
        } catch (\Throwable $th) {
            return [
                'status'  => false,
                'type'    => 'error',
                'message' => $th->getMessage(),
                'req' => $request->all()
            ];
        }
    }
    public function update(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required',
                'user_id' => 'required',
                'device_id' => 'required',
                'verify_id' => 'required',
                'tgl' => 'required',
                'latitude' => 'nullable',
                'longitude' => 'nullable',
                'jam' => 'required',
                'jam_pulang' => 'required',
                'ket' => 'nullable',
            ]);

            $id = $request->id;
            $user_id = $request->user_id;
            $tgl = $request->tgl;
            $jam = $request->jam;
            $jamPulang = $request->jam_pulang;
            $ket = $request->ket;

            $device = Device::find($request->device_id);
            $verify = Verify::find($request->verify_id);

            $absensi = Absensi::find($id);
            $absensi->tgl_absen = $tgl;
            $absensi->users_id = $user_id;
            $absensi->device_id = $request->device_id;
            $absensi->verify_id = $request->verify_id;
            $absensi->pagi = $jam;
            $absensi->sore = $jamPulang;
            $absensi->latitude = $device->name;
            $absensi->longitude = $device->name;
            $absensi->keterangan = $ket;
            RealtimeAbsensi::isiKategori($absensi, \Carbon\Carbon::parse($tgl . ' ' . $jamPulang));
            $absensi->save();

            return [
                'status'  => true,
                'type'    => 'success',
                'message' => 'Success',
            ];
        } catch (\Throwable $th) {
            return [
                'status'  => false,
                'type'    => 'error',
                'message' => $th->getMessage(),
                'req' => $request->all()
            ];
        }
    }
    public function destroy(Request $request)
    {
        try {
            Absensi::find($request->id)->delete();
            return [
                'status'  => true,
                'type'    => 'success',
                'message' => 'Success',
            ];
        } catch (\Throwable $th) {
            return [
                'status'  => false,
                'type'    => 'error',
                'message' => $th->getMessage(),
                'req' => $request->all()
            ];
        }
    }
    public function exportExcel(Request $request)
    {
        $data = Absensi::join('users', 'users.id', '=', 'absensi.users_id')
            ->leftJoin('departemen', 'departemen.id', '=', 'users.departemen_id')
            ->leftJoin('kategori', 'kategori.id', '=', 'absensi.kategori_id')
            ->select(
                'users.username',
                'users.name',
                'departemen.nama as departemen',
                'absensi.tgl_absen',
                \DB::raw('MONTH(absensi.tgl_absen) as bulan'),
                \DB::raw('YEAR(absensi.tgl_absen) as tahun'),
                'absensi.latitude',
                'absensi.longitude',
                'absensi.pagi as jam_datang',
                'absensi.sore as jam_pulang',
                'absensi.keterangan',
            )
            ->selectRaw('CASE WHEN kategori.kode = ? THEN 1 ELSE NULL END as grade_a', ['DURASI-7'])
            ->selectRaw('CASE WHEN kategori.kode = ? THEN 1 ELSE NULL END as grade_b', ['DURASI-5'])
            ->selectRaw('CASE WHEN kategori.kode = ? THEN 1 ELSE NULL END as grade_c', ['DURASI-3'])
            ->tap(function ($query) use ($request) {
                $this->applyAbsensiFilters($query, $request);
            })
            ->get();

        if ($request->filter_type == 'semua') {
            $periode = 'semua-periode';
        } elseif ($request->filter_type == 'rentang_tanggal') {
            $periode = $request->start_date . '-sampai-' . $request->end_date;
        } else {
            $periode = ($request->tahun && $request->tahun != '*' ? $request->tahun : 'semua-tahun')
                . '-'
                . ($request->bulan && $request->bulan != '*' ? str_pad($request->bulan, 2, '0', STR_PAD_LEFT) : 'semua-bulan');
        }

        return Excel::download(new ExcelExport($data, [
            'USERNAME',
            'NAMA',
            'DEPARTEMEN',
            'TANGGAL ABSEN',
            'BULAN',
            'TAHUN',
            'LATITUDE',
            'LONGITUDE',
            'JAM DATANG',
            'JAM PULANG',
            'KETERANGAN',
            'GRADE A',
            'GRADE B',
            'GRADE C',
        ]), 'absensi-' . $periode . '.xlsx');
    }
    public function import(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv'
            ]);

            $import = new AbsensiImport($request);
            Excel::import($import, $request->file('file'));

            return [
                'status' => true,
                'type' => 'success',
                'data' => $import,
                'message' => 'Success import ' . $import->success . ' data dari ' . $import->max . ' error: ' . $import->error
            ];
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'type' => 'error',
                'message' => implode('<br><br>', array_map('implode', $e->errors())),
                'req' => $request->all()
            ]);
        } catch (\Throwable $th) {
            return [
                'status' => false,
                'type' => 'error',
                'message' => $th->getMessage()
            ];
        }
    }

    private function applyAbsensiFilters($query, Request $request)
    {
        $roleId = $request->input('role_id', '*') ?: '*';
        $departemenId = $request->input('departemen_id', '*') ?: '*';
        $filterType = $request->input('filter_type', 'semua') ?: 'semua';

        if ($roleId !== '*') {
            $query->where('users.role_id', $roleId);
        }

        if ($departemenId !== '*') {
            $query->where('users.departemen_id', $departemenId);
        }

        if ($filterType === 'semua') {
            return $query;
        }

        if ($filterType === 'rentang_tanggal') {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            if ($startDate && $endDate) {
                $query->whereBetween('absensi.tgl_absen', [$startDate, $endDate]);
            } elseif ($startDate) {
                $query->whereDate('absensi.tgl_absen', '>=', $startDate);
            } elseif ($endDate) {
                $query->whereDate('absensi.tgl_absen', '<=', $endDate);
            }

            return $query;
        }

        $bulan = $request->input('bulan', '*') ?: '*';
        $tahun = $request->input('tahun', '*') ?: '*';

        if ($bulan !== '*') {
            $query->whereMonth('absensi.tgl_absen', $bulan);
        }

        if ($tahun !== '*') {
            $query->whereYear('absensi.tgl_absen', $tahun);
        }

        return $query;
    }
}
