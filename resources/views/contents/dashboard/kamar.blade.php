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
                        <li class="breadcrumb-item active" aria-current="page">Daftar Kamar</li>
                    </ol>
                </nav>

                <div class="mb-3">
                    <button type="button" class="btn btn-dark d-flex align-items-center justify-content-center gap-1"
                        onclick="openModalKamar()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                            class="bi bi-plus-lg" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2" />
                        </svg>
                        Kamar</button>
                </div>

                {{-- Kamar --}}
                <div class="card border-0">
                    <div class="card-body">
                        <table class="table table-light table-hover border-0 m-0" id="datatableKamar">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Lantai</th>
                                    <th scope="col">Nomor Kamar</th>
                                    <th scope="col">Tipe Kamar</th>
                                    <th scope="col">Token Listrik</th>
                                    <th scope="col">Status</th>
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
        var tableKamar
        $(document).ready(function() {
            tableKamar = $("#datatableKamar").DataTable({
                processing: true,
                ajax: {
                    url: "{{ route('datatablekamar') }}",
                    type: "POST",
                    // dataSrc: ""
                    dataType: "json",
                },
                columns: [{
                        data: "nomor",
                    },
                    {
                        data: "lantai",
                    },
                    {
                        data: "nomor_kamar",
                    },
                    {
                        data: "tipe_kamar",
                    },
                    {
                        data: "token_listrik",
                    },
                    {
                        data: "status",
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

        // Kamar
        function openModalKamar() {
            var formData = new FormData();
            formData.append("token", $("#token").val());

            $.ajax({
                url: "{{ route('getmodalkamar') }}",
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

        function requestKamar(e) {
            e.preventDefault()

            $("#btnRequest").prop("disabled", true)
            var formData = new FormData();
            formData.append("token", $("#token").val());
            formData.append("lantai", $("#lantai").val());
            formData.append("tipekamar", $("#tipekamar").val());
            formData.append("token_listrik", $("#token_listrik").val());

            $.ajax({
                url: "{{ route('postkamar') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.message == "success") {
                        Swal.fire({
                            title: "Berhasil",
                            text: "Kamar berhasil ditambahkan",
                            icon: "success"
                        })

                        // Lantai
                        $("#lantai").removeClass("is-invalid")
                        $("#errorLantai").text("")

                        // Tipe Kamar
                        $("#tipekamar").removeClass("is-invalid")
                        $("#errorTipeKamar").text("")

                        // Token Listrik
                        $("#token_listrik").removeClass("is-invalid")
                        $("#errorTokenListrik").text("")

                        $("#universalModal").modal("hide")
                        setTimeout(function() {
                            location.reload()
                        }, 1000)
                    } else {
                        // Lantai
                        if (response.dataError.hasOwnProperty('lantai')) {
                            $("#lantai").addClass("is-invalid")
                            $("#errorLantai").text(response.dataError['lantai'])
                        } else {
                            $("#lantai").removeClass("is-invalid")
                            $("#errorLantai").text("")
                        }

                        // Tipe Kamar
                        if (response.dataError.hasOwnProperty('tipekamar')) {
                            $("#tipekamar").addClass("is-invalid")
                            $("#errorTipeKamar").text(response.dataError['tipekamar'])
                        } else {
                            $("#tipekamar").removeClass("is-invalid")
                            $("#errorTipeKamar").text("")
                        }

                        // Token Listrik
                        if (response.dataError.hasOwnProperty('token_listrik')) {
                            $("#token_listrik").addClass("is-invalid")
                            $("#errorTokenListrik").text(response.dataError['token_listrik'])
                        } else {
                            $("#token_listrik").removeClass("is-invalid")
                            $("#errorTokenListrik").text("")
                        }

                        $("#btnRequest").prop("disabled", false)
                    }
                },
            });
        }

        // Edit Kamar 
        function openModalEditKamar(e) {
            var formData = new FormData();
            formData.append("token", $("#token").val());
            formData.append("kamar_id", e.getAttribute('data-edit'));

            $.ajax({
                url: "{{ route('getmodaleditkamar') }}",
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

        function requestEditKamar(e) {
            e.preventDefault()

            $("#btnRequest").prop("disabled", true)

            var formData = new FormData();
            formData.append("token", $("#token").val());
            formData.append("kamar_id", $("#kamar_id").val());
            formData.append("tipekamar_id", $("#tipekamar_id").val());
            formData.append("token_listrik", $("#token_listrik").val());
            formData.append("token_listrik_edit", $("#token_listrik_edit").val());

            $.ajax({
                url: "{{ route('postedittipekamar') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.message == "success") {
                        Swal.fire({
                            title: "Berhasil",
                            text: "Kamar berhasil diperbarui",
                            icon: "success"
                        })

                        // Tipe Kamar
                        $("#tipekamar_id").removeClass("is-invalid")
                        $("#errorTipeKamar").text("")

                        // Token Listrik
                        $("#token_listrik").removeClass("is-invalid")
                        $("#errorTokenListrik").text("")

                        $("#universalModal").modal("hide")
                        setTimeout(function() {
                            location.reload()
                        }, 1000)
                    } else {
                        // Tipe Kamar
                        if (response.dataError.hasOwnProperty('tipekamar_id')) {
                            $("#tipekamar_id").addClass("is-invalid")
                            $("#errorTipeKamar").text(response.dataError['tipekamar_id'])
                        } else {
                            $("#tipekamar_id").removeClass("is-invalid")
                            $("#errorTipeKamar").text("")
                        }

                        // Token Listrik
                        if (response.dataError.hasOwnProperty('token_listrik')) {
                            $("#token_listrik").addClass("is-invalid")
                            $("#errorTokenListrik").text(response.dataError['token_listrik'])
                        } else {
                            $("#token_listrik").removeClass("is-invalid")
                            $("#errorTokenListrik").text("")
                        }

                        $("#btnRequest").prop("disabled", false)
                    }
                },
            });
        }
    </script>
@endpush
