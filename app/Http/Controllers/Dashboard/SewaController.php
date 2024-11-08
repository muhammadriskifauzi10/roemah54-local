<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Mail\PotonganhargaEmail;
use App\Models\Booking;
use Illuminate\Support\Facades\Mail;
use App\Models\Harga;
use App\Models\Lokasi;
use App\Models\Lantai;
use App\Models\Mitra;
use App\Models\Pembayaran;
use App\Models\Penyewa;
use App\Models\Potonganharga;
use App\Models\Tipekamar;
use App\Models\Tokenlistrik;
use App\Models\Transaksi;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;

class SewaController extends Controller
{
    public function index()
    {
        $lantai = DB::table('lantais')
            ->join('lokasis', 'lantais.id', '=', 'lokasis.lantai_id')
            ->join('hargas', 'lokasis.tipekamar_id', '=', 'hargas.tipekamar_id')
            ->select(
                'lantais.id',
                'lantais.namalantai'
            )
            ->distinct()
            ->where('lokasis.jenisruangan_id', 2)
            ->where('lokasis.status', 0)
            ->orderBy('lantais.id', 'ASC')
            ->get();

        $tipekamar = Tipekamar::all();
        $data = [
            'judul' => 'Sewa',
            'lantai' => $lantai,
            'tipekamar' => $tipekamar
        ];

        return view('contents.dashboard.sewa', $data);
    }
    public function create()
    {
        $booking = htmlspecialchars(request()->input('booking'), true);

        $noktp = htmlspecialchars(request()->input('noktp'), true);
        if (!Penyewa::where('noktp', $noktp)->where('jenis_penyewa', 'Umum')->where('status', '<>', 2)->exists()) {
            $rulefotoktp = 'required|mimes:jpg,jpeg,png';
        } else {
            $rulefotoktp = 'mimes:jpg,jpeg,png';
        }

        if ($booking == "Y") {
            $ruledari_tanggal = 'required|date';
            $rulesampai_tanggal = 'required|date';

            $rulenoktp = 'nullable|numeric|digits:16';
            $rulealamat = '';
            $rulejeniskelamin = '';
            $rulefotoktp = 'mimes:jpg,jpeg,png';
            $ruletanggalmmasuk = 'date';

            $status_penyewa = 2;
        } else {
            $ruledari_tanggal = '';
            $rulesampai_tanggal = '';

            $rulenoktp = 'required|numeric|digits:16';
            $rulealamat = 'required';
            $rulejeniskelamin = [
                function ($attribute, $value, $fail) {
                    if ($value == "Pilih Jenis Kelamin") {
                        $fail('Kolom ini wajib dipilih');
                    }
                },
            ];
            $ruletanggalmmasuk = 'required|date';

            $status_penyewa = 1;
        }

        $validator = Validator::make(request()->all(), [
            'dari_tanggal' => $ruledari_tanggal,
            'sampai_tanggal' => $rulesampai_tanggal,
            'tanggalmasuk' => $ruletanggalmmasuk,
            'jumlahhari' => 'nullable|numeric',
            'namalengkap' => 'required',
            'noktp' => $rulenoktp,
            'nohp' => 'required|regex:/^08[0-9]{8,}$/',
            'lantai' => [
                function ($attribute, $value, $fail) {
                    if (!Lantai::where('id', (int)$value)->exists()) {
                        $fail('Kolom ini wajib dipilih');
                    }
                },
            ],
            'kamar' => [
                function ($attribute, $value, $fail) {
                    if (!Lokasi::where('id', (int)$value)->exists()) {
                        $fail('Kolom ini wajib dipilih');
                    }
                },
            ],
            'jenissewa' => [
                function ($attribute, $value, $fail) {
                    if ($value == "Pilih Jenis Sewa") {
                        $fail('Kolom ini wajib dipilih');
                    }
                },
            ],
            'mitra' => [
                function ($attribute, $value, $fail) {
                    if (!Mitra::where('id', (int)$value)->exists()) {
                        $fail('Kolom ini wajib dipilih');
                    }
                },
            ],
            'fotoktp' => $rulefotoktp,
            'total_bayar' => 'nullable',
            'diskon' => 'nullable|numeric|min:0|max:100',
            'alamat' => $rulealamat,
            'jenis_kelamin' => $rulejeniskelamin,
            'bukti_pembayaran' => 'mimes:jpg,jpeg,png',
            // 'tipe_pembayaran' => 'required',
        ], [
            'dari_tanggal.required' => 'Kolom ini wajib diisi',
            'sampai_tanggal.required' => 'Kolom ini wajib diisi',
            'tanggalmasuk.required' => 'Kolom ini wajib diisi',
            'tanggalmasuk.date' => 'Kolom ini wajib diisi',
            'namalengkap.required' => 'Kolom ini wajib diisi',
            'noktp.required' => 'No KTP wajib diisi',
            'noktp.numeric' => 'No KTP tidak valid',
            'noktp.digits' => 'No KTP tidak valid',
            'nohp.required' => 'Kolom ini wajib diisi',
            'nohp.regex' => 'No HP tidak valid',
            'fotoktp.required' => 'Kolom ini wajib diisi',
            'fotoktp.mimes' => 'Ekstensi file hanya mendukung format jpg dan jpeg',
            'total_bayar.required' => 'Kolom ini wajib diisi',
            'total_bayar.not_in' => 'Kolom ini wajib diisi',
            'alamat.required' => 'Kolom ini wajib diisi',
            'bukti_pembayaran.mimes' => 'Ekstensi file hanya mendukung format jpg dan jpeg',
            // 'tipe_pembayaran.required' => 'Kolom ini tidak valid',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $jumlahhari = htmlspecialchars(request()->input('jumlahhari'), true);
            $tanggalmasuk = htmlspecialchars(request()->input('tanggalmasuk'), true);
            $tanggalmasuk_format = Carbon::parse($tanggalmasuk)->format('Y-m-d H:i');
            $namalengkap = htmlspecialchars(request()->input('namalengkap'), true);
            $nohp = htmlspecialchars(request()->input('nohp'), true);
            $kamar = htmlspecialchars(request()->input('kamar'), true);
            $jenissewa = request()->input('jenissewa');
            $mitra = request()->input('mitra');
            $jenis_kelamin = htmlspecialchars(request()->input('jenis_kelamin'), true);
            $alamat = htmlspecialchars(request()->input('alamat'), true);
            $fotoktp = request()->file('fotoktp');
            $model_kamar = Lokasi::where('id', $kamar)->first();
            $model_harga = Harga::where('tipekamar_id', (int)$model_kamar->tipekamar_id)->where('mitra_id', (int)$mitra)->first();

            // metode pembayaran
            $total_bayar = htmlspecialchars(request()->input('total_bayar'), true);
            $metode_pembayaran = htmlspecialchars(request()->input('metode_pembayaran'), true);
            $bukti_pembayaran = request()->file('bukti_pembayaran');
            request()->merge([
                'total_bayar' => str_replace('.', '', request()->input('total_bayar')),
            ]);

            if (intval($total_bayar) > 0) {
                if ($bukti_pembayaran == NULL) {
                    if ($metode_pembayaran == "None") {
                        return redirect()->back()->with('messageFailed', 'File bukti pembayaran dan metode pembayaran wajib ditentukan');
                    } elseif ($metode_pembayaran != "None") {
                        return redirect()->back()->with('messageFailed', 'File bukti pembayaran wajib ditentukan');
                    }
                }

                if ($metode_pembayaran == "None") {
                    return redirect()->back()->with('messageFailed', 'Metode pembayaran wajib ditentukan');
                }
            } else {
                if ($bukti_pembayaran != NULL) {
                    if ($metode_pembayaran == "None") {
                        return redirect()->back()->with('messageFailed', 'Pembayaran wajib diisi dan metode pembayaran wajib ditentukan');
                    } elseif ($metode_pembayaran != "None") {
                        return redirect()->back()->with('messageFailed', 'Pembayaran wajib diisi');
                    }
                }

                if ($metode_pembayaran != "None") {
                    return redirect()->back()->with('messageFailed', 'Pembayaran wajib diisi dan file bukti pembayaran wajib ditentukan');
                }
            }

            if ($total_bayar) {
                $total_bayar = str_replace(".", "", $total_bayar);
            } else {
                $total_bayar = 0;
            }

            if ($jenis_kelamin == "Pilih Jenis Kelamin") {
                $jenis_kelamin = NULL;
            }

            if (empty($noktp)) {
                $penyewa = Penyewa::create([
                    'namalengkap' => $namalengkap,
                    'noktp' => $noktp,
                    'nohp' => $nohp,
                    'jenis_kelamin' => $jenis_kelamin,
                    'alamat' => $alamat,
                    'fotoktp' => "",
                    'status' => $status_penyewa,
                    'operator_id' => auth()->user()->id,
                ]);

                if (request()->file('fotoktp')) {
                    $fotoktp = "penyewa" . "-" . $penyewa->id . "." .  request()->file('fotoktp')->getClientOriginalExtension();
                    $file = request()->file('fotoktp');
                    $tujuan_upload = 'img/ktp/umum';
                    $file->move($tujuan_upload, $fotoktp);

                    Penyewa::where('id', $penyewa->id)->update([
                        'fotoktp' => $fotoktp,
                        'updated_at' => date("Y-m-d H:i:s"),
                    ]);
                }
            } else {
                if (!Penyewa::where('noktp', $noktp)->where('jenis_penyewa', 'Umum')->exists()) {
                    $penyewa = Penyewa::create([
                        'namalengkap' => $namalengkap,
                        'noktp' => $noktp,
                        'nohp' => $nohp,
                        'jenis_kelamin' => $jenis_kelamin,
                        'alamat' => $alamat,
                        'fotoktp' => "",
                        'status' => $status_penyewa,
                        'operator_id' => auth()->user()->id,
                    ]);

                    if (request()->file('fotoktp')) {
                        $fotoktp = "penyewa" . "-" . $penyewa->id . "." .  request()->file('fotoktp')->getClientOriginalExtension();
                        $file = request()->file('fotoktp');
                        $tujuan_upload = 'img/ktp/umum';
                        $file->move($tujuan_upload, $fotoktp);

                        Penyewa::where('id', $penyewa->id)->update([
                            'fotoktp' => $fotoktp,
                            'updated_at' => date("Y-m-d H:i:s"),
                        ]);
                    }
                } else {
                    $penyewa = Penyewa::where('noktp', $noktp)->where('jenis_penyewa', 'Umum')->first();

                    if (request()->file('fotoktp')) {
                        // Hapus file KTP lama jika ada
                        if (file_exists('img/ktp/umum/' . $penyewa->fotoktp)) {
                            unlink('img/ktp/umum/' . $penyewa->fotoktp);
                        }

                        $fotoktp = "penyewa" . "-" . $penyewa->id . "." . request()->file('fotoktp')->getClientOriginalExtension();
                        $file = request()->file('fotoktp');
                        $tujuan_upload = 'img/ktp/umum';
                        $file->move($tujuan_upload, $fotoktp);
                    } else {
                        $fotoktp = $penyewa->fotoktp;
                    }

                    Penyewa::where('id', $penyewa->id)->update([
                        'namalengkap' => $namalengkap,
                        'noktp' => $noktp,
                        'nohp' => $nohp,
                        'jenis_kelamin' => $jenis_kelamin,
                        'alamat' => $alamat,
                        'fotoktp' => $fotoktp,
                        'status' => $status_penyewa,
                        'operator_id' => auth()->user()->id,
                        'updated_at' => date("Y-m-d H:i:s"),
                    ]);
                }
            }

            if (stripos($jenissewa, 'Harian') !== false) {
                $tenggatwaktu = $this->check_out($tanggalmasuk_format);

                if ($model_harga->mitra_id == 1) {
                    $diskon = 0;
                    $potongan_harga = 0;
                    $jumlah_pembayaran = $model_harga->harian;
                } elseif ($model_harga->mitra_id == 2) {
                    $diskon = 15;
                    $potongan_harga = $model_harga->harian * ($diskon / 100);
                    $jumlah_pembayaran = $model_harga->harian - $potongan_harga;
                }

                if ($jumlahhari || $jumlahhari > 0) {
                    $new_tanggalmasuk = Carbon::parse($tanggalmasuk_format)->addDay($jumlahhari - 1);
                    $tenggatwaktu = $this->check_out($new_tanggalmasuk);

                    $potongan_harga = $model_harga->harian * intval($jumlahhari) * ($diskon / 100);
                    $jumlah_pembayaran = intval($jumlah_pembayaran) * intval($jumlahhari);
                }
            } elseif (stripos($jenissewa, 'Mingguan / 7 Hari') !== false) {
                $tenggatwaktu = Carbon::parse($this->check_out($tanggalmasuk_format))->addWeek();

                if ($model_harga->mitra_id == 1) {
                    $diskon = 0;
                    $potongan_harga = 0;
                    $jumlah_pembayaran = $model_harga->mingguan;
                } elseif ($model_harga->mitra_id == 2) {
                    $diskon = 15;
                    $potongan_harga = $model_harga->mingguan * ($diskon / 100);
                    $jumlah_pembayaran = $model_harga->mingguan - $potongan_harga;
                }
            } elseif (stripos($jenissewa, 'Mingguan / (14 Hari)') !== false) {
                $tenggatwaktu = Carbon::parse($this->check_out($tanggalmasuk_format))->addWeeks(2);

                if ($model_harga->mitra_id == 1) {
                    $diskon = 0;
                    $potongan_harga = 0;
                    $jumlah_pembayaran = $model_harga->hari14;
                } elseif ($model_harga->mitra_id == 2) {
                    $diskon = 15;
                    $potongan_harga = $model_harga->hari14 * ($diskon / 100);
                    $jumlah_pembayaran = $model_harga->hari14 - $potongan_harga;
                }
            } elseif (stripos($jenissewa, 'Bulanan') !== false) {
                // Bulanan (Monthly)
                $tenggatwaktu = Carbon::parse($this->check_out($tanggalmasuk_format))->addMonth();

                if ($model_harga->mitra_id == 1) {
                    $diskon = 0;
                    $potongan_harga = 0;
                    $jumlah_pembayaran = $model_harga->bulanan;
                } elseif ($model_harga->mitra_id == 2) {
                    $diskon = 15;
                    $potongan_harga = $model_harga->bulanan * ($diskon / 100);
                    $jumlah_pembayaran = $model_harga->bulanan - $potongan_harga;
                }
            }

            if (intval($total_bayar) >= intval($jumlah_pembayaran)) {
                $status_pembayaran = "completed";
                $status_kamar = 1;
            } else {
                $status_pembayaran = "pending";
                $status_kamar = 2;
            }

            if ($booking == "Y") {
                $dari_tanggal = htmlspecialchars(request()->input('dari_tanggal'), true);
                $sampai_tanggal = htmlspecialchars(request()->input('sampai_tanggal'), true);

                $daritanggal_format = Carbon::parse($dari_tanggal)->format('Y-m-d H:i');
                $sampaitanggal_format = Carbon::parse($sampai_tanggal)->format('Y-m-d H:i');
                $catatan = htmlspecialchars(request()->input('catatan'), true);

                $pembayaran = new Pembayaran();
                $pembayaran->tagih_id = 1;
                if (intval($total_bayar) > 0) {
                    $pembayaran->tanggal_pembayaran = date('Y-m-d H:i:s');
                }
                $pembayaran->penyewa_id = $penyewa->id;
                $pembayaran->lokasi_id = $kamar;
                $pembayaran->mitra_id = $mitra;
                $pembayaran->tipekamar_id = Tipekamar::where('id', $model_harga->tipekamar_id)->first()->id;
                $pembayaran->tipekamar = Tipekamar::where('id', $model_harga->tipekamar_id)->first()->tipekamar;
                $pembayaran->jenissewa = $jenissewa;
                $pembayaran->jumlah_pembayaran = intval($jumlah_pembayaran) + intval($potongan_harga);
                $pembayaran->diskon = $diskon;
                $pembayaran->potongan_harga = intval($potongan_harga);
                $pembayaran->total_bayar = $total_bayar;
                $pembayaran->kurang_bayar = intval($jumlah_pembayaran) - intval($total_bayar);
                $pembayaran->status_pembayaran = $status_pembayaran;
                $pembayaran->status = 2;
                $pembayaran->operator_id = auth()->user()->id;
                $post = $pembayaran->save();

                $model_booking = new Booking();
                $model_booking->pembayaran_id = $pembayaran->id;
                $model_booking->dari_tanggal = $daritanggal_format;
                $model_booking->sampai_tanggal = $sampaitanggal_format;
                $model_booking->lokasi_id = $kamar;
                $model_booking->catatan = $catatan;
                $model_booking->operator_id = auth()->user()->id;
                $model_booking->save();
            } else {
                $pembayaran = new Pembayaran();
                $pembayaran->tagih_id = 1;
                if (intval($total_bayar) > 0) {
                    $pembayaran->tanggal_pembayaran = date('Y-m-d H:i:s');
                }
                $pembayaran->tanggal_masuk = $tanggalmasuk_format;
                $pembayaran->tanggal_keluar = $tenggatwaktu;
                $pembayaran->penyewa_id = $penyewa->id;
                $pembayaran->lokasi_id = $kamar;
                $pembayaran->mitra_id = $mitra;
                $pembayaran->tipekamar_id = Tipekamar::where('id', $model_harga->tipekamar_id)->first()->id;
                $pembayaran->tipekamar = Tipekamar::where('id', $model_harga->tipekamar_id)->first()->tipekamar;
                $pembayaran->jenissewa = $jenissewa;
                $pembayaran->jumlah_pembayaran = intval($jumlah_pembayaran) + intval($potongan_harga);
                $pembayaran->diskon = $diskon;
                $pembayaran->potongan_harga = intval($potongan_harga);
                $pembayaran->total_bayar = $total_bayar;
                $pembayaran->kurang_bayar = intval($jumlah_pembayaran) - intval($total_bayar);
                $pembayaran->status_pembayaran = $status_pembayaran;
                $pembayaran->status = 1;
                $pembayaran->operator_id = auth()->user()->id;
                $post = $pembayaran->save();
            }

            if ($post) {
                if (intval($total_bayar) > 0) {
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

                    $transaksi = new Transaksi();
                    $transaksi->pembayaran_id = $pembayaran->id;
                    $transaksi->no_transaksi = $no_transaksi;
                    $transaksi->tagih_id = 1;
                    $transaksi->tanggal_transaksi = date('Y-m-d H:i:s');
                    $transaksi->jumlah_uang = $total_bayar;
                    $transaksi->metode_pembayaran = $metode_pembayaran;
                    $transaksi->operator_id = auth()->user()->id;
                    $posttransaksi = $transaksi->save();

                    if ($posttransaksi) {
                        if (request()->file('bukti_pembayaran')) {
                            $bukti_pembayaran = "bukti_pembayaran" . "-" . $transaksi->id . "." . request()->file('bukti_pembayaran')->getClientOriginalExtension();
                            $file = request()->file('bukti_pembayaran');
                            $tujuan_upload = 'img/bukti_pembayaran/pemasukan';
                            $file->move($tujuan_upload, $bukti_pembayaran);

                            Transaksi::where('id', $transaksi->id)->update([
                                'bukti_pembayaran' => $bukti_pembayaran
                            ]);
                        }
                    }
                }

                if ($booking == "Y") {
                    DB::commit();
                    return redirect()->route('dasbor')->with('messageSuccess', 'Kamar berhasil di booking');
                } else {
                    Lokasi::where('id', $kamar)->increment('jumlah_penyewa');
                    Lokasi::where('id', $kamar)->update([
                        'status' => $status_kamar,
                        'operator_id' => auth()->user()->id,
                        'updated_at' => date("Y-m-d H:i:s"),
                    ]);
                    DB::commit();
                    return redirect()->route('dasbor')->with('messageSuccess', 'Penyewaan kamar berhasil ditambahkan');
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }
    }
    // Ajax Request
    // Pembayaran
    public function batalkanpembayarankamar()
    {
        if (request()->ajax()) {
            $pembayaran_id = htmlspecialchars(request()->input('pembayaran_id'), ENT_QUOTES, 'UTF-8');
            if (Pembayaran::where('id', $pembayaran_id)->exists()) {
                try {
                    DB::beginTransaction();
                    $model_pembayaran = Pembayaran::where('id', $pembayaran_id)->first();

                    Pembayaran::where('id', $model_pembayaran->id)->update([
                        'status_pembayaran' => 'failed',
                        'status' => 0,
                        'operator_id' => auth()->user()->id,
                        'updated_at' => date("Y-m-d H:i:s"),
                    ]);

                    if (Pembayaran::where('penyewa_id', $model_pembayaran->penyewa_id)->where('status', 1)->count() < 1) {
                        Penyewa::where('id', $model_pembayaran->penyewa_id)->update([
                            'status' => 0,
                            'operator_id' => auth()->user()->id,
                            'updated_at' => date("Y-m-d H:i:s"),
                        ]);
                    }

                    Lokasi::where('id', $model_pembayaran->lokasi_id)->update([
                        'status' => 0,
                        'operator_id' => auth()->user()->id,
                        'updated_at' => date("Y-m-d H:i:s"),
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
            } else {
                $response = [
                    'status' => 404,
                    'message' => 'error',
                ];

                return response()->json($response);
            }
        }
    }
    // pembayaran kamar
    public function getmodalselesaikanpembayarankamar()
    {
        if (request()->ajax()) {
            $pembayaran_id = htmlspecialchars(request()->input('pembayaran_id'), ENT_QUOTES, 'UTF-8');
            if (Pembayaran::where('id', $pembayaran_id)->exists()) {
                $model_pembayaran = Pembayaran::where('id', $pembayaran_id)->first();

                if ($model_pembayaran->diskon != 0) {
                    $label = '
                        <tr>
                            <td class="text-left">Harga Kamar</td>
                            <td class="text-right" width="10">:</td>
                            <td class="text-right">RP. ' . number_format($model_pembayaran->jumlah_pembayaran, '0', '.', '.') . '</td>
                        </tr>
                        <tr>
                            <td class="text-left">Potongan Harga</td>
                            <td class="text-right" width="10">:</td>
                            <td class="text-right">RP. ' . number_format($model_pembayaran->potongan_harga, '0', '.', '.') . '</td>
                        </tr>
                        <tr>
                            <td class="text-left">Total Pembayaran</td>
                            <td class="text-right" width="10">:</td>
                            <td class="text-right">RP. ' . number_format($model_pembayaran->jumlah_pembayaran - $model_pembayaran->potongan_harga, '0', '.', '.') . '</td>
                        </tr>
                    ';
                } else {
                    if ($model_pembayaran->potongan_harga != 0) {
                        $label = '
                            <tr>
                                <td class="text-left">Harga Kamar</td>
                                <td class="text-right" width="10">:</td>
                                <td class="text-right">RP. ' . number_format($model_pembayaran->jumlah_pembayaran, '0', '.', '.') . '</td>
                            </tr>
                            <tr>
                                <td class="text-left">Potongan Harga</td>
                                <td class="text-right" width="10">:</td>
                                <td class="text-right">RP. ' . number_format($model_pembayaran->potongan_harga, '0', '.', '.') . '</td>
                            </tr>
                            <tr>
                                <td class="text-left">Total Pembayaran</td>
                                <td class="text-right" width="10">:</td>
                                <td class="text-right">RP. ' . number_format($model_pembayaran->jumlah_pembayaran - $model_pembayaran->potongan_harga, '0', '.', '.') . '</td>
                            </tr>
                        ';
                    } else {
                        $label = '
                            <tr>
                                <td class="text-left">Harga Kamar</td>
                                <td class="text-right" width="10">:</td>
                                <td class="text-right">RP. ' . number_format($model_pembayaran->jumlah_pembayaran, '0', '.', '.') . '</td>
                            </tr>
                        ';
                    }
                }

                $dataHTML = '
                <form class="modal-content" onsubmit="requestSelesaikanPembayaranKamar(event)" autocomplete="off" id="formselesaikanpembayarankamar">
                    <input type="hidden" name="__token" value="' . request()->input('token') . '" id="token">
                    <input type="hidden" name="pembayaran_id" value="' . $model_pembayaran->id . '" id="pembayaran_id">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="universalModalLabel">Bayar Kamar</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <table style="width: 100%;" id="labelpembayaran">
                                <tbody>
                                    ' . $label . '
                                    <tr>
                                        <td class="text-left">Total Bayar</td>
                                        <td class="text-right" width="10">:</td>
                                        <td class="text-right">RP. ' . number_format($model_pembayaran->total_bayar, '0', '.', '.') . '</td>
                                    </tr>
                                    <tr>
                                        <td class="text-left">Kurang Bayar</td>
                                        <td class="text-right" width="10">:</td>
                                        <td class="text-right">RP. ' . number_format($model_pembayaran->kurang_bayar, '0', '.', '.') . '</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mb-3">
                            <label for="total_bayar" class="form-label fw-bold">Total Bayar <sup class="text-danger">*</sup></label>
                            <div class="input-group" style="z-index: 0;">
                                <span class="input-group-text bg-success text-light fw-bold">RP</span>
                                <input type="text"
                                    class="form-control formatrupiah"
                                    name="total_bayar" id="total_bayar" value="0">
                            </div>
                            <span class="text-danger" id="errorTotalBayar"></span>
                        </div>
                        <div class="mb-3">
                            <label for="bukti_pembayaran" class="form-label fw-bold">Bukti Pembayaran <sup class="red">*</sup></label>
                            <input type="file" class="form-control" name="bukti_pembayaran" id="bukti_pembayaran">
                            <span class="text-danger" id="errorBuktiPembayaran"></span>
                        </div>
                        <div class="mb-3">
                            <label for="cash" class="form-label fw-bold">
                                Metode Pembayaran
                            </label>
                            <div class="row">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="metode_pembayaran" id="cash"
                                        value="Cash" checked>
                                    <label class="form-check-label" for="cash">
                                        Cash
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="metode_pembayaran" id="debit"
                                        value="Debit">
                                    <label class="form-check-label" for="debit">
                                        Debit
                                    </label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="metode_pembayaran" id="qris"
                                        value="QRIS">
                                    <label class="form-check-label" for="qris">
                                        QRIS
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="metode_pembayaran" id="transfer"
                                        value="Transfer">
                                    <label class="form-check-label" for="transfer">
                                        Transfer
                                    </label>
                                </div>
                            </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="potongan_harga" class="form-label fw-bold">Potongan Harga</label>
                            <div class="input-group" style="z-index: 0;">
                                <span class="input-group-text bg-success text-light fw-bold">RP</span>
                                <input type="text"
                                class="form-control formatrupiah"
                                name="potongan_harga" id="potongan_harga" value="0">
                                <button type="button" class="btn btn-success input-group-text" onclick="onGetToken(' . $model_pembayaran->id . ')" id="btnRequestGetToken">
                                    Get Token
                                </button>
                            </div>
                            <span class="text-danger" id="errorPotonganHarga"></span>
                        </div>
                        <hr/>
                        <div class="mb-3">
                            <div class="input-group" style="z-index: 0;">
                                <input type="text" class="form-control" name="kode" id="kode" placeholder="KODE-xxxxx">
                                <button type="button" class="btn btn-success input-group-text" onclick="onVerifikasi(' . $model_pembayaran->id . ')" id="btnRequestVerifikasi">
                                    Verifikasi
                                </button>
                            </div>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-success w-100" id="btnRequest">
                                Ya
                            </button>
                        </div>
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
        }

        return response()->json($response);
    }
    public function sendemailverifikasipotonganharga()
    {
        if (request()->ajax()) {
            try {
                DB::beginTransaction();

                $pembayaran_id = request()->input('pembayaran_id');
                $potongan_harga = request()->input('potongan_harga');

                $model_pembayaran = Pembayaran::where('id', intval($pembayaran_id))->first();
                $kamar = Lokasi::where('id', $model_pembayaran->lokasi_id)->first();
                $penyewa = Penyewa::where('id', $model_pembayaran->penyewa_id)->first();

                $kode = mt_rand(10000, 99999);

                $data = new Collection([
                    'pembayaran' => $model_pembayaran,
                    'kamar' => $kamar,
                    'penyewa' => $penyewa,
                    'kode' => $kode,
                    'potongan_harga' => str_replace('.', '', $potongan_harga),
                ]);

                $model_potonganharga = new Potonganharga();
                $model_potonganharga->kode = $kode;
                $model_potonganharga->potongan_harga = str_replace(".", "", $potongan_harga);
                $model_potonganharga->expired = 'N';
                $model_potonganharga->save();

                Mail::to(strtolower('maxwinata@gmail.com'))->send(new PotonganhargaEmail($data));

                $response = [
                    'status' => 200,
                    'message' => 'success',
                    'data' => request()->all(),
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
    public function verifikasipotonganharga()
    {
        if (request()->ajax()) {
            try {
                DB::beginTransaction();
                $pembayaran_id = request()->input('pembayaran_id');
                $kode = request()->input('kode');

                $model_potonganharga = Potonganharga::where('kode', (int)$kode)
                    ->where('expired', 'N')
                    ->whereDate('created_at', '<=', Carbon::now()->subMinutes(15))
                    ->first();

                if ($model_potonganharga) {
                    $model_pembayaran = Pembayaran::where('id', $pembayaran_id)->first();

                    Pembayaran::where('id', (int)$pembayaran_id)->update([
                        'potongan_harga' => intval($model_pembayaran->potongan_harga) + intval($model_potonganharga->potongan_harga),
                        'kurang_bayar' => intval($model_pembayaran->kurang_bayar) - intval($model_potonganharga->potongan_harga)
                    ]);

                    $model_pembayaran = Pembayaran::where('id', $pembayaran_id)->first();

                    if ($model_pembayaran->diskon != 0) {
                        $label = '
                                    <tr>
                                        <td class="text-left">Harga Kamar</td>
                                        <td class="text-right" width="10">:</td>
                                        <td class="text-right">RP. ' . number_format($model_pembayaran->jumlah_pembayaran, '0', '.', '.') . '</td>
                                    </tr>
                                    <tr>
                                        <td class="text-left">Potongan Harga</td>
                                        <td class="text-right" width="10">:</td>
                                        <td class="text-right">RP. ' . number_format($model_pembayaran->potongan_harga, '0', '.', '.') . '</td>
                                    </tr>
                                    <tr>
                                        <td class="text-left">Total Pembayaran</td>
                                        <td class="text-right" width="10">:</td>
                                        <td class="text-right">RP. ' . number_format($model_pembayaran->jumlah_pembayaran - $model_pembayaran->potongan_harga, '0', '.', '.') . '</td>
                                    </tr>
                                    <tr>
                                        <td class="text-left">Total Bayar</td>
                                        <td class="text-right" width="10">:</td>
                                        <td class="text-right">RP. ' . number_format($model_pembayaran->total_bayar, '0', '.', '.') . '</td>
                                    </tr>
                                    <tr>
                                        <td class="text-left">Kurang Bayar</td>
                                        <td class="text-right" width="10">:</td>
                                        <td class="text-right">RP. ' . number_format($model_pembayaran->kurang_bayar, '0', '.', '.') . '</td>
                                    </tr>
                                ';
                    } else {
                        if ($model_pembayaran->potongan_harga != 0) {
                            $label = '
                                        <tr>
                                            <td class="text-left">Harga Kamar</td>
                                            <td class="text-right" width="10">:</td>
                                            <td class="text-right">RP. ' . number_format($model_pembayaran->jumlah_pembayaran, '0', '.', '.') . '</td>
                                        </tr>
                                        <tr>
                                            <td class="text-left">Potongan Harga</td>
                                            <td class="text-right" width="10">:</td>
                                            <td class="text-right">RP. ' . number_format($model_pembayaran->potongan_harga, '0', '.', '.') . '</td>
                                        </tr>
                                        <tr>
                                            <td class="text-left">Total Pembayaran</td>
                                            <td class="text-right" width="10">:</td>
                                            <td class="text-right">RP. ' . number_format($model_pembayaran->jumlah_pembayaran - $model_pembayaran->potongan_harga, '0', '.', '.') . '</td>
                                        </tr>
                                        <tr>
                                            <td class="text-left">Total Bayar</td>
                                            <td class="text-right" width="10">:</td>
                                            <td class="text-right">RP. ' . number_format($model_pembayaran->total_bayar, '0', '.', '.') . '</td>
                                        </tr>
                                        <tr>
                                            <td class="text-left">Kurang Bayar</td>
                                            <td class="text-right" width="10">:</td>
                                            <td class="text-right">RP. ' . number_format($model_pembayaran->kurang_bayar, '0', '.', '.') . '</td>
                                        </tr>
                                    ';
                        } else {
                            $label = '
                                        <tr>
                                            <td class="text-left">Harga Kamar</td>
                                            <td class="text-right" width="10">:</td>
                                            <td class="text-right">RP. ' . number_format($model_pembayaran->jumlah_pembayaran, '0', '.', '.') . '</td>
                                        </tr>
                                        <tr>
                                            <td class="text-left">Total Bayar</td>
                                            <td class="text-right" width="10">:</td>
                                            <td class="text-right">RP. ' . number_format($model_pembayaran->total_bayar, '0', '.', '.') . '</td>
                                        </tr>
                                        <tr>
                                            <td class="text-left">Kurang Bayar</td>
                                            <td class="text-right" width="10">:</td>
                                            <td class="text-right">RP. ' . number_format($model_pembayaran->kurang_bayar, '0', '.', '.') . '</td>
                                        </tr>
                                    ';
                        }
                    }

                    Potonganharga::where('kode', (int)$kode)->update([
                        'pembayaran_id' => intval($pembayaran_id),
                        'expired' => 'Y'
                    ]);

                    if (intval($model_pembayaran->total_bayar) >= intval($model_pembayaran->kurang_bayar)) {
                        Pembayaran::where('id', (int)$pembayaran_id)->update([
                            'status_pembayaran' => 'completed'
                        ]);

                        Lokasi::where('id', $model_pembayaran->lokasi_id)->update([
                            'status' => 1,
                            'operator_id' => auth()->user()->id,
                            'updated_at' => date("Y-m-d H:i:s"),
                        ]);

                        $response = [
                            'status' => 200,
                            'message' => 'completed',
                            'label' => $label,
                        ];

                        DB::commit();
                        return response()->json($response);
                    }

                    $response = [
                        'status' => 200,
                        'message' => 'success',
                        'label' => $label,
                    ];
                } else {
                    $response = [
                        'status' => 400,
                        'message' => 'error',
                    ];
                }

                DB::commit();
                return response()->json($response);
            } catch (Exception $e) {
                DB::rollBack();
                $response = [
                    'status' => 422,
                    'message' => 'error' . $e->getMessage(),
                ];
            }
        }
    }
    public function selesaikanpembayarankamar()
    {
        if (request()->ajax()) {
            $pembayaran_id = htmlspecialchars(request()->input('pembayaran_id'), ENT_QUOTES, 'UTF-8');
            $total_bayar = htmlspecialchars(request()->input('total_bayar'), ENT_QUOTES, 'UTF-8');

            $pembayaran = $total_bayar ? str_replace('.', '', $total_bayar) : 0;
            $metode_pembayaran = htmlspecialchars(request()->input('metode_pembayaran'), ENT_QUOTES, 'UTF-8');

            if (Pembayaran::where('id', $pembayaran_id)->exists()) {
                try {
                    DB::beginTransaction();
                    $model_pembayaran = Pembayaran::where('id', $pembayaran_id)->first();

                    $jumlah_pembayaran = intval($model_pembayaran->jumlah_pembayaran) - intval($model_pembayaran->potongan_harga);

                    $tot_potongan = intval($model_pembayaran->potongan_harga);
                    $tot_bayar = intval($model_pembayaran->total_bayar) + intval($pembayaran);
                    $tot_kurangbayar = (intval($model_pembayaran->kurang_bayar) - intval($pembayaran));

                    if ($tot_bayar >= $jumlah_pembayaran) {
                        DB::table('pembayarans')->where('id', $model_pembayaran->id)->update([
                            'tanggal_pembayaran' => date('Y-m-d H:i:s'),
                            'potongan_harga' => $tot_potongan,
                            'total_bayar' => $tot_bayar,
                            'kurang_bayar' => $tot_kurangbayar,
                            'status_pembayaran' => 'completed',
                            'operator_id' => auth()->user()->id,
                            'updated_at' => date("Y-m-d H:i:s"),
                        ]);

                        Lokasi::where('id', $model_pembayaran->lokasi_id)->update([
                            'status' => 1,
                            'operator_id' => auth()->user()->id,
                            'updated_at' => date("Y-m-d H:i:s"),
                        ]);
                    } else {
                        DB::table('pembayarans')->where('id', $model_pembayaran->id)->update([
                            'tanggal_pembayaran' => date('Y-m-d H:i:s'),
                            'potongan_harga' => $tot_potongan,
                            'total_bayar' => $tot_bayar,
                            'kurang_bayar' => $tot_kurangbayar,
                            'operator_id' => auth()->user()->id,
                            'updated_at' => date("Y-m-d H:i:s"),
                        ]);
                    }

                    if (intval($pembayaran) > 0) {
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

                        $transaksi = new Transaksi();
                        $transaksi->no_transaksi = $no_transaksi;
                        $transaksi->tagih_id = 1;
                        $transaksi->pembayaran_id = $model_pembayaran->id;
                        $transaksi->tanggal_transaksi = date('Y-m-d H:i:s');
                        $transaksi->jumlah_uang = str_replace('.', '', $pembayaran);
                        $transaksi->metode_pembayaran = $metode_pembayaran;
                        $transaksi->tipe = "pemasukan";
                        $transaksi->operator_id = auth()->user()->id;
                        $posttransaksi = $transaksi->save();

                        if ($posttransaksi) {
                            if (request()->file('bukti_pembayaran')) {
                                $bukti_pembayaran = "bukti_pembayaran" . "-" . $transaksi->id . "." . request()->file('bukti_pembayaran')->getClientOriginalExtension();
                                $file = request()->file('bukti_pembayaran');
                                $tujuan_upload = 'img/bukti_pembayaran/pemasukan';
                                $file->move($tujuan_upload, $bukti_pembayaran);

                                Transaksi::where('id', $transaksi->id)->update([
                                    'bukti_pembayaran' => $bukti_pembayaran
                                ]);
                            }
                        }
                    }

                    DB::commit();
                    $response = [
                        'status' => 200,
                        'message' => 'success',
                    ];
                } catch (Exception $e) {
                    DB::rollBack();
                    $response = [
                        'status' => 422,
                        'message' => 'error' . $e->getMessage(),
                    ];
                }
            } else {
                $response = [
                    'status' => 422,
                    'message' => 'error',
                ];
            }

            return response()->json($response);
        }
    }
    // isi token kamar
    public function bayarisitokenkamar()
    {
        if (request()->ajax()) {
            $transaksi_id = htmlspecialchars(request()->input('transaksi_id'), ENT_QUOTES, 'UTF-8');
            $foto_kwh_lama = request()->file('foto_kwh_lama');
            $foto_kwh_baru = request()->file('foto_kwh_baru');
            $jumlah_kwh_lama = htmlspecialchars(request()->input('jumlah_kwh_lama'), ENT_QUOTES, 'UTF-8');
            $jumlah_kwh_baru = htmlspecialchars(request()->input('jumlah_kwh_baru'), ENT_QUOTES, 'UTF-8');
            $keterangan = htmlspecialchars(request()->input('keterangan'), ENT_QUOTES, 'UTF-8');
            $jumlah_pembayaran = htmlspecialchars(request()->input('jumlah_pembayaran'), ENT_QUOTES, 'UTF-8');
            $metode_pembayaran = htmlspecialchars(request()->input('metode_pembayaran'), ENT_QUOTES, 'UTF-8');

            if (Pembayaran::where('id', $transaksi_id)->exists()) {
                try {
                    DB::beginTransaction();
                    $model_pembayaran = Pembayaran::where('id', $transaksi_id)->first();

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

                    $transaksi = new Transaksi();
                    $transaksi->no_transaksi = $no_transaksi;
                    $transaksi->tagih_id = 2;
                    $transaksi->pembayaran_id = $model_pembayaran->id;
                    $transaksi->tanggal_transaksi = date('Y-m-d H:i:s');
                    $transaksi->jumlah_uang = str_replace('.', '', $jumlah_pembayaran);
                    $transaksi->metode_pembayaran = $metode_pembayaran;
                    $transaksi->tipe = "pengeluaran";
                    $transaksi->operator_id = auth()->user()->id;
                    $posttransaksi = $transaksi->save();

                    if ($posttransaksi) {

                        $bukti_pembayaran = "bukti_pembayaran" . "-" . $transaksi->id . "." . request()->file('bukti_pembayaran')->getClientOriginalExtension();
                        $file = request()->file('bukti_pembayaran');
                        $tujuan_upload = 'img/bukti_pembayaran/pengeluaran';
                        $file->move($tujuan_upload, $bukti_pembayaran);

                        Transaksi::where('id', $transaksi->id)->update([
                            'bukti_pembayaran' => $bukti_pembayaran
                        ]);

                        $model_post_tokenlistrik = new Tokenlistrik();
                        $model_post_tokenlistrik->tanggal_token = date('Y-m-d H:i:s');
                        $model_post_tokenlistrik->pembayaran_id = $model_pembayaran->id;
                        $model_post_tokenlistrik->penyewa_id = $model_pembayaran->penyewa_id;
                        $model_post_tokenlistrik->lokasi_id = $model_pembayaran->lokasi_id;
                        $model_post_tokenlistrik->jumlah_kwh_lama = $jumlah_kwh_lama;
                        $model_post_tokenlistrik->jumlah_kwh_baru = $jumlah_kwh_baru;
                        $model_post_tokenlistrik->jumlah_pembayaran = str_replace('.', '', $jumlah_pembayaran);
                        $model_post_tokenlistrik->bukti_pembayaran = $bukti_pembayaran;
                        $model_post_tokenlistrik->keterangan = $keterangan;
                        $model_post_tokenlistrik->operator_id = auth()->user()->id;
                        $model_post_tokenlistrik->save();

                        // foto kwh loma
                        $fotokwhlamatokenlistrik = "kwhlama" . "-" . $model_post_tokenlistrik->id . "." .  $foto_kwh_lama->getClientOriginalExtension();
                        $file = $foto_kwh_lama;
                        $tujuan_upload = 'img/fotokwhlamatokenlistrik';
                        $file->move($tujuan_upload, $fotokwhlamatokenlistrik);

                        // foto kwh baru
                        $fotokwhbarutokenlistrik = "kwhbaru" . "-" . $model_post_tokenlistrik->id . "." .  $foto_kwh_baru->getClientOriginalExtension();
                        $file = $foto_kwh_baru;
                        $tujuan_upload = 'img/fotokwhbarutokenlistrik';
                        $file->move($tujuan_upload, $fotokwhbarutokenlistrik);

                        Tokenlistrik::where('id', $model_post_tokenlistrik->id)->update([
                            'fotokwhlama' => $fotokwhlamatokenlistrik,
                            'fotokwhbaru' => $fotokwhbarutokenlistrik,
                        ]);

                        DB::commit();
                        $response = [
                            'status' => 200,
                            'message' => 'success',
                            'data' => request()->all()
                        ];
                    }
                } catch (Exception $e) {
                    DB::rollBack();
                    $response = [
                        'status' => 422,
                        'message' => 'error' . $e->getMessage(),
                    ];
                }
            } else {
                $response = [
                    'status' => 422,
                    'message' => request()->all(),
                ];
            }

            return response()->json($response);
        }
    }
    // perpanjang
    public function getmodalperpanjangpembayarankamar()
    {
        if (request()->ajax()) {
            $pembayaran_id = htmlspecialchars(request()->input('pembayaran_id'), ENT_QUOTES, 'UTF-8');
            if (Pembayaran::where('id', $pembayaran_id)->exists()) {
                $model_pembayaran = Pembayaran::where('id', $pembayaran_id)->first();

                if ($model_pembayaran->mitra_id == 3) {
                    $selectjenissewa = '';
                    $inputtotalhari = '';
                } else {
                    $jenissewa = [
                        'Harian',
                        'Mingguan / 7 Hari',
                        'Mingguan / (14 Hari)',
                        'Bulanan',
                    ];

                    $optionjenissewa = [];
                    foreach ($jenissewa as $value) {
                        $selected = $value == $model_pembayaran->jenissewa ? "selected" : "";
                        $optionjenissewa[] = '<option value="' . $value . '" ' . $selected . '>' . $value . '</option>';
                    }

                    $selectjenissewa = '
                    <div class="mb-3">
                        <label for="jenissewa" class="form-label fw-bold">Pilih Jenis Sewa</label>
                        <select class="form-select form-modal-select-2"
                            name="jenissewa" id="jenissewa" style="width: 100%;" onchange="selectJenisSewa()">
                            <option>Pilih Jenis Sewa</option>
                            ' . implode(" ", $optionjenissewa) . '
                        </select>
                        <span class="text-danger" id="errorJenisSewa"></span>
                    </div>
                    ';

                    $inputtotalhari = '
                    <div class="mb-3">
                        <label for="jumlahhari" class="form-label fw-bold">Jumlah Hari</label>
                        <div class="input-group" style="z-index: 0;">
                            <input type="number" class="form-control" name="jumlahhari" id="jumlahhari" oninput="jumlahHari()">
                            <span class="input-group-text bg-success text-light fw-bold">Hari</span>
                        </div>
                    </div>';
                }

                $dataHTML = '
                <form class="modal-content" onsubmit="requestBayarPerpanjangPenyewaanKamar(event)" autocomplete="off" id="formbayarperpanjangkamar">
                    <input type="hidden" name="_token" value="' . request()->input('token') . '" id="token">
                    <input type="hidden" name="_pembayaran_id" value="' . $pembayaran_id . '" id="pembayaran_id">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="universalModalLabel">Perpanjang Penyewaan Kamar</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ' . $selectjenissewa . '
                        ' . $inputtotalhari . '
                        <div class="mb-3">
                            <label for="total_bayar" class="form-label fw-bold">Total
                                Bayar</label>
                            <div class="input-group" style="z-index: 0;">
                                <span class="input-group-text bg-success text-light fw-bold">RP</span>
                                <input type="text"
                                    class="form-control formatrupiah"
                                    name="total_bayar" id="total_bayar" placeholder="0">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="bukti_pembayaran" class="form-label fw-bold">Bukti Pembayaran</label>
                            <input type="file" class="form-control" name="bukti_pembayaran" id="bukti_pembayaran">
                            <span class="text-danger" id="errorBuktiPembayaran"></span>
                        </div>
                        <div class="mb-3">
                            <label for="cash" class="form-label fw-bold">
                                Metode Pembayaran
                            </label>
                            <div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio"
                                        name="metode_pembayaran" id="none" value="None"
                                        checked>
                                    <label class="form-check-label" for="none">
                                        Tidak Ada
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="metode_pembayaran" id="cash"
                                        value="Cash">
                                    <label class="form-check-label" for="cash">
                                        Cash
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="metode_pembayaran" id="debit"
                                        value="Debit">
                                    <label class="form-check-label" for="debit">
                                        Debit
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="metode_pembayaran" id="qris"
                                        value="QRIS">
                                    <label class="form-check-label" for="qris">
                                        QRIS
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="metode_pembayaran" id="transfer"
                                        value="Transfer">
                                    <label class="form-check-label" for="transfer">
                                        Transfer
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-success text-light w-100" id="btnRequest">
                                Ya
                            </button>
                        </div>
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
        }

        return response()->json($response);
    }
    public function bayarperpanjangankamar()
    {
        if (request()->ajax()) {
            $pembayaran_id = htmlspecialchars(request()->input('_pembayaran_id'), ENT_QUOTES, 'UTF-8');
            $jenissewa = request()->input('jenissewa');
            $jumlahhari = htmlspecialchars(request()->input('jumlahhari'), true);
            $total_bayar = htmlspecialchars(request()->input('total_bayar'), true);
            $metode_pembayaran = htmlspecialchars(request()->input('metode_pembayaran'), ENT_QUOTES, 'UTF-8');

            if (Pembayaran::where('id', $pembayaran_id)->exists()) {
                try {
                    DB::beginTransaction();
                    $model_pembayaran = Pembayaran::where('id', $pembayaran_id)->first();
                    $kamar = Lokasi::where('id', $model_pembayaran->lokasi_id)->first();
                    $model_harga = Harga::where("tipekamar_id", $kamar->tipekamar_id)->first();

                    $tanggalmasuk = $model_pembayaran->tanggal_keluar;
                    $tanggalmasuk_format = Carbon::parse($tanggalmasuk)->format('Y-m-d H:i');

                    if ($total_bayar) {
                        $total_bayar = str_replace(".", "", $total_bayar);
                    } else {
                        $total_bayar = 0;
                    }

                    if ($model_pembayaran->mitra_id == 3) {
                        // Bulanan (Monthly)
                        $tenggatwaktu = Carbon::parse($this->check_out($tanggalmasuk_format))->addMonth();

                        $diskon = 0;
                        $potongan_harga = 0;
                        $jumlah_pembayaran = 500000;

                        $tagih = 3;
                    } else {
                        if (stripos($jenissewa, 'Harian') !== false) {
                            $tenggatwaktu = $this->check_out($tanggalmasuk_format);

                            if ($model_harga->mitra_id == 1) {
                                $diskon = 0;
                                $potongan_harga = 0;
                                $jumlah_pembayaran = $model_harga->harian;
                            } elseif ($model_harga->mitra_id == 2) {
                                $diskon = 15;
                                $potongan_harga = $model_harga->harian * ($diskon / 100);
                                $jumlah_pembayaran = $model_harga->harian - $potongan_harga;
                            }

                            if ($jumlahhari || $jumlahhari > 0) {
                                $new_tanggalmasuk = Carbon::parse($tanggalmasuk_format)->addDay($jumlahhari - 1);
                                $tenggatwaktu = $this->check_out($new_tanggalmasuk);

                                $potongan_harga = $model_harga->harian * intval($jumlahhari) * ($diskon / 100);
                                $jumlah_pembayaran = intval($jumlah_pembayaran) * intval($jumlahhari);
                            }
                        } elseif (stripos($jenissewa, 'Mingguan / 7 Hari') !== false) {
                            $tenggatwaktu = Carbon::parse($this->check_out($tanggalmasuk_format))->addWeek();

                            if ($model_harga->mitra_id == 1) {
                                $diskon = 0;
                                $potongan_harga = 0;
                                $jumlah_pembayaran = $model_harga->mingguan;
                            } elseif ($model_harga->mitra_id == 2) {
                                $diskon = 15;
                                $potongan_harga = $model_harga->mingguan * ($diskon / 100);
                                $jumlah_pembayaran = $model_harga->mingguan - $potongan_harga;
                            }
                        } elseif (stripos($jenissewa, 'Mingguan / (14 Hari)') !== false) {
                            $tenggatwaktu = Carbon::parse($this->check_out($tanggalmasuk_format))->addWeeks(2);

                            if ($model_harga->mitra_id == 1) {
                                $diskon = 0;
                                $potongan_harga = 0;
                                $jumlah_pembayaran = $model_harga->hari14;
                            } elseif ($model_harga->mitra_id == 2) {
                                $diskon = 15;
                                $potongan_harga = $model_harga->hari14 * ($diskon / 100);
                                $jumlah_pembayaran = $model_harga->hari14 - $potongan_harga;
                            }
                        } elseif (stripos($jenissewa, 'Bulanan') !== false) {
                            // Bulanan (Monthly)
                            $tenggatwaktu = Carbon::parse($this->check_out($tanggalmasuk_format))->addMonth();

                            if ($model_harga->mitra_id == 1) {
                                $diskon = 0;
                                $potongan_harga = 0;
                                $jumlah_pembayaran = $model_harga->bulanan;
                            } elseif ($model_harga->mitra_id == 2) {
                                $diskon = 15;
                                $potongan_harga = $model_harga->bulanan * ($diskon / 100);
                                $jumlah_pembayaran = $model_harga->bulanan - $potongan_harga;
                            }
                        }

                        $tagih = 1;
                    }

                    if (intval($total_bayar) >= intval($jumlah_pembayaran)) {
                        $status_pembayaran = "completed";
                        $status_kamar = 1;
                    } else {
                        $status_pembayaran = "pending";
                        $status_kamar = 2;
                    }

                    $model_post_pembayaran = new Pembayaran();
                    $model_post_pembayaran->tagih_id = $tagih;
                    if (intval($total_bayar) > 0) {
                        $model_post_pembayaran->tanggal_pembayaran = date('Y-m-d H:i:s');
                    }

                    $model_post_pembayaran->tanggal_masuk = $tanggalmasuk;
                    $model_post_pembayaran->tanggal_keluar = $tenggatwaktu;
                    $model_post_pembayaran->penyewa_id = $model_pembayaran->penyewa_id;
                    $model_post_pembayaran->mitra_id = $model_pembayaran->mitra_id;
                    $model_post_pembayaran->lokasi_id = $model_pembayaran->lokasi_id;
                    $model_post_pembayaran->tipekamar_id = $model_pembayaran->tipekamar_id;
                    $model_post_pembayaran->tipekamar = Tipekamar::where('id', $model_pembayaran->tipekamar_id)->first()->tipekamar;
                    $model_post_pembayaran->jenissewa = $jenissewa;
                    $model_post_pembayaran->jumlah_pembayaran = intval($jumlah_pembayaran) + intval($potongan_harga);
                    $model_post_pembayaran->diskon = $diskon;
                    $model_post_pembayaran->potongan_harga = intval($potongan_harga);
                    $model_post_pembayaran->total_bayar = $total_bayar;
                    $model_post_pembayaran->kurang_bayar = intval($jumlah_pembayaran) - intval($total_bayar);
                    $model_post_pembayaran->status_pembayaran = $status_pembayaran;
                    $model_post_pembayaran->status = 1;
                    $model_post_pembayaran->operator_id = auth()->user()->id;
                    $post = $model_post_pembayaran->save();

                    if ($post) {
                        if (intval($total_bayar) > 0) {
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

                            $transaksi = new Transaksi();
                            $transaksi->no_transaksi = $no_transaksi;
                            $transaksi->tagih_id = $tagih;
                            $transaksi->pembayaran_id = $model_post_pembayaran->id;
                            $transaksi->tanggal_transaksi = date('Y-m-d H:i:s');
                            $transaksi->jumlah_uang = $total_bayar;
                            $transaksi->metode_pembayaran = $metode_pembayaran;
                            $transaksi->tipe = "pemasukan";
                            $transaksi->operator_id = auth()->user()->id;
                            $posttransaksi = $transaksi->save();

                            if ($posttransaksi) {
                                if (request()->file('bukti_pembayaran')) {
                                    $bukti_pembayaran = "bukti_pembayaran" . "-" . $transaksi->id . "." . request()->file('bukti_pembayaran')->getClientOriginalExtension();
                                    $file = request()->file('bukti_pembayaran');
                                    $tujuan_upload = 'img/bukti_pembayaran/pemasukan';
                                    $file->move($tujuan_upload, $bukti_pembayaran);

                                    Transaksi::where('id', $transaksi->id)->update([
                                        'bukti_pembayaran' => $bukti_pembayaran
                                    ]);
                                }
                            }
                        }

                        Lokasi::where('id', $model_pembayaran->lokasi_id)->update([
                            'status' => $status_kamar,
                            'operator_id' => auth()->user()->id,
                            'updated_at' => date("Y-m-d H:i:s"),
                        ]);

                        // // denda checkout
                        // $givenDateTime = Carbon::create($model_pembayaran->tanggal_keluar);

                        // // Ambil waktu sekarang
                        // $now = Carbon::now();

                        // // Tentukan waktu batasan (15:00 pada tanggal yang sama)
                        // $limitTime = $givenDateTime->copy()->setHour(15)->setMinute(0)->setSecond(0);

                        // // Hitung pembayaran berdasarkan waktu sekarang dan waktu batasan
                        // if ($now->greaterThanOrEqualTo($givenDateTime) && $now->greaterThanOrEqualTo($limitTime)) {
                        //     $model_denda_checkout = new Denda();
                        //     $model_denda_checkout->tanggal_denda = date('Y-m-d H:i:s');
                        //     $model_denda_checkout->pembayaran_id = $model_pembayaran->id;
                        //     $model_denda_checkout->penyewa_id = $model_pembayaran->penyewa_id;
                        //     $model_denda_checkout->lokasi_id = $model_pembayaran->lokasi_id;
                        //     $model_denda_checkout->tagih_id = 3;
                        //     $model_denda_checkout->jumlah_uang = 100000;
                        //     $model_denda_checkout->operator_id = auth()->user()->id;
                        //     $model_denda_checkout->save();
                        // } elseif ($now->greaterThanOrEqualTo($givenDateTime) && $now->lessThan($limitTime)) {
                        //     $model_denda_checkout = new Denda();
                        //     $model_denda_checkout->tanggal_denda = date('Y-m-d H:i:s');
                        //     $model_denda_checkout->pembayaran_id = $model_pembayaran->id;
                        //     $model_denda_checkout->penyewa_id = $model_pembayaran->penyewa_id;
                        //     $model_denda_checkout->lokasi_id = $model_pembayaran->lokasi_id;
                        //     $model_denda_checkout->tagih_id = 3;
                        //     $model_denda_checkout->jumlah_uang = 50000;
                        //     $model_denda_checkout->operator_id = auth()->user()->id;
                        //     $model_denda_checkout->save();
                        // }

                        Pembayaran::where('id', $model_pembayaran->id)->update([
                            'status' => 0,
                            'operator_id' => auth()->user()->id,
                            'updated_at' => date("Y-m-d H:i:s"),
                        ]);
                    }

                    DB::commit();
                    $response = [
                        'status' => 200,
                        'message' => 'success',
                    ];
                } catch (Exception $e) {
                    DB::rollBack();
                    $response = [
                        'status' => 422,
                        'message' => 'error' . $e->getMessage(),
                    ];
                }
            } else {
                $response = [
                    'status' => 400,
                    'message' => 'error',
                ];
            }

            return response()->json($response);
        }
    }
    // end baru
    // Trigger
    public function getselectlantaikamar()
    {
        if (request()->ajax()) {
            $lantai = htmlspecialchars(request()->input('lantai'), ENT_QUOTES, 'UTF-8');
            $min = htmlspecialchars(request()->input('min'), ENT_QUOTES, 'UTF-8');
            $minFormat = Carbon::parse($min)->format('Y-m-d');
            $max = htmlspecialchars(request()->input('max'), ENT_QUOTES, 'UTF-8');
            $maxFormat = Carbon::parse($max)->format('Y-m-d');
            if (Lantai::where('id', (int)$lantai)->exists()) {
                // $now = Carbon::now();

                // $kamar = DB::table('lokasis as l')
                //     ->leftJoin('bookings as b', 'l.id', '=', 'b.lokasi_id')
                //     ->select(
                //         'l.id',
                //         'l.nomor_kamar',
                //         'l.tipekamar_id',
                //         'l.lantai_id',
                //         'l.status',
                //         'b.dari_tanggal',
                //         'b.sampai_tanggal'
                //     )
                //     ->where('l.jenisruangan_id', 2)
                //     ->where('l.lantai_id', (int)$lantai) // Pastikan $lantai adalah integer
                //     ->whereNotIn('l.tipekamar_id', [5, 6, 7])
                //     ->where('l.status', 0)
                //     ->where(function ($query) use ($minFormat, $maxFormat) {
                //         $query->whereNull('b.dari_tanggal')
                //             ->orWhere(function ($subQuery) use ($minFormat, $maxFormat) {
                //                 $subQuery->whereRaw("CAST(b.dari_tanggal AS DATE) >= ?", [$minFormat])
                //                     ->whereRaw("CAST(b.dari_tanggal AS DATE) <= ?", [$maxFormat])
                //                     ->orWhereRaw("CAST(b.sampai_tanggal AS DATE) >= ?", [$minFormat])
                //                     ->whereRaw("CAST(b.sampai_tanggal AS DATE) <= ?", [$maxFormat]);
                //             });
                //     })
                //     ->get();

                // $kamar = DB::table('lokasis as l')
                //     ->leftJoin('bookings as b', 'l.id', '=', 'b.lokasi_id')
                //     ->select(
                //         'l.id',
                //         'l.nomor_kamar',
                //         'l.tipekamar_id',
                //         'l.lantai_id',
                //         'b.dari_tanggal',
                //         'b.sampai_tanggal'
                //     )
                //     ->where('l.jenisruangan_id', 2)
                //     ->where('l.lantai_id', (int)$lantai)
                //     ->whereNotIn('l.tipekamar_id', [5, 6, 7])
                //     ->where('l.status', 0)
                //     ->where(function ($query) use ($now, $minFormat, $maxFormat) {
                //         $query->whereNull('b.dari_tanggal')
                //             ->orWhere(function ($query) use ($now, $minFormat, $maxFormat) {
                //                 $query->whereDate('b.dari_tanggal', '>', '2024-10-22')
                //                     ->whereDate('b.sampai_tanggal', '<', '2024-10-26');
                //             });
                //     })
                //     ->get();

                // dd($kamar);
                // $kamar = Lokasi::where('jenisruangan_id', 2)
                //     ->where('lantai_id', (int)$lantai)
                //     ->whereNotIn('tipekamar_id', [5, 6, 7])
                //     ->where('status', 0)
                //     ->get();

                $kamar = DB::select("
                SELECT
                    l.id, 
                    l.nomor_kamar, 
                    l.tipekamar_id, 
                    l.lantai_id, 
                    l.status,
                    b.dari_tanggal, 
                    b.sampai_tanggal
                    FROM lokasis as l
                    LEFT JOIN bookings b ON l.id = b.lokasi_id
                    WHERE l.jenisruangan_id = 2
                    AND l.lantai_id = " . (int)$lantai . "
                    AND l.tipekamar_id NOT IN (5, 6, 7)
                    AND l.status = 0
                    AND
                    (
                    (b.dari_tanggal IS NULL)
                    OR
                    NOT (
                        (CAST(b.dari_tanggal AS DATE) >= '" . $minFormat . "' AND CAST(b.dari_tanggal AS DATE) <= '" . $maxFormat . "')
                        OR
                        (CAST(b.sampai_tanggal AS DATE) >= '" . $minFormat . "' AND CAST(b.sampai_tanggal AS DATE) <= '" . $maxFormat . "')
                        )
                    )
                ");

                $selectkamar = [];
                foreach ($kamar as $row) {
                    // if (Booking::where('lokasi_id', (int)$row->id)->whereDate('dari_tanggal', '>', $now)
                    //     ->orWhereDate('sampai_tanggal', '<', $now)->where('status', 1)->exists()
                    // ) {
                    $selectkamar[] = '<option value="' . $row->id . '">Nomor Kamar: ' . $row->nomor_kamar . ' | Tipe Kamar: ' . Tipekamar::where('id', $row->tipekamar_id)->first()->tipekamar . '</option>';
                    // }
                }

                $response = [
                    'status' => 200,
                    'message' => 'success',
                    'data' => [
                        // 'now' => $now->format('Y-m-d'),
                        'namalantai' => Lantai::where('id', (int)$lantai)->first()->namalantai,
                        'dataHTML' => implode(" ", $selectkamar)
                    ]
                ];
            } else {
                $response = [
                    'status' => 422,
                    'message' => 'error',
                ];
            }
            return response()->json($response);
        }
    }
    public function getselectkamar()
    {
        if (request()->ajax()) {
            $kamar_id = htmlspecialchars(request()->input('kamar'), ENT_QUOTES, 'UTF-8');
            if (Lokasi::where('id', (int)$kamar_id)->exists()) {

                $kamar = Lokasi::where('id', (int)$kamar_id)->first();

                $jenissewa = [
                    'Harian',
                    'Mingguan / 7 Hari',
                    'Mingguan / (14 Hari)',
                    'Bulanan',
                ];

                $selectjenissewa = [];
                foreach ($jenissewa as $value) {
                    $selectjenissewa[] = '<option value="' . $value . '">' . $value . '</option>';
                }

                $response = [
                    'status' => 200,
                    'message' => 'success',
                    'data' => [
                        'nomorkamar' => $kamar->nomor_kamar,
                        'tipekamar' => $kamar->tipekamars->tipekamar,
                        'dataHTML' => implode(" ", $selectjenissewa),
                    ]
                ];
            } else {
                $response = [
                    'status' => 422,
                    'message' => 'error',
                ];
            }
            return response()->json($response);
        }
    }
    public function getselectjenissewa()
    {
        if (request()->ajax()) {
            $kamar_id = htmlspecialchars(request()->input('kamar'), ENT_QUOTES, 'UTF-8');
            $jenissewa = request()->input('jenissewa');
            if (Lokasi::where('id', (int)$kamar_id)->exists()) {
                $kamar = Lokasi::where('id', (int)$kamar_id)->first();
                $harga = Harga::where('tipekamar_id', (int)$kamar->tipekamar_id)->first();
                if (Harga::where('tipekamar_id', (int)$kamar->tipekamar_id)->where('mitra_id', (int)$harga->mitra_id)->exists()) {
                    $selectmitra = [];
                    foreach (
                        Harga::join('mitras as t2', 'hargas.mitra_id', '=', 't2.id')
                            ->where('hargas.tipekamar_id', '=', $harga->tipekamar_id)
                            ->get() as $row
                    ) {
                        $selectmitra[] = '<option value="' . $row->id . '">' . $row->mitra . '</option>';
                    }

                    $response = [
                        'status' => 200,
                        'message' => 'success',
                        'data' => [
                            'jenissewa' => $jenissewa,
                            'dataHTML' => implode(" ", $selectmitra),
                        ]
                    ];
                }
            } else {
                $response = [
                    'status' => 422,
                    'message' => 'error',
                ];
            }
            return response()->json($response);
        }
    }
    public function getselectmitra()
    {
        if (request()->ajax()) {
            $kamar_id = htmlspecialchars(request()->input('kamar'), ENT_QUOTES, 'UTF-8');
            $mitra = request()->input('mitra');
            $jenissewa = request()->input('jenissewa');
            $jumlahhari = request()->input('jumlahhari');
            if (Lokasi::where('id', (int)$kamar_id)->exists() && Mitra::where('id', (int)$mitra)->exists()) {
                $kamar = Lokasi::where('id', (int)$kamar_id)->first();
                $harga = Harga::where('tipekamar_id', (int)$kamar->tipekamar_id)->where('mitra_id', (int)$mitra)->first();

                if ($jenissewa == "Harian") {
                    if ($harga->mitra_id == 1) {
                        $hargakamar = $harga->harian;

                        $diskon = 0;
                        $potongan = 0;

                        $totalpembayaran = $harga->harian;
                    } elseif ($harga->mitra_id == 2) {
                        $hargakamar = $harga->harian;

                        $diskon = 15;
                        $potongan = $harga->harian * ($diskon / 100);

                        $totalpembayaran = $harga->harian - $potongan;
                    }

                    if ($jumlahhari || $jumlahhari > 0) {
                        $potongan = $harga->harian * intval($jumlahhari) * ($diskon / 100);
                        $totalpembayaran = intval($totalpembayaran) * intval($jumlahhari);
                    }
                } elseif ($jenissewa == "Mingguan / 7 Hari") {
                    if ($harga->mitra_id == 1) {
                        $hargakamar = $harga->mingguan;

                        $diskon = 0;
                        $potongan = 0;

                        $totalpembayaran = $harga->mingguan;
                    } elseif ($harga->mitra_id == 2) {
                        $hargakamar = $harga->mingguan;

                        $diskon = 15;
                        $potongan = $harga->mingguan * ($diskon / 100);

                        $totalpembayaran = $harga->mingguan - $potongan;
                    }
                } elseif ($jenissewa == "Mingguan / (14 Hari)") {
                    if ($harga->mitra_id == 1) {
                        $hargakamar = $harga->hari14;

                        $diskon = 0;
                        $potongan = 0;

                        $totalpembayaran = $harga->hari14;
                    } elseif ($harga->mitra_id == 2) {
                        $hargakamar = $harga->hari14;

                        $diskon = 15;
                        $potongan = $harga->hari14 * ($diskon / 100);

                        $totalpembayaran = $harga->hari14 - $potongan;
                    }
                } elseif ($jenissewa == "Bulanan") {
                    if ($harga->mitra_id == 1) {
                        $hargakamar = $harga->bulanan;

                        $diskon = 0;
                        $potongan = 0;

                        $totalpembayaran = $harga->bulanan;
                    } elseif ($harga->mitra_id == 2) {
                        $hargakamar = $harga->bulanan;

                        $diskon = 15;
                        $potongan = $harga->bulanan * ($diskon / 100);

                        $totalpembayaran = $harga->bulanan - $potongan;
                    }
                } else {
                    $hargakamar = 0;

                    $diskon = 0;
                    $potongan = 0;

                    $totalpembayaran = 0;
                }

                $selectmitra = [];
                foreach (Mitra::all() as $row) {
                    $selectmitra[] = '<option value="' . $row->id . '">' . $row->mitra . '</option>';
                }

                $response = [
                    'status' => 200,
                    'message' => 'success',
                    'data' => [
                        'mitra' => Mitra::where('id', (int)$mitra)->first()->mitra,
                        'hargakamar' => $hargakamar,
                        'diskon' => $diskon,
                        'potongan' => $potongan,
                        'totalpembayaran' => $totalpembayaran,
                        'dataHTML' => implode(" ", $selectmitra),
                    ]
                ];
            } else {
                $response = [
                    'status' => 422,
                    'message' => 'error',
                ];
            }
            return response()->json($response);
        }
    }
    // Helper
    public function check_out($checkinTime)
    {
        // // Konversi waktu check-in ke timestamp
        // $checkinTimestamp = strtotime($checkinTime);

        // // Dapatkan jam dari waktu check-in
        // $checkinHour = date('H', $checkinTimestamp);

        // // Tentukan waktu check-out berdasarkan aturan yang diberikan
        // if ($checkinHour < 6) {
        //     // Jika check-in sebelum jam 6 pagi, check-out pada jam 12 siang di hari yang sama
        //     $checkoutTimestamp = strtotime(date('Y-m-d', $checkinTimestamp) . ' 12:00');
        // } else {
        //     // Jika check-in pada atau setelah jam 6 pagi, check-out pada jam 12 siang keesokan harinya
        //     $checkoutTimestamp = strtotime(date('Y-m-d', $checkinTimestamp) . ' +1 day 12:00');
        // }

        // return date('Y-m-d H:i', $checkoutTimestamp);

        // Check out baru
        // Konversi waktu check-in ke timestamp
        $checkinTimestamp = strtotime($checkinTime);

        // Tentukan waktu check-out selalu pada jam 1 siang keesokan harinya
        $checkoutTimestamp = strtotime(date('Y-m-d', $checkinTimestamp) . ' +1 day 13:00');

        return date('Y-m-d H:i', $checkoutTimestamp);
    }
}
