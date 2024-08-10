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
                        <li class="breadcrumb-item"><a href="{{ route('kamar') }}">Kembali</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Informasi Tamu Kamar {{ $kamar->nomor_kamar }}
                        </li>
                    </ol>
                </nav>

                {{-- Kamar --}}
                <div class="card border-0">
                    <div class="card-body">
                        <table class="table table-light table-hover border-0 m-0" id="datatableInformasiTamu">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Status Penyewa</th>
                                    <th scope="col">Tanggal Masuk</th>
                                    <th scope="col">Tanggal Keluar</th>
                                    <th scope="col">Nama Penyewa</th>
                                    <th scope="col">Mitra</th>
                                    <th scope="col">Jenis Sewa</th>
                                    <th scope="col">Harga Kamar</th>
                                    <th scope="col">Diskon</th>
                                    <th scope="col">Potongan Harga</th>
                                    <th scope="col">Total Bayar</th>
                                    <th scope="col">Tanggal Pembayaran</th>
                                    <th scope="col">Kurang Bayar</th>
                                    <th scope="col">Status Pembayaran</th>
                                    <th scope="col">Aksi</th>
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
        var tableInformasiTamu
        $(document).ready(function() {
            tableInformasiTamu = $("#datatableInformasiTamu").DataTable({
                processing: true,
                ajax: {
                    url: "{{ route('kamar.datatableinformasitamu') }}",
                    type: "POST",
                    // dataSrc: ""
                    dataType: "json",
                    data: function(d) {
                        d.kamar_id = '{{ $kamar->id }}';
                    },
                },
                columns: [{
                        data: "nomor",
                    },
                    {
                        data: 'status_penyewa'
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
                    formData.append("penyewa_id", id);

                    $.ajax({
                        url: "{{ route('kamar.postpulangkantamu') }}",
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
