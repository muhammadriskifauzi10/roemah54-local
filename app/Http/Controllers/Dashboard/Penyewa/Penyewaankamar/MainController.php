<?php

namespace App\Http\Controllers\Dashboard\Penyewa\Penyewaankamar;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
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

        $penyewaankamar = Pembayaran::when($minDate && $maxDate, function ($query) use ($minDate, $maxDate) {
            $query->whereDate('tanggal_masuk', '>=', $minDate)
                ->whereDate('tanggal_masuk', '<=', $maxDate);
        })
            ->where('tagih_id', 1)
            ->orderBy('tanggal_masuk', 'ASC')
            ->get();

        $output = [];
        $no = 1;
        foreach ($penyewaankamar as $row) {
            if ($row->tanggal_pembayaran) {
                if ($row->status_pembayaran == "completed" || $row->status_pembayaran == "pending") {
                    $cetakkwitansi = '<a href="' . route('penyewaankamar.cetakkwitansi', encrypt($row->id)) . '" class="btn btn-success" style="width: 140px;" target="_blank">Cetak Kwitansi</a>';
                } else {
                    $cetakkwitansi = "-";
                }
            } else {
                $cetakkwitansi = "-";
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
                ' . $cetakkwitansi . '
                </div>
            ';

            $output[] = [
                'nomor' => '<strong>' . $no++ . '</strong>',
                'tanggal_masuk' => Carbon::parse($row->tanggal_masuk)->format("Y-m-d H:i:s"),
                'tanggal_keluar' => Carbon::parse($row->tanggal_keluar)->format("Y-m-d H:i:s"),
                'nama_penyewa' => $row->penyewas->namalengkap,
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
