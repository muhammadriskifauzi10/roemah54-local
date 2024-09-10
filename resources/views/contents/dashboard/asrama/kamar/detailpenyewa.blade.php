@extends('templates.dashboard.main')

@section('contents')
    <div class="container-fluid mt-3 mb-3">
        <div class="row">
            <div class="col-xl-2 mb-3">
                @include('templates.dashboard.sidebar')
            </div>
            <div class="col-xl-10">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:history.back()">Kembali</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail Penyewa</li>
                    </ol>
                </nav>

                @if ($asrama->jumlah_mahasiswa != $asrama->tipekamars->kapasitas)
                    <div class="d-flex align-items-center justify-content-end mb-3">
                        <button type="button" class="btn btn-dark" onclick="openModalTambahPenyewa()">
                            <span class="d-flex align-items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                    class="bi bi-plus-lg" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2" />
                                </svg>
                                Penyewa
                            </span>
                        </button>
                    </div>
                @endif

                <div class="card border-0">
                    <div class="card-body p-0">
                        <table class="table table-hover not-va m-0">
                            <tbody>
                                <tr>
                                    <th scope="row" class="text-left bg-green text-light" colspan="3">Informasi Kamar
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-left">Nomor Kamar</th>
                                    <th scope="row" class="text-right">:</th>
                                    <th scope="row" class="text-left fw-bold text-success">{{ $asrama->nomor_kamar }}
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-left">Tipe Kamar</th>
                                    <th scope="row" class="text-right">:</th>
                                    <th scope="row" class="text-left fw-bold">{{ $asrama->tipekamar }}
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-left">Kapasitas</th>
                                    <th scope="row" class="text-right">:</th>
                                    <th scope="row" class="text-left fw-bold">
                                        {{ $asrama->tipekamars->kapasitas }} Mahasiswa
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-left">Jumlah Mahasiswa</th>
                                    <th scope="row" class="text-right">:</th>
                                    <th scope="row" class="text-left fw-bold">
                                        {{ $asrama->jumlah_mahasiswa }} Mahasiswa
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-left bg-green text-light" colspan="3">Informasi
                                        Penyewa</th>
                                </tr>
                                @if ($asramadetail->count() > 0)
                                    @foreach ($asramadetail as $row)
                                        <tr>
                                            <th scope="row" class="text-left">Nama Penyewa</th>
                                            <th scope="row" class="text-right">:</th>
                                            <th scope="row" class="text-left fw-bold text-success">
                                                {{ $row->penyewas->namalengkap }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="text-left">KTP</th>
                                            <th scope="row" class="text-right">:</th>
                                            <th scope="row" class="text-left">
                                                <a href="{{ asset('img/ktp/' . $row->penyewas->fotoktp) }}" class="fw-bold"
                                                    target="_blank">Lihat File</a>
                                            </th>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <th scope="row" class="text-center" colspan="3">Penyewa Kosong</th>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscripts')
    <script>
        // tambah penyewa
        function openModalTambahPenyewa() {
            $("#universalModalContent").empty();
            $("#universalModalContent").addClass("modal-dialog-centered");
            $("#universalModalContent").append(`
            <div class="modal-content">
                <div class="modal-body">
                    <div class="loading">
                        <span class="dots pulse1"></span>
                        <span class="dots pulse2"></span>
                        <span class="dots pulse3"></span>
                    </div>
                </div>
            </div>
            `);

            $("#universalModal").modal("show");

            setTimeout(function() {
                $("#universalModalContent").html(
                    `
                    <form class="modal-content" onsubmit="requestTambahPenyewa(event)" autocomplete="off" enctype="multipart/form-data" id="tambahpenyewa">
                        <input type="hidden" name="__token" value="` +
                    $("meta[name='csrf-token']").attr("content") +
                    `" id="token">
                        <input type="hidden" name="pembayaran_id" value="` + pembayaran_id + `" id="pembayaran_id">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="universalModalLabel">Tambah Penyewa</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="noktp" class="form-label fw-bold">No KTP <sup class="red">*</sup></label>
                                <input type="text" class="form-control" name="noktp" id="noktp" oninput="onNoKtp(event)">
                                <span class="text-danger" id="errorNoKTP"></span>
                            </div>
                            <div class="mb-3">
                                <label for="namalengkap" class="form-label fw-bold">Nama Lengkap <sup class="red">*</sup></label>
                                <input type="text" class="form-control" name="namalengkap" id="namalengkap">
                                <span class="text-danger" id="errorNamaLengkap"></span>
                            </div>
                            <div class="mb-3">
                                <label for="nohp" class="form-label fw-bold">No HP <sup class="red">*</sup></label>
                                <input type="text" class="form-control" name="nohp" id="nohp">
                                <span class="text-danger" id="errorNoHP"></span>
                            </div>
                            <div class="mb-3">
                                <label for="alamat" class="form-label fw-bold">Alamat <sup class="red">*</sup></label>
                                <textarea class="form-control" name="alamat" id="alamat"></textarea>
                                <span class="text-danger" id="errorAlamat"></span>
                            </div>
                            <div class="mb-3">
                                <label for="fotoktp" class="form-label fw-bold">Foto KTP <sup class="red">*</sup></label>
                                <input type="file" class="form-control" name="fotoktp" id="fotoktp">
                                <span class="text-danger" id="errorFotoKTP"></span>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-success w-100" id="btnRequest">
                                    Ya
                                </button>
                            </div>
                        </div>
                    </form>
                `
                );

                // Money
                $('.formatrupiah').maskMoney({
                    allowNegative: false,
                    precision: 0,
                    thousands: '.'
                });
            }, 1000);
        }
    </script>
@endpush
