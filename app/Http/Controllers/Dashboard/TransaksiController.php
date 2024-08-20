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
    // baru
    public function datatabletransaksi()
    {
        $minDate = request()->input('minDate');
        $maxDate = request()->input('maxDate');
        $jenis_transaksi = request()->input('jenis_transaksi');
        $tipe = request()->input('tipe');

        $transaksi = Transaksi::join('pembayarans as p', 'transaksis.pembayaran_id', '=', 'p.id')
            ->select(
                'transaksis.id',
                'transaksis.no_transaksi',
                'transaksis.tagih_id',
                'transaksis.tanggal_transaksi',
                'transaksis.jumlah_uang',
                'transaksis.metode_pembayaran',
                'transaksis.tipe',
                'p.tanggal_masuk',
                'p.tanggal_keluar',
                'p.penyewa_id',
                'p.lokasi_id',
                'p.tipekamar',
                'p.jenissewa',
            )
            ->when($jenis_transaksi, function ($query) use ($jenis_transaksi) {
                foreach (Tagih::all() as $row) {
                    if ($jenis_transaksi == $row->id) {
                        return $query->where('transaksis.tagih_id', $row->id);
                    }
                }
            })
            ->when($tipe, function ($query) use ($tipe) {
                if ($tipe == 1) {
                    return $query->where('transaksis.tipe', 'pemasukan');
                } elseif ($tipe == 2) {
                    return $query->where('transaksis.tipe', 'pengeluaran');
                }
            })
            ->when($minDate && $maxDate, function ($query) use ($minDate, $maxDate) {
                $query->whereDate('transaksis.tanggal_transaksi', '>=', $minDate)
                    ->whereDate('transaksis.tanggal_transaksi', '<=', $maxDate);
            })
            ->orderBy('transaksis.tipe', 'ASC')
            ->orderby('transaksis.tanggal_transaksi', 'DESC')->get();

        $output = [];
        $no = 1;
        foreach ($transaksi as $row) {
            $output[] = [
                'nomor' => '<strong>' . $no++ . '</strong>',
                'tanggal_transaksi' => Carbon::parse($row->tanggal_transaksi)->format("Y-m-d H:i:s"),
                'no_transaksi' => $row->no_transaksi,
                'tanggal_masuk' => Carbon::parse($row->tanggal_masuk)->format("Y-m-d H:i:s"),
                'tanggal_keluar' => Carbon::parse($row->tanggal_keluar)->format("Y-m-d H:i:s"),
                'nama_penyewa' => $row->penyewas->namalengkap,
                'nomor_kamar' => $row->lokasis->nomor_kamar,
                'tipe_kamar' => $row->tipekamar,
                'jenissewa' => $row->jenissewa,
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
