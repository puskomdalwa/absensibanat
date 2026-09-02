<?php
namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Keterangan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('home.dashboard.index', compact('user'));
    }

    public function data(Request $request)
    {
        $user   = Auth::user();
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
                                 Isi Kegiatan
                            </button>
                        </div>';
                return $actionButtons;
            })
            ->rawColumns(['action', 'name', 'keterangan', 'has_keterangan'])
            ->toJson();
    }

    public function keterangan(Absensi $absensi)
    {
        return response()->json($absensi->keterangans);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'absensi_id' => 'required|exists:absensi,id',
                'waktu'      => 'required',
                'keterangan' => 'required|string|max:500',
            ]);

            $keterangan             = new Keterangan();
            $keterangan->absensi_id = $request->absensi_id;
            $keterangan->waktu      = $request->waktu;
            $keterangan->keterangan = $request->keterangan;
            $keterangan->save();

            return response()->json([
                'status'  => 'true',
                'type'    => 'success',
                'message' => 'Berhasil menambahkan keterangan absensi',
                'request' => $request->all(),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'type'    => 'error',
                'message' => implode('<br><br>', array_map('implode', $e->errors())),
                'req'     => $request->all(),
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
            return [
                'status'  => false,
                'type'    => 'error',
                'message' => $th->getMessage(),
            ];
        }
    }

    public function update(Request $request)
    {
        try {
            $request->validate([
                'id'         => 'required|exists:keterangan,id',
                'waktu'      => 'required',
                'keterangan' => 'required|string|max:500',
            ]);

            $keterangan             = Keterangan::find($request->id);
            $keterangan->waktu      = $request->waktu;
            $keterangan->keterangan = $request->keterangan;
            $keterangan->save();

            return response()->json([
                'status'  => 'true',
                'type'    => 'success',
                'message' => 'Berhasil mengedit keterangan absensi',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'type'    => 'error',
                'message' => implode('<br><br>', array_map('implode', $e->errors())),
                'req'     => $request->all(),
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
            return [
                'status'  => false,
                'type'    => 'error',
                'message' => $th->getMessage(),
            ];
        }
    }

    public function delete(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:keterangan,id',
            ]);

            $keterangan = Keterangan::findOrFail($request->id);
            $keterangan->delete();

            return response()->json([
                'status'  => 'true',
                'type'    => 'success',
                'message' => 'Berhasil menghapus keterangan absensi ' . $request->id,
                'req'     => $request->all(),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'type'    => 'error',
                'message' => implode('<br><br>', array_map('implode', $e->errors())),
                'req'     => $request->all(),
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
            return [
                'status'  => false,
                'type'    => 'error',
                'message' => $th->getMessage(),
            ];
        }
    }
}
