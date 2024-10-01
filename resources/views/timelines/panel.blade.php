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

    {{-- off - canvas --}}
    <div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="offcanvasWithBothOptions"
        aria-labelledby="offcanvasWithBothOptionsLabel">

        <div class="w-100 text-end">
            <button type="button" class="btn-close me-4 mt-4" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-header">

            <div class="w-100 d-flex flex-column ps-4">

                <div class="profile-picture-container profile-border rounded-circle d-flex justify-content-center align-items-center overflow-hidden"
                    style="border: 0.4rem solid {{ Auth::user()->color }};">

                    <img class="img-size" src="{{ asset('storage/' . auth()->user()->profile_picture) }}"
                        alt="Imagem do usuário">

                </div>

                <div class='mt-2 text-start'>

                    <h2 class="fs-4 poppins-medium m-0 p-0">
                        {{ auth()->user()->name }}
                        {{ auth()->user()->lastname }}
                    </h2>

                    <p class="fs-5 roboto">{{ auth()->user()->role }} </p>

                    <p class="fs-5 roboto m-0 p-0">{{ auth()->user()->email }} </p>

                    <p class="fs-5 roboto m-0">{{ auth()->user()->telephone }} </p>
                </div>

            </div>

        </div>

        <div class="offcanvas-body">

            <ul class="list-group poppins">

                @php
                    $today = Carbon\Carbon::today()->format('Y-m-d');
                @endphp

                <a href="{{ route('display.day') }}" class="side-link">
                    <li class="list-group-item">Meu dia</li>
                </a>

                <a href="{{ route('display.week') }}" class="side-link">
                    <li class="list-group-item">Minha semana</li>
                </a>

                <a href="{{ route('display.month') }}" class="side-link">
                    <li class="list-group-item">Meu mês</li>
                </a>

                <a href="{{ route('home') }}" class="side-link">
                    <li class="list-group-item" aria-current="true">Meu painel</li>
                </a>

                <a href="{{ route('display.panel') }}" class="side-link">
                    <li class="list-group-item" aria-current="true">Painel geral</li>
                </a>

            </ul>

            <div class="mt-3">

                <form action="{{ route('home') }}" method="get" class="d-flex me-5 flex-row">
                    @csrf
                    <select name="select_filter" class="form-select rounded-0 rounded-start border-end-1 fs-6"
                        aria-label="Default select example">

                        <option selected disabled> Filtrar tarefas</option>

                        <option value="created_by_me"> Criadas por mim</option>

                        <option value="participating">Estou participando</option>

                        <option value="concluded"> Concluídas</option>

                    </select>

                    {{-- botão de enviar --}}
                    <button class="btn btn-secondary rounded-end rounded-0 border-start-0" type="submit"
                        aria-label="Enviar filtro">

                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6" width="19" height="19">

                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>

                    </button>

                </form>

            </div>

        </div>

    </div>

    <div style="
        position:fixed;
        left:50%;
        transform: translateX(-50%);
        top:5%;
    ">
        <time class="poppins" id="clock" style="font-size: 3.2rem;">Carregando...</time>
    </div>

    <div class="container-fluid px-5">

        <div class="row d-flex align-items-center mt-5">

            {{-- botão de offcanvas --}}

            <div class="col-md-2">

                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">

                    </button>

                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="{{ route('task.create') }}">
                                Criar Tarefa
                            </a>
                        </li>
                        <li> <button class="dropdown-item" type="button" data-bs-toggle="offcanvas"
                                data-bs-target="#offcanvasWithBothOptions" aria-controls="offcanvasWithBothOptions"
                                title="Visualizar barra lateral">
                                Opções de tela

                            </button>
                        </li>

                    </ul>
                </div>

            </div>

            <div class='col-md-10 mt-2 text-end'>

                @php
                    $labelOverview = getPaneldateLabel($hasAnytaskToday);
                @endphp

                <time class="fs-1 poppins m-0 p-0">
                    {!! $labelOverview !!}
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

        <div class="row full-height-84vh mt-4"
            style="
                position:relative;
                height: 84vh;
                overflow-x: auto;
                overflow-y: hidden;
            ">

            <div class="d-flex mt-5 p-0"
                style="
                flex-wrap: nowrap;
                height:100%;
            ">
                @for ($i = 0; $i < 24; $i++)

                    @php

                        $now = getCarbonNow();

                        $blockTime = getHourForBlock($i);

                        $startBlockTime = getCarbonTime($blockTime);

                        $tasks = getTasksByStartTime($blockTime, $now);

                        $endBlockTime = $startBlockTime->copy()->addHour()->subSecond();

                        $shouldDisplayTimeMarker = $now->between($startBlockTime, $endBlockTime);

                    @endphp

                    <div id="hour-block" class=""
                        style="
                            border-left: {{ $i === 0 ? '2px dashed #abb2b9' : 'none' }};
                            border-right: 2px dashed #abb2b9;
                            min-width:200px; margin: 0;
                            padding: 0; position:relative;
                        ">

                        @if ($shouldDisplayTimeMarker)
                            @php
                                $blockStartTimeMarkerStartGap = getCarbonTime($blockTime)->diffInMinutes($now);

                                $timeMarkerPosition = ($blockStartTimeMarkerStartGap * 100) / 60;
                            @endphp

                            <div id="time-marker" class="bg-danger p-0" aria-hidden="true"
                                style="
                                    width:3px;
                                    height:85vh;
                                    position:absolute;
                                    left:{{ $timeMarkerPosition }}%;
                                    z-index:2
                            ">
                            </div>
                        @endif

                        @if ($i >= 1)
                            <time id="block-time" class="fs-5 poppins"
                                style="
                                color:#4F4F4F;
                                position:absolute;
                                top:-5%;
                                left:-10%
                            ">
                                {{ $blockTime }}
                            </time>
                        @endif

                        @foreach ($tasks as $index => $task)
                            @php

                                $previousTask = $index > 0 ? $tasks[$index - 1] : null;

                                $previousDuration = null;

                                $previousStart = null;

                                $previousEnd = null;

                                $duration = getDuration($task);

                                $start = getCarbonTime($duration->start);

                                $end = getCarbonTime($duration->end);

                                $overlap = false;

                                if ($previousTask !== null) {
                                    $previousDuration = getDuration($previousTask);

                                    $previousStart = getCarbonTime($previousDuration->start);

                                    $previousEnd = getCarbonTime($previousDuration->end);

                                    $overlap =
                                        ($end->gt($previousStart) && $end->lte($previousEnd)) ||
                                        ($start->lte($previousStart) && $end->gte($previousEnd)) ||
                                        ($start->gte($previousStart) && $start->lte($previousEnd));
                                }

                                $blockStartTaskStartGap = $startBlockTime->diffInMinutes($start);

                                $taskPositionLeft = ($blockStartTaskStartGap * 100) / 60;

                                $durationInMinutes = $start->diffInMinutes($end);

                                $taskContainerWidth = ($durationInMinutes * 200) / 60;

                                $participants = $task->participants()->where('status', 'accepted')->get();
                            @endphp

                            {{-- Bloco da tarefa --}}
                            <a data-overlap="{{ $overlap }}" href="{{ route('task.show', $task->id) }}"
                                class="marked-container text-decoration-none"
                                style="
                                position:absolute;
                                left:{{ $taskPositionLeft }}%;
                                top:30px;
                                min-width:{{ $taskContainerWidth }}px;
                                max-width:{{ $taskContainerWidth }}px;
                                z-index:1;
                                "
                                title="{{ $task->title }}">

                                <div data-task-id="{{ $task->id }}" class="">

                                    <div class="rounded-pill"
                                        style="
                                            min-height:1.5vh;
                                            background-color:{{ $task->creator->color }};
                                        ">
                                    </div>

                                    <div class="d-flex {{ $taskContainerWidth <= 200 ? 'align-items-center flex-column ' : '' }} {{ $taskContainerWidth < 400 ? 'justify-content-center  ' : '' }} mt-1 rounded px-2 py-3"
                                        style="
                                        position-relative;
                                        box-shadow: 1px 1px 10px rgba(0, 0, 0, 0.2);
                                        background-color:white;
                                        overflow:hidden
                                        ">

                                        <div class="rounded-circle d-flex justify-content-center align-items-center ms-2 overflow-hidden"
                                            style="
                                            min-width:80px;
                                            min-height:80px;
                                            max-width:80px;
                                            max-height:80px;
                                            border: 0.3rem solid {{ $task->creator->color }};
                                        ">
                                            <img class="w-100"
                                                src="{{ asset('storage/' . $task->creator->profile_picture) }}"
                                                alt="Imagem do usuário">
                                        </div>

                                        @if ($taskContainerWidth <= 200)
                                            <div class="d-flex flex-column ms-3">
                                                <span
                                                    class="poppins-regular fs-4 text-nowrap mt-2 text-center text-black">{{ \Illuminate\Support\Str::limit($task->title, 14, '...') }}
                                                </span>

                                                <span class="roboto fs-5 text-black">

                                                    @if (count($participants) > 0)
                                                        <span class="roboto d-flex text-secondary mt-2">
                                                            {{-- Ícone de participante --}}
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                width="1.5em" viewBox="0 0 24 24" stroke-width="1.5"
                                                                stroke="currentColor" class="size-6 me-2">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                            </svg>

                                                            {{ count($participants) }}
                                                            {{ count($participants) > 1 ? 'participantes' : 'participante' }}
                                                        </span>
                                                    @endif

                                                    {{-- Ícone do relógio --}}
                                                    <div class="text-secondary mt-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            width="1.5em" viewBox="0 0 24 24" stroke-width="1.5"
                                                            stroke="currentColor" class="size-6">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                        </svg>

                                                        {{ $start->format('H:i') }}
                                                        -
                                                        {{ $end->format('H:i') }}
                                                    </div>

                                                </span>

                                            </div>
                                        @elseif($taskContainerWidth <= 400)
                                            <div class="d-flex flex-column ms-3">
                                                <span
                                                    class="poppins-regular fs-3 text-nowrap text-black">{{ \Illuminate\Support\Str::limit($task->title, 20, '...') }}</span>

                                                <span class="roboto fs-5 text-black">

                                                    @if (count($participants) > 0)
                                                        <span class="roboto d-flex text-secondary mt-2">
                                                            {{-- Ícone de participante --}}
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                width="1.5em" viewBox="0 0 24 24" stroke-width="1.5"
                                                                stroke="currentColor" class="size-6 me-2">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                            </svg>

                                                            {{ count($participants) }}
                                                            {{ count($participants) > 1 ? 'participantes' : 'participante' }}
                                                        </span>
                                                    @endif

                                                    {{-- Ícone do relógio --}}
                                                    <div class="text-secondary mt-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            width="1.5em" viewBox="0 0 24 24" stroke-width="1.5"
                                                            stroke="currentColor" class="size-6">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                        </svg>

                                                        {{ $start->format('H:i') }}
                                                        -
                                                        {{ $end->format('H:i') }}
                                                    </div>

                                                </span>

                                            </div>
                                        @elseif($taskContainerWidth > 400)
                                            <div class="d-flex flex-column ms-3">
                                                <span
                                                    class="poppins-regular fs-3 text-nowrap text-black">{{ $task->title }}</span>

                                                <span class="roboto fs-5 d-flex flex-row text-black">

                                                    @if (count($participants) > 0)
                                                        <span class="roboto d-flex text-secondary">
                                                            {{-- Ícone de participante --}}
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                width="1.5em" viewBox="0 0 24 24" stroke-width="1.5"
                                                                stroke="currentColor" class="size-6 me-2">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                            </svg>

                                                            {{ count($participants) }}
                                                            {{ count($participants) > 1 ? 'participantes' : 'participante' }}
                                                        </span>
                                                        <span class="mx-2">|</span>
                                                    @endif
                                                    {{-- Ícone do relógio --}}
                                                    <div class="text-secondary">


                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            width="1.5em" viewBox="0 0 24 24" stroke-width="1.5"
                                                            stroke="currentColor" class="size-6 me-2">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                        </svg>



                                                        {{ $start->format('H:i') }}
                                                        até
                                                        {{ $end->format('H:i') }}
                                                    </div>

                                                </span>

                                            </div>
                                        @endif

                                    </div>

                                </div>
                            </a>
                        @endforeach

                    </div>
                @endfor

            </div>

        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {

                //Lógica para lidar com a posição do scroll centralizado quando a página recarregar
                const timeMarker = document.querySelector('#time-marker');

                const scrollContainer = document.querySelector('.full-height-84vh');

                const screenWidth = window.innerWidth;

                scrollContainer.scrollTo(0, 0);

                requestAnimationFrame(() => {

                    // scrollContainerLeftPosition = scrollContainer.getBoundingClientRect().left;

                    const timeMarkerHorizontalPostion = timeMarker.getBoundingClientRect().x;

                    const halfScreenWidth = screenWidth / 2

                    const adjustedScrollPosition = timeMarkerHorizontalPostion - halfScreenWidth;

                    // Ajuste o scroll para a posição do timeMarker
                    scrollContainer.scrollTo(adjustedScrollPosition, 0);

                });

            });

            function autoRefreshEveryMinute() {

                console.log("Recarregando a página a cada minuto");
                location.reload();

            }

            setInterval(autoRefreshEveryMinute, 60000);

            // Lógica para lidar com a posição do bloco da tarefa:

            const taskContainers = document.querySelectorAll('.marked-container');

            let previousTaskBottom = 0;

            taskContainers.forEach(taskContainer => {

                const isTaskOverlapping = taskContainer.getAttribute('data-overlap');


                if (isTaskOverlapping) {

                    const previousElement = taskContainer.previousElementSibling;

                    const previousTagName = previousElement.tagName.toLowerCase();

                    if (previousTagName === 'a') {

                        const previousTaskHeight = previousElement.offsetHeight;

                        const previousTaskTop = parseFloat(previousElement.style.top);
                        const newTop = previousTaskTop + previousTaskHeight + 10;

                        taskContainer.style.top = `${newTop}px`;
                    }

                }
            });
        </script>

</body>

</html>
