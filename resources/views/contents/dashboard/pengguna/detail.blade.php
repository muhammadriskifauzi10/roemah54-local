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
                        <li class="breadcrumb-item"><a href="{{ route('pengguna') }}">Kembali</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail Pengguna</li>
                    </ol>
                </nav>

                <div class="row">
                    {{-- detail data --}}
                    <div class="col-xl-9 mb-3">
                        <div class="d-flex align-items-center justify-content-end mb-3">
                            @if ($pengguna->status != 1)
                                <button type="button" class="btn btn-success" data-aktifkan="{{ $pengguna->id }}"
                                    onclick="requestAktifkanPengguna(this)">
                                    Aktifkan
                                </button>
                            @endif
                        </div>

                        <div class="card border-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-2 text-center">
                                        <span class="d-inline-block border border-3 rounded-circle overflow-hidden">
                                            <img src="{{ asset('img/pengguna/default.png') }}" alt="default"
                                                style="max-width: 150px;">
                                        </span>
                                    </div>
                                    <div class="col-xl-10">
                                        <table style="width: 100%">
                                            <tr>
                                                <td>Nama</td>
                                                <td style="text-align: right">:</td>
                                                <td class="fw-bold">
                                                    {{ $pengguna->username }}</td>
                                            </tr>
                                            {{-- <tr>
                                                <td>Email</td>
                                                <td style="text-align: right">:</td>
                                                <td class="fw-bold">
                                                    {{ $pengguna->email }}</td>
                                            </tr> --}}
                                            <tr>
                                                <td>Status</td>
                                                <td style="text-align: right">:</td>
                                                <td class="fw-bold">
                                                    {!! $pengguna->status == 1
                                                        ? '<span class="badge bg-green">Aktif</span>'
                                                        : '<span class="badge bg-red">Nonaktif</span>' !!}
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- edit data --}}
                    <div class="col-lg-3">
                        <div class="card border-0">
                            <div class="card-header bg-green text-light text-center fw-bold">
                                Edit Data
                            </div>
                            <div class="card-body">
                                <form action="{{ route('pengguna.updatepengguna', encrypt($pengguna->id)) }}" class="row"
                                    autocomplete="off" method="POST">
                                    @method('PUT')
                                    @csrf
                                    {{-- role --}}
                                    <div class="mb-3">
                                        <label for="role" class="form-label fw-bold">Role <sup
                                                class="text-danger">*</sup></label>
                                        <select class="form-select form-select-2 @error('role') is-invalid @enderror"
                                            name="role" id="role" style="width: 100%;">
                                            <option>Pilih Role</option>
                                            @foreach ($role as $row)
                                                <option value="{{ $row->id }}"
                                                    {{ old('role', $pengguna->role_id) == $row->id ? 'selected' : '' }}>
                                                    {{ $row->name }}
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
                                    <div class="mb-3">
                                        <label for="nama_pengguna" class="form-label fw-bold">Nama Pengguna <sup
                                                class="text-danger">*</sup></label>
                                        <input type="text"
                                            class="form-control @error('nama_pengguna') is-invalid @enderror"
                                            name="nama_pengguna" id="nama_pengguna"
                                            value="{{ old('nama_pengguna', $pengguna->username) }}">
                                        @error('nama_pengguna')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    {{-- password --}}
                                    <div class="mb-3">
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
                                    <div class="mb-3">
                                        <label for="password_confirmation" class="form-label fw-bold">Konfirmasi
                                            Kata Sandi <sup class="text-danger">*</sup></label>
                                        <input type="password"
                                            class="form-control @error('password_confirmation') is-invalid @enderror"
                                            name="password_confirmation" id="password_confirmation">
                                        @error('password_confirmation')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div>
                                        <button type="submit" class="btn btn-success w-100" id="btn-submit">
                                            <strong class="d-block">
                                                Perbarui
                                            </strong>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
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

        // aktifkan
        function requestAktifkanPengguna(e) {
            Swal.fire({
                title: 'Aktifkan Pengguna?',
                text: "Anda yakin ingin aktifkan pengguna?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#25d366', // Warna hijau
                cancelButtonColor: '#cc0000', // Warna merah
                confirmButtonText: 'Ya, saya yakin!',
                cancelButtonText: 'Tidak, batalkan!'
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData();
                    formData.append("pengguna_id", e.getAttribute('data-aktifkan'));

                    $.ajax({
                        url: "{{ route('pengguna.aktifkanpengguna') }}",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.message == "success") {
                                Swal.fire({
                                    title: "Berhasil",
                                    text: "Pengguna Berhasil Diaktifkan",
                                    icon: "success"
                                })
                                setTimeout(function() {
                                    location.reload()
                                }, 1000)
                            } else {
                                Swal.fire({
                                    title: "Opps, terjadi kesalahan",
                                    icon: "error"
                                })
                            }
                        },
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Dibatalkan",
                        icon: "error"
                    })
                }
            })
        }
    </script>
@endpush
