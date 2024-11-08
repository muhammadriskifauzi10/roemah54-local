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
                        <li class="breadcrumb-item active" aria-current="page">Role</li>
                    </ol>
                </nav>

                <div class="mb-3">
                    <button type="button" class="btn btn-dark d-flex align-items-center justify-content-center gap-1"
                        onclick="openModalTambahRole()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                            class="bi bi-plus-lg" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2" />
                        </svg>
                        Role</button>
                </div>

                {{-- role --}}
                <div class="card border-0">
                    <div class="card-body">
                        <table class="table table-light table-hover border-0 m-0" id="datatableRole" style="width: 100%">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Nama Role</th>
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
        var tableRole
        $(document).ready(function() {
            tableRole = $("#datatableRole").DataTable({
                processing: true,
                ajax: {
                    url: "{{ route('role.datatablerole') }}",
                    type: "POST",
                    // dataSrc: ""
                    dataType: "json"
                },
                columns: [{
                        data: "nomor",
                    },
                    {
                        data: "nama_role",
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

        // role
        function openModalTambahRole() {
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
                    <form class="modal-content" onsubmit="requestTambahRole(event)" autocomplete="off">
                        <input type="hidden" name="__token" value="` +
                    $("meta[name='csrf-token']").attr("content") +
                    `" id="token">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="universalModalLabel">Tambah Role</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="role" class="form-label fw-bold">Role</label>
                                <input type="text" class="form-control" placeholder="Masukkan nama role" id="role">
                                <div class="invalid-feedback" id="errorRole"></div>
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

                $("#role").focus()
            }, 1000);
        }

        function requestTambahRole(e) {
            e.preventDefault()

            $("#btnRequest").prop("disabled", true)

            $("#role").removeClass("is-invalid")
            $("#errorRole").text("")

            var formData = new FormData();
            formData.append("token", $("#token").val());
            formData.append("role", $("#role").val());

            $.ajax({
                url: "{{ route('role.tambahrole') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.message == "success") {
                        Swal.fire({
                            title: "Berhasil",
                            text: "Role berhasil ditambahkan",
                            icon: "success"
                        })

                        $("#role").val("")
                        $("#role").removeClass("is-invalid")
                        $("#errorRole").text("")
                        setTimeout(function() {
                            location.reload()
                        }, 1000)
                    } else if (response.message == "validation") {

                        // role
                        if (response.dataError.hasOwnProperty('role')) {
                            $("#role").addClass("is-invalid")
                            $("#errorRole").text(response.dataError['role'])
                        } else {
                            $("#role").removeClass("is-invalid")
                            $("#errorRole").text("")
                        }

                        $("#btnRequest").prop("disabled", false)
                    } else {
                        Swal.fire({
                            title: "Opps, terjadi kesalahan",
                            icon: "error"
                        })
                    }
                },
            });
        }

        // edit role
        function openModalEditRole(e) {
            var formData = new FormData();
            formData.append("token", $("meta[name='csrf-token']").attr("content"));
            formData.append("role_id", e.getAttribute('data-edit'));

            $.ajax({
                url: "{{ route('role.getmodaleditrole') }}",
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

        function requestEditRole(e) {
            e.preventDefault()

            $("#btnRequest").prop("disabled", true)

            var formData = new FormData();
            formData.append("token", $("#token").val());
            formData.append("role", $("#role").val());
            formData.append("role_id", $("#role_id").val());

            $.ajax({
                url: "{{ route('role.updaterole') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.message == "success") {
                        Swal.fire({
                            title: "Berhasil",
                            text: "Role berhasil diperbarui",
                            icon: "success"
                        })

                        // role
                        $("#role").removeClass("is-invalid")
                        $("#errorRole").text("")

                        $("#universalModal").modal("hide")
                        setTimeout(function() {
                            location.reload()
                        }, 1000)
                    } else if (response.message == "validation") {
                        // role
                        if (response.dataError.hasOwnProperty('role')) {
                            $("#role").addClass("is-invalid")
                            $("#errorRole").text(response.dataError['role'])
                        } else {
                            $("#role").removeClass("is-invalid")
                            $("#errorRole").text("")
                        }

                        $("#btnRequest").prop("disabled", false)
                    } else {
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
