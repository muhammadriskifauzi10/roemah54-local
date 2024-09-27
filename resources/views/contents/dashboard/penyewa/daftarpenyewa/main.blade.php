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
                        <div class="row">
                            <div class="col-xl-6 mb-3">
                                <label for="jenis_penyewa" class="form-label">Jenis Penyewa</label>
                                <select class="form-select form-select-2" name="jenis_penyewa" id="jenis_penyewa"
                                    style="width: 100%;">
                                    <option>Pilih Jenis Penyewa</option>
                                    <option value="Umum">Umum</option>
                                    <option value="Asrama">Asrama</option>
                                </select>
                            </div>
                            <div class="col-xl-6 mb-3">
                                <label for="status" class="form-label">Status Penyewa</label>
                                <select class="form-select form-select-2" name="status" id="status"
                                    style="width: 100%;">
                                    <option>Pilih Status</option>
                                    <option value="0">Tidak Menyewa</option>
                                    <option value="1" selected>Sedang Menyewa</option>
                                </select>
                            </div>
                        </div>

                        <table class="mb-3">
                            <tr>
                                <th scope="col" class="text-left">Total Penyewa Aktif</th>
                                <th scope="col" class="text-right">:</th>
                                <th scope="col" class="text-left">
                                    {{ DB::table('penyewas')->where('status', 1)->count() }}</th>
                            </tr>
                        </table>
                        <table class="table table-light table-hover border-0 m-0" id="datatableDaftarPenyewa"
                            style="width: 100%; white-space: nowrap">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Nama Lengkap</th>
                                    <th scope="col">No KTP</th>
                                    <th scope="col">No HP</th>
                                    <th scope="col">Jenis Kelamin</th>
                                    <th scope="col">Alamat</th>
                                    <th scope="col">Jenis Penyewa</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Foto KTP</th>
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
        var tableDaftarPenyewa
        $(document).ready(function() {
            tableDaftarPenyewa = $("#datatableDaftarPenyewa").DataTable({
                processing: true,
                pageLength: 100,
                ajax: {
                    url: "{{ route('daftarpenyewa.datatabledaftarpenyewa') }}",
                    type: "POST",
                    // dataSrc: ""
                    dataType: "json",
                    data: function(d) {
                        d.jenis_penyewa = $("#jenis_penyewa").val();
                        d.status = $("#status").val();
                    },
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
                        data: "jenis_kelamin",
                    },
                    {
                        data: "alamat",
                    },
                    {
                        data: "jenis_penyewa",
                    },
                    {
                        data: "status",
                    },
                    {
                        data: "foto_ktp",
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

            $("#jenis_penyewa, #status").change(function() {
                tableDaftarPenyewa.ajax.reload();
            });
        });
    </script>
@endpush
