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
                        <li class="breadcrumb-item active" aria-current="page">Penyewaan Kamar Asrama</li>
                    </ol>
                </nav>

                <div class="row">
                    <div class="col-xl-9 mb-3">
                        <div class="card border-0">
                            <div class="card-body">
                                <div class="mb-3">
                                    <button type="button" class="btn btn-dark d-flex align-items-center gap-2"
                                        data-bs-toggle="modal" data-bs-target="#gambarktpModal" id="openCamera">
                                        <i class="bi bi-camera"></i>
                                        Gambar KTP
                                    </button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="gambarktpModal" tabindex="-1"
                                        aria-labelledby="gambarktpModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="gambarktpModalLabel">Ambil Gambar KTP
                                                    </h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <video id="video" autoplay
                                                        style="display: none; width: 100%;"></video>
                                                    {{-- <button id="capture" style="display: none;">Capture</button> --}}
                                                    <canvas id="canvas" width="640" height="480"
                                                        style="display: none;"></canvas>
                                                </div>
                                                <div class="modal-footer justify-content-center">
                                                    <button type="button"
                                                        class="btn btn-dark d-flex align-items-center gap-2" id="capture"
                                                        style="display: none">
                                                        <i class="bi bi-camera"></i>
                                                        Tangkap
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <form action="{{ route('asrama.mahasiswa.posttambahpenyewa') }}" autocomplete="off"
                                    method="POST" enctype="multipart/form-data">
                                    @csrf
                                    {{-- data diri --}}
                                    <div class="card border-2 border-dark mb-3">
                                        <div class="card-header bg-dark text-light">
                                            Data Diri
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                {{-- No KTP --}}
                                                <div class="col-lg-6 mb-3">
                                                    <label for="noktp" class="form-label fw-bold">No KTP</label>
                                                    <input type="number"
                                                        class="form-control @error('noktp') is-invalid @enderror"
                                                        name="noktp" id="noktp" placeholder="Masukkan No KTP"
                                                        value="{{ old('noktp') }}" oninput="onNoKtp(event)">
                                                    @error('noktp')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                                {{-- Nama --}}
                                                <div class="col-lg-6 mb-3">
                                                    <label for="namalengkap" class="form-label fw-bold">Nama Lengkap</label>
                                                    <input type="text"
                                                        class="form-control @error('namalengkap') is-invalid @enderror"
                                                        name="namalengkap" id="namalengkap"
                                                        placeholder="Masukkan Nama Lengkap"
                                                        value="{{ old('namalengkap') }}">
                                                    @error('namalengkap')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row">
                                                {{-- No HP --}}
                                                <div class="col-lg-6 mb-3">
                                                    <label for="nohp" class="form-label fw-bold">No HP / WA</label>
                                                    <input type="text"
                                                        class="form-control @error('nohp') is-invalid @enderror"
                                                        name="nohp" id="nohp" placeholder="Masukkan No HP / WA"
                                                        value="{{ old('nohp') }}">
                                                    @error('nohp')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                                {{-- Foto KTP --}}
                                                <div class="col-lg-6 mb-3">
                                                    <label for="fotoktp" class="form-label fw-bold">Foto KTP</label>
                                                    <input type="file"
                                                        class="form-control @error('fotoktp') is-invalid @enderror"
                                                        name="fotoktp" id="fotoktp">
                                                    @error('fotoktp')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row">
                                                {{-- Alamat --}}
                                                <div class="col-lg-6">
                                                    <label for="alamat" class="form-label fw-bold">Alamat KTP</label>
                                                    <textarea class="form-control @error('alamat') is-invalid @enderror" name="alamat" id="alamat">{{ old('alamat') }}</textarea>
                                                    @error('alamat')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                                {{-- jenis kelamin --}}
                                                <div class="col-lg-6 mb-3">
                                                    <label for="jenis_kelamin" class="form-label fw-bold">Jenis
                                                        Kelamin</label>
                                                    <select
                                                        class="form-select form-select-2 @error('jenis_kelamin') is-invalid @enderror"
                                                        name="jenis_kelamin" id="jenis_kelamin" style="width: 100%;">
                                                        <option>Pilih Jenis Kelamin</option>
                                                        <option value="L"
                                                            {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-Laki
                                                        </option>
                                                        <option value="P"
                                                            {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan
                                                        </option>
                                                    </select>
                                                    @error('jenis_kelamin')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- penginapan kamar --}}
                                    <div class="card border-2 border-dark mb-3">
                                        <div class="card-header bg-dark text-light">
                                            Kamar Asrama
                                        </div>
                                        <div class="card-body">
                                            {{-- Kamar --}}
                                            <div class="mb-3">
                                                <label for="lokasi" class="form-label fw-bold">Kamar</label>
                                                <select
                                                    class="form-select form-select-2 @error('lokasi') is-invalid @enderror"
                                                    name="lokasi" id="lokasi" style="width: 100%;">
                                                    <option>Pilih Kamar</option>
                                                    @foreach ($kamar as $row)
                                                        <option value="{{ $row->id }}"
                                                            {{ old('lokasi_id') == $row->id ? 'selected' : '' }}>
                                                            Lantai {{ $row->lantai_id }} |
                                                            Nomor Kamar: {{ $row->nomor_kamar }} |
                                                            Tipe Kamar: {{ $row->tipekamars->tipekamar }} |
                                                            Sisa:
                                                            {{ intval($row->kapasitas) - intval($row->jumlah_penyewa) }}
                                                            Mahasiswa
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('lokasi')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            {{-- Tanggal Masuk --}}
                                            <div>
                                                <label for="tanggalmasuk" class="form-label fw-bold">Tanggal
                                                    Masuk</label>
                                                <input type="datetime-local"
                                                    class="form-control @error('tanggalmasuk') is-invalid @enderror without_ampm"
                                                    name="tanggalmasuk" id="tanggalmasuk"
                                                    value="{{ old('tanggalmasuk') }}">
                                                @error('tanggalmasuk')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    {{-- pembayaran --}}
                                    <div class="card border-2 border-dark mb-3">
                                        <div class="card-header bg-dark text-light">
                                            Pembayaran
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                {{-- total bayar --}}
                                                <div class="col-lg-6 mb-3">
                                                    <label for="total_bayar" class="form-label fw-bold">Total Bayar</label>
                                                    <div class="input-group" style="z-index: 0;">
                                                        <span
                                                            class="input-group-text bg-success text-light fw-bold">RP</span>
                                                        <input type="text"
                                                            class="form-control formatrupiah @error('total_bayar') is-invalid @enderror"
                                                            name="total_bayar" id="total_bayar" placeholder="0">
                                                        @error('total_bayar')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                {{-- upload bukti pembayaran --}}
                                                <div class="col-lg-6 mb-3">
                                                    <label for="bukti_pembayaran" class="form-label fw-bold">File Bukti
                                                        Pembayaran</label>
                                                    <input type="file"
                                                        class="form-control @error('bukti_pembayaran') is-invalid @enderror"
                                                        name="bukti_pembayaran" id="bukti_pembayaran"
                                                        value="{{ old('bukti_pembayaran') }}">
                                                    @error('bukti_pembayaran')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            {{-- tipe pembayaran --}}
                                            <div>
                                                <label for="cash" class="form-label fw-bold">
                                                    Tipe Pembayaran
                                                </label>
                                                <div class="row">
                                                    <div class="col-lg-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio"
                                                                name="metode_pembayaran" id="none" value="None"
                                                                checked>
                                                            <label class="form-check-label" for="none">
                                                                Tidak Ada
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio"
                                                                name="metode_pembayaran" id="cash" value="Cash">
                                                            <label class="form-check-label" for="cash">
                                                                Cash
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio"
                                                                name="metode_pembayaran" id="debit" value="Debit">
                                                            <label class="form-check-label" for="debit">
                                                                Debit
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio"
                                                                name="metode_pembayaran" id="qris" value="QRIS">
                                                            <label class="form-check-label" for="qris">
                                                                QRIS
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio"
                                                                name="metode_pembayaran" id="transfer" value="Transfer">
                                                            <label class="form-check-label" for="transfer">
                                                                Transfer
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <button type="submit" class="btn btn-success w-100" id="btn-submit">
                                                <strong class="d-block">
                                                    Sewa
                                                </strong>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    {{-- Detail Penyewaan --}}
                    <div class="col-xl-3 mb-3 detailpenyewaan">
                        <div class="card border-0">
                            <div class="card-header bg-green text-light text-center fw-bold">
                                Detail Penyewaan
                            </div>
                            <div class="card-body">
                                <table style="width: 100%">
                                    <tr>
                                        <td>Jenis Sewa</td>
                                        <td>:</td>
                                        <td style="text-align: right;">Bulanan</td>
                                    </tr>
                                    <tr>
                                        <td>Mitra</td>
                                        <td>:</td>
                                        <td style="text-align: right;">Asrama</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            <hr class="border-2" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Total Pembayaran</td>
                                        <td>:</td>
                                        <td style="text-align: right;"><strong id="d-totalpembayaran">RP. 500.000</strong>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscripts')
    <script>
        $(document).ready(function() {
            $("#btn-submit").on("click", function() {
                $("#btn-submit").html(`
                    <div class="spinner-border spinner-border-sm" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                `)

                setTimeout(function() {
                    $("#btn-submit").prop("disabled", true)
                }, 1);
            })

            const openCameraButton = document.getElementById('openCamera');
            const video = document.getElementById('video');
            const captureButton = document.getElementById('capture');
            const canvas = document.getElementById('canvas');
            const photoFile = document.getElementById('fotoktp');

            openCameraButton.addEventListener('click', () => {
                navigator.mediaDevices.getUserMedia({
                        video: true
                    })
                    .then(stream => {
                        video.style.display = 'block';
                        captureButton.style.display = 'block';
                        video.srcObject = stream;
                    })
                    .catch(err => console.log("Error: " + err));
            });

            captureButton.addEventListener('click', () => {
                const context = canvas.getContext('2d');
                context.drawImage(video, 0, 0, 640, 480);
                canvas.toBlob(blob => {
                    const file = new File([blob], "fotoktp.png", {
                        type: 'image/png'
                    });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    photoFile.files = dataTransfer.files;
                });

                $("#gambarktpModal").modal("hide")
            });
        })

        function onNoKtp(event) {
            const noktp = event.target.value;

            if (noktp.length == 16) {
                var formData = new FormData();
                formData.append("noktp", noktp);

                $.ajax({
                    url: "{{ route('asrama.mahasiswa.getrequestformsewaonktp') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.message == "success") {
                            $("#namalengkap").val(response.data['namalengkap'])
                            $("#nohp").val(response.data['nohp'])
                            $("#alamat").val(response.data['alamat'])
                        }
                    },
                });
            }
        }
    </script>
@endpush
