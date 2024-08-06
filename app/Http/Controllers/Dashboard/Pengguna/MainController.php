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
        $pengguna = User::orderby('role_id', 'ASC')->orderby('created_at', 'ASC')->get();

        $output = [];
        $no = 1;
        foreach ($pengguna as $row) {
            $output[] = [
                'nomor' => '<strong>' . $no++ . '</strong>',
                'role' => $row->roles->role,
                'nama_pengguna' => $row->username,
                'status' => $row->status == 1 ? '<span class="badge bg-green">Aktif</span>' : '<span class="badge bg-red">-</span>',
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
            'judul' => 'Tambah Laundri',
            'role' => $role
        ];

        return view('contents.dashboard.manajemenpengguna.pengguna.tambah', $data);
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
                return redirect()->route('manajemenpengguna.pengguna')->with('messageSuccess', 'Pengguna berhasil ditambahkan!');
            }
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }
    }
}
