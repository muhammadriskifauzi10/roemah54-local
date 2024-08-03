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
                        <li class="breadcrumb-item"><a href="{{ route('harga') }}">Kembali</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tambah Harga</li>
                    </ol>
                </nav>

                <div class="card border-0">
                    <div class="card-body">
                        <form action="{{ route('postharga') }}" class="row" autocomplete="off" method="POST">
                            @csrf
                            <div class="row">
                                {{-- tipe kamar --}}
                                <div class="mb-3">
                                    <label for="tipe" class="form-label fw-bold">Tipe Kamar</label>
                                    <select class="form-select form-select-2 @error('tipe') is-invalid @enderror"
                                        name="tipe" id="tipe" style="width: 100%;" onchange="selectHarga()">
                                        <option>Pilih Tipe Kamar</option>
                                        @foreach ($tipekamar as $row)
                                            <option value="{{ $row->id }}"
                                                {{ old('tipe') == $row->id ? 'selected' : '' }}>
                                                {{ $row->tipekamar }}</option>
                                        @endforeach
                                    </select>
                                    @error('tipe')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- mitra --}}
                                <div class="mb-3">
                                    <label for="mitra" class="form-label fw-bold">Mitra</label>
                                    <select class="form-select form-select-2 @error('mitra') is-invalid @enderror"
                                        name="mitra" id="mitra" style="width: 100%;" onchange="selectHarga()">>
                                        <option>Pilih Mitra</option>
                                        @foreach ($mitra as $row)
                                            <option value="{{ $row->id }}"
                                                {{ old('mitra') == $row->id ? 'selected' : '' }}>
                                                {{ $row->mitra }}</option>
                                        @endforeach
                                    </select>
                                    @error('mitra')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="row">
                                    {{-- harian --}}
                                    <div class="col-sm-3 mb-3">
                                        <label for="harian" class="form-label fw-bold">Harga Harian</label>
                                        <div class="input-group" style="z-index: 0;">
                                            <span class="input-group-text bg-success text-light fw-bold">RP</span>
                                            <input type="text"
                                                class="form-control formatrupiah  @error('harian') is-invalid @enderror"
                                                name="harian" id="harian" value="{{ old('harian', 0) }}">
                                            @error('harian')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    {{-- mingguan / 7 hari --}}
                                    <div class="col-sm-3 mb-3">
                                        <label for="mingguan" class="form-label fw-bold">Harga Mingguan / 7 Hari</label>
                                        <div class="input-group" style="z-index: 0;">
                                            <span class="input-group-text bg-success text-light fw-bold">RP</span>
                                            <input type="text"
                                                class="form-control formatrupiah  @error('mingguan') is-invalid @enderror"
                                                name="mingguan" id="mingguan" value="{{ old('mingguan', 0) }}">
                                            @error('mingguan')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    {{-- mingguan / ( > 14 hari ) --}}
                                    <div class="col-sm-3 mb-3">
                                        <label for="hari14" class="form-label fw-bold">Mingguan / ( > 14 Hari)</label>
                                        <div class="input-group" style="z-index: 0;">
                                            <span class="input-group-text bg-success text-light fw-bold">RP</span>
                                            <input type="text"
                                                class="form-control formatrupiah  @error('hari14') is-invalid @enderror"
                                                name="hari14" id="hari14" value="{{ old('hari14', 0) }}">
                                            @error('hari14')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    {{-- bulanan --}}
                                    <div class="col-sm-3 mb-3">
                                        <label for="bulanan" class="form-label fw-bold">Harga Bulanan</label>
                                        <div class="input-group" style="z-index: 0;">
                                            <span class="input-group-text bg-success text-light fw-bold">RP</span>
                                            <input type="text"
                                                class="form-control formatrupiah  @error('bulanan') is-invalid @enderror"
                                                name="bulanan" id="bulanan" value="{{ old('bulanan', 0) }}">
                                            @error('bulanan')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <button type="submit" class="btn btn-success w-100" id="btn-submit">
                                        Simpan
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

        function selectHarga() {
            var formData = new FormData();
            formData.append("tipe", $("#tipe").val());
            formData.append("mitra", $("#mitra").val());

            $.ajax({
                url: "{{ route('getselectharga') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.message == "success") {
                        $("#harian").val(response['data'].harian)
                        $("#mingguan").val(response['data'].mingguan)
                        $("#hari14").val(response['data'].hari14)
                        $("#bulanan").val(response['data'].bulanan)
                    } else {
                        $("#harian").val(0)
                        $("#mingguan").val(0)
                        $("#hari14").val(0)
                        $("#bulanan").val(0)
                    }
                },
            });
        }
    </script>
@endpush
