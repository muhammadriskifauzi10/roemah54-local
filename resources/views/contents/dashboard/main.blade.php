@extends('templates.dashboard.main')

@section('mystyles')
    <style>
        .lantai:hover .card {
            background-color: #b7d6ff;
            transition: .3s linear;
        }
    </style>
@endsection

@section('contents')
    <div class="container-fluid mt-3 mb-3">
        <div class="row">
            <div class="col-xl-2 mb-3">
                @include('templates.dashboard.sidebar')
            </div>
            <div class="col-xl-10">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">Lantai</li>
                    </ol>
                </nav>

                {{-- <div class="mb-3">
                    <button type="button" class="btn btn-dark d-flex align-items-center gap-1" onclick="requestLantai()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                            class="bi bi-plus-lg" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2" />
                        </svg>
                        Lantai</button>
                </div> --}}
                @if ($lantai->count() > 0)
                    <div class="row">
                        @foreach ($lantai as $row)
                            <a href="{{ route('detaillantai', $row->id) }}"
                                class="col-xl-12 lantai text-decoration-none mb-4">
                                <div class="card border-0 rounded" style="height: 100%">
                                    <div class="card-header bg-green text-light text-center fw-bold">
                                        {{ $row->namalantai }}
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        @if ($row->lokasis->count() > 0)
                                            <table>
                                                <tbody>
                                                    <tr>
                                                        <td>Jumlah Kamar</td>
                                                        {{-- <td class="text-right" width="10">:</td> --}}
                                                        <td class="text-right fw-bold green">
                                                            {{ $row->lokasis->count() }} Kamar
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Harian</td>
                                                        {{-- <td class="text-right" width="10">:</td> --}}
                                                        <td class="text-right fw-bold">
                                                            {{ DB::table('lokasis as k')->join('pembayarans as p', 'k.id', '=', 'p.lokasi_id')->join('lantais as l', 'k.lantai_id', '=', 'l.id')->join('penyewas as s', 'p.penyewa_id', '=', 's.id')->where('l.id', $row->id)->where('p.jenissewa', 'Harian')->whereIn('k.status', [1, 2])->where('p.status_pembayaran', '!=', 'failed')->whereIn('p.status', [1, 2])->distinct('k.id')->count('k.id') }}
                                                            Kamar
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Mingguan / 7 Hari </td>
                                                        {{-- <td class="text-right" width="10">:</td> --}}
                                                        <td class="text-right fw-bold">
                                                            {{ DB::table('lokasis as k')->join('pembayarans as p', 'k.id', '=', 'p.lokasi_id')->join('lantais as l', 'k.lantai_id', '=', 'l.id')->join('penyewas as s', 'p.penyewa_id', '=', 's.id')->where('l.id', $row->id)->where('p.jenissewa', 'Mingguan / 7 Hari')->whereIn('k.status', [1, 2])->where('p.status_pembayaran', '!=', 'failed')->whereIn('p.status', [1, 2])->distinct('k.id')->count('k.id') }}
                                                            Kamar
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Mingguan / (14 Hari)</td>
                                                        {{-- <td class="text-right" width="10">:</td> --}}
                                                        <td class="text-right fw-bold">
                                                            {{ DB::table('lokasis as k')->join('pembayarans as p', 'k.id', '=', 'p.lokasi_id')->join('lantais as l', 'k.lantai_id', '=', 'l.id')->join('penyewas as s', 'p.penyewa_id', '=', 's.id')->where('l.id', $row->id)->where('p.jenissewa', 'Mingguan / (14 Hari)')->whereIn('k.status', [1, 2])->where('p.status_pembayaran', '!=', 'failed')->whereIn('p.status', [1, 2])->distinct('k.id')->count('k.id') }}
                                                            Kamar
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Bulanan</td>
                                                        {{-- <td class="text-right" width="10">:</td> --}}
                                                        <td class="text-right fw-bold">
                                                            {{ DB::table('lokasis as k')->join('pembayarans as p', 'k.id', '=', 'p.lokasi_id')->join('lantais as l', 'k.lantai_id', '=', 'l.id')->join('penyewas as s', 'p.penyewa_id', '=', 's.id')->where('l.id', $row->id)->where('p.jenissewa', 'Bulanan')->whereIn('k.status', [1, 2])->where('p.status_pembayaran', '!=', 'failed')->whereIn('p.status', [1, 2])->distinct('k.id')->count('k.id') }}
                                                            Kamar
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <div class="mt-2 d-flex align-items-center gap-4">
                                                <div class="bg-success"
                                                    style="width: 20px; height: 20px; border-radius: 50px;"></div>
                                                <strong class="black">{{ $row->lokasis->whereIn('status', [1,2])->count() }}
                                                    Kamar Terisi</strong>
                                            </div>
                                            <div class="mt-1 d-flex align-items-center gap-4">
                                                <div class="bg-warning"
                                                    style="width: 20px; height: 20px; border-radius: 50px;"></div>
                                                <strong class="black">{{ $row->lokasis->where('status', 2)->count() }}
                                                    Booking / Belum Lunas</strong>
                                            </div>
                                            <div class="mt-1 d-flex align-items-center gap-4">
                                                <div class="bg-danger"
                                                    style="width: 20px; height: 20px; border-radius: 50px;"></div>
                                                <strong class="black">{{ $row->lokasis->where('status', 0)->count() }}
                                                    Kamar
                                                    Kosong</strong>
                                            </div>
                                        @else
                                            <div class="mt-4 text-center">
                                                <span class="sub-title text-secondary">-_- Fasilitas Kosong -_-</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="m-0 text-center fw-bold text-secondary">-_- Lantai Kosong -_-</p>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('myscripts')
    <script>
        function requestLantai() {
            Swal.fire({
                title: 'Tambah Lantai?',
                text: "Anda yakin ingin menambahkan lantai ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#25d366', // Warna hijau
                cancelButtonColor: '#cc0000', // Warna merah
                confirmButtonText: 'Ya, saya yakin!',
                cancelButtonText: 'Tidak, batalkan!'
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData();
                    formData.append("token", $("#token").val());

                    $.ajax({
                        url: "{{ route('postlantai') }}",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.message == "success") {
                                Swal.fire({
                                    title: "Berhasil",
                                    text: "Lantai berhasil ditambahkan",
                                    icon: "success"
                                })
                                setTimeout(function() {
                                    location.reload()
                                }, 1000)
                            } else {
                                Swal.fire({
                                    title: "Opps, terjadi kesalahan",
                                    icon: "error"
                                })
                            }
                        },
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Dibatalkan",
                        icon: "error"
                    })
                }
            })
        }
    </script>
@endpush
