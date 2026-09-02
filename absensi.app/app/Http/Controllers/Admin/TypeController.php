<?php

namespace App\Http\Controllers\Admin;

use App\Models\Type;
use App\Models\User;
use App\Models\Departemen;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class TypeController extends Controller
{
    public function index()
    {
        $type = Type::all();
        return view('admin.type.index', compact('type'));
    }

    public function data(Request $request)
    {
        $search = request('search.value');
        $data = Type::select('*');
        return DataTables::of($data)
            ->filter(function ($query) use ($search, $request) {
                $query->where(function ($query) use ($search) {
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
                                    <a class="dropdown-item" href="' . route('admin.type.assign', $row->id) . '">
                                        Assign Users
                                    </a>
                                </li>
                                <div class="dropdown-divider"></div>
                                <li>
                                    <button class="dropdown-item edit-record-button"
                                        data-id="' . $row->id . '"
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
                'nama' => 'required|unique:type,nama',
            ]);
            
            $type = new Type();
            $type->nama = $request->nama;
            $type->save();

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
            $type = Type::findOrFail($request->id);

            $request->validate([
                'id' => 'required|exists:type,id',
                'nama' => 'required|unique:type,nama,' . $type->id,
            ]);

            $type->nama = $request->nama;
            $type->save();

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

            $data = Type::findOrFail($request->id);
            $data->delete();

            \DB::commit();
            return [
                'status'  => true,
                'type'    => 'success',
                'message' => 'Success',
                'request' => $request->all(),
            ];
        } catch (\Throwable $th) {
            \DB::rollback();
            return [
                'status'  => false,
                'type'    => 'error',
                'message' => $th->getMessage(),
                'request' => $request->all(),
            ];
        }
    }

    public function assign($id)
    {
        $type = Type::findOrFail($id);
        $users = User::with(['role', 'departemen', 'type'])->get();
        $departemen = Departemen::all();
        return view('admin.type.assign', compact('type', 'users', 'departemen'));
    }

    public function assignStore(Request $request, $id)
    {
        try {
            \DB::beginTransaction();
            $type = Type::findOrFail($id);

            $request->validate([
                'user_ids' => 'nullable|array',
                'user_ids.*' => 'exists:users,id',
            ]);

            $userIds = $request->input('user_ids', []);

            // 1. Unassign all users currently assigned to this type
            User::where('type_id', $type->id)->update(['type_id' => null]);

            // 2. Assign the newly selected users to this type
            if (!empty($userIds)) {
                User::whereIn('id', $userIds)->update(['type_id' => $type->id]);
            }

            \DB::commit();
            return redirect()->route('admin.type.index')->with('success', 'Batch assignment updated successfully.');
        } catch (\Throwable $th) {
            \DB::rollback();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
