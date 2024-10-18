<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Lantai;
use App\Models\Lokasi;
use App\Models\Pembayaran;
use App\Models\Penyewa;
use App\Models\Transaksi;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function index()
    {
        // Mendapatkan tanggal awal dan akhir bulan sebelumnya
        $startOfPreviousMonth = Carbon::now()->subMonth()->startOfMonth()->toDateString();
        $endOfPreviousMonth = Carbon::now()->subMonth()->endOfMonth()->toDateString();

        // Menghitung total pendapatan bulan sebelumnya
        $totalPendapatanBulanSebelumnya = Transaksi::where('tipe', 'pemasukan')
            ->whereDate('tanggal_transaksi', '>=', $startOfPreviousMonth)
            ->whereDate('tanggal_transaksi', '<=', $endOfPreviousMonth)
            ->sum('jumlah_uang');

        // Mendapatkan tanggal awal dan akhir bulan ini
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

        // Menghitung total pendapatan bulan ini
        $totalPendapatanBulanIni = Transaksi::where('tipe', 'pemasukan')
            ->whereDate('tanggal_transaksi', '>=', $startOfMonth)
            ->whereDate('tanggal_transaksi', '<=', $endOfMonth)
            ->sum('jumlah_uang');


        $lantai = Lantai::join('lokasis', 'lantais.id', '=', 'lokasis.lantai_id')
            ->select(
                'lantais.id',
                'lantais.namalantai',
            )
            ->distinct()
            ->where('lokasis.jenisruangan_id', 2)
            ->orderBy('lantais.id', 'ASC')
            ->get();

        $data = [
            'judul' => 'Dasbor',
            'lantai' => $lantai,
            'pendapatandibulansebelumnya' => $totalPendapatanBulanSebelumnya,
            'pendapatandibulanini' => $totalPendapatanBulanIni,
        ];

        return view('contents.dashboard.main', $data);
    }
    public function datatablepenyewaankamar()
    {
        $penyewaankamar = Pembayaran::whereDate('created_at', Carbon::today())
            ->where('status', 1)
            ->latest()
            ->get();

        $output = [];
        $no = 1;
        foreach ($penyewaankamar as $row) {
            // status pembayaran
            if ($row->status_pembayaran == "completed") {
                $status_pembayaran = "<span class='badge bg-success'>Lunas</span>";
            } elseif ($row->status_pembayaran == "pending") {
                $status_pembayaran = "<span class='badge bg-warning text-light'>Belum Lunas</span>";
            } elseif ($row->status_pembayaran == "failed") {
                $status_pembayaran = "<span class='badge bg-danger'>Dibatalkan</span>";
            }

            // status
            if ($row->status == 1) {
                $status = "<span class='badge bg-success'>Sedang Menyewa</span>";
            } else {
                $status = "<span class='badge bg-danger'>-</span>";
            }

            $output[] = [
                'nomor' => "<strong>" . $no++ . "</strong>",
                'tanggal_masuk' => Carbon::parse($row->tanggal_masuk)->format("d-m-Y H:i"),
                'tanggal_keluar' => Carbon::parse($row->tanggal_keluar)->format("d-m-Y H:i"),
                'nama_penyewa' => $row->penyewas->namalengkap,
                'nomor_kamar' => $row->lokasis->nomor_kamar,
                'tipe_kamar' => $row->tipekamar,
                'mitra' => $row->mitras->mitra,
                'jenissewa' => $row->jenissewa,
                'status_pembayaran' => $status_pembayaran,
                'status' => $status,
            ];
        }

        return response()->json([
            'data' => $output,
            'total' => $penyewaankamar->count(),
        ]);
    }
    public function detaildata(Lantai $lantai)
    {
        $data = [
            'judul' => 'Detail Lantai',
            'lantai' => $lantai,
        ];
        return view('contents.dashboard.detaillantai', $data);
    }
    public function create()
    {
        if (request()->ajax()) {
            try {
                DB::beginTransaction();
                Lantai::create([
                    'namalantai' => "Lantai " . Lantai::all()->count() + 1,
                    'operator_id' => auth()->user()->id
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
        }
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
}
