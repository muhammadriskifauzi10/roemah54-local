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
                        <li class="breadcrumb-item active" aria-current="page">Log Barang inventaris</li>
                    </ol>
                </nav>

                {{-- barang inventaris --}}
                <div class="card border-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-3 mb-3">
                                <label for="minDate" class="form-label fw-600">Min Tanggal Log</label>
                                <input type="date" class="form-control" id="minDate" value="{{ date('Y-m-01') }}">
                            </div>
                            <div class="col-xl-3 mb-3">
                                <label for="maxDate" class="form-label fw-600">Max Tanggal Log</label>
                                <input type="date" class="form-control" id="maxDate" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-xl-3 mb-3">
                                <label for="barang" class="form-label">Barang</label>
                                <select class="form-select form-select-2" name="barang" id="barang"
                                    style="width: 100%;">
                                    <option selected>Pilih Barang</option>
                                    @foreach ($barang as $row)
                                        <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-3 mb-3">
                                <label for="kategori" class="form-label">Kategori</label>
                                <select class="form-select form-select-2" name="kategori" id="kategori"
                                    style="width: 100%;">
                                    <option selected>Pilih Kategori</option>
                                    @foreach ($kategori as $row)
                                        <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <table class="table table-light table-hover border-0 m-0" id="datatableLog" style="width: 100%">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Tanggal Log</th>
                                    <th scope="col">No Barcode</th>
                                    <th scope="col">Tanggal Masuk</th>
                                    <th scope="col">Kategori</th>
                                    <th scope="col">Nama Barang</th>
                                    <th scope="col">Deskripsi</th>
                                    <th scope="col">Harga</th>
                                    <th scope="col">Jumlah</th>
                                    <th scope="col">Total Harga</th>
                                    <th scope="col">Satuan</th>
                                    <th scope="col">Asal</th>
                                    <th scope="col">Tujuan</th>
                                    <th scope="col">Log</th>
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
        var tableLog
        $(document).ready(function() {
            tableLog = $("#datatableLog").DataTable({
                processing: true,
                ajax: {
                    url: "{{ route('inventaris.datatablelog') }}",
                    type: "POST",
                    // dataSrc: ""
                    dataType: "json",
                    data: function(d) {
                        d.minDate = $("#minDate").val();
                        d.maxDate = $("#maxDate").val();
                        d.barang = $("#barang").val();
                        d.kategori = $("#kategori").val();
                    },
                },
                columns: [{
                        data: "nomor",
                    },
                    {
                        data: "tanggal_log",
                    },
                    {
                        data: "no_barcode",
                    },
                    {
                        data: "tanggal_masuk",
                    },
                    {
                        data: "kategori",
                    },
                    {
                        data: "nama_barang",
                    },
                    {
                        data: "deskripsi",
                    },
                    {
                        data: "harga",
                    },
                    {
                        data: "jumlah",
                    },
                    {
                        data: "total_harga",
                    },
                    {
                        data: "satuan",
                    },
                    {
                        data: "asal",
                    },
                    {
                        data: "tujuan",
                    },
                    {
                        data: "log",
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

            $("#minDate, #maxDate, #barang, #kategori").change(function() {
                tableLog.ajax.reload();
            });
        });
    </script>
@endpush
