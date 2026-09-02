<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Services\BulkData;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function index()
    {
        $user = \Auth::user();
        $role = Role::all();
        return view('admin.profile.index', compact('user', 'role'));
    }

    public function update(Request $request)
    {
        try {
            \DB::beginTransaction();
            $user = $request->user();

            $request->validate([
                'username' => 'required|unique:users,username,' . $user->id,
                'name' => 'required',
                'email' => 'nullable|email|unique:users,email,' . $user->id,
                'jenis_kelamin' => 'required',
                'password' => 'nullable',
                'confirm_password' => 'nullable|same:password',
                'upload_photo' => 'nullable|mimes:jpeg,png,jpg,gif,webp,ico|max:' . BulkData::maxSizeUpload,
                'photo' => 'required_with:upload_photo',
            ], [
                'username.unique' => 'The username is already taken. Please choose another one.',
                'email.unique' => 'The email has already been registered. Please use a different email.',
                'confirm_password.same' => 'Password and Confirm Password must match.',
                'confirm_password.required' => 'The confirm password field is required.',
                'photo.required_with' => 'Photo is required when upload photo is provided.',
            ]);

            if ($request->photo) {
                $imageData = $request->photo;

                // Extract the MIME type and the base64-encoded image data
                preg_match('#^data:image/(\w+);base64,#i', $imageData, $matches);

                if (isset($matches[1])) {
                    $extension = $matches[1]; // Extract the file extension (e.g., png, jpeg, gif)

                    $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $imageData);
                    $imageData = str_replace(' ', '+', $imageData);
                    $image = base64_decode($imageData);

                    $fileName = uniqid() . '.' . $extension;

                    $path = public_path('photo/' . $fileName);
                    file_put_contents($path, $image);

                    if ($user->photo && file_exists(public_path('photo/' . $user->photo))) {
                        unlink(public_path('photo/' . $user->photo));
                    }
                    $user->photo = $fileName;
                }
                
            }

            $user->username = $request->username;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->jenis_kelamin = $request->jenis_kelamin;
            if ($request->password) {
                $user->password = \Hash::make($request->password);
            }
            $user->save();

            \DB::commit();
            return redirect()->back()->with('success', "Berhasil update profile");
        } catch (\Illuminate\Validation\ValidationException $e) {
            \DB::rollBack();
            return redirect()->back()->with('error', implode('<br><br>', array_map('implode', $e->errors())));
        } catch (\Throwable $th) {
            \DB::rollback();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
