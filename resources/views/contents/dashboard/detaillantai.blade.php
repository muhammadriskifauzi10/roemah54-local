@extends('templates.dashboard.main')

@section('mystyles')
    <style>
        .kamar:hover .card {
            background-color: #b7d6ff;
            transition: .3s linear;
        }

        table * {
            vertical-align: baseline;
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
                        <li class="breadcrumb-item"><a href="{{ route('dasbor') }}">Kembali</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail {{ $lantai->namalantai }}</li>
                    </ol>
                </nav>

                @if ($lantai->lokasis->count() > 0)
                    <div class="row">
                        <div class="col-xl-9">
                            <div class="row">
                                @if (App\Models\Lokasi::where('jenisruangan_id', 2)->where('lantai_id', $lantai->id)->where(function ($query) {
                                            $query->where('status', 1)->orWhere('status', 2);
                                        })->count() > 0)
                                    @foreach ($lantai->lokasis as $l)
                                        @foreach (App\Models\Pembayaran::where('lokasi_id', $l->id)->where('status', '<>', 0)->get() as $p)
                                            @if ($p->status_pembayaran == 'completed')
                                                {{-- kamar terisi --}}
                                                <a href="{{ route('dasbor.detailpenyewa', $p->id) }}"
                                                    class="col-xl-6 kamar text-decoration-none mb-4">
                                                    <div class="card border-0 rounded" style="height: 100%">
                                                        @if (\Carbon\Carbon::now() > \Carbon\Carbon::parse($p->tanggal_keluar))
                                                            <div
                                                                class="card-header bg-danger text-light text-center fw-bold">
                                                                Belum Diperpanjang
                                                            </div>
                                                        @else
                                                            <div
                                                                class="card-header bg-green text-light text-center fw-bold">
                                                                Kamar Terisi
                                                            </div>
                                                        @endif
                                                        <div class="card-body d-flex flex-column">
                                                            <table style="width: 100%">
                                                                <tbody>
                                                                    <tr>
                                                                        <td>Nomor Kamar</td>
                                                                        <td class="text-right fw-bold text-success">
                                                                            {{ $l->nomor_kamar }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Tipe Kamar</td>
                                                                        <td class="text-right">
                                                                            {{ $p->tipekamar }}
                                                                        </td>
                                                                    </tr>
                                                                    {{-- <tr>
                                                                        <td>Penyewa</td>
                                                                        <td class="text-right">
                                                                            {{ $p->penyewas->namalengkap }}
                                                                        </td>
                                                                    </tr> --}}
                                                                    <tr>
                                                                        <td>Tanggal Masuk</td>
                                                                        <td class="text-right">
                                                                            {{ \Carbon\Carbon::parse($p->tanggal_masuk)->translatedFormat('l, d-m-Y H:i:s') }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Tanggal Keluar</td>
                                                                        <td class="text-right">
                                                                            {{ \Carbon\Carbon::parse($p->tanggal_keluar)->translatedFormat('l, d-m-Y H:i:s') }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Jenis Sewa</td>
                                                                        <td class="text-right">
                                                                            {{ $p->jenissewa }}
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <div
                                                                class="mt-4 d-flex align-items-center justify-content-center gap-1">
                                                                @if ($p->jenissewa == 'Harian')
                                                                    <button type="button"
                                                                        class="btn btn-primary fw-bold d-flex align-items-center justify-content-center gap-1"
                                                                        onclick="openModalBayarIsiTokenKamar(event, {{ $p->id }})"
                                                                        style="width: 180px;">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            width="16" height="16"
                                                                            fill="currentColor" class="bi bi-lightning-fill"
                                                                            viewBox="0 0 16 16">
                                                                            <path
                                                                                d="M5.52.359A.5.5 0 0 1 6 0h4a.5.5 0 0 1 .474.658L8.694 6H12.5a.5.5 0 0 1 .395.807l-7 9a.5.5 0 0 1-.873-.454L6.823 9.5H3.5a.5.5 0 0 1-.48-.641z" />
                                                                        </svg>
                                                                        Isi Token</button>
                                                                @endif
                                                                @if (\Carbon\Carbon::now() > \Carbon\Carbon::parse($p->tanggal_keluar))
                                                                    <button type="button"
                                                                        class="btn btn-success fw-bold d-flex align-items-center justify-content-center gap-1"
                                                                        onclick="openModalPerpanjangPenyewaanKamar(event, {{ $p->id }})"
                                                                        style="width: 180px;">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            width="16" height="16"
                                                                            fill="currentColor" class="bi bi-credit-card"
                                                                            viewBox="0 0 16 16">
                                                                            <path
                                                                                d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v1h14V4a1 1 0 0 0-1-1zm13 4H1v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1z" />
                                                                            <path
                                                                                d="M2 10a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z" />
                                                                        </svg>
                                                                        Perpanjang Kamar
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </a>
                                            @elseif ($p->status_pembayaran == 'pending')
                                                {{-- booking / belum lunas --}}
                                                <a href="{{ route('dasbor.detailpenyewa', $p->id) }}"
                                                    class="col-xl-6 kamar text-decoration-none mb-4">
                                                    <div class="card border-0 rounded" style="height: 100%">
                                                        <div class="card-header bg-warning text-light text-center fw-bold">
                                                            Booking / Belum Lunas
                                                        </div>
                                                        <div class="card-body d-flex flex-column">
                                                            <table style="width: 100%">
                                                                <tbody>
                                                                    <tr>
                                                                        <td>Nomor Kamar</td>
                                                                        <td class="text-right fw-bold text-success">
                                                                            {{ $l->nomor_kamar }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Tipe Kamar</td>
                                                                        <td class="text-right">
                                                                            {{ $p->tipekamar }}
                                                                        </td>
                                                                    </tr>
                                                                    {{-- <tr>
                                                                        <td>Penyewa</td>
                                                                        <td class="text-right">
                                                                            {{ $p->penyewas->namalengkap }}
                                                                        </td>
                                                                    </tr> --}}
                                                                    <tr>
                                                                        <td>Tanggal Masuk</td>
                                                                        <td class="text-right">
                                                                            {{ \Carbon\Carbon::parse($p->tanggal_masuk)->translatedFormat('l, d-m-Y H:i:s') }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Tanggal Keluar</td>
                                                                        <td class="text-right">
                                                                            {{ \Carbon\Carbon::parse($p->tanggal_keluar)->translatedFormat('l, d-m-Y H:i:s') }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Jenis Sewa</td>
                                                                        <td class="text-right">
                                                                            {{ $p->jenissewa }}
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <div
                                                                class="mt-4 d-flex align-items-center justify-content-center gap-1">
                                                                <button type="button" class="btn btn-danger fw-bold"
                                                                    onclick="requestBatalkanPembayaran(event, {{ $p->id }})"
                                                                    style="width: 110px;">Batalkan</button>
                                                                <button type="button" class="btn btn-success fw-bold"
                                                                    onclick="openModalBayarKamar(event, {{ $p->id }})"
                                                                    style="width: 110px;">Bayar</button>
                                                                @if ($p->jenissewa == 'Harian')
                                                                    <button type="button" class="btn btn-primary fw-bold"
                                                                        onclick="openModalBayarIsiTokenKamar(event, {{ $p->id }})"
                                                                        style="width: 110px;">Isi Token</button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </a>
                                            @endif
                                        @endforeach
                                    @endforeach
                                @else
                                    <p class="m-0 text-center fw-bold text-secondary">-_- Tambah Penyewaan Kamar -_-</p>
                                @endif
                            </div>
                        </div>
                        {{-- Detail Penyewaan --}}
                        <div class="col-xl-3 mb-3">
                            <div class="card border-0">
                                <div class="card-header bg-danger text-light text-center fw-bold">
                                    Daftar Kamar Belum Terisi
                                </div>
                                <div class="card-body">
                                    <table style="width: 100%">
                                        @if (App\Models\Lokasi::where('jenisruangan_id', 2)->where('lantai_id', $lantai->id)->where('status', 0)->count() > 0)
                                            @php
                                                $kamar = App\Models\Lokasi::where('jenisruangan_id', 2)
                                                    ->where('lantai_id', $lantai->id)
                                                    ->where('status', 0)
                                                    ->get();
                                                $total = count($kamar);
                                                $counter = 0;
                                            @endphp

                                            @foreach ($kamar as $row)
                                                <tr>
                                                    <td>Nomor Kamar</td>
                                                    <td>:</td>
                                                    <td class="fw-bold text-danger" style="text-align: right;">
                                                        {{ $row->nomor_kamar }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Tipe Kamar</td>
                                                    <td>:</td>
                                                    <td class="fw-bold text-danger" style="text-align: right;">
                                                        {{ $row->tipekamars->tipekamar }}</td>
                                                </tr>

                                                @if (++$counter < $total)
                                                    <tr>
                                                        <td colspan="3">
                                                            <hr>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="3" style="text-align: center">Semua Kamar Terisi</td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <p class="m-0 text-center fw-bold text-secondary">-_- Kamar Kosong -_-</p>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('myscripts')
    <script>
        function requestBatalkanPembayaran(e, pembayaran_id) {
            e.preventDefault()
            Swal.fire({
                title: 'Batalkan Booking Kamar?',
                text: "Anda yakin ingin membatalkan booking kamar ini!",
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
                    formData.append("pembayaran_id", pembayaran_id);

                    $.ajax({
                        url: "{{ route('postbatalkanpembayarankamar') }}",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.message == "success") {
                                Swal.fire({
                                    title: "Berhasil",
                                    text: "Booking kamar berhasil dibatalkan",
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

        // bayar kamar
        function openModalBayarKamar(e, pembayaran_id) {
            e.preventDefault()

            var formData = new FormData();
            formData.append("token", $("#token").val());
            formData.append("pembayaran_id", pembayaran_id);

            $.ajax({
                url: "{{ route('getmodalselesaikanpembayarankamar') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
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
                },
                success: function(response) {
                    if (response.message == "success") {
                        setTimeout(function() {
                            $("#universalModalContent").html(response.dataHTML.trim());

                            // select 2
                            $(".form-select-2").select2({
                                dropdownParent: $("#universalModal"),
                                theme: "bootstrap-5",
                                // selectionCssClass: "select2--small",
                                // dropdownCssClass: "select2--small",
                            });

                            // Money
                            $('.formatrupiah').maskMoney({
                                allowNegative: false,
                                precision: 0,
                                thousands: '.'
                            });
                        }, 1000);
                    }
                },
            });
        }

        function requestSelesaikanPembayaranKamar(e) {
            e.preventDefault()

            let error = 0;

            if ($("#penyewa").val() == "Pilih Penyewa") {
                // penyewa
                $("#penyewa").addClass("is-invalid")
                $("#errorPenyewa").text("Kolom ini wajib diisi")
                error++
            } else {
                // penyewa
                $("#penyewa").removeClass("is-invalid")
                $("#errorPenyewa").text("")
            }

            if (($("#total_bayar").val() == "" || $("#total_bayar").val() == 0) && ($("#potongan_harga").val() == "" || $(
                    "#potongan_harga").val() == 0)) {
                // total bayar
                $("#total_bayar").addClass("is-invalid")
                $("#errorTotalBayar").text("Kolom ini wajib diisi")

                // potongan harga
                $("#potongan_harga").addClass("is-invalid")
                $("#errorPotonganHarga").text("Kolom ini wajib diisi")
                error++
            } else {
                // total harga
                $("#total_bayar").removeClass("is-invalid")
                $("#errorTotalBayar").text("")

                // potongan harga
                $("#potongan_harga").removeClass("is-invalid")
                $("#errorPotonganHarga").text("")
            }

            if (error == 0) {
                $("#btnRequest").prop("disabled", true)

                var formData = new FormData();
                formData.append("token", $("#token").val());
                formData.append("pembayaran_id", $("#pembayaran_id").val());
                formData.append("penyewa", $("#penyewa").val());
                formData.append("total_bayar", $("#total_bayar").val());
                formData.append("potongan_harga", $("#potongan_harga").val());
                formData.append("metode_pembayaran", $("input[name='metode_pembayaran']:checked").val());

                $.ajax({
                    url: "{{ route('postselesaikanpembayarankamar') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.message == "success") {
                            Swal.fire({
                                title: "Berhasil",
                                text: "Pembayaran berhasil ditambahkan",
                                icon: "success"
                            })
                            setTimeout(function() {
                                location.reload()
                            }, 1000)
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
        }

        // Isi Token
        function openModalBayarIsiTokenKamar(e, transaksi_id) {
            e.preventDefault()
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
                    <form class="modal-content" onsubmit="requestBayarIsiTokenKamar(event)" autocomplete="off" enctype="multipart/form-data" id="bayarisitokenkamar">
                        <input type="hidden" name="__token" value="` +
                    $("meta[name='csrf-token']").attr("content") +
                    `" id="token">
                        <input type="hidden" name="transaksi_id" value="` + transaksi_id + `" id="transaksi_id">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="universalModalLabel">Isi Token Listrik</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                             <div class="mb-3">
                                <label for="foto_kwh_lama" class="form-label fw-bold">Foto KWH Lama <sup class="red">*</sup></label>
                                <input type="file" class="form-control" name="foto_kwh_lama" id="foto_kwh_lama">
                                <span class="text-danger" id="errorFotoKWHLama"></span>
                            </div>
                            <div class="mb-3">
                                <label for="jumlah_kwh_lama" class="form-label fw-bold">Jumlah KWH Lama <sup class="red">*</sup></label>
                                <input type="number" class="form-control" name="jumlah_kwh_lama" id="jumlah_kwh_lama" placeholder="0">
                                <span class="text-danger" id="errorJumlahKWHLama"></span>
                            </div>
                            <div class="mb-3">
                                <label for="jumlah_kwh_baru" class="form-label fw-bold">Jumlah KWH Baru <sup class="red">*</sup></label>
                                <input type="number" class="form-control" name="jumlah_kwh_baru" id="jumlah_kwh_baru" placeholder="0">
                                <span class="text-danger" id="errorJumlahKWHBaru"></span>
                            </div>
                            <div class="mb-3">
                                <label for="jumlah_pembayaran" class="form-label fw-bold">Jumlah Pembayaran <sup class="red">*</sup></label>
                                <div class="input-group" style="z-index: 0;">
                                    <span class="input-group-text bg-success text-light fw-bold">RP</span>
                                    <input type="text"
                                        class="form-control formatrupiah"
                                        name="jumlah_pembayaran" id="jumlah_pembayaran" value="0">
                                </div>
                                <span class="text-danger" id="errorJumlahPembayaran"></span>
                            </div>
                            <div class="mb-3">
                                <label for="keterangan" class="form-label fw-bold">Keterangan <sup class="red">*</sup></label>
                                <textarea class="form-control" name="keterangan" id="keterangan"></textarea>
                                <span class="text-danger" id="errorKeterangan"></span>
                            </div>
                            <div class="mb-3">
                                <label for="cash" class="form-label fw-bold">
                                    Metode Pembayaran
                                </label>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="metode_pembayaran" id="cash"
                                                value="Cash" checked>
                                            <label class="form-check-label" for="cash">
                                                Cash
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="metode_pembayaran" id="debit"
                                                value="Debit">
                                            <label class="form-check-label" for="debit">
                                                Debit
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="metode_pembayaran" id="qris"
                                                value="QRIS">
                                            <label class="form-check-label" for="qris">
                                                QRIS
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="metode_pembayaran" id="transfer"
                                                value="Transfer">
                                            <label class="form-check-label" for="transfer">
                                                Transfer
                                            </label>
                                        </div>
                                    </div>
                                </div>
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

        function requestBayarIsiTokenKamar(e) {
            e.preventDefault()

            let error = 0;

            if ($('#foto_kwh_lama')[0].files.length === 0) {
                $("#foto_kwh_lama").addClass("is-invalid")
                $("#errorFotoKWHLama").text("Kolom ini wajib diisi")
                error++
            } else {
                let file = $('#foto_kwh_lama')[0].files[0];
                let fileType = file.type;
                let allowedTypes = ['image/jpeg', 'image/jpg'];

                // Check if the file type is allowed
                if (!allowedTypes.includes(fileType)) {
                    $('#foto_kwh_lama').addClass('is-invalid');
                    $('#errorFotoKWHLama').text('Ekstensi file hanya mendukung format jpg dan jpeg');
                    error++;
                } else {
                    $("#foto_kwh_lama").removeClass("is-invalid")
                    $("#errorFotoKWHLama").text("")
                }
            }
            // KWH lama
            if ($("#jumlah_kwh_lama").val() == "") {
                $("#jumlah_kwh_lama").addClass("is-invalid")
                $("#errorJumlahKWHLama").text("Kolom ini wajib diisi")
                error++
            } else {
                $("#jumlah_kwh_lama").removeClass("is-invalid")
                $("#errorJumlahKWHLama").text("")
            }
            // KWH Baru
            if ($("#jumlah_kwh_baru").val() == "") {
                $("#jumlah_kwh_baru").addClass("is-invalid")
                $("#errorJumlahKWHBaru").text("Kolom ini wajib diisi")
                error++
            } else {
                $("#jumlah_kwh_baru").removeClass("is-invalid")
                $("#errorJumlahKWHBaru").text("")
            }
            // Jumlah Pembayaran
            if ($("#jumlah_pembayaran").val() == "" || $("#jumlah_pembayaran").val() == 0) {
                $("#jumlah_pembayaran").addClass("is-invalid")
                $("#errorJumlahPembayaran").text("Kolom ini wajib diisi")
                error++
            } else {
                $("#jumlah_pembayaran").removeClass("is-invalid")
                $("#errorJumlahPembayaran").text("")
            }
            // Keterangan
            if ($("#keterangan").val() == "") {
                $("#keterangan").addClass("is-invalid")
                $("#errorKeterangan").text("Kolom ini wajib diisi")
                error++
            } else {
                $("#keterangan").removeClass("is-invalid")
                $("#errorKeterangan").text("")
            }

            if (error == 0) {
                $("#btnRequest").prop("disabled", true)

                var formData = new FormData($('#bayarisitokenkamar')[0]);

                $.ajax({
                    url: "{{ route('postbayarisitokenkamar') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.message == "success") {
                            Swal.fire({
                                title: "Berhasil",
                                text: "Token listrik berhasil diisi",
                                icon: "success"
                            })

                            $('#bayarisitokenkamar')[0].reset(); // Resets all form fields

                            setTimeout(function() {
                                location.reload()
                            }, 1000)
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
        }

        // perpanjang
        function openModalPerpanjangPenyewaanKamar(e, pembayaran_id) {
            e.preventDefault()

            var formData = new FormData();
            formData.append("token", $('meta[name="csrf-token"]').attr('content'))
            formData.append("pembayaran_id", pembayaran_id);

            $.ajax({
                url: "{{ route('getmodalperpanjangpembayarankamar') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
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
                },
                success: function(response) {
                    if (response.message == "success") {
                        setTimeout(function() {
                            $("#universalModalContent").html(response.dataHTML.trim());

                            $(".form-select-2").select2({
                                dropdownParent: $("#universalModal"),
                                theme: "bootstrap-5",
                                // selectionCssClass: "select2--small",
                                // dropdownCssClass: "select2--small",
                            });

                            // Money
                            $('.formatrupiah').maskMoney({
                                allowNegative: false,
                                precision: 0,
                                thousands: '.'
                            });
                        }, 1000);
                    }
                },
            });
        }

        function requestBayarPerpanjangPenyewaanKamar(e) {
            e.preventDefault()

            let error = 0;

            if ($("#penyewa").val() == "Pilih Penyewa") {
                // penyewa
                $("#penyewa").addClass("is-invalid")
                $("#errorPenyewa").text("Kolom ini wajib diisi")
                error++
            } else {
                // penyewa
                $("#penyewa").removeClass("is-invalid")
                $("#errorPenyewa").text("")
            }

            if ($("#jenissewa").val() === "Pilih Jenis Sewa") {
                $("#jenissewa").addClass("is-invalid")
                $("#errorJenisSewa").text("Kolom ini wajib diisi")
                error++
            } else {
                $("#jenissewa").removeClass("is-invalid")
                $("#errorJenisSewa").text("")
            }

            if (error == 0) {
                $("#btnRequest").prop("disabled", true)

                var formData = new FormData();
                formData.append("token", $("#token").val());
                formData.append("pembayaran_id", $("#pembayaran_id").val());
                formData.append("penyewa", $("#penyewa").val());
                formData.append("jenissewa", $("#jenissewa").val());
                formData.append("jumlahhari", $("#jumlahhari").val());
                formData.append("total_bayar", $("#total_bayar").val());
                formData.append("metode_pembayaran", $("input[name='metode_pembayaran']:checked").val());

                $.ajax({
                    url: "{{ route('postbayarperpanjangankamar') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.message == "success") {
                            Swal.fire({
                                title: "Berhasil",
                                text: "Kamar Berhasil Diperpanjang",
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
            }
        }

        function selectJenisSewa() {
            jumlahHari()
        }

        function jumlahHari() {
            var jenissewa = $("#jenissewa").val()
            let jumlahhari = $("#jumlahhari").val()

            if (jumlahhari <= 0) {
                $("#jumlahhari").val("")
                jumlahhari = 0
            }

            if (jenissewa != "Harian") {
                $("#jumlahhari").val("")
            }
        }
    </script>
@endpush
