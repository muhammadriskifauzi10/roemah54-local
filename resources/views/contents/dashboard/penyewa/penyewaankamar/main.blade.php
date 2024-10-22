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

                {{-- penyewaan kamar --}}
                <div class="card border-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 mb-3">
                                <label for="minDate" class="form-label fw-600">Min Tanggal Masuk</label>
                                <input type="date" class="form-control" id="minDate" value="{{ $tanggal_masuk }}">
                            </div>
                            <div class="col-xl-4 mb-3">
                                <label for="maxDate" class="form-label fw-600">Max Tanggal Masuk</label>
                                <input type="date" class="form-control" id="maxDate" value="{{ date('Y-m-d') }}">
                            </div>
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
                        </div>
                        <div class="row">
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
                            <div class="col-xl-4 mb-3">
                                <label for="status" class="form-label">Status Penyewa</label>
                                <select class="form-select form-select-2" name="status" id="status"
                                    style="width: 100%;">
                                    <option>Pilih Status</option>
                                    <option value="0">Tidak Menyewa</option>
                                    <option value="1" selected>Sedang Menyewa</option>
                                </select>
                            </div>
                        </div>
                        <table class="table table-light table-hover border-0 m-0" id="datatablePenyewaanKamar"
                            style="width: 100%; white-space: nowrap">
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
                pageLength: 100,
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
                        d.status = $("#status").val();
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

            $("#minDate, #maxDate, #penyewa, #mitra, #status_pembayaran, #status").change(function() {
                tablePenyewaanKamar.ajax.reload();
            });
        });

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

            // bukti pembayaran
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

        function onGetToken(pembayaran_id) {

            let error = 0;

            if (($("#potongan_harga").val() == "" || $("#potongan_harga").val() == 0)) {
                // potongan harga
                $("#potongan_harga").addClass("is-invalid")
                $("#errorPotonganHarga").text("Kolom ini wajib diisi")
                error++
            } else {
                // potongan harga
                $("#potongan_harga").removeClass("is-invalid")
                $("#errorPotonganHarga").text("")
            }

            if (error == 0) {

                $("#btnRequestGetToken").prop("disabled", true)

                var formData = new FormData();
                formData.append("potongan_harga", $("#potongan_harga").val());
                formData.append("pembayaran_id", pembayaran_id);

                $.ajax({
                    url: "{{ route('sendemailverifikasipotonganharga') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.message == "success") {
                            $("#btnRequestGetToken").prop("disabled", false)

                            // potongan harga
                            $("#potongan_harga").removeClass("is-invalid")
                            $("#potongan_harga").val(0)
                            $("#errorPotonganHarga").text("")
                        } else {
                            $("#btnRequestGetToken").prop("disabled", false)

                            Swal.fire({
                                title: "Opps, terjadi kesalahan",
                                icon: "error"
                            })
                        }
                    },
                });
            }
        }

        function onVerifikasi(pembayaran_id) {
            $("#btnRequestVerifikasi").prop("disabled", true)

            var formData = new FormData();
            formData.append("pembayaran_id", pembayaran_id);
            formData.append("kode", $("#kode").val());

            $.ajax({
                url: "{{ route('verifikasipotonganharga') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.message == "completed") {
                        $("#btnRequestVerifikasi").prop("disabled", false)

                        $("#labelpembayaran").empty()
                        $("#labelpembayaran").append(response['label'])

                        $("#kode").val("")

                        Swal.fire({
                            title: "Berhasil",
                            text: "Token valid, berhasil menerapkan potongan harga",
                            icon: "success"
                        })

                        setTimeout(function() {
                            location.reload()
                        }, 1000)
                    } else if (response.message == "success") {
                        $("#btnRequestVerifikasi").prop("disabled", false)

                        $("#labelpembayaran").empty()
                        $("#labelpembayaran").append(response['label'])

                        $("#kode").val("")

                        Swal.fire({
                            title: "Berhasil",
                            text: "Token valid, berhasil menerapkan potongan harga",
                            icon: "success"
                        })
                    } else if (response.message == "error") {
                        $("#btnRequestVerifikasi").prop("disabled", false)

                        Swal.fire({
                            title: "Token tidak ditemukan",
                            icon: "error"
                        })
                    } else {
                        $("#btnRequestVerifikasi").prop("disabled", false)

                        Swal.fire({
                            title: "Opps, terjadi kesalahan",
                            icon: "error"
                        })
                    }
                },
            });
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

            let total_bayar = $("#total_bayar").val();
            let metode_pembayaran = $("input[name='metode_pembayaran']:checked").val();

            let error = 0;
            // jenis sewa
            if ($("#jenissewa").val() === "Pilih Jenis Sewa") {
                $("#jenissewa").addClass("is-invalid")
                $("#errorJenisSewa").text("Kolom ini wajib diisi")
                error++
            } else {
                $("#jenissewa").removeClass("is-invalid")
                $("#errorJenisSewa").text("")
            }

            let message = '';

            // bukti pembayaran
            if (parseInt(total_bayar) > 0) {
                if ($('#bukti_pembayaran')[0].files.length === 0) {
                    if (metode_pembayaran === "None") {
                        message = 'File bukti pembayaran dan metode pembayaran wajib ditentukan';
                    } else {
                        message = 'File bukti pembayaran wajib ditentukan';
                    }
                } else {
                    if (metode_pembayaran === "None") {
                        message = 'Metode pembayaran wajib ditentukan';
                    }

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
            } else {
                if ($('#bukti_pembayaran')[0].files.length > 0) {
                    if (metode_pembayaran === "None") {
                        message = 'Pembayaran wajib diisi dan metode pembayaran wajib ditentukan';
                    } else {
                        message = 'Pembayaran wajib diisi';
                    }

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
                } else {
                    if (metode_pembayaran !== "None") {
                        message = 'Pembayaran wajib diisi dan file bukti pembayaran wajib ditentukan';
                    }
                }
            }

            // Show the error message using SweetAlert
            if (message) {
                error++

                Swal.fire({
                    title: message,
                    icon: "error"
                });
            }

            if (error == 0) {
                $("#btnRequest").prop("disabled", true)

                var formData = new FormData($('#formbayarperpanjangkamar')[0]);

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
