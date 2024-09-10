<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Dashboard\Asrama\Kamar\MainController as KamarMainController;
use App\Http\Controllers\Dashboard\Asrama\Mahasiswa\MainController as MahasiswaMainController;
use App\Http\Controllers\Dashboard\Asrama\MainController as AsramaMainController;
use App\Http\Controllers\Dashboard\Bisnis\Ritel\MainController as RitelMainController;
use App\Http\Controllers\Dashboard\Harga\MainController as HargaMainController;
use App\Http\Controllers\Dashboard\HargaController;
use App\Http\Controllers\Dashboard\Inventaris\Barang\MainController as BarangMainController;
use App\Http\Controllers\Dashboard\Inventaris\Kategori\MainController as KategoriMainController;
use App\Http\Controllers\Dashboard\Inventaris\Log\MainController as LogMainController;
use App\Http\Controllers\Dashboard\Inventaris\Penggunaanbarang\MainController as PenggunaanbarangMainController;
use App\Http\Controllers\Dashboard\KamarController;
use App\Http\Controllers\Dashboard\Lokasi\MainController as LokasiMainController;
use App\Http\Controllers\Dashboard\MainController;
use App\Http\Controllers\Dashboard\Pengguna\MainController as PenggunaMainController;
use App\Http\Controllers\Dashboard\Penyewa\Daftarpenyewa\MainController as DaftarpenyewaMainController;
use App\Http\Controllers\Dashboard\Penyewa\Dendacheckout\MainController as DendacheckoutMainController;
use App\Http\Controllers\Dashboard\Penyewa\Penyewaankamar\MainController as PenyewaankamarMainController;
use App\Http\Controllers\Dashboard\Role\MainController as RoleMainController;
use App\Http\Controllers\Dashboard\Scan\MainController as ScanMainController;
use App\Http\Controllers\Dashboard\SewaController;
use App\Http\Controllers\Dashboard\Tipekamar\MainController as TipekamarMainController;
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
    Route::get('/dasbor/{lantai:id}', [MainController::class, 'detaildata'])->name('dasbor.detaillantai');
    Route::get('/dasbor/detailpenyewa/{penyewa:id}', [MainController::class, 'detailpenyewa'])->name('dasbor.detailpenyewa');

    // Lantai
    Route::post('/tambahlantai', [MainController::class, 'create'])->name('postlantai');
    // lokasi
    Route::get('/lokasi', [LokasiMainController::class, 'index'])->name('lokasi');
    Route::post('/lokasi/datatablelokasi', [LokasiMainController::class, 'datatablelokasi'])->name('lokasi.datatablelokasi');
    Route::post('/lokasi/getmodalcetakqrcodelokasi', [LokasiMainController::class, 'getmodalcetakqrcodelokasi'])->name('lokasi.getmodalcetakqrcodelokasi');
    Route::get('/lokasi/cetakqrcode', [LokasiMainController::class, 'cetakqrcode'])->name('lokasi.cetakqrcode');

    // penyewa
    // daftar penyewa
    Route::get('/daftarpenyewa', [DaftarpenyewaMainController::class, 'index'])->name('daftarpenyewa');
    Route::post('/daftarpenyewa/datatabledaftarpenyewa', [DaftarpenyewaMainController::class, 'datatabledaftarpenyewa'])->name('daftarpenyewa.datatabledaftarpenyewa');
    // Penyewaan Kamar
    Route::get('/penyewaankamar', [PenyewaankamarMainController::class, 'index'])->name('penyewaankamar');
    Route::post('/penyewaankamar/datatablepenyewaankamar', [PenyewaankamarMainController::class, 'datatablepenyewaankamar'])->name('penyewaankamar.datatablepenyewaankamar');
    Route::get('/penyewaankamar/cetakkwitansi/{id}', [PenyewaankamarMainController::class, 'cetakkwitansi'])->name('penyewaankamar.cetakkwitansi');
    Route::get('/penyewaankamar/cetakinvoice/{id}', [PenyewaankamarMainController::class, 'cetakinvoice'])->name('penyewaankamar.cetakinvoice');
    Route::post('/penyewaankamar/pulangkantamu', [PenyewaankamarMainController::class, 'pulangkantamu'])->name('penyewaankamar.pulangkantamu');
    Route::post('/penyewaankamar/getrequestformsewaonktp', [PenyewaankamarMainController::class, 'getrequestformsewaonktp'])->name('penyewaankamar.getrequestformsewaonktp');
    // denda checkout
    Route::get('/dendacheckout', [DendacheckoutMainController::class, 'index'])->name('dendacheckout');
    Route::post('/dendacheckout/datatabledendacheckout', [DendacheckoutMainController::class, 'datatabledendacheckout'])->name('dendacheckout.datatabledendacheckout');
    Route::post('/dendacheckout/getmodalbayardenda', [DendacheckoutMainController::class, 'getmodalbayardenda'])->name('dendacheckout.getmodalbayardenda');
    Route::post('/dendacheckout/bayardenda', [DendacheckoutMainController::class, 'bayardenda'])->name('dendacheckout.bayardenda');

    // Sewa
    Route::group(['middleware' => 'hassewa'], function () {
        Route::get('/sewa', [SewaController::class, 'index'])->name('sewa');
        Route::post('/sewa', [SewaController::class, 'create'])->name('postsewa');
    });

    Route::post('/getmodalperpanjangpembayarankamar', [SewaController::class, 'getmodalperpanjangpembayarankamar'])->name('getmodalperpanjangpembayarankamar');
    Route::post('/sewa/bayarperpanjangankamar', [SewaController::class, 'bayarperpanjangankamar'])->name('postbayarperpanjangankamar');
    Route::post('/getselectlantaikamar', [SewaController::class, 'getselectlantaikamar'])->name('getselectlantaikamar');
    Route::post('/getselectkamar', [SewaController::class, 'getselectkamar'])->name('getselectkamar');
    Route::post('/getselectjenissewa', [SewaController::class, 'getselectjenissewa'])->name('getselectjenissewa');
    Route::post('/getselectmitra', [SewaController::class, 'getselectmitra'])->name('getselectmitra');
    Route::post('/batalkanpembayarankamar', [SewaController::class, 'batalkanpembayarankamar'])->name('postbatalkanpembayarankamar');
    Route::post('/getmodalselesaikanpembayarankamar', [SewaController::class, 'getmodalselesaikanpembayarankamar'])->name('getmodalselesaikanpembayarankamar');
    Route::post('/selesaikanpembayarankamar', [SewaController::class, 'selesaikanpembayarankamar'])->name('postselesaikanpembayarankamar');
    Route::post('/bayarisitokenkamar', [SewaController::class, 'bayarisitokenkamar'])->name('postbayarisitokenkamar');

    // kamar
    // tipekamar
    Route::get('/tipekamar', [TipekamarMainController::class, 'index'])->name('tipekamar');
    Route::post('/tipekamar/datatabletipekamar', [TipekamarMainController::class, 'datatabletipekamar'])->name('tipekamar.datatabletipekamar');
    Route::post('/tipekamar/tambahtipekamar', [TipekamarMainController::class, 'create'])->name('tipekamar.posttipekamar');
    // Kamar
    Route::get('/kamar', [KamarController::class, 'index'])->name('kamar');
    Route::post('/kamar/datatablekamar', [KamarController::class, 'datatablekamar'])->name('kamar.datatablekamar');
    Route::post('/kamar/getmodalkamar', [KamarController::class, 'getmodalkamar'])->name('kamar.getmodalkamar');
    Route::post('/kamar/tambahkamar', [KamarController::class, 'create'])->name('kamar.postkamar');
    Route::post('/kamar/getmodaleditkamar', [KamarController::class, 'getmodaleditkamar'])->name('kamar.getmodaleditkamar');
    Route::post('/kamar/postedittipekamar', [KamarController::class, 'edittipekamar'])->name('kamar.postedittipekamar');
    // kamar asrama
    Route::get('/asrama/mahasiswa', [MahasiswaMainController::class, 'index'])->name('asrama.mahasiswa');
    Route::post('/asrama/mahasiswa/datatableasramamahasiswa', [MahasiswaMainController::class, 'datatableasramamahasiswa'])->name('asrama.mahasiswa.datatableasramamahasiswa');
    Route::post('/asrama/mahasiswa/getmodaltambahpenyewa', [MahasiswaMainController::class, 'getmodaltambahpenyewa'])->name('asrama.mahasiswa.getmodaltambahpenyewa');
    Route::post('/asrama/mahasiswa/tambahpenyewa', [MahasiswaMainController::class, 'tambahpenyewa'])->name('asrama.mahasiswa.tambahpenyewa');
    Route::get('/asrama/mahasiswa/cetakinvoice/{id}', [MahasiswaMainController::class, 'cetakinvoice'])->name('asrama.mahasiswa.cetakinvoice');
    Route::get('/asrama/mahasiswa/cetakkwitansi/{id}', [MahasiswaMainController::class, 'cetakkwitansi'])->name('asrama.mahasiswa.cetakkwitansi');
    Route::post('/asrama/mahasiswa/getrequestformsewaonktp', [MahasiswaMainController::class, 'getrequestformsewaonktp'])->name('asrama.mahasiswa.getrequestformsewaonktp');
    // kamar asrama
    Route::get('/asrama/kamar', [KamarMainController::class, 'index'])->name('asrama.kamar');
    Route::post('/asrama/kamar/datatableasrama', [KamarMainController::class, 'datatableasrama'])->name('asrama.kamar.datatableasrama');
    Route::get('/asrama/kamar/tambahasrama', [KamarMainController::class, 'tambahasrama'])->name('asrama.kamar.tambahasrama');
    Route::post('/asrama/kamar/tambahasrama', [KamarMainController::class, 'create'])->name('asrama.kamar.postasrama');
    Route::get('/asrama/kamar/detailpenyewa/{id}', [KamarMainController::class, 'detailpenyewa'])->name('asrama.kamar.detailpenyewa');
    Route::post('/asrama/kamar/getselectlantaikamar', [KamarMainController::class, 'getselectlantaikamar'])->name('asrama.kamar.getselectlantaikamar');
    // Harga
    Route::get('/harga', [HargaMainController::class, 'index'])->name('harga');
    Route::post('/harga/datatableharga', [HargaMainController::class, 'datatableharga'])->name('harga.datatableharga');
    Route::get('/harga/tambahharga', [HargaMainController::class, 'tambahharga'])->name('harga.tambahharga');
    Route::post('/harga/getselectharga', [HargaMainController::class, 'getselectharga'])->name('harga.getselectharga');
    Route::post('/harga/tambahharga', [HargaMainController::class, 'create'])->name('harga.postharga');

    // layanan
    // laundri
    Route::get('/ritel', [RitelMainController::class, 'index'])->name('ritel');
    Route::post('/ritel/datatableritel', [RitelMainController::class, 'datatableritel'])->name('ritel.datatableritel');
    Route::get('/ritel/tambahritel', [RitelMainController::class, 'tambahritel'])->name('ritel.tambahritel');
    Route::post('/ritel/tambahritel', [RitelMainController::class, 'create'])->name('ritel.posttambahritel');

    // laporan
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
    Route::post('/inventaris/mutasipenggunaanbarang', [PenggunaanbarangMainController::class, 'mutasipenggunaanbarang'])->name('inventaris.mutasipenggunaanbarang');
    Route::post('/inventaris/penggunaanbarang/getmodalcetakqrcodepenggunaanbarang', [PenggunaanbarangMainController::class, 'getmodalcetakqrcodepenggunaanbarang'])->name('inventaris.getmodalcetakqrcodepenggunaanbarang');
    Route::get('/inventaris/penggunaanbarang/cetakqrcode', [PenggunaanbarangMainController::class, 'cetakqrcode'])->name('inventaris.cetakqrcode');
    Route::post('/inventaris/destroypenggunaanbarang', [PenggunaanbarangMainController::class, 'destroypenggunaanbarang'])->name('inventaris.destroypenggunaanbarang');
    // log
    Route::get('/inventaris/log', [LogMainController::class, 'index'])->name('inventaris.log');
    Route::post('/inventaris/datatablelog', [LogMainController::class, 'datatablelog'])->name('inventaris.datatablelog');

    // manajemen pengguna
    // role
    Route::get('/role', [RoleMainController::class, 'index'])->name('role');
    Route::post('/role/datatablerole', [RoleMainController::class, 'datatablerole'])->name('role.datatablerole');
    Route::post('/role', [RoleMainController::class, 'create'])->name('role.tambahrole');
    // pengguna
    Route::get('/pengguna', [PenggunaMainController::class, 'index'])->name('pengguna');
    Route::post('/pengguna/datatablepengguna', [PenggunaMainController::class, 'datatablepengguna'])->name('pengguna.datatablepengguna');
    Route::get('/pengguna/tambahpengguna', [PenggunaMainController::class, 'tambahpengguna'])->name('pengguna.tambahpengguna');
    Route::post('/pengguna/tambahpengguna', [PenggunaMainController::class, 'create'])->name('pengguna.posttambahpengguna');
    Route::get('/pengguna/detailpengguna/{id}', [PenggunaMainController::class, 'detailpengguna'])->name('pengguna.detailpengguna');
    Route::post('/pengguna/destroypengguna', [PenggunaMainController::class, 'destroypengguna'])->name('pengguna.destroypengguna');
    Route::post('/pengguna/aktifkanpengguna', [PenggunaMainController::class, 'aktifkanpengguna'])->name('pengguna.aktifkanpengguna');
    Route::put('/pengguna/updatepengguna/{id}', [PenggunaMainController::class, 'update'])->name('pengguna.updatepengguna');
    // scan
    // penggunaan barang inventaris
    Route::get('/scan/penggunaanbaranginventaris', [ScanMainController::class, 'penggunaanbaranginventaris'])->name('scan.penggunaanbaranginventaris');
    Route::post('/scan/getscanpenggunaanbaranginventaris', [ScanMainController::class, 'getscanpenggunaanbaranginventaris'])->name('scan.getscanpenggunaanbaranginventaris');
    // lokasi
    Route::get('/scan/lokasi', [ScanMainController::class, 'lokasi'])->name('scan.lokasi');
    Route::post('/scan/getscanlokasi', [ScanMainController::class, 'getscanlokasi'])->name('scan.getscanlokasi');
});
