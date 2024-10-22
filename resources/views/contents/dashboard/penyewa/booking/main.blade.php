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
                        <li class="breadcrumb-item active" aria-current="page">Booking</li>
                    </ol>
                </nav>

                {{-- booking --}}
                <div class="card border-0">
                    <div class="card-body">
                        {{-- <div class="row">
                            <div class="col-xl-6 mb-3">
                                <label for="minDate" class="form-label fw-600">Min</label>
                                <input type="date" class="form-control" id="minDate" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-xl-6 mb-3">
                                <label for="maxDate" class="form-label fw-600">Max</label>
                                <input type="date" class="form-control" id="maxDate" value="{{ date('Y-m-d') }}">
                            </div>
                        </div> --}}
                        <div class="row">
                            <div class="col-xl-6 mb-3">
                                <label for="mitra" class="form-label">Mitra</label>
                                <select class="form-select form-select-2" name="mitra" id="mitra"
                                    style="width: 100%;">
                                    <option>Pilih Mitra</option>
                                    @foreach (App\Models\Mitra::all() as $row)
                                        <option value="{{ $row->id }}">{{ $row->mitra }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-6 mb-3">
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
                        <table class="table table-light table-hover border-0 m-0" id="datatableBooking"
                            style="width: 100%; white-space: nowrap">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Dari Tanggal</th>
                                    <th scope="col">Sampai Tanggal</th>
                                    <th scope="col">Nama Booking</th>
                                    <th scope="col">No HP</th>
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
        var tableBooking
        $(document).ready(function() {
            tableBooking = $("#datatableBooking").DataTable({
                processing: true,
                pageLength: 100,
                ajax: {
                    url: "{{ route('booking.datatablebooking') }}",
                    type: "POST",
                    // dataSrc: ""
                    dataType: "json",
                    data: function(d) {
                        // d.minDate = $("#minDate").val();
                        // d.maxDate = $("#maxDate").val();
                        d.mitra = $("#mitra").val();
                        d.status_pembayaran = $("#status_pembayaran").val();
                    },
                },
                columns: [{
                        data: "nomor",
                    },
                    {
                        data: "dari_tanggal",
                    },
                    {
                        data: "sampai_tanggal",
                    },
                    {
                        data: "nama_booking",
                    },
                    {
                        data: "no_hp",
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

            $("#minDate, #maxDate, #mitra, #status_pembayaran").change(function() {
                tableBooking.ajax.reload();
            });
        });

        // selesaikan booking
        function openModalSelesaikanBooking(e, pembayaran_id) {
            e.preventDefault()

            var formData = new FormData();
            formData.append("token", $("#token").val());
            formData.append("pembayaran_id", pembayaran_id);

            $.ajax({
                url: "{{ route('getmodalselesaikanbooking') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $("#universalModalContent").empty();
                    $("#universalModalContent").addClass("modal-xl");
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
                            // $('.formatrupiah').maskMoney({
                            //     allowNegative: false,
                            //     precision: 0,
                            //     thousands: '.'
                            // });
                        }, 1000);
                    }
                },
            });
        }

        function requestSelesaikanBooking(e) {
            e.preventDefault()

            let error = 0;

            // no ktp
            if ($("#noktp").val() == "") {
                $("#noktp").addClass("is-invalid")
                $("#errornoktp").text("Kolom ini wajib diisi")
                error++
            } else {
                if ($("#noktp").val().length !== 16) {
                    $("#noktp").addClass("is-invalid")
                    $("#errornoktp").text("Kolom ini tidak valid")
                    error++
                } else {
                    $("#noktp").removeClass("is-invalid")
                    $("#errornoktp").text("")
                }
            }
            // nama lengkap
            if ($("#namalengkap").val() == "") {
                $("#namalengkap").addClass("is-invalid")
                $("#errornamalengkap").text("Kolom ini wajib diisi")
                error++
            } else {
                $("#namalengkap").removeClass("is-invalid")
                $("#errornamalengkap").text("")
            }
            // no hp
            if ($("#nohp").val() == "") {
                $("#nohp").addClass("is-invalid")
                $("#errornohp").text("Kolom ini wajib diisi")
                error++
            } else {
                $("#nohp").removeClass("is-invalid")
                $("#errornohp").text("")
            }
            // alamat
            if ($("#alamat").val() == "") {
                $("#alamat").addClass("is-invalid")
                $("#erroralamat").text("Kolom ini wajib diisi")
                error++
            } else {
                $("#alamat").removeClass("is-invalid")
                $("#erroralamat").text("")
            }
            // foto ktp
            if ($('#fotoktp')[0].files.length === 0) {
                $("#fotoktp").addClass("is-invalid")
                $("#errorfotoktp").text("Kolom ini wajib diisi")
                error++
            } else {
                let file = $('#fotoktp')[0].files[0];
                let fileType = file.type;
                let allowedTypes = ['image/jpeg', 'image/jpg'];

                // Check if the file type is allowed
                if (!allowedTypes.includes(fileType)) {
                    $('#fotoktp').addClass('is-invalid');
                    $('#errorfotoktp').text('Ekstensi file hanya mendukung format jpg dan jpeg');
                    error++;
                } else {
                    $("#fotoktp").removeClass("is-invalid")
                    $("#errorfotoktp").text("")
                }
            }
            // jenis kelamin
            if ($("#jenis_kelamin").val() == "Pilih Jenis Kelamin") {
                $("#jenis_kelamin").addClass("is-invalid")
                $("#errorjeniskelamin").text("Kolom ini wajib diisi")
                error++
            } else {
                $("#jenis_kelamin").removeClass("is-invalid")
                $("#errorjeniskelamin").text("")
            }
            // tanggal masuk
            if ($("#tanggalmasuk").val() == "") {
                $("#tanggalmasuk").addClass("is-invalid")
                $("#errortanggalmasuk").text("Kolom ini wajib diisi")
                error++
            } else {
                $("#tanggalmasuk").removeClass("is-invalid")
                $("#errortanggalmasuk").text("")
            }
            if (error == 0) {
                $("#btnRequest").prop("disabled", true)

                var formData = new FormData($('#formselesaikanbooking')[0]);

                $.ajax({
                    url: "{{ route('postselesaikanbooking') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.message == "success") {
                            Swal.fire({
                                title: "Berhasil",
                                text: "Booking berhasil diselesaikan",
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
                    $("#universalModalContent").removeClass("modal-xl");
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

            // total bayar
            if (($("#total_bayar").val() == "" || $("#total_bayar").val() == 0)) {
                // total bayar
                $("#total_bayar").addClass("is-invalid")
                $("#errorTotalBayar").text("Kolom ini wajib diisi")

                error++
            } else {
                // total harga
                $("#total_bayar").removeClass("is-invalid")
                $("#errorTotalBayar").text("")
            }

            // foto ktp
            if ($('#bukti_pembayaran')[0].files.length === 0) {
                $("#bukti_pembayaran").addClass("is-invalid")
                $("#errorBuktiPembayaran").text("Kolom ini wajib diisi")
                error++
            } else {
                let file = $('#bukti_pembayaran')[0].files[0];
                let fileType = file.type;
                let allowedTypes = ['image/jpeg', 'image/jpg'];

                // Check if the file type is allowed
                if (!allowedTypes.includes(fileType)) {
                    $('#bukti_pembayaran').addClass('is-invalid');
                    $('#errorBuktiPembayaran').text('Ekstensi file hanya mendukung format jpg dan jpeg');
                    error++;
                } else {
                    $("#bukti_pembayaran").removeClass("is-invalid")
                    $("#errorBuktiPembayaran").text("")
                }
            }

            if (error == 0) {
                $("#btnRequest").prop("disabled", true)

                var formData = new FormData($('#formselesaikanpembayarankamar')[0]);

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
    </script>
@endpush
