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

                @if ($tipekamar->kapasitas != $pembayaran->jumlah_penyewa)
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
                                    <th scope="row" class="text-left fw-bold text-success">{{ $kamar->nomor_kamar }}</th>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-left">Tipe Kamar</th>
                                    <th scope="row" class="text-right">:</th>
                                    <th scope="row" class="text-left">
                                        {{ DB::table('tipekamars')->where('id', $kamar->tipekamar_id)->first()->tipekamar }}
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-left">Tanggal Masuk</th>
                                    <th scope="row" class="text-right">:</th>
                                    <th scope="row" class="text-left">
                                        {{ \Carbon\Carbon::parse($pembayaran->tanggal_masuk)->translatedFormat('l, Y-m-d H:i:s') }}
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-left">Tanggal Keluar</th>
                                    <th scope="row" class="text-right">:</th>
                                    <th scope="row" class="text-left">
                                        {{ \Carbon\Carbon::parse($pembayaran->tanggal_keluar)->translatedFormat('l, Y-m-d H:i:s') }}
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-left">Jenis Sewa</th>
                                    <th scope="row" class="text-right">:</th>
                                    <th scope="row" class="text-left">{{ $pembayaran->jenissewa }}
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-left">Mitra</th>
                                    <th scope="row" class="text-right">:</th>
                                    <th scope="row" class="text-left">
                                        {{ DB::table('mitras')->where('id', $pembayaran->mitra_id)->first()->mitra }}
                                    </th>
                                </tr>
                                @if ($pembayaran->diskon != 0)
                                    <tr>
                                        <th scope="row" class="text-left">Harga Kamar</th>
                                        <th scope="row" class="text-right">:</th>
                                        <th scope="row" class="text-left">RP.
                                            {{ number_format($pembayaran->jumlah_pembayaran, '0', '.', '.') }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-left">Diskon</th>
                                        <th scope="row" class="text-right">:</th>
                                        <th scope="row" class="text-left">15 %</th>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-left">Potongan Harga</th>
                                        <th scope="row" class="text-right">:</th>
                                        <th scope="row" class="text-left">RP.
                                            {{ number_format($pembayaran->potongan_harga, '0', '.', '.') }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-left">Total Pembayaran</th>
                                        <th scope="row" class="text-right">:</th>
                                        <th scope="row" class="text-left">RP.
                                            {{ number_format($pembayaran->jumlah_pembayaran - $pembayaran->potongan_harga, '0', '.', '.') }}
                                        </th>
                                    </tr>
                                @else
                                    <tr>
                                        <th scope="row" class="text-left">Harga Kamar</th>
                                        <th scope="row" class="text-right">:</th>
                                        <th scope="row" class="text-left">RP.
                                            {{ number_format($pembayaran->jumlah_pembayaran, '0', '.', '.') }}
                                        </th>
                                    </tr>
                                @endif
                                <tr>
                                    <th scope="row" class="text-left">Total Bayar</th>
                                    <th scope="row" class="text-right">:</th>
                                    <th scope="row" class="text-left">RP.
                                        {{ number_format($pembayaran->total_bayar, '0', '.', '.') }}
                                    </th>
                                </tr>
                                @if ($pembayaran->status_pembayaran == 'pending')
                                    <tr>
                                        <th scope="row" class="text-left">Kurang Bayar</th>
                                        <th scope="row" class="text-right">:</th>
                                        <th scope="row" class="text-left">RP.
                                            {{ number_format($pembayaran->kurang_bayar, '0', '.', '.') }}
                                        </th>
                                    </tr>
                                @endif
                                <tr>
                                    <th scope="row" class="text-left">Status Pembayaran</th>
                                    <th scope="row" class="text-right">:</th>
                                    <th scope="row" class="text-left">
                                        @if ($pembayaran->status_pembayaran == 'completed')
                                            <strong class='badge bg-success text-light fw-bold'>Lunas</strong>
                                        @elseif ($pembayaran->status_pembayaran == 'pending')
                                            <strong class='badge bg-warning text-light fw-bold'>Booking / Belum
                                                Lunas</strong>
                                        @endif
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-left bg-green text-light" colspan="3">Informasi
                                        Penyewa</th>
                                </tr>
                                @foreach ($pembayaran_detail as $row)
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
        const pembayaran_id = "{{ $pembayaran->id }}"

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

        function requestTambahPenyewa(e) {
            e.preventDefault()

            $("#btnRequest").prop("disabled", true)

            var formData = new FormData($('#tambahpenyewa')[0]);
            formData.append("pembayaran_id", pembayaran_id);

            $.ajax({
                url: "{{ route('dasbor.tambahpenyewa') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.message == "success") {
                        Swal.fire({
                            title: "Berhasil",
                            text: "Penyewa berhasil ditambahkan",
                            icon: "success"
                        })

                        $('#tambahpenyewa')[0].reset(); // Resets all form fields

                        // no ktp
                        $("#noktp").removeClass("is-invalid")
                        $("#errorNoKTP").text("")

                        // nama lengkap
                        $("#namalengkap").removeClass("is-invalid")
                        $("#errorNamaLengkap").text("")

                        // no hp
                        $("#nohp").removeClass("is-invalid")
                        $("#errorNoHP").text("")

                        // alamat
                        $("#alamat").removeClass("is-invalid")
                        $("#errorAlamat").text("")

                        // foto ktp
                        $("#fotoktp").removeClass("is-invalid")
                        $("#errorFotoKTP").text("")

                        setTimeout(function() {
                            location.reload()
                        }, 1000)
                    } else if (response.message == "errorvalidation") {
                        $("#btnRequest").prop("disabled", false)

                        // no ktp
                        if (response.dataError.hasOwnProperty('noktp')) {
                            $("#noktp").addClass("is-invalid")
                            $("#errorNoKTP").text(response.dataError['noktp'])
                        } else {
                            $("#noktp").removeClass("is-invalid")
                            $("#errorNoKTP").text("")
                        }

                        // nama lengkap
                        if (response.dataError.hasOwnProperty('namalengkap')) {
                            $("#namalengkap").addClass("is-invalid")
                            $("#errorNamaLengkap").text(response.dataError['namalengkap'])
                        } else {
                            $("#namalengkap").removeClass("is-invalid")
                            $("#errorNamaLengkap").text("")
                        }

                        // no hp
                        if (response.dataError.hasOwnProperty('nohp')) {
                            $("#nohp").addClass("is-invalid")
                            $("#errorNoHP").text(response.dataError['nohp'])
                        } else {
                            $("#nohp").removeClass("is-invalid")
                            $("#errorNoHP").text("")
                        }

                        // alamat
                        if (response.dataError.hasOwnProperty('alamat')) {
                            $("#alamat").addClass("is-invalid")
                            $("#errorAlamat").text(response.dataError['alamat'])
                        } else {
                            $("#alamat").removeClass("is-invalid")
                            $("#errorAlamat").text("")
                        }

                        // alamat
                        if (response.dataError.hasOwnProperty('fotoktp')) {
                            $("#fotoktp").addClass("is-invalid")
                            $("#errorFotoKTP").text(response.dataError['fotoktp'])
                        } else {
                            $("#fotoktp").removeClass("is-invalid")
                            $("#errorFotoKTP").text("")
                        }
                    } else if (response.message == "opps") {
                        $("#btnRequest").prop("disabled", false)

                        Swal.fire({
                            title: "Gagal",
                            text: "Penyewa sudah terdaftar di kamar ini",
                            icon: "error"
                        })
                    } else {
                        $("#btnRequest").prop("disabled", false)

                        Swal.fire({
                            title: "Opps, terjadi kesalahan",
                            icon: "error"
                        })
                    }
                },
            });
        }
    </script>
@endpush
