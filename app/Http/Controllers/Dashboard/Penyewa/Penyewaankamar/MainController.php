<?php

namespace App\Http\Controllers\Dashboard\Penyewa\Penyewaankamar;

use App\Http\Controllers\Controller;
use App\Models\Denda;
use App\Models\Lokasi;
use App\Models\Pembayaran;
use App\Models\Pembayarandetail;
use App\Models\Penyewa;
use App\Models\Tipekamar;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MainController extends Controller
{
    public function index()
    {
        $penyewa = Penyewa::all();

        $data = [
            'judul' => 'Penyewaan Kamar',
            'penyewa' => $penyewa,
        ];

        return view('contents.dashboard.penyewa.penyewaankamar.main', $data);
    }
    public function datatablepenyewaankamar()
    {
        $minDate = request()->input('minDate');
        $maxDate = request()->input('maxDate');
        $penyewa = request()->input('penyewa');
        $status_pembayaran = request()->input('status_pembayaran');

        $penyewaankamar = Pembayaran::when($minDate && $maxDate, function ($query) use ($minDate, $maxDate) {
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
        $nomor = 1;
        foreach ($penyewaankamar as $row) {
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
                <a href="' . route('penyewaankamar.cetakkwitansi', encrypt($row->id)) . '" class="btn btn-success text-light fw-bold d-flex align-items-center justify-content-center gap-1" style="width: 180px;" target="_blank">
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
                     <a href="' . route('penyewaankamar.cetakinvoice', encrypt($row->id)) . '" class="btn btn-warning text-light fw-bold d-flex align-items-center justify-content-center gap-1" style="width: 180px;" target="_blank">
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

                // if ($row->status == 1) {
                //     $pulangkantamu = '
                //     <button type="button" class="btn btn-danger text-light fw-bold d-flex align-items-center justify-content-center gap-1" onclick="requestPulangkanTamu(' . $row->id . ')" style="width: 180px;">
                //         <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                //             class="bi bi-person-raised-hand" viewBox="0 0 16 16">
                //             <path
                //                 d="M6 6.207v9.043a.75.75 0 0 0 1.5 0V10.5a.5.5 0 0 1 1 0v4.75a.75.75 0 0 0 1.5 0v-8.5a.25.25 0 1 1 .5 0v2.5a.75.75 0 0 0 1.5 0V6.5a3 3 0 0 0-3-3H6.236a1 1 0 0 1-.447-.106l-.33-.165A.83.83 0 0 1 5 2.488V.75a.75.75 0 0 0-1.5 0v2.083c0 .715.404 1.37 1.044 1.689L5.5 5c.32.32.5.754.5 1.207" />
                //             <path d="M8 3a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3" />
                //         </svg>
                //         Pulangkan Tamu
                //     </button>';
                // } else {
                    $pulangkantamu = '';
                // }
            } elseif ($row->status_pembayaran == "failed") {
                $bayar = '';
                $pulangkantamu = '';
            }

            $aksi = '
            <div class="d-flex flex-column align-items-center justify-content-center gap-1">
                <a href="' . route('penyewaankamar.detail', $row->id) . '" class="btn btn-info text-light fw-bold d-flex align-items-center justify-content-center gap-1" style="width: 180px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                    </svg>    
                    Detail
                </a>
                ' . $bayar . '
                ' . $cetakpembayaran . '
                ' . $pulangkantamu . '
            
            </div>
            ';

            $output[] = [
                'nomor' => "<strong>" . $nomor++ . "</strong>",
                'tanggal_masuk' => Carbon::parse($row->tanggal_masuk)->format("Y-m-d H:i:s"),
                'tanggal_keluar' => Carbon::parse($row->tanggal_keluar)->format("Y-m-d H:i:s"),
                'nomor_kamar' => $row->lokasis->nomor_kamar,
                'tipe_kamar' => $row->tipekamar,
                'mitra' => $row->mitras->mitra,
                'jenissewa' => $row->jenissewa,
                'jumlah_pembayaran' => $row->jumlah_pembayaran ? "RP. " . number_format($row->jumlah_pembayaran, '0', '.', '.') : "RP. 0",
                'diskon' => intval($row->diskon) . " %",
                'potongan_harga' => $row->potongan_harga ? "RP. " . number_format($row->potongan_harga, '0', '.', '.') : "RP. 0",
                'total_bayar' => $row->total_bayar ? "RP. " . number_format($row->total_bayar, '0', '.', '.') : "RP. 0",
                'tanggal_pembayaran' => $row->tanggal_pembayaran ? Carbon::parse($row->tanggal_pembayaran)->format("Y-m-d H:i:s") : "-",
                'kurang_bayar' => $row->kurang_bayar ? "RP. " . number_format($row->kurang_bayar, '0', '.', '.') : "RP. 0",
                // 'jumlah_penyewa' => $row->jumlah_penyewa . ' Orang',
                'status_pembayaran' => $status_pembayaran,
                'aksi' => $aksi,
            ];
        }

        return response()->json([
            'data' => $output
        ]);
    }
    public function detail($id)
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
    public function cetakkwitansi($id)
    {
        $id = decrypt($id);

        if (!Pembayaran::where('id', $id)->exists()) {
            abort(404);
        }

        $pembayaran = Pembayaran::where('id', $id)->first();

        // Generate QR code as SVG
        $qrcode = QrCode::format('svg')->size(200)->generate('https://example.com');

        $data = [
            'judul' => 'Penyewaan Kamar',
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
        $pdf = Pdf::loadView('contents.dashboard.penyewa.penyewaankamar.downloadpdf.cetakkwitansi', $data);
        return $pdf->stream('cetakkwitansi.pdf');
        // return view('contents.dashboard.downloadpdf.cetakinvoice', $data);
    }
    public function cetakinvoice($id)
    {
        $id = decrypt($id);

        if (!Pembayaran::where('id', $id)->exists()) {
            abort(404);
        }

        $pembayaran = Pembayaran::where('id', $id)->first();

        // Generate QR code as SVG
        $qrcode = QrCode::format('svg')->size(200)->generate('https://example.com');

        $data = [
            'judul' => 'Penyewaan Kamar',
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
        $pdf = Pdf::loadView('contents.dashboard.penyewa.penyewaankamar.downloadpdf.cetakinvoice', $data);
        return $pdf->stream('cetakinvoice.pdf');
        // return view('contents.dashboard.downloadpdf.cetakinvoice', $data);
    }
    public function pulangkantamu()
    {
        if (request()->ajax()) {
            $pembayaran_id = htmlspecialchars(request()->input('pembayaran_id'), ENT_QUOTES, 'UTF-8');
            if (Pembayaran::where('id', $pembayaran_id)->exists()) {
                try {
                    DB::beginTransaction();

                    $model_pembayaran = Pembayaran::where('id', $pembayaran_id)->first();

                    Pembayaran::where('id', $model_pembayaran->id)->update([
                        'status' => 0,
                        'operator_id' => auth()->user()->id,
                        'updated_at' => date("Y-m-d H:i:s"),
                    ]);

                    Pembayarandetail::where('pembayaran_id', $model_pembayaran->id)->update([
                        'status' => 0
                    ]);

                    foreach (Pembayarandetail::where('pembayaran_id', $model_pembayaran->id)->get() as $row) {
                        if (Pembayarandetail::where('penyewa_id', $row->penyewa_id)->where('status', 1)->count() < 1) {
                            Penyewa::where('id', $row->penyewa_id)->update([
                                'status' => 0,
                                'operator_id' => auth()->user()->id,
                                'updated_at' => date("Y-m-d H:i:s"),
                            ]);
                        }
                    }

                    // kosongkan kamar
                    Lokasi::where('id', $model_pembayaran->lokasi_id)->update([
                        'status' => 0,
                        'operator_id' => auth()->user()->id,
                        'updated_at' => date("Y-m-d H:i:s"),
                    ]);

                    // // denda checkout
                    // $givenDateTime = Carbon::create($model_pembayaran->tanggal_keluar);

                    // // Ambil waktu sekarang
                    // $now = Carbon::now();

                    // // Tentukan waktu batasan (15:00 pada tanggal yang sama)
                    // $limitTime = $givenDateTime->copy()->setHour(15)->setMinute(0)->setSecond(0);

                    // // Hitung pembayaran berdasarkan waktu sekarang dan waktu batasan
                    // if ($now->greaterThanOrEqualTo($givenDateTime) && $now->greaterThanOrEqualTo($limitTime)) {
                    //     $model_denda_checkout = new Denda();
                    //     $model_denda_checkout->tanggal_denda = date('Y-m-d H:i:s');
                    //     $model_denda_checkout->pembayaran_id = $model_pembayaran->id;
                    //     $model_denda_checkout->penyewa_id = $model_pembayaran->penyewa_id;
                    //     $model_denda_checkout->lokasi_id = $model_pembayaran->lokasi_id;
                    //     $model_denda_checkout->tagih_id = 3;
                    //     $model_denda_checkout->jumlah_uang = 100000;
                    //     $model_denda_checkout->operator_id = auth()->user()->id;
                    //     $model_denda_checkout->save();
                    // } elseif ($now->greaterThanOrEqualTo($givenDateTime) && $now->lessThan($limitTime)) {
                    //     $model_denda_checkout = new Denda();
                    //     $model_denda_checkout->tanggal_denda = date('Y-m-d H:i:s');
                    //     $model_denda_checkout->pembayaran_id = $model_pembayaran->id;
                    //     $model_denda_checkout->penyewa_id = $model_pembayaran->penyewa_id;
                    //     $model_denda_checkout->lokasi_id = $model_pembayaran->lokasi_id;
                    //     $model_denda_checkout->tagih_id = 3;
                    //     $model_denda_checkout->jumlah_uang = 50000;
                    //     $model_denda_checkout->operator_id = auth()->user()->id;
                    //     $model_denda_checkout->save();
                    // }

                    $response = [
                        'status' => 200,
                        'message' => 'success',
                    ];

                    DB::commit();
                    return response()->json($response);
                } catch (Exception $e) {
                    $response = [
                        'status' => 500,
                        'message' => 'error',
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
    // ajax request
    public function getrequestformsewaonktp()
    {
        if (request()->ajax()) {
            $noktp = htmlspecialchars(request()->input('noktp'), ENT_QUOTES, 'UTF-8');
            if (Penyewa::where('noktp', $noktp)->exists()) {
                try {
                    DB::beginTransaction();

                    $penyewa = Penyewa::where('noktp', $noktp)->first();

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
}
