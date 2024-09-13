<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Kwitansi</title>

    <style>
        * {
            margin: 0;
            padding: 0px;
        }

        body {
            font-family: Arial, sans-serif;
            padding: 0px 10px;
        }

        .pacifio {
            font-family: 'Pacifico', sans-serif !important;
            font-size: 30px;
            padding: 0px;
        }

        /* .invoice {
            margin: 0 auto;
        } */

        /* .content {
            margin-top: 20px;
        } */

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table tr,
        table td {
            padding: 4px;
            /* border: 1px solid red; */
        }

        .border-bottom-dashed {
            border-bottom: 1px dashed black;
            inline-display: block;
        }

        .border-bottom-solid {
            border-bottom: 1px solid black;
            inline-display: block;
        }
    </style>
</head>

<body>
    <table>
        <tr>
            <td>
                <span class="pacifio">Roemah 54</span>
            </td>
            <td rowspan="6" style="width: 50%; vertical-align: baseline;">
                <span class="pacifio">
                    <span class="pacifio border-bottom-solid">Kwitansi</span>
                    <br>
                    Receipt
                </span>
            </td>
        </tr>
        <tr>
            <td>
                <p>Penginapan Harian, Mingguan dan Bulanan</p>
            </td>
        </tr>
        <tr>
            <td>
                <span>Jl. Sei Batang Hari No. 54</span>
            </td>
        </tr>
        <tr>
            <td>
                <p>Medan 20121</p>
            </td>
        </tr>
        <tr>
            <td>
                <p>No HP/WA: 0822-7635-8873</p>
            </td>
        </tr>
        <tr>
            <td>
                <p>Web: bundathamrin-roemah54.com</p>
            </td>
        </tr>
    </table>

    <table style="border: 2px solid black; margin-top: 10px;">
        <tr>
            <td colspan="6">
                No.Kwitansi: <span class="border-bottom-solid">{{ $pembayaran->id }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="border-bottom-solid">Nama</span> <br>
                Name
            </td>
            <td style="text-align: right">
                :
            </td>
            <td colspan="4">
                <span class="border-bottom-dashed">{{ ucwords($pembayaran->penyewas->namalengkap) }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="border-bottom-solid">Type Kamar</span> <br>
                Room Type
            </td>
            <td style="text-align: right">
                :
            </td>
            <td>
                {{ $pembayaran->tipekamar }}
                {{-- @php
                    $tipekamars = App\Models\Tipekamar::all();
                    $total = count($tipekamars);
                    $counter = 0;
                @endphp

                @foreach ($tipekamars as $row)
                    @if ($row->tipekamar == $pembayaran->tipekamar)
                        <span>{{ $row->tipekamar }}</span>
                    @else
                        <span><del>{{ $row->tipekamar }}</del></span>
                    @endif

                    @if (++$counter < $total)
                        <span class="slash">/</span>
                    @endif
                @endforeach --}}
            </td>
            <td>
                <span class="border-bottom-solid">No Kamar</span> <br>
                Room Number
            </td>
            <td style="text-align: right">
                :
            </td>
            <td>
                <span class="border-bottom-solid">{{ $pembayaran->lokasis->nomor_kamar }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="border-bottom-solid">Tanggal Masuk</span> <br>
                Check In
            </td>
            <td style="text-align: right">
                :
            </td>
            <td>
                <span
                    class="border-bottom-dashed">{{ \Carbon\Carbon::parse($pembayaran->tanggal_masuk)->format('d/m/Y') }}</span>
            </td>
            <td>
                <span class="border-bottom-solid">Tanggal Keluar</span> <br>
                Check Out
            </td>
            <td style="text-align: right">
                :
            </td>
            <td>
                <span
                    class="border-bottom-dashed">{{ \Carbon\Carbon::parse($pembayaran->tanggal_keluar)->format('d/m/Y') }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="border-bottom-solid">Harga Kamar</span> <br>
                Room Rate
            </td>
            <td style="text-align: right">
                :
            </td>
            <td>
                Rp.
                <span
                    class="border-bottom-solid">{{ $pembayaran->jumlah_pembayaran ? number_format($pembayaran->jumlah_pembayaran, '0', '.', '.') : '' }}</span>
            </td>
            <td colspan="3" rowspan="3" style="vertical-align: baseline; text-align: center;">
                Medan, <span class="border-bottom-dashed">{{ \Carbon\Carbon::now()->translatedFormat('d M Y') }}</span>
                <div style="margin-top: 10px;">
                    <<img
                        src="https://barcode.tec-it.com/barcode.ashx?data={{ $pembayaran->id }}&code=QRCode&translate-esc=on&dpi=150&eclevel=L"
                        alt="" style="width: 80px; height: 80px;">
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <span class="border-bottom-solid">Potongan Harga</span> <br>
                Discounts
            </td>
            <td style="text-align: right">
                :
            </td>
            <td>
                Rp.
                <span
                    class="border-bottom-solid">{{ $pembayaran->potongan_harga ? number_format($pembayaran->potongan_harga, '0', '.', '.') : '' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                Total Bayar
            </td>
            <td style="text-align: right">
                :
            </td>
            <td>
                Rp.
                <span
                    class="border-bottom-solid">{{ $pembayaran->total_bayar ? number_format($pembayaran->total_bayar, '0', '.', '.') : '' }}</span>
            </td>
        </tr>
    </table>
</body>

</html>
