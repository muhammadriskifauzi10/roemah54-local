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
                        <li class="breadcrumb-item active" aria-current="page">Edit Daftar Penyewa</li>
                    </ol>
                </nav>

                <div class="row">
                    <div class="col-xl-12 mb-3">
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

                                <form action="{{ route('daftarpenyewa.update', encrypt($penyewa->id)) }}" autocomplete="off"
                                    method="POST" enctype="multipart/form-data">
                                    @method('PUT')
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
                                                        value="{{ old('noktp', $penyewa->noktp) }}"
                                                        oninput="onNoKtp(event)">
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
                                                        value="{{ old('namalengkap', $penyewa->namalengkap) }}">
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
                                                        value="{{ old('nohp', $penyewa->nohp) }}">
                                                    @error('nohp')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                                {{-- Foto KTP --}}
                                                <div class="col-lg-6 mb-3">
                                                    <label for="fotoktp" class="form-label fw-bold">Foto KTP
                                                        ({!! $fotoktp !!})
                                                    </label>
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
                                                    <textarea class="form-control @error('alamat') is-invalid @enderror" name="alamat" id="alamat">{{ old('alamat', nl2br($penyewa->alamat)) }}</textarea>
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
                                                            {{ old('jenis_kelamin', $penyewa->jenis_kelamin) == 'L' ? 'selected' : '' }}>
                                                            Laki-Laki
                                                        </option>
                                                        <option value="P"
                                                            {{ old('jenis_kelamin', $penyewa->jenis_kelamin) == 'P' ? 'selected' : '' }}>
                                                            Perempuan
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

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <button type="submit" class="btn btn-success w-100" id="btn-submit">
                                                <strong class="d-block">
                                                    Perbarui
                                                </strong>
                                            </button>
                                        </div>
                                    </div>
                                </form>
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
    </script>
@endpush
