<?php

namespace App\Http\Controllers\Dashboard\Scan;

use App\Http\Controllers\Controller;
use App\Models\Jenisruangan;
use App\Models\Lokasi;
use App\Models\Penggunaanbaranginventaris;
use Illuminate\Http\Request;

class MainController extends Controller
{
    // penggunaan barang inventaris
    public function penggunaanbaranginventaris()
    {
        $data = [
            'judul' => 'Scan Penggunaan Barang Inventaris'
        ];

        return view('contents.dashboard.scan.penggunaanbaranginventaris.main', $data);
    }
    public function getscanpenggunaanbaranginventaris()
    {
        if (request()->ajax()) {
            $no_barcode = request()->input('no_barcode');

            if (Penggunaanbaranginventaris::where('no_barcode', $no_barcode)->exists()) {
                $penggunaanbaranginventaris = Penggunaanbaranginventaris::where('no_barcode', $no_barcode)->first();

                $asal = Lokasi::where('id', $penggunaanbaranginventaris->lokasi_id)->first();
                if ($asal->jenisruangan_id == 2) {
                    $lokasi = 'Kamar Nomor ' . $asal->nomor_kamar;
                } else {
                    $lokasi = Jenisruangan::where('id', $asal->jenisruangan_id)->first()->nama;
                }

                $dataHTML = '
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="universalModalLabel">Penggunaan Barang Inventaris</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center">
                        Berada di lokasi ' . $lokasi . '
                        </div>
                    </div>
                </div>
                ';

                $response = [
                    'status' => 200,
                    'message' => 'success',
                    'dataHTML' => $dataHTML
                ];
            } else {
                $response = [
                    'status' => 400,
                    'message' => 'error',
                ];
            }

            return response()->json($response);
        }
    }
    // lokasi
    public function lokasi()
    {
        $data = [
            'judul' => 'Scan Lokasi'
        ];

        return view('contents.dashboard.scan.lokasi.main', $data);
    }
    public function getscanlokasi()
    {
        if (request()->ajax()) {
            $no_barcode = request()->input('no_barcode');

            if (Lokasi::where('id', $no_barcode)->exists()) {
                $data = [];
                if (Penggunaanbaranginventaris::where('lokasi_id', (int)$no_barcode)->count() > 0) {
                    $no = 1;
                    foreach (Penggunaanbaranginventaris::where('lokasi_id', (int)$no_barcode)->get() as $row) {
                        $data[] = '
                        <tr>
                            <td>' . $no++ . '</td>
                            <td>' . $row->baranginventaris->nama . '</td>
                            <td>' . $row->baranginventaris->jumlah . '</td>
                        </tr>
                        ';
                    }
                } else {
                    $data[] = '
                        <tr>
                            <td colspan="3">Barang Kosong</td>
                        </tr>
                    ';
                }

                $dataHTML = '
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="universalModalLabel">Daftar Barang Inventaris</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-success text-center m-0">
                             <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Nama Barang</th>
                                    <th scope="col">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                ' . implode("", $data) . '
                            </tbody>
                        </table>
                    </div>
                </div>
                ';

                $response = [
                    'status' => 200,
                    'message' => 'success',
                    'dataHTML' => $dataHTML
                ];
            } else {
                $response = [
                    'status' => 400,
                    'message' => 'error',
                ];
            }

            return response()->json($response);
        }
    }
}
