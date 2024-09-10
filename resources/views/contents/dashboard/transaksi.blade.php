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
                        <li class="breadcrumb-item active" aria-current="page">Transaksi</li>
                    </ol>
                </nav>

                {{-- Transaksi --}}
                <div class="card border-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-3 mb-3">
                                <label for="minDate" class="form-label fw-600">Min Tanggal Transaksi</label>
                                <input type="date" class="form-control" id="minDate" value="{{ date('Y-m-01') }}">
                            </div>
                            <div class="col-xl-3 mb-3">
                                <label for="maxDate" class="form-label fw-600">Max Tanggal Transaksi</label>
                                <input type="date" class="form-control" id="maxDate" value="{{ date('Y-m-d') }}">
                            </div>

                            <div class="col-xl-3 mb-3">
                                <label for="jenis_transaksi" class="form-label">Jenis Transaksi</label>
                                <select class="form-select form-select-2" name="jenis_transaksi" id="jenis_transaksi"
                                    style="width: 100%;">
                                    <option>Pilih Tipe Transaksi</option>
                                    @foreach (App\Models\Tagih::all() as $row)
                                        <option value="{{ $row->id }}">{{ $row->tagih }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-3 mb-3">
                                <label for="tipe" class="form-label">Tipe Transaksi</label>
                                <select class="form-select form-select-2" name="tipe" id="tipe"
                                    style="width: 100%;">
                                    <option>Pilih Tipe Transaksi</option>
                                    <option value="1">Pemasukan</option>
                                    <option value="2">Pengeluaran</option>
                                </select>
                            </div>
                        </div>

                        <table class="mb-3">
                            <tr>
                                <th scope="col" class="text-left">Pemasukan</th>
                                <th scope="col" class="text-right">:</th>
                                <th scope="col" class="text-left" colspan="5" id="pemasukan"></th>
                            </tr>
                            <tr>
                                <th scope="col" class="text-left">Pengeluaran</th>
                                <th scope="col" class="text-right">:</th>
                                <th scope="col" class="text-left" colspan="5" id="pengeluaran"></th>
                            </tr>
                        </table>
                        <table class="table table-light table-hover border-0 m-0" id="datatableTransaksi">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Tanggal Transaksi</th>
                                    <th scope="col">No Transaksi</th>
                                    <th scope="col">Tanggal Masuk</th>
                                    <th scope="col">Tanggal Keluar</th>
                                    <th scope="col">Nama Penyewa</th>
                                    <th scope="col">Nomor Kamar</th>
                                    <th scope="col">Tipe Kamar</th>
                                    <th scope="col">Jenis Sewa</th>
                                    <th scope="col">Jenis Transaksi</th>
                                    <th scope="col">Metode Pembayaran</th>
                                    <th scope="col">Tipe</th>
                                    <th scope="col">Jumlah Uang</th>
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
        var tableTransaksi
        $(document).ready(function() {
            var totalPemasukan = 0;
            var totalPengeluaran = 0;
            tableTransaksi = $("#datatableTransaksi").DataTable({
                processing: true,
                paging: false,
                ajax: {
                    url: "{{ route('datatabletransaksi') }}",
                    type: "POST",
                    // dataSrc: ""
                    dataType: "json",
                    data: function(d) {
                        d.minDate = $("#minDate").val();
                        d.maxDate = $("#maxDate").val();
                        d.jenis_transaksi = $("#jenis_transaksi").val();
                        d.tipe = $("#tipe").val();
                    },
                    dataSrc: function(json) {
                        // Reset total pemasukan dan pengeluaran
                        totalPemasukan = 0;
                        totalPengeluaran = 0;

                        // Hitung total pemasukan dan pengeluaran
                        json.data.forEach(function(row) {
                            if (row.tipe === "Pemasukan") {
                                totalPemasukan += parseFloat(row.jumlah_uang.replace(
                                    /[^0-9,-]+/g, "").replace(',', '.'));
                            } else if (row.tipe === "Pengeluaran") {
                                totalPengeluaran += parseFloat(row.jumlah_uang.replace(
                                    /[^0-9,-]+/g, "").replace(',', '.'));
                            }
                        });

                        $("#pemasukan").text("RP. " + json.pemasukan.toLocaleString().replaceAll(",",
                            "."))
                        $("#pengeluaran").text("RP. " + json.pengeluaran.toLocaleString().replaceAll(
                            ",",
                            "."))
                        return json.data;
                    },
                },
                columns: [
                    {
                        data: "nomor",
                    },
                    {
                        data: "tanggal_transaksi",
                    },
                    {
                        data: "no_transaksi",
                    },
                    {
                        data: "tanggal_masuk",
                    },
                    {
                        data: "tanggal_keluar",
                    },
                    {
                        data: "nama_penyewa",
                    },
                    {
                        data: "nomor_kamar",
                    },
                    {
                        data: "tipe_kamar",
                    },
                    {
                        data: "jenissewa",
                    },
                    {
                        data: "tagihan",
                    },
                    {
                        data: "metode_pembayaran",
                    },
                    {
                        data: "tipe",
                    },
                    {
                        data: "jumlah_uang",
                    },
                ],
                dom: "lBfrtip",
                buttons: [{
                    extend: "excel",
                    text: "Export Excel",
                    filename: "transaksi",
                    className: 'btn btn-success',
                    exportOptions: {
                        modifier: {
                            search: "none",
                        },
                    },
                    customize: function(xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];

                        // Atur gaya untuk sel pertama (A1)
                        $("row:first c", sheet).attr("s", "2");

                        // Atur teks ke "Laporan Ambulan" di sel A1
                        $("c[r=A1] t", sheet).text(
                            "Transaksi dari tanggal " +
                            $("#minDate").val() +
                            " Sampai tanggal " +
                            $("#maxDate").val()
                        );

                        var row = '<row r="1">' +
                            '<c t="inlineStr" r="A1"><is><t>Transaksi | ' + $("#minDate")
                            .val() + '-' + $("#maxDate").val() + '</t></is></c>' +
                            '</row>';
                        var totalRow = '<row r="' + (xlsx.rowCount + 1) + '">' +
                            '<c t="inlineStr" r="A' + (xlsx.rowCount + 1) +
                            '"><is><t>Total Pemasukan: RP. ' + totalPemasukan.toLocaleString(
                                'id-ID', {
                                    minimumFractionDigits: 0
                                }) + '</t></is></c>' +
                            '</row>' +
                            '<row r="' + (xlsx.rowCount + 2) + '">' +
                            '<c t="inlineStr" r="A' + (xlsx.rowCount + 2) +
                            '"><is><t>Total Pengeluaran: RP. ' + totalPengeluaran
                            .toLocaleString('id-ID', {
                                minimumFractionDigits: 0
                            }) + '</t></is></c>' +
                            '</row>';

                        sheet.childNodes[0].childNodes[1].innerHTML = row + sheet.childNodes[0]
                            .childNodes[1].innerHTML + totalRow;
                    },
                    title: `Tanggal Transaksi ${$('#minDate').val()}`
                }, ],
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

            $("#minDate, #maxDate, #jenis_transaksi, #tipe").change(function() {
                tableTransaksi.ajax.reload();
            });
        });
    </script>
@endpush
