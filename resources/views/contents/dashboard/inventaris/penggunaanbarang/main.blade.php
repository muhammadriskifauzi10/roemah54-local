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
                        <li class="breadcrumb-item active" aria-current="page">Penggunaan Barang inventaris</li>
                    </ol>
                </nav>

                <div class="mb-3 d-flex align-items-center justify-content-between">
                    <button type="button" class="btn btn-dark d-flex align-items-center justify-content-center gap-1"
                        onclick="openModalTambahPenggunaanBarang()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                            class="bi bi-plus-lg" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2" />
                        </svg>
                        Penggunaan Barang
                    </button>
                    <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-1"
                        onclick="openModalCetakQrCode()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-printer" viewBox="0 0 16 16">
                            <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1" />
                            <path
                                d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1" />
                        </svg>
                        QR Code
                    </button>
                </div>

                {{-- penggunaan barang inventaris --}}
                <div class="card border-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-3 mb-3">
                                <label for="minDate" class="form-label fw-600">Dari Tanggal</label>
                                <input type="date" class="form-control" id="minDate" value="{{ date('Y-m-01') }}">
                            </div>
                            <div class="col-xl-3 mb-3">
                                <label for="maxDate" class="form-label fw-600">Sampai Tanggal</label>
                                <input type="date" class="form-control" id="maxDate" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-xl-3 mb-3">
                                <label for="kategori" class="form-label">Kategori</label>
                                <select class="form-select form-select-2" name="kategori" id="kategori"
                                    style="width: 100%;">
                                    <option selected>Pilih Kategori</option>
                                    @foreach ($kategori as $row)
                                        <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-3 mb-3">
                                <label for="lokasi" class="form-label">Lokasi</label>
                                <select class="form-select form-select-2" name="lokasi" id="lokasi"
                                    style="width: 100%;">
                                    <option selected>Pilih Lokasi</option>
                                    @foreach ($lokasi as $row)
                                        @php
                                            if ($row->jenisruangan_id == 2) {
                                                $lokasi = 'Kamar Nomor ' . $row->nomor_kamar;
                                            } else {
                                                $lokasi = App\Models\Jenisruangan::where(
                                                    'id',
                                                    $row->jenisruangan_id,
                                                )->first()->nama;
                                            }
                                        @endphp
                                        <option value="{{ $row->id }}">{{ $lokasi }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <table class="table table-light table-hover border-0 m-0" id="datatablePenggunaanBarangInventaris"
                            style="width: 100%">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">No Barcode</th>
                                    <th scope="col">Kategori</th>
                                    <th scope="col">Nama Barang</th>
                                    <th scope="col">Lokasi</th>
                                    <th scope="col">Jumlah</th>
                                    <th scope="col"></th>
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
        var tablePenggunaanBarangInventaris
        $(document).ready(function() {
            tablePenggunaanBarangInventaris = $("#datatablePenggunaanBarangInventaris").DataTable({
                processing: true,
                ajax: {
                    url: "{{ route('inventaris.datatablepenggunaanbarang') }}",
                    type: "POST",
                    // dataSrc: ""
                    dataType: "json",
                    data: function(d) {
                        d.minDate = $("#minDate").val();
                        d.maxDate = $("#maxDate").val();
                        d.kategori = $("#kategori").val();
                        d.lokasi = $("#lokasi").val();
                    },
                },
                columns: [{
                        data: "nomor",
                    },
                    {
                        data: "no_barcode",
                    },
                    {
                        data: "kategori",
                    },
                    {
                        data: "nama_barang",
                    },
                    {
                        data: "lokasi",
                    },
                    {
                        data: "jumlah",
                    },
                    {
                        data: "aksi",
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
            });

            $("#minDate, #maxDate, #kategori, #lokasi").change(function() {
                tablePenggunaanBarangInventaris.ajax.reload();
            });

            // barcode
            // $("#barcode").on("click", function() {
            //     console.log("oke")
            // })
        });

        // penggunaan barang
        function openModalTambahPenggunaanBarang() {
            var formData = new FormData();
            formData.append("token", $("meta[name='csrf-token']").attr("content"));

            $.ajax({
                url: "{{ route('inventaris.getmodalpenggunaanbarang') }}",
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
                        }, 1000);
                    }
                },
            });
        }

        // cetak qr code
        function openModalCetakQrCode() {
            $.ajax({
                url: "{{ route('inventaris.getmodalcetakqrcodepenggunaanbarang') }}",
                type: "POST",
                // data: formData,
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
                        }, 1000);
                    } else {
                        Swal.fire({
                            title: "Opps, terjadi kesalahan",
                            icon: "error"
                        })
                    }
                },
            });
        }

        function questionBarcode() {
            let barcode = $("#barcode").is(':checked')

            if (barcode) {
                $("#parentlabelbarcode").show()
                $("#labelbarcode").val("")
                $("#labelbarcode").removeClass("is-invalid")
                $("#errorLabelBarcode").text("")
                $("#labelbarcode").focus()
            } else {
                $("#parentlabelbarcode").hide()
                $("#labelbarcode").val("")
                $("#labelbarcode").removeClass("is-invalid")
                $("#errorLabelBarcode").text("")
            }
        }

        function requestTambahPenggunaanBarang(e) {
            e.preventDefault()

            $("#btnRequest").prop("disabled", true)
            const form = $("#formtambahpenggunaanbarang")[0]

            const formData = new FormData(form);

            $.ajax({
                url: "{{ route('inventaris.tambahpenggunaanbarang') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.message == "success") {
                        Swal.fire({
                            title: "Berhasil",
                            text: "Penggunaan barang inventaris berhasil ditambahkan",
                            icon: "success"
                        })

                        // barang inventaris
                        $("#baranginventaris").removeClass("is-invalid")
                        $("#errorBarangInventaris").text("")

                        // lantai
                        $("#lokasi").removeClass("is-invalid")
                        $("#errorLokasi").text("")

                        // jumlah
                        $("#jumlah").removeClass("is-invalid")
                        $("#errorJumlah").text("")

                        // label barcode
                        $("#labelbarcode").val("")
                        $("#labelbarcode").removeClass("is-invalid")
                        $("#errorLabelBarcode").text("")

                        setTimeout(function() {
                            location.reload()
                        }, 1000)
                    } else if (response.message == "opps") {
                        // barang inventaris
                        if (response.dataError.hasOwnProperty('baranginventaris')) {
                            $("#baranginventaris").addClass("is-invalid")
                            $("#errorBarangInventaris").text(response.dataError['baranginventaris'])
                        } else {
                            $("#baranginventaris").removeClass("is-invalid")
                            $("#errorBarangInventaris").text("")
                        }

                        // lokasi
                        if (response.dataError.hasOwnProperty('lokasi')) {
                            $("#lokasi").addClass("is-invalid")
                            $("#errorLokasi").text(response.dataError['lokasi'])
                        } else {
                            $("#lokasi").removeClass("is-invalid")
                            $("#errorLokasi").text("")
                        }

                        // jumlah
                        if (response.dataError.hasOwnProperty('jumlah')) {
                            $("#jumlah").addClass("is-invalid")
                            $("#errorJumlah").text(response.dataError['jumlah'])
                        } else {
                            $("#jumlah").removeClass("is-invalid")
                            $("#errorJumlah").text("")
                        }

                        // label barcode
                        if (response.dataError.hasOwnProperty('labelbarcode')) {
                            $("#labelbarcode").addClass("is-invalid")
                            $("#errorLabelBarcode").text(response.dataError['labelbarcode'])
                        } else {
                            $("#labelbarcode").removeClass("is-invalid")
                            $("#errorLabelBarcode").text("")
                        }

                        $("#btnRequest").prop("disabled", false)
                    } else {
                        Swal.fire({
                            title: "Opps, terjadi kesalahan",
                            icon: "error"
                        })

                        $("#btnRequest").prop("disabled", false)
                    }
                },
            });
        }

        // mutasi
        function openModalMutasiPenggunaanBarang(e) {
            var formData = new FormData();
            const mutasi_id = e.getAttribute('data-mutasi');

            var formData = new FormData();
            formData.append("token", $("meta[name='csrf-token']").attr("content"));
            formData.append("mutasi_id", mutasi_id);

            $.ajax({
                url: "{{ route('inventaris.getmodalmutasipenggunaanbarang') }}",
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
                        }, 1000);
                    }
                },
            });
        }

        function requestMutasiPenggunaanBarang(e) {
            e.preventDefault()

            $("#btnRequest").prop("disabled", true)
            const form = $("#formmutasipenggunaanbarang")[0]

            const formData = new FormData(form);

            $.ajax({
                url: "{{ route('inventaris.mutasipenggunaanbarang') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.message == "success") {
                        Swal.fire({
                            title: "Berhasil",
                            text: "Mutasi barang berhasil",
                            icon: "success"
                        })

                        // tujuan mutasi
                        $("#tujuanmutasi").removeClass("is-invalid")
                        $("#errorTujuanMutasi").text("")

                        setTimeout(function() {
                            location.reload()
                        }, 1000)
                    } else if (response.message == "opps") {
                        // tujuan mutasi
                        if (response.dataError.hasOwnProperty('tujuanmutasi')) {
                            $("#tujuanmutasi").addClass("is-invalid")
                            $("#errorTujuanMutasi").text(response.dataError['tujuanmutasi'])
                        } else {
                            $("#tujuanmutasi").removeClass("is-invalid")
                            $("#errorTujuanMutasi").text("")
                        }

                        $("#btnRequest").prop("disabled", false)
                    } else {
                        Swal.fire({
                            title: "Opps, terjadi kesalahan",
                            icon: "error"
                        })

                        $("#btnRequest").prop("disabled", false)
                    }
                },
            });
        }

        // hapus
        function requestHapusPenggunaanBarang(e) {
            Swal.fire({
                title: 'Hapus Penggunaan Barang?',
                text: "Anda yakin ingin menghapus penggunaan barang ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#25d366', // Warna hijau
                cancelButtonColor: '#cc0000', // Warna merah
                confirmButtonText: 'Ya, saya yakin!',
                cancelButtonText: 'Tidak, batalkan!'
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData();
                    formData.append("penggunaanbarang_id", e.getAttribute('data-hapus'));

                    $.ajax({
                        url: "{{ route('inventaris.destroypenggunaanbarang') }}",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.message == "success") {
                                Swal.fire({
                                    title: "Berhasil",
                                    text: "Penggunaan Barang Berhasil Dihapus",
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
