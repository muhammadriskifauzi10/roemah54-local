<?php

namespace App\Http\Controllers\Dashboard\Role;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MainController extends Controller
{
    public function index()
    {
        $data = [
            'judul' => 'Role',
        ];

        return view('contents.dashboard.role.main', $data);
    }
    public function datatablerole()
    {
        $role = Role::orderby('created_at', 'ASC')->get();

        $output = [];
        foreach ($role as $row) {
            $output[] = [
                'nama_role' => $row->role,
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
                $role = htmlspecialchars(request()->input('role'), ENT_QUOTES, 'UTF-8');

                if (Role::where('role', $role)->exists()) {
                    $response = [
                        'status' => 422,
                        'message' => 'error',
                    ];

                    return response()->json($response);
                }

                Role::create([
                    'role' => Str::title($role),
                    'operator_id' => auth()->user()->id
                ]);

                // server
                DB::connection("mysqldua")->table("roles")->insert([
                    'role' => Str::title($role),
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
