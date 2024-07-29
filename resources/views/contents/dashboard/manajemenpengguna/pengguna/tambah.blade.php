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
                        <li class="breadcrumb-item"><a href="{{ route('manajemenpengguna.pengguna') }}">Kembali</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tambah Pengguna</li>
                    </ol>
                </nav>

                <div class="card border-0">
                    <div class="card-body">
                        <form action="{{ route('manajemenpengguna.posttambahpengguna') }}" class="row" autocomplete="off" method="POST">
                            @csrf
                            <div class="row">
                                {{-- role --}}
                                <div class="col-lg-6 mb-3">
                                    <label for="role" class="form-label fw-bold">Role <sup
                                            class="text-danger">*</sup></label>
                                    <select class="form-select form-select-2 @error('role') is-invalid @enderror"
                                        name="role" id="role" style="width: 100%;">
                                        <option>Pilih Role</option>
                                        @foreach ($role as $row)
                                            <option value="{{ $row->id }}"
                                                {{ old('role') == $row->id ? 'selected' : '' }}>
                                                {{ $row->role }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                {{-- nama --}}
                                <div class="col-lg-6 mb-3">
                                    <label for="nama_pengguna" class="form-label fw-bold">Nama Pengguna <sup
                                            class="text-danger">*</sup></label>
                                    <input type="text" class="form-control @error('nama_pengguna') is-invalid @enderror"
                                        name="nama_pengguna" id="nama_pengguna" value="{{ old('nama_pengguna') }}">
                                    @error('nama_pengguna')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                {{-- password --}}
                                <div class="col-lg-6 mb-3">
                                    <label for="password" class="form-label fw-bold">Kata Sandi <sup
                                            class="text-danger">*</sup></label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        name="password" id="password">
                                    @error('password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                {{-- password confirmation --}}
                                <div class="col-lg-6 mb-3">
                                    <label for="password_confirmation" class="form-label fw-bold">Konfirmasi Kata Sandi <sup
                                            class="text-danger">*</sup></label>
                                    <input type="password"
                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                        name="password_confirmation" id="password_confirmation">
                                    @error('password_confirmation')
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
