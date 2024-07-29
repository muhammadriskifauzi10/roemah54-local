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
                        <li class="breadcrumb-item active" aria-current="page">Laundri</li>
                    </ol>
                </nav>

                <div class="mb-3">
                    <a href="{{ route('tambahlaundri') }}" type="button" class="btn btn-dark">
                        <span class="d-flex align-items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                class="bi bi-plus-lg" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2" />
                            </svg>
                            Laundri</a>
                    </span>
                </div>

                {{-- Transaksi --}}
                <div class="card border-0">
                    <div class="card-body">
                        <div class="col-6 mb-3">
                            <label for="minDate" class="form-label fw-600">Filter Laundri</label>
                            <input type="date" class="form-control" id="minDate">
                        </div>
                        <table class="table table-light table-hover border-0 m-0" id="datatableLaundri" style="width: 100%">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Tanggal Laundri</th>
                                    <th scope="col">Penyewa</th>
                                    <th scope="col">No Kamar</th>
                                    <th scope="col">Kiloan</th>
                                    <th scope="col">Jumlah Pembayaran</th>
                                    <th scope="col">Keterangan</th>
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
        var tableLaundri
        $(document).ready(function() {
            tableLaundri = $("#datatableLaundri").DataTable({
                processing: true,
                ajax: {
                    url: "{{ route('datatablelaundri') }}",
                    type: "POST",
                    // dataSrc: ""
                    dataType: "json",
                    data: function(d) {
                        d.minDate = $("#minDate").val();
                    },
                },
                columns: [{
                        data: "nomor",
                    },
                    {
                        data: "tanggal_laundri",
                    },
                    {
                        data: "penyewa",
                    },
                    {
                        data: "kamar",
                    },
                    {
                        data: "kiloan",
                    },
                    {
                        data: "jumlah_pembayaran",
                    },
                    {
                        data: "keterangan",
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

            $("#minDate").change(function() {
                tableLaundri.ajax.reload();
            });
        });
    </script>
@endpush
