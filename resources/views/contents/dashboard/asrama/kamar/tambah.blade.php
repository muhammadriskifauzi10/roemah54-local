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
                        <li class="breadcrumb-item"><a href="{{ route('asrama') }}">Kembali</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tambah Asrama</li>
                    </ol>
                </nav>

                <div class="card border-0">
                    <div class="card-body">
                        <form action="{{ route('asrama.postasrama') }}" class="row" autocomplete="off" method="POST">
                            @csrf

                            <div class="row">
                                {{-- Lantai --}}
                                <div class="col-lg-6 mb-3">
                                    <label for="lantai" class="form-label fw-bold">Lantai</label>
                                    <select class="form-select form-select-2 @error('lantai') is-invalid @enderror"
                                        name="lantai" id="lantai" style="width: 100%;" onchange="selectLantai()">
                                        <option>Pilih Lantai</option>
                                        @foreach ($lantai as $row)
                                            <option value="{{ $row->id }}"
                                                {{ old('lantai') == $row->id ? 'selected' : '' }}>
                                                {{ $row->namalantai }}</option>
                                        @endforeach
                                    </select>
                                    @error('lantai')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                {{-- Kamar --}}
                                <div class="col-lg-6 mb-3">
                                    <label for="kamar" class="form-label fw-bold">Kamar</label>
                                    <select class="form-select form-select-2 @error('kamar') is-invalid @enderror"
                                        name="kamar" id="kamar" style="width: 100%;">
                                        <option>Pilih Kamar</option>
                                    </select>
                                    @error('kamar')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
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
        selectLantai()

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

        function selectLantai() {
            // kamar
            $("#kamar").empty()
            $("#kamar").append(`
                <option>Pilih Kamar</option>
            `)

            var formData = new FormData();
            formData.append("_token", $("meta[name='csrf-token']").attr("content"));
            formData.append("lantai", $("#lantai").val());

            $.ajax({
                url: "{{ route('asrama.getselectlantaikamar') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.message == "success") {
                        $("#kamar").append(response.data['dataHTML'].trim())
                    }
                },
            });
        }
    </script>
@endpush
