<?php

namespace App\Http\Controllers\Dashboard\Asrama\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Asrama;
use App\Models\Asramadetail;
use App\Models\Lokasi;
use App\Models\Penyewa;
use App\Models\Transaksiasrama;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MainController extends Controller
{
    public function index()
    {
        $penyewa = Penyewa::where('jenis_penyewa', 'Mahasiswa')->get();

        $data = [
            'judul' => 'Asrama Mahasiswa',
            'penyewa' => $penyewa,
        ];

        return view('contents.dashboard.asrama.mahasiswa.main', $data);
    }
    public function datatableasramamahasiswa()
    {
        $minDate = request()->input('minDate');
        $maxDate = request()->input('maxDate');
        $penyewa = request()->input('penyewa');
        $status_pembayaran = request()->input('status_pembayaran');

        $asramadetail = Asramadetail::when($minDate && $maxDate, function ($query) use ($minDate, $maxDate) {
            $query->whereDate('tanggal_masuk', '>=', $minDate)
                ->whereDate('tanggal_masuk', '<=', $maxDate);
        })
            ->when($penyewa !== "Pilih Penyewa", function ($query) use ($penyewa) {
                $query->where('penyewa_id', $penyewa);
            })
            ->when($status_pembayaran !== "Pilih Status Pembayaran", function ($query) use ($status_pembayaran) {
                $query->where('status_pembayaran', $status_pembayaran);
            })
            ->orderBy('tanggal_masuk', 'DESC')
            ->get();

        $output = [];
        $no = 1;
        foreach ($asramadetail as $row) {
            // status pembayaran
            if ($row->status_pembayaran == "completed") {
                $status_pembayaran = "<span class='badge bg-success'>Lunas</span>";
            } elseif ($row->status_pembayaran == "pending") {
                $status_pembayaran = "<span class='badge bg-warning text-light'>Belum Lunas</span>";
            } elseif ($row->status_pembayaran == "failed") {
                $status_pembayaran = "<span class='badge bg-danger'>Dibatalkan</span>";
            }

            // tombol cetak kwitansi
            if ($row->tanggal_pembayaran && in_array($row->status_pembayaran, ['completed', 'pending'])) {
                $cetakpembayaran = '
                <a href="' . route('asrama.mahasiswa.cetakkwitansi', encrypt($row->id)) . '" class="btn btn-success text-light fw-bold d-flex align-items-center justify-content-center gap-1" style="width: 180px;" target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16">
                        <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/>
                        <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1"/>
                    </svg>
                    Cetak Kwitansi
                </a>';
            } else {
                if ($row->status_pembayaran == "failed") {
                    $cetakpembayaran = "";
                } else {
                    $cetakpembayaran = '
                     <a href="' . route('asrama.mahasiswa.cetakinvoice', encrypt($row->id)) . '" class="btn btn-warning text-light fw-bold d-flex align-items-center justify-content-center gap-1" style="width: 180px;" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16">
                            <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/>
                            <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1"/>
                        </svg>
                        Cetak Invoice
                    </a>';
                }
            }

            // status penyewa
            if ($row->status_pembayaran == "completed") {
                $bayar = '';
                if ($row->status == 1) {
                    $pulangkantamu = '
                    <button type="button" class="btn btn-danger text-light fw-bold d-flex align-items-center justify-content-center gap-1" onclick="requestPulangkanTamu(' . $row->id . ')" style="width: 180px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-person-raised-hand" viewBox="0 0 16 16">
                            <path
                                d="M6 6.207v9.043a.75.75 0 0 0 1.5 0V10.5a.5.5 0 0 1 1 0v4.75a.75.75 0 0 0 1.5 0v-8.5a.25.25 0 1 1 .5 0v2.5a.75.75 0 0 0 1.5 0V6.5a3 3 0 0 0-3-3H6.236a1 1 0 0 1-.447-.106l-.33-.165A.83.83 0 0 1 5 2.488V.75a.75.75 0 0 0-1.5 0v2.083c0 .715.404 1.37 1.044 1.689L5.5 5c.32.32.5.754.5 1.207" />
                            <path d="M8 3a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3" />
                        </svg>
                        Pulangkan Tamu
                    </button>';
                } else {
                    $pulangkantamu = '';
                }
            } else if ($row->status_pembayaran == "pending") {
                $bayar = '
                <button type="button" class="btn btn-success text-light fw-bold d-flex align-items-center justify-content-center gap-1" onclick="openModalBayarKamar(event, ' . $row->id . ')" style="width: 180px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-credit-card" viewBox="0 0 16 16">
                        <path
                            d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v1h14V4a1 1 0 0 0-1-1zm13 4H1v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1z" />
                        <path d="M2 10a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z" />
                    </svg>
                    Bayar
                </button>';

                if ($row->status == 1) {
                    $pulangkantamu = '
                    <button type="button" class="btn btn-danger text-light fw-bold d-flex align-items-center justify-content-center gap-1" onclick="requestPulangkanTamu(' . $row->id . ')" style="width: 180px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-person-raised-hand" viewBox="0 0 16 16">
                            <path
                                d="M6 6.207v9.043a.75.75 0 0 0 1.5 0V10.5a.5.5 0 0 1 1 0v4.75a.75.75 0 0 0 1.5 0v-8.5a.25.25 0 1 1 .5 0v2.5a.75.75 0 0 0 1.5 0V6.5a3 3 0 0 0-3-3H6.236a1 1 0 0 1-.447-.106l-.33-.165A.83.83 0 0 1 5 2.488V.75a.75.75 0 0 0-1.5 0v2.083c0 .715.404 1.37 1.044 1.689L5.5 5c.32.32.5.754.5 1.207" />
                            <path d="M8 3a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3" />
                        </svg>
                        Pulangkan Tamu
                    </button>';
                } else {
                    $pulangkantamu = '';
                }
            } elseif ($row->status_pembayaran == "failed") {
                $bayar = '';
                $pulangkantamu = '';
            }

            $aksi = '
            <div class="d-flex flex-column align-items-center justify-content-center gap-1">
                ' . $bayar . '
                ' . $cetakpembayaran . '
                ' . $pulangkantamu . '
            
            </div>
            ';

            $output[] = [
                'nomor' => "<strong>" . $no++ . "</strong>",
                'tanggal_masuk' => Carbon::parse($row->tanggal_masuk)->format("Y-m-d H:i:s"),
                'tanggal_keluar' => Carbon::parse($row->tanggal_keluar)->format("Y-m-d H:i:s"),
                'nama_penyewa' => $row->penyewas->namalengkap,
                'jumlah_pembayaran' => $row->jumlah_pembayaran ? "RP. " . number_format($row->jumlah_pembayaran, '0', '.', '.') : "RP. 0",
                'potongan_harga' => $row->potongan_harga ? "RP. " . number_format($row->potongan_harga, '0', '.', '.') : "RP. 0",
                'total_bayar' => $row->total_bayar ? "RP. " . number_format($row->total_bayar, '0', '.', '.') : "RP. 0",
                'tanggal_pembayaran' => $row->tanggal_pembayaran ? Carbon::parse($row->tanggal_pembayaran)->format("Y-m-d H:i:s") : "-",
                'kurang_bayar' => $row->kurang_bayar ? "RP. " . number_format($row->kurang_bayar, '0', '.', '.') : "RP. 0",
                'status_pembayaran' => $status_pembayaran,
                'aksi' => $aksi,
            ];
        }

        return response()->json([
            'data' => $output
        ]);
    }
    public function getmodaltambahpenyewa()
    {
        if (request()->ajax()) {
            $optionasrama = [];

            foreach (
                Asrama::select(
                    'asramas.id',
                    'asramas.nomor_kamar',
                    'asramas.tipekamar',
                    't.kapasitas',
                    'asramas.tipekamar_id',
                    'asramas.jumlah_mahasiswa'
                )
                    ->join('tipekamars as t', 'asramas.tipekamar_id', '=', 't.id')
                    ->whereColumn('asramas.jumlah_mahasiswa', '<=', 't.kapasitas')
                    ->get()
                as $row
            ) {
                $optionasrama[] = ' <option value="' . $row->id . '">Nomor Kamar: ' . $row->nomor_kamar . ' | Tipe Kamar: ' . $row->tipekamars->tipekamar . '</option>';
            }

            $dataHTML = '
            <form class="modal-content" onsubmit="requestTambahPenyewa(event)" autocomplete="off" enctype="multipart/form-data" id="formtambahpenyewa">
                <input type="hidden" name="__token" value="' . request()->input('_token') . '" id="token">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="universalModalLabel">Tambah Penyewa</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card border-2 border-dark mb-3">
                        <div class="card-header bg-dark text-light">
                            Data Diri
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="noktp" class="form-label fw-bold">No KTP <sup class="red">*</sup></label>
                                <input type="text" class="form-control" name="noktp" id="noktp" oninput="onNoKtp(event)">
                                <span class="text-danger" id="errorNoKTP"></span>
                            </div>
                            <div class="mb-3">
                                <label for="namalengkap" class="form-label fw-bold">Nama Lengkap <sup class="red">*</sup></label>
                                <input type="text" class="form-control" name="namalengkap" id="namalengkap">
                                <span class="text-danger" id="errorNamaLengkap"></span>
                            </div>
                            <div class="mb-3">
                                <label for="nohp" class="form-label fw-bold">No HP <sup class="red">*</sup></label>
                                <input type="text" class="form-control" name="nohp" id="nohp">
                                <span class="text-danger" id="errorNoHP"></span>
                            </div>
                            <div class="mb-3">
                                <label for="alamat" class="form-label fw-bold">Alamat <sup class="red">*</sup></label>
                                <textarea class="form-control" name="alamat" id="alamat"></textarea>
                                <span class="text-danger" id="errorAlamat"></span>
                            </div>
                            <div>
                                <label for="fotoktp" class="form-label fw-bold">Foto KTP <sup class="red">*</sup></label>
                                <input type="file" class="form-control" name="fotoktp" id="fotoktp">
                                <span class="text-danger" id="errorFotoKTP"></span>
                            </div>
                        </div>
                    </div>
                    <div class="card border-2 border-dark mb-3">
                        <div class="card-header bg-dark text-light">
                            Penginapan Asrama
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="asrama" class="form-label fw-bold">asrama <sup class="text-danger">*</sup></label>
                                <select class="form-select form-select-2"
                                    name="asrama" id="asrama" style="width: 100%;">
                                    <option>Pilih Kamar</option>
                                    ' . implode(" ", $optionasrama) . '
                                </select>
                                <span class="text-danger" id="errorKamar"></span>
                            </div>
                            <div>
                                <label for="tanggalmasuk" class="form-label fw-bold">Tanggal Masuk <sup class="red">*</sup></label>
                                <input type="date" class="form-control" name="tanggalmasuk" id="tanggalmasuk">
                                <span class="text-danger" id="errorTanggalMasuk"></span>
                            </div>
                        </div>
                    </div>
                    <div class="card border-2 border-dark mb-3">
                        <div class="card-header bg-dark text-light">
                            Pembayaran
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="total_bayar" class="form-label fw-bold">Total
                                        Bayar</label>
                                <div class="input-group" style="z-index: 0;">
                                    <span class="input-group-text bg-success text-light fw-bold">RP</span>
                                    <input type="text"
                                        class="form-control formatrupiah"
                                        name="total_bayar" id="total_bayar" placeholder="0">
                                </div>
                                <span class="text-danger" id="errorTotalBayar"></span>
                            </div>
                            <div>
                                <div class="row">
                                    <div class="mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio"
                                                name="metode_pembayaran" id="none" value="Tidak Ada" checked>
                                            <label class="form-check-label" for="none">
                                                Tidak Ada
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio"
                                                name="metode_pembayaran" id="cash" value="Cash">
                                            <label class="form-check-label" for="cash">
                                                Cash
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio"
                                                name="metode_pembayaran" id="debit" value="Debit">
                                            <label class="form-check-label" for="debit">
                                                Debit
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio"
                                                name="metode_pembayaran" id="qris" value="QRIS">
                                            <label class="form-check-label" for="qris">
                                                QRIS
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio"
                                                name="metode_pembayaran" id="transfer" value="Transfer">
                                            <label class="form-check-label" for="transfer">
                                                Transfer
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-success w-100" id="btnRequest">
                            Sewa
                        </button>
                    </div>
                </div>
            </form>
            ';

            $response = [
                'status' => 200,
                'message' => 'success',
                'dataHTML' => $dataHTML
            ];
        } else {
            $response = [
                'status' => 400,
                'message' => 'opps',
            ];
        }

        return response()->json($response);
    }
    public function tambahpenyewa()
    {
        if (request()->ajax()) {
            // data diri
            $noktp = htmlspecialchars(request()->input('noktp'), true);
            $namalengkap = htmlspecialchars(request()->input('namalengkap'), true);
            $nohp = htmlspecialchars(request()->input('nohp'), true);
            $alamat = htmlspecialchars(request()->input('alamat'), true);
            $fotoktp = request()->file('fotoktp');
            // penginapan kamar
            $asrama = htmlspecialchars(request()->input('asrama'), true);
            $tanggalmasuk = htmlspecialchars(request()->input('tanggalmasuk'), true);
            $tanggalmasuk_format = Carbon::parse($tanggalmasuk)->format('Y-m-d H:i');
            // pembayaran
            $total_bayar = htmlspecialchars(request()->input('total_bayar'), true);
            $metode_pembayaran = htmlspecialchars(request()->input('metode_pembayaran'), true);

            if ($total_bayar) {
                $total_bayar = str_replace(".", "", $total_bayar);
            } else {
                $total_bayar = 0;
            }

            if (!Penyewa::where('noktp', $noktp)->where('jenis_penyewa', 'Mahasiswa')->exists()) {
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
                'kamar' => [
                    function ($attribute, $value, $fail) {
                        if (!Lokasi::where('id', (int)$value)->exists()) {
                            $fail('Kolom ini wajib dipilih');
                        }
                    },
                ],
                'tanggalmasuk' => 'required|date',
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
                'tanggalmasuk.required' => 'Kolom ini wajib diisi',
                'tanggalmasuk.date' => 'Kolom ini wajib diisi',
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

                $penyewa = Penyewa::where('noktp', $noktp)->where('jenis_penyewa', 'Mahasiswa')->first();

                if (!Penyewa::where('noktp', $noktp)->where('jenis_penyewa', 'Mahasiswa')->exists()) {
                    $penyewa = Penyewa::create([
                        'namalengkap' => $namalengkap,
                        'noktp' => $noktp,
                        'nohp' => $nohp,
                        'alamat' => $alamat,
                        'jenis_penyewa' => 'Mahasiswa',
                        'fotoktp' => "",
                        'operator_id' => auth()->user()->id,
                    ]);

                    $fotoktp = "penyewa" . "-" . $penyewa->id . "." .  request()->file('fotoktp')->getClientOriginalExtension();
                    $file = request()->file('fotoktp');
                    $tujuan_upload = 'img/ktp/asrama';
                    $file->move($tujuan_upload, $fotoktp);

                    Penyewa::where('id', $penyewa->id)->update([
                        'fotoktp' => $fotoktp,
                    ]);
                } else {
                    $penyewa = Penyewa::where('noktp', $noktp)->first();

                    if (request()->file('fotoktp')) {
                        // Hapus file KTP lama jika ada
                        if (file_exists('img/ktp/asrama/' . $penyewa->fotoktp)) {
                            unlink('img/ktp/asrama/' . $penyewa->fotoktp);
                        }

                        $fotoktp = "penyewa" . "-" . $penyewa->id . "." . request()->file('fotoktp')->getClientOriginalExtension();
                        $file = request()->file('fotoktp');
                        $tujuan_upload = 'img/ktp/asrama';
                        $file->move($tujuan_upload, $fotoktp);
                    } else {
                        $fotoktp = $penyewa->fotoktp;
                    }

                    Penyewa::where('id', $penyewa->id)->update([
                        'namalengkap' => $namalengkap,
                        'noktp' => $noktp,
                        'nohp' => $nohp,
                        'alamat' => $alamat,
                        'jenis_penyewa' => 'Mahasiswa',
                        'fotoktp' => $fotoktp,
                        'status' => 1,
                        'operator_id' => auth()->user()->id,
                        'updated_at' => date("Y-m-d H:i:s"),
                    ]);
                }

                // Bulanan (Monthly)
                $tenggatwaktu = Carbon::parse($this->check_out($tanggalmasuk_format))->addMonth();
                $potongan_harga = 0;
                $jumlah_pembayaran = 500000;

                if (intval($total_bayar) >= intval($jumlah_pembayaran)) {
                    $status_pembayaran = "completed";
                } else {
                    $status_pembayaran = "pending";
                }

                $asramadetail = new Asramadetail();
                $asramadetail->asrama_id = $asrama;
                if (intval($total_bayar) > 0) {
                    $asramadetail->tanggal_pembayaran = date('Y-m-d H:i:s');
                }
                $asramadetail->tanggal_masuk = $tanggalmasuk_format;
                $asramadetail->tanggal_keluar = $tenggatwaktu;
                $asramadetail->penyewa_id = $penyewa->id;
                $asramadetail->jumlah_pembayaran = intval($jumlah_pembayaran) + intval($potongan_harga);
                $asramadetail->potongan_harga = intval($potongan_harga);
                $asramadetail->total_bayar = $total_bayar;
                $asramadetail->kurang_bayar = intval($jumlah_pembayaran) - intval($total_bayar);
                $asramadetail->status_pembayaran = $status_pembayaran;
                $asramadetail->status = 1;
                $asramadetail->operator_id = auth()->user()->id;
                $post = $asramadetail->save();

                if ($post) {
                    if (intval($total_bayar) > 0) {
                        // Generate no transaksi
                        $tahun = date('Y');
                        $bulan = date('m');
                        $tanggal = date('d');
                        $infoterakhir = Transaksiasrama::orderBy('created_at', 'DESC')->first();

                        if ($infoterakhir) {
                            $tahunterakhir = Carbon::parse($infoterakhir->created_at)->format('Y') ?? 0;
                            $bulanterakhir = Carbon::parse($infoterakhir->created_at)->format('m') ?? 0;
                            $tanggalterakhir = Carbon::parse($infoterakhir->created_at)->format('d') ?? 0;
                            $nomor = substr($infoterakhir->no_transaksi, 6);

                            if ($tahun != $tahunterakhir || $bulan != $bulanterakhir || $tanggal != $tanggalterakhir) {
                                $nomor = 0;
                            }
                        } else {
                            $nomor = 0;
                        }

                        // yymmddxxxxxx
                        $no_transaksi = sprintf('%02d%02d%02d%06d', date('y'), $bulan, $tanggal, $nomor + 1);

                        $transaksi = new Transaksiasrama();
                        $transaksi->asrama_id = $asrama;
                        $transaksi->penyewa_id = $penyewa->id;
                        $transaksi->no_transaksi = $no_transaksi;
                        $transaksi->tagih_id = 1;
                        $transaksi->tanggal_transaksi = date('Y-m-d H:i:s');
                        $transaksi->jumlah_uang = $total_bayar;
                        $transaksi->metode_pembayaran = $metode_pembayaran;
                        $transaksi->operator_id = auth()->user()->id;
                        $transaksi->save();
                    }

                    Asrama::where('id', (int)$asrama)->increment('jumlah_mahasiswa');
                }

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
    public function cetakkwitansi($id)
    {
        $id = decrypt($id);

        if (!Asramadetail::where('id', $id)->exists()) {
            abort(404);
        }

        $asramadetail = Asramadetail::where('id', $id)->first();
        $asrama = Asrama::where('id', $asramadetail->asrama_id)->first();

        // Generate QR code as SVG
        $qrcode = QrCode::format('svg')->size(200)->generate('https://example.com');

        $data = [
            'judul' => 'Asrama Cetak Kwitansi',
            'asramadetail' => $asramadetail,
            'asrama' => $asrama,
            'qrcode' => $qrcode
        ];

        // Options and configuration for Dompdf
        $options = new Options();
        $options->set('defaultFont', 'Pacifico');

        $dompdf = new Dompdf($options);

        $dompdf->set_option('isFontSubsettingEnabled', true);

        // Define font directory
        $fontDir = storage_path('fonts');
        if (!Storage::exists($fontDir)) {
            Storage::makeDirectory($fontDir);
        }
        $dompdf->set_option('fontDir', [$fontDir]);

        // Add custom fonts
        $fontFile = $fontDir . '/pacifico_normal_fdc22bcc936095541e9d9b0e02dbdac0.ttf';
        if (!file_exists($fontFile)) {
            // Handle the case where the font file does not exist
            abort(500, 'Font file not found');
        }
        $fontMetrics = $dompdf->getFontMetrics();
        $fontMetrics->getFont('Pacifico', 'normal', $fontFile);

        // Generate PDF
        $pdf = Pdf::loadView('contents.dashboard.asrama.mahasiswa.downloadpdf.cetakkwitansi', $data);
        return $pdf->stream('cetakkwitansi.pdf');
        // return view('contents.dashboard.downloadpdf.cetakinvoice', $data);
    }
    public function cetakinvoice($id)
    {
        $id = decrypt($id);

        if (!Asramadetail::where('id', $id)->exists()) {
            abort(404);
        }

        $pembayaran = Asrama::where('id', $id)->first();

        // Generate QR code as SVG
        $qrcode = QrCode::format('svg')->size(200)->generate('https://example.com');

        $data = [
            'judul' => 'Asrama Cetak Invoice',
            'pembayaran' => $pembayaran,
            'qrcode' => $qrcode
        ];

        // Options and configuration for Dompdf
        $options = new Options();
        $options->set('defaultFont', 'Pacifico');

        $dompdf = new Dompdf($options);

        $dompdf->set_option('isFontSubsettingEnabled', true);

        // Define font directory
        $fontDir = storage_path('fonts');
        if (!Storage::exists($fontDir)) {
            Storage::makeDirectory($fontDir);
        }
        $dompdf->set_option('fontDir', [$fontDir]);

        // Add custom fonts
        $fontFile = $fontDir . '/pacifico_normal_fdc22bcc936095541e9d9b0e02dbdac0.ttf';
        if (!file_exists($fontFile)) {
            // Handle the case where the font file does not exist
            abort(500, 'Font file not found');
        }
        $fontMetrics = $dompdf->getFontMetrics();
        $fontMetrics->getFont('Pacifico', 'normal', $fontFile);

        // Generate PDF
        $pdf = Pdf::loadView('contents.dashboard.asrama.mahasiswa.downloadpdf.cetakinvoice', $data);
        return $pdf->stream('cetakinvoice.pdf');
        // return view('contents.dashboard.downloadpdf.cetakinvoice', $data);
    }
    // Helper
    public function getrequestformsewaonktp()
    {
        if (request()->ajax()) {
            $noktp = htmlspecialchars(request()->input('noktp'), ENT_QUOTES, 'UTF-8');
            if (Penyewa::where('noktp', $noktp)->where('jenis_penyewa', 'Mahasiswa')->exists()) {
                try {
                    DB::beginTransaction();

                    $penyewa = Penyewa::where('noktp', $noktp)->where('jenis_penyewa', 'Mahasiswa')->first();

                    $response = [
                        'status' => 200,
                        'data' => [
                            'namalengkap' => $penyewa->namalengkap,
                            'nohp' => $penyewa->nohp,
                            'alamat' => $penyewa->alamat
                        ],
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
            } else {
                $response = [
                    'status' => 404,
                    'message' => 'error',
                ];

                return response()->json($response);
            }
        }
    }
    public function check_out($checkinTime)
    {
        // // Konversi waktu check-in ke timestamp
        // $checkinTimestamp = strtotime($checkinTime);

        // // Dapatkan jam dari waktu check-in
        // $checkinHour = date('H', $checkinTimestamp);

        // // Tentukan waktu check-out berdasarkan aturan yang diberikan
        // if ($checkinHour < 6) {
        //     // Jika check-in sebelum jam 6 pagi, check-out pada jam 12 siang di hari yang sama
        //     $checkoutTimestamp = strtotime(date('Y-m-d', $checkinTimestamp) . ' 12:00');
        // } else {
        //     // Jika check-in pada atau setelah jam 6 pagi, check-out pada jam 12 siang keesokan harinya
        //     $checkoutTimestamp = strtotime(date('Y-m-d', $checkinTimestamp) . ' +1 day 12:00');
        // }

        // return date('Y-m-d H:i', $checkoutTimestamp);

        // Check out baru
        // Konversi waktu check-in ke timestamp
        $checkinTimestamp = strtotime($checkinTime);

        // Tentukan waktu check-out selalu pada jam 1 siang keesokan harinya
        $checkoutTimestamp = strtotime(date('Y-m-d', $checkinTimestamp) . ' +1 day 13:00');

        return date('Y-m-d H:i', $checkoutTimestamp);
    }
}
