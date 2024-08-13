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
                        <li class="breadcrumb-item active" aria-current="page">Denda Checkout</li>
                    </ol>
                </nav>

                {{-- denda checkout --}}
                <div class="card border-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 mb-3">
                                <label for="minDate" class="form-label fw-600">Min Tanggal Denda</label>
                                <input type="date" class="form-control" id="minDate" value="{{ date('Y-m-01') }}">
                            </div>
                            <div class="col-xl-4 mb-3">
                                <label for="maxDate" class="form-label fw-600">Max Tanggal Denda</label>
                                <input type="date" class="form-control" id="maxDate" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-xl-4 mb-3">
                                <label for="status_pembayaran" class="form-label">Status Pembayaran</label>
                                <select class="form-select form-select-2" name="status_pembayaran" id="status_pembayaran"
                                    style="width: 100%;">
                                    <option>Pilih Status Pembayaran</option>
                                    <option value="1">Sudah Dibayar</option>
                                    <option value="2">Belum Dibayar</option>
                                </select>
                            </div>
                        </div>
                        <table class="table table-light table-hover border-0 m-0" id="datatableDendaCheckout">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Tanggal Denda</th>
                                    <th scope="col">Nama Penyewa</th>
                                    <th scope="col">Nomor Kamar</th>
                                    <th scope="col">Jumlah Uang</th>
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
        var tableDendaCheckout
        $(document).ready(function() {
            tableDendaCheckout = $("#datatableDendaCheckout").DataTable({
                processing: true,
                ajax: {
                    url: "{{ route('dendacheckout.datatabledendacheckout') }}",
                    type: "POST",
                    // dataSrc: ""
                    dataType: "json",
                    data: function(d) {
                        d.minDate = $("#minDate").val();
                        d.maxDate = $("#maxDate").val();
                        d.status_pembayaran = $("#status_pembayaran").val();
                    },
                },
                columns: [{
                        data: "nomor",
                    },
                    {
                        data: "tanggal_denda",
                    },
                    {
                        data: "nama_penyewa",
                    },
                    {
                        data: "nomor_kamar",
                    },
                    {
                        data: "jumlah_uang",
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
                    filename: "denda_checkout",
                    className: 'btn btn-success',
                    exportOptions: {
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
                            "Denda Checkout dari tanggal " +
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

            $("#minDate, #maxDate, #status_pembayaran").change(function() {
                tableDendaCheckout.ajax.reload();
            });
        });

        // bayar denda
        function openModalBayarDenda(id) {
            var formData = new FormData();
            formData.append("token", $('meta[name="csrf-token"]').attr('content'))
            formData.append("denda_id", id);

            $.ajax({
                url: "{{ route('dendacheckout.getmodalbayardenda') }}",
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
                        }, 1000);
                    }
                },
            });
        }

        function requestBayarDenda() {
            $("#btnRequest").prop("disabled", true)
            
            var formData = new FormData();
            formData.append("token", $("#token").val());
            formData.append("denda_id", $("#denda_id").val());
            formData.append("metode_pembayaran", $("input[name='metode_pembayaran']:checked").val());

            $.ajax({
                url: "{{ route('dendacheckout.bayardenda') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.message == "success") {
                        Swal.fire({
                            title: "Berhasil",
                            text: "Denda Berhasil Dibayar",
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
    </script>
@endpush
