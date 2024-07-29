<?php

namespace App\Http\Controllers\Dashboard\Inventaris\Log;

use App\Http\Controllers\Controller;
use App\Models\Baranginventaris;
use App\Models\Jenisruangan;
use App\Models\Kategoribaranginventaris;
use App\Models\Logbaranginventaris;
use App\Models\Logmutasibaranginventaris;
use App\Models\Lokasi;
use Carbon\Carbon;

class MainController extends Controller
{
    public function index()
    {
        $barang = Baranginventaris::orderBy('nama', 'ASC')->get();
        $kategori = Kategoribaranginventaris::all();

        $data = [
            'judul' => 'Log Barang Inventaris',
            'barang' => $barang,
            'kategori' => $kategori
        ];

        return view('contents.dashboard.inventaris.log.main', $data);
    }
    public function datatablelog()
    {
        $min = request()->input('minDate');
        $max = request()->input('maxDate');
        $barang = request()->input('barang');
        $kategori = request()->input('kategori');

        $barangExists = Baranginventaris::where('id', (int)$barang)->exists();
        $kategoriExists = Kategoribaranginventaris::where('id', (int)$kategori)->exists();

        $logbaranginventaris = Logbaranginventaris::when($barangExists, function ($query) use ($barang) {
            $query->where('baranginventaris_id', (int)$barang);
        })
            ->when($kategoriExists, function ($query) use ($kategori) {
                $query->where('kategoribaranginventaris_id', (int)$kategori);
            })
            ->when($min && $max, function ($query) use ($min, $max) {
                $query->whereDate('tanggal_log', '>=', $min)
                    ->whereDate('tanggal_log', '<=', $max);
            })
            ->orderBy('created_at', 'DESC')
            ->get();

        $output = [];
        $no = 1;
        foreach ($logbaranginventaris as $row) {
            // asal
            if ($row->asallokasi_id) {
                $asal = Lokasi::where('id', $row->asallokasi_id)->first();
                if ($asal->jenisruangan_id == 2) {
                    $lokasiasal = 'Kamar Nomor ' . $asal->nomor_kamar;
                } else {
                    $lokasiasal = Jenisruangan::where('id', $asal->jenisruangan_id)->first()->nama;
                }
            } else {
                $lokasiasal = "-";
            }

            // tujuan
            if ($row->tujuanlokasi_id) {
                $tujuan = Lokasi::where('id', $row->tujuanlokasi_id)->first();
                if ($tujuan->jenisruangan_id == 2) {
                    $lokasitujuan = 'Kamar Nomor ' . $tujuan->nomor_kamar;
                } else {
                    $lokasitujuan = Jenisruangan::where('id', $tujuan->jenisruangan_id)->first()->nama;
                }
            } else {
                $lokasitujuan = "-";
            }

            if ($row->log == 0) {
                $log = "<span class='badge bg-danger'>Hapus Barang</span>";
            } elseif ($row->log == 1) {
                $log = "<span class='badge bg-success'>Tambah Barang</span>";
            } elseif ($row->log == 2) {
                $log = "<span class='badge bg-warning'>Pembaruan Barang</span>";
            } elseif ($row->log == 4) {
                $log = "<span class='badge bg-success'>Penggunaan Barang</span>";
            } elseif ($row->log == 5) {
                $log = "<span class='badge bg-danger'>Mutasi Barang</span>";
            } elseif ($row->log == 6) {
                $log = "<span class='badge bg-warning'>Hapus Penggunaan Barang</span>";
            } elseif ($row->log == 7) {
                $log = "<span class='badge bg-info'>Perbaikan Barang</span>";
            }

            $output[] = [
                'nomor' => '<strong>' . $no++ . '</strong>',
                'tanggal_log' => Carbon::parse($row->tanggal_log)->format('Y-m-d H:i'),
                'no_barcode' => $row->no_barcode ? $row->no_barcode : '-',
                'tanggal_masuk' => Carbon::parse($row->tanggal_masuk)->format('Y-m-d H:i'),
                'kategori' => $row->kategoris->nama,
                'nama_barang' => $row->nama,
                'deskripsi' => $row->deskripsi ? $row->deskripsi : '-',
                'harga' => "RP. " . number_format($row->harga, '0', '.', '.'),
                'jumlah' => intval($row->jumlah) - intval($row->jumlah_terpakai),
                'total_harga' => "RP. " . number_format($row->harga * (intval($row->jumlah) - intval($row->jumlah_terpakai)), '0', '.', '.'),
                'satuan' => $row->satuan ? $row->satuan : '-',
                'asal' => $lokasiasal,
                'tujuan' => $lokasitujuan,
                'log' => $log,
            ];
        }

        return response()->json([
            'data' => $output
        ]);
    }
}
