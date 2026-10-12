<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LoginApiController extends Controller
{
    function auth(Request $request)
    {
        $rules = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (!Auth::attempt($rules)) {
            return response()->json([
                'message' => 'Username atau kata sandi tidak valid',
                'status' => false,
            ], 401);
        }

        $auth = Auth::user();

        $user = User::leftJoin('departemen', 'departemen.id', '=', 'users.departemen_id')
            ->leftJoin('role', 'role.id', '=', 'users.role_id')
            ->where('users.id', $auth->id)
            ->select(
                'users.id as id',
                'users.kode',
                'email',
                'username',
                'name',
                'jenis_kelamin',
                'photo',
                'departemen.nama as jabatan',
                'role.akses as role',
                'role.id as role_id'
            )->first();

        $token = $auth->createToken('token')->plainTextToken;

        return response()->json([
            'message' => 'Login success',
            'status' => true,
            'token' => $token,
            'data' => $user
        ]);
    }

    function me(Request $request)
    {
        $auth = $request->user();
        $user = User::leftJoin('departemen', 'departemen.id', '=', 'users.departemen_id')
            ->leftJoin('role', 'role.id', '=', 'users.role_id')
            ->where('users.id', $auth->id)
            ->select(
                'users.id as id',
                'users.kode',
                'email',
                'username',
                'name',
                'jenis_kelamin',
                'photo',
                'departemen.nama as jabatan',
                'role.akses as role',
                'role.id as role_id'
            )->first();

        return response()->json([
            'status' => true,
            'data' => $user
        ]);
    }

    function ubahPassword(Request $request)
    {
        try {
            $request->validate([
                'password_lama' => 'required',
                'password_baru' => 'required',
            ]);

            $user = Auth::user();

            if (!Hash::check($request->password_lama, $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Password lama salah'
                ], 422);
            }

            $user->password = Hash::make($request->password_baru);
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Berhasil mengganti kata sandi'
            ]);
        } catch (\Throwable $th) {
            Log::info($th);
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengubah kata sandi: ' . $th->getMessage()
            ], 500);
        }
    }

    function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'status' => true,
                'message' => 'Berhasil keluar'
            ]);
        } catch (\Throwable $th) {
            Log::info($th);
            return response()->json([
                'status' => false,
                'message' => 'Gagal logout'
            ], 500);
        }
    }

    function updateProfile(Request $request)
    {
        try {
            $user = $request->user() ?: Auth::user();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Sesi login telah kedaluwarsa. Silakan login kembali.'
                ], 401);
            }

            try {
                $request->validate([
                    'name' => 'sometimes|string|max:255',
                    'email' => [
                        'sometimes',
                        'email',
                        'max:255',
                        \Illuminate\Validation\Rule::unique('users', 'email')->ignore($user->id),
                    ],
                    'jenis_kelamin' => 'sometimes|nullable|string|in:Laki-laki,Perempuan,*',
                    'photo' => 'sometimes|nullable|string',
                ]);
            } catch (\Illuminate\Validation\ValidationException $ve) {
                $errors = $ve->validator->errors();
                $firstMsg = $errors->first();
                Log::warning('Update profile validation failed: ' . json_encode($errors->all()));
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak valid: ' . $firstMsg,
                    'errors' => $errors->all()
                ], 422);
            }

            if ($request->has('name') && !empty($request->name)) {
                $user->name = $request->name;
            }
            if ($request->has('email') && !empty($request->email)) {
                $user->email = $request->email;
            }
            if ($request->has('jenis_kelamin') && !empty($request->jenis_kelamin)) {
                $user->jenis_kelamin = $request->jenis_kelamin;
            }

            // Handle photo upload (base64)
            if ($request->has('photo') && !empty($request->photo)) {
                $base64 = $request->photo;
                if (preg_match('/^data:image\/(\w+);base64,/', $base64, $matches)) {
                    $ext = strtolower($matches[1]);
                    if ($ext === 'jpeg') $ext = 'jpg';
                    $base64 = substr($base64, strpos($base64, ',') + 1);
                } else {
                    $ext = 'png';
                }

                $imageData = base64_decode($base64);
                if ($imageData === false) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Format foto tidak valid'
                    ], 422);
                }

                $filename = 'profile_' . $user->id . '_' . time() . '.' . $ext;

                // Save to all target public directories
                $destDirs = [
                    public_path('photo'),
                    base_path('../public_html/photo'),
                    'c:/laragon/www/absensiBanat/absensibanat/public_html/photo',
                ];

                foreach ($destDirs as $dir) {
                    if (!empty($dir)) {
                        if (!is_dir($dir)) {
                            @mkdir($dir, 0777, true);
                        }
                        if (is_dir($dir)) {
                            if ($user->photo && file_exists($dir . '/' . $user->photo)) {
                                @unlink($dir . '/' . $user->photo);
                            }
                            @file_put_contents($dir . '/' . $filename, $imageData);
                        }
                    }
                }

                $user->photo = $filename;
            }

            $user->save();

            $updatedUser = User::leftJoin('departemen', 'departemen.id', '=', 'users.departemen_id')
                ->leftJoin('role', 'role.id', '=', 'users.role_id')
                ->where('users.id', $user->id)
                ->select(
                    'users.id as id',
                    'users.kode',
                    'email',
                    'username',
                    'name',
                    'jenis_kelamin',
                    'photo',
                    'departemen.nama as jabatan',
                    'role.akses as role',
                    'role.id as role_id'
                )->first();

            return response()->json([
                'status' => true,
                'message' => 'Profil berhasil diperbarui',
                'data' => $updatedUser
            ]);
        } catch (\Throwable $th) {
            Log::error('Update profile error: ' . $th->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal memperbarui profil: ' . $th->getMessage()
            ], 500);
        }
    }
}
