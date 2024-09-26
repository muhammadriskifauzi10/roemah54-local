<?php

namespace App\Http\Controllers\Dashboard\Pengguna;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

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
        $pengguna = User::where('users.id', '<>', auth()->user()->id)
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->orderBy('roles.id', 'ASC') // Atau 'roles.name' jika ingin berdasarkan nama role
            ->select('users.*') // Hanya ambil kolom dari tabel users
            ->get();

        $output = [];
        $no = 1;
        foreach ($pengguna as $row) {

            if (auth()->user()->can('lihat pengguna')) {
                $lihatpengguna = '
                    <a href="' . route('pengguna.detailpengguna', encrypt($row->id)) . '" class="btn btn-info text-light fw-bold d-flex align-items-center justify-content-center gap-1" style="width: 100px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                            <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                        </svg>    
                        Detail
                    </a>
                ';
            } else {
                $lihatpengguna = '';
            }

            if (auth()->user()->can('hapus pengguna')) {
                $hapuspengguna = '
                    <button type="button" class="btn btn-danger text-light fw-bold d-flex align-items-center justify-content-center gap-1" data-hapus="' . $row->id . '" onclick="requestHapusPengguna(this)" style="width: 100px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
                            <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0"/>
                        </svg>
                        Hapus
                    </button>
                ';
            } else {
                $hapuspengguna = '';
            }

            if ($lihatpengguna == '' && $hapuspengguna == '') {
                $aksibutton = '-';
            } else {
                $aksibutton = $lihatpengguna . $hapuspengguna;
            }

            $aksi = '<div class="d-flex flex-column align-items-center justify-content-center gap-1">
                        ' . $aksibutton . '
                    </div>';

            $output[] = [
                'nomor' => "<strong>" . $no++ . "</strong>",
                'role' => $row->getRoleNames(),
                'nama_pengguna' => $row->username,
                'status' => $row->status == 1 ? '<span class="badge bg-green">Aktif</span>' : '<span class="badge bg-red">Nonaktif</span>',
                'aksi' => $aksi,
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
                'username' => $nama_pengguna,
                'slug' => Str::slug($nama_pengguna),
                'email' => Str::lower(preg_replace('/\s+/', '', $nama_pengguna) . "@gmail.com"),
                'password' => bcrypt($password)
            ]);

            if ($post) {
                $post->assignRole(Role::find($role)->name);

                DB::commit();
                return redirect()->route('pengguna')->with('messageSuccess', 'Pengguna berhasil ditambahkan!');
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

        if (auth()->user()->hasRole('Developer')) {
            $role = Role::all();
        } else {
            $role = Role::where('name', auth()->user()->getRoleNames())->get();
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
                'username' => $nama_baru,
                'slug' => Str::slug($nama_baru),
                'email' => Str::lower(preg_replace('/\s+/', '', $nama_baru) . "@gmail.com"),
                'password' => bcrypt($password)
            ]);

            if ($update) {
                DB::commit();
                return redirect()->back()->with('messageSuccess', 'Pengguna berhasil diperbarui!');
            }
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }
    }
}
