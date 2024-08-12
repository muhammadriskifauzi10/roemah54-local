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
                        <li class="breadcrumb-item"><a href="{{ route('detaillantai', $kamar->lantai_id) }}">Kembali</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail Penyewa</li>
                    </ol>
                </nav>

                {{-- @if ($penyewa->transaksisewa_kamars->status_pembayaran == 'completed')
                    @if (\Carbon\Carbon::now() > \Carbon\Carbon::parse($penyewa->transaksisewa_kamars->tanggal_keluar))
                        <div class="d-flex align-items-center justify-content-end mb-3">
                            <button type="button"
                                class="btn btn-success d-flex align-items-center justify-content-center gap-1"
                                onclick="openModalBayarKamar()" style="width: 180px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-credit-card" viewBox="0 0 16 16">
                                    <path
                                        d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v1h14V4a1 1 0 0 0-1-1zm13 4H1v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1z" />
                                    <path d="M2 10a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z" />
                                </svg>
                                Perpanjang Kamar
                            </button>
                        </div>
                    @endif
                @endif --}}

                <div class="card border-0">
                    <div class="card-body p-0">
                        <table class="table table-hover not-va m-0">
                            <tbody>
                                <tr>
                                    <th scope="row" class="text-left">Nama Lengkap</th>
                                    <th scope="row" class="text-right">:</th>
                                    <th scope="row" class="text-left">{{ $penyewa->namalengkap }}</th>
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
                                <th scope="row" class="text-left">Foto KTP</th>
                                <th scope="row" class="text-right">:</th>
                                <th scope="row" class="text-left"><a href="/img/ktp/{{ $penyewa->fotoktp }}"
                                        target="_blank">Lihat
                                        File</a></th>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-left">Periode</th>
                                    <th scope="row" class="text-right">:</th>
                                    <th scope="row" class="text-left">
                                        {{ \Carbon\Carbon::parse($penyewa->transaksisewa_kamars->tanggal_masuk)->translatedFormat('l, Y-m-d H:i:s') }}
                                        |
                                        {{ \Carbon\Carbon::parse($penyewa->transaksisewa_kamars->tanggal_keluar)->translatedFormat('l, Y-m-d H:i:s') }}
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-left">Lantai</th>
                                    <th scope="row" class="text-right">:</th>
                                    <th scope="row" class="text-left">
                                        {{ DB::table('lantais')->where('id', $kamar->lantai_id)->first()->namalantai }}
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
                                <tr>
                                    <th scope="row" class="text-left">Mitra</th>
                                    <th scope="row" class="text-right">:</th>
                                    <th scope="row" class="text-left">
                                        {{ DB::table('mitras')->where('id', $penyewa->transaksisewa_kamars->mitra_id)->first()->mitra }}
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-left">Jenis Sewa</th>
                                    <th scope="row" class="text-right">:</th>
                                    <th scope="row" class="text-left">{{ $penyewa->transaksisewa_kamars->jenissewa }}
                                    </th>
                                </tr>
                                @if ($penyewa->transaksisewa_kamars->diskon != 0)
                                    <tr>
                                        <th scope="row" class="text-left">Harga Kamar</th>
                                        <th scope="row" class="text-right">:</th>
                                        <th scope="row" class="text-left">RP.
                                            {{ number_format($penyewa->transaksisewa_kamars->jumlah_pembayaran, '0', '.', '.') }}
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
                                            {{ number_format($penyewa->transaksisewa_kamars->potongan_harga, '0', '.', '.') }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-left">Total Pembayaran</th>
                                        <th scope="row" class="text-right">:</th>
                                        <th scope="row" class="text-left">RP.
                                            {{ number_format($penyewa->transaksisewa_kamars->jumlah_pembayaran - $penyewa->transaksisewa_kamars->potongan_harga, '0', '.', '.') }}
                                        </th>
                                    </tr>
                                @else
                                    <tr>
                                        <th scope="row" class="text-left">Harga Kamar</th>
                                        <th scope="row" class="text-right">:</th>
                                        <th scope="row" class="text-left">RP.
                                            {{ number_format($penyewa->transaksisewa_kamars->jumlah_pembayaran, '0', '.', '.') }}
                                        </th>
                                    </tr>
                                @endif
                                <tr>
                                    <th scope="row" class="text-left">Total Bayar</th>
                                    <th scope="row" class="text-right">:</th>
                                    <th scope="row" class="text-left">RP.
                                        {{ number_format($penyewa->transaksisewa_kamars->total_bayar, '0', '.', '.') }}
                                    </th>
                                </tr>
                                @if ($penyewa->transaksisewa_kamars->status_pembayaran == 'pending')
                                    <tr>
                                        <th scope="row" class="text-left">Kurang Bayar</th>
                                        <th scope="row" class="text-right">:</th>
                                        <th scope="row" class="text-left">RP.
                                            {{ number_format($penyewa->transaksisewa_kamars->kurang_bayar, '0', '.', '.') }}
                                        </th>
                                    </tr>
                                @endif
                                <tr>
                                    <th scope="row" class="text-left">Status Pembayaran</th>
                                    <th scope="row" class="text-right">:</th>
                                    <th scope="row" class="text-left">
                                        @if ($penyewa->transaksisewa_kamars->status_pembayaran == 'completed')
                                            <strong class='badge bg-success text-light fw-bold'>Lunas</strong>
                                        @elseif ($penyewa->transaksisewa_kamars->status_pembayaran == 'pending')
                                            <strong class='badge bg-warning text-light fw-bold'>Booking / Belum
                                                Lunas</strong>
                                        @endif
                                    </th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection