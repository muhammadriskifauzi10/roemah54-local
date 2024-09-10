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
                        <li class="breadcrumb-item active" aria-current="page">Harga</li>
                    </ol>
                </nav>

                <div class="mb-3">
                    <a href="{{ route('harga.tambahharga') }}" class="btn btn-dark">
                        <span class="d-flex align-items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                class="bi bi-plus-lg" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2" />
                            </svg>
                            Harga</a>
                    </span>
                </div>

                {{-- Kamar --}}
                <div class="card border-0">
                    <div class="card-body">
                        <table class="table table-light table-hover border-0 m-0" id="datatableHarga">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Tipe Kamar</th>
                                    <th scope="col">Mitra</th>
                                    <th scope="col">Harian</th>
                                    <th scope="col">Mingguan / 7 Hari</th>
                                    <th scope="col">Mingguan / (14 Hari)</th>
                                    <th scope="col">Bulanan</th>
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
        var tableHarga
        $(document).ready(function() {
            tableHarga = $("#datatableHarga").DataTable({
                processing: true,
                paging: false,
                ajax: {
                    url: "{{ route('harga.datatableharga') }}",
                    type: "POST",
                    // dataSrc: ""
                    dataType: "json",
                },
                columns: [{
                        data: "nomor",
                    },
                    {
                        data: "tipe_kamar",
                    },
                    {
                        data: "mitra",
                    },
                    {
                        data: "harian",
                    },
                    {
                        data: "mingguan",
                    },
                    {
                        data: "hari14",
                    },
                    {
                        data: "bulanan",
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
    </script>
@endpush
