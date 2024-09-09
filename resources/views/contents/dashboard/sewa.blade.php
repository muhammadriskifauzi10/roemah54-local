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
                        <li class="breadcrumb-item active" aria-current="page">Penyewaan Kamar</li>
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

                                <form action="{{ route('postsewa') }}" autocomplete="off" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        {{-- No KTP --}}
                                        <div class="col-lg-4 mb-3">
                                            <label for="noktp" class="form-label fw-bold">No KTP</label>
                                            <input type="number" class="form-control @error('noktp') is-invalid @enderror"
                                                name="noktp" id="noktp" placeholder="Masukkan No KTP"
                                                value="{{ old('noktp') }}" oninput="onNoKtp(event)">
                                            @error('noktp')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        {{-- Nama --}}
                                        <div class="col-lg-4 mb-3">
                                            <label for="namalengkap" class="form-label fw-bold">Nama Lengkap</label>
                                            <input type="text"
                                                class="form-control @error('namalengkap') is-invalid @enderror"
                                                name="namalengkap" id="namalengkap" placeholder="Masukkan Nama Lengkap"
                                                value="{{ old('namalengkap') }}">
                                            @error('namalengkap')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        {{-- No HP --}}
                                        <div class="col-lg-4 mb-3">
                                            <label for="nohp" class="form-label fw-bold">No HP / WA</label>
                                            <input type="text" class="form-control @error('nohp') is-invalid @enderror"
                                                name="nohp" id="nohp" placeholder="Masukkan No HP / WA"
                                                value="{{ old('nohp') }}">
                                            @error('nohp')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        {{-- Lantai --}}
                                        <div class="col-lg-4 mb-3">
                                            <label for="lantai" class="form-label fw-bold">Lantai</label>
                                            <select class="form-select form-select-2 @error('lantai') is-invalid @enderror"
                                                name="lantai" id="lantai" style="width: 100%;"
                                                onchange="selectLantai()">
                                                <option>Pilih Lantai</option>
                                                @foreach ($lantai as $row)
                                                    <option value="{{ $row->id }}">
                                                        {{ $row->namalantai }}</option>
                                                @endforeach
                                            </select>
                                            @error('lantai')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        {{-- Kamar --}}
                                        <div class="col-lg-4 mb-3">
                                            <label for="kamar" class="form-label fw-bold">Kamar</label>
                                            <select class="form-select form-select-2 @error('kamar') is-invalid @enderror"
                                                name="kamar" id="kamar" style="width: 100%;"
                                                onchange="selectKamar()">
                                                <option>Pilih Kamar</option>
                                            </select>
                                            @error('kamar')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        {{-- Jenis Sewa --}}
                                        <div class="col-lg-4 mb-3">
                                            <label for="jenissewa" class="form-label fw-bold">Jenis Sewa</label>
                                            <select
                                                class="form-select form-select-2 @error('jenissewa') is-invalid @enderror"
                                                name="jenissewa" id="jenissewa" style="width: 100%;"
                                                onchange="selectJenisSewa()">
                                                <option>Pilih Jenis Sewa</option>
                                            </select>
                                            @error('jenissewa')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        {{-- mitra --}}
                                        <div class="col-lg-4 mb-3">
                                            <label for="mitra" class="form-label fw-bold">Mitra</label>
                                            <select class="form-select form-select-2 @error('mitra') is-invalid @enderror"
                                                name="mitra" id="mitra" style="width: 100%;"
                                                onchange="selectMitra()">
                                                <option>Pilih Mitra</option>
                                            </select>
                                            @error('mitra')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        {{-- Tanggal Masuk --}}
                                        <div class="col-lg-4 mb-3">
                                            <label for="tanggalmasuk" class="form-label fw-bold">Tanggal Masuk</label>
                                            <input type="datetime-local"
                                                class="form-control @error('tanggalmasuk') is-invalid @enderror without_ampm"
                                                name="tanggalmasuk" id="tanggalmasuk" value="{{ old('tanggalmasuk') }}">
                                            @error('tanggalmasuk')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        {{-- Jumlah Hari --}}
                                        <div class="col-lg-4 mb-3">
                                            <label for="jumlahhari" class="form-label fw-bold">Jumlah Hari</label>
                                            <div class="input-group" style="z-index: 0;">
                                                <input type="number" class="form-control" name="jumlahhari"
                                                    id="jumlahhari" value="{{ old('jumlahhari') }}"
                                                    oninput="jumlahHari()">
                                                <span class="input-group-text bg-success text-light fw-bold">Hari</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        {{-- Alamat --}}
                                        <div class="col-lg-4 mb-3">
                                            <label for="alamat" class="form-label fw-bold">Alamat KTP</label>
                                            <textarea class="form-control @error('alamat') is-invalid @enderror" name="alamat" id="alamat">{{ old('alamat') }}</textarea>
                                            @error('alamat')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        {{-- Foto KTP --}}
                                        <div class="col-lg-4 mb-3">
                                            <label for="fotoktp" class="form-label fw-bold">Foto KTP</label>
                                            <input type="file"
                                                class="form-control @error('fotoktp') is-invalid @enderror" name="fotoktp"
                                                id="fotoktp">
                                            @error('fotoktp')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        {{-- total bayar --}}
                                        <div class="col-lg-4 mb-3">
                                            <label for="total_bayar" class="form-label fw-bold">Total
                                                Bayar</label>
                                            <div class="input-group" style="z-index: 0;">
                                                <span class="input-group-text bg-success text-light fw-bold">RP</span>
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
                                    </div>

                                    {{-- tipe pembayaran --}}
                                    <div class="mb-3">
                                        <label for="cash" class="form-label fw-bold">
                                            Tipe Pembayaran
                                        </label>
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio"
                                                        name="metode_pembayaran" id="cash" value="Cash" checked>
                                                    <label class="form-check-label" for="cash">
                                                        Cash
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio"
                                                        name="metode_pembayaran" id="debit" value="Debit">
                                                    <label class="form-check-label" for="debit">
                                                        Debit
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio"
                                                        name="metode_pembayaran" id="qris" value="QRIS">
                                                    <label class="form-check-label" for="qris">
                                                        QRIS
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
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
                    <div class="col-xl-3 mb-3">
                        <div class="card border-0">
                            <div class="card-header bg-green text-light text-center fw-bold">
                                Detail Penyewaan
                            </div>
                            <div class="card-body">
                                <table style="width: 100%">
                                    <tr>
                                        <td>Lantai</td>
                                        <td>:</td>
                                        <td style="text-align: right;" id="d-lantai">-</td>
                                    </tr>
                                    <tr>
                                        <td>Nomor Kamar</td>
                                        <td>:</td>
                                        <td style="text-align: right;" id="d-nomorkamar">-</td>
                                    </tr>
                                    <tr>
                                        <td>Tipe Kamar</td>
                                        <td>:</td>
                                        <td style="text-align: right;" id="d-tipekamar">-</td>
                                    </tr>
                                    <tr>
                                        <td>Jenis Sewa</td>
                                        <td>:</td>
                                        <td style="text-align: right;" id="d-jenissewa">-</td>
                                    </tr>
                                    <tr>
                                        <td>Mitra</td>
                                        <td>:</td>
                                        <td style="text-align: right;" id="d-mitra">-</td>
                                    </tr>
                                    <tr>
                                        <td>Harga Kamar</td>
                                        <td>:</td>
                                        <td style="text-align: right;"><strong id="d-hargakamar">RP.
                                                0</strong></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            <hr class="border-2" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Diskon</td>
                                        <td>:</td>
                                        <td style="text-align: right;"><strong class="red" id="d-diskon">0
                                                %</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Potongan Harga</td>
                                        <td>:</td>
                                        <td style="text-align: right;"><strong id="d-potongan">RP.
                                                0</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Total Pembayaran</td>
                                        <td>:</td>
                                        <td style="text-align: right;"><strong id="d-totalpembayaran">RP.
                                                0</strong></td>
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
                    url: "{{ route('penyewaankamar.getrequestformsewaonktp') }}",
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

        function selectLantai() {
            // kamar
            $("#kamar").empty()
            $("#kamar").append(`
                <option>Pilih Kamar</option>
            `)
            // jenis sewa
            $("#jenissewa").empty()
            $("#jenissewa").append(`
            <option>Pilih Jenis Sewa</option>
            `)
            // mitra
            $("#mitra").empty()
            $("#mitra").append(`
            <option>Pilih Mitra</option>
            `)
            var formData = new FormData();
            formData.append("lantai", $("#lantai").val());

            $.ajax({
                url: "{{ route('getselectlantaikamar') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.message == "success") {
                        $("#d-lantai").text(response.data['namalantai']);
                        $("#d-nomorkamar").text("-");
                        $("#d-tipekamar").text("-");
                        $("#d-jenissewa").text("-");
                        $("#d-mitra").text("-");
                        $("#d-hargakamar").text("RP. 0");
                        $("#d-diskon").text("0 %");
                        $("#d-potongan").text("RP. 0");
                        $("#d-totalpembayaran").text("RP. 0");

                        $("#kamar").append(response.data['dataHTML'].trim())
                    } else {
                        $("#d-lantai").text("-")
                        $("#d-nomorkamar").text("-");
                        $("#d-tipekamar").text("-");
                        $("#d-jenissewa").text("-");
                        $("#d-mitra").text("-");
                        $("#d-hargakamar").text("RP. 0");
                        $("#d-diskon").text("0 %");
                        $("#d-potongan").text("RP. 0");
                        $("#d-totalpembayaran").text("RP. 0");
                    }
                },
            });
        }

        function selectKamar() {
            // jenis sewa
            $("#jenissewa").empty()
            $("#jenissewa").append(`
                <option>Pilih Jenis Sewa</option>
            `)
            // mitra
            $("#mitra").empty()
            $("#mitra").append(`
                <option>Pilih Mitra</option>
            `)
            var formData = new FormData();
            formData.append("kamar", $("#kamar").val());

            $.ajax({
                url: "{{ route('getselectkamar') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.message == "success") {
                        $("#d-nomorkamar").text(response.data['nomorkamar']);
                        $("#d-tipekamar").text(response.data['tipekamar']);
                        $("#d-jenissewa").text("-");
                        $("#d-mitra").text("-");
                        $("#d-hargakamar").text("RP. 0");
                        $("#d-diskon").text("0 %");
                        $("#d-potongan").text("RP. 0");
                        $("#d-totalpembayaran").text("RP. 0");
                        $("#jenissewa").append(response.data['dataHTML'].trim())
                    } else {
                        $("#d-nomorkamar").text("-");
                        $("#d-tipekamar").text("-");
                        $("#d-jenissewa").text("-");
                        $("#d-mitra").text("-");
                        $("#d-hargakamar").text("RP. 0");
                        $("#d-diskon").text("0 %");
                        $("#d-potongan").text("RP. 0");
                        $("#d-totalpembayaran").text("RP. 0");
                    }
                },
            });
        }

        function selectJenisSewa() {
            var formData = new FormData();
            formData.append("kamar", $("#kamar").val());
            formData.append("jenissewa", $("#jenissewa").val());

            $.ajax({
                url: "{{ route('getselectjenissewa') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.message == "success") {
                        if ($("#jenissewa").val() == "Pilih Jenis Sewa") {
                            $("#d-jenissewa").text("-");

                            // mitra
                            $("#mitra").empty()
                            $("#mitra").append(`
                                <option>Pilih Mitra</option>
                                `)

                            // jumlah hari
                            jumlahHari()
                        } else {
                            $("#d-jenissewa").text(response.data['jenissewa']);

                            if ($("#mitra").val() != "Pilih Mitra") {
                                // jumlah hari
                                jumlahHari()
                            } else {
                                // mitra
                                $("#mitra").empty()
                                $("#mitra").append(`
                                <option>Pilih Mitra</option>
                                `)

                                // mitra
                                $("#mitra").append(response.data['dataHTML'].trim())

                                // jumlah hari
                                jumlahHari()
                            }
                        }
                    } else {
                        $("#d-jenissewa").text("-");
                        $("#d-mitra").text("-");
                        $("#d-hargakamar").text("RP. 0");
                        $("#d-diskon").text("0 %");
                        $("#d-potongan").text("RP. 0");
                        $("#d-totalpembayaran").text("RP. 0");
                    }
                },
            });
        }

        function selectMitra(jumlahhari = 0) {
            var formData = new FormData();
            formData.append("kamar", $("#kamar").val());
            formData.append("jenissewa", $("#jenissewa").val());
            formData.append("mitra", $("#mitra").val());

            // jumlah hari
            if ($("#jumlahhari").val() > 0) {
                jumlahhari = parseInt($("#jumlahhari").val())
            }

            formData.append("jumlahhari", jumlahhari);

            $.ajax({
                url: "{{ route('getselectmitra') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.message == "success") {
                        $("#d-mitra").text(response.data['mitra']);

                        $("#d-hargakamar").text("RP. " + parseInt(response.data['hargakamar']).toLocaleString()
                            .replaceAll(",",
                                "."));
                        $("#d-diskon").text(response.data['diskon'] + " %");
                        $("#d-potongan").text("RP. " + parseInt(response.data['potongan']).toLocaleString()
                            .replaceAll(",",
                                "."));
                        $("#d-totalpembayaran").text("RP. " + parseInt(response.data['totalpembayaran'])
                            .toLocaleString()
                            .replaceAll(",",
                                "."));
                    } else {
                        $("#d-mitra").text("-");
                        $("#d-hargakamar").text("RP. 0");
                        $("#d-diskon").text("0 %");
                        $("#d-potongan").text("RP. 0");
                        $("#d-totalpembayaran").text("RP. 0");
                    }
                },
            });
        }

        function jumlahHari() {
            var jenissewa = $("#jenissewa").val()
            let jumlahhari = $("#jumlahhari").val()

            if (jumlahhari <= 0) {
                $("#jumlahhari").val("")
                jumlahhari = 0
            }

            if (jenissewa == "Harian") {
                selectMitra(parseInt(jumlahhari))
            } else {
                selectMitra(0)
                $("#jumlahhari").val("")
            }
        }
    </script>
@endpush
