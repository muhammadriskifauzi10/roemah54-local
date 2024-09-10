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
                        <li class="breadcrumb-item active" aria-current="page">Asrama</li>
                    </ol>
                </nav>

                <div class="d-flex align-items-center justify-content-between mb-3">
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
                    <a href="{{ route('asrama.kamar') }}" class="fw-bold text-decoration-none">Daftar Kamar</a>
                </div>

                {{-- asrama mahasiswa --}}
                <div class="card border-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-3 mb-3">
                                <label for="minDate" class="form-label fw-600">Min Tanggal Masuk</label>
                                <input type="date" class="form-control" id="minDate" value="{{ date('Y-m-01') }}">
                            </div>
                            <div class="col-xl-3 mb-3">
                                <label for="maxDate" class="form-label fw-600">Max Tanggal Masuk</label>
                                <input type="date" class="form-control" id="maxDate" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-xl-3 mb-3">
                                <label for="penyewa" class="form-label">Pilih Penyewa</label>
                                <select class="form-select form-select-2" name="penyewa" id="penyewa"
                                    style="width: 100%;">
                                    <option>Pilih Penyewa</option>
                                    @foreach ($penyewa as $row)
                                        <option value="{{ $row->id }}">{{ $row->namalengkap }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-3 mb-3">
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
                        <table class="table table-light table-hover border-0 m-0" id="datatableAsramaMahasiswa">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Tanggal Masuk</th>
                                    <th scope="col">Tanggal Keluar</th>
                                    <th scope="col">Nama Penyewa</th>
                                    <th scope="col">Jumlah Pembayaran</th>
                                    <th scope="col">Potongan Harga</th>
                                    <th scope="col">Total Bayar</th>
                                    <th scope="col">Tanggal Pembayaran</th>
                                    <th scope="col">Kurang Bayar</th>
                                    <th scope="col">Status Pembayaran</th>
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
        var tableAsramaMahasiswa
        $(document).ready(function() {
            tableAsramaMahasiswa = $("#datatableAsramaMahasiswa").DataTable({
                processing: true,
                ajax: {
                    url: "{{ route('asrama.mahasiswa.datatableasramamahasiswa') }}",
                    type: "POST",
                    // dataSrc: ""
                    dataType: "json",
                    data: function(d) {
                        d.minDate = $("#minDate").val();
                        d.maxDate = $("#maxDate").val();
                        d.penyewa = $("#penyewa").val();
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
                        data: "jumlah_pembayaran",
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
                dom: "lBfrtip",
                buttons: [{
                    extend: "excel",
                    text: "Export Excel",
                    filename: "kamar_asrama",
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
                            "Asrama Mahasiswa dari tanggal " +
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

            $("#minDate, #maxDate, #penyewa, #status_pembayaran").change(function() {
                tableAsramaMahasiswa.ajax.reload();
            });
        });

        function onNoKtp(event) {
            const noktp = event.target.value;

            if (noktp.length == 16) {
                var formData = new FormData();
                formData.append("noktp", noktp);

                $.ajax({
                    url: "{{ route('asrama.mahasiswa.getrequestformsewaonktp') }}",
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
            var formData = new FormData();
            formData.append("_token", $("meta[name='csrf-token']").attr("content"));

            $.ajax({
                url: "{{ route('asrama.mahasiswa.getmodaltambahpenyewa') }}",
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

        function requestTambahPenyewa(e) {
            e.preventDefault()

            $("#btnRequest").prop("disabled", true)

            const form = $("#formtambahpenyewa")[0]

            const formData = new FormData(form);

            $.ajax({
                url: "{{ route('asrama.mahasiswa.tambahpenyewa') }}",
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

                        $('#formtambahpenyewa')[0].reset(); // Resets all form fields

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

                        // kamar
                        $("#kamar").removeClass("is-invalid")
                        $("#errorKamar").text("")

                        // tanggal masuk
                        $("#tanggalmasuk").removeClass("is-invalid")
                        $("#errorTanggalMasuk").text("")

                        setTimeout(function() {
                            location.reload()
                        }, 1000)
                    } else if (response.message == "errorvalidation") {
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

                        // foto ktp
                        if (response.dataError.hasOwnProperty('fotoktp')) {
                            $("#fotoktp").addClass("is-invalid")
                            $("#errorFotoKTP").text(response.dataError['fotoktp'])
                        } else {
                            $("#fotoktp").removeClass("is-invalid")
                            $("#errorFotoKTP").text("")
                        }

                        // kamar
                        if (response.dataError.hasOwnProperty('kamar')) {
                            $("#kamar").addClass("is-invalid")
                            $("#errorKamar").text(response.dataError['kamar'])
                        } else {
                            $("#kamar").removeClass("is-invalid")
                            $("#errorKamar").text("")
                        }

                        // tanggal masuk
                        if (response.dataError.hasOwnProperty('tanggalmasuk')) {
                            $("#tanggalmasuk").addClass("is-invalid")
                            $("#errorTanggalMasuk").text(response.dataError['tanggalmasuk'])
                        } else {
                            $("#tanggalmasuk").removeClass("is-invalid")
                            $("#errorTanggalMasuk").text("")
                        }

                        $("#btnRequest").prop("disabled", false)
                    } else {
                        console.log(response)
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
