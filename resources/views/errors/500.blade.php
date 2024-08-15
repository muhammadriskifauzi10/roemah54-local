<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Roemah 54</title>

    {{-- Bootstrap 5 CSS --}}
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />
    <style>
        /* Open Sans Font */
        @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@800&display=swap');

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Open Sans', sans-serif !important;
            margin: 0 !important;
        }

        body {
            height: 100vh;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @keyframes rocketAnimation {
            from {
                transform: rotate(-4deg)
            }
            to {
                transform: rotate(4deg)
            }
        }

        .panel {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 26px;
        }
        
        .panel .rocket {
            font-size: 160px;
            animation: rocketAnimation 1s linear infinite alternate;
        }

        .panel h1 {
            line-height: 60px;
        }

        /* Laptop */
        @media (max-width: 992px) {
            .panel h1 {
                font-size: 1.2rem;
                line-height: 33px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="panel">
            <span class="rocket">🚀</span>
            <h1 class="text-center">Opps, maaf situs kami sedang dalam perbaikan</h1>
        </div>
    </div>
</body>

</html>
