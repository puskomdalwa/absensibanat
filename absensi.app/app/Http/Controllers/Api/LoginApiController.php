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
    function auth(Request $request){
      $rules = $request->validate([
            'username'=>'required',
            'password'=>'required'
        ]);
        if (!Auth::attempt($rules)) {
           return response()->json([
            'message'=>'Login gagal',
            'status'=>false,
           ]);
        }
        $auth = Auth::user(); 
        $user = User::join('departemen','departemen.id','=','users.departemen_id')->where('users.id',$auth->id)->select('users.id as id','email','username','name','jenis_kelamin','departemen.nama as jabatan')->first();
       
        $token = $auth->createToken('token')->plainTextToken;
        return response()->json([
            'message'=>'Login success',
            'status'=>true,
            'token'=>$token,
            'data'=>$user
        ]);
    }
   function ubahPassword(Request $request){
       try {
        $request->validate([
            'password_lama'=>'required',
            'password_baru'=>'required',
        ]);

        $user = Auth::user();

       if (!Hash::check($request->password_lama,$user->password)) {
            return response()->json([
                'status'=>false,
                'message'=>'password salah'
            ]);
       }

       $user->password = Hash::make($request->password_baru);
       $user->save();

       return response()->json([
        'status'=>true,
        'message'=>'berhasil ganti password'
       ]);
       } catch (\Throwable $th) {
        Log::info($th);
       }
        
   }
   function logout(Request $request ) {
       try {
        $user = $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status'=>true,
            'message'=>'berhasil'
        ]);
       } catch (\Throwable $th) {
        Log::info($th);
       }
   }
}
