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
                        <li class="breadcrumb-item active" aria-current="page">Kategori</li>
                    </ol>
                </nav>

                <div class="mb-3">
                    <button type="button" class="btn btn-dark d-flex align-items-center justify-content-center gap-1"
                        onclick="openModalTambahKategori()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                            class="bi bi-plus-lg" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2" />
                        </svg>
                        Kategori</button>
                </div>

                {{-- kategori --}}
                <div class="card border-0">
                    <div class="card-body">
                        <table class="table table-light table-hover border-0 m-0" id="datatableKategori" style="width: 100%">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Nama Kategori</th>
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
        var tableKategori
        $(document).ready(function() {
            tableKategori = $("#datatableKategori").DataTable({
                processing: true,
                ajax: {
                    url: "{{ route('inventaris.datatablekategori') }}",
                    type: "POST",
                    // dataSrc: ""
                    dataType: "json"
                },
                columns: [{
                        data: "nomor",
                    },
                    {
                        data: "nama_kategori",
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

        // kategori
        function openModalTambahKategori() {
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
                    <form class="modal-content" onsubmit="requestTambahKategori(event)" autocomplete="off">
                        <input type="hidden" name="__token" value="` +
                    $("meta[name='csrf-token']").attr("content") +
                    `" id="token">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="universalModalLabel">Tambah Kategori</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div>
                                <label for="kategori" class="form-label fw-bold">Kategori <sup class="text-danger">*</sup></label>
                                <input type="text" class="form-control" placeholder="Masukkan nama kategori" id="kategori">
                                <div class="invalid-feedback" id="errorKategori"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success w-100" id="btnRequest">
                                Ya
                            </button>
                        </div>
                    </form>
                `
                );

                $("#role").focus()
            }, 1000);
        }

        function requestTambahKategori(e) {
            e.preventDefault()

            if ($("#kategori").val() === "") {
                $("#kategori").addClass("is-invalid")
                $("#errorKategori").text("Kolom ini wajib diisi")
            } else {
                $("#btnRequest").prop("disabled", true)

                $("#kategori").removeClass("is-invalid")
                $("#errorKategori").text("")

                var formData = new FormData();
                formData.append("token", $("#token").val());
                formData.append("kategori", $("#kategori").val());

                $.ajax({
                    url: "{{ route('inventaris.tambahkategori') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.message == "success") {
                            Swal.fire({
                                title: "Berhasil",
                                text: "Kategori berhasil ditambahkan",
                                icon: "success"
                            })

                            $("#kategori").val("")
                            $("#kategori").removeClass("is-invalid")
                            $("#errorKategori").text("")
                            setTimeout(function() {
                                location.reload()
                            }, 1000)
                        } else {
                            $("#kategori").addClass("is-invalid")
                            $("#errorKategori").text("Nama kolom ini sudah terdaftar")

                            $("#btnRequest").prop("disabled", false)
                        }
                    },
                });
            }
        }
    </script>
@endpush
