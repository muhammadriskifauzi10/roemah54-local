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
                        <li class="breadcrumb-item"><a href="{{ route('harga') }}">Kembali</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tambah Harga</li>
                    </ol>
                </nav>

                <div class="mb-3">
                    <button type="button" class="btn btn-dark d-flex align-items-center justify-content-center gap-1"
                        onclick="openModalTambahTipeKamar()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                            class="bi bi-plus-lg" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2" />
                        </svg>
                        Tipe Kamar</button>
                </div>

                <div class="card border-0">
                    <div class="card-body">
                        <form action="{{ route('postharga') }}" class="row" autocomplete="off" method="POST">
                            @csrf
                            <div class="row">
                                {{-- tipe kamar --}}
                                <div class="mb-3">
                                    <label for="tipe" class="form-label fw-bold">Tipe Kamar</label>
                                    <select class="form-select form-select-2 @error('tipe') is-invalid @enderror"
                                        name="tipe" id="tipe" style="width: 100%;" onchange="selectHarga()">
                                        <option>Pilih Tipe Kamar</option>
                                        @foreach ($tipekamar as $row)
                                            <option value="{{ $row->id }}"
                                                {{ old('tipe') == $row->id ? 'selected' : '' }}>
                                                {{ $row->tipekamar }}</option>
                                        @endforeach
                                    </select>
                                    @error('tipe')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- mitra --}}
                                <div class="mb-3">
                                    <label for="mitra" class="form-label fw-bold">Mitra</label>
                                    <select class="form-select form-select-2 @error('mitra') is-invalid @enderror"
                                        name="mitra" id="mitra" style="width: 100%;" onchange="selectHarga()">>
                                        <option>Pilih Mitra</option>
                                        @foreach ($mitra as $row)
                                            <option value="{{ $row->id }}"
                                                {{ old('mitra') == $row->id ? 'selected' : '' }}>
                                                {{ $row->mitra }}</option>
                                        @endforeach
                                    </select>
                                    @error('mitra')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="row">
                                    {{-- harian --}}
                                    <div class="col-sm-3 mb-3">
                                        <label for="harian" class="form-label fw-bold">Harga Harian</label>
                                        <div class="input-group" style="z-index: 0;">
                                            <span class="input-group-text bg-success text-light fw-bold">RP</span>
                                            <input type="text"
                                                class="form-control formatrupiah  @error('harian') is-invalid @enderror"
                                                name="harian" id="harian" value="{{ old('harian', 0) }}">
                                            @error('harian')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    {{-- mingguan / 7 hari --}}
                                    <div class="col-sm-3 mb-3">
                                        <label for="mingguan" class="form-label fw-bold">Harga Mingguan / 7 Hari</label>
                                        <div class="input-group" style="z-index: 0;">
                                            <span class="input-group-text bg-success text-light fw-bold">RP</span>
                                            <input type="text"
                                                class="form-control formatrupiah  @error('mingguan') is-invalid @enderror"
                                                name="mingguan" id="mingguan" value="{{ old('mingguan', 0) }}">
                                            @error('mingguan')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    {{-- mingguan / ( > 14 hari ) --}}
                                    <div class="col-sm-3 mb-3">
                                        <label for="hari14" class="form-label fw-bold">Mingguan / ( > 14 Hari)</label>
                                        <div class="input-group" style="z-index: 0;">
                                            <span class="input-group-text bg-success text-light fw-bold">RP</span>
                                            <input type="text"
                                                class="form-control formatrupiah  @error('hari14') is-invalid @enderror"
                                                name="hari14" id="hari14" value="{{ old('hari14', 0) }}">
                                            @error('hari14')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    {{-- bulanan --}}
                                    <div class="col-sm-3 mb-3">
                                        <label for="bulanan" class="form-label fw-bold">Harga Bulanan</label>
                                        <div class="input-group" style="z-index: 0;">
                                            <span class="input-group-text bg-success text-light fw-bold">RP</span>
                                            <input type="text"
                                                class="form-control formatrupiah  @error('bulanan') is-invalid @enderror"
                                                name="bulanan" id="bulanan" value="{{ old('bulanan', 0) }}">
                                            @error('bulanan')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <button type="submit" class="btn btn-success w-100" id="btn-submit">
                                        Simpan
                                    </button>
                                </div>
                            </div>
                        </form>
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
        })

        // Tipe Kamar
        function openModalTambahTipeKamar() {
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
                    <form class="modal-content" onsubmit="requestTambahTipeKamar(event)" autocomplete="off">
                        <input type="hidden" name="__token" value="` +
                    $("meta[name='csrf-token']").attr("content") +
                    `" id="token">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="universalModalLabel">Tambah Tipe Kamar</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div>
                                <label for="tipekamar" class="form-label fw-bold">Tipe Kamar</label>
                                <input type="text" class="form-control" placeholder="Masukkan nama tipe kamar" id="tipekamar">
                                <div class="invalid-feedback" id="errorTipeKamar"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success w-100" id="btnRequest">
                                Ya
                            </button>
                        </div>
                    </form>
                `
                );

                $("#tipekamar").focus()
            }, 1000);
        }
        function requestTambahTipeKamar(e) {
            e.preventDefault()

            if ($("#tipekamar").val() === "") {
                $("#tipekamar").addClass("is-invalid")
                $("#errorTipeKamar").text("Kolom ini wajib diisi")
            } else {
                $("#btnRequest").prop("disabled", true)

                $("#tipekamar").removeClass("is-invalid")
                $("#errorTipeKamar").text("")

                var formData = new FormData();
                formData.append("token", $("#token").val());
                formData.append("tipekamar", $("#tipekamar").val());

                $.ajax({
                    url: "{{ route('posttipekamar') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.message == "success") {
                            Swal.fire({
                                title: "Berhasil",
                                text: "Tipe kamar berhasil ditambahkan",
                                icon: "success"
                            })

                            $("#tipekamar").val("")
                            $("#tipekamar").removeClass("is-invalid")
                            $("#errorTipeKamar").text("")
                            setTimeout(function() {
                                location.reload()
                            }, 1000)
                        } else {
                            $("#tipekamar").addClass("is-invalid")
                            $("#errorTipeKamar").text("Nama kolom ini sudah terdaftar")

                            $("#btnRequest").prop("disabled", false)
                        }
                    },
                });
            }
        }

        function selectHarga() {
            var formData = new FormData();
            formData.append("tipe", $("#tipe").val());
            formData.append("mitra", $("#mitra").val());

            $.ajax({
                url: "{{ route('getselectharga') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.message == "success") {
                        $("#harian").val(response['data'].harian)
                        $("#mingguan").val(response['data'].mingguan)
                        $("#hari14").val(response['data'].hari14)
                        $("#bulanan").val(response['data'].bulanan)
                    } else {
                        $("#harian").val(0)
                        $("#mingguan").val(0)
                        $("#hari14").val(0)
                        $("#bulanan").val(0)
                    }
                },
            });
        }
    </script>
@endpush
