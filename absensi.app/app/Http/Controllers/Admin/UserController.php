<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use App\Models\Departemen;
use App\Models\Type;
use Illuminate\Http\Request;
use App\Http\Services\BulkData;
use App\Imports\MainUserImport;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UserController extends Controller
{
    public function index()
    {
        $isStaff    = auth()->user()->isStaff();
        $role       = $isStaff
            ? Role::where('akses', 'user')->get()
            : Role::all();
        $departemen = Departemen::all();
        $type       = Type::all();
        return view('admin.user.index', compact('role', 'departemen', 'type', 'isStaff'));
    }

    public function data(Request $request)
    {
        $isStaff = $request->user()->isStaff();
        $search = request('search.value');
        $data   = User::select('*');
        return DataTables::of($data)
            ->filter(function ($query) use ($search, $request) {
                $query->when($request->role_id != "*", function ($query) use ($request) {
                    $query->where('role_id', $request->role_id);
                });
                $query->when($request->departemen_id != "*", function ($query) use ($request) {
                    $query->where('departemen_id', $request->departemen_id);
                });
                $query->where(function ($query) use ($search) {
                    $query->orWhere('username', 'LIKE', "%$search%");
                    $query->orWhere('name', 'LIKE', "%$search%");
                    $query->orWhere('email', 'LIKE', "%$search%");
                    $query->orWhere('jenis_kelamin', 'LIKE', "%$search%");
                });
            })
            ->editColumn('name', function ($row) {
                $assetsPath = asset('photo') . '/';
                $detailUrl = route('admin.user.absensi.detail', $row->id);

                if ($row->photo) {
                    // Untuk photo gambar
                    $output = '<img src="' . $assetsPath . $row->photo . '" alt="Avatar" class="rounded-circle">';
                } else {
                    // Untuk photo badge dengan inisial
                    $stateNum = rand(0, 5);
                    $states   = ['success', 'danger', 'warning', 'info', 'primary', 'secondary'];
                    $state    = $states[$stateNum];

                    // Ambil inisial dari nama lengkap
                    preg_match_all('/\b\w/', $row->name, $matches);
                    $initials = isset($matches[0]) ? strtoupper($matches[0][0] . end($matches[0])) : '';

                    $output = '<span class="avatar-initial rounded-circle bg-label-' . $state . '">' . $initials . '</span>';
                }

                $roleName = optional($row->role)->akses ?? 'Unknown';
                $departemenName = optional($row->departemen)->nama ?? 'Unknown';
                $typeName = optional($row->type)->nama;
                $details = $roleName . ' - ' . $departemenName . ($typeName ? ' (' . $typeName . ')' : '');

                return '
                    <a href="' . $detailUrl . '" class="d-flex justify-content-start align-items-center user-name text-body">
                        <div class="avatar-wrapper">
                            <div class="avatar me-2">
                                ' . $output . '
                            </div>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="emp_name text-truncate">' . htmlspecialchars($row->name) . '</span>
                            <small class="emp_post text-truncate text-muted">' . htmlspecialchars($row->email ?? 'Unknown') . '</small>
                            <small class="emp_post text-truncate text-muted">' . htmlspecialchars($details) . '</small>
                        </div>
                    </a>
                ';
            })
            ->addColumn('action', function ($row) use ($isStaff) {
                $detailButton = '
                    <a class="dropdown-item" href="' . route('admin.user.absensi.detail', $row->id) . '">
                        Detail Absensi
                    </a>';

                if ($isStaff && ! $row->hasRole('user')) {
                    return '<a class="btn btn-sm btn-primary" href="' . route('admin.user.absensi.detail', $row->id) . '">Detail Absensi</a>';
                }

                $actionButtons = '
                        <div class="d-inline-block">
                            <a href="javascript:;" class="btn btn-sm btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="ti ti-dots-vertical ti-md"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end m-0">
                                <li>
                                    ' . $detailButton . '
                                </li>
                                <div class="dropdown-divider"></div>
                                <li>
                                    <button class="dropdown-item edit-record-button"
                                        data-id="' . $row->id . '"
                                        data-username="' . e($row->username) . '"
                                        data-name="' . e($row->name) . '"
                                        data-email="' . e($row->email) . '"
                                        data-photo="' . e($row->photo) . '"
                                        data-role_id="' . $row->role_id . '"
                                        data-departemen_id="' . $row->departemen_id . '"
                                        data-type_id="' . $row->type_id . '"
                                        data-jenis_kelamin="' . e($row->jenis_kelamin) . '"
                                        >Edit</button></li>
                                    <div class="dropdown-divider"></div>
                                <li>
                                    <form class="form-delete-record">
                                    ' . method_field('DELETE') . csrf_field() . '
                                        <input type="hidden" name="id" value="' . $row->id . '">
                                        <input type="hidden" name="name" value="' . e($row->name) . '">
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
            DB::beginTransaction();
            $isStaff = $request->user()->isStaff();

            $request->validate([
                'id'               => 'required|integer|unique:users,id',
                'username'         => 'required|string|max:255|unique:users',
                'name'             => 'required|string|max:255',
                'email'            => 'nullable|email|max:255|unique:users',
                'jenis_kelamin'    => 'required|in:Laki-laki,Perempuan,*',
                'role_id'          => $isStaff ? 'nullable' : 'required|exists:role,id',
                'departemen_id'    => 'nullable|exists:departemen,id',
                'type_id'          => 'nullable|exists:type,id',
                'password'         => 'required|string|min:8|max:255',
                'confirm_password' => 'required|same:password',
                'upload_photo'     => 'nullable|mimes:jpeg,png,jpg,gif,webp,ico|max:' . BulkData::maxSizeUpload,
                'photo'            => 'required_with:upload_photo',
            ], [
                'username.unique'           => 'The username is already taken. Please choose another one.',
                'email.unique'              => 'The email has already been registered. Please use a different email.',
                'confirm_password.same'     => 'Password and Confirm Password must match.',
                'confirm_password.required' => 'The confirm password field is required.',
                'photo.required_with'       => 'Photo is required when upload photo is provided.',
            ]);

            $roleId = $isStaff
                ? Role::where('akses', 'user')->value('id')
                : $request->role_id;

            if (! $roleId) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'role_id' => 'Role user belum tersedia. Hubungi administrator.',
                ]);
            }

            $user = new User();
            if ($request->photo) {
                $imageData = $request->photo;

                // Extract the MIME type and the base64-encoded image data
                preg_match('#^data:image/(\w+);base64,#i', $imageData, $matches);

                if (isset($matches[1])) {
                    $extension = $matches[1]; // Extract the file extension (e.g., png, jpeg, gif)

                    $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $imageData);
                    $imageData = str_replace(' ', '+', $imageData);
                    $image     = base64_decode($imageData);

                    $fileName = uniqid() . '.' . $extension;

                    $path = public_path('photo/' . $fileName);
                    file_put_contents($path, $image);

                    $user->photo = $fileName;
                }
            }

            $user->id            = $request->id;

            $user->username      = $request->username;
            $user->name          = $request->name;
            $user->email         = $request->email;
            $user->jenis_kelamin = $request->jenis_kelamin;
            $user->role_id       = $roleId;
            $user->departemen_id = $request->departemen_id ?: null;
            $user->type_id       = $request->type_id ?: null;
            if ($request->password) {
                $user->password = Hash::make($request->password);
            }
            $user->save();

            DB::commit();
            return [
                'status'  => true,
                'type'    => 'success',
                'message' => 'Success',
            ];
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'type'    => 'error',
                'message' => implode('<br><br>', array_map('implode', $e->errors())),
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
            return [
                'status'  => false,
                'type'    => 'error',
                'message' => 'Gagal menyimpan user.',
            ];
        }
    }

    public function update(Request $request)
    {
        $user = User::findOrFail($request->id);

        if ($request->user()->isStaff() && ! $user->hasRole('user')) {
            abort(403, 'Staff hanya dapat mengubah akun dengan role user.');
        }

        try {
            DB::beginTransaction();
            $isStaff = $request->user()->isStaff();

            $request->validate([
                'id'               => 'required|exists:users,id',
                'username'         => 'required|unique:users,username,' . $user->id,
                'name'             => 'required',
                'email'            => 'nullable|email|unique:users,email,' . $user->id,
                'jenis_kelamin'    => 'required',
                'role_id'          => 'nullable',
                'departemen_id'    => 'nullable',
                'type_id'          => 'nullable|exists:type,id',
                'password'         => 'nullable',
                'confirm_password' => 'nullable|same:password',
                'upload_photo'     => 'nullable|mimes:jpeg,png,jpg,gif,webp,ico|max:' . BulkData::maxSizeUpload,
                'photo'            => 'required_with:upload_photo',
            ], [
                'username.unique'           => 'The username is already taken. Please choose another one.',
                'email.unique'              => 'The email has already been registered. Please use a different email.',
                'confirm_password.same'     => 'Password and Confirm Password must match.',
                'photo.required_with'       => 'Photo is required when upload photo is provided.',
            ]);

            if ($request->photo) {
                $imageData = $request->photo;

                // Extract the MIME type and the base64-encoded image data
                preg_match('#^data:image/(\w+);base64,#i', $imageData, $matches);

                if (isset($matches[1])) {
                    $extension = $matches[1]; // Extract the file extension (e.g., png, jpeg, gif)

                    $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $imageData);
                    $imageData = str_replace(' ', '+', $imageData);
                    $image     = base64_decode($imageData);

                    $fileName = uniqid() . '.' . $extension;

                    $path = public_path('photo/' . $fileName);
                    file_put_contents($path, $image);

                    $user->photo = $fileName;
                }
            }

            $user->username      = $request->username;
            $user->name          = $request->name;
            $user->email         = $request->email;
            $user->role_id       = $isStaff
                ? Role::where('akses', 'user')->value('id')
                : ($request->role_id ?: $user->role_id);
            $user->departemen_id = $request->departemen_id ?: $user->departemen_id;
            $user->type_id       = $request->type_id ?: null;
            $user->jenis_kelamin = $request->jenis_kelamin;
            if ($request->password != null && $request->password != '') {
                $user->password = Hash::make($request->password);
            }
            $user->save();

            DB::commit();
            return [
                'status'  => true,
                'type'    => 'success',
                'message' => 'Success',
            ];
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
        $data = User::findOrFail($request->id);

        if ($request->user()->isStaff() && ! $data->hasRole('user')) {
            abort(403, 'Staff hanya dapat menghapus akun dengan role user.');
        }

        try {
            DB::beginTransaction();
            $request->validate([
                'id' => 'required',
            ]);

            if ($data->photo) {
                $path = public_path('photo/' . $data->photo);
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            $data->delete();

            DB::commit();
            return [
                'status'  => true,
                'type'    => 'success',
                'message' => 'Success',
                'request' => $request->all(),
            ];
        } catch (\Throwable $th) {
            DB::rollback();
            return [
                'status'  => false,
                'type'    => 'error',
                'message' => $th->getMessage(),
                'request' => $request->all(),
            ];
        }
    }

    public function import(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv',
            ]);

            $mainUserImport = new MainUserImport($request);
            Excel::import($mainUserImport, $request->file('file'));

            $result = $mainUserImport->getResult();

            return [
                'status'  => true,
                'type'    => 'success',
                'data'    => $result,
                'message' => 'Success import ' . $result['success'] . ' data dari ' . $result['max'] . ' error: ' . $result['error'],
            ];
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status'  => false,
                'type'    => 'error',
                'message' => implode('<br><br>', array_map('implode', $e->errors())),
                'req'     => $request->all(),
            ]);
        } catch (\Throwable $th) {
            return [
                'status'  => false,
                'type'    => 'error',
                'message' => $th->getMessage(),
            ];
        }
    }

}
