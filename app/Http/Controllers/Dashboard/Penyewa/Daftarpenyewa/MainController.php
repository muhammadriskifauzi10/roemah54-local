<?php

namespace App\Http\Controllers\Dashboard\Penyewa\Daftarpenyewa;

use App\Http\Controllers\Controller;
use App\Models\Penyewa;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MainController extends Controller
{
      public function index()
      {
            $data = [
                  'judul' => 'Daftar Penyewa',
            ];

            return view('contents.dashboard.penyewa.daftarpenyewa.main', $data);
      }
      public function datatabledaftarpenyewa()
      {
            $jenis_penyewa = request()->input('jenis_penyewa');
            $status = request()->input('status');

            $daftarpenyewa = Penyewa::when($jenis_penyewa !== "Pilih Jenis Penyewa", function ($query) use ($jenis_penyewa) {
                  $query->where('jenis_penyewa', $jenis_penyewa);
            })
                  ->when($status !== "Pilih Status", function ($query) use ($status) {
                        $query->where('status', $status);
                  })->get();

            $output = [];
            $no = 1;
            foreach ($daftarpenyewa as $row) {
                  // status pembayaran
                  if ($row->status == 1) {
                        $status = "<span class='badge bg-success'>Sedang Menyewa</span>";
                  } else {
                        $status = "<span class='badge bg-danger'>-</span>";
                  }

                  if ($row->jenis_kelamin) {
                        if ($row->jenis_kelamin == "L") {
                              $jenis_kelamin = "Laki-Laki";
                        } else {
                              $jenis_kelamin = "Perempuan";
                        }
                  } else {
                        $jenis_kelamin = "-";
                  }

                  // if (auth()->user()->can('edit daftarpenyewa')) {
                        $editdaftarpenyewa = '
                        <a href="' . route('daftarpenyewa.edit', encrypt($row->id)) . '" class="btn btn-warning text-light fw-bold d-flex align-items-center justify-content-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pen-fill" viewBox="0 0 16 16">
                                <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001"/>
                            </svg>
                            Edit Penyewa
                        </a>
                        ';
                  // } else {
                  //       $editdaftarpenyewa = '-';
                  // }

                  if ($row->jenis_penyewa == "Umum") {
                        $fotoktp = '<a href="' . asset('img/ktp/umum/' . $row->fotoktp) . '" class="text-decoration-none fw-bold fw-bold" target="_blank">Lihat File</a>';
                  } else {
                        $fotoktp = '<a href="' . asset('img/ktp/asrama/' . $row->fotoktp) . '" class="text-decoration-none fw-bold fw-bold" target="_blank">Lihat File</a>';
                  }

                  $aksi = '<div class="d-flex flex-column align-items-center justify-content-center gap-1">
                        ' . $editdaftarpenyewa . '
                  </div>';

                  $output[] = [
                        'nomor' => "<strong>" . $no++ . "</strong>",
                        'nama_lengkap' => $row->namalengkap,
                        'no_ktp' => $row->noktp,
                        'no_hp' => $row->nohp,
                        'jenis_kelamin' => $jenis_kelamin,
                        'alamat' => nl2br($row->alamat),
                        'jenis_penyewa' => $row->jenis_penyewa,
                        'status' => $status,
                        'foto_ktp' => $fotoktp,
                        'aksi' => $aksi
                  ];
            }

            return response()->json([
                  'data' => $output
            ]);
      }
      public function edit($id)
      {
            $id = decrypt($id);

            if (!Penyewa::where('id', $id)->exists()) {
                  abort(404);
            }

            $penyewa = Penyewa::where('id', $id)->first();

            if ($penyewa->jenis_penyewa == "Umum") {
                  $fotoktp = '<a href="' . asset('img/ktp/umum/' . $penyewa->fotoktp) . '" class="text-decoration-none fw-bold" target="_blank">Lihat File Sebelumnya</a>';
            } else {
                  $fotoktp = '<a href="' . asset('img/ktp/asrama/' . $penyewa->fotoktp) . '" class="text-decoration-none fw-bold" target="_blank">Lihat File Sebelumnya</a>';
            }

            $data = [
                  'judul' => 'Edit Daftar Penyewa',
                  'penyewa' => $penyewa,
                  'fotoktp' => $fotoktp
            ];

            return view('contents.dashboard.penyewa.daftarpenyewa.edit', $data);
      }
      public function update($id)
      {
            $id = decrypt($id);

            if (!Penyewa::where('id', $id)->exists()) {
                  abort(404);
            }

            $penyewa = Penyewa::where('id', $id)->first();

            $noktp = htmlspecialchars(request()->input('noktp'), true);

            $validator = Validator::make(request()->all(), [
                  'namalengkap' => 'required',
                  'noktp' => 'required|numeric|digits:16',
                  'nohp' => 'required|regex:/^08[0-9]{8,}$/',
                  'jenis_kelamin' => [
                        function ($attribute, $value, $fail) {
                              if ($value == "Pilih Jenis Kelamin") {
                                    $fail('Kolom ini wajib dipilih');
                              }
                        },
                  ],
                  'fotoktp' => 'mimes:jpg,jpeg,png',
                  'alamat' => 'required',
                  // 'tipe_pembayaran' => 'required',
            ], [
                  'namalengkap.required' => 'Kolom ini wajib diisi',
                  'noktp.required' => 'No KTP wajib diisi',
                  'noktp.numeric' => 'No KTP tidak valid',
                  'noktp.digits' => 'No KTP tidak valid',
                  'nohp.required' => 'Kolom ini wajib diisi',
                  'nohp.regex' => 'No HP tidak valid',
                  'fotoktp.mimes' => 'Ekstensi file hanya mendukung format jpg dan jpeg',
                  'alamat.required' => 'Kolom ini wajib diisi',
            ]);

            if ($validator->fails()) {
                  return redirect()
                        ->back()
                        ->withErrors($validator)
                        ->withInput();
            }

            try {
                  DB::beginTransaction();

                  $namalengkap = htmlspecialchars(request()->input('namalengkap'), true);
                  $nohp = htmlspecialchars(request()->input('nohp'), true);
                  $jenis_kelamin = htmlspecialchars(request()->input('jenis_kelamin'), true);
                  $alamat = htmlspecialchars(request()->input('alamat'), true);
                  $fotoktp = request()->file('fotoktp');

                  $update = Penyewa::where('id', $penyewa->id)->update([
                        'namalengkap' => $namalengkap,
                        'noktp' => $noktp,
                        'nohp' => $nohp,
                        'jenis_kelamin' => $jenis_kelamin,
                        'alamat' => $alamat,
                        'operator_id' => auth()->user()->id,
                        'updated_at' => date("Y-m-d H:i:s"),
                  ]);

                  if ($update) {
                        if (request()->file('fotoktp')) {
                              // umum
                              if ($penyewa->jenis_penyewa == "Umum") {
                                    // Hapus file KTP lama jika ada
                                    if (file_exists('img/ktp/umum/' . $penyewa->fotoktp)) {
                                          unlink('img/ktp/umum/' . $penyewa->fotoktp);
                                    }

                                    $fotoktp = "penyewa" . "-" . $penyewa->id . "." . request()->file('fotoktp')->getClientOriginalExtension();
                                    $file = request()->file('fotoktp');
                                    $tujuan_upload = 'img/ktp/umum';
                                    $file->move($tujuan_upload, $fotoktp);
                              }
                              // mahasiswa
                              else {
                                    if (file_exists('img/ktp/asrama/' . $penyewa->fotoktp)) {
                                          unlink('img/ktp/asrama/' . $penyewa->fotoktp);
                                    }

                                    $fotoktp = "penyewa" . "-" . $penyewa->id . "." . request()->file('fotoktp')->getClientOriginalExtension();
                                    $file = request()->file('fotoktp');
                                    $tujuan_upload = 'img/ktp/asrama';
                                    $file->move($tujuan_upload, $fotoktp);
                              }

                              Penyewa::where('id', $penyewa->id)->update([
                                    'fotoktp' => $fotoktp,
                              ]);
                        }

                        DB::commit();
                        return redirect()->back()->with('messageSuccess', 'Penyewaan kamar berhasil diperbarui');
                  }
            } catch (Exception $e) {
                  DB::rollBack();
                  echo $e->getMessage();
            }
      }
}
