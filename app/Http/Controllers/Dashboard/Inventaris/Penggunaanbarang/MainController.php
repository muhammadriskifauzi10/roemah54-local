<?php

namespace App\Http\Controllers\Dashboard\Inventaris\Penggunaanbarang;

use App\Http\Controllers\Controller;
use App\Models\Baranginventaris;
use App\Models\Jenisruangan;
use App\Models\Kategoribaranginventaris;
use App\Models\Logbaranginventaris;
use App\Models\Logmutasibaranginventaris;
use App\Models\Lokasi;
use App\Models\Penggunaanbaranginventaris;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MainController extends Controller
{
    public function index()
    {
        $kategori = Kategoribaranginventaris::all();
        $lokasi = Lokasi::orderBy('jenisruangan_id', 'ASC')->get();

        $data = [
            'judul' => 'Penggunaan barang Inventaris',
            'kategori' => $kategori,
            'lokasi' => $lokasi
        ];

        return view('contents.dashboard.inventaris.penggunaanbarang.main', $data);
    }
    public function datatablepenggunaanbarang()
    {
        $min = request()->input('minDate');
        $max = request()->input('maxDate');
        $kategori = request()->input('kategori');
        $lokasi = request()->input('lokasi');

        $kategoriExists = Kategoribaranginventaris::where('id', (int)$kategori)->exists();
        $lokasiExists = Lokasi::where('id', (int)$lokasi)->exists();

        $penggunaanbaranginventaris = Penggunaanbaranginventaris::join('baranginventaris as bi', 'penggunaanbaranginventaris.baranginventaris_id', '=', 'bi.id')
            ->join('lokasis as l', 'penggunaanbaranginventaris.lokasi_id', '=', 'l.id')
            ->select(
                'penggunaanbaranginventaris.id',
                'penggunaanbaranginventaris.no_barcode',
                'penggunaanbaranginventaris.lokasi_id',
                'penggunaanbaranginventaris.jumlah',
                'penggunaanbaranginventaris.created_at',
                'bi.kategoribaranginventaris_id',
                'bi.nama',
                'l.jenisruangan_id',
            )
            ->when($kategoriExists, function ($query) use ($kategori) {
                $query->where('bi.kategoribaranginventaris_id', (int)$kategori);
            })
            ->when($lokasiExists, function ($query) use ($lokasi) {
                $query->where('penggunaanbaranginventaris.lokasi_id', (int)$lokasi);
            })
            ->when($min && $max, function ($query) use ($min, $max) {
                $query->whereDate('penggunaanbaranginventaris.created_at', '>=', $min)
                    ->whereDate('penggunaanbaranginventaris.created_at', '<=', $max);
            })
            ->orderBy('penggunaanbaranginventaris.updated_at', 'DESC')
            ->get();

        $output = [];
        $no = 1;
        foreach ($penggunaanbaranginventaris as $row) {
            if ($row->jenisruangan_id == 2) {
                $lokasi = 'Kamar Nomor ' . $row->lokasis->nomor_kamar;
            } else {
                $lokasi = Jenisruangan::where('id', $row->jenisruangan_id)->first()->nama;
            }

            $aksi = '
            <div class="d-flex align-items-center justify-content-center gap-1">
                <button type="button" class="btn btn-primary text-light fw-bold"
                data-mutasi="' . $row->id . '" onclick="openModalMutasiPenggunaanBarang(this)">Mutasi</button>
                <button type="button" class="btn btn-danger text-light fw-bold"
                data-hapus="' . $row->id . '" onclick="requestHapusPenggunaanBarang(this)">Hapus</button>
            </div>';

            $output[] = [
                'nomor' => '<strong>' . $no++ . '</strong>',
                'tanggal' => Carbon::parse($row->created_at)->format('Y-m-d H:i'),
                'no_barcode' => $row->no_barcode ? $row->no_barcode : '-',
                'kategori' => $row->kategoris->nama,
                'nama_barang' => $row->nama,
                'lokasi' => $lokasi,
                'jumlah' => $row->jumlah,
                'aksi' => $aksi
            ];
        }

        return response()->json([
            'data' => $output
        ]);
    }
    public function getmodalpenggunaanbarang()
    {
        if (request()->ajax()) {
            $optionbaranginventaris = [];
            $optionlokasi = [];

            foreach (BarangInventaris::whereColumn('jumlah', '>', 'jumlah_terpakai')->get() as $row) {
                $optionbaranginventaris[] = ' <option value="' . $row->id . '">' . $row->nama . ' - Tersedia ' . intval($row->jumlah) - intval($row->jumlah_terpakai) . ' Item</option>';
            }

            foreach (Lokasi::orderBy('jenisruangan_id', 'ASC')->get() as $row) {
                if ($row->jenisruangan_id == 2) {
                    $lokasi = 'Kamar Nomor ' . $row->nomor_kamar;
                } else {
                    $lokasi = Jenisruangan::where('id', $row->jenisruangan_id)->first()->nama;
                }

                $optionlokasi[] = ' <option value="' . $row->id . '">' . $lokasi . '</option>';
            }


            $dataHTML = '
            <form class="modal-content" id="formtambahpenggunaanbarang" onsubmit="requestTambahPenggunaanBarang(event)" autocomplete="off">
                <input type="hidden" name="__token" value="' . request()->input('token') . '" id="token">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="universalModalLabel">Tambah Penggunaan Barang</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="baranginventaris" class="form-label fw-bold">Barang Inventaris <sup class="text-danger">*</sup></label>
                        <select class="form-select form-select-2"
                            name="baranginventaris" id="baranginventaris" style="width: 100%;">
                            <option>Pilih Barang Inventaris</option>
                            ' . implode(" ", $optionbaranginventaris) . '
                        </select>
                        <span class="text-danger" id="errorBarangInventaris"></span>
                    </div>
                    <div class="mb-3">
                        <label for="lokasi" class="form-label fw-bold">Lokasi <sup class="text-danger">*</sup></label>
                        <select class="form-select form-select-2"
                            name="lokasi" id="lokasi" style="width: 100%;">
                            <option>Pilih Tipe Kamar</option>
                            ' . implode(" ", $optionlokasi) . '
                        </select>
                        <span class="text-danger" id="errorLokasi"></span>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah" class="form-label fw-bold">Jumlah <sup class="text-danger">*</sup></label>
                        <input type="number" class="form-control" name="jumlah" id="jumlah">
                        <span class="text-danger" id="errorJumlah"></span>
                    </div>
                    <div>
                        <label for="barcode" class="form-label fw-bold">Ada Barcode?</label>
                        <div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input" name="barcode" role="switch" id="barcode" onchange="questionBarcode()">
                        </div>
                    </div>
                    <div id="parentlabelbarcode" style="display: none">
                        <div class="mt-3">
                            <label for="labelbarcode" class="form-label fw-bold">Label Barcode <sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control" name="labelbarcode" id="labelbarcode">
                            <span class="text-danger" id="errorLabelBarcode"></span>
                        </div>
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
    public function create()
    {
        if (request()->ajax()) {
            try {
                DB::beginTransaction();

                $baranginventaris = htmlspecialchars(request()->input('baranginventaris'), ENT_QUOTES, 'UTF-8');
                $lokasi = htmlspecialchars(request()->input('lokasi'), ENT_QUOTES, 'UTF-8');
                $jumlah = htmlspecialchars(request()->input('jumlah'), ENT_QUOTES, 'UTF-8');
                $barcode = htmlspecialchars(request()->input('barcode'), ENT_QUOTES, 'UTF-8');

                if ($barcode == "on") {
                    $labelbarcode = htmlspecialchars(request()->input('labelbarcode'), ENT_QUOTES, 'UTF-8');
                    $rulelabelbarcode = "required";
                } else {
                    $labelbarcode = "";
                    $rulelabelbarcode = "";
                }

                $model_baranginventaris = Baranginventaris::where('id', (int)$baranginventaris)->first();
                $model_lokasi = Lokasi::where('id', (int)$lokasi)->first();

                if ($model_baranginventaris) {
                    $sisa_baranginventaris = intval($model_baranginventaris->jumlah) - intval($model_baranginventaris->jumlah_terpakai);

                    if (intval($jumlah) > $sisa_baranginventaris) {
                        $rulejumlah = 'max: ' . $sisa_baranginventaris;
                    } else {
                        $rulejumlah = '';
                    }
                } else {
                    $sisa_baranginventaris = 0;
                    $rulejumlah = '';
                }

                $validator = Validator::make(request()->all(), [
                    'baranginventaris' => [
                        function ($attribute, $value, $fail) {
                            if (!Baranginventaris::where('id', (int)$value)->exists()) {
                                $fail('Kolom ini wajib dipilih');
                            }
                        },
                    ],
                    'lokasi' => [
                        function ($attribute, $value, $fail) {
                            if (!Lokasi::where('id', (int)$value)->exists()) {
                                $fail('Kolom ini wajib dipilih');
                            }
                        },
                    ],
                    'jumlah' => ['required', 'numeric', 'min:1', $rulejumlah],
                    'labelbarcode' => $rulelabelbarcode
                ], [
                    'jumlah.required' => 'Kolom ini wajib diisi',
                    'jumlah.numeric' => 'Kolom ini tidak valid',
                    'jumlah.min' => 'Kolom ini wajib diisi',
                    'jumlah.max' => 'Kolom ini tidak valid',
                    'labelbarcode.required' => 'Kolom ini wajib diisi',
                ]);

                if ($validator->fails()) {
                    $response = [
                        'status' => 422,
                        'message' => 'opps',
                        'dataError' => $validator->errors()
                    ];

                    return response()->json($response);
                }

                Baranginventaris::where('id', (int)$baranginventaris)->update([
                    'jumlah_terpakai' => intval($model_baranginventaris->jumlah_terpakai) + intval($jumlah),
                    'operator_id' => auth()->user()->id,
                    'updated_at' => date("Y-m-d H:i:s"),
                ]);

                // server
                DB::connection("mysqldua")->table("baranginventaris")->where('id', (int)$baranginventaris)->update([
                    'jumlah_terpakai' => intval($model_baranginventaris->jumlah_terpakai) + intval($jumlah),
                    'operator_id' => auth()->user()->id,
                    'updated_at' => date("Y-m-d H:i:s"),
                ]);

                $model_penggunaanbaranginventaris = new Penggunaanbaranginventaris();
                $model_penggunaanbaranginventaris->baranginventaris_id = $baranginventaris;
                $model_penggunaanbaranginventaris->lokasi_id = $lokasi;
                $model_penggunaanbaranginventaris->jumlah = intval($jumlah);
                $model_penggunaanbaranginventaris->operator_id = auth()->user()->id;
                $post = $model_penggunaanbaranginventaris->save();

                // server
                DB::connection("mysqldua")->table("penggunaanbaranginventaris")->insert([
                    'baranginventaris_id' => $baranginventaris,
                    'lokasi_id' => $lokasi,
                    'jumlah' => intval($jumlah),
                    'operator_id' => auth()->user()->id,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ]);

                if ($post) {

                    // log
                    $logbaranginventaris = new Logbaranginventaris();
                    $logbaranginventaris->tanggal_log = date('Y-m-d H:i:s');
                    $logbaranginventaris->baranginventaris_id = $model_baranginventaris->id;

                    if ($barcode == "on") {
                        $text = $labelbarcode;
                        // Use explode to split the string by the first space
                        $parts = explode(' ', $text);
                        // Get the first part before the space
                        $no_barcode = Penggunaanbaranginventaris::whereNotNull('no_barcode')->where('label', Str::lower($parts[0]))->count() + 1 . '-' . Str::lower($parts[0]) . '-' . 'lokasi' . $model_lokasi->id;

                        Penggunaanbaranginventaris::where('id', (int)$model_penggunaanbaranginventaris->id)->update([
                            'no_barcode' => $no_barcode,
                            'label' => Str::lower($parts[0]),
                        ]);

                        $logbaranginventaris->no_barcode = $no_barcode;
                        $logbaranginventaris->label = Str::lower($parts[0]);

                        $server_no_barcode = $no_barcode;
                        $server_label = Str::lower($parts[0]);
                    } else {
                        $server_no_barcode = NULL;
                        $server_label = NULL;
                    }
                   
                    $logbaranginventaris->tanggal_masuk = $model_baranginventaris->tanggal_masuk;
                    $logbaranginventaris->kategoribaranginventaris_id = $model_baranginventaris->kategoribaranginventaris_id;
                    $logbaranginventaris->nama = $model_baranginventaris->nama;
                    $logbaranginventaris->jumlah = intval($jumlah);
                    $logbaranginventaris->asallokasi_id = $lokasi;
                    $logbaranginventaris->log = 3;
                    $logbaranginventaris->operator_id = auth()->user()->id;
                    $logbaranginventaris->save();

                    // server
                    DB::connection("mysqldua")->table("logbaranginventaris")->insert([
                        'tanggal_log' => date('Y-m-d H:i:s'),
                        'baranginventaris_id' => $model_baranginventaris->id,
                        'no_barcode' => $server_no_barcode,
                        'label' => $server_label,
                        'tanggal_masuk' => $model_baranginventaris->tanggal_masuk,
                        'kategoribaranginventaris_id' => $model_baranginventaris->kategoribaranginventaris_id,
                        'nama' => $model_baranginventaris->nama,
                        'jumlah' => intval($jumlah),
                        'asallokasi_id' => $lokasi,
                        'log' => 3,
                        'operator_id' => auth()->user()->id,
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s"),
                    ]);

                    $response = [
                        'status' => 200,
                        'message' => 'success',
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
                $response = [
                    'status' => 500,
                    'message' => $e->getMessage(),
                ];

                DB::rollBack();
                return response()->json($response);
            }
        }
    }
    // mutasi
    public function getmodalmutasipenggunaanbarang()
    {
        if (request()->ajax()) {
            $mutasi_id = request()->input('mutasi_id');

            if (Penggunaanbaranginventaris::where('id', (int)$mutasi_id)->exists()) {
                $model_penggunaanbarang = Penggunaanbaranginventaris::where('id', (int)$mutasi_id)->first();

                $optionlokasi = [];

                foreach (Lokasi::orderBy('jenisruangan_id', 'ASC')->get() as $row) {
                    if ($row->jenisruangan_id == 2) {
                        $lokasi = 'Kamar Nomor ' . $row->nomor_kamar;
                    } else {
                        $lokasi = Jenisruangan::where('id', $row->jenisruangan_id)->first()->nama;
                    }

                    // cek lokasi awal
                    if ($model_penggunaanbarang->lokasi_id == $row->id) {
                        $selected = 'selected';
                    } else {
                        $selected = '';
                    }

                    $optionlokasi[] = ' <option value="' . $row->id . '" ' . $selected . '>' . $lokasi . '</option>';
                }

                $dataHTML = '
                <form class="modal-content" id="formmutasipenggunaanbarang" onsubmit="requestMutasiPenggunaanBarang(event)" autocomplete="off">
                    <input type="hidden" name="__token" value="' . request()->input('token') . '" id="token">
                    <input type="hidden" name="penggunaanbarang_id" value="' . $model_penggunaanbarang->id . '" id="token">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="universalModalLabel">Mutasi Penggunaan Barang</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div>
                            <label for="tujuanmutasi" class="form-label fw-bold">Tujuan Mutasi <sup class="text-danger">*</sup></label>
                            <select class="form-select form-select-2"
                                name="tujuanmutasi" id="tujuanmutasi" style="width: 100%;">
                                <option>Pilih Tujuan Mutasi</option>
                                ' . implode(" ", $optionlokasi) . '
                            </select>
                            <span class="text-danger" id="errorTujuanMutasi"></span>
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
        } else {
            $response = [
                'status' => 400,
                'message' => 'opps',
            ];
        }

        return response()->json($response);
    }
    public function mutasipenggunaanbarang()
    {
        if (request()->ajax()) {
            try {
                DB::beginTransaction();

                $penggunaanbarang_id = htmlspecialchars(request()->input('penggunaanbarang_id'), ENT_QUOTES, 'UTF-8');
                $tujuanmutasi = htmlspecialchars(request()->input('tujuanmutasi'), ENT_QUOTES, 'UTF-8');

                $model_penggunaanbaranginventaris = Penggunaanbaranginventaris::where('id', (int)$penggunaanbarang_id)->first();
                $model_baranginventaris = Baranginventaris::where('id', (int)$model_penggunaanbaranginventaris->baranginventaris_id)->first();

                if ($tujuanmutasi == $model_penggunaanbaranginventaris->lokasi_id) {
                    $ruletujuanmutasi = 'unique:penggunaanbaranginventaris,lokasi_id';
                } else {
                    $ruletujuanmutasi = '';
                }

                $validator = Validator::make(request()->all(), [
                    'tujuanmutasi' => [
                        $ruletujuanmutasi,
                        function ($attribute, $value, $fail) {
                            if (!Lokasi::where('id', (int)$value)->exists()) {
                                $fail('Kolom ini wajib dipilih');
                            }
                        },
                    ],
                ], [
                    'tujuanmutasi.unique' => 'Pilih tujuan mutasi'
                ]);

                if ($validator->fails()) {
                    $response = [
                        'status' => 422,
                        'message' => 'opps',
                        'dataError' => $validator->errors()
                    ];

                    return response()->json($response);
                }

                $update = Penggunaanbaranginventaris::where('id', (int)$penggunaanbarang_id)->update([
                    'lokasi_id' => $tujuanmutasi,
                    'operator_id' => auth()->user()->id,
                    'updated_at' => date("Y-m-d H:i:s"),
                ]);

                // server
                DB::connection("mysqldua")->table("penggunaanbaranginventaris")->where('id', (int)$penggunaanbarang_id)->update([
                    'lokasi_id' => $tujuanmutasi,
                    'operator_id' => auth()->user()->id,
                    'updated_at' => date("Y-m-d H:i:s"),
                ]);

                if ($update) {
                    // log
                    $logbaranginventaris = new Logbaranginventaris();
                    $logbaranginventaris->tanggal_log = date('Y-m-d H:i:s');
                    $logbaranginventaris->baranginventaris_id = $model_baranginventaris->id;

                    if ($model_penggunaanbaranginventaris->no_barcode) {
                        $text = $model_penggunaanbaranginventaris->no_barcode;

                        // Use explode to split the string by the first space
                        $parts = explode('-', $text);

                        $no_barcode = $parts[0] . '-' . $model_penggunaanbaranginventaris->label . '-' . 'lokasi' . $tujuanmutasi;

                        Penggunaanbaranginventaris::where('id', (int)$model_penggunaanbaranginventaris->id)->update([
                            'no_barcode' => $no_barcode,
                            'operator_id' => auth()->user()->id,
                            'updated_at' => date("Y-m-d H:i:s"),
                        ]);

                        // server
                        DB::connection("mysqldua")->table("penggunaanbaranginventaris")->where('id', (int)$model_penggunaanbaranginventaris->id)->update([
                            'no_barcode' => $no_barcode,
                            'operator_id' => auth()->user()->id,
                            'updated_at' => date("Y-m-d H:i:s"),
                        ]);

                        $logbaranginventaris->no_barcode = $no_barcode;
                        $logbaranginventaris->label = $model_penggunaanbaranginventaris->label;

                        $server_no_barcode = $no_barcode;
                        $server_label = $model_penggunaanbaranginventaris->label;
                    } else {
                        $server_no_barcode = NULL;
                        $server_label = NULL;
                    }
            
                    $logbaranginventaris->kategoribaranginventaris_id = $model_baranginventaris->kategoribaranginventaris_id;
                    $logbaranginventaris->nama = $model_baranginventaris->nama;
                    $logbaranginventaris->jumlah = $model_penggunaanbaranginventaris->jumlah;
                    $logbaranginventaris->asallokasi_id = $model_penggunaanbaranginventaris->lokasi_id;
                    $logbaranginventaris->tujuanlokasi_id = $tujuanmutasi;
                    $logbaranginventaris->log = 4;
                    $logbaranginventaris->operator_id = auth()->user()->id;
                    $logbaranginventaris->save();

                    // server
                    DB::connection("mysqldua")->table("logbaranginventaris")->insert([
                        'tanggal_log' => date('Y-m-d H:i:s'),
                        'baranginventaris_id' => $model_baranginventaris->id,
                        'no_barcode' => $server_no_barcode,
                        'label' => $server_label,
                        'kategoribaranginventaris_id' => $model_baranginventaris->kategoribaranginventaris_id,
                        'nama' => $model_baranginventaris->nama,
                        'jumlah' => $model_penggunaanbaranginventaris->jumlah,
                        'asallokasi_id' => $model_penggunaanbaranginventaris->lokasi_id,
                        'tujuanlokasi_id' => $tujuanmutasi,
                        'log' => 4,
                        'operator_id' => auth()->user()->id,
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s"),
                    ]);

                    $response = [
                        'status' => 200,
                        'message' => 'success',
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
                $response = [
                    'status' => 500,
                    'message' => $e->getMessage(),
                ];

                DB::rollBack();
                return response()->json($response);
            }
        }
    }
    // hapus
    public function destroypenggunaanbarang()
    {
        if (request()->ajax()) {
            try {
                DB::beginTransaction();

                $penggunaanbarang_id = htmlspecialchars(request()->input('penggunaanbarang_id'), ENT_QUOTES, 'UTF-8');

                $model_penggunaanbaranginventaris = Penggunaanbaranginventaris::where('id', (int)$penggunaanbarang_id)->first();
                $model_baranginventaris = Baranginventaris::where('id', (int)$model_penggunaanbaranginventaris->baranginventaris_id)->first();

                // barang inventaris
                Baranginventaris::where('id', $model_baranginventaris->id)->update([
                    'jumlah_terpakai' => 0,
                    'operator_id' => auth()->user()->id,
                    'updated_at' => date("Y-m-d H:i:s"),
                ]);

                // server
                DB::connection("mysqldua")->table("baranginventaris")->where('id', $model_baranginventaris->id)->update([
                    'jumlah_terpakai' => 0,
                    'operator_id' => auth()->user()->id,
                    'updated_at' => date("Y-m-d H:i:s"),
                ]);

                // penggunaan barang inventaris
                Penggunaanbaranginventaris::where('id', $model_penggunaanbaranginventaris->id)->delete();

                // server
                DB::connection("mysqldua")->table("penggunaanbaranginventaris")->where('id', $model_penggunaanbaranginventaris->id)->delete();

                // log
                $logbaranginventaris = new Logbaranginventaris();
                $logbaranginventaris->tanggal_log = date('Y-m-d H:i:s');
                $logbaranginventaris->baranginventaris_id = $model_baranginventaris->id;

                if ($model_penggunaanbaranginventaris->no_barcode) {
                    $logbaranginventaris->no_barcode = $model_penggunaanbaranginventaris->no_barcode;
                    $logbaranginventaris->label = $model_penggunaanbaranginventaris->label;

                    $server_no_barcode = $model_penggunaanbaranginventaris->no_barcode;
                    $server_label = $model_penggunaanbaranginventaris->label;
                } else {
                    $server_no_barcode = NULL;
                    $server_label = NULL;
                }
              
                $logbaranginventaris->kategoribaranginventaris_id = $model_baranginventaris->kategoribaranginventaris_id;
                $logbaranginventaris->nama = $model_baranginventaris->nama;
                $logbaranginventaris->jumlah = $model_penggunaanbaranginventaris->jumlah;
                $logbaranginventaris->asallokasi_id = $model_penggunaanbaranginventaris->lokasi_id;
                $logbaranginventaris->log = 5;
                $logbaranginventaris->operator_id = auth()->user()->id;
                $logbaranginventaris->save();
               
                // server
                DB::connection("mysqldua")->table("logbaranginventaris")->insert([
                    'tanggal_log' => date('Y-m-d H:i:s'),
                    'baranginventaris_id' => $model_baranginventaris->id,
                    'no_barcode' => $server_no_barcode,
                    'label' => $server_label,
                    'kategoribaranginventaris_id' => $model_baranginventaris->kategoribaranginventaris_id,
                    'nama' => $model_baranginventaris->nama,
                    'jumlah' => $model_penggunaanbaranginventaris->jumlah,
                    'asallokasi_id' => $model_penggunaanbaranginventaris->lokasi_id,
                    'log' => 5,
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
}
