<?php

namespace App\Http\Controllers\Dashboard\Penyewa\Penyewaankamar;

use App\Http\Controllers\Controller;
use App\Models\Lokasi;
use App\Models\Pembayaran;
use App\Models\Penyewa;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MainController extends Controller
{
    public function index()
    {
        $data = [
            'judul' => 'Penyewaan Kamar',
        ];

        return view('contents.dashboard.penyewa.penyewaankamar.main', $data);
    }
    public function datatablepenyewaankamar()
    {
        $minDate = request()->input('minDate');
        $maxDate = request()->input('maxDate');
        $status_pembayaran = request()->input('status_pembayaran');

        $penyewaankamar = Pembayaran::join('penyewas as p', 'pembayarans.penyewa_id', '=', 'p.id')
            ->select(
                'pembayarans.id',
                'pembayarans.tanggal_masuk',
                'pembayarans.tanggal_keluar',
                'pembayarans.tagih_id',
                'pembayarans.lokasi_id',
                'pembayarans.tipekamar_id',
                'pembayarans.mitra_id',
                'pembayarans.jenissewa',
                'pembayarans.jumlah_pembayaran',
                'pembayarans.diskon',
                'pembayarans.potongan_harga',
                'pembayarans.total_bayar',
                'pembayarans.tanggal_pembayaran',
                'pembayarans.kurang_bayar',
                'pembayarans.status_pembayaran',
                'p.namalengkap',
                'p.status',
            )
            ->when($minDate && $maxDate, function ($query) use ($minDate, $maxDate) {
                $query->whereDate('pembayarans.tanggal_masuk', '>=', $minDate)
                    ->whereDate('pembayarans.tanggal_masuk', '<=', $maxDate);
            })
            ->when($status_pembayaran !== "Pilih Status Pembayaran", function ($query) use ($status_pembayaran) {
                $query->where('pembayarans.status_pembayaran', $status_pembayaran);
            })
            ->where('pembayarans.tagih_id', 1)
            ->where('p.status', 1)
            ->orderBy('pembayarans.tanggal_masuk', 'ASC')
            ->get();

        $output = [];
        $no = 1;
        foreach ($penyewaankamar as $row) {
            if ($row->tanggal_pembayaran && in_array($row->status_pembayaran, ['completed', 'pending'])) {
                $cetak = '
                    <div class="d-flex align-items-center justify-content-center gap-1">
                        <a href="' . route('penyewaankamar.cetakkwitansi', encrypt($row->id)) . '" class="btn btn-success d-flex align-items-center justify-content-center gap-1" style="width: 160px;" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16">
                                <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/>
                                <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1"/>
                            </svg>
                            Cetak Kwitansi
                        </a>
                    </div>';
            } else {
                $cetak = '-';
            }

            if ($row->status_pembayaran == "completed") {
                $status_pembayaran = "<span class='badge bg-success'>Lunas</span>";
            } elseif ($row->status_pembayaran == "pending") {
                $status_pembayaran = "<span class='badge bg-warning text-light'>Booking / Belum Lunas</span>";
            } elseif ($row->status_pembayaran == "failed") {
                $status_pembayaran = "<span class='badge bg-danger'>Dibatalkan</span>";
            }

            $aksi = '
                <div class="d-flex align-items-center justify-content-center">
                ' . $cetak . '
                </div>
            ';

            $output[] = [
                'nomor' => '<strong>' . $no++ . '</strong>',
                'tanggal_masuk' => Carbon::parse($row->tanggal_masuk)->format("Y-m-d H:i:s"),
                'tanggal_keluar' => Carbon::parse($row->tanggal_keluar)->format("Y-m-d H:i:s"),
                'nama_penyewa' => $row->namalengkap,
                'nomor_kamar' => $row->lokasis->nomor_kamar,
                'tipe_kamar' => $row->tipekamars->tipekamar,
                'mitra' => $row->mitras->mitra,
                'jenissewa' => $row->jenissewa,
                'jumlah_pembayaran' => $row->jumlah_pembayaran ? "RP. " . number_format($row->jumlah_pembayaran, '0', '.', '.') : "RP. 0",
                'diskon' => intval($row->diskon) . " %",
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
    public function detailpenyewa(Penyewa $penyewa)
    {
        if ($penyewa->status == 0) {
            abort(404);
        }

        if ($penyewa->transaksisewa_kamars) {
            $kamar = Lokasi::where('id', $penyewa->transaksisewa_kamars->lokasi_id)->first();
            if ($kamar->status == 1 || $kamar->status == 2) {
                $data = [
                    'judul' => 'Detail Penyewa',
                    'penyewa' => $penyewa,
                    'kamar' => $kamar,
                    // 'tenggatwaktu' => $tenggatwaktu
                ];

                return view('contents.dashboard.penyewa.penyewaankamar.detailpenyewa', $data);
            } else {
                abort(404);
            }
        } else {
            abort(404);
        }
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
}
