<?php

namespace App\Http\Controllers\Dashboard\Harga;

use App\Http\Controllers\Controller;
use App\Models\Harga;
use App\Models\Mitra;
use App\Models\Tipekamar;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class MainController extends Controller
{
    public function index()
    {
        $data = [
            'judul' => 'Harga',
        ];

        return view('contents.dashboard.harga.main', $data);
    }
    public function datatableharga()
    {
        $harga = Harga::orderBy('tipekamar_id', 'ASC')->orderBy('mitra_id', 'ASC')->get();

        $output = [];
        foreach ($harga as $row) {
            $output[] = [
                'tipe_kamar' => $row->tipekamars->tipekamar,
                'mitra' => $row->mitras->mitra,
                'harian' => "RP. " . number_format($row->harian, '0', '.', '.'),
                'mingguan' => "RP. " . number_format($row->mingguan, '0', '.', '.'),
                'hari14' => "RP. " . number_format($row->hari14, '0', '.', '.'),
                'bulanan' => "RP. " . number_format($row->bulanan, '0', '.', '.'),
            ];
        }

        return response()->json([
            'data' => $output
        ]);
    }
    public function tambahharga()
    {
        $tipekamar = Tipekamar::all();
        $mitra = Mitra::all();

        $data = [
            'judul' => 'Tambah Harga',
            'tipekamar' => $tipekamar,
            'mitra' => $mitra,
        ];

        return view('contents.dashboard.harga.tambah', $data);
    }
    public function create()
    {
        $tipe = htmlspecialchars(request()->input('tipe'), ENT_QUOTES, 'UTF-8');
        $mitra = htmlspecialchars(request()->input('mitra'), ENT_QUOTES, 'UTF-8');
        $harian = htmlspecialchars(request()->input('harian'), ENT_QUOTES, 'UTF-8');
        $mingguan = htmlspecialchars(request()->input('mingguan'), ENT_QUOTES, 'UTF-8');
        $hari14 = htmlspecialchars(request()->input('hari14'), ENT_QUOTES, 'UTF-8');
        $bulanan = htmlspecialchars(request()->input('bulanan'), ENT_QUOTES, 'UTF-8');

        $validator = Validator::make(request()->all(), [
            // 'kamar' => [
            //     'required',
            //     function ($attribute, $value, $fail) {
            //         if (!DB::table('tipekamars')->exists()) {
            //             $fail('Kolom ini wajib dipilih');
            //         }
            //     },
            // ],
            'tipe' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!DB::table('tipekamars')->where('id', (int)$value)->exists()) {
                        $fail('Kolom ini wajib dipilih');
                    }
                },
            ],
            'mitra' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!DB::table('mitras')->where('id', (int)$value)->exists()) {
                        $fail('Kolom ini wajib dipilih');
                    }
                },
            ],
            // 'harian' => 'required|not_in:0',
            // 'mingguan' => 'required|not_in:0',
            // 'hari14' => 'required|not_in:0',
            // 'bulanan' => 'required|not_in:0',
        ], [
            // 'harian.required' => 'Kolom ini wajib diisi',
            // 'harian.not_in' => 'Kolom ini wajib diisi',
            // 'mingguan.required' => 'Kolom ini wajib diisi',
            // 'mingguan.not_in' => 'Kolom ini wajib diisi',
            // 'hari14.required' => 'Kolom ini wajib diisi',
            // 'hari14.not_in' => 'Kolom ini wajib diisi',
            // 'bulanan.required' => 'Kolom ini wajib diisi',
            // 'bulanan.not_in' => 'Kolom ini wajib diisi',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            if (Harga::where('tipekamar_id', (int)$tipe)->where('mitra_id', (int)$mitra)->exists()) {
                $update = Harga::where('tipekamar_id', (int)$tipe)->where('mitra_id', (int)$mitra)->update([
                    'harian' => $harian ? str_replace('.', '', $harian) : 0,
                    'mingguan' => $mingguan ? str_replace('.', '', $mingguan) : 0,
                    'hari14' => $hari14 ? str_replace('.', '', $hari14) : 0,
                    'bulanan' => $bulanan ? str_replace('.', '', $bulanan) : 0,
                    'operator_id' => auth()->user()->id,
                    'updated_at' => date("Y-m-d H:i:s"),
                ]);

                if ($update) {
                    // server
                    DB::connection("mysqldua")->table("hargas")->where('tipekamar_id', (int)$tipe)->where('mitra_id', (int)$mitra)->update([
                        'harian' => $harian ? str_replace('.', '', $harian) : 0,
                        'mingguan' => $mingguan ? str_replace('.', '', $mingguan) : 0,
                        'hari14' => $hari14 ? str_replace('.', '', $hari14) : 0,
                        'bulanan' => $bulanan ? str_replace('.', '', $bulanan) : 0,
                        'operator_id' => auth()->user()->id,
                        'updated_at' => date("Y-m-d H:i:s"),
                    ]);

                    DB::commit();
                    return redirect()->route('harga')->with('messageSuccess', 'Harga berhasil diperbarui!');
                }
            }

            $post = Harga::create([
                'tipekamar_id' => $tipe,
                'mitra_id' => $mitra,
                'harian' => $harian ? str_replace('.', '', $harian) : 0,
                'mingguan' => $mingguan ? str_replace('.', '', $mingguan) : 0,
                'hari14' => $hari14 ? str_replace('.', '', $hari14) : 0,
                'bulanan' => $bulanan ? str_replace('.', '', $bulanan) : 0,
                'operator_id' => auth()->user()->id
            ]);

            // server
            DB::connection("mysqldua")->table("hargas")->insert([
                'tipekamar_id' => $tipe,
                'mitra_id' => $mitra,
                'harian' => $harian ? str_replace('.', '', $harian) : 0,
                'mingguan' => $mingguan ? str_replace('.', '', $mingguan) : 0,
                'hari14' => $hari14 ? str_replace('.', '', $hari14) : 0,
                'bulanan' => $bulanan ? str_replace('.', '', $bulanan) : 0,
                'operator_id' => auth()->user()->id,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ]);

            if ($post) {
                DB::commit();
                return redirect()->route('harga')->with('messageSuccess', 'Harga berhasil ditambahkan!');
            }
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('harga')->with('messageFailed', 'Opps, terjadi kesalahan!');
        }
    }
    // ajax request
    public function getselectharga()
    {
        if (request()->ajax()) {
            $tipe = htmlspecialchars(request()->input('tipe'), ENT_QUOTES, 'UTF-8');
            $mitra = htmlspecialchars(request()->input('mitra'), ENT_QUOTES, 'UTF-8');
            if (Harga::where('tipekamar_id', (int)$tipe)->where('mitra_id', (int)$mitra)->exists()) {
                $data = Harga::where('tipekamar_id', (int)$tipe)->where('mitra_id', (int)$mitra)->first();

                $response = [
                    'status' => 200,
                    'message' => 'success',
                    'data' => [
                        'harian' => intval($data->harian),
                        'mingguan' => intval($data->mingguan),
                        'hari14' => intval($data->hari14),
                        'bulanan' => intval($data->bulanan),
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
}
