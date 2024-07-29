<?php

namespace App\Http\Controllers\Dashboard\Inventaris\Barang;

use App\Http\Controllers\Controller;
use App\Models\Baranginventaris;
use App\Models\Kategoribaranginventaris;
use App\Models\Logbaranginventaris;
// use App\Models\Logbaranginventaris;
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

        $data = [
            'judul' => 'Barang Inventaris',
            'kategori' => $kategori
        ];

        return view('contents.dashboard.inventaris.barang.main', $data);
    }
    public function datatablebarang()
    {
        $min = request()->input('minDate');
        $max = request()->input('maxDate');
        $kategori = request()->input('kategori');

        $kategoriExists = Kategoribaranginventaris::where('id', (int)$kategori)->exists();

        $baranginventaris = Baranginventaris::when($kategoriExists, function ($query) use ($kategori) {
            $query->where('kategoribaranginventaris_id', (int)$kategori);
        })
            ->when($min && $max, function ($query) use ($min, $max) {
                $query->whereDate('tanggal_masuk', '>=', $min)
                    ->whereDate('tanggal_masuk', '<=', $max);
            })
            ->orderBy('created_at', 'ASC')
            ->get();

        $output = [];
        $no = 1;
        foreach ($baranginventaris as $row) {
            $aksi = '
            <div class="d-flex align-items-center justify-content-center gap-1">
                <a href="' . route('inventaris.editbarang', encrypt($row->id)) . '" class="btn btn-warning text-light fw-bold">Edit</a>
            </div>';

            $output[] = [
                'nomor' => '<strong>' . $no++ . '</strong>',
                'tanggal_masuk' => Carbon::parse($row->tanggal_masuk)->format('Y-m-d H:i'),
                'kategori' => $row->kategoris->nama,
                'nama_barang' => $row->nama,
                'deskripsi' => $row->deskripsi,
                'harga' => "RP. " . number_format($row->harga, '0', '.', '.'),
                'jumlah' => intval($row->jumlah) - intval($row->jumlah_terpakai),
                'total_harga' => "RP. " . number_format($row->harga * (intval($row->jumlah) - intval($row->jumlah_terpakai)), '0', '.', '.'),
                'satuan' => $row->satuan,
                'aksi' => $aksi,
            ];
        }

        return response()->json([
            'data' => $output
        ]);
    }
    public function tambahbarang()
    {
        $kategori = Kategoribaranginventaris::all();

        $data = [
            'judul' => 'Tambah Barang Iventaris',
            'kategori' => $kategori
        ];

        return view('contents.dashboard.inventaris.barang.tambah', $data);
    }
    public function create()
    {
        $validator = Validator::make(request()->all(), [
            'tanggalmasuk' => 'required|date',
            'kategori' => [
                function ($attribute, $value, $fail) {
                    if (!Kategoribaranginventaris::where('id', (int)$value)->exists()) {
                        $fail('Kolom ini wajib dipilih');
                    }
                },
            ],
            'nama' => 'required',
            'deskripsi' => 'required',
            'jumlah' => 'required|numeric|min:1',
            'satuan' => 'required',
        ], [
            'tanggalmasuk.required' => 'Kolom ini wajib diisi',
            'tanggalmasuk.date' => 'Kolom ini wajib diisi',
            'nama.required' => 'Kolom ini wajib diisi',
            'deskripsi.required' => 'Kolom ini wajib diisi',
            'jumlah.required' => 'Kolom ini wajib diisi',
            'jumlah.numeric' => 'Kolom ini tidak valid',
            'jumlah.min' => 'Kolom ini tidak valid',
            'satuan.required' => 'Kolom ini wajib diisi',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $tanggalmasuk = htmlspecialchars(request()->input('tanggalmasuk'), true);
            $tanggalmasuk_format = Carbon::parse($tanggalmasuk)->format('Y-m-d H:i');
            $kategori = htmlspecialchars(request()->input('kategori'), true);
            $nama = htmlspecialchars(request()->input('nama'), true);
            $deskripsi = htmlspecialchars(request()->input('deskripsi'), true);
            $harga = htmlspecialchars(request()->input('harga'), true);
            $jumlah = htmlspecialchars(request()->input('jumlah'), true);
            $satuan = htmlspecialchars(request()->input('satuan'), true);

            $harga = $harga ? str_replace('.', '', $harga) : 0;
            $total_harga = intval($harga) * intval($jumlah);

            $baranginventaris = new Baranginventaris();
            $baranginventaris->tanggal_masuk = $tanggalmasuk_format;
            $baranginventaris->kategoribaranginventaris_id = $kategori;
            $baranginventaris->nama = $nama;
            $baranginventaris->deskripsi = $deskripsi;
            $baranginventaris->harga = intval($harga);
            $baranginventaris->jumlah = $jumlah;
            $baranginventaris->total_harga = intval($total_harga);
            $baranginventaris->satuan = Str::upper($satuan);
            $baranginventaris->operator_id = auth()->user()->id;
            $post = $baranginventaris->save();

            // server
            DB::connection("mysqldua")->table("baranginventaris")->insert([
                'tanggal_masuk' => $tanggalmasuk_format,
                'kategoribaranginventaris_id' => $kategori,
                'nama' => $nama,
                'deskripsi' => $deskripsi,
                'harga' => intval($harga),
                'jumlah' => $jumlah,
                'total_harga' => intval($total_harga),
                'satuan' => Str::upper($satuan),
                'operator_id' => auth()->user()->id,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ]);

            if ($post) {
                // log
                $logbaranginventaris = new Logbaranginventaris();
                $logbaranginventaris->tanggal_log = date('Y-m-d H:i:s');
                $logbaranginventaris->baranginventaris_id = $baranginventaris->id;
                $logbaranginventaris->tanggal_masuk = $tanggalmasuk_format;
                $logbaranginventaris->kategoribaranginventaris_id = $kategori;
                $logbaranginventaris->nama = $nama;
                $logbaranginventaris->deskripsi = $deskripsi;
                $logbaranginventaris->harga = intval($harga);
                $logbaranginventaris->jumlah = $jumlah;
                $logbaranginventaris->total_harga = intval($total_harga);
                $logbaranginventaris->satuan = Str::upper($satuan);
                $logbaranginventaris->log = 1;
                $logbaranginventaris->operator_id = auth()->user()->id;
                $logbaranginventaris->save();

                // server
                DB::connection("mysqldua")->table("logbaranginventaris")->insert([
                    'tanggal_log' => date('Y-m-d H:i:s'),
                    'baranginventaris_id' => $baranginventaris->id,
                    'tanggal_masuk' => $tanggalmasuk_format,
                    'kategoribaranginventaris_id' => $kategori,
                    'nama' => $nama,
                    'deskripsi' => $deskripsi,
                    'harga' => intval($harga),
                    'jumlah' => $jumlah,
                    'total_harga' => intval($total_harga),
                    'satuan' => Str::upper($satuan),
                    'log' => 1,
                    'operator_id' => auth()->user()->id,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ]);

                DB::commit();
                return redirect()->route('inventaris.barang')->with('messageSuccess', 'Barang Inventaris berhasil ditambahkan!');
            }
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }
    }
    public function editbarang($id)
    {
        $id = decrypt($id);

        if (!Baranginventaris::where('id', $id)->exists()) {
            abort(404);
        }

        $kategori = Kategoribaranginventaris::all();

        $barang = Baranginventaris::where('id', $id)->first();

        $data = [
            'judul' => 'Edit Barang Inventaris',
            'kategori' => $kategori,
            'barang' => $barang
        ];

        return view('contents.dashboard.inventaris.barang.edit', $data);
    }
    public function update($id)
    {
        $id = decrypt($id);

        if (!Baranginventaris::where('id', $id)->exists()) {
            return redirect()->back()->with('messageFailed', 'Opps, terjadi kesalahan!');
        }

        $validator = Validator::make(request()->all(), [
            'tanggalmasuk' => 'required|date',
            'kategori' => [
                function ($attribute, $value, $fail) {
                    if (!Kategoribaranginventaris::where('id', (int)$value)->exists()) {
                        $fail('Kolom ini wajib dipilih');
                    }
                },
            ],
            'nama' => 'required',
            'deskripsi' => 'required',
            'jumlah' => 'required|numeric|min:0',
            'satuan' => 'required',
        ], [
            'tanggalmasuk.required' => 'Kolom ini wajib diisi',
            'tanggalmasuk.date' => 'Kolom ini wajib diisi',
            'nama.required' => 'Kolom ini wajib diisi',
            'deskripsi.required' => 'Kolom ini wajib diisi',
            'jumlah.required' => 'Kolom ini wajib diisi',
            'jumlah.numeric' => 'Kolom ini tidak valid',
            'jumlah.min' => 'Kolom ini tidak valid',
            'satuan.required' => 'Kolom ini wajib diisi',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            $model_baranginventaris = Baranginventaris::where('id', $id)->first();

            $tanggalmasuk = htmlspecialchars(request()->input('tanggalmasuk'), true);
            $tanggalmasuk_format = Carbon::parse($tanggalmasuk)->format('Y-m-d H:i');
            $kategori = htmlspecialchars(request()->input('kategori'), true);
            $nama = htmlspecialchars(request()->input('nama'), true);
            $deskripsi = htmlspecialchars(request()->input('deskripsi'), true);
            $harga = htmlspecialchars(request()->input('harga'), true);
            $jumlah = htmlspecialchars(request()->input('jumlah'), true);
            $satuan = htmlspecialchars(request()->input('satuan'), true);

            $harga = $harga ? str_replace('.', '', $harga) : 0;

            $jumlahlama = intval($model_baranginventaris->jumlah) - intval($model_baranginventaris->jumlah_terpakai);

            if (intval($jumlah) > $jumlahlama) {
                $jumlahbaru = intval($model_baranginventaris->jumlah) + (intval($jumlah) - intval($jumlahlama));
            } elseif (intval($jumlah) >= $jumlahlama) {
                $jumlahbaru = intval($model_baranginventaris->jumlah);
            } else {
                $jumlahbaru = intval($model_baranginventaris->jumlah) - ($jumlahlama - intval($jumlah));
            }

            $total_harga = intval($harga) * intval($jumlahbaru);

            $update = Baranginventaris::where('id', $id)->update([
                'tanggal_masuk' => $tanggalmasuk_format,
                'kategoribaranginventaris_id' => $kategori,
                'nama' => $nama,
                'deskripsi' => $deskripsi,
                'harga' => intval($harga),
                'jumlah' => $jumlahbaru,
                'total_harga' => intval($total_harga),
                'satuan' => Str::upper($satuan),
                'operator_id' => auth()->user()->id,
            ]);

            // server
            DB::connection("mysqldua")->table("baranginventaris")->where('id', $id)->update([
                'tanggal_masuk' => $tanggalmasuk_format,
                'kategoribaranginventaris_id' => $kategori,
                'nama' => $nama,
                'deskripsi' => $deskripsi,
                'harga' => intval($harga),
                'jumlah' => $jumlahbaru,
                'total_harga' => intval($total_harga),
                'satuan' => Str::upper($satuan),
                'operator_id' => auth()->user()->id,
                'updated_at' => date("Y-m-d H:i:s"),
            ]);

            if ($update) {
                // log
                $logbaranginventaris = new Logbaranginventaris();
                $logbaranginventaris->tanggal_log = date('Y-m-d H:i:s');
                $logbaranginventaris->baranginventaris_id = $id;
                $logbaranginventaris->tanggal_masuk = $tanggalmasuk_format;
                $logbaranginventaris->kategoribaranginventaris_id = $kategori;
                $logbaranginventaris->nama = $nama;
                $logbaranginventaris->deskripsi = $deskripsi;
                $logbaranginventaris->harga = intval($harga);
                $logbaranginventaris->jumlah = $jumlah;
                $logbaranginventaris->total_harga = intval($total_harga);
                $logbaranginventaris->satuan = Str::upper($satuan);
                $logbaranginventaris->log = 2;
                $logbaranginventaris->operator_id = auth()->user()->id;
                $logbaranginventaris->save();

                // server
                DB::connection("mysqldua")->table("logbaranginventaris")->insert([
                    'tanggal_log' => date('Y-m-d H:i:s'),
                    'baranginventaris_id' => $id,
                    'tanggal_masuk' => $tanggalmasuk_format,
                    'kategoribaranginventaris_id' => $kategori,
                    'nama' => $nama,
                    'deskripsi' => $deskripsi,
                    'harga' => intval($harga),
                    'jumlah' => $jumlah,
                    'total_harga' => intval($total_harga),
                    'satuan' => Str::upper($satuan),
                    'log' => 2,
                    'operator_id' => auth()->user()->id,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ]);


                DB::commit();
                return redirect()->route('inventaris.barang')->with('messageSuccess', 'Barang Inventaris berhasil ditambahkan!');
            }
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }
    }
}
