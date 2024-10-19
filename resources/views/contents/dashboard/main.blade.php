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
                        <li class="breadcrumb-item active" aria-current="page">Dasbor</li>
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
                            <a href="{{ route('dasbor.detaillantai', $row->id) }}"
                                class="col-xl-6 lantai text-decoration-none mb-4">
                                <div class="card border-0 rounded" style="height: 100%">
                                    <div class="card-header bg-green text-light text-center fw-bold">
                                        {{ $row->namalantai }}
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td>Jumlah Kamar</td>
                                                    <td class="text-right fw-bold">
                                                        {{ $row->lokasis->count() }} Kamar
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Kamar Terisi</td>
                                                    <td class="text-right fw-bold">
                                                        {{ $row->lokasis->whereIn('status', 1)->count() }} Kamar Terisi
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Kamar Kosong</td>
                                                    <td class="text-right fw-bold">
                                                        {{ $row->lokasis->where('status', 0)->count() }} Kamar Kosong
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <hr />
                                        <p class="m-0 fw-bold text-center">Kamar Umum</p>
                                        <hr />
                                        @if ($row->lokasis->count() > 0)
                                            <table>
                                                <tbody>
                                                    <tr>
                                                        <td>Jumlah Kamar</td>
                                                        <td class="text-right fw-bold">
                                                            {{ $row->lokasis->whereNotIn('tipekamar_id', [5, 6, 7])->whereIn('status', 1)->count() }}
                                                            Kamar
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Kamar Booking</td>
                                                        <td class="text-right fw-bold">
                                                            {{ DB::table('lokasis as k')
                                                            ->join('pembayarans as p', 'k.id', '=', 'p.lokasi_id')
                                                            ->join('lantais as l', 'k.lantai_id', '=', 'l.id')
                                                            ->where('l.id', $row->id)
                                                            ->where('p.tagih_id', 1)
                                                            ->where('p.status_pembayaran', '!=', 'failed')
                                                            ->where('p.status', 2)
                                                            ->count() }}
                                                            Kamar
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Harian</td>
                                                        <td class="text-right fw-bold">
                                                            {{ DB::table('lokasis as k')
                                                            ->join('pembayarans as p', 'k.id', '=', 'p.lokasi_id')
                                                            ->join('lantais as l', 'k.lantai_id', '=', 'l.id')
                                                            ->join('penyewas as s', 'p.penyewa_id', '=', 's.id')
                                                            ->where('l.id', $row->id)->where('p.tagih_id', 1)
                                                            ->where('p.jenissewa', 'Harian')
                                                            ->where('k.status', 1)
                                                            ->where('p.status_pembayaran', '!=', 'failed')
                                                            ->where('p.status', 1)
                                                            ->distinct('k.id')
                                                            ->count('k.id') }}
                                                            Kamar
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Mingguan / 7 Hari </td>
                                                        <td class="text-right fw-bold">
                                                            {{ DB::table('lokasis as k')->join('pembayarans as p', 'k.id', '=', 'p.lokasi_id')->join('lantais as l', 'k.lantai_id', '=', 'l.id')->join('penyewas as s', 'p.penyewa_id', '=', 's.id')->where('l.id', $row->id)->where('p.tagih_id', 1)->where('p.jenissewa', 'Mingguan / 7 Hari')->where('k.status', 1)->where('p.status_pembayaran', '!=', 'failed')->where('p.status', 1)->distinct('k.id')->count('k.id') }}
                                                            Kamar
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Mingguan / (14 Hari)</td>
                                                        <td class="text-right fw-bold">
                                                            {{ DB::table('lokasis as k')->join('pembayarans as p', 'k.id', '=', 'p.lokasi_id')->join('lantais as l', 'k.lantai_id', '=', 'l.id')->join('penyewas as s', 'p.penyewa_id', '=', 's.id')->where('l.id', $row->id)->where('p.tagih_id', 1)->where('p.jenissewa', 'Mingguan / (14 Hari)')->where('k.status', 1)->where('p.status_pembayaran', '!=', 'failed')->where('p.status', 1)->distinct('k.id')->count('k.id') }}
                                                            Kamar
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Bulanan</td>
                                                        <td class="text-right fw-bold">
                                                            {{ DB::table('lokasis as k')->join('pembayarans as p', 'k.id', '=', 'p.lokasi_id')->join('lantais as l', 'k.lantai_id', '=', 'l.id')->join('penyewas as s', 'p.penyewa_id', '=', 's.id')->where('l.id', $row->id)->where('p.tagih_id', 1)->where('p.jenissewa', 'Bulanan')->where('k.status', 1)->where('p.status_pembayaran', '!=', 'failed')->where('p.status', 1)->distinct('k.id')->count('k.id') }}
                                                            Kamar
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <hr />
                                            <p class="m-0 fw-bold text-center">Kamar Asrama</p>
                                            <hr />
                                            <table>
                                                <tbody>
                                                    <tr>
                                                        <td>Jumlah Kamar</td>
                                                        <td class="text-right fw-bold">
                                                            {{ $row->lokasis->whereIn('tipekamar_id', [5, 6, 7])->whereIn('status', 1)->count() }}
                                                            Kamar
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Kamar Booking</td>
                                                        <td class="text-right fw-bold">
                                                            {{ $row->lokasis->whereIn('tipekamar_id', [5, 6, 7])->whereIn('status', 2)->count() }}
                                                            Kamar
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
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

                {{-- penyewaan kamar --}}
                <div class="card border-0">
                    <div class="card-header bg-green text-light text-center fw-bold">
                        Penyewa Terbaru Hari Ini
                    </div>
                    <div class="card-body">
                        <table class="mb-3">
                            <tr>
                                <th scope="col" class="text-left">Total Penyewa Terbaru</th>
                                <th scope="col" class="text-right">:</th>
                                <th scope="col" class="text-left" id="total"></th>
                            </tr>
                        </table>
                        <table class="table table-light table-hover border-0 m-0" id="datatablePenyewaanKamar"
                            style="width: 100%; white-space: nowrap">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Tanggal Masuk</th>
                                    <th scope="col">Tanggal Keluar</th>
                                    <th scope="col">Nama Penyewa</th>
                                    <th scope="col">Nomor Kamar</th>
                                    <th scope="col">Tipe Kamar</th>
                                    <th scope="col">Mitra</th>
                                    <th scope="col">Jenis Sewa</th>
                                    <th scope="col">Status Pembayaran</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscripts')
    <script>
        var tablePenyewaanKamar
        $(document).ready(function() {
            tablePenyewaanKamar = $("#datatablePenyewaanKamar").DataTable({
                processing: true,
                ajax: {
                    url: "{{ route('dasbor.datatablepenyewaankamar') }}",
                    type: "POST",
                    // dataSrc: ""
                    dataType: "json",
                    dataSrc: function(json) {
                        totalPenyewaTerbaru = 0;

                        // Hitung total pemasukan dan pengeluaran
                        json.data.forEach(function(row) {
                            totalPenyewaTerbaru += parseFloat(row.total);
                        });

                        $("#total").text(json.total)

                        return json.data;
                    },
                },
                columns: [{
                        data: "nomor",
                    },
                    {
                        data: "tanggal_masuk",
                    },
                    {
                        data: "tanggal_keluar",
                    },
                    {
                        data: "nama_penyewa",
                    },
                    {
                        data: "nomor_kamar",
                    },
                    {
                        data: "tipe_kamar",
                    },
                    {
                        data: "mitra",
                    },
                    {
                        data: "jenissewa",
                    },
                    {
                        data: "status_pembayaran",
                    },
                    {
                        data: "status",
                    },
                ],
                // "order": [
                //     [1, 'asc']
                // ],
                // scrollY: "700px",
                scrollX: true,
                // scrollCollapse: true,
                // paging:         false,
                // fixedColumns: {
                //     left: 3,
                // }
                paging: false, // Menonaktifkan pagination
                info: false, // Menonaktifkan informasi jumlah data
                searching: false, // Menonaktifkan pencarian
                dom: "t", // Hanya menampilkan tabel tanpa elemen lain (paging, info, dll)
            });
        });

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
