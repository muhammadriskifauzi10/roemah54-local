<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Laundri;
use App\Models\Pembayaran;
use App\Models\Penyewa;
use App\Models\Tokenlistrik;
use App\Models\Transaksi;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LaundriController extends Controller
{
    public function index()
    {
        $data = [
            'judul' => 'Laundri',
        ];

        return view('contents.dashboard.laundri', $data);
    }
    public function datatablelaundri()
    {
        $minDate = request()->input('minDate');

        $laundri = Laundri::when($minDate, function ($query) use ($minDate) {
            return $query->whereDate('tanggal_laundri', '=', $minDate);
        })
            ->orderby('tanggal_laundri', 'DESC')->get();

        $output = [];
        $no = 1;
        foreach ($laundri as $row) {
            $output[] = [
                'nomor' => '<strong>' . $no++ . '</strong>',
                'tanggal_laundri' => Carbon::parse($row->tanggal_laundri)->format("Y-m-d H:i:s"),
                'penyewa' => $row->penyewas->namalengkap,
                'kamar' => $row->kamars->nomor_kamar,
                'kiloan' => $row->kiloan . " KG",
                'jumlah_pembayaran' => $row->jumlah_pembayaran ? "RP. " . number_format($row->jumlah_pembayaran, '0', '.', '.') : "RP. 0",
                'keterangan' => $row->keterangan
            ];
        }

        return response()->json([
            'data' => $output
        ]);
    }
    public function tambahlaundri()
    {
        $penyewa = Penyewa::select('penyewas.namalengkap', 'pb.penyewa_id', 'pb.kamar_id', DB::raw('MAX(pb.tanggal_pembayaran) as max_tanggal_pembayaran'), 'k.nomor_kamar')
            ->join('pembayarans AS pb', 'penyewas.id', '=', 'pb.penyewa_id')
            ->join('kamars AS k', 'pb.kamar_id', '=', 'k.id')
            ->where('k.status', 1)
            ->groupBy('penyewas.namalengkap', 'pb.penyewa_id', 'pb.kamar_id', 'k.nomor_kamar') // Sesuaikan GROUP BY dengan kolom non-agregat yang Anda pilih
            ->orderBy('max_tanggal_pembayaran', 'DESC')
            ->get();

        $data = [
            'judul' => 'Tambah Laundri',
            'penyewa' => $penyewa
        ];

        return view('contents.dashboard.tambahlaundri', $data);
    }
    public function posttambahlaundri()
    {
        $validator = Validator::make(request()->all(), [
            'penyewa' => [
                function ($attribute, $value, $fail) {
                    if (!Penyewa::where('id', (int)$value)->exists()) {
                        $fail('Kolom ini wajib dipilih');
                    }
                },
            ],
            'kiloan' => 'required|not_in:0',
            'jumlah_pembayaran' => 'required|not_in:0',
            'keterangan' => 'required',
        ], [
            'kiloan.required' => 'Kolom ini wajib diisi',
            'kiloan.not_in' => 'Kolom ini wajib diisi',
            'jumlah_pembayaran.required' => 'Kolom ini wajib diisi',
            'jumlah_pembayaran.not_in' => 'Kolom ini wajib diisi',
            'keterangan.required' => 'Kolom ini wajib diisi',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            $penyewa = htmlspecialchars(request()->input('penyewa'), true);

            $model_j = Penyewa::select('penyewas.namalengkap', 'pb.penyewa_id', 'pb.kamar_id', DB::raw('MAX(pb.tanggal_pembayaran) as max_tanggal_pembayaran'), 'k.nomor_kamar')
                ->join('pembayarans AS pb', 'penyewas.id', '=', 'pb.penyewa_id')
                ->join('kamars AS k', 'pb.kamar_id', '=', 'k.id')
                ->where('k.status', 1)
                ->where('pb.penyewa_id', $penyewa)
                ->groupBy('penyewas.namalengkap', 'pb.penyewa_id', 'pb.kamar_id', 'k.nomor_kamar') // Sesuaikan GROUP BY dengan kolom non-agregat yang Anda pilih
                ->orderBy('max_tanggal_pembayaran', 'DESC')
                ->first();

            $kiloan = htmlspecialchars(request()->input('kiloan'), true);
            $jumlah_pembayaran = htmlspecialchars(request()->input('jumlah_pembayaran'), true);
            $keterangan = htmlspecialchars(request()->input('keterangan'), true);
            $metode_pembayaran = htmlspecialchars(request()->input('metode_pembayaran'), true);

            $model_post_pembayaran = new Pembayaran();
            $model_post_pembayaran->tagih_id = 3;
            $model_post_pembayaran->tanggal_pembayaran = date('Y-m-d H:i:s');
            $model_post_pembayaran->penyewa_id = $model_j->penyewa_id;
            $model_post_pembayaran->kamar_id = $model_j->kamar_id;
            $model_post_pembayaran->jumlah_pembayaran = intval($jumlah_pembayaran);
            $model_post_pembayaran->total_bayar = intval($jumlah_pembayaran);
            $model_post_pembayaran->status_pembayaran = 'completed';
            $model_post_pembayaran->operator_id = auth()->user()->id;
            $model_post_pembayaran->save();

            $model_post_laundri = new Laundri();
            $model_post_laundri->tanggal_laundri = date('Y-m-d H:i:s');
            $model_post_laundri->penyewa_id = $model_j->penyewa_id;
            $model_post_laundri->kamar_id = $model_j->kamar_id;
            $model_post_laundri->kiloan = $kiloan;
            $model_post_laundri->jumlah_pembayaran = intval($jumlah_pembayaran);
            $model_post_laundri->keterangan = $keterangan;
            $model_post_laundri->operator_id = auth()->user()->id;
            $post = $model_post_laundri->save();

            if ($post) {
                // Generate no transaksi
                $tahun = date('Y');
                $bulan = date('m');
                $tanggal = date('d');
                $infoterakhir = Transaksi::orderBy('created_at', 'DESC')->first();

                if ($infoterakhir) {
                    $tahunterakhir = Carbon::parse($infoterakhir->created_at)->format('Y') ?? 0;
                    $bulanterakhir = Carbon::parse($infoterakhir->created_at)->format('m') ?? 0;
                    $tanggalterakhir = Carbon::parse($infoterakhir->created_at)->format('d') ?? 0;
                    $nomor = substr($infoterakhir->no_transaksi, 6);

                    if ($tahun != $tahunterakhir || $bulan != $bulanterakhir || $tanggal != $tanggalterakhir) {
                        $nomor = 0;
                    }
                } else {
                    $nomor = 0;
                }

                // yymmddxxxxxx
                $no_transaksi = sprintf('%02d%02d%02d%06d', date('y'), $bulan, $tanggal, $nomor + 1);

                $jumlah_pembayaran = str_replace(".", "", $jumlah_pembayaran);

                $model_post_transaksi = new Transaksi();
                $model_post_transaksi->no_transaksi = $no_transaksi;
                $model_post_transaksi->tagih_id = 3;
                $model_post_transaksi->pembayaran_id = $model_post_pembayaran->id;
                $model_post_transaksi->tanggal_transaksi = date('Y-m-d H:i:s');
                $model_post_transaksi->jumlah_uang = $jumlah_pembayaran;
                $model_post_transaksi->metode_pembayaran = $metode_pembayaran;
                $model_post_transaksi->tipe = "pemasukan";
                $model_post_transaksi->operator_id = auth()->user()->id;
                $model_post_transaksi->save();
            }

            DB::commit();
            return redirect('/dasbor')->with('messageSuccess', 'Laundri berhasil ditambahkan!');
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            // return redirect()->back()->with('messageFailed', 'Opps, terjadi kesalahan!');
        }
    }
}
