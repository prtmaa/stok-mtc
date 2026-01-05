<?php

use App\Http\Controllers\BarangInController;
use App\Http\Controllers\BarangOutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\StokFlowController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/item', function () {
    return view('item.index');
})->middleware(['auth', 'verified'])->name('item');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/', [DashboardController::class, 'index'])->name('index');

    Route::get('/item/export-excel', [ItemController::class, 'exportExcel'])->name('item.exportExcel');
    Route::get('/item/data', [ItemController::class, 'data'])->name('item.data');
    Route::resource('/item', ItemController::class);
    Route::get('/item/export-excel', [ItemController::class, 'exportExcel'])->name('item.exportExcel');


    Route::get('/in/data', [BarangInController::class, 'data'])->name('in.data');
    Route::resource('/in', BarangInController::class);
    Route::get('/barang-in/export', [BarangInController::class, 'exportExcel'])->name('in.export');


    Route::get('/out/data', [BarangOutController::class, 'data'])->name('out.data');
    Route::resource('/out', BarangOutController::class);
    Route::get('/get-items', [BarangOutController::class, 'getItems'])->name('get.items');
    Route::get('/barang-out/export', [BarangOutController::class, 'exportExcel'])->name('out.export');
    Route::get('/barang-out/harga/{item}', [BarangOutController::class, 'getHargaRataRata']);

    Route::get('/kategori/data', [KategoriController::class, 'data'])->name('kategori.data');
    Route::resource('/kategori', KategoriController::class);

    Route::get('/uom/data', [SatuanController::class, 'data'])->name('uom.data');
    Route::resource('/uom', SatuanController::class);

    Route::get('/supplier/data', [SupplierController::class, 'data'])->name('supplier.data');
    Route::resource('/supplier', SupplierController::class);

    Route::get('/divisi/data', [DivisiController::class, 'data'])->name('divisi.data');
    Route::resource('/divisi', DivisiController::class);

    Route::get('/get-satuan/{id}', [ItemController::class, 'getSatuan']);


    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::post('/laporan/generate', [LaporanController::class, 'generateLaporanBulanan'])->name('laporan.generate');
    Route::get('/laporan/export', [LaporanController::class, 'exportExcel'])->name('laporan.export');

    Route::middleware(['auth', 'role:master'])->group(function () {
        Route::get('/user/data', [UserController::class, 'data'])->name('user.data');
        Route::resource('/user', UserController::class);
    });
});

require __DIR__ . '/auth.php';
