<?php

namespace App\Http\Controllers\Dashboard\Asrama\Kamar;

use App\Http\Controllers\Controller;
use App\Models\Asrama;
use App\Models\Asramadetail;
use App\Models\Lantai;
use App\Models\Lokasi;
use App\Models\Tipekamar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MainController extends Controller
{
    public function index()
    {
        $data = [
            'judul' => 'Kamar Asrama'
        ];

        return view('contents.dashboard.asrama.kamar.main', $data);
    }
    public function datatableasrama()
    {
        $asrama = Asrama::orderby('lantai_id', 'ASC')->get();

        $output = [];
        $no = 1;
        foreach ($asrama as $row) {

            // $aksi = '
            // <div class="d-flex flex-column align-items-center justify-content-center gap-1">
            //     <a href="' . route('asrama.detailpenyewa', encrypt($row->id)) . '" class="btn btn-info text-light fw-bold d-flex align-items-center justify-content-center gap-1" style="width: 100px;">
            //         <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
            //             <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
            //             <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
            //         </svg>    
            //         Detail
            //     </a>
            // </div>';

            $output[] = [
                'nomor' => '<strong>' . $no++ . '</strong>',
                'lantai' => $row->lantais->namalantai,
                'nomor_kamar' => $row->nomor_kamar,
                'tipe_kamar' => $row->tipekamar,
                'jenissewa' => $row->jenissewa,
                'jumlah_kapasitas' => $row->tipekamars->kapasitas . ' Mahasiswa',
                'jumlah_mahasiswa' => $row->jumlah_mahasiswa . ' Mahasiswa',
                // 'aksi' => $aksi,
            ];
        }

        return response()->json([
            'data' => $output
        ]);
    }
    public function tambahasrama()
    {
        $lantai = DB::table('lantais')
            ->join('lokasis', 'lantais.id', '=', 'lokasis.lantai_id')
            ->join('hargas', 'lokasis.tipekamar_id', '=', 'hargas.tipekamar_id')
            ->select(
                'lantais.id',
                'lantais.namalantai'
            )
            ->distinct()
            ->where('lokasis.jenisruangan_id', 2)
            ->where('lokasis.status', 0)
            ->orderBy('lantais.id', 'ASC')
            ->get();

        $data = [
            'judul' => 'Tambah Kamar Asrama',
            'lantai' => $lantai
        ];

        return view('contents.dashboard.asrama.tambah', $data);
    }
    public function create()
    {
        $lantai = htmlspecialchars(request()->input('lantai'), ENT_QUOTES, 'UTF-8');
        $kamar = htmlspecialchars(request()->input('kamar'), ENT_QUOTES, 'UTF-8');

        $validator = Validator::make(request()->all(), [
            'lantai' => [
                function ($attribute, $value, $fail) {
                    if (!Lantai::where('id', (int)$value)->exists()) {
                        $fail('Kolom ini wajib dipilih');
                    }
                },
            ],
            'kamar' => [
                function ($attribute, $value, $fail) {
                    if (!Lokasi::where('id', (int)$value)->exists()) {
                        $fail('Kolom ini wajib dipilih');
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $lokasi = Lokasi::where('id', $kamar)->first();

        $post = Asrama::create([
            'lantai_id' => $lantai,
            'nomor_kamar' => $lokasi->nomor_kamar,
            'tipekamar_id' => $lokasi->tipekamar_id,
            'tipekamar' => Tipekamar::where('id', $lokasi->tipekamar_id)->first()->tipekamar,
            'operator_id' => auth()->user()->id
        ]);

        if ($post) {
            Lokasi::where('id', $kamar)->update([
                'status' => 1
            ]);

            return redirect()->route('asrama')->with('messageSuccess', 'Kamar asrama berhasil ditambahkan');
        }
    }
    public function detailpenyewa($id)
    {
        $id = decrypt($id);

        if (!Asrama::where('id', $id)->exists()) {
            abort(404);
        }

        $asrama = Asrama::findorfail($id);
        $asramadetail = Asramadetail::where('asrama_id', $asrama->asrama_id)->get();

        $data = [
            'judul' => 'Detail Kamar Asrama',
            'asrama' => $asrama,
            'asramadetail' => $asramadetail,
        ];

        return view('contents.dashboard.asrama.detailpenyewa', $data);
    }
    // Trigger
    public function getselectlantaikamar()
    {
        if (request()->ajax()) {
            $lantai = htmlspecialchars(request()->input('lantai'), ENT_QUOTES, 'UTF-8');
            if (Lantai::where('id', (int)$lantai)->exists()) {
                $kamar = Lokasi::where('jenisruangan_id', 2)->where('lantai_id', (int)$lantai)
                    ->whereIn('tipekamar_id', [5, 6])
                    ->where('status', 0)->get();

                $selectkamar = [];
                foreach ($kamar as $row) {
                    $selectkamar[] = '<option value="' . $row->id . '">Nomor Kamar: ' . $row->nomor_kamar . ' | Tipe Kamar: ' . $row->tipekamars->tipekamar . '</option>';
                }

                $response = [
                    'status' => 200,
                    'message' => 'success',
                    'data' => [
                        'dataHTML' => implode(" ", $selectkamar)
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
