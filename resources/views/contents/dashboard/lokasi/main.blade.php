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
                        <li class="breadcrumb-item active" aria-current="page">Daftar Lokasi</li>
                    </ol>
                </nav>

                <div class="mb-3 d-flex align-items-center justify-content-end">
                    <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-1"
                        onclick="openModalCetakQrCode()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-printer" viewBox="0 0 16 16">
                            <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1" />
                            <path
                                d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1" />
                        </svg>
                        QR Code
                    </button>
                </div>
                {{-- lokasi --}}
                <div class="card border-0">
                    <div class="card-body">
                        <table class="table table-light table-hover border-0 m-0" id="datatableLokasi">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">No Token Listrik</th>
                                    <th scope="col">Lantai</th>
                                    <th scope="col">Lokasi</th>
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
        var tableLokasi
        $(document).ready(function() {
            tableLokasi = $("#datatableLokasi").DataTable({
                processing: true,
                paging: false,
                ajax: {
                    url: "{{ route('lokasi.datatablelokasi') }}",
                    type: "POST",
                    // dataSrc: ""
                    dataType: "json",
                },
                columns: [{
                        data: "nomor",
                    },
                    {
                        data: "token_listrik",
                    },
                    {
                        data: "lantai",
                    },
                    {
                        data: "lokasi",
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

        // cetak qr code
        function openModalCetakQrCode() {
            $.ajax({
                url: "{{ route('lokasi.getmodalcetakqrcodelokasi') }}",
                type: "POST",
                // data: formData,
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
