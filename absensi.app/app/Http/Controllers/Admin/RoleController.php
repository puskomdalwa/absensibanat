<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Services\BulkData;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    public function index()
    {
        $isSuperAdmin = auth()->user()->isSuperAdmin();
        $query = Role::query();
        if (! $isSuperAdmin) {
            $query->where('akses', '!=', 'superadmin');
        }
        $role = $query->get();
        return view('admin.role.index', compact('role'));
    }

    public function data(Request $request)
    {
        $isSuperAdmin = $request->user()->isSuperAdmin();
        $search = request('search.value');
        $data = Role::query();
        if (! $isSuperAdmin) {
            $data->where('akses', '!=', 'superadmin');
        }

        return DataTables::of($data)
            ->filter(function ($query) use ($search, $request) {
                $query->where(function ($query) use ($search) {
                    $query->orWhere('akses', 'LIKE', "%$search%");
                });
            })
            ->addColumn('action', function ($row) use ($isSuperAdmin) {
                if (! $isSuperAdmin) {
                    return '<span class="badge bg-label-secondary"><i class="ti ti-lock ti-xs me-1"></i>Read Only</span>';
                }
                $actionButtons = '
                        <div class="d-inline-block">
                            <a href="javascript:;" class="btn btn-sm btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="ti ti-dots-vertical ti-md"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end m-0">
                                <li>
                                    <button class="dropdown-item edit-record-button"
                                        data-id="' . $row->id . '"
                                        data-akses="' . $row->akses . '"
                                        >Edit</button></li>
                                    <div class="dropdown-divider"></div>
                                <li>
                                    <form class="form-delete-record">
                                    ' . method_field('DELETE') . csrf_field() . '
                                        <input type="hidden" name="id" value="' . $row->id . '">
                                        <input type="hidden" name="name" value="' . $row->akses . '">
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
        if (! auth()->user()->isSuperAdmin()) {
            abort(403, 'Akses Ditolak. Modul Role hanya dapat dikelola oleh Superadmin.');
        }
        try {
            DB::beginTransaction();
            $request->validate([
                'akses' => 'required|unique:role',
            ]);
            
            $role = new Role();
            $role->akses = $request->akses;
            $role->save();

            DB::commit();    
            return [
                'status' => true,
                'type' => 'success',
                'message' => 'Success'
            ];
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'type' => 'error',
                'message' => implode('<br><br>', array_map('implode', $e->errors())),
                'req' => $request->all()
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
            return [
                'status' => false,
                'type' => 'error',
                'message' => $th->getMessage()
            ];
        }
    }

    public function update(Request $request)
    {
        if (! auth()->user()->isSuperAdmin()) {
            abort(403, 'Akses Ditolak. Modul Role hanya dapat dikelola oleh Superadmin.');
        }
        try {
            DB::beginTransaction();
            $role = Role::findOrFail($request->id);

            $request->validate([
                'id' => 'required|exists:role,id',
                'akses' => 'required|unique:role,akses,' . $role->id,
            ]);

            $role->akses = $request->akses;
            $role->save();

            DB::commit();
            return [
                'status' => true,
                'type' => 'success',
                'message' => 'Success'
            ];
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'type' => 'error',
                'message' => implode('<br><br>', array_map('implode', $e->errors())),
                'req' => $request->all()
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
            return [
                'status' => false,
                'type' => 'error',
                'message' => $th->getMessage()
            ];
        }
    }

    public function delete(Request $request)
    {
        if (! auth()->user()->isSuperAdmin()) {
            abort(403, 'Akses Ditolak. Modul Role hanya dapat dikelola oleh Superadmin.');
        }
        try {
            DB::beginTransaction();
            $request->validate([
                'id' => 'required',
            ]);

            $data = Role::findOrFail($request->id);
            $data->delete();

            DB::commit();
            return [
                'status' => true,
                'type' => 'success',
                'message' => 'Success',
                'request' => $request->all(),
            ];
        } catch (\Throwable $th) {
            DB::rollback();
            return [
                'status' => false,
                'type' => 'error',
                'message' => $th->getMessage(),
                'request' => $request->all(),
            ];
        }
    }
}
