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

                {{-- Penyewaan kamar --}}
                <div class="card border-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-6 mb-3">
                                <label for="minDate" class="form-label fw-600">Min Tanggal Masuk</label>
                                <input type="date" class="form-control" id="minDate" value="{{ date('Y-m-01') }}">
                            </div>
                            <div class="col-xl-6 mb-3">
                                <label for="maxDate" class="form-label fw-600">Max Tanggal Masuk</label>
                                <input type="date" class="form-control" id="maxDate" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-4 mb-3">
                                <label for="penyewa" class="form-label">Pilih Penyewa</label>
                                <select class="form-select form-select-2" name="penyewa" id="penyewa"
                                    style="width: 100%;">
                                    <option>Pilih Penyewa</option>
                                    @foreach ($penyewa as $row)
                                        <option value="{{ $row->id }}">{{ $row->namalengkap }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-4 mb-3">
                                <label for="mitra" class="form-label">Mitra</label>
                                <select class="form-select form-select-2" name="mitra" id="mitra"
                                    style="width: 100%;">
                                    <option>Pilih Mitra</option>
                                    @foreach (App\Models\Mitra::all() as $row)
                                        <option value="{{ $row->id }}">{{ $row->mitra }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-4 mb-3">
                                <label for="status_pembayaran" class="form-label">Status Pembayaran</label>
                                <select class="form-select form-select-2" name="status_pembayaran" id="status_pembayaran"
                                    style="width: 100%;">
                                    <option>Pilih Status Pembayaran</option>
                                    <option value="failed">Dibatalkan</option>
                                    <option value="completed">Lunas</option>
                                    <option value="pending">Belum Lunas</option>
                                </select>
                            </div>
                        </div>
                        <table class="table table-light table-hover border-0 m-0" id="datatablePenyewaanKamar">
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
                                    <th scope="col">Harga Kamar</th>
                                    <th scope="col">Diskon</th>
                                    <th scope="col">Potongan Harga</th>
                                    <th scope="col">Total Bayar</th>
                                    <th scope="col">Tanggal Pembayaran</th>
                                    <th scope="col">Kurang Bayar</th>
                                    <th scope="col">Status Pembayaran</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" width="150"></th>
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
                    url: "{{ route('penyewaankamar.datatablepenyewaankamar') }}",
                    type: "POST",
                    // dataSrc: ""
                    dataType: "json",
                    data: function(d) {
                        d.minDate = $("#minDate").val();
                        d.maxDate = $("#maxDate").val();
                        d.penyewa = $("#penyewa").val();
                        d.mitra = $("#mitra").val();
                        d.status_pembayaran = $("#status_pembayaran").val();
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
                        data: "jumlah_pembayaran",
                    },
                    {
                        data: "diskon",
                    },
                    {
                        data: "potongan_harga",
                    },
                    {
                        data: "total_bayar",
                    },
                    {
                        data: "tanggal_pembayaran",
                    },
                    {
                        data: "kurang_bayar",
                    },
                    {
                        data: "status_pembayaran",
                    },
                    {
                        data: "status",
                    },
                    {
                        data: "aksi",
                    },
                ],
                dom: "lBfrtip",
                buttons: [{
                    extend: "excel",
                    text: "Export Excel",
                    filename: "penyewaan_kamar",
                    className: 'btn btn-success',
                    exportOptions: {
                        columns: function(idx, data, node) {
                            // Mengecualikan kolom pertama (aksi) dari ekspor
                            return idx !== 0;
                        },
                        modifier: {
                            search: "none",
                        },
                    },
                    customize: function(xlsx) {
                        var sheet = xlsx.xl.worksheets["sheet1.xml"];

                        // Atur gaya untuk sel pertama (A1)
                        $("row:first c", sheet).attr("s", "2");

                        // Atur teks ke "Laporan Ambulan" di sel A1
                        $("c[r=A1] t", sheet).text(
                            "Penyewaan Kamar dari tanggal " +
                            $("#minDate").val() +
                            " Sampai tanggal " +
                            $("#maxDate").val()
                        );
                    },
                }, ],
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

            $("#minDate, #maxDate, #penyewa, #mitra, #status_pembayaran").change(function() {
                tablePenyewaanKamar.ajax.reload();
            });
        });

        // bayar kamar
        function openModalBayarKamar(e, transaksi_id) {
            e.preventDefault()

            var formData = new FormData();
            formData.append("token", $("#token").val());
            formData.append("transaksi_id", transaksi_id);

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

            if (($("#total_bayar").val() == "" || $("#total_bayar").val() == 0) && ($("#potongan_harga")
                    .val() == "" || $(
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
                formData.append("transaksi_id", $("#transaksi_id").val());
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

                            $(".form-modal-select-2").select2({
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

        // pindahkan tamu
        function openModalPindahkanTamu(e, pembayaran_id) {
            e.preventDefault()

            var formData = new FormData();
            formData.append("token", $('meta[name="csrf-token"]').attr('content'))
            formData.append("pembayaran_id", pembayaran_id);

            $.ajax({
                url: "{{ route('penyewaankamar.getmodalpindahkantamu') }}",
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

                            $(".form-modal-select-2").select2({
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

        function requestPindahkanTamu(e) {
            e.preventDefault()

            let error = 0;
            if ($("#lokasi").val() === "Pilih Kamar") {
                $("#lokasi").addClass("is-invalid")
                $("#errorLokasi").text("Kolom ini wajib diisi")
                error++
            } else {
                $("#lokasi").removeClass("is-invalid")
                $("#errorLokasi").text("")
            }

            if (error == 0) {
                console.log("oke")
                $("#btnRequest").prop("disabled", true)

                const form = $("#formpindahkantamu")[0]

                const formData = new FormData(form);

                $.ajax({
                    url: "{{ route('penyewaankamar.pindahkantamu') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.message == "success") {
                            Swal.fire({
                                title: "Berhasil",
                                text: "Tamu berhasil dipindahkan",
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

        // pulangkan tamu
        function requestPulangkanTamu(id) {
            Swal.fire({
                title: 'Pulangkan Tamu?',
                text: "Anda yakin ingin pulangkan tamu?",
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
                    formData.append("pembayaran_id", id);

                    $.ajax({
                        url: "{{ route('penyewaankamar.pulangkantamu') }}",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.message == "success") {
                                Swal.fire({
                                    title: "Berhasil",
                                    text: "Tamu Berhasil Dipulangkan",
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
