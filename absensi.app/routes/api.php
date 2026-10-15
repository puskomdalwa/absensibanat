<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginApiController;
use App\Http\Controllers\Admin\AbsensiController;
use App\Http\Controllers\Api\AbsensiApiController;
use App\Http\Controllers\Api\GeofenceApiController;
use App\Http\Controllers\Api\MobileAttendanceController;
use App\Http\Controllers\Api\AdminUserApiController;
use App\Http\Controllers\Webhook\FingerspotController;
use App\Http\Controllers\Api\AbsensiController as ApiAbsensiController;
use App\Http\Controllers\Api\ClientAbsensiController;

/*
|--------------------------------------------------------------------------
| API Routes - Absensi Banat Mobile & Web
|--------------------------------------------------------------------------
*/

// Authentication
Route::post('/login', [LoginApiController::class, 'auth']);

// Public/Mobile Geofence List (Bisa diakses mobile sebelum / sesudah login)
Route::get('/geofence/active', [GeofenceApiController::class, 'getActiveLocations']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [LoginApiController::class, 'me']);
    Route::post('/ubah_katasandi', [LoginApiController::class, 'ubahPassword']);
    Route::post('/update-profile', [LoginApiController::class, 'updateProfile']);
    Route::post('/logout', [LoginApiController::class, 'logout']);

    // Mobile GPS & Face Attendance Actions
    Route::post('/presensi/checkin', [MobileAttendanceController::class, 'checkIn']);
    Route::post('/presensi/checkout', [MobileAttendanceController::class, 'checkOut']);
    Route::get('/presensi/today', [MobileAttendanceController::class, 'statusToday']);
    Route::get('/presensi/summary', [MobileAttendanceController::class, 'summaryMonthly']);
    Route::get('/presensi/leaderboard', [MobileAttendanceController::class, 'getLeaderboard']);
    // Mobile Catatan Kegiatan & Keterangan Presensi
    Route::get('/presensi/kegiatan', [MobileAttendanceController::class, 'getKegiatan']);
    Route::post('/presensi/kegiatan', [MobileAttendanceController::class, 'storeKegiatan']);
    Route::put('/presensi/kegiatan/{id}', [MobileAttendanceController::class, 'updateKegiatan']);
    Route::delete('/presensi/kegiatan/{id}', [MobileAttendanceController::class, 'deleteKegiatan']);

    // Geofence Management (Admin & Staff)
    // Laporan & Rekapitulasi Presensi
    Route::prefix('laporan')->group(function () {
        Route::get('/options', [\App\Http\Controllers\Api\LaporanApiController::class, 'getFilterOptions']);
        Route::get('/rekap', [\App\Http\Controllers\Api\LaporanApiController::class, 'getRekapBulanan']);
        Route::get('/export/excel', [\App\Http\Controllers\Api\LaporanApiController::class, 'exportExcel']);
    });

    
    // Admin & Superadmin User Detail & Role Management
    Route::get('/admin/roles', [AdminUserApiController::class, 'getRoles']);
    Route::get('/admin/users', [AdminUserApiController::class, 'getUsers']);
    Route::post('/admin/user/{id}/update-role', [AdminUserApiController::class, 'updateUserRole']);
    Route::get('/admin/user/{id}/absensi', [AdminUserApiController::class, 'getUserAbsensi']);
    Route::get('/admin/user/{id}/absensi/export/excel', [AdminUserApiController::class, 'exportUserAbsensiExcel']);

    Route::prefix('geofence')->group(function () {
        Route::get('/', [GeofenceApiController::class, 'index']);
        Route::post('/', [GeofenceApiController::class, 'store']);
        Route::put('/{id}', [GeofenceApiController::class, 'update']);
        Route::delete('/{id}', [GeofenceApiController::class, 'destroy']);
    });

    // Existing Absensi API routes (backward compatible)
    Route::get('/data_absen', [AbsensiApiController::class, 'index']);
    Route::post('/absen', [AbsensiApiController::class, 'absenHariIni']);
    Route::post('/data_bydate', [AbsensiApiController::class, 'absensiByTgl']);
    Route::get('/absensi/export/excel', [AbsensiApiController::class, 'exportAbsensiExcel']);
    Route::post('/data_bydate_not_user', [AbsensiApiController::class, 'absensiByTglNotUser']);
    Route::post('/belum_absen', [AbsensiApiController::class, 'belumAbsen']);
    Route::post('/absensi_byid', [AbsensiApiController::class, 'getDataAbsensiPerId']);
    Route::post('/edit_ket', [AbsensiApiController::class, 'updateKeterangan']);
    Route::post('/add_ket', [AbsensiApiController::class, 'addKeterangan']);
    Route::post('/detail_absen', [AbsensiApiController::class, 'absensiDetail']); 
    Route::post('/delete', [AbsensiApiController::class, 'destroy']); 
    Route::get('/list_absensi', [AbsensiApiController::class, 'listData']); 
});

Route::prefix('absensi')->group(function () {
    Route::get('/', [ApiAbsensiController::class, 'index']);
});

Route::prefix('webhook')->group(function () {
    Route::post('/fingerspot', [FingerspotController::class, 'index'])->name('webhook.fingerspot');
});

// Helper route tanpa middleware untuk mempermudah developer eksternal menguji/generate signature
Route::get('/client/v1/signature-helper', [ClientAbsensiController::class, 'signatureGeneratorHelper']);

// API Client Eksternal (diproteksi oleh middleware api.client)
Route::prefix('client/v1')->middleware('api.client')->group(function () {
    Route::get('/absensi', [ClientAbsensiController::class, 'index']);
    Route::get('/absensi/rekap', [ClientAbsensiController::class, 'rekap']);
});
