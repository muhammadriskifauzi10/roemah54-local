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
                          data-edit="' . $row->id . '" onclick="openModalEditKamar(this)">
                              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pen" viewBox="0 0 16 16">
                                  <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z"/>
                              </svg>
                              Edit Kamar
                          </button>
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
}
