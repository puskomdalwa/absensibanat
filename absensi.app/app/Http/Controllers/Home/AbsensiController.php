<?php

namespace App\Http\Controllers\Home;

use App\Models\User;
use App\Models\Absensi;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        if (request()->ajax()) {
            $users = User::where('role_id', '!=', 1)
                ->when($request->departemen_id != '*' && $request->departemen_id != null, function ($query) use ($request) {
                    $query->where('departemen_id', $request->departemen_id);
                })
                ->when($request->search != null && $request->search != '', function ($query) use ($request) {
                    $query->where('name', 'like', "%$request->search%");
                })
                ->when($request->sort != null, function ($query) use ($request) {
                    $query->orderBy($request->sort);
                });

            if ($request->show == '*' && $request->show != null) {
                $users = $users->get();
            } else {
                $users = $users->paginate($request->show); // Keep pagination
            }

            // Check if the result is paginated
            $isPaginated = $users instanceof \Illuminate\Pagination\LengthAwarePaginator;
            return view('home.absensi.data', compact('users', 'isPaginated'));
        }
        return view('home.absensi.index');
    }

    public function show(User $user)
    {
        return view('home.absensi.show', compact('user'));
    }

    public function data(User $user, Request $request)
    {
        $search = request('search.value');
        $data   = Absensi::join('users', 'users.id', '=', 'absensi.users_id') // Pastikan join ke tabel users
            ->where('absensi.users_id', $user->id)
            ->select('absensi.*', 'users.name as user_name')
             ->addSelect(DB::raw("EXISTS (SELECT 1 FROM keterangan WHERE keterangan.absensi_id = absensi.id) as has_keterangan"));
        return DataTables::of($data)
            ->filter(function ($query) use ($search, $request) {
                $query->where(function ($query) use ($search) {
                    $query->orWhere('latitude', 'LIKE', "%$search%");
                    $query->orWhere('users.name', 'LIKE', "%$search%");
                    $query->orWhere('tgl_absen', 'LIKE', "%$search%");
                    $query->orWhere('pagi', 'LIKE', "%$search%");
                    $query->orWhere('keterangan', 'LIKE', "%$search%");
                });
                if ($request->startDate && $request->endDate) {
                    $query->whereBetween('tgl_absen', [$request->startDate, $request->endDate]);
                } elseif ($request->startDate) {
                    $query->where('tgl_absen', '>=', $request->startDate);
                } elseif ($request->endDate) {
                    $query->where('tgl_absen', '<=', $request->endDate);
                }
            })
            ->editColumn('has_keterangan', function ($row) {
                return $row->has_keterangan ? '<span class="badge bg-success">Sudah Isi</span>' : '<span class="badge bg-danger">Belum Isi</span>';
            })
            ->editColumn('keterangan', function ($row) {
                $keterangan = $row->keterangan;
                if (strlen($keterangan) > 100) {
                    $short = Str::limit($keterangan, 100, '... ');
                    $short .= '<a href="#" class="baca-selengkapnya" data-keterangan="' . e($keterangan) . '" data-bs-toggle="modal" data-bs-target="#keteranganModal">Baca selengkapnya</a>';
                    return $short;
                }
                return e($keterangan);
            })
            ->addColumn('action', function ($row) {
                $actionButtons = '
                        <div class="d-inline-block">
                            <button class="btn btn-sm btn-text-secondary rounded btn-icon" data-bs-toggle="modal" data-bs-target="#modal-keterangan"
                            data-id="' . $row->id . '" data-keterangan="' . $row->keterangan . '" data-tgl_absen="' . $row->tgl_absen . '" data-pagi="' . $row->pagi . '">
                                 Lihat Kegiatan
                            </button>
                        </div>';
                return $actionButtons;
            })
            ->rawColumns(['action', 'name', 'keterangan', 'has_keterangan'])
            ->toJson();
    }

     public function keterangan(User $user, Absensi $absensi)
    {
        return response()->json($absensi->keterangans);
    }
}
