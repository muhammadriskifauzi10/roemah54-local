<?php

namespace App\Http\Controllers\Dashboard\Penyewa\Daftarpenyewa;

use App\Http\Controllers\Controller;

class MainController extends Controller
{
      public function index()
      {
            $data = [
                  'judul' => 'Daftar Penyewa',
            ];

            return view('contents.dashboard.penyewa.daftarpenyewa.main', $data);
      }
      public function datatabledaftarpenyewa()
      {
            $daftarpenyewa = Penyewa::all();

            $output = [];
            $no = 1;
            foreach ($daftarpenyewa as $row) {
                  // status pembayaran
                  if ($row->status == 1) {
                        $status = "<span class='badge bg-success'>Sedang Menyewa</span>";
                  } else {
                        $status = "<span class='badge bg-danger'>-</span>";
                  }

                  $output[] = [
                        'nomor' => '<strong>' . $no++ . '</strong>',
                        'nama_lengkap' => $row->namalengkap,
                        'no_ktp' => $row->noktp,
                        'no_hp' => $row->nohp,
                        'alamat' => $row->alamat,
                        'status' => $status,
                  ];
            }

            return response()->json([
                  'data' => $output
            ]);
      }
}
