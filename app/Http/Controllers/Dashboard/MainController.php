<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Lantai;
use Exception;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Catch_;

class MainController extends Controller
{
    public function index()
    {
        $lantai = Lantai::join('lokasis', 'lantais.id', '=', 'lokasis.lantai_id')
            ->select(
                'lantais.id',
                'lantais.namalantai',
            )
            ->distinct()
            ->where('lokasis.jenisruangan_id', 2)
            ->orderBy('lantais.id', 'ASC')
            ->get();

        $data = [
            'judul' => 'Dasbor',
            'lantai' => $lantai
        ];

        return view('contents.dashboard.main', $data);
    }
    public function detaildata(Lantai $lantai)
    {
        $data = [
            'judul' => 'Detail Lantai',
            'lantai' => $lantai,
        ];
        return view('contents.dashboard.detaillantai', $data);
    }
    public function create()
    {
        if (request()->ajax()) {
            try {
                DB::beginTransaction();
                Lantai::create([
                    'namalantai' => "Lantai " . Lantai::all()->count() + 1,
                    'operator_id' => auth()->user()->id
                ]);

                $response = [
                    'status' => 200,
                    'message' => 'success',
                ];

                // server
                DB::connection("mysqldua")->table("lantais")->insert([
                    'namalantai' => "Lantai " . Lantai::all()->count() + 1,
                    'operator_id' => auth()->user()->id,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ]);

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
