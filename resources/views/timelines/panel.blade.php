<!DOCTYPE html>
<html lang="en">

<head>

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Tela Tarefa') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
            rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/sass/app.scss', 'resources/js/app.js'])

        {{-- styles --}}

        @if (Auth::check())
            <style>
                .profile-border {
                    border: 0.4rem solid {{ Auth::user()->color }};
                }
            </style>
        @endif

    </head>
</head>

<body>
    <div class="container">

        <div class="row d-flex align-items-center mt-5">

            {{-- botão de voltar --}}

            <div class="col-md-2">

                <a class="btn btn-primary me-3 py-2" href="{{ route('home') }}"
                    aria-label="Voltar para a pagina inicial">

                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-6">

                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                    </svg>

                </a>
            </div>

            <div class="fs-1 col-md-7 text-center">
                <time class="m-0" id="clock">Carregando...</time>
            </div>

            <div class='col-md-3 mt-2'>

                @php
                    $labelOverview = getPaneldateLabel($hasAnytaskToday);
                @endphp

                <h2 class="fs-3 poppins m-0 p-0">
                    {{ $labelOverview }}
                </h2>

            </div>

        </div>

        <script>
            function startClock() {

                setInterval(updateClock, 1000);
            }

            function updateClock() {

                const now = new Date();
                let hours = now.getHours();
                let minutes = now.getMinutes();
                let seconds = now.getSeconds();


                hours = hours < 10 ? "0" + hours : hours;
                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                document.getElementById('clock').textContent = `${hours}:${minutes}:${seconds}`;
            }

            // Iniciar o relógio ao carregar a página
            startClock();
        </script>

</body>

</html>
