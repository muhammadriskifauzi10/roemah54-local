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
                        <li class="breadcrumb-item active" aria-current="page">Daftar Pesanan Katering</li>
                    </ol>
                </nav>

                <div class="mb-3">
                    <button type="button" class="btn btn-dark d-flex align-items-center justify-content-center gap-1"
                        onclick="openModal()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                            class="bi bi-plus-lg" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2" />
                        </svg>
                        Katering</button>
                </div>

                {{-- Kamar --}}
                <div class="card border-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 mb-3">
                                <label for="date1" class="form-label fw-600">Dari</label>
                                <input type="date" class="form-control" id="date1" value="">
                            </div>
                            <div class="col-xl-4 mb-3">
                                <label for="date2" class="form-label fw-600">Sampai</label>
                                <input type="date" class="form-control" id="date2" value="">
                            </div>
                        </div>
                        <table class="table table-light table-hover border-0 m-0" id="datatableKamar"
                            style="width: 100%; white-space: nowrap;">
                            <thead>
                                <tr>
                                    <th scope="col" rowspan="2" width="150"></th>
                                    <th scope="col" rowspan="2">No</th>
                                    <th scope="col" colspan="2">Tgl</th>
                                    <th scope="col" rowspan="2">Jenis Order</th>
                                    <th scope="col" rowspan="2">Jumlah Porsi</th>
                                    <th scope="col" rowspan="2">Lokai</th>
                                </tr>
                                <tr>
                                    <th scope="col">Dari</th>
                                    <th scope="col">Sampai</th>
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
        var tableKamar,
         date1 = $('#date1'),
         date2 = $('#date2');

        tableKat(date1.val(),date2.val());

        function tableKat(date1,date2){
            tableKamar = $("#datatableKamar").DataTable({
                processing: true,
                paging: false,
                destroy: true,
                ajax: {
                    url: "{{ route('katering.data') }}",
                    type: "POST",
                    // dataSrc: ""
                    dataType: "json",
                    data: {
                        _token: "{{ csrf_token() }}",
                        date1: date1,
                        date2: date2
                    }
                },
                columns: [
                    {
                        data: "aksi",
                    },
                    {
                        data: "nomor",
                    },
                    {
                        data: "dari",
                    },
                    {
                        data: "sampai",
                    },
                    {
                        data: "jenis_order",
                    },
                    {
                        data: "jumlah_porsi",
                    },
                    {
                        data: "lokasi_id",
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
        }

        $("#date1, #date2").change(function() {
            tableKat(date1.val(),date2.val());
        });


        // Kamar
        function openModal() {
            var formData = new FormData();
            formData.append("token", $("meta[name='csrf-token']").attr("content"));

            $.ajax({
                url: "{{ route('katering.getmodal.add') }}",
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


        $(document).on("change", "#jenis_order", function (){
            var jns = $(this).val();
            if (jns == 'KATERING') {
                $("#lokasiDisplay").hide('slow');
            }else if (jns == 'MAKANAN_TAMU') {
                $("#lokasiDisplay").show('slow');
            }
        });

        function requestPost(e) {
            e.preventDefault()

            $("#btnRequest").prop("disabled", false);
            var formData = new FormData();
            formData.append("dari", $("#dari").val());
            formData.append("sampai", $("#sampai").val());
            formData.append("jenis_order", $("#jenis_order").val());
            formData.append("jumlah_porsi", $("#jumlah_porsi").val());
            formData.append("lokasi_id", $("#lokasi_id").val());

            $.ajax({
                url: "{{ route('katering.add') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $("#btnRequest").prop("disabled", true);
                },
                success: function(response) {
                    if (response.message == "success") {
                        Swal.fire({
                            title: "Berhasil",
                            text: "Data berhasil ditambahkan",
                            icon: "success"
                        })

                        $("#dari").removeClass("is-invalid")
                        $("#errorDari").text("")

                        $("#sampai").removeClass("is-invalid")
                        $("#errorSampai").text("")

                        $("#jenis_order").removeClass("is-invalid")
                        $("#errorJenis").text("")

                        $("#jumlah_porsi").removeClass("is-invalid")
                        $("#errorJumlah").text("")

                        $("#lokasi_id").removeClass("is-invalid")
                        $("#errorKamar").text("")

                        $("#universalModal").modal("hide")
                        tableKat(date1.val(),date2.val());
                        $("#btnRequest").prop("disabled", false)
                    } else {
                        if (response.dataError.hasOwnProperty('dari')) {
                            $("#dari").addClass("is-invalid")
                            $("#errorDari").text(response.dataError['dari'])
                        } else {
                            $("#dari").removeClass("is-invalid")
                            $("#errorDari").text("")
                        }

                        if (response.dataError.hasOwnProperty('sampai')) {
                            $("#sampai").addClass("is-invalid")
                            $("#errorSampai").text(response.dataError['sampai'])
                        } else {
                            $("#sampai").removeClass("is-invalid")
                            $("#errorSampai").text("")
                        }

                        if (response.dataError.hasOwnProperty('jenis_order')) {
                            $("#jenis_order").addClass("is-invalid")
                            $("#errorJenis").text(response.dataError['jenis_order'])
                        } else {
                            $("#jenis_order").removeClass("is-invalid")
                            $("#errorJenis").text("")
                        }

                        if (response.dataError.hasOwnProperty('jumlah_porsi')) {
                            $("#jumlah_porsi").addClass("is-invalid")
                            $("#errorJumlah").text(response.dataError['jumlah_porsi'])
                        } else {
                            $("#jumlah_porsi").removeClass("is-invalid")
                            $("#errorJumlah").text("")
                        }

                        if (response.dataError.hasOwnProperty('lokasi_id')) {
                            $("#lokasi_id").addClass("is-invalid")
                            $("#errorKamar").text(response.dataError['lokasi_id'])
                        } else {
                            $("#lokasi_id").removeClass("is-invalid")
                            $("#errorKamar").text("")
                        }

                        $("#btnRequest").prop("disabled", false)
                    }
                },
            });
        }

        // Edit Kamar 
        function openModalEdit(e) {
            var formData = new FormData();
            formData.append("token", $("meta[name='csrf-token']").attr("content"));
            formData.append("katering_id", e.getAttribute('data-edit'));

            $.ajax({
                url: "{{ route('katering.getmodal.edit') }}",
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

                            if ($("#jenis_order").val() == 'KATERING') {
                                $("#lokasiDisplay").hide('slow');
                            }else if ($("#jenis_order").val() == 'MAKANAN_TAMU') {
                                $("#lokasiDisplay").show('slow');
                            }
                        }, 1000);
                    }
                },
            });
        }

        function requestEdit(e) {
            e.preventDefault()

            $("#btnRequest").prop("disabled", false)

            var formData = new FormData();
            formData.append("katering_id", $("#katering_id").val());
            formData.append("dari", $("#dari").val());
            formData.append("sampai", $("#sampai").val());
            formData.append("jenis_order", $("#jenis_order").val());
            formData.append("jumlah_porsi", $("#jumlah_porsi").val());
            formData.append("lokasi_id", $("#lokasi_id").val());

            $.ajax({
                url: "{{ route('katering.edit') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $("#btnRequest").prop("disabled", true);
                },  
                success: function(response) {
                    if (response.message == "success") {
                        Swal.fire({
                            title: "Berhasil",
                            text: "Data berhasil diperbarui",
                            icon: "success"
                        })

                        $("#dari").removeClass("is-invalid")
                        $("#errorDari").text("")

                        $("#sampai").removeClass("is-invalid")
                        $("#errorSampai").text("")

                        $("#jenis_order").removeClass("is-invalid")
                        $("#errorJenis").text("")

                        $("#jumlah_porsi").removeClass("is-invalid")
                        $("#errorJumlah").text("")

                        $("#lokasi_id").removeClass("is-invalid")
                        $("#errorKamar").text("")

                        $("#universalModal").modal("hide")
                        tableKat(date1.val(),date2.val());

                    } else {
                        // Tipe Kamar
                        if (response.dataError.hasOwnProperty('dari')) {
                            $("#dari").addClass("is-invalid")
                            $("#errorDari").text(response.dataError['dari'])
                        } else {
                            $("#dari").removeClass("is-invalid")
                            $("#errorDari").text("")
                        }

                        if (response.dataError.hasOwnProperty('sampai')) {
                            $("#sampai").addClass("is-invalid")
                            $("#errorSampai").text(response.dataError['sampai'])
                        } else {
                            $("#sampai").removeClass("is-invalid")
                            $("#errorSampai").text("")
                        }

                        if (response.dataError.hasOwnProperty('jenis_order')) {
                            $("#jenis_order").addClass("is-invalid")
                            $("#errorJenis").text(response.dataError['jenis_order'])
                        } else {
                            $("#jenis_order").removeClass("is-invalid")
                            $("#errorJenis").text("")
                        }

                        if (response.dataError.hasOwnProperty('jumlah_porsi')) {
                            $("#jumlah_porsi").addClass("is-invalid")
                            $("#errorJumlah").text(response.dataError['jumlah_porsi'])
                        } else {
                            $("#jumlah_porsi").removeClass("is-invalid")
                            $("#errorJumlah").text("")
                        }

                        if (response.dataError.hasOwnProperty('lokasi_id')) {
                            $("#lokasi_id").addClass("is-invalid")
                            $("#errorKamar").text(response.dataError['lokasi_id'])
                        } else {
                            $("#lokasi_id").removeClass("is-invalid")
                            $("#errorKamar").text("")
                        }

                        $("#btnRequest").prop("disabled", false)
                    }
                },
            });
        }
    </script>
@endpush
