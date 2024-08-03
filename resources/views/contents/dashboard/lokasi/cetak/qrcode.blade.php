<!DOCTYPE html>
<html>

<head>
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 4px 0px;
            width: 100%;
            height: 100%;
            box-sizing: border-box;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        td img {
            margin: 0px auto;
            max-width: 65px;
        }

        td h6 {
            margin: 0;
            margin-top: 5px;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <table>
        @foreach ($lokasi as $index => $row)
            @if ($index % 2 == 0)
                <tr>
            @endif
            <td>
                <img src="https://barcode.tec-it.com/barcode.ashx?data={{ $row->id }}&code=QRCode&translate-esc=on&dpi=&eclevel=L"
                    alt="{{ $row->id }}">
                <h6>
                    @php
                        if ($row->jenisruangan_id == 2) {
                            echo 'Nomor Kamar: ' . $row->nomor_kamar;
                        } else {
                            echo App\Models\Jenisruangan::where('id', $row->jenisruangan_id)->first()->nama;
                        }
                    @endphp
                </h6>
            </td>
            @if ($index % 2 == 1)
                </tr>
            @endif
        @endforeach
        @if (count($lokasi) % 2 != 0)
            </tr>
        @endif
    </table>
</body>

</html>
