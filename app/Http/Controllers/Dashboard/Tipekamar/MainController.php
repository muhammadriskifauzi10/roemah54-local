<?php

namespace App\Http\Controllers\Dashboard\Tipekamar;

use App\Http\Controllers\Controller;
use App\Models\Tipekamar;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function index()
    {
        $data = [
            'judul' => 'Role',
        ];

        return view('contents.dashboard.tipekamar.main', $data);
    }
    public function datatabletipekamar()
    {
        $tipekamar = Tipekamar::orderby('created_at', 'ASC')->get();

        $output = [];
        foreach ($tipekamar as $row) {
            $output[] = [
                'tipekamar' => $row->tipekamar,
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
                $tipekamar = htmlspecialchars(request()->input('tipekamar'), ENT_QUOTES, 'UTF-8');

                if (Tipekamar::where('tipekamar', $tipekamar)->exists()) {
                    $response = [
                        'status' => 422,
                        'message' => 'error',
                    ];

                    return response()->json($response);
                }

                Tipekamar::create([
                    'tipekamar' => Str::upper($tipekamar),
                    'operator_id' => auth()->user()->id
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
