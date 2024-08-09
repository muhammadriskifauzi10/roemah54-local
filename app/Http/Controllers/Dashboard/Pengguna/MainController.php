<?php

namespace App\Http\Controllers\Dashboard\Pengguna;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MainController extends Controller
{
    public function index()
    {
        $data = [
            'judul' => 'Pengguna',
        ];

        return view('contents.dashboard.pengguna.main', $data);
    }
    public function datatablepengguna()
    {
        $pengguna = User::where('id', '<>', auth()->user()->id)->orderby('role_id', 'ASC')->orderby('created_at', 'ASC')->get();

        $output = [];
        $no = 1;
        foreach ($pengguna as $row) {

            $aksi = '<div class="d-flex align-items-center justify-content-center gap-1">
                        <a href="' . route('pengguna.detailpengguna', encrypt($row->id)) . '" class="btn btn-warning text-light fw-bold" style="width: 90px;">Detail</a>
                        <button type="button" class="btn btn-danger text-light fw-bold"
                        data-hapus="' . $row->id . '" onclick="requestHapusPengguna(this)" style="width: 90px;">Hapus</button>
                   </div>';

            $output[] = [
                'nomor' => '<strong>' . $no++ . '</strong>',
                'role' => $row->roles->role,
                'nama_pengguna' => $row->username,
                'status' => $row->status == 1 ? '<span class="badge bg-green">Aktif</span>' : '<span class="badge bg-red">Nonaktif</span>',
                'aksi' => $aksi
            ];
        }

        return response()->json([
            'data' => $output
        ]);
    }
    public function tambahpengguna()
    {
        $role = Role::all();

        $data = [
            'judul' => 'Tambah Pengguna',
            'role' => $role
        ];

        return view('contents.dashboard.pengguna.tambah', $data);
    }
    public function create()
    {
        $validator = Validator::make(request()->all(), [
            'role' => [
                function ($attribute, $value, $fail) {
                    if (!Role::where('id', (int)$value)->exists()) {
                        $fail('Kolom ini wajib dipilih');
                    }
                },
            ],
            'nama_pengguna' => 'required|unique:users,username',
            'password' => 'required',
            'password_confirmation' => 'required|same:password',
        ], [
            'nama_pengguna.required' => 'Kolom ini wajib diisi',
            'nama_pengguna.unique' => 'Kolom ini sudah terdaftar',
            'password.required' => 'Kolom ini wajib diisi',
            'password_confirmation.required' => 'Kolom ini wajib diisi',
            'password_confirmation.same' => 'Konfirmasi kata sandi salah',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            $role = htmlspecialchars(request()->input('role'), true);
            $nama_pengguna = htmlspecialchars(request()->input('nama_pengguna'), true);
            $password = htmlspecialchars(request()->input('password'), true);

            $post = User::create([
                'role_id' => $role,
                'username' => $nama_pengguna,
                'slug' => Str::slug($nama_pengguna),
                'email' => Str::lower(preg_replace('/\s+/', '', $nama_pengguna) . "@gmail.com"),
                'password' => bcrypt($password)
            ]);

            // server
            DB::connection("mysqldua")->table("users")->insert([
                'role_id' => $role,
                'username' => $nama_pengguna,
                'slug' => Str::slug($nama_pengguna),
                'email' => Str::lower(preg_replace('/\s+/', '', $nama_pengguna) . "@gmail.com"),
                'password' => bcrypt($password),
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ]);

            if ($post) {
                DB::commit();
                return redirect()->route('pengguna')->with('messageSuccess', 'Pengguna berhasil ditambahkan!');
<<<<<<< HEAD
=======
            }
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }
    }
    public function detailpengguna($id)
    {
        $id = decrypt($id);

        if (!User::where('id', $id)->exists()) {
            abort(404);
        }

        $role = Role::query();

        if (auth()->user()->role_id == 1) {
            $role = Role::all();
        } else {
            $role = Role::where('id', auth()->user()->role_id)->get();
        }

        $pengguna = User::where('id', $id)->first();
        $data = [
            'judul' => 'Detail Pengguna',
            'role' => $role,
            'pengguna' => $pengguna,
        ];

        return view('contents.dashboard.pengguna.detail', $data);
    }
    // hapus
    public function destroypengguna()
    {
        if (request()->ajax()) {
            try {
                DB::beginTransaction();

                $pengguna_id = htmlspecialchars(request()->input('pengguna_id'), ENT_QUOTES, 'UTF-8');

                // user
                User::where('id', $pengguna_id)->update([
                    'status' => 0,
                    'updated_at' => date("Y-m-d H:i:s"),
                ]);

                // server
                DB::connection("mysqldua")->table("users")->where('id', $pengguna_id)->update([
                    'status' => 0,
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
    // aktifkan
    public function aktifkanpengguna()
    {
        if (request()->ajax()) {
            try {
                DB::beginTransaction();

                $pengguna_id = htmlspecialchars(request()->input('pengguna_id'), ENT_QUOTES, 'UTF-8');

                // user
                User::where('id', $pengguna_id)->update([
                    'status' => 1,
                    'updated_at' => date("Y-m-d H:i:s"),
                ]);

                // server
                DB::connection("mysqldua")->table("users")->where('id', $pengguna_id)->update([
                    'status' => 1,
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
    // update
    public function update($id)
    {
        $id = decrypt($id);

        if (!User::where('id', $id)->exists()) {
            abort(404);
        }

        $model_pengguna = User::where('id', $id)->first();
        $nama_baru = htmlspecialchars(request()->input('nama_pengguna'), true);

        if ($model_pengguna->username != $nama_baru) {
            $rule_namapengguna = 'unique:users,username';
        } else {
            $rule_namapengguna = '';
        }

        $validator = Validator::make(request()->all(), [
            'role' => [
                function ($attribute, $value, $fail) {
                    if (!Role::where('id', (int)$value)->exists()) {
                        $fail('Kolom ini wajib dipilih');
                    }
                },
            ],
            'nama_pengguna' => ['required', $rule_namapengguna],
            'password' => 'required',
            'password_confirmation' => 'required|same:password',
        ], [
            'nama_pengguna.required' => 'Kolom ini wajib diisi',
            'nama_pengguna.unique' => 'Kolom ini sudah terdaftar',
            'password.required' => 'Kolom ini wajib diisi',
            'password_confirmation.required' => 'Kolom ini wajib diisi',
            'password_confirmation.same' => 'Konfirmasi kata sandi salah',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            $role = htmlspecialchars(request()->input('role'), true);
            $password = htmlspecialchars(request()->input('password'), true);

            $update = User::where('id', $id)->update([
                'role_id' => $role,
                'username' => $nama_baru,
                'slug' => Str::slug($nama_baru),
                'email' => Str::lower(preg_replace('/\s+/', '', $nama_baru) . "@gmail.com"),
                'password' => bcrypt($password)
            ]);

            // server
            DB::connection("mysqldua")->table("users")->where('id', $id)->update([
                'role_id' => $role,
                'username' => $nama_baru,
                'slug' => Str::slug($nama_baru),
                'email' => Str::lower(preg_replace('/\s+/', '', $nama_baru) . "@gmail.com"),
                'password' => bcrypt($password),
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ]);

            if ($update) {
                DB::commit();
                return redirect()->back()->with('messageSuccess', 'Pengguna berhasil diperbarui!');
>>>>>>> 4d434aee6f09f111af6eac6625cfd6d1ec099b20
            }
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }
    }
}
