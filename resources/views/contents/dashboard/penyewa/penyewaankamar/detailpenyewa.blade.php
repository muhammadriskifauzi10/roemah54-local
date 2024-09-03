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
                        <li class="breadcrumb-item"><a href="javascript:history.back()">Kembali</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail Penyewa</li>
                    </ol>
                </nav>

                <div class="card border-0">
                    <div class="card-body p-0">
                        <table class="table table-hover not-va m-0">
                            <tbody>
                                <tr>
                                    <th scope="row" class="text-left bg-green text-light" colspan="3">Informasi Kamar
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-left">Nomor Kamar</th>
                                    <th scope="row" class="text-right">:</th>
                                    <th scope="row" class="text-left fw-bold text-success">{{ $kamar->nomor_kamar }}</th>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-left">Tipe Kamar</th>
                                    <th scope="row" class="text-right">:</th>
                                    <th scope="row" class="text-left">
                                        {{ DB::table('tipekamars')->where('id', $kamar->tipekamar_id)->first()->tipekamar }}
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-left">Tanggal Masuk</th>
                                    <th scope="row" class="text-right">:</th>
                                    <th scope="row" class="text-left">
                                        {{ \Carbon\Carbon::parse($pembayaran->tanggal_masuk)->translatedFormat('l, Y-m-d H:i:s') }}
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-left">Tanggal Keluar</th>
                                    <th scope="row" class="text-right">:</th>
                                    <th scope="row" class="text-left">
                                        {{ \Carbon\Carbon::parse($pembayaran->tanggal_keluar)->translatedFormat('l, Y-m-d H:i:s') }}
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-left">Jenis Sewa</th>
                                    <th scope="row" class="text-right">:</th>
                                    <th scope="row" class="text-left">{{ $pembayaran->jenissewa }}
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-left">Jumlah Penyewa</th>
                                    <th scope="row" class="text-right">:</th>
                                    <th scope="row" class="text-left">{{ $pembayaran->jumlah_penyewa }} Orang
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-left">Mitra</th>
                                    <th scope="row" class="text-right">:</th>
                                    <th scope="row" class="text-left">
                                        {{ DB::table('mitras')->where('id', $pembayaran->mitra_id)->first()->mitra }}
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
                                <tr>
                                    <th scope="row" class="text-left bg-green text-light" colspan="3">Informasi
                                        Penyewa</th>
                                </tr>
                                @foreach ($pembayaran_detail as $row)
                                    <tr>
                                        <th scope="row" class="text-left">Nama Penyewa</th>
                                        <th scope="row" class="text-right">:</th>
                                        <th scope="row" class="text-left fw-bold text-success">
                                            {{ $row->penyewas->namalengkap }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-left">KTP</th>
                                        <th scope="row" class="text-right">:</th>
                                        <th scope="row" class="text-left">
                                            <a href="{{ asset('img/ktp/' . $row->penyewas->fotoktp) }}"
                                                class="fw-bold" target="_blank">Lihat File</a>
                                        </th>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
