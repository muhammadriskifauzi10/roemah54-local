<?php

namespace App\Http\Controllers\Dashboard\Penyewa\Booking;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Lokasi;
use App\Models\Mitra;
use App\Models\Pembayaran;
use App\Models\Penyewa;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function index()
    {
        $data = [
            'judul' => 'Booking Kamar',
        ];

        return view('contents.dashboard.penyewa.booking.main', $data);
    }
    public function datatablebooking()
    {
        // $minDate = request()->input('minDate');
        // $maxDate = request()->input('maxDate');
        $mitra = request()->input('mitra');
        $status_pembayaran = request()->input('status_pembayaran');

        $penyewaankamar = Booking::join('pembayarans as p', 'bookings.pembayaran_id', '=', 'p.id')
            ->select(
                'p.id',
                'bookings.dari_tanggal',
                'bookings.sampai_tanggal',
                'bookings.catatan',
                'p.penyewa_id',
                'p.lokasi_id',
                'p.tipekamar',
                'p.mitra_id',
                'p.jenissewa',
                'p.jumlah_pembayaran',
                'p.diskon',
                'p.potongan_harga',
                'p.total_bayar',
                'p.tanggal_pembayaran',
                'p.kurang_bayar',
                'p.status_pembayaran',
                'p.status'
            )
            ->when($mitra, function ($query) use ($mitra) {
                foreach (Mitra::all() as $row) {
                    if ($mitra == $row->id) {
                        return $query->where('p.mitra_id', $row->id);
                    }
                }
            })
            ->when($status_pembayaran !== "Pilih Status Pembayaran", function ($query) use ($status_pembayaran) {
                $query->where('p.status_pembayaran', $status_pembayaran);
            })
            ->where('p.status', 2)
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

            // status penyewa
            if ($row->status_pembayaran == "completed") {
                $bayar = '';

                // $cetakkwitansi = '
                //     <a href="' . route('penyewaankamar.cetakkwitansi', encrypt($row->id)) . '" class="btn btn-success text-light fw-bold d-flex align-items-center justify-content-center gap-1" style="width: 180px;" target="_blank">
                //         <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16">
                //             <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/>
                //             <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1"/>
                //         </svg>
                //         Cetak Kwitansi
                //     </a>';
                // $cetakinvoice = "";
            } else if ($row->status_pembayaran == "pending") {
                $bayar = '
                    <button type="button" class="btn btn-success text-light fw-bold d-flex align-items-center justify-content-center gap-1" onclick="openModalBayarKamar(event, ' . $row->id . ')" style="width: 180px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-credit-card" viewBox="0 0 16 16">
                            <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v1h14V4a1 1 0 0 0-1-1zm13 4H1v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1z"/>
                            <path d="M2 10a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"/>
                        </svg>
                        Bayar
                    </button>';

                // if ($row->tanggal_pembayaran) {
                //     $cetakkwitansi = '
                //         <a href="' . route('penyewaankamar.cetakkwitansi', encrypt($row->id)) . '" class="btn btn-success text-light fw-bold d-flex align-items-center justify-content-center gap-1" style="width: 180px;" target="_blank">
                //             <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16">
                //                 <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/>
                //                 <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1"/>
                //             </svg>
                //             Cetak Kwitansi
                //         </a>';
                // } else {
                //     $cetakkwitansi = "";
                // }

                // $cetakinvoice = '
                //      <a href="' . route('penyewaankamar.cetakinvoice', encrypt($row->id)) . '" class="btn btn-warning text-light fw-bold d-flex align-items-center justify-content-center gap-1" style="width: 180px;" target="_blank">
                //         <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16">
                //             <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/>
                //             <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1"/>
                //         </svg>
                //         Cetak Invoice
                //     </a>';
            } elseif ($row->status_pembayaran == "failed") {
                $bayar = '';
                // $cetakkwitansi = "";
                // $cetakinvoice = "";
            }

            // status
            if (Carbon::now() > $row->sampai_tanggal) {
                $status = "<span class='badge bg-danger'>Sudah Lewat Tanggal</span>";

                $selesaikanbooking = '';
            } else {
                $status = "<span class='badge bg-success'>Booking</span>";

                $selesaikanbooking = '
                <button type="button" class="btn btn-warning text-light fw-bold d-flex align-items-center justify-content-center gap-1" onclick="openModalSelesaikanBooking(event, ' . $row->id . ')" style="width: 180px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-up-right" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5"/>
                        <path fill-rule="evenodd" d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0z"/>
                    </svg>
                    Selesaikan
                </button>';
            }

            $aksi = '
                <div class="d-flex flex-column align-items-center justify-content-center gap-1">
                    ' . $selesaikanbooking . '
                    ' . $bayar . '
                </div>
            ';
            // ' . $cetakkwitansi . '
            // ' . $cetakinvoice . '

            $output[] = [
                'nomor' => "<strong>" . $no++ . "</strong>",
                'dari_tanggal' => Carbon::parse($row->dari_tanggal)->format("d-m-Y H:i"),
                'sampai_tanggal' => Carbon::parse($row->sampai_tanggal)->format("d-m-Y H:i"),
                'nama_booking' => $row->penyewas->namalengkap,
                'no_hp' => $row->penyewas->nohp,
                'nomor_kamar' => $row->lokasis->nomor_kamar,
                'tipe_kamar' => $row->tipekamar,
                'mitra' => $row->mitras->mitra,
                'jenissewa' => $row->jenissewa,
                'jumlah_pembayaran' => $row->jumlah_pembayaran ? "RP. " . number_format($row->jumlah_pembayaran, '0', '.', '.') : "RP. 0",
                'diskon' => intval($row->diskon) . " %",
                'potongan_harga' => $row->potongan_harga ? "RP. " . number_format($row->potongan_harga, '0', '.', '.') : "RP. 0",
                'total_bayar' => $row->total_bayar ? "RP. " . number_format($row->total_bayar, '0', '.', '.') : "RP. 0",
                'tanggal_pembayaran' => $row->tanggal_pembayaran ? Carbon::parse($row->tanggal_pembayaran)->format("d-m-Y H:i:s") : "-",
                'kurang_bayar' => $row->kurang_bayar ? "RP. " . number_format($row->kurang_bayar, '0', '.', '.') : "RP. 0",
                'status_pembayaran' => $status_pembayaran,
                'status' => $status,
                'aksi' => $aksi,
            ];
        }

        return response()->json([
            'data' => $output
        ]);
    }
    // booking
    public function getmodalselesaikanbooking()
    {
        if (request()->ajax()) {
            $pembayaran_id = htmlspecialchars(request()->input('pembayaran_id'), ENT_QUOTES, 'UTF-8');
            if (Pembayaran::where('id', $pembayaran_id)->exists()) {
                $model_pembayaran = Pembayaran::where('id', $pembayaran_id)->first();
                $penyewa = Penyewa::find($model_pembayaran->penyewa_id);
                $booking = Booking::where('pembayaran_id', $model_pembayaran->id)->first();

                $selectedL = $penyewa->jenis_kelamin == 'L' ? 'selected' : '';
                $selectedP = $penyewa->jenis_kelamin == 'P' ? 'selected' : '';

                $dataHTML = '
                 <form class="modal-content" onsubmit="requestSelesaikanBooking(event)" autocomplete="off" id="formselesaikanbooking">
                    <input type="hidden" name="__token" value="' . request()->input('token') . '" id="token">
                    <input type="hidden" name="pembayaran_id" value="' . $model_pembayaran->id . '" id="pembayaran_id">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="universalModalLabel">Selesaikan Booking</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <table class="table table-borderless not-va" style="width: 100%; ">
                                    <tr>
                                        <td class="text-left">Catatan</td>
                                        <td class="text-right" width="10">:</td>
                                        <td class="text-left">' . nl2br($booking->catatan) . '</td>
                                    </tr>
                            </table>
                        </div>
                        <div class="card border-2 border-dark mb-3">
                            <div class="card-header bg-dark text-light">
                                Data Diri
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6 mb-3">
                                        <label for="noktp" class="form-label fw-bold">No KTP</label>
                                        <input type="number"
                                            class="form-control"
                                            name="noktp" id="noktp" placeholder="Masukkan No KTP" value="' . $penyewa->noktp . '">
                                            <div class="invalid-feedback" id="errornoktp">
                                            </div>
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <label for="namalengkap" class="form-label fw-bold">Nama
                                            Lengkap</label>
                                        <input type="text"
                                            class="form-control"
                                            name="namalengkap" id="namalengkap"
                                            placeholder="Masukkan Nama Lengkap" value="' . $penyewa->namalengkap . '">
                                            <div class="invalid-feedback" id="errornamalengkap">
                                            </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6 mb-3">
                                        <label for="nohp" class="form-label fw-bold">No HP / WA</label>
                                        <input type="text"
                                            class="form-control"
                                            name="nohp" id="nohp" placeholder="Masukkan No HP / WA" value="' . $penyewa->nohp . '">
                                            <div class="invalid-feedback" id="errornohp">
                                            </div>
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <label for="fotoktp" class="form-label fw-bold">Foto KTP</label>
                                        <input type="file"
                                            class="form-control"
                                            name="fotoktp" id="fotoktp">
                                            <div class="invalid-feedback" id="errorfotoktp">
                                            </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <label for="alamat" class="form-label fw-bold">Alamat KTP</label>
                                        <textarea class="form-control" name="alamat" id="alamat">' . nl2br($penyewa->alamat) . '</textarea>
                                        <div class="invalid-feedback" id="erroralamat">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <label for="jenis_kelamin" class="form-label fw-bold">Jenis
                                            Kelamin</label>
                                        <select
                                            class="form-select form-select-2"
                                            name="jenis_kelamin" id="jenis_kelamin" style="width: 100%;">
                                            <option>Pilih Jenis Kelamin</option>
                                            <option value="L" ' . $selectedL . '>Laki-Laki</option>
                                            <option value="P" ' . $selectedP . '>Perempuan</option>
                                        </select>
                                        <div class="invalid-feedback" id="errorjeniskelamin">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="tanggalmasuk" class="form-label fw-bold">Tanggal
                                Masuk</label>
                            <input type="datetime-local"
                                class="form-control without_ampm"
                                name="tanggalmasuk" id="tanggalmasuk">
                            <div class="invalid-feedback" id="errortanggalmasuk">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success w-100" id="btnRequest">
                            Ya
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
        }

        return response()->json($response);
    }
    public function postselesaikanbooking()
    {
        if (request()->ajax()) {
            $pembayaran_id = htmlspecialchars(request()->input('pembayaran_id'), ENT_QUOTES, 'UTF-8');

            $noktp = htmlspecialchars(request()->input('noktp'), true);
            $namalengkap = htmlspecialchars(request()->input('namalengkap'), true);
            $nohp = htmlspecialchars(request()->input('nohp'), true);
            $alamat = htmlspecialchars(request()->input('alamat'), true);
            $jenis_kelamin = htmlspecialchars(request()->input('jenis_kelamin'), true);

            if (Pembayaran::where('id', $pembayaran_id)->exists()) {
                try {
                    DB::beginTransaction();
                    $model_pembayaran = Pembayaran::where('id', $pembayaran_id)->first();
                    $penyewa = Penyewa::where('id', $model_pembayaran->penyewa_id)->first();

                    $tanggalmasuk = htmlspecialchars(request()->input('tanggalmasuk'), true);
                    $tanggalmasuk_format = Carbon::parse($tanggalmasuk)->format('Y-m-d H:i');

                    if (stripos($model_pembayaran->jenissewa, 'Harian') !== false) {
                        $tenggatwaktu = $this->check_out($tanggalmasuk_format);
                    } elseif (stripos($model_pembayaran->jenissewa, 'Mingguan / 7 Hari') !== false) {
                        $tenggatwaktu = Carbon::parse($this->check_out($tanggalmasuk_format))->addWeek();
                    } elseif (stripos($model_pembayaran->jenissewa, 'Mingguan / (14 Hari)') !== false) {
                        $tenggatwaktu = Carbon::parse($this->check_out($tanggalmasuk_format))->addWeeks(2);
                    } elseif (stripos($model_pembayaran->jenissewa, 'Bulanan') !== false) {
                        $tenggatwaktu = Carbon::parse($this->check_out($tanggalmasuk_format))->addMonth();
                    }

                    if ($model_pembayaran->mitra_id == 3) {
                        // Hapus file KTP lama jika ada
                        if (file_exists('img/ktp/asrama/' . $penyewa->fotoktp)) {
                            unlink('img/ktp/asrama/' . $penyewa->fotoktp);
                        }

                        $fotoktp = "penyewa" . "-" . $penyewa->id . "." .  request()->file('fotoktp')->getClientOriginalExtension();
                        $file = request()->file('fotoktp');
                        $tujuan_upload = 'img/ktp/asrama';
                        $file->move($tujuan_upload, $fotoktp);
                    } else {
                        // Hapus file KTP lama jika ada
                        if (file_exists('img/ktp/umum/' . $penyewa->fotoktp)) {
                            unlink('img/ktp/umum/' . $penyewa->fotoktp);
                        }

                        $fotoktp = "penyewa" . "-" . $penyewa->id . "." .  request()->file('fotoktp')->getClientOriginalExtension();
                        $file = request()->file('fotoktp');
                        $tujuan_upload = 'img/ktp/umum';
                        $file->move($tujuan_upload, $fotoktp);
                    }

                    Penyewa::where('id', $penyewa->id)->update([
                        'namalengkap' => $namalengkap,
                        'noktp' => $noktp,
                        'nohp' => $nohp,
                        'jenis_kelamin' => $jenis_kelamin,
                        'alamat' => $alamat,
                        'fotoktp' => $fotoktp,
                        'status' => 1,
                        'updated_at' => date("Y-m-d H:i:s"),
                    ]);

                    $update = Pembayaran::where('id', $model_pembayaran->id)->update([
                        'tanggal_masuk' => $tanggalmasuk_format,
                        'tanggal_keluar' => $tenggatwaktu,
                        'status' => 1,
                        'operator_id' => auth()->user()->id,
                        'updated_at' => date("Y-m-d H:i:s"),
                    ]);

                    if ($update) {
                        Lokasi::where('id', $model_pembayaran->lokasi_id)->increment('jumlah_penyewa');
                        Lokasi::where('id', $model_pembayaran->lokasi_id)->update([
                            'status' => 1,
                            'operator_id' => auth()->user()->id,
                            'updated_at' => date("Y-m-d H:i:s"),
                        ]);

                        DB::commit();
                        $response = [
                            'status' => 200,
                            'message' => 'success',
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
                    'status' => 400,
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
