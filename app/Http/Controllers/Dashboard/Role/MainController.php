<?php

namespace App\Http\Controllers\Dashboard\Role;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;

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
        $no = 1;
        foreach ($role as $row) {
            $menuaksi = '<button type="button" class="btn btn-warning text-light fw-bold d-flex align-items-center justify-content-center gap-1" data-edit="' . $row->id . '" onclick="openModalEditRole(this)">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pen-fill" viewBox="0 0 16 16">
                    <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001"/>
                </svg>
                Edit Role
            </button>';

            $aksi = '
            <div class="d-flex flex-column align-items-center justify-content-center gap-1">
            ' . $menuaksi . '
            </div>
            ';

            $output[] = [
                'nomor' => "<strong>" . $no++ . "</strong>",
                'nama_role' => $row->name,
                'aksi' => $aksi,
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

                $validator = Validator::make(request()->all(), [
                    'role' => ['required', 'unique:roles,name'],
                ], [
                    'role.required' => 'Kolom ini wajib diisi',
                    'role.unique' => 'Kolom ini sudah terdaftar',
                ]);

                if ($validator->fails()) {
                    $response = [
                        'status' => 422,
                        'message' => 'validation',
                        'dataError' => $validator->errors()
                    ];

                    return response()->json($response);
                }

                Role::create([
                    'name' => Str::title($role),
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
    public function getmodaleditrole()
    {
        if (request()->ajax()) {
            $role = Role::find(request()->input('role_id'));

            $dataHTML = '
            <form class="modal-content" onsubmit="requestEditRole(event)" autocomplete="off">
                <input type="hidden" name="__token" value="' . request()->input('token') . '" id="token">
                <input type="hidden" name="role_id" value="' . $role->id . '" id="role_id">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="universalModalLabel">Edit Role</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="role" class="form-label fw-bold">Role</label>
                        <input type="text" class="form-control" placeholder="Masukkan nama role" id="role" value="' . $role->name . '">
                        <div class="invalid-feedback" id="errorRole"></div>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-success w-100" id="btnRequest">
                            Perbarui
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
    public function update()
    {
        if (request()->ajax()) {
            try {
                DB::beginTransaction();
                $role = request()->input('role');
                $model_role = Role::find(request()->input('role_id'));

                if ($model_role->name == $role) {
                    $rule_role = '';
                } else {
                    $rule_role = 'unique:roles,name';
                }

                $validator = Validator::make(request()->all(), [
                    'role' => ['required', $rule_role],
                ], [
                    'role.required' => 'Kolom ini wajib diisi',
                    'role.unique' => 'Kolom ini sudah terdaftar',
                ]);

                if ($validator->fails()) {
                    $response = [
                        'status' => 422,
                        'message' => 'validation',
                        'dataError' => $validator->errors()
                    ];

                    return response()->json($response);
                }

                Role::where('id', (int)request()->input('role_id'))->update([
                    'name' => Str::title($role),
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
