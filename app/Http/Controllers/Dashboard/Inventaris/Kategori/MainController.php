<?php

namespace App\Http\Controllers\Dashboard\Inventaris\Kategori;

use App\Http\Controllers\Controller;
use App\Models\Kategoribaranginventaris;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MainController extends Controller
{
    public function index()
    {
        $data = [
            'judul' => 'Kategori',
        ];

        return view('contents.dashboard.inventaris.kategori.main', $data);
    }
    public function datatablekategori()
    {
        $kategori = Kategoribaranginventaris::orderby('created_at', 'ASC')->get();

        $output = [];
        $no = 1;
        foreach ($kategori as $row) {
            $output[] = [
                'nomor' => '<strong>' . $no++ . '</strong>',
                'nama_kategori' => $row->nama,
            ];
        }

        return response()->json([
            'data' => $output
        ]);
    }
    // Ajax Request
    public function create()
    {
        if (request()->ajax()) {
            try {
                DB::beginTransaction();
                $kategori = htmlspecialchars(request()->input('kategori'), ENT_QUOTES, 'UTF-8');

                if (Kategoribaranginventaris::where('nama', $kategori)->exists()) {
                    $response = [
                        'status' => 422,
                        'message' => 'error',
                    ];

                    return response()->json($response);
                }

                $kategoribaranginventaris = new Kategoribaranginventaris();
                $kategoribaranginventaris->nama = Str::title($kategori);
                $kategoribaranginventaris->operator_id = auth()->user()->id;
                $kategoribaranginventaris->save();

                // server
                DB::connection("mysqldua")->table("kategoribaranginventaris")->insert([
                    'nama' => Str::title($kategori),
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
