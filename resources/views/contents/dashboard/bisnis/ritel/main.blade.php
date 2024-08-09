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
                        <li class="breadcrumb-item active" aria-current="page">Ritel</li>
                    </ol>
                </nav>

                <div class="mb-3">
                    <a href="{{ route('ritel.tambahritel') }}" type="button" class="btn btn-dark">
                        <span class="d-flex align-items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                class="bi bi-plus-lg" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2" />
                            </svg>
                            Ritel</a>
                    </span>
                </div>

                {{-- ritel --}}
                <div class="card border-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 mb-3">
                                <label for="minDate" class="form-label fw-600">Min Tanggal Ritel</label>
                                <input type="date" class="form-control" id="minDate" value="{{ date('Y-m-01') }}">
                            </div>
                            <div class="col-xl-4 mb-3">
                                <label for="maxDate" class="form-label fw-600">Max Tanggal Ritel</label>
                                <input type="date" class="form-control" id="maxDate" value="{{ date('Y-m-d') }}">
                            </div>

                            <div class="col-xl-4 mb-3">
                                <label for="jenis_ritel" class="form-label">Jenis Ritel</label>
                                <select class="form-select form-select-2" name="jenis_ritel" id="jenis_ritel"
                                    style="width: 100%;">
                                    <option>Pilih Jenis Ritel</option>
                                    @foreach ($jenisritel as $row)
                                        <option value="{{ $row->id }}">
                                            {{ $row->jenis_ritel }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <table class="table table-light table-hover border-0 m-0" id="datatableRitel" style="width: 100%">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Tanggal Ritel</th>
                                    <th scope="col">Penyewa</th>
                                    <th scope="col">No Kamar</th>
                                    <th scope="col">Jenis Ritel</th>
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
        var tableRitel
        $(document).ready(function() {
            tableRitel = $("#datatableRitel").DataTable({
                processing: true,
                ajax: {
                    url: "{{ route('ritel.datatableritel') }}",
                    type: "POST",
                    // dataSrc: ""
                    dataType: "json",
                    data: function(d) {
                        d.minDate = $("#minDate").val();
                        d.maxDate = $("#maxDate").val();
                        d.jenis_ritel = $("#jenis_ritel").val();
                    },
                },
                columns: [{
                        data: "nomor",
                    },
                    {
                        data: "tanggal_ritel",
                    },
                    {
                        data: "penyewa",
                    },
                    {
                        data: "kamar",
                    },
                    {
                        data: "jenis_ritel",
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

            $("#minDate, #maxDate, #jenis_ritel").change(function() {
                tableRitel.ajax.reload();
            });
        });
    </script>
@endpush
