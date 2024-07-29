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
                        <li class="breadcrumb-item"><a href="{{ route('inventaris.barang') }}">Kembali</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Barang</li>
                    </ol>
                </nav>

                <div class="card border-0">
                    <div class="card-body">
                        <form action="{{ route('inventaris.update', encrypt($barang->id)) }}" class="row"
                            autocomplete="off" method="POST">
                            @method('PUT')
                            @csrf
                            <div class="row">
                                {{-- tanggal masuk --}}
                                <div class="col-lg-6 mb-3">
                                    <label for="tanggalmasuk" class="form-label fw-bold">Tanggal Masuk <sup
                                            class="text-danger">*</sup></label>
                                    <input type="datetime-local"
                                        class="form-control @error('tanggalmasuk') is-invalid @enderror" name="tanggalmasuk"
                                        id="tanggalmasuk" value="{{ old('tanggalmasuk', $barang->tanggal_masuk) }}">
                                    @error('tanggalmasuk')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                {{-- kategori --}}
                                <div class="col-lg-6 mb-3">
                                    <label for="kategori" class="form-label fw-bold">Kategori <sup
                                            class="text-danger">*</sup></label>
                                    <select class="form-select form-select-2 @error('kategori') is-invalid @enderror"
                                        name="kategori" id="kategori" style="width: 100%;">
                                        <option>Pilih Kategori</option>
                                        @foreach ($kategori as $row)
                                            <option value="{{ $row->id }}"
                                                {{ old('kategori', $barang->kategoribaranginventaris_id) == $row->id ? 'selected' : '' }}>
                                                {{ $row->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kategori')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    {{-- nama --}}
                                    <div class="mb-3">
                                        <label for="nama" class="form-label fw-bold">Nama <sup
                                                class="text-danger">*</sup></label>
                                        <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                            name="nama" id="nama" value="{{ old('nama', $barang->nama) }}">
                                        @error('nama')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    {{-- deskripsi barang --}}
                                    <div class="mb-3">
                                        <label for="deskripsi" class="form-label fw-bold">Deskripsi <sup
                                                class="text-danger">*</sup></label>
                                        <textarea class="form-control @error('deskripsi') is-invalid @enderror" name="deskripsi" id="deskripsi">{{ old('deskripsi', $barang->deskripsi) }}</textarea>
                                        @error('deskripsi')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    {{-- harga --}}
                                    <div class="mb-3">
                                        <label for="harga" class="form-label fw-bold">Harga <sup
                                                class="text-danger">*</sup></label>
                                        <div class="input-group" style="z-index: 0;">
                                            <span class="input-group-text bg-success text-light fw-bold">RP</span>
                                            <input type="text"
                                                class="form-control formatrupiah @error('harga') is-invalid @enderror"
                                                name="harga" id="harga" placeholder="0"
                                                value="{{ number_format($barang->harga, '0', '.', '.') }}">
                                            @error('harga')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        {{-- jumlah --}}
                                        <div class="col-6 mb-3">
                                            <label for="jumlah" class="form-label fw-bold">Jumlah <sup
                                                    class="text-danger">*</sup></label>
                                            <input type="number" class="form-control @error('jumlah') is-invalid @enderror"
                                                name="jumlah" id="jumlah"
                                                value="{{ old('jumlah', intval($barang->jumlah) - intval($barang->jumlah_terpakai)) }}">
                                            @error('jumlah')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        {{-- satuan --}}
                                        <div class="col-6 mb-3">
                                            <label for="satuan" class="form-label fw-bold">Satuan <sup
                                                    class="text-danger">*</sup></label>
                                            <input type="text" class="form-control @error('satuan') is-invalid @enderror"
                                                name="satuan" id="satuan" value="{{ old('satuan', $barang->satuan) }}"
                                                placeholder="Contoh: PCS">
                                            @error('satuan')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <button type="submit" class="btn btn-success w-100" id="btn-submit">
                                        <strong class="d-block">
                                            Perbarui
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
    </script>
@endpush
