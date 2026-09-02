<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginApiController;
use App\Http\Controllers\Admin\AbsensiController;
use App\Http\Controllers\Api\AbsensiApiController;
use App\Http\Controllers\Webhook\FingerspotController;
use App\Http\Controllers\Api\AbsensiController as ApiAbsensiController;
use App\Http\Controllers\Api\ClientAbsensiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [LoginApiController::class, 'auth']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/ubah_katasandi', [LoginApiController::class, 'ubahPassword']);
    Route::post('/logout', [LoginApiController::class, 'logout']);
    Route::get('/data_absen', [AbsensiApiController::class, 'index']);
    Route::post('/absen', [AbsensiApiController::class, 'absenHariIni']);
    Route::post('/data_bydate', [AbsensiApiController::class, 'absensiByTgl']);
    Route::post('/data_bydate_not_user', [AbsensiApiController::class, 'absensiByTglNotUser']);
    Route::post('/belum_absen', [AbsensiApiController::class, 'belumAbsen']);
    Route::post('/absensi_byid', [AbsensiApiController::class, 'getDataAbsensiPerId']);
    Route::post('/edit_ket', [AbsensiApiController::class, 'updateKeterangan']);
    Route::post('/add_ket', [AbsensiApiController::class, 'addKeterangan']);
    Route::post('/detail_absen',[AbsensiApiController::class,'absensiDetail']); 
    Route::post('/delete',[AbsensiApiController::class,'destroy']); 
    Route::get('/list_absensi',[AbsensiApiController::class,'listData']); 

});

Route::prefix('absensi')->group(function(){
    Route::get('/', [ApiAbsensiController::class, 'index']);
});


Route::prefix('webhook')->group(function () {
    Route::post('/fingerspot', [FingerspotController::class, 'index'])->name('webhook.fingerspot');
});

// Helper route tanpa middleware untuk mempermudah developer eksternal menguji/generate signature
Route::get('/client/v1/signature-helper', [ClientAbsensiController::class, 'signatureGeneratorHelper']);

// API Client Eksternal (diproteksi oleh middleware api.client - verifikasi API Key & Signature HMAC SHA256)
Route::prefix('client/v1')->middleware('api.client')->group(function () {
    Route::get('/absensi', [ClientAbsensiController::class, 'index']);
    Route::get('/absensi/rekap', [ClientAbsensiController::class, 'rekap']);
});

// Route::post('/auth', [AuthApiController::class, 'login']);
// Route::post('/absensi', [AuthApiController::class, 'simpanAbsensi']);
// Route::post('/data', [AuthApiController::class, 'index']);
// Route::post('/profile', [AuthApiController::class, 'profilUser']);
// Route::get('/test', [AuthApiController::class, 'coba']);
// Route::post('/logout', [AuthApiController::class, 'keluar'])->middleware('auth:sanctum');
