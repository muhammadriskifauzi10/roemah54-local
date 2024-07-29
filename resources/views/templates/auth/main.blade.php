<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="cache-control" content="no-cache, must-revalidate, post-check=0, pre-check=0">
    <meta http-equiv="expires" content="0">
    <meta http-equiv="pragma" content="no-cache">
    <title>{{ $judul }}</title>

    {{-- Bootstrap 5 CSS --}}
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />

    {{-- Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Sweetalert --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('css/sweetalert2.min.css') }}">

    {{-- My style --}}
    <link rel="stylesheet" href="{{ asset('css/universal.css') }}" />

    @yield('mystyles')
</head>

<body style="background-color: #eef0f8">
    <main>
        @yield('contents')
    </main>

    {{-- Jquery JS --}}
    <script src="{{ asset('js/jquery.min.js') }}"></script>

    {{-- Universal JS --}}
    <script src="{{ asset('js/universal.js') }}"></script>

    {{-- Sweetalert JS --}}
    <script src="{{ asset('js/sweetalert2.min.js') }}"></script>

    <script>
        // Session Success
        let existsMessageSuccess = "{{ Session::has('messageSuccess') }}";
        let getMessageSuccess = "{{ Session::get('messageSuccess') }}";
        if (existsMessageSuccess) {
            Swal.fire({
                title: "Berhasil",
                text: getMessageSuccess,
                icon: "success"
            })
        }

        // Session Failed
        let existsMessageFailed = "{{ Session::has('messageFailed') }}";
        let getMessageFailed = "{{ Session::get('messageFailed') }}";
        if (existsMessageFailed) {
            Swal.fire({
                title: "Gagal",
                text: getMessageFailed,
                icon: "error"
            })
        }
    </script>
    @stack('myscripts')
</body>

</html>
