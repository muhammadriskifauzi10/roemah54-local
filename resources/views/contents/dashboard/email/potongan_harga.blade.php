<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Potongan harga Roemah 54</title>
</head>

<body>
    <h3 style="margin: 0">
        Potongan Harga: Rp. {{ number_format($potongan_harga, '0', '.', '.') }}
    </h3>
    <h3 style="margin: 0">
        Kode: {{ $kode }}
    </h3>

    <table>
        <tbody>
            {{-- informasi kamar --}}
            <tr>
                <th colspan="3" scope="row" class="text-left bg-green text-light">Informasi Kamar
                </th>
            </tr>
            <tr>
                <th scope="row" class="text-left">Lantai</th>
                <th scope="row" class="text-right">:</th>
                <th scope="row" class="text-left">
                    {{ DB::table('lantais')->where('id', $data['kamar']['lantai_id'])->first()->namalantai }}
                </th>
            </tr>
            <tr>
                <th scope="row" class="text-left">Nomor Kamar</th>
                <th scope="row" class="text-right">:</th>
                <th scope="row" class="text-left">{{ $kamar->nomor_kamar }}</th>
            </tr>
            <tr>
                <th scope="row" class="text-left">Tipe Kamar</th>
                <th scope="row" class="text-right">:</th>
                <th scope="row" class="text-left">
                    {{ DB::table('tipekamars')->where('id', $kamar->tipekamar_id)->first()->tipekamar }}
                </th>
            </tr>
            {{-- informasi tamu --}}
            <tr>
                <th colspan="3" scope="row" class="text-left bg-green text-light">
                    Informasi Tamu
                </th>
            </tr>
            <tr>
                <th scope="row" class="text-left">Nama Lengkap</th>
                <th scope="row" class="text-right">:</th>
                <th scope="row" class="text-left">{{ $penyewa->namalengkap }}</th>
            </tr>
            <tr>
                <th scope="row" class="text-left">Tanggal Masuk</th>
                <th scope="row" class="text-right">:</th>
                <th scope="row" class="text-left">
                    {{ \Carbon\Carbon::parse($pembayaran->tanggal_masuk)->translatedFormat('l, d-m-Y H:i:s') }}
                </th>
            </tr>
            <tr>
                <th scope="row" class="text-left">Tanggal Keluar</th>
                <th scope="row" class="text-right">:</th>
                <th scope="row" class="text-left">
                    {{ \Carbon\Carbon::parse($pembayaran->tanggal_keluar)->translatedFormat('l, d-m-Y H:i:s') }}
                </th>
            </tr>
            <tr>
                <th scope="row" class="text-left">No KTP</th>
                <th scope="row" class="text-right">:</th>
                <th scope="row" class="text-left">{{ $penyewa->noktp }}</th>
            </tr>
            <tr>
                <th scope="row" class="text-left">No HP</th>
                <th scope="row" class="text-right">:</th>
                <th scope="row" class="text-left">{{ $penyewa->nohp }}</th>
            </tr>
            <tr>
                <th scope="row" class="text-left">Alamat</th>
                <th scope="row" class="text-right">:</th>
                <th scope="row" class="text-left">{!! nl2br($penyewa->alamat) !!}</th>
            </tr>
            {{-- informasi biaya --}}
            <tr>
                <th colspan="3" scope="row" class="text-left bg-green text-light">
                    Informasi Biaya
                </th>
            </tr>
            <tr>
                <th scope="row" class="text-left">Mitra</th>
                <th scope="row" class="text-right">:</th>
                <th scope="row" class="text-left">
                    {{ DB::table('mitras')->where('id', $pembayaran->mitra_id)->first()->mitra }}
                </th>
            </tr>
            <tr>
                <th scope="row" class="text-left">Jenis Sewa</th>
                <th scope="row" class="text-right">:</th>
                <th scope="row" class="text-left">{{ $pembayaran->jenissewa }}
                </th>
            </tr>
            @if ($pembayaran->diskon != 0)
                <tr>
                    <th scope="row" class="text-left">Harga Kamar</th>
                    <th scope="row" class="text-right">:</th>
                    <th scope="row" class="text-left">RP.
                        {{ number_format($pembayaran->jumlah_pembayaran, '0', '.', '.') }}
                    </th>
                </tr>
                <tr>
                    <th scope="row" class="text-left">Diskon</th>
                    <th scope="row" class="text-right">:</th>
                    <th scope="row" class="text-left">15 %</th>
                </tr>
                <tr>
                    <th scope="row" class="text-left">Potongan Harga</th>
                    <th scope="row" class="text-right">:</th>
                    <th scope="row" class="text-left">RP.
                        {{ number_format($pembayaran->potongan_harga, '0', '.', '.') }}
                    </th>
                </tr>
                <tr>
                    <th scope="row" class="text-left">Total Pembayaran</th>
                    <th scope="row" class="text-right">:</th>
                    <th scope="row" class="text-left">RP.
                        {{ number_format($pembayaran->jumlah_pembayaran - $pembayaran->potongan_harga, '0', '.', '.') }}
                    </th>
                </tr>
            @else
                <tr>
                    <th scope="row" class="text-left">Harga Kamar</th>
                    <th scope="row" class="text-right">:</th>
                    <th scope="row" class="text-left">RP.
                        {{ number_format($pembayaran->jumlah_pembayaran, '0', '.', '.') }}
                    </th>
                </tr>
            @endif
            <tr>
                <th scope="row" class="text-left">Total Bayar</th>
                <th scope="row" class="text-right">:</th>
                <th scope="row" class="text-left">RP.
                    {{ number_format($pembayaran->total_bayar, '0', '.', '.') }}
                </th>
            </tr>
            @if ($pembayaran->status_pembayaran == 'pending')
                <tr>
                    <th scope="row" class="text-left">Kurang Bayar</th>
                    <th scope="row" class="text-right">:</th>
                    <th scope="row" class="text-left">RP.
                        {{ number_format($pembayaran->kurang_bayar, '0', '.', '.') }}
                    </th>
                </tr>
            @endif
            <tr>
                <th scope="row" class="text-left">Status Pembayaran</th>
                <th scope="row" class="text-right">:</th>
                <th scope="row" class="text-left">
                    @if ($pembayaran->status_pembayaran == 'completed')
                        <strong class='badge bg-success text-light fw-bold'>Lunas</strong>
                    @elseif ($pembayaran->status_pembayaran == 'pending')
                        <strong class='badge bg-warning text-light fw-bold'>Booking / Belum
                            Lunas</strong>
                    @endif
                </th>
            </tr>
        </tbody>
    </table>
</body>

</html>
