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
                        <li class="breadcrumb-item"><a href="{{ route('ritel') }}">Kembali</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tambah Ritel</li>
                    </ol>
                </nav>

                <div class="card border-0">
                    <div class="card-body">
                        <form action="{{ route('ritel.posttambahritel') }}" class="row" autocomplete="off"
                            method="POST">
                            @csrf
                            <div class="row">
                                {{-- penyewa --}}
                                <div class="col-lg-6 mb-3">
                                    <label for="penyewa" class="form-label fw-bold">Penyewa <sup
                                            class="text-danger">*</sup></label>
                                    <select class="form-select form-select-2 @error('penyewa') is-invalid @enderror"
                                        name="penyewa" id="penyewa" style="width: 100%;">
                                        <option>Pilih Penyewa</option>
                                        @foreach ($penyewa as $row)
                                            <option value="{{ $row->id }}"
                                                {{ old('penyewa') == $row->id ? 'selected' : '' }}>
                                                {{ $row->namalengkap }}</option>
                                        @endforeach
                                    </select>
                                    @error('penyewa')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                {{-- jenis ritel --}}
                                <div class="col-lg-6 mb-3">
                                    <label for="jenis_ritel" class="form-label fw-bold">Jenis Ritel <sup
                                            class="text-danger">*</sup></label>
                                    <select class="form-select form-select-2 @error('jenis_ritel') is-invalid @enderror"
                                        name="jenis_ritel" id="jenis_ritel" style="width: 100%;"
                                        onchange="jenisRitel(event)">
                                        <option>Pilih Jenis Ritel</option>
                                        @foreach ($jenisritel as $row)
                                            <option value="{{ $row->id }}"
                                                {{ old('jenis_ritel') == $row->id ? 'selected' : '' }}>
                                                {{ $row->jenis_ritel }}</option>
                                        @endforeach
                                    </select>
                                    @error('penyewa')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                    {{-- jumlah kilo --}}
                                    <div class="mb-3">
                                        <label for="kiloan" class="form-label fw-bold">Jumlah Kilo</label>
                                        <div class="input-group" style="z-index: 0;">
                                            <input type="number" class="form-control @error('kiloan') is-invalid @enderror"
                                                name="kiloan" id="kiloan" value="{{ old('kiloan') }}"
                                                @if (old('jenis_ritel') == 4) @else
                                                disabled @endif>
                                            <span class="input-group-text bg-success text-light fw-bold">KG</span>
                                        </div>
                                        @error('kiloan')
                                            <div class="text-danger" style="font-size: .875em;" id="errorKiloan">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    {{-- keterangan --}}
                                    <div class="mb-3">
                                        <label for="keterangan" class="form-label fw-bold">Keterangan <sup
                                                class="text-danger">*</sup></label>
                                        <textarea class="form-control @error('keterangan') is-invalid @enderror" name="keterangan" id="keterangan">{{ old('keterangan') }}</textarea>
                                        @error('keterangan')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    {{-- jumlah pembayaran --}}
                                    <div class="mb-3">
                                        <label for="jumlah_pembayaran" class="form-label fw-bold">Jumlah Pembayaran <sup
                                                class="text-danger">*</sup></label>
                                        <div class="input-group" style="z-index: 0;">
                                            <span class="input-group-text bg-success text-light fw-bold">RP</span>
                                            <input type="text"
                                                class="form-control formatrupiah  @error('jumlah_pembayaran') is-invalid @enderror"
                                                name="jumlah_pembayaran" id="jumlah_pembayaran"
                                                value="{{ old('jumlah_pembayaran', 0) }}">
                                            @error('jumlah_pembayaran')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    {{-- tipe pembayaran --}}
                                    <div class="mb-3">
                                        <label for="cash" class="form-label fw-bold">
                                            Tipe Pembayaran
                                        </label>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="metode_pembayaran"
                                                        id="cash" value="Cash" checked>
                                                    <label class="form-check-label" for="cash">
                                                        Cash
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="metode_pembayaran"
                                                        id="debit" value="Debit">
                                                    <label class="form-check-label" for="debit">
                                                        Debit
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio"
                                                        name="metode_pembayaran" id="qris" value="QRIS">
                                                    <label class="form-check-label" for="qris">
                                                        QRIS
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio"
                                                        name="metode_pembayaran" id="transfer" value="Transfer">
                                                    <label class="form-check-label" for="transfer">
                                                        Transfer
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <button type="submit" class="btn btn-success w-100" id="btn-submit">
                                        <strong class="d-block">
                                            Simpan
                                        </strong>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscripts')
    <script>
        $(document).ready(function() {
            $("#btn-submit").on("click", function() {
                $("#btn-submit").html(`
                    <div class="spinner-border spinner-border-sm" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                `)

                setTimeout(function() {
                    $("#btn-submit").prop("disabled", true)
                }, 1);
            })
        })

        function jenisRitel(e) {
            const jenisritel = e.target.value;

            if (jenisritel == 6) {
                $("#kiloan").removeAttr("disabled")
                $("#kiloan").val(1)
            } else {
                $("#kiloan").attr("disabled", "true")
                $("#kiloan").val("")
                $("#kiloan").removeClass("is-invalid")
                $("#errorKiloan").text("")
            }
        }
    </script>
@endpush
