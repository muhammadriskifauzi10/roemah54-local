<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Dashboard\HargaController;
use App\Http\Controllers\Dashboard\Inventaris\Barang\MainController as BarangMainController;
use App\Http\Controllers\Dashboard\Inventaris\Kategori\MainController as KategoriMainController;
use App\Http\Controllers\Dashboard\Inventaris\Log\MainController as LogMainController;
use App\Http\Controllers\Dashboard\Inventaris\Penggunaanbarang\MainController as PenggunaanbarangMainController;
use App\Http\Controllers\Dashboard\KamarController;
use App\Http\Controllers\Dashboard\LaundriController;
use App\Http\Controllers\Dashboard\MainController;
use App\Http\Controllers\Dashboard\Manajemenpengguna\Pengguna\MainController as PenggunaMainController;
use App\Http\Controllers\Dashboard\Manajemenpengguna\Role\MainController as RoleMainController;
use App\Http\Controllers\Dashboard\PenyewaankamarController;
use App\Http\Controllers\Dashboard\SewaController;
use App\Http\Controllers\Dashboard\TransaksiController;
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

// Route Auth
Route::group(['middleware' => 'guest'], function () {
    Route::get('/', [LoginController::class, 'index'])->name('login');
    Route::post('/', [LoginController::class, 'authenticate'])->name('authenticate');
});

// Route Guest
Route::group(['middleware' => 'auth'], function () {
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

    // menu utama
    // dasbor
    Route::get('/dasbor', [MainController::class, 'index'])->name('dasbor');
    Route::get('/dasbor/{lantai:id}', [MainController::class, 'detaildata'])->name('detaillantai');
    // Lantai
    Route::post('/tambahlantai', [MainController::class, 'create'])->name('postlantai');
    // Sewa
    Route::group(['middleware' => 'hassewa'], function () {
        Route::get('/sewa', [SewaController::class, 'index'])->name('sewa');
        Route::post('/sewa', [SewaController::class, 'create'])->name('postsewa');
    });
    // penyewa
    Route::get('/sewa/{penyewa:id}', [SewaController::class, 'detaildata'])->name('detailpenyewa');
    Route::post('/sewa/kosongkankamar', [SewaController::class, 'kosongkankamar'])->name('postkosongkankamar');
    Route::post('/getmodalbayarkamar', [SewaController::class, 'getmodalbayarkamar'])->name('getmodalbayarkamar');
    Route::post('/sewa/bayarkamar', [SewaController::class, 'bayarkamar'])->name('postbayarkamar');
    Route::post('/getselectlantaikamar', [SewaController::class, 'getselectlantaikamar'])->name('getselectlantaikamar');
    Route::post('/getselectkamar', [SewaController::class, 'getselectkamar'])->name('getselectkamar');
    Route::post('/getselectjenissewa', [SewaController::class, 'getselectjenissewa'])->name('getselectjenissewa');
    Route::post('/getselectmitra', [SewaController::class, 'getselectmitra'])->name('getselectmitra');
    Route::post('/batalkanpembayarankamar', [SewaController::class, 'batalkanpembayarankamar'])->name('postbatalkanpembayarankamar');
    Route::post('/getmodalselesaikanpembayarankamar', [SewaController::class, 'getmodalselesaikanpembayarankamar'])->name('getmodalselesaikanpembayarankamar');
    Route::post('/selesaikanpembayarankamar', [SewaController::class, 'selesaikanpembayarankamar'])->name('postselesaikanpembayarankamar');
    Route::post('/bayarisitokenkamar', [SewaController::class, 'bayarisitokenkamar'])->name('postbayarisitokenkamar');
    // Kamar
    Route::get('/kamar', [KamarController::class, 'index'])->name('kamar');
    Route::post('/datatablekamar', [KamarController::class, 'datatablekamar'])->name('datatablekamar');
    Route::post('/getmodalkamar', [KamarController::class, 'getmodalkamar'])->name('getmodalkamar');
    Route::post('/getmodaleditkamar', [KamarController::class, 'getmodaleditkamar'])->name('getmodaleditkamar');
    Route::post('/postedittipekamar', [KamarController::class, 'postedittipekamar'])->name('postedittipekamar');
    Route::post('/tambahkamar', [KamarController::class, 'create'])->name('postkamar');
    // Penyewaan Kamar
    Route::get('/penyewaankamar', [PenyewaankamarController::class, 'index'])->name('penyewaankamar');
    Route::post('/datatablepenyewaankamar', [PenyewaankamarController::class, 'datatablepenyewaankamar'])->name('datatablepenyewaankamar');
    Route::get('/penyewaankamar/cetakkwitansi/{id}', [PenyewaankamarController::class, 'cetakkwitansi'])->name('cetakkwitansipembayaran');

    // Laundri
    // Route::get('/laundri', [LaundriController::class, 'index'])->name('laundri');
    // Route::post('/datatablelaundri', [LaundriController::class, 'datatablelaundri'])->name('datatablelaundri');
    // Route::get('/tambahlaundri', [LaundriController::class, 'tambahlaundri'])->name('tambahlaundri');
    // Route::post('/tambahlaundri', [LaundriController::class, 'posttambahlaundri'])->name('posttambahlaundri');

    // keuangan
    // Harga
    Route::get('/harga', [HargaController::class, 'index'])->name('harga');
    Route::post('/datatableharga', [HargaController::class, 'datatableharga'])->name('datatableharga');
    Route::get('/tambahharga', [HargaController::class, 'tambahharga'])->name('tambahharga');
    Route::post('/tambahtipekamar', [HargaController::class, 'posttipekamar'])->name('posttipekamar');
    Route::post('/getselectharga', [HargaController::class, 'getselectharga'])->name('getselectharga');
    Route::post('/tambahharga', [HargaController::class, 'create'])->name('postharga');
    // Transaksi
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi');
    Route::post('/datatabletransaksi', [TransaksiController::class, 'datatabletransaksi'])->name('datatabletransaksi');

    // inventaris
    // kategori
    Route::get('/inventaris/kategori', [KategoriMainController::class, 'index'])->name('inventaris.kategori');
    Route::post('/inventaris/datatablekategori', [KategoriMainController::class, 'datatablekategori'])->name('inventaris.datatablekategori');
    Route::post('/inventaris/kategori', [KategoriMainController::class, 'create'])->name('inventaris.tambahkategori');
    // barang
    Route::get('/inventaris/barang', [BarangMainController::class, 'index'])->name('inventaris.barang');
    Route::post('/inventaris/datatablebarang', [BarangMainController::class, 'datatablebarang'])->name('inventaris.datatablebarang');
    Route::get('/inventaris/tambahbarang', [BarangMainController::class, 'tambahbarang'])->name('inventaris.tambahbarang');
    Route::post('/inventaris/tambahbarang', [BarangMainController::class, 'create'])->name('inventaris.posttambahbarang');
    Route::get('/inventaris/editbarang/{id}', [BarangMainController::class, 'editbarang'])->name('inventaris.editbarang');
    Route::put('/inventaris/editbarang/{id}', [BarangMainController::class, 'update'])->name('inventaris.update');
    // penggunaan barang inventaris
    Route::get('/inventaris/penggunaanbarang', [PenggunaanbarangMainController::class, 'index'])->name('inventaris.penggunaanbarang');
    Route::post('/inventaris/datatablepenggunaanbarang', [PenggunaanbarangMainController::class, 'datatablepenggunaanbarang'])->name('inventaris.datatablepenggunaanbarang');
    Route::post('/inventaris/getmodalpenggunaanbarang', [PenggunaanbarangMainController::class, 'getmodalpenggunaanbarang'])->name('inventaris.getmodalpenggunaanbarang');
    Route::post('/inventaris/tambahpenggunaanbarang', [PenggunaanbarangMainController::class, 'create'])->name('inventaris.tambahpenggunaanbarang');
    Route::post('/inventaris/getmodalmutasipenggunaanbarang', [PenggunaanbarangMainController::class, 'getmodalmutasipenggunaanbarang'])->name('inventaris.getmodalmutasipenggunaanbarang');
    Route::post('/inventaris/destroypenggunaanbarang', [PenggunaanbarangMainController::class, 'destroypenggunaanbarang'])->name('inventaris.destroypenggunaanbarang');
    Route::post('/inventaris/mutasipenggunaanbarang', [PenggunaanbarangMainController::class, 'mutasipenggunaanbarang'])->name('inventaris.mutasipenggunaanbarang');
    // log
    Route::get('/inventaris/log', [LogMainController::class, 'index'])->name('inventaris.log');
    Route::post('/inventaris/datatablelog', [LogMainController::class, 'datatablelog'])->name('inventaris.datatablelog');

    // manajemen pengguna
    // role
    Route::get('/manajemenpengguna/role', [RoleMainController::class, 'index'])->name('manajemenpengguna.role');
    Route::post('/manajemenpengguna/datatablerole', [RoleMainController::class, 'datatablerole'])->name('manajemenpengguna.datatablerole');
    Route::post('/manajemenpengguna/role', [RoleMainController::class, 'create'])->name('manajemenpengguna.tambahrole');
    // pengguna
    Route::get('/manajemenpengguna/pengguna', [PenggunaMainController::class, 'index'])->name('manajemenpengguna.pengguna');
    Route::post('/manajemenpengguna/datatablepengguna', [PenggunaMainController::class, 'datatablepengguna'])->name('manajemenpengguna.datatablepengguna');
    Route::get('/manajemenpengguna/tambahpengguna', [PenggunaMainController::class, 'tambahpengguna'])->name('manajemenpengguna.tambahpengguna');
    Route::post('/manajemenpengguna/tambahpengguna', [PenggunaMainController::class, 'create'])->name('manajemenpengguna.posttambahpengguna');
});
