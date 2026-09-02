<?php

namespace App\Http\Controllers\Admin;

use App\Models\Departemen;
use Illuminate\Http\Request;
use App\Http\Services\BulkData;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class DepartemenController extends Controller
{
    public function index()
    {
        $departemen = Departemen::all();
        return view('admin.departemen.index', compact('departemen'));
    }

    public function data(Request $request)
    {
        $search = request('search.value');
        $data = Departemen::select('*');
        return DataTables::of($data)
            ->filter(function ($query) use ($search, $request) {
                $query->where(function ($query) use ($search) {
                    $query->orWhere('kode', 'LIKE', "%$search%");
                    $query->orWhere('nama', 'LIKE', "%$search%");
                });
            })
            ->addColumn('action', function ($row) {
                $actionButtons = '
                        <div class="d-inline-block">
                            <a href="javascript:;" class="btn btn-sm btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="ti ti-dots-vertical ti-md"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end m-0">
                                <li>
                                    <button class="dropdown-item edit-record-button"
                                        data-id="' . $row->id . '"
                                        data-kode="' . $row->kode . '"
                                        data-nama="' . $row->nama . '"
                                        >Edit</button></li>
                                    <div class="dropdown-divider"></div>
                                <li>
                                    <form class="form-delete-record">
                                    ' . method_field('DELETE') . csrf_field() . '
                                        <input type="hidden" name="id" value="' . $row->id . '">
                                        <input type="hidden" name="name" value="' . $row->nama . '">
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
            \DB::beginTransaction();
            $request->validate([
                'kode' => 'required|unique:departemen',
                'nama' => 'required',
            ]);
            
            $departemen = new Departemen();
            $departemen->kode = $request->kode;
            $departemen->nama = $request->nama;
            $departemen->save();

            \DB::commit();    
            return [
                'status' => true,
                'type' => 'success',
                'message' => 'Success'
            ];
        } catch (\Illuminate\Validation\ValidationException $e) {
            \DB::rollBack();
            return response()->json([
                'status' => false,
                'type' => 'error',
                'message' => implode('<br><br>', array_map('implode', $e->errors())),
                'req' => $request->all()
            ]);
        } catch (\Throwable $th) {
            \DB::rollback();
            return [
                'status' => false,
                'type' => 'error',
                'message' => $th->getMessage()
            ];
        }
    }

    public function update(Request $request)
    {
        try {
            \DB::beginTransaction();
            $departemen = Departemen::findOrFail($request->id);

            $request->validate([
                'id' => 'required|exists:departemen,id',
                'kode' => 'required|unique:departemen,kode,' . $departemen->id,
                'nama' => 'required',
            ]);

            $departemen->kode = $request->kode;
            $departemen->nama = $request->nama;
            $departemen->save();

            \DB::commit();
            return [
                'status' => true,
                'type' => 'success',
                'message' => 'Success'
            ];
        } catch (\Illuminate\Validation\ValidationException $e) {
            \DB::rollBack();
            return response()->json([
                'status' => false,
                'type' => 'error',
                'message' => implode('<br><br>', array_map('implode', $e->errors())),
                'req' => $request->all()
            ]);
        } catch (\Throwable $th) {
            \DB::rollback();
            return [
                'status' => false,
                'type' => 'error',
                'message' => $th->getMessage()
            ];
        }
    }

    public function delete(Request $request)
    {
        try {
            \DB::beginTransaction();
            $request->validate([
                'id' => 'required',
            ]);

            $data = Departemen::findOrFail($request->id);
            $data->delete();

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
}
