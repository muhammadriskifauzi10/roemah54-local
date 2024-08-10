<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Harga;
use App\Models\Lokasi;
use App\Models\Lantai;
use App\Models\Pembayaran;
use App\Models\Penyewa;
use App\Models\Tipekamar;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KamarController extends Controller
{
    public function index()
    {
        $data = [
            'judul' => 'Kamar'
        ];

        return view('contents.dashboard.kamar.main', $data);
    }
    // Ajax Request
    public function datatablekamar()
    {
        $kamar = Lokasi::where('jenisruangan_id', 2)->orderby('lantai_id', 'ASC')->orderby('nomor_kamar', 'ASC')->get();

        $output = [];
        $no = 1;
        foreach ($kamar as $row) {
            if ($row->status == 1) {
                $status = "<strong class='badge bg-success fw-bold'>Terisi</strong>";
            } elseif ($row->status == 2) {
                $status = "<strong class='badge bg-warning text-light fw-bold'>Booking</strong>";
            } else {
                $status = "<strong class='badge bg-danger fw-bold'>Belum Terisi</strong>";
            }

            $aksi = '<div class="d-flex align-items-center justify-content-center gap-1">
                        <button type="button" class="btn btn-warning text-light fw-bold"
                        data-edit="' . $row->id . '" onclick="openModalEditKamar(this)">Edit</button>
                   </div>';

            $output[] = [
                'nomor' => '<strong>' . $no++ . '</strong>',
                'lantai' => $row->lantais->namalantai,
                'nomor_kamar' => $row->nomor_kamar,
                'tipe_kamar' => $row->tipekamars->tipekamar,
                'token_listrik' => $row->token_listrik,
                'status' => $status,
                'aksi' => $aksi,
            ];
        }

        return response()->json([
            'data' => $output
        ]);
    }
    public function getmodalkamar()
    {
        if (request()->ajax()) {
            $optionlantai = [];
            $optiontipekamar = [];
            foreach (Lantai::all() as $row) {
                $optionlantai[] = ' <option value="' . $row->id . '">' . $row->namalantai . '</option>';
            }

            $kamar = Harga::select('t2.id', 't2.tipekamar')
                ->join('tipekamars as t2', 'hargas.tipekamar_id', '=', 't2.id')
                ->distinct()
                ->get();

            foreach ($kamar as $row) {
                $optiontipekamar[] = ' <option value="' . $row->id . '">' . $row->tipekamar . '</option>';
            }


            $dataHTML = '
            <form class="modal-content" onsubmit="requestKamar(event)" autocomplete="off">
                <input type="hidden" name="__token" value="' . request()->input('token') . '" id="token">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="universalModalLabel">Tambah Kamar</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="lantai" class="form-label fw-bold">Lantai <sup class="text-danger">*</sup></label>
                        <select class="form-select form-select-2"
                            name="lantai" id="lantai" style="width: 100%;">
                            <option>Pilih Lantai</option>
                            ' . implode(" ", $optionlantai) . '
                        </select>
                        <span class="text-danger" id="errorLantai"></span>
                    </div>
                    <div class="mb-3">
                        <label for="tipekamar" class="form-label fw-bold">Tipe Kamar <sup class="text-danger">*</sup></label>
                        <select class="form-select form-select-2"
                            name="tipekamar" id="tipekamar" style="width: 100%;">
                            <option>Pilih Tipe Kamar</option>
                            ' . implode(" ", $optiontipekamar) . '
                        </select>
                        <span class="text-danger" id="errorTipeKamar"></span>
                    </div>
                    <div>
                        <label for="token_listrik" class="form-label fw-bold">Token Listrik <sup class="text-danger">*</sup></label>
                        <input type="text" class="form-control"
                            name="token_listrik" id="token_listrik" placeholder="Masukkan token listrik">
                        <span class="text-danger" id="errorTokenListrik"></span>
                    </div>
                </div>
                <div class="modal-footer">
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

        return response()->json($response);
    }
    public function getmodaleditkamar()
    {
        if (request()->ajax()) {
            $kamar_id = htmlspecialchars(request()->input('kamar_id'), ENT_QUOTES, 'UTF-8');
            if (Lokasi::where('id', $kamar_id)->exists()) {
                $kamar = Lokasi::where("id", $kamar_id)->first();
                $optiontipekamar = [];
                foreach (Tipekamar::all() as $row) {
                    $selected = ($kamar->tipekamar_id == $row->id) ? "selected" : "";
                    $optiontipekamar[] = '<option value="' . $row->id . '" ' . $selected . '>' . $row->tipekamar . '</option>';
                }

                $dataHTML = '
                <form class="modal-content" onsubmit="requestEditKamar(event)" autocomplete="off">
                    <input type="hidden" name="__token" value="' . request()->input('token') . '" id="token">
                    <input type="hidden" name="kamar_id" id="kamar_id" value="' . $kamar->id . '">
                    <input type="hidden" name="token_listrik_edit" id="token_listrik_edit" value="' . $kamar->token_listrik . '">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="universalModalLabel">Edit Kamar</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="tipekamar_id" class="form-label fw-bold">Tipe Kamar</label>
                            <select class="form-select form-select-2"
                                name="tipekamar" id="tipekamar_id" style="width: 100%;">
                                <option>Pilih Tipe Kamar</option>
                                ' . implode(" ", $optiontipekamar) . '
                            </select>
                            <span class="text-danger" id="errorTipeKamar"></span>
                        </div>
                        <div>
                            <label for="token_listrik" class="form-label fw-bold">Token Listrik <sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control"
                                name="token_listrik" id="token_listrik" placeholder="Masukkan token listrik" value="' . $kamar->token_listrik . '">
                            <span class="text-danger" id="errorTokenListrik"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
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
    public function create()
    {
        if (request()->ajax()) {
            try {
                $lantai = htmlspecialchars(request()->input('lantai'), ENT_QUOTES, 'UTF-8');
                $tipekamar = htmlspecialchars(request()->input('tipekamar'), ENT_QUOTES, 'UTF-8');
                $token_listrik = htmlspecialchars(request()->input('token_listrik'), ENT_QUOTES, 'UTF-8');

                $validator = Validator::make(request()->all(), [
                    'lantai' => [
                        function ($attribute, $value, $fail) {
                            if (!Lantai::where('id', (int)$value)->exists()) {
                                $fail('Kolom ini wajib dipilih');
                            }
                        },
                    ],
                    'tipekamar' => [
                        function ($attribute, $value, $fail) {
                            if (!Tipekamar::where('id', (int)$value)->exists()) {
                                $fail('Kolom ini wajib dipilih');
                            }
                        },
                    ],
                    'token_listrik' => ['required', 'unique:lokasis,token_listrik']
                ], [
                    'token_listrik.required' => 'Kolom ini wajib diisi',
                    'token_listrik.unique' => 'Kolom ini sudah terdaftar',
                ]);

                if ($validator->fails()) {
                    $response = [
                        'status' => 400,
                        'message' => 'opps',
                        'dataError' => $validator->errors()
                    ];

                    return response()->json($response);
                }

                if (Lokasi::where('jenisruangan_id', 2)->get()->count() > 0) {
                    $nomor_kamar =  Lokasi::where('jenisruangan_id', 2)->latest()->first()->nomor_kamar + 1;
                } else {
                    $nomor_kamar = 1;
                }

                Lokasi::create([
                    'jenisruangan_id' => 2,
                    'lantai_id' => $lantai,
                    'nomor_kamar' => $nomor_kamar,
                    'tipekamar_id' => $tipekamar,
                    'token_listrik' => $token_listrik,
                    'operator_id' => auth()->user()->id
                ]);

                // server
                DB::connection("mysqldua")->table("lokasis")->insert([
                    'lantai_id' => $lantai,
                    'nomor_kamar' => $nomor_kamar,
                    'tipekamar_id' => $tipekamar,
                    'token_listrik' => $token_listrik,
                    'operator_id' => auth()->user()->id,
                    'created_at' => date("Y-m-d H:i:s"),
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
        }
    }
    public function edittipekamar()
    {
        if (request()->ajax()) {
            try {
                DB::beginTransaction();
                $kamar_id = htmlspecialchars(request()->input('kamar_id'), ENT_QUOTES, 'UTF-8');
                $tipekamar_id = htmlspecialchars(request()->input('tipekamar_id'), ENT_QUOTES, 'UTF-8');
                $token_listrik = htmlspecialchars(request()->input('token_listrik'), ENT_QUOTES, 'UTF-8');
                $token_listrik_edit = htmlspecialchars(request()->input('token_listrik_edit'), ENT_QUOTES, 'UTF-8');

                if ($token_listrik != $token_listrik_edit) {
                    $ruletokenlistrik = 'unique:kamars,token_listrik';
                } else {
                    $ruletokenlistrik = '';
                }

                if (Lokasi::where('id', $kamar_id)->exists()) {
                    $validator = Validator::make(request()->all(), [
                        'tipekamar_id' => [
                            function ($attribute, $value, $fail) {
                                if (!Tipekamar::where('id', (int)$value)->exists()) {
                                    $fail('Kolom ini wajib dipilih');
                                }
                            },
                        ],
                        'token_listrik' => ['required', $ruletokenlistrik]
                    ], [
                        'token_listrik.required' => 'Kolom ini wajib diisi',
                        'token_listrik.unique' => 'Kolom ini sudah terdaftar',
                    ]);

                    if ($validator->fails()) {
                        $response = [
                            'status' => 400,
                            'message' => 'opps',
                            'dataError' => $validator->errors()
                        ];

                        return response()->json($response);
                    }

                    Lokasi::where('id', $kamar_id)->update([
                        'tipekamar_id' => $tipekamar_id,
                        'token_listrik' => $token_listrik,
                        'operator_id' => auth()->user()->id,
                        'updated_at' => date("Y-m-d H:i:s"),
                    ]);

                    // server
                    DB::connection("mysqldua")->table("lokasis")->where('id', $kamar_id)->update([
                        'tipekamar_id' => $tipekamar_id,
                        'token_listrik' => $token_listrik,
                        'operator_id' => auth()->user()->id,
                        'updated_at' => date("Y-m-d H:i:s"),
                    ]);

                    $response = [
                        'status' => 200,
                        'message' => 'success',
                    ];

                    DB::commit();
                    return response()->json($response);
                }
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
    public function informasitamu($id)
    {
        $id = decrypt($id);

        if (!Lokasi::where('id', $id)->exists()) {
            abort(404);
        }

        $kamar = Lokasi::where('id', $id)->first();

        $data = [
            'judul' => 'Informasi Tamu',
            'kamar' => $kamar
        ];

        return view('contents.dashboard.kamar.informasitamu', $data);
    }
    public function datatableinformasitamu()
    {
        $minDate = request()->input('minDate');
        $maxDate = request()->input('maxDate');
        $kamar_id = request()->input('kamar_id');

        $penyewaankamar = Pembayaran::where('lokasi_id', $kamar_id)
            ->where('tagih_id', 1)
            ->orderBy('tanggal_masuk', 'DESC')
            ->get();

        $output = [];
        $no = 1;
        foreach ($penyewaankamar as $row) {
            // if ($row->tanggal_pembayaran && in_array($row->status_pembayaran, ['completed', 'pending'])) {
            //     $cetak = '
            //         <div class="d-flex align-items-center justify-content-center gap-1">
            //             <a href="' . route('penyewaankamar.cetakkwitansi', encrypt($row->id)) . '" class="btn btn-success d-flex align-items-center justify-content-center gap-1" style="width: 160px;" target="_blank">
            //                 <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16">
            //                     <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/>
            //                     <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1"/>
            //                 </svg>
            //                 Cetak Kwitansi
            //             </a>
            //         </div>';
            // } else {
            //     $cetak = '-';
            // }

            if ($row->penyewas->status == 1) {
                $status_penyewa = "<span class='badge bg-success'>Aktif</span>";
            } else {
                if ($row->status_pembayaran == "failed") {
                    $status_penyewa = "<span class='badge bg-danger text-light'>Tamu Dibatalkan</span>";
                } else {
                    $status_penyewa = "<span class='badge bg-danger text-light'>Tamu Sudah Pulang</span>";
                }
            }

            if ($row->status_pembayaran == "completed") {
                $status_pembayaran = "<span class='badge bg-success'>Lunas</span>";
            } elseif ($row->status_pembayaran == "pending") {
                $status_pembayaran = "<span class='badge bg-warning text-light'>Belum Lunas</span>";
            } elseif ($row->status_pembayaran == "failed") {
                $status_pembayaran = "<span class='badge bg-danger'>Dibatalkan</span>";
            }

            if ($row->penyewas->status == 1 && $row->status_pembayaran == "pending") {
                $aksi = '
                    <div class="d-flex align-items-center justify-content-center gap-1">
                        <button type="button" class="btn btn-success fw-bold d-flex align-items-center justify-content-center gap-1" onclick="openModalBayarKamar(event, ' . $row->id . ')" style="width: 180px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-credit-card" viewBox="0 0 16 16">
                                <path
                                    d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v1h14V4a1 1 0 0 0-1-1zm13 4H1v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1z" />
                                <path d="M2 10a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z" />
                            </svg>
                        Bayar</button>
                        <button type="button" class="btn btn-danger text-light fw-bold" onclick="requestPulangkanTamu(' . $row->penyewas->id . ')" style="width: 180px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-person-raised-hand" viewBox="0 0 16 16">
                                <path
                                    d="M6 6.207v9.043a.75.75 0 0 0 1.5 0V10.5a.5.5 0 0 1 1 0v4.75a.75.75 0 0 0 1.5 0v-8.5a.25.25 0 1 1 .5 0v2.5a.75.75 0 0 0 1.5 0V6.5a3 3 0 0 0-3-3H6.236a1 1 0 0 1-.447-.106l-.33-.165A.83.83 0 0 1 5 2.488V.75a.75.75 0 0 0-1.5 0v2.083c0 .715.404 1.37 1.044 1.689L5.5 5c.32.32.5.754.5 1.207" />
                                <path d="M8 3a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3" />
                            </svg>
                            Pulangkan Tamu
                        </button>
                    </div>
                ';
            } else {
                $aksi = '-';
            }


            $output[] = [
                'nomor' => '<strong>' . $no++ . '</strong>',
                'status_penyewa' => $status_penyewa,
                'tanggal_masuk' => Carbon::parse($row->tanggal_masuk)->format("Y-m-d H:i:s"),
                'tanggal_keluar' => Carbon::parse($row->tanggal_keluar)->format("Y-m-d H:i:s"),
                'nama_penyewa' => $row->penyewas->namalengkap,
                'mitra' => $row->mitras->mitra,
                'jenissewa' => $row->jenissewa,
                'jumlah_pembayaran' => $row->jumlah_pembayaran ? "RP. " . number_format($row->jumlah_pembayaran, '0', '.', '.') : "RP. 0",
                'diskon' => intval($row->diskon) . " %",
                'potongan_harga' => $row->potongan_harga ? "RP. " . number_format($row->potongan_harga, '0', '.', '.') : "RP. 0",
                'total_bayar' => $row->total_bayar ? "RP. " . number_format($row->total_bayar, '0', '.', '.') : "RP. 0",
                'tanggal_pembayaran' => $row->tanggal_pembayaran ? Carbon::parse($row->tanggal_pembayaran)->format("Y-m-d H:i:s") : "-",
                'kurang_bayar' => $row->kurang_bayar ? "RP. " . number_format($row->kurang_bayar, '0', '.', '.') : "RP. 0",
                'status_pembayaran' => $status_pembayaran,
                'aksi' => $aksi,
            ];
        }

        return response()->json([
            'data' => $output
        ]);
    }
    // baru
    public function pulangkantamu()
    {
        if (request()->ajax()) {
            $penyewa_id = htmlspecialchars(request()->input('penyewa_id'), ENT_QUOTES, 'UTF-8');
            if (Penyewa::where('id', $penyewa_id)->exists()) {
                try {
                    DB::beginTransaction();

                    Penyewa::where('id', $penyewa_id)->update([
                        'status' => 0,
                        'operator_id' => auth()->user()->id,
                        'updated_at' => date("Y-m-d H:i:s"),
                    ]);

                    // server
                    DB::connection("mysqldua")->table("penyewas")->where('id', $penyewa_id)->update([
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
                        'message' => 'error',
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
    // baru
    public function kosongkankamar()
    {
        if (request()->ajax()) {
            $kamar_id = htmlspecialchars(request()->input('kamar_id'), ENT_QUOTES, 'UTF-8');
            if (Lokasi::where('id', $kamar_id)->exists()) {
                try {
                    DB::beginTransaction();

                    Lokasi::where('id', $kamar_id)->update([
                        'status' => 0,
                        'operator_id' => auth()->user()->id,
                        'updated_at' => date("Y-m-d H:i:s"),
                    ]);

                    // server
                    DB::connection("mysqldua")->table("lokasis")->where('id', $kamar_id)->update([
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
                        'message' => 'error',
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
}
