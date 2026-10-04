<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use App\Models\Departemen;
use App\Models\Type;
use App\Models\Device;
use App\Models\FingerspotDeviceUser;
use App\Models\Absensi;
use App\Models\Keterangan;
use App\Http\Services\Fingerspot;
use Illuminate\Http\Request;
use App\Http\Services\BulkData;
use App\Imports\MainUserImport;
use App\Imports\UserImport;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UserController extends Controller
{
    public function index()
    {
        $authUser   = auth()->user();
        $isStaff    = $authUser->isStaff();
        if ($authUser->isSuperAdmin()) {
            $role = Role::all();
        } elseif ($authUser->hasRole('admin')) {
            $role = Role::whereIn('akses', ['user', 'staff'])->get();
        } else {
            $role = Role::where('akses', 'user')->get();
        }
        $departemen = Departemen::all();
        $type       = Type::all();
        return view('admin.user.index', compact('role', 'departemen', 'type', 'isStaff'));
    }

    public function data(Request $request)
    {
        $authUser     = $request->user();
        $isSuperAdmin = $authUser->isSuperAdmin();
        $isStaff      = $authUser->isStaff();
        $search       = request('search.value');
        $data         = User::select('*');

        if (! $isSuperAdmin) {
            // Admin dan staff tidak boleh melihat user dengan role superadmin
            $data->whereDoesntHave('role', function ($q) {
                $q->where('akses', 'superadmin');
            });
        }
        if ($isStaff) {
            // Staff juga tidak boleh melihat admin
            $data->whereDoesntHave('role', function ($q) {
                $q->where('akses', 'admin');
            });
        }
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
            ->addColumn('action', function ($row) use ($isStaff, $isSuperAdmin) {
                $detailButton = '
                    <a class="dropdown-item" href="' . route('admin.user.absensi.detail', $row->id) . '">
                        Detail Absensi
                    </a>';

                if (! $isSuperAdmin && $row->hasRole('superadmin')) {
                    return '';
                }

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
            ->addColumn('checkbox', function ($row) use ($isStaff, $isSuperAdmin, $request) {
                if ($row->id == $request->user()->id) {
                    return '<div class="text-center"><input type="checkbox" class="form-check-input user-row-checkbox" disabled title="Akun Anda sendiri"></div>';
                }
                if (! $isSuperAdmin && $row->hasRole('superadmin')) {
                    return '<div class="text-center"><input type="checkbox" class="form-check-input user-row-checkbox" disabled title="Superadmin tidak dapat dihapus"></div>';
                }
                if ($isStaff && ! $row->hasRole('user')) {
                    return '<div class="text-center"><input type="checkbox" class="form-check-input user-row-checkbox" disabled title="Staff hanya dapat menghapus role user"></div>';
                }
                return '<div class="text-center"><input type="checkbox" class="form-check-input user-row-checkbox" value="' . $row->id . '" data-name="' . e($row->name) . '"></div>';
            })
            ->rawColumns(['checkbox', 'action', 'name'])
            ->toJson();
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $isStaff = $request->user()->isStaff();

            $request->validate([
                'id'               => 'nullable|integer|unique:users,id',
                'username'         => 'required|string|max:255|unique:users',
                'name'             => 'required|string|max:255',
                'email'            => 'nullable|email|max:255|unique:users,email',
                'jenis_kelamin'    => 'required|in:Laki-laki,Perempuan,*',
                'role_id'          => $isStaff ? 'nullable' : 'required|exists:role,id',
                'departemen_id'    => 'nullable|exists:departemen,id',
                'type_id'          => 'nullable|exists:type,id',
                'password'         => 'required|string|min:6|max:255',
                'confirm_password' => 'required|same:password',
                'upload_photo'     => 'nullable|mimes:jpeg,png,jpg,gif,webp,ico|max:' . BulkData::maxSizeUpload,
                'photo'            => 'required_with:upload_photo',
            ], [
                'id.unique'                 => 'ID civitas tersebut sudah terdaftar. Silakan gunakan ID lain atau kosongkan untuk otomatis.',
                'id.integer'                => 'ID civitas harus berupa angka.',
                'username.required'         => 'Username wajib diisi.',
                'username.unique'           => 'Username sudah digunakan. Silakan gunakan username lain.',
                'name.required'             => 'Nama civitas wajib diisi.',
                'email.email'               => 'Format email tidak valid. Gunakan format seperti nama@domain.com.',
                'email.unique'              => 'Email tersebut sudah terdaftar. Silakan gunakan email lain.',
                'password.required'         => 'Password wajib diisi.',
                'password.min'              => 'Password minimal 6 karakter.',
                'confirm_password.same'     => 'Konfirmasi password harus sama dengan password.',
                'confirm_password.required' => 'Konfirmasi password wajib diisi.',
                'photo.required_with'       => 'Foto wajib disertakan jika memilih file foto.',
            ]);

            if ($isStaff) {
                $roleId = Role::where('akses', 'user')->value('id');
            } elseif ($request->user()->hasRole('admin')) {
                $chosenRole = Role::find($request->role_id);
                if (! $chosenRole || ! in_array(strtolower($chosenRole->akses), ['user', 'staff'])) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'role_id' => 'Admin hanya diperbolehkan membuat pengguna dengan role user atau staff.',
                    ]);
                }
                $roleId = $chosenRole->id;
            } else {
                $roleId = $request->role_id;
            }

            if (! $roleId) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'role_id' => 'Role belum tersedia atau tidak valid. Hubungi administrator.',
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

            if ($request->filled('id')) {
                $user->id = $request->id;
            } else {
                $nextId = (User::max('id') ?? 0) + 1;
                $user->id = $nextId;
            }

            $user->username      = trim($request->username);
            $user->name          = trim($request->name);

            // Flexible email: if provided, use it. If left blank, generate a valid fallback to satisfy NOT NULL
            if ($request->filled('email')) {
                $user->email = trim($request->email);
            } else {
                $cleanUser = preg_replace('/[^a-zA-Z0-9_\.]/', '', $user->username);
                $fallbackEmail = strtolower($cleanUser) . '@dalwa.ac.id';
                if (User::where('email', $fallbackEmail)->exists()) {
                    $fallbackEmail = strtolower($cleanUser) . '_' . ($user->id ?? time()) . '@dalwa.ac.id';
                }
                $user->email = $fallbackEmail;
            }

            $user->jenis_kelamin = $request->jenis_kelamin;
            $user->role_id       = $roleId;
            $user->departemen_id = $request->departemen_id ?: null;
            $user->type_id       = $request->type_id ?: null;
            if ($request->password) {
                $user->password = Hash::make($request->password);
            }
            $user->save();

            DB::commit();
            return response()->json([
                'status'  => true,
                'type'    => 'success',
                'message' => 'User baru berhasil ditambahkan.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'type'    => 'error',
                'message' => implode('<br>', array_map('implode', $e->errors())),
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
            \Log::error('Error storing user: ' . $th->getMessage(), ['trace' => $th->getTraceAsString()]);
            return response()->json([
                'status'  => false,
                'type'    => 'error',
                'message' => 'Gagal menyimpan user: ' . $th->getMessage(),
            ]);
        }
    }

    public function update(Request $request)
    {
        $authUser     = $request->user();
        $isSuperAdmin = $authUser->isSuperAdmin();
        $isAdmin      = $authUser->hasRole('admin');
        $isStaff      = $authUser->isStaff();

        $user = User::findOrFail($request->id);

        if (! $isSuperAdmin && $user->hasRole('superadmin')) {
            abort(403, 'Anda tidak memiliki hak untuk mengubah akun Superadmin.');
        }

        if ($isStaff && ! $user->hasRole('user')) {
            abort(403, 'Staff hanya dapat mengubah akun dengan role user.');
        }

        if ($isAdmin && $request->filled('role_id')) {
            $chosenRole = Role::find($request->role_id);
            if ($chosenRole && ! in_array(strtolower($chosenRole->akses), ['user', 'staff'])) {
                return response()->json([
                    'status' => false,
                    'type'   => 'error',
                    'message' => 'Admin hanya diperbolehkan menetapkan role user atau staff.',
                ], 422);
            }
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

            $user->username      = trim($request->username);
            $user->name          = trim($request->name);
            if ($request->filled('email')) {
                $user->email = trim($request->email);
            }
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
        $authUser     = $request->user();
        $isSuperAdmin = $authUser->isSuperAdmin();
        $isStaff      = $authUser->isStaff();
        $data         = User::findOrFail($request->id);

        if ($authUser->id == $data->id) {
            return response()->json([
                'status'  => false,
                'type'    => 'error',
                'message' => 'Anda tidak dapat menghapus akun Anda sendiri.',
            ], 422);
        }

        if (! $isSuperAdmin && $data->hasRole('superadmin')) {
            abort(403, 'Akun Superadmin tidak dapat dihapus.');
        }

        if ($isStaff && ! $data->hasRole('user')) {
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
                    @unlink($path);
                }
            }

            // Hapus riwayat presensi dan keterangan terkait terlebih dahulu
            $absensiIds = Absensi::where('users_id', $data->id)->pluck('id');
            if ($absensiIds->isNotEmpty()) {
                Keterangan::whereIn('absensi_id', $absensiIds)->delete();
                Absensi::whereIn('id', $absensiIds)->delete();
            }

            // Hapus token API jika ada
            DB::table('personal_access_tokens')
                ->where('tokenable_type', User::class)
                ->where('tokenable_id', $data->id)
                ->delete();

            // Cek device tempat user terdaftar sebelum menghapus cache lokal
            $enrolledDevices = FingerspotDeviceUser::where('pin', (string)$data->id)->pluck('cloud_id')->all();
            FingerspotDeviceUser::where('pin', (string)$data->id)->delete();

            $data->delete();

            DB::commit();

            // Dispatch delete command ke mesin fisik
            try {
                if (!empty($enrolledDevices)) {
                    foreach ($enrolledDevices as $cloudId) {
                        Fingerspot::deleteUserInfo(null, $cloudId, (string)$data->id);
                    }
                } else {
                    $devices = Device::all();
                    foreach ($devices as $dev) {
                        Fingerspot::deleteUserInfo(null, $dev->cloud_id, (string)$data->id);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('[UserController::delete] Failed to send delete_userinfo to device: ' . $e->getMessage());
            }

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

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer',
        ]);

        @set_time_limit(180);

        $currentUser = $request->user();
        $targetIds = array_diff($request->ids, [$currentUser->id]);

        if (empty($targetIds)) {
            return response()->json([
                'status'  => false,
                'type'    => 'error',
                'message' => 'Tidak ada akun valid yang dapat dihapus (Anda tidak dapat menghapus akun Anda sendiri).',
            ], 422);
        }

        try {
            DB::beginTransaction();

            $query = User::whereIn('id', $targetIds);
            if (! $currentUser->isSuperAdmin()) {
                $superadminRoleId = Role::where('akses', 'superadmin')->value('id');
                if ($superadminRoleId) {
                    $query->where('role_id', '!=', $superadminRoleId);
                }
            }
            if ($currentUser->isStaff()) {
                $roleUserId = Role::where('akses', 'user')->value('id');
                $query->where('role_id', $roleUserId);
            }

            $users = $query->get();

            if ($users->isEmpty()) {
                DB::rollBack();
                return response()->json([
                    'status'  => false,
                    'type'    => 'error',
                    'message' => 'Tidak ada akun pengguna yang memenuhi syarat untuk dihapus.',
                ], 422);
            }

            $validUserIds = $users->pluck('id')->all();
            $deletedPins = array_map('strval', $validUserIds);

            // 1. Hapus file foto
            foreach ($users as $user) {
                if ($user->photo) {
                    $path = public_path('photo/' . $user->photo);
                    if (file_exists($path)) {
                        @unlink($path);
                    }
                }
            }

            // 2. Hapus riwayat presensi dan keterangan secara batch
            $absensiIds = Absensi::whereIn('users_id', $validUserIds)->pluck('id');
            if ($absensiIds->isNotEmpty()) {
                Keterangan::whereIn('absensi_id', $absensiIds)->delete();
                Absensi::whereIn('id', $absensiIds)->delete();
            }

            // 3. Hapus token personal access tokens jika ada
            DB::table('personal_access_tokens')
                ->where('tokenable_type', User::class)
                ->whereIn('tokenable_id', $validUserIds)
                ->delete();

            // 4. Cari mesin tempat user terdaftar sebelum menghapus cache fingerspot lokal
            $enrolledDeviceUsers = FingerspotDeviceUser::whereIn('pin', $deletedPins)
                ->select('cloud_id', 'pin')
                ->get();

            if (!empty($deletedPins)) {
                FingerspotDeviceUser::whereIn('pin', $deletedPins)->delete();
            }

            // 5. Hapus akun pengguna secara batch
            $deletedCount = User::whereIn('id', $validUserIds)->delete();

            DB::commit();

            // 6. Dispatch delete_userinfo ke mesin fisik untuk user yang memang terdaftar di mesin
            try {
                if ($enrolledDeviceUsers->isNotEmpty()) {
                    foreach ($enrolledDeviceUsers as $edu) {
                        Fingerspot::deleteUserInfo(null, $edu->cloud_id, (string)$edu->pin);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('[UserController::bulkDelete] Failed to dispatch delete_userinfo to devices: ' . $e->getMessage());
            }

            return response()->json([
                'status'        => true,
                'type'          => 'success',
                'message'       => "Berhasil menghapus {$deletedCount} akun pengguna.",
                'deleted_count' => $deletedCount,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Bulk delete users error: ' . $th->getMessage());
            return response()->json([
                'status'  => false,
                'type'    => 'error',
                'message' => 'Terjadi kesalahan saat menghapus pengguna: ' . $th->getMessage(),
            ], 500);
        }
    }

    public function import(Request $request)
    {
        $tempPath = null;
        try {
            $request->validate([
                'file' => 'required|file',
            ], [
                'file.required' => 'Silakan pilih file Excel / CSV terlebih dahulu.',
                'file.file'     => 'File yang diunggah tidak valid.',
            ]);

            $file = $request->file('file');
            if (!$file || !$file->isValid()) {
                throw new \Exception('File upload tidak valid atau gagal diunggah.');
            }

            $extension = strtolower($file->getClientOriginalExtension() ?: 'xlsx');
            if (!in_array($extension, ['xlsx', 'xls', 'csv', 'txt'])) {
                throw new \Exception('Format file harus berupa .xlsx, .xls, atau .csv (terdeteksi: .' . $extension . ').');
            }

            // Move uploaded file to a concrete physical storage path to prevent "Path cannot be empty" on Windows
            $tempDir = storage_path('app/temp-imports');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0777, true);
            }

            $fileName = 'import_user_' . time() . '_' . uniqid() . '.' . $extension;
            $file->move($tempDir, $fileName);
            $tempPath = $tempDir . DIRECTORY_SEPARATOR . $fileName;

            if (!file_exists($tempPath) || filesize($tempPath) === 0) {
                throw new \Exception('File sementara gagal dibuat atau kosong.');
            }

            // Robust reader that auto-detects real format (Xlsx, Csv, Html, Xls, etc.) regardless of extension
            $rows = $this->extractSpreadsheetRows($tempPath);

            if (empty($rows)) {
                throw new \Exception('File tidak berisi data atau format tidak dapat dibaca.');
            }

            $userImport = new UserImport($request);
            $userImport->collection(collect($rows));

            $result = $userImport->getResult();

            $msgParts = [];
            $msgParts[] = "Berhasil memproses {$result['success']} dari {$result['max']} baris.";
            if ($result['created'] > 0) {
                $msgParts[] = "Baru: {$result['created']}.";
            }
            if ($result['updated'] > 0) {
                $msgParts[] = "Diperbarui: {$result['updated']}.";
            }
            if (!empty($result['new_roles'])) {
                $uniqueRoles = array_unique($result['new_roles']);
                $msgParts[] = "Role baru dibuat: " . implode(', ', $uniqueRoles) . ".";
            }
            if (!empty($result['new_departemen'])) {
                $uniqueDept = array_unique($result['new_departemen']);
                $msgParts[] = "Departemen baru dibuat: " . implode(', ', $uniqueDept) . ".";
            }
            if ($result['error'] > 0) {
                $msgParts[] = "Gagal/Dilewati: {$result['error']} baris.";
            }

            return [
                'status'  => true,
                'type'    => 'success',
                'data'    => $result,
                'message' => implode(' ', $msgParts),
            ];
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status'  => false,
                'type'    => 'error',
                'message' => implode('<br><br>', array_map('implode', $e->errors())),
                'req'     => $request->all(),
            ]);
        } catch (\Throwable $th) {
            Log::error('[UserController Import Error] ' . $th->getMessage(), [
                'trace'     => $th->getTraceAsString(),
                'file'      => $th->getFile(),
                'line'      => $th->getLine(),
            ]);
            return [
                'status'  => false,
                'type'    => 'error',
                'message' => $th->getMessage(),
            ];
        } finally {
            if ($tempPath && file_exists($tempPath)) {
                @unlink($tempPath);
            }
        }
    }

    /**
     * Safely extract rows from any spreadsheet file (XLSX, XLS, CSV, HTML, TSV)
     * regardless of whether extension matches actual contents.
     */
    protected function extractSpreadsheetRows(string $filePath): array
    {
        try {
            $fileType = IOFactory::identify($filePath);
            $reader = IOFactory::createReader($fileType);

            if ($reader instanceof \PhpOffice\PhpSpreadsheet\Reader\Csv) {
                $reader->setInputEncoding('UTF-8');
                $handle = @fopen($filePath, 'r');
                if ($handle) {
                    $firstLine = fgets($handle);
                    fclose($handle);
                    $semiCount = substr_count($firstLine, ';');
                    $commaCount = substr_count($firstLine, ',');
                    $tabCount = substr_count($firstLine, "\t");

                    if ($semiCount > $commaCount && $semiCount > $tabCount) {
                        $reader->setDelimiter(';');
                    } elseif ($tabCount > $commaCount && $tabCount > $semiCount) {
                        $reader->setDelimiter("\t");
                    } else {
                        $reader->setDelimiter(',');
                    }
                }
            }

            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            return $sheet->toArray(null, true, true, false);
        } catch (\Throwable $e) {
            // Fallback plain CSV parser
            $rows = [];
            if (($handle = @fopen($filePath, 'r')) !== false) {
                $firstLine = fgets($handle);
                rewind($handle);
                $delim = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';
                while (($data = fgetcsv($handle, 0, $delim)) !== false) {
                    $rows[] = $data;
                }
                fclose($handle);
            }
            if (!empty($rows)) {
                return $rows;
            }
            throw $e;
        }
    }

    public function downloadTemplate(Request $request)
    {
        $format = strtolower($request->get('format', 'xlsx'));

        $columns = ['NO', 'KODE', 'NAMA DOSEN', 'L/P', 'TTL', 'E-MAIL', 'HP', 'STATUS', 'ROLE', 'DEPARTEMEN', 'KODE-DEPARTEMEN'];
        $sampleRows = [
            ['1', '80117', 'AISYAH', 'P', 'KABUPATEN PASURUAN, 20-07-1981', 'aisyah01@gmail.com', '081936926117', 'AKTIF', 'user', 'Dosen', '002'],
            ['2', '80118', 'AHMAD FAUZI', 'L', 'PASURUAN, 15-05-1985', 'ahmad.fauzi@example.com', '081234567890', 'AKTIF', 'staff', 'Staff', '003'],
        ];

        if ($format === 'csv') {
            $headers = [
                'Content-Type'        => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="template_import_user.csv"',
                'Pragma'              => 'no-cache',
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
                'Expires'             => '0',
            ];

            $callback = function () use ($columns, $sampleRows) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($file, $columns, ';');
                foreach ($sampleRows as $row) {
                    fputcsv($file, $row, ';');
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // Generate genuine .xlsx file
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Pengguna');
        $sheet->fromArray(array_merge([$columns], $sampleRows));

        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $tempDir = storage_path('app/temp-imports');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }
        $tempPath = $tempDir . '/template_import_user_' . time() . '.xlsx';
        $writer->save($tempPath);

        return response()->download($tempPath, 'template_import_user.xlsx')->deleteFileAfterSend(true);
    }

}
