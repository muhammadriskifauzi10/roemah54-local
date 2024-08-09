@extends('templates.auth.main')

@section('mystyles')
    <style>
        main {
            height: 100vh;
        }
    </style>
@endsection

@section('contents')
    <div class="container h-100">
        <div class="row justify-content-center m-0 h-100 d-flex align-items-center justify-content-center">
            <div class="col-lg-5">
                <div class="card shadow-lg border-0 rounded-lg m-0">
                    <div class="card-body">
                        <a href="/" class="text-decoration-none">
                            <h3 class="text-center font-weight-light my-4 text-success logo">Roemah 54</h3>
                        </a>
                        <form action="{{ route('authenticate') }}" method="POST" autocomplete="off">
                            @csrf
                            <div class="mb-3">
                                <label for="username" class="form-label">Nama Pengguna</label>
                                <input type="username" class="form-control" name="username" id="username" autofocus />
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Kata Sandi</label>
                                <input type="password" class="form-control" name="password" id="password" />
                            </div>
                            <div class="d-flex mt-4 mb-0">
                                <button type="submit" class="btn btn-success w-100" id="btn-submit">
                                    Login
                                </button>
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
