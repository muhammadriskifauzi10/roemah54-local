<?php

namespace App\Http\Controllers\Dashboard\Penyewa\Dendacheckout;

use App\Http\Controllers\Controller;
use App\Models\Denda;
use App\Models\Transaksi;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function index()
    {
        $data = [
            'judul' => 'Denda Checkout',
        ];

        return view('contents.dashboard.penyewa.dendacheckout.main', $data);
    }
    public function datatabledendacheckout()
    {
        $minDate = request()->input('minDate');
        $maxDate = request()->input('maxDate');
        $status_pembayaran = request()->input('status_pembayaran');

        $dendacheckout = Denda::where('tagih_id', 3)
            ->when($minDate && $maxDate, function ($query) use ($minDate, $maxDate) {
                $query->whereDate('tanggal_denda', '>=', $minDate)
                    ->whereDate('tanggal_denda', '<=', $maxDate);
            })
            ->when($status_pembayaran !== "Pilih Status Pembayaran", function ($query) use ($status_pembayaran) {
                $query->where('status_pembayaran', $status_pembayaran);
            })
            ->orderBy('created_at', 'DESC')
            ->get();

        $output = [];
        $no = 1;
        foreach ($dendacheckout as $row) {
            // status pembayaran
            if ($row->status_pembayaran == 1) {
                $status_pembayaran = "<span class='badge bg-success'>Sudah Dibayar</span>";
                $bayar = '';
            } else if ($row->status_pembayaran == 2) {
                $status_pembayaran = "<span class='badge bg-danger'>Belum Dibayar</span>";
                $bayar = '
                <button type="button" class="btn btn-success text-light fw-bold" onclick="openModalBayarDenda(' . $row->id . ')" style="width: 180px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-credit-card" viewBox="0 0 16 16">
                        <path
                            d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v1h14V4a1 1 0 0 0-1-1zm13 4H1v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1z" />
                        <path d="M2 10a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z" />
                    </svg>
                    Bayar Denda
                </button>';
            }

            $aksi = '<div class="d-flex align-items-center justify-content-center gap-1">
            ' . $bayar . '
            </div>';

            $output[] = [
                'nomor' => '<strong>' . $no++ . '</strong>',
                'tanggal_denda' => Carbon::parse($row->tanggal_denda)->format("Y-m-d H:i:s"),
                'nama_penyewa' => $row->penyewas->namalengkap,
                'nomor_kamar' => $row->lokasis->nomor_kamar,
                'jumlah_uang' => $row->jumlah_uang ? "RP. " . number_format($row->jumlah_uang, '0', '.', '.') : "RP. 0",
                'status_pembayaran' => $status_pembayaran,
                'aksi' => $aksi
            ];
        }

        return response()->json([
            'data' => $output
        ]);
    }
    public function getmodalbayardenda()
    {
        if (request()->ajax()) {
            $denda_id = htmlspecialchars(request()->input('denda_id'), ENT_QUOTES, 'UTF-8');
            if (Denda::where('id', (int)$denda_id)->exists()) {
                $dataHTML = '
                <form class="modal-content" onsubmit="requestBayarDenda(event)" autocomplete="off">
                    <input type="hidden" name="__token" value="' . request()->input('token') . '" id="token">
                    <input type="hidden" name="denda_id" value="' . $denda_id . '" id="denda_id">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="universalModalLabel">Bayar Denda</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="cash" class="form-label fw-bold">
                                Metode Pembayaran
                            </label>
                            <div class="row">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="metode_pembayaran" id="cash"
                                        value="Cash" checked>
                                    <label class="form-check-label" for="cash">
                                        Cash
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="metode_pembayaran" id="debit"
                                        value="Debit">
                                    <label class="form-check-label" for="debit">
                                        Debit
                                    </label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="metode_pembayaran" id="qris"
                                        value="QRIS">
                                    <label class="form-check-label" for="qris">
                                        QRIS
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="metode_pembayaran" id="transfer"
                                        value="Transfer">
                                    <label class="form-check-label" for="transfer">
                                        Transfer
                                    </label>
                                </div>
                            </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-end">
                            <button type="submit" class="btn btn-success w-100" id="btnRequest">
                                Ya
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
        }

        return response()->json($response);
    }
    public function bayardenda()
    {
        if (request()->ajax()) {
            $denda_id = htmlspecialchars(request()->input('denda_id'), ENT_QUOTES, 'UTF-8');
            $metode_pembayaran = htmlspecialchars(request()->input('metode_pembayaran'), ENT_QUOTES, 'UTF-8');
            if (Denda::where('id', $denda_id)->exists()) {
                try {
                    DB::beginTransaction();

                    $model_denda = Denda::where('id', $denda_id)->first();

                    if ($model_denda->pembayaran_id) {
                        $pembayaran_id = $model_denda->pembayaran_id;
                    } else {
                        $pembayaran_id = NULL;
                    }

                    // denda
                    Denda::where('id', $model_denda->id)->update([
                        'status_pembayaran' => 1,
                        'operator_id' => auth()->user()->id,
                        'updated_at' => date("Y-m-d H:i:s"),
                    ]);

                    // server
                    DB::connection("mysqldua")->table("dendas")->where('id', $model_denda->id)->update([
                        'status_pembayaran' => 1,
                        'operator_id' => auth()->user()->id,
                        'updated_at' => date("Y-m-d H:i:s"),
                    ]);

                    // Generate no transaksi
                    $tahun = date('Y');
                    $bulan = date('m');
                    $tanggal = date('d');
                    $infoterakhir = Transaksi::orderBy('created_at', 'DESC')->first();

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

                    $transaksi = new Transaksi();
                    $transaksi->no_transaksi = $no_transaksi;
                    $transaksi->tagih_id = 3;
                    $transaksi->pembayaran_id = $pembayaran_id;
                    $transaksi->tanggal_transaksi = date('Y-m-d H:i:s');
                    $transaksi->jumlah_uang = intval($model_denda->jumlah_uang);
                    $transaksi->metode_pembayaran = $metode_pembayaran;
                    $transaksi->tipe = "pemasukan";
                    $transaksi->operator_id = auth()->user()->id;
                    $transaksi->save();

                    // server
                    DB::connection("mysqldua")->table("transaksis")->insert([
                        'no_transaksi' => $no_transaksi,
                        'tagih_id' => 3,
                        'pembayaran_id' => $pembayaran_id,
                        'tanggal_transaksi' => date('Y-m-d H:i:s'),
                        'jumlah_uang' => intval($model_denda->jumlah_uang),
                        'metode_pembayaran' => $metode_pembayaran,
                        'tipe' => "pemasukan",
                        'operator_id' => auth()->user()->id,
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s"),
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
            } else {
                $response = [
                    'status' => 404,
                    'message' => 'error',
                ];

                return response()->json($response);
            }
        }
    }
}
