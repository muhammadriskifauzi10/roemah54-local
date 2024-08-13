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
                        <li class="breadcrumb-item active" aria-current="page">Daftar Penyewa</li>
                    </ol>
                </nav>

                {{-- daftar penyewa --}}
                <div class="card border-0">
                    <div class="card-body">
                        <table class="table table-light table-hover border-0 m-0" id="datatableDaftarPenyewa">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Nama Lengkap</th>
                                    <th scope="col">No KTP</th>
                                    <th scope="col">No HP</th>
                                    <th scope="col">Alamat</th>
                                    <th scope="col">Status</th>
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
        var tableDaftarPenyewa
        $(document).ready(function() {
            tableDaftarPenyewa = $("#datatableDaftarPenyewa").DataTable({
                processing: true,
                ajax: {
                    url: "{{ route('daftarpenyewa.datatabledaftarpenyewa') }}",
                    type: "POST",
                    // dataSrc: ""
                    dataType: "json",
                },
                columns: [{
                        data: "nomor",
                    },
                    {
                        data: "nama_lengkap",
                    },
                    {
                        data: "no_ktp",
                    },
                    {
                        data: "no_hp",
                    },
                    {
                        data: "alamat",
                    },
                    {
                        data: "status",
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
