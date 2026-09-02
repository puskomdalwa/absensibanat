<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AbsensiController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepartemenController;
use App\Http\Controllers\Home\LandingPageController;
use App\Http\Controllers\Home\PrivacyPolicyController;
use App\Http\Controllers\Home\AbsensiController as HomeAbsensiController;
use App\Http\Controllers\Operasi\UserController as OperasiUserController;
use App\Http\Controllers\Home\DashboardController as HomeDashboardController;
use App\Http\Controllers\Home\RealtimeController;
use App\Http\Controllers\TestingController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\ApiClientController;
use App\Http\Controllers\Admin\TypeController;
use App\Http\Controllers\LaporanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes([
    'register' => false,
    'reset' => false,
    'verify' => false,
    'confirm' => false,
    'email' => false,
]);

Route::get('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login');
});

Route::get('/beranda', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/', [LandingPageController::class, 'index'])->name('root.index');
Route::redirect('/index.html', '/');

Route::get('/privacy_policy', [PrivacyPolicyController::class, 'index'])->name('privacy_policy.index');

Route::get('/getData', [LandingPageController::class, 'getData'])->name('root.getData');

Route::prefix('absensi')->group(function () {
    Route::get('/', [HomeAbsensiController::class, 'index'])->name('absensi.index');
    Route::get('/{user}', [HomeAbsensiController::class, 'show'])->name('absensi.show')->middleware(['auth', 'absensi_access']);
    Route::get('/{user}/keterangan/{absensi}', [HomeAbsensiController::class, 'keterangan'])->name('absensi.keterangan')->middleware(['auth', 'absensi_access']);
    Route::get('/{user}/data', [HomeAbsensiController::class, 'data'])->name('absensi.data')->middleware(['auth', 'absensi_access']);
});

Route::prefix('realtime')->middleware(['is_admin'])->group(function () {
    Route::get('/', [RealtimeController::class, 'index'])->name('realtime.index');
    Route::get('/data', [RealtimeController::class, 'data'])->name('realtime.data');

    Route::get('/today', [RealtimeController::class, 'indexToday'])->name('realtime.today');
    Route::get('/data/today', [RealtimeController::class, 'dataToday'])->name('realtime.data.today');
});

Route::prefix('dashboard')->middleware(['auth'])->group(function () {
    Route::get('/', [HomeDashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/data', [HomeDashboardController::class, 'data'])->name('dashboard.data');
    Route::get('/keterangan/{absensi}', [HomeDashboardController::class, 'keterangan'])->name('dashboard.keterangan');
    Route::post('/', [HomeDashboardController::class, 'store'])->name('dashboard.store');
    Route::put('/', [HomeDashboardController::class, 'update'])->name('dashboard.update');
    Route::get('/delete', [HomeDashboardController::class, 'delete'])->name('dashboard.delete');
});

Route::prefix('laporan')->middleware(['auth'])->group(function () {
    Route::get('/', [LaporanController::class, 'userIndex'])->name('laporan.index');
    Route::get('/data', [LaporanController::class, 'userData'])->name('laporan.data');
});

Route::prefix('admin')->middleware(['auth', 'role:admin,staff'])->group(function () {
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard.index');
        Route::get('/dataAbsensi', [DashboardController::class, 'dataAbsensi'])->name('admin.dashboard.dataAbsensi');
    });

    Route::prefix('role')->middleware('role:admin')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('admin.role.index');
        Route::get('/data', [RoleController::class, 'data'])->name('admin.role.data');
        Route::post('/store', [RoleController::class, 'store'])->name('admin.role.store');
        Route::put('/update', [RoleController::class, 'update'])->name('admin.role.update');
        Route::delete('/delete', [RoleController::class, 'delete'])->name('admin.role.delete');
    });

    Route::prefix('type')->middleware('role:admin')->group(function () {
        Route::get('/', [TypeController::class, 'index'])->name('admin.type.index');
        Route::get('/data', [TypeController::class, 'data'])->name('admin.type.data');
        Route::post('/store', [TypeController::class, 'store'])->name('admin.type.store');
        Route::put('/update', [TypeController::class, 'update'])->name('admin.type.update');
        Route::delete('/delete', [TypeController::class, 'delete'])->name('admin.type.delete');
        Route::get('/{type}/assign', [TypeController::class, 'assign'])->name('admin.type.assign');
        Route::post('/{type}/assign', [TypeController::class, 'assignStore'])->name('admin.type.assign.store');
    });

    Route::prefix('departemen')->middleware('role:admin')->group(function () {
        Route::get('/', [DepartemenController::class, 'index'])->name('admin.departemen.index');
        Route::get('/data', [DepartemenController::class, 'data'])->name('admin.departemen.data');
        Route::post('/store', [DepartemenController::class, 'store'])->name('admin.departemen.store');
        Route::put('/update', [DepartemenController::class, 'update'])->name('admin.departemen.update');
        Route::delete('/delete', [DepartemenController::class, 'delete'])->name('admin.departemen.delete');
    });

    Route::prefix('absensi')->group(function () {
        Route::get('/', [AbsensiController::class, 'index'])->name('admin.absensi.index');
        Route::get('/data', [AbsensiController::class, 'data'])->name('admin.absensi.data');
        Route::post('/simpan', [AbsensiController::class, 'store'])->name('admin.absensi.store')->middleware('role:admin');
        Route::delete('/hapus', [AbsensiController::class, 'destroy'])->name('admin.absensi.delete')->middleware('role:admin');
        Route::put('/update', [AbsensiController::class, 'update'])->name('admin.absensi.edit')->middleware('role:admin');

        Route::prefix('export')->group(function () {
            Route::get('/excel', [AbsensiController::class, 'exportExcel'])->name('admin.absensi.export.excel');
        });
        Route::post('/import', [AbsensiController::class, 'import'])->name('admin.absensi.import')->middleware('role:admin');
    });

    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('admin.user.index');
        Route::get('/data', [UserController::class, 'data'])->name('admin.user.data');
        Route::get('/{user}/absensi', [LaporanController::class, 'adminUserDetail'])->name('admin.user.absensi.detail');
        Route::get('/{user}/absensi/summary', [LaporanController::class, 'adminUserDetailSummary'])->name('admin.user.absensi.summary');
        Route::get('/{user}/absensi/data', [LaporanController::class, 'adminUserDetailData'])->name('admin.user.absensi.data');
        Route::get('/{user}/absensi/export/excel', [LaporanController::class, 'adminUserDetailExportExcel'])->name('admin.user.absensi.export.excel');
        Route::get('/{user}/absensi/export/pdf', [LaporanController::class, 'adminUserDetailExportPdf'])->name('admin.user.absensi.export.pdf');
        Route::post('/store', [UserController::class, 'store'])->name('admin.user.store');
        Route::put('/update', [UserController::class, 'update'])->name('admin.user.update');
        Route::delete('/delete', [UserController::class, 'delete'])->name('admin.user.delete');
        Route::post('/import', [UserController::class, 'import'])->name('admin.user.import')->middleware('role:admin');
    });

    Route::prefix('kategori')->middleware('role:admin')->group(function () {
        Route::get('/', [KategoriController::class, 'index'])->name('admin.kategori.index');
        Route::get('/data', [KategoriController::class, 'data'])->name('admin.kategori.data');
        Route::post('/store', [KategoriController::class, 'store'])->name('admin.kategori.store');
        Route::put('/update', [KategoriController::class, 'update'])->name('admin.kategori.update');
        Route::post('/synchronize', [KategoriController::class, 'synchronize'])->name('admin.kategori.synchronize');
        Route::delete('/delete', [KategoriController::class, 'delete'])->name('admin.kategori.delete');
    });

    Route::prefix('laporan')->middleware('role:admin')->group(function () {
        Route::get('/', [LaporanController::class, 'adminIndex'])->name('admin.laporan.index');
        Route::get('/data', [LaporanController::class, 'adminData'])->name('admin.laporan.data');
    });

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('admin.profile.index');
        Route::put('/update', [ProfileController::class, 'update'])->name('admin.profile.update');
    });

    Route::prefix('gallery')->middleware('role:admin')->group(function () {
        Route::get('/', [GalleryController::class, 'index'])->name('admin.gallery.index');
        Route::get('/data', [GalleryController::class, 'data'])->name('admin.gallery.data');
        Route::post('/store', [GalleryController::class, 'store'])->name('admin.gallery.store');
        Route::delete('/delete', [GalleryController::class, 'destroy'])->name('admin.gallery.delete');
    });

    Route::prefix('api-client')->middleware('role:admin')->group(function () {
        Route::get('/', [ApiClientController::class, 'index'])->name('admin.api_client.index');
        Route::get('/data', [ApiClientController::class, 'data'])->name('admin.api_client.data');
        Route::post('/store', [ApiClientController::class, 'store'])->name('admin.api_client.store');
        Route::put('/update', [ApiClientController::class, 'update'])->name('admin.api_client.update');
        Route::delete('/delete', [ApiClientController::class, 'delete'])->name('admin.api_client.delete');
    });
});

// Public API for gallery marquee
Route::get('/api/gallery/latest', [GalleryController::class, 'getLatest'])->name('api.gallery.latest');

Route::prefix('operasi')->middleware(['auth', 'role:admin'])->group(function () {
    Route::prefix('user')->group(function () {
        Route::get('/autocomplete/{query}', [OperasiUserController::class, 'autocomplete'])->name('operasi.user.autocomplete');
    });
});

Route::get('testing', [TestingController::class, 'index'])->name('testing.index');
Route::get('testing/readLog', [TestingController::class, 'readLog'])->name('testing.readLog');
