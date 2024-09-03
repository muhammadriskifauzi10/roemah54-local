<?php

namespace App\Http\Controllers\Dashboard\Bisnis\Ritel;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Penyewa;
use App\Models\Ritel;
use App\Models\Tagih;
use App\Models\Transaksi;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MainController extends Controller
{
    public function index()
    {
        $jenisritel = Tagih::select(
            'id',
            'tagih AS jenis_ritel'
        )->whereNotIn('id', [1, 2])->get();


        $data = [
            'judul' => 'Ritel',
            'jenisritel' => $jenisritel
        ];

        return view('contents.dashboard.bisnis.ritel.main', $data);
    }
    public function datatableritel()
    {
        $minDate = request()->input('minDate');
        $maxDate = request()->input('maxDate');
        $jenis_ritel = request()->input('jenis_ritel');

        $ritel = Ritel::when($jenis_ritel, function ($query) use ($jenis_ritel) {
            foreach (Tagih::all() as $row) {
                if ($jenis_ritel == $row->id) {
                    return $query->where('jenis_ritel', $row->id);
                }
            }
        })
            ->when($minDate && $maxDate, function ($query) use ($minDate, $maxDate) {
                $query->whereDate('tanggal_ritel', '>=', $minDate)
                    ->whereDate('tanggal_ritel', '<=', $maxDate);
            })
            ->orderby('tanggal_ritel', 'DESC')->get();

        $output = [];
        $no = 1;
        foreach ($ritel as $row) {
            $output[] = [
                'nomor' => '<strong>' . $no++ . '</strong>',
                'tanggal_ritel' => Carbon::parse($row->tanggal_ritel)->format("Y-m-d H:i:s"),
                'penyewa' => $row->penyewas->namalengkap,
                'kamar' => $row->lokasis->nomor_kamar,
                'jenis_ritel' => $row->tagihs->tagih,
                'kiloan' => intval($row->kiloan) . " KG",
                'jumlah_pembayaran' => $row->jumlah_pembayaran ? "RP. " . number_format($row->jumlah_pembayaran, '0', '.', '.') : "RP. 0",
                'keterangan' => $row->keterangan
            ];
        }

        return response()->json([
            'data' => $output
        ]);
    }
    public function tambahritel()
    {
        $penyewa = Penyewa::where('status', 1)->get();
        $jenisritel = Tagih::select(
            'id',
            'tagih AS jenis_ritel'
        )->whereNotIn('id', [1, 2, 3])->get();

        $data = [
            'judul' => 'Tambah ritel',
            'penyewa' => $penyewa,
            'jenisritel' => $jenisritel
        ];

        return view('contents.dashboard.bisnis.ritel.tambah', $data);
    }
    public function create()
    {
        $validator = Validator::make(request()->all(), [
            'penyewa' => [
                function ($attribute, $value, $fail) {
                    if (!Penyewa::where('id', (int)$value)->exists()) {
                        $fail('Kolom ini wajib dipilih');
                    }
                },
            ],
            'jenis_ritel' => [
                function ($attribute, $value, $fail) {
                    if (!Tagih::where('id', (int)$value)->exists()) {
                        $fail('Kolom ini wajib dipilih');
                    }
                },
            ],
            'kiloan' => 'nullable|integer|min:1',
            'jumlah_pembayaran' => 'required|not_in:0',
            'keterangan' => 'required',
        ], [
            'kiloan.min' => 'Kolom ini wajib diisi',
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
            $jenis_ritel = htmlspecialchars(request()->input('jenis_ritel'), true);

            $model_pembayaran = Pembayaran::where('penyewa_id', $penyewa)->latest()->first();

            $kiloan = htmlspecialchars(request()->input('kiloan'), true);
            $jumlah_pembayaran = htmlspecialchars(request()->input('jumlah_pembayaran'), true);
            $keterangan = htmlspecialchars(request()->input('keterangan'), true);
            $metode_pembayaran = htmlspecialchars(request()->input('metode_pembayaran'), true);

            $jumlah_pembayaran = str_replace(".", "", $jumlah_pembayaran);

            if ($jenis_ritel == "4") {
                $kiloan = $kiloan ? $kiloan : NULL;
            } else {
                $kiloan = $kiloan ? $kiloan : NULL;
            }

            // pembayaran
            $model_post_pembayaran = new Pembayaran();
            $model_post_pembayaran->tagih_id = $jenis_ritel;
            $model_post_pembayaran->tanggal_pembayaran = date('Y-m-d H:i:s');
            $model_post_pembayaran->penyewa_id = $model_pembayaran->penyewa_id;
            $model_post_pembayaran->lokasi_id = $model_pembayaran->lokasi_id;
            $model_post_pembayaran->jumlah_pembayaran = intval($jumlah_pembayaran);
            $model_post_pembayaran->total_bayar = intval($jumlah_pembayaran);
            $model_post_pembayaran->status_pembayaran = 'completed';
            $model_post_pembayaran->operator_id = auth()->user()->id;
            $model_post_pembayaran->save();

            // ritel
            $model_post_ritel = new Ritel();
            $model_post_ritel->tanggal_ritel = date('Y-m-d H:i:s');
            $model_post_ritel->penyewa_id = $model_pembayaran->penyewa_id;
            $model_post_ritel->lokasi_id = $model_pembayaran->lokasi_id;
            $model_post_ritel->jenis_ritel = $jenis_ritel;
            $model_post_ritel->kiloan = $kiloan;
            $model_post_ritel->jumlah_pembayaran = intval($jumlah_pembayaran);
            $model_post_ritel->keterangan = $keterangan;
            $model_post_ritel->operator_id = auth()->user()->id;
            $post = $model_post_ritel->save();

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

                // transaksi
                $model_post_transaksi = new Transaksi();
                $model_post_transaksi->no_transaksi = $no_transaksi;
                $model_post_transaksi->tagih_id = $jenis_ritel;
                $model_post_transaksi->pembayaran_id = $model_post_pembayaran->id;
                $model_post_transaksi->tanggal_transaksi = date('Y-m-d H:i:s');
                $model_post_transaksi->jumlah_uang = intval($jumlah_pembayaran);
                $model_post_transaksi->metode_pembayaran = $metode_pembayaran;
                $model_post_transaksi->tipe = "pemasukan";
                $model_post_transaksi->operator_id = auth()->user()->id;
                $model_post_transaksi->save();
            }

            DB::commit();
            return redirect()->route('ritel')->with('messageSuccess', 'Ritel berhasil ditambahkan!');
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            // return redirect()->back()->with('messageFailed', 'Opps, terjadi kesalahan!');
        }
    }
}
