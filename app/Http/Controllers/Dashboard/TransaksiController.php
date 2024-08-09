<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Tagih;
use App\Models\Transaksi;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    public function index()
    {
        $data = [
            'judul' => 'Transaksi Pembayaran',
        ];

        return view('contents.dashboard.transaksi', $data);
    }
    public function datatabletransaksi()
    {
        $minDate = request()->input('minDate');
        $maxDate = request()->input('maxDate');
        $jenis_transaksi = request()->input('jenis_transaksi');
        $tipe = request()->input('tipe');

        $transaksi = Transaksi::when($jenis_transaksi, function ($query) use ($jenis_transaksi) {
            foreach (Tagih::all() as $row) {
                if ($jenis_transaksi == $row->id) {
                    return $query->where('tagih_id', $row->id);
                }
            }
        })
            ->when($tipe, function ($query) use ($tipe) {
                if ($tipe == 1) {
                    return $query->where('tipe', 'pemasukan');
                } elseif ($tipe == 2) {
                    return $query->where('tipe', 'pengeluaran');
                }
            })
            ->when($minDate && $maxDate, function ($query) use ($minDate, $maxDate) {
                $query->whereDate('tanggal_transaksi', '>=', $minDate)
                    ->whereDate('tanggal_transaksi', '<=', $maxDate);
            })
            ->orderBy('tipe', 'ASC')
            ->orderby('tanggal_transaksi', 'DESC')->get();

        $output = [];
        $no = 1;
        foreach ($transaksi as $row) {
            $output[] = [
                'nomor' => '<strong>' . $no++ . '</strong>',
                'tanggal_transaksi' => Carbon::parse($row->tanggal_transaksi)->format("Y-m-d H:i:s"),
                'no_transaksi' => $row->no_transaksi,
                'tagihan' => $row->tagihan->tagih,
                'metode_pembayaran' => $row->metode_pembayaran,
                'tipe' => $row->tipe == "pemasukan" ? "Pemasukan" : "Pengeluaran",
                'jumlah_uang' => $row->jumlah_uang ? "RP. " . number_format($row->jumlah_uang, '0', '.', '.') : "RP. 0",
            ];
        }

        return response()->json([
            'data' => $output,
            'pemasukan' => $transaksi->where('tipe', 'pemasukan')->sum('jumlah_uang'),
            'pengeluaran' => $transaksi->where('tipe', 'pengeluaran')->sum('jumlah_uang'),
        ]);
    }
}
