<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Services\RealtimeAbsensi;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class KategoriController extends Controller
{
    public function index()
    {
        return view('admin.kategori.index');
    }

    public function data(Request $request)
    {
        $search = request('search.value');
        $data = Kategori::select('*');

        return DataTables::of($data)
            ->filter(function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->orWhere('nama', 'LIKE', "%$search%");
                    $query->orWhere('kode', 'LIKE', "%$search%");
                    $query->orWhere('selisih', 'LIKE', "%$search%");
                    $query->orWhere('nominal', 'LIKE', "%$search%");
                    $query->orWhere('keterangan', 'LIKE', "%$search%");
                });
            })
            ->editColumn('nominal', function ($row) {
                return 'Rp ' . number_format((float) $row->nominal, 0, ',', '.');
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="d-inline-block">
                        <a href="javascript:;" class="btn btn-sm btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="ti ti-dots-vertical ti-md"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end m-0">
                            <li>
                                <button class="dropdown-item edit-record-button"
                                    data-id="' . $row->id . '"
                                    data-nama="' . e($row->nama) . '"
                                    data-kode="' . e($row->kode) . '"
                                    data-selisih="' . $row->selisih . '"
                                    data-nominal="' . $row->nominal . '"
                                    data-keterangan="' . e($row->keterangan) . '"
                                >Edit</button>
                            </li>
                            <div class="dropdown-divider"></div>
                            <li>
                                <form class="form-delete-record">
                                    ' . method_field('DELETE') . csrf_field() . '
                                    <input type="hidden" name="id" value="' . $row->id . '">
                                    <input type="hidden" name="name" value="' . e($row->nama) . '">
                                    <button type="submit" class="dropdown-item text-danger">Delete</button>
                                </form>
                            </li>
                        </ul>
                    </div>';
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function store(Request $request)
    {
        try {
            \DB::beginTransaction();
            $request->validate([
                'nama' => 'required|string|max:255',
                'kode' => 'required|string|max:255|unique:kategori,kode',
                'selisih' => 'required|integer|min:0',
                'nominal' => 'required|numeric|min:0',
                'keterangan' => 'nullable|string',
            ]);

            Kategori::create($request->only([
                'nama',
                'kode',
                'selisih',
                'nominal',
                'keterangan',
            ]));

            \DB::commit();
            return [
                'status' => true,
                'type' => 'success',
                'message' => 'Success',
            ];
        } catch (\Illuminate\Validation\ValidationException $e) {
            \DB::rollBack();
            return response()->json([
                'status' => false,
                'type' => 'error',
                'message' => implode('<br><br>', array_map('implode', $e->errors())),
                'req' => $request->all(),
            ]);
        } catch (\Throwable $th) {
            \DB::rollback();
            return [
                'status' => false,
                'type' => 'error',
                'message' => $th->getMessage(),
            ];
        }
    }

    public function update(Request $request)
    {
        try {
            \DB::beginTransaction();
            $kategori = Kategori::findOrFail($request->id);

            $request->validate([
                'id' => 'required|exists:kategori,id',
                'nama' => 'required|string|max:255',
                'kode' => 'required|string|max:255|unique:kategori,kode,' . $kategori->id,
                'selisih' => 'required|integer|min:0',
                'nominal' => 'required|numeric|min:0',
                'keterangan' => 'nullable|string',
            ]);

            $kategori->update($request->only([
                'nama',
                'kode',
                'selisih',
                'nominal',
                'keterangan',
            ]));

            \DB::commit();
            return [
                'status' => true,
                'type' => 'success',
                'message' => 'Success',
            ];
        } catch (\Illuminate\Validation\ValidationException $e) {
            \DB::rollBack();
            return response()->json([
                'status' => false,
                'type' => 'error',
                'message' => implode('<br><br>', array_map('implode', $e->errors())),
                'req' => $request->all(),
            ]);
        } catch (\Throwable $th) {
            \DB::rollback();
            return [
                'status' => false,
                'type' => 'error',
                'message' => $th->getMessage(),
            ];
        }
    }

    public function delete(Request $request)
    {
        try {
            \DB::beginTransaction();
            $request->validate([
                'id' => 'required|exists:kategori,id',
            ]);

            Kategori::findOrFail($request->id)->delete();

            \DB::commit();
            return [
                'status' => true,
                'type' => 'success',
                'message' => 'Success',
                'request' => $request->all(),
            ];
        } catch (\Throwable $th) {
            \DB::rollback();
            return [
                'status' => false,
                'type' => 'error',
                'message' => $th->getMessage(),
                'request' => $request->all(),
            ];
        }
    }

    public function synchronize(Request $request)
    {
        $request->validate([
            'cursor' => 'nullable|integer|min:0',
            'max_id' => 'nullable|integer|min:0',
            'total' => 'nullable|integer|min:0',
        ]);

        $cursor = (int) $request->input('cursor', 0);
        $maxId = (int) $request->input('max_id', 0);

        if ($maxId === 0) {
            $maxId = (int) (DB::table('absensi')->max('id') ?: 0);
        }

        $total = $request->filled('total')
            ? (int) $request->input('total')
            : DB::table('absensi')->where('id', '<=', $maxId)->count();

        $kategoriList = Kategori::orderBy('selisih', 'desc')->orderBy('id')->get();

        if ($kategoriList->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Sinkronisasi tidak dapat dijalankan karena data kategori masih kosong.',
            ], 422);
        }

        $absensiList = DB::table('absensi')
            ->select(['id', 'users_id', 'tgl_absen', 'pagi', 'sore', 'selisih', 'kategori_id'])
            ->where('id', '>', $cursor)
            ->where('id', '<=', $maxId)
            ->orderBy('id')
            ->limit(300)
            ->get();

        $updated = 0;
        $failed = 0;
        $nextCursor = $cursor;

        DB::transaction(function () use ($absensiList, $kategoriList, &$updated, &$failed, &$nextCursor) {
            foreach ($absensiList as $absensi) {
                $nextCursor = (int) $absensi->id;

                try {
                    $selisih = RealtimeAbsensi::selisihMenit(
                        $absensi->tgl_absen,
                        $absensi->pagi,
                        $absensi->sore
                    );
                    $hapusJamPulang = $absensi->sore !== null
                        && $selisih < RealtimeAbsensi::MINIMUM_DURASI_PULANG;

                    if ($hapusJamPulang) {
                        $selisih = 0;
                    }

                    $isSantri = false;
                    $user = User::with('type')->find($absensi->users_id);
                    if ($user && $user->type && strcasecmp($user->type->nama, 'santri') === 0) {
                        $isSantri = true;
                    }

                    if ($isSantri) {
                        $kategori = $kategoriList->firstWhere('kode', 'SANTRI') ?: Kategori::where('kode', 'SANTRI')->first();
                    } else {
                        $filteredKategoriList = $kategoriList->where('kode', '!=', 'SANTRI');
                        $kategori = RealtimeAbsensi::kategoriUntukDurasi($selisih, $filteredKategoriList);
                    }

                    $kategoriId = $kategori ? (int) $kategori->id : $absensi->kategori_id;

                    if (
                        $hapusJamPulang
                        || $absensi->selisih === null
                        || (int) $absensi->selisih !== $selisih
                        || (int) $absensi->kategori_id !== $kategoriId
                    ) {
                        $perubahan = [
                            'selisih' => $selisih,
                            'kategori_id' => $kategoriId,
                        ];

                        if ($hapusJamPulang) {
                            $perubahan['sore'] = null;
                        }

                        DB::table('absensi')
                            ->where('id', $absensi->id)
                            ->update($perubahan);
                        $updated++;
                    }
                } catch (\Throwable $exception) {
                    report($exception);
                    $failed++;
                }
            }
        });

        $processed = $absensiList->count();
        $done = $processed === 0 || $nextCursor >= $maxId;

        return response()->json([
            'status' => true,
            'total' => $total,
            'max_id' => $maxId,
            'next_cursor' => $nextCursor,
            'batch_processed' => $processed,
            'batch_updated' => $updated,
            'batch_failed' => $failed,
            'done' => $done,
        ]);
    }
}
