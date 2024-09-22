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
    </head>
</head>

<body>
    <div style="
    position:fixed;
    left:50%;
     transform: translateX(-50%);
    top:5%;
    ">
        <time class="" id="clock" style="font-size: 3rem;">Carregando...</time>
    </div>

    <div id="time-marker" class="bg-danger p-0"
        style="
        width:3px;
        height:85vh;
        position:fixed;
        top:15%;
        left:50%;
        transform: translateX(-50%);
        z-index:2
    ">
    </div>

    <div class="container-fluid px-5">

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


            <div class='col-md-10 mt-2 text-end'>

                @php
                    $labelOverview = getPaneldateLabel($hasAnytaskToday);
                @endphp

                <time class="fs-2 poppins m-0 p-0">
                    {{ $labelOverview }}
                </time>

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

        <div class="row full-height-88vh mt-4" style="position:relative">

            <div class="d-flex mt-5 p-0"
                style="
                flex-wrap: nowrap;
                position:absolute;
                left:{{ $positionLeft }}%;
                height:100%;
            ">
                @for ($i = 0; $i < 24; $i++)
                    <div class="h-100 border-end"
                        style="
                        min-width:200px;
                        margin: 0;
                        padding: 0;
                        position:relative;
                    ">
                        @php
                            $blockTime = getHourForBlock($i);
                        @endphp

                        <time style="position:absolute; top:-5%;left:-10%">
                            {{ $blockTime }}
                        </time>

                    </div>
                @endfor

            </div>

        </div>

</body>

</html>
