<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Lantai;
use App\Models\Lokasi;
use App\Models\Pembayaran;
use App\Models\Pembayarandetail;
use App\Models\Penyewa;
use App\Models\Tipekamar;
use App\Models\Transaksi;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MainController extends Controller
{
    public function index()
    {
        // Mendapatkan tanggal awal dan akhir bulan sebelumnya
        $startOfPreviousMonth = Carbon::now()->subMonth()->startOfMonth()->toDateString();
        $endOfPreviousMonth = Carbon::now()->subMonth()->endOfMonth()->toDateString();

        // Menghitung total pendapatan bulan sebelumnya
        $totalPendapatanBulanSebelumnya = Transaksi::where('tipe', 'pemasukan')
            ->whereDate('tanggal_transaksi', '>=', $startOfPreviousMonth)
            ->whereDate('tanggal_transaksi', '<=', $endOfPreviousMonth)
            ->sum('jumlah_uang');

        // Mendapatkan tanggal awal dan akhir bulan ini
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

        // Menghitung total pendapatan bulan ini
        $totalPendapatanBulanIni = Transaksi::where('tipe', 'pemasukan')
            ->whereDate('tanggal_transaksi', '>=', $startOfMonth)
            ->whereDate('tanggal_transaksi', '<=', $endOfMonth)
            ->sum('jumlah_uang');


        $lantai = Lantai::join('lokasis', 'lantais.id', '=', 'lokasis.lantai_id')
            ->select(
                'lantais.id',
                'lantais.namalantai',
            )
            ->distinct()
            ->where('lokasis.jenisruangan_id', 2)
            ->orderBy('lantais.id', 'ASC')
            ->get();

        $data = [
            'judul' => 'Dasbor',
            'lantai' => $lantai,
            'pendapatandibulansebelumnya' => $totalPendapatanBulanSebelumnya,
            'pendapatandibulanini' => $totalPendapatanBulanIni,
        ];

        return view('contents.dashboard.main', $data);
    }
    public function detaildata(Lantai $lantai)
    {
        $data = [
            'judul' => 'Detail Lantai',
            'lantai' => $lantai,
        ];
        return view('contents.dashboard.detaillantai', $data);
    }
    public function create()
    {
        if (request()->ajax()) {
            try {
                DB::beginTransaction();
                Lantai::create([
                    'namalantai' => "Lantai " . Lantai::all()->count() + 1,
                    'operator_id' => auth()->user()->id
                ]);

                $response = [
                    'status' => 200,
                    'message' => 'success',
                ];

                DB::commit();
                return response()->json($response);
            } catch (Exception $e) {
                $response = [
                    'status' => 500,
                    'message' => $e->getMessage(),
                ];

                DB::rollBack();
                return response()->json($response);
            }
        }
    }
    public function detailpenyewa($id)
    {
        if (!Pembayaran::where('id', $id)->exists()) {
            abort(404);
        }

        $pembayaran = Pembayaran::findorfail($id);
        $pembayaran_detail = Pembayarandetail::where('pembayaran_id', $pembayaran->id)->get();

        $kamar = Lokasi::where('id', $pembayaran->lokasi_id)->first();
        $tipekamar = Tipekamar::where('id', $pembayaran->tipekamar_id)->first();

        $data = [
            'judul' => 'Detail Penyewa',
            'pembayaran' => $pembayaran,
            'pembayaran_detail' => $pembayaran_detail,
            'kamar' => $kamar,
            'tipekamar' => $tipekamar,
        ];

        return view('contents.dashboard.penyewa.penyewaankamar.detailpenyewa', $data);
    }
    public function tambahpenyewa()
    {
        if (request()->ajax()) {
            $pembayaran_id = htmlspecialchars(request()->input('pembayaran_id'), true);
            $noktp = htmlspecialchars(request()->input('noktp'), true);
            $namalengkap = htmlspecialchars(request()->input('namalengkap'), true);
            $nohp = htmlspecialchars(request()->input('nohp'), true);
            $alamat = htmlspecialchars(request()->input('alamat'), true);
            $fotoktp = request()->file('fotoktp');

            if (!Penyewa::where('noktp', $noktp)->exists()) {
                $rulefotoktp = 'required|mimes:jpg,jpeg,png';
            } else {
                $rulefotoktp = 'mimes:jpg,jpeg,png';
            }

            $validator = Validator::make(request()->all(), [
                'namalengkap' => 'required',
                'noktp' => 'required|numeric|digits:16',
                'nohp' => 'required|regex:/^08[0-9]{8,}$/',
                'fotoktp' => $rulefotoktp,
                'alamat' => 'required',
            ], [
                'namalengkap.required' => 'Kolom ini wajib diisi',
                'noktp.required' => 'No KTP wajib diisi',
                'noktp.numeric' => 'No KTP tidak valid',
                'noktp.digits' => 'No KTP tidak valid',
                'nohp.required' => 'Kolom ini wajib diisi',
                'nohp.regex' => 'No HP tidak valid',
                'fotoktp.required' => 'Kolom ini wajib diisi',
                'fotoktp.mimes' => 'Ekstensi file hanya mendukung format jpg dan jpeg',
                'alamat.required' => 'Kolom ini wajib diisi',
                // 'tipe_pembayaran.required' => 'Kolom ini tidak valid',
            ]);

            if ($validator->fails()) {
                $response = [
                    'status' => 422,
                    'message' => 'errorvalidation',
                    'dataError' => $validator->errors()
                ];

                return response()->json($response);
            }

            try {
                DB::beginTransaction();

                $penyewa = Penyewa::where('noktp', $noktp)->first();

                if ($penyewa && Pembayarandetail::where('pembayaran_id', intval($pembayaran_id))->where('penyewa_id', $penyewa->id)->exists()) {
                    $response = [
                        'status' => 500,
                        'message' => 'opps',
                    ];

                    return response()->json($response);
                } else {
                    if (!Penyewa::where('noktp', $noktp)->exists()) {
                        $penyewa = Penyewa::create([
                            'namalengkap' => $namalengkap,
                            'noktp' => $noktp,
                            'nohp' => $nohp,
                            'alamat' => $alamat,
                            'fotoktp' => "",
                            'operator_id' => auth()->user()->id,
                        ]);

                        $fotoktp = "penyewa" . "-" . $penyewa->id . "." .  request()->file('fotoktp')->getClientOriginalExtension();
                        $file = request()->file('fotoktp');
                        $tujuan_upload = 'img/ktp';
                        $file->move($tujuan_upload, $fotoktp);

                        Penyewa::where('id', $penyewa->id)->update([
                            'fotoktp' => $fotoktp,
                            'updated_at' => date("Y-m-d H:i:s"),
                        ]);
                    } else {
                        $penyewa = Penyewa::where('noktp', $noktp)->first();

                        if (request()->file('fotoktp')) {
                            // Hapus file KTP lama jika ada
                            if (file_exists('img/ktp/' . $penyewa->fotoktp)) {
                                unlink('img/ktp/' . $penyewa->fotoktp);
                            }

                            $fotoktp = "penyewa" . "-" . $penyewa->id . "." . request()->file('fotoktp')->getClientOriginalExtension();
                            $file = request()->file('fotoktp');
                            $tujuan_upload = 'img/ktp';
                            $file->move($tujuan_upload, $fotoktp);
                        } else {
                            $fotoktp = $penyewa->fotoktp;
                        }

                        Penyewa::where('id', $penyewa->id)->update([
                            'namalengkap' => $namalengkap,
                            'noktp' => $noktp,
                            'nohp' => $nohp,
                            'alamat' => $alamat,
                            'fotoktp' => $fotoktp,
                            'status' => 1,
                            'operator_id' => auth()->user()->id,
                            'updated_at' => date("Y-m-d H:i:s"),
                        ]);
                    }

                    // pembayaran detail
                    $pembayarandetail = new Pembayarandetail();
                    $pembayarandetail->pembayaran_id = $pembayaran_id;
                    $pembayarandetail->penyewa_id = $penyewa->id;
                    $pembayarandetail->save();

                    Pembayaran::where('id', $pembayaran_id)->update([
                        'jumlah_penyewa' => Pembayarandetail::where('pembayaran_id', intval($pembayaran_id))->get()->count()
                    ]);

                    $response = [
                        'status' => 200,
                        'message' => 'success',
                    ];
                }

                DB::commit();
                return response()->json($response);
            } catch (Exception $e) {
                $response = [
                    'status' => 500,
                    'message' => $e->getMessage(),
                ];

                DB::rollBack();
                return response()->json($response);
            }
        }
    }
    // public function detailpenyewa(Penyewa $penyewa)
    // {
    //     if ($penyewa->status == 0) {
    //         abort(404);
    //     }

    //     if ($penyewa->transaksisewa_kamars) {
    //         $kamar = Lokasi::where('id', $penyewa->transaksisewa_kamars->lokasi_id)->first();
    //         if ($kamar->status == 1 || $kamar->status == 2) {
    //             $data = [
    //                 'judul' => 'Detail Penyewa',
    //                 'penyewa' => $penyewa,
    //                 'kamar' => $kamar,
    //                 // 'tenggatwaktu' => $tenggatwaktu
    //             ];

    //             return view('contents.dashboard.penyewa.penyewaankamar.detailpenyewa', $data);
    //         } else {
    //             abort(404);
    //         }
    //     } else {
    //         abort(404);
    //     }
    // }
}
