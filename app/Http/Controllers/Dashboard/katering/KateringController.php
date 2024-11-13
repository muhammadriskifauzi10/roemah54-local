<?php

namespace App\Http\Controllers\Dashboard\katering;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Katering;
use App\Models\Lokasi;
use App\Models\Lantai;
use App\Models\Pembayaran;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KateringController extends Controller
{
    public function index()
    {
        $data = [
            'judul' => 'Katering',
        ];

        return view('contents.dashboard.katering.data', $data);
    }

    public function kateringData(Request $request)
    {

        if($request->date1 != '' && $request->date2 != '')
        {
            $date1 = date('Y-m-d', strtotime($request->date1));
            $date2 = date('Y-m-d', strtotime($request->date2));
        }else{
            $date1 = "";
            $date2 = "";
        }

        $kamar = Katering::when($date1!='', function ($query) use ($date1){
                return $query->whereDate('DARI', '>=', $date1);
            })
            ->when($date2!='', function ($query) use ($date2){
                return $query->whereDate('DARI', '<=', $date2);
            })
            ->orderby('dari', 'DESC')
            ->get();

        $output = [];
        $no = 1;
        foreach ($kamar as $row) {
            if ($row->jenis_order == 'KATERING') {
                $jenis_order = "<strong class='badge bg-success fw-bold'>KATERING</strong>";
            } elseif ($row->jenis_order == 'MAKANAN_TAMU') {
                $jenis_order = "<strong class='badge bg-warning text-light fw-bold'>MAKANAN TAMU</strong>";
            } 

            $getLok = Lokasi::where('id',$row->lokasi_id)->first();
            if ($getLok) {
                $lantai = Lantai::getName($getLok->lantai_id);
                $nomor_kamar = $getLok->nomor_kamar;

                $lok = $lantai. " / Kamar" .$nomor_kamar;
            }else{
                $lok = "-";
            }

            $edit = '
                <button type="button" class="btn btn-warning text-light fw-bold d-flex align-items-center justify-content-center gap-1" data-edit="' . $row->id . '" onclick="openModalEdit(this)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pen-fill" viewBox="0 0 16 16">
                        <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001"/>
                    </svg>
                    Edit
                </button>
                ';

            $output[] = [
                'nomor' => "<strong>" . $no++ . "</strong>",
                'dari' => date("d-m-Y",strtotime($row->dari)),
                'sampai' => date("d-m-Y",strtotime($row->sampai)),
                'jenis_order' => $jenis_order,
                'jumlah_porsi' => $row->jumlah_porsi,
                'lokasi_id' => $lok,
                'aksi' => $edit,
            ];
        }

        return response()->json([
            'data' => $output
        ]);
    }

    public function getmodalAdd()
    {
        if (request()->ajax()) {
            $optiontipekamar = [];

            $kamar = Lokasi::orderby('lantai_id', 'ASC')
                ->orderby('nomor_kamar', 'ASC')
                ->get();

            foreach ($kamar as $row) {
                $optiontipekamar[] = ' <option value="' . $row->id . '">'. Lantai::getName($row->lantai_id) .' / Kamar '. $row->nomor_kamar . '</option>';
            }


            $dataHTML = '
            <form class="modal-content" onsubmit="requestPost(event)" autocomplete="off">
                <input type="hidden" name="__token" value="' . request()->input('token') . '" id="token">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="universalModalLabel">Tambah Data</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="dari" class="form-label fw-bold">Dari <sup class="text-danger">*</sup></label>
                        <input type="date" class="form-control" id="dari" value="'. date('Y-m-d') .'">
                        <span class="text-danger" id="errorDari"></span>
                    </div>
                    <div class="mb-3">
                        <label for="sampai" class="form-label fw-bold">Sampai <sup class="text-danger">*</sup></label>
                        <input type="date" class="form-control" id="sampai" value="'. date('Y-m-d') .'">
                        <span class="text-danger" id="errorSampai"></span>
                    </div>
                    <div class="mb-3">
                        <label for="jenis_order" class="form-label fw-bold">Jenis Order <sup class="text-danger">*</sup></label>
                        <select class="form-select form-select-2"
                            name="jenis_order" id="jenis_order" style="width: 100%;">
                            <option>Pilih Jenis Orderan</option>
                            <option value="KATERING">KATERING</option>
                            <option value="MAKANAN_TAMU">MAKANAN TAMU</option>
                        </select>
                        <span class="text-danger" id="errorJenis"></span>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah_porsi" class="form-label fw-bold">Jumlah Porsi <sup class="text-danger">*</sup></label>
                        <input type="text" class="form-control"
                            name="jumlah_porsi" id="jumlah_porsi" placeholder="Masukkan token listrik" value="">
                        <span class="text-danger" id="errorJumlah"></span>
                    </div>
                    <div class="mb-3" id="lokasiDisplay" style="display:none;">
                        <label for="lokasi_id" class="form-label fw-bold">Pilih Lokasi Kamar <sup class="text-danger">*</sup></label>
                        <select class="form-select form-select-2"
                            name="lokasi_id" id="lokasi_id" style="width: 100%;">
                            <option>Pilih Lokasi Kamar</option>
                            ' . implode(" ", $optiontipekamar) . '
                        </select>
                        <span class="text-danger" id="errorKamar"></span>
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

        return response()->json($response);
    }


    public function kateringAdd()
    {
        if (request()->ajax()) {
            try {
                $dari = htmlspecialchars(request()->input('dari'), ENT_QUOTES, 'UTF-8');
                $sampai = htmlspecialchars(request()->input('sampai'), ENT_QUOTES, 'UTF-8');
                $jenis_order = htmlspecialchars(request()->input('jenis_order'), ENT_QUOTES, 'UTF-8');
                $jumlah_porsi = htmlspecialchars(request()->input('jumlah_porsi'), ENT_QUOTES, 'UTF-8');
                $lokasi_id = htmlspecialchars(request()->input('lokasi_id'), ENT_QUOTES, 'UTF-8');

                $validator = Validator::make(request()->all(), [
                    'dari' => 'required',
                    'sampai' => 'required',
                    'jenis_order' => 'required',
                    'jumlah_porsi' => 'required',
                ], [
                    'dari.required' => 'Kolom ini wajib diisi',
                    'sampai.required' => 'Kolom ini wajib diisi',
                    'jenis_order.required' => 'Kolom ini wajib diisi',
                    'jumlah_porsi.required' => 'Kolom ini wajib diisi',
                ]);

                if ($validator->fails()) {
                    $response = [
                        'status' => 422,
                        'message' => 'validation',
                        'dataError' => $validator->errors()
                    ];

                    return response()->json($response);
                }

                $add = new Katering();
                $add->dari = date("Y-m-d");
                $add->sampai = date("Y-m-d");
                $add->jenis_order = $jenis_order;
                $add->jumlah_porsi = $jumlah_porsi;
                $add->lokasi_id = $lokasi_id;
                $add->operator_id = auth()->user()->id;
                $add->save();

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

    public function getmodalEdit()
    {
        if (request()->ajax()) {
            $katering_id = htmlspecialchars(request()->input('katering_id'), ENT_QUOTES, 'UTF-8');
            if (Katering::where('id', $katering_id)->exists()) {
                $dataKat = Katering::where('id', $katering_id)->first();

                $optiontipekamar = [];
                $kamar = Lokasi::orderby('lantai_id', 'ASC')
                    ->orderby('nomor_kamar', 'ASC')
                    ->get();

                foreach ($kamar as $row) {
                    $optiontipekamar[] = ' <option value="' . $row->id . '" '.(($dataKat->lokasi_id == $row->id) ? "selected" : "" ).'>'. Lantai::getName($row->lantai_id) .' / Kamar '. $row->nomor_kamar . '</option>';
                }

                $dataHTML = '
                <form class="modal-content" onsubmit="requestEdit(event)" autocomplete="off">
                    <input type="hidden" name="__token" value="' . request()->input('token') . '" id="token">
                    <input type="hidden" name="katering_id" id="katering_id" value="' . $katering_id . '">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="universalModalLabel">Edit Kamar</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="dari" class="form-label fw-bold">Dari <sup class="text-danger">*</sup></label>
                            <input type="date" class="form-control" id="dari" value="'. $dataKat->dari .'">
                            <span class="text-danger" id="errorDari"></span>
                        </div>
                        <div class="mb-3">
                            <label for="sampai" class="form-label fw-bold">Sampai <sup class="text-danger">*</sup></label>
                            <input type="date" class="form-control" id="sampai" value="'. $dataKat->dari .'">
                            <span class="text-danger" id="errorSampai"></span>
                        </div>
                        <div class="mb-3">
                            <label for="jenis_order" class="form-label fw-bold">Jenis Order <sup class="text-danger">*</sup></label>
                            <select class="form-select form-select-2"
                                name="jenis_order" id="jenis_order" style="width: 100%;">
                                <option>Pilih Jenis Orderan</option>
                                <option value="KATERING" '. (($dataKat->jenis_order == "KATERING") ? "selected" : "" ) .'>KATERING</option>
                                <option value="MAKANAN_TAMU" '. (($dataKat->jenis_order == "MAKANAN_TAMU") ? "selected" : "" ) .'>MAKANAN TAMU</option>
                            </select>
                            <span class="text-danger" id="errorJenis"></span>
                        </div>
                        <div class="mb-3">
                            <label for="jumlah_porsi" class="form-label fw-bold">Jumlah Porsi <sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control"
                                name="jumlah_porsi" id="jumlah_porsi" placeholder="Masukkan token listrik" value="'. $dataKat->jumlah_porsi .'">
                            <span class="text-danger" id="errorJumlah"></span>
                        </div>
                        <div class="mb-3" id="lokasiDisplay" style="display:none;">
                            <label for="lokasi_id" class="form-label fw-bold">Pilih Lokasi Kamar <sup class="text-danger">*</sup></label>
                            <select class="form-select form-select-2"
                                name="lokasi_id" id="lokasi_id" style="width: 100%;">
                                <option>Pilih Lokasi Kamar</option>
                                ' . implode(" ", $optiontipekamar) . '
                            </select>
                            <span class="text-danger" id="errorKamar"></span>
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

    public function kateringEdit()
    {
        if (request()->ajax()) {
            try {
                $katering_id = htmlspecialchars(request()->input('katering_id'), ENT_QUOTES, 'UTF-8');
                $dari = htmlspecialchars(request()->input('dari'), ENT_QUOTES, 'UTF-8');
                $sampai = htmlspecialchars(request()->input('sampai'), ENT_QUOTES, 'UTF-8');
                $jenis_order = htmlspecialchars(request()->input('jenis_order'), ENT_QUOTES, 'UTF-8');
                $jumlah_porsi = htmlspecialchars(request()->input('jumlah_porsi'), ENT_QUOTES, 'UTF-8');
                $lokasi_id = htmlspecialchars(request()->input('lokasi_id'), ENT_QUOTES, 'UTF-8');

                $validator = Validator::make(request()->all(), [
                    'dari' => 'required',
                    'sampai' => 'required',
                    'jenis_order' => 'required',
                    'jumlah_porsi' => 'required',
                ], [
                    'dari.required' => 'Kolom ini wajib diisi',
                    'sampai.required' => 'Kolom ini wajib diisi',
                    'jenis_order.required' => 'Kolom ini wajib diisi',
                    'jumlah_porsi.required' => 'Kolom ini wajib diisi',
                ]);

                if ($validator->fails()) {
                    $response = [
                        'status' => 422,
                        'message' => 'validation',
                        'dataError' => $validator->errors()
                    ];

                    return response()->json($response);
                }

                if (Katering::where('id', $katering_id)->exists()) {
                    $edit = Katering::where('id', $katering_id)->first();
                    $edit->dari = date("Y-m-d");
                    $edit->sampai = date("Y-m-d");
                    $edit->jenis_order = $jenis_order;
                    $edit->jumlah_porsi = $jumlah_porsi;
                    $edit->lokasi_id = ($jenis_order == "KATERING") ? "" : $lokasi_id;
                    $edit->operator_id = auth()->user()->id;
                    $edit->save();

                    $response = [
                        'status' => 200,
                        'message' => 'success',
                    ];

                    DB::commit();
                }else{
                    $response = [
                        'status' => 400,
                        'message' => 'opps',
                    ];
                }

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
}