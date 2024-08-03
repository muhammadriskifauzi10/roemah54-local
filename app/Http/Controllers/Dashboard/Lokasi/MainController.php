<?php

namespace App\Http\Controllers\Dashboard\Lokasi;

use App\Http\Controllers\Controller;
use App\Models\Jenisruangan;
use App\Models\Lokasi;
use Barryvdh\DomPDF\Facade\Pdf;

class MainController extends Controller
{
    public function index()
    {
        $data = [
            'judul' => 'Daftar Lokasi'
        ];

        return view('contents.dashboard.lokasi.main', $data);
    }
    // Ajax Request
    public function datatablelokasi()
    {
        $lokasi = Lokasi::orderby('lantai_id', 'ASC')->orderby('jenisruangan_id', 'ASC')->get();

        $output = [];
        $no = 1;
        foreach ($lokasi as $row) {
            if ($row->jenisruangan_id == 2) {
                $lokasi = 'Nomor Kamar: ' . $row->nomor_kamar;
            } else {
                $lokasi = Jenisruangan::where('id', $row->jenisruangan_id)->first()->nama;
            }
            $output[] = [
                'nomor' => '<strong>' . $no++ . '</strong>',
                'lantai' => $row->lantais->namalantai,
                'lokasi' => $lokasi,
                'token_listrik' => $row->token_listrik ? $row->token_listrik : "-",
            ];
        }

        return response()->json([
            'data' => $output
        ]);
    }
    // cetak
    public function getmodalcetakqrcodelokasi()
    {
        if (request()->ajax()) {
            $optionlabel = [];
            foreach (Lokasi::orderby('lantai_id', 'ASC')->orderby('jenisruangan_id', 'ASC')->get() as $row) {
                if ($row->jenisruangan_id == 2) {
                    $lokasi = 'Nomor Kamar: ' . $row->nomor_kamar;
                } else {
                    $lokasi = Jenisruangan::where('id', $row->jenisruangan_id)->first()->nama;
                }

                $optionlabel[] = ' <option value="' . $row->id . '">' . $lokasi . '</option>';
            }

            $dataHTML = '
                 <form class="modal-content" action="' . route('lokasi.cetakqrcode') . '" autocomplete="off" target="__blank">
                     <div class="modal-header">
                         <h1 class="modal-title fs-5" id="universalModalLabel">Cetak QR Code</h1>
                         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                     </div>
                     <div class="modal-body">
                         <div>
                             <label for="lokasi" class="form-label fw-bold">Pilih Lokasi</label>
                             <select class="form-select form-select-2"
                                 name="lokasi[]" id="lokasi" multiple="multiple" style="width: 100%;">
                                 ' . implode(" ", $optionlabel) . '
                             </select>
                         </div>
                     </div>
                     <div class="modal-footer">
                         <button type="submit" class="btn btn-primary w-100" id="btnRequest">
                             <i class="fa fa-barcode me-1"></i>
                             Print
                         </button>
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
    public function cetakqrcode()
    {
        $datainput = request()->input('lokasi');

        if (!$datainput) {
            return redirect()->route('kamar')->with('messageFailed', 'Qr Code wajib dipilih');
        }

        $lokasi = Lokasi::orderby('lantai_id', 'ASC')->orderby('jenisruangan_id', 'ASC')->wherein('id', $datainput)->get();

        $data = [
            'judul' => 'Cetak QR Code',
            'lokasi' => $lokasi
        ];

        $width = 80 * 2.83465; // 80 mm to points
        $height = 25 * 2.83465; // 30 mm to points

        // Generate PDF
        $pdf = Pdf::loadView('contents.dashboard.lokasi.cetak.qrcode', $data)
            ->setPaper([0, 0, $width, $height]);

        return $pdf->stream('cetakqrcode.pdf');
    }
}
