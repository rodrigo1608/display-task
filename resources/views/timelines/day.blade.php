@extends('layouts.app')

@section('content')

    <div class="container">

        <div class="row mb-5 mt-5">

            <div class='col'>

                <div class='row d-flex justify-content-evenly align-items-center mb-3 px-5 py-3'>

                    <div class='col-md-7 mt-2'>

                        @php
                            $labelOverview = getlabelOverviewForDay($date, $hasAnytaskToday);
                        @endphp

                        <h2 class="fs-4 poppins m-0 p-0">{!! $labelOverview !!}</h2>

                    </div>

                    <div class="col-md-2 me-5">

                        <form action="{{ route('display.day') }}" method="get">
                            @csrf
                            <div class="d-flex">

                                <input type="date" id="input-date" name="date"
                                    class="form-control rounded-0 rounded-start border-end-1 fs-6"
                                    value="{{ old('specific_date', request()->input('date', Carbon\Carbon::now()->format('Y-m-d'))) }}">

                                {{-- Botão para enviar a pesquisa de tarefas por data --}}

                                <button type="submit" class="btn btn-primary rounded-end rounded-0 border-start-0 py-0"
                                    aria-label="Submit date" title="Enviar data">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        width="19" height="19" stroke-width="1.5" stroke="currentColor"
                                        class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                                    </svg>

                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>
        </div>

        <div id="day-schedule-container" class="row full-height-76vh mt-5 rounded"
            style="
        height: 73vh;
        overflow: auto;">

            <div class="container-day col-md-9 container rounded p-0">

                <div class="d-flex flex-column align-items-center justify-content-center m-0 mt-2" style=" width:100% ">

                    @for ($i = 0; $i < 24; $i++)
                        @php

                            $now = getCarbonNow();

                            $blockTime = getHourForBlock($i);

                            $time = getCarbonTime($blockTime);

                            $tasks = getTasksForDayAndTime($blockTime, $date);

                            $timePlusOneHour = $time->copy()->addHour()->subSecond();

                            $shouldDisplayTimeMarker = $now->between($time, $timePlusOneHour);

                            $blockStartTimeMarkerStartGap = getCarbonTime($blockTime)->diffInMinutes($now);

                            $timeMarkerPosition = ($blockStartTimeMarkerStartGap * 100) / 60;

                        @endphp

                        <div class="row"
                            style="
                             border-top-width: 2px;
                             border-top-style: solid;
                            border-color:#E6E6E6;
                            background-color: #FAFAFA;
                            position:relative; height:100px; width:98%;">

                            <div class="col-md-2 fs-6" style="position:absolute; top:-13px; left:-17%;">
                                <p class="poppins-light text-secondary text-end">{{ $i }}h</p>
                            </div>

                            @if ($shouldDisplayTimeMarker)
                                @php
                                    $blockStartTimeMarkerStartGap = getCarbonTime($blockTime)->diffInMinutes($now);

                                    $timeMarkerPosition = ($blockStartTimeMarkerStartGap * 100) / 60;

                                @endphp

                                <div id="time-marker" class="bg-danger"
                                    style="
                                    height:2px;
                                    position:absolute;
                                    top:{{ $timeMarkerPosition }}px;
                                    z-index:3">
                                </div>
                            @endif

                            @if (!$tasks->isEmpty())
                                @foreach ($tasks as $task)
                                    @php

                                        $duration = getDuration($task);

                                        $start = getCarbonTime($duration->start);

                                        $end = getCarbonTime($duration->end);

                                        $blockStartTaskStartGap = getCarbonTime($blockTime)->diffInMinutes($start);

                                        $taskPositionTop = ($blockStartTaskStartGap * 100) / 60;

                                        $durationInMinutes = $start->diffInMinutes($end);

                                        $taskContainerHeigh = ($durationInMinutes * 100) / 60;

                                    @endphp

                                    {{-- Bloco da tarefa --}}
                                    <a href="{{ route('task.show', $task->id) }}"
                                        class="task-container text-decoration-none d-inline-flex flex-row rounded p-0"
                                        style="
                                            min-height:{{ $taskContainerHeigh }}px;
                                            max-height:{{ $taskContainerHeigh }}px;
                                            position:absolute;
                                            left: 50%;
                                            transform: translateX(-50%);
                                            z-index: 1;
                                            top:{{ $taskPositionTop }}%;
                                            width:95%;
                                            align-items: stretch;
                                            "
                                        title="{{ $task->title }}">

                                        <div class="rounded-pill me-1"
                                            style="
                                            min-width:1.5vh;
                                            max-width:1.5vh;
                                            background-color:{{ $task->creator->color }};
                                        ">
                                        </div>

                                        <div class="w-100 ms-1 rounded bg-white px-3"
                                            style="
                                            box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
                                            overflow:hidden;
                                            ">

                                            <div
                                                class="fs-5 poppins d-flex {{ $taskContainerHeigh >= 100 ? 'flex-column' : '' }} text-black">

                                                <span class="">{{ $task->title }}</span>

                                                @php
                                                    $participantsAmount = count(
                                                        $task->participants()->where('status', 'accepted')->get(),
                                                    );
                                                @endphp

                                                @if ($taskContainerHeigh < 100)
                                                    <div class="text-secondary roboto d-flex mx-2 flex-row">

                                                        <span class="mx-2"> | </span>

                                                        {{-- Ícone do relógio --}}
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            width="1.3em" viewBox="0 0 24 24" stroke-width="1.5   "
                                                            stroke="currentColor" class="size-6 mx-1">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                        </svg>

                                                        {{ $start->format('H:i') }} <span class="mx-2"> até</span>
                                                        {{ $end->format('H:i') }}


                                                        @if ($participantsAmount > 0)
                                                            <span class="mx-3"> | </span>

                                                            <span class="d-flex">
                                                                {{-- Ícone de participante --}}
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                    width="1.3em" viewBox="0 0 24 24" stroke-width="1.5"
                                                                    stroke="currentColor" class="size-6 me-1">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                                </svg>

                                                                {{ $participantsAmount }}
                                                                {{ $participantsAmount > 1 ? 'participantes' : 'participante' }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @elseif($taskContainerHeigh < 130)
                                                    <div class="text-secondary roboto d-flex mt-2 flex-row">

                                                        {{-- Ícone do relógio --}}
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            width="1.3em" viewBox="0 0 24 24" stroke-width="1.5"
                                                            stroke="currentColor" class="size-6 m-0 me-1">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                        </svg>

                                                        {{ $start->format('H:i') }} <span class="mx-2"> até</span>
                                                        {{ $end->format('H:i') }}

                                                        @if ($participantsAmount > 0)
                                                            <span class="mx-3"> | </span>
                                                            <span class="d-flex text-secondary">
                                                                {{-- Ícone de participante --}}
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                    width="1.3em" viewBox="0 0 24 24" stroke-width="1.5"
                                                                    stroke="currentColor" class="size-6 me-1">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                                </svg>

                                                                {{ $participantsAmount }}
                                                                {{ $participantsAmount > 1 ? 'participantes' : 'participante' }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-secondary roboto mt-2">
                                                        {{-- Ícone do relógio --}}
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            width="1.3em" viewBox="0 0 24 24" stroke-width="1.5"
                                                            stroke="currentColor" class="size-6 m-0">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                        </svg>

                                                        {{ $start->format('H:i') }} <span class="mx-2"> até</span>
                                                        {{ $end->format('H:i') }}

                                                        @if ($participantsAmount > 0)
                                                            <span class="d-flex text-secondary mt-2">
                                                                {{-- Ícone de participante --}}
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                    width="1.3em" viewBox="0 0 24 24" stroke-width="1.5"
                                                                    stroke="currentColor" class="size-6 me-2">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                                </svg>

                                                                {{ $participantsAmount }}
                                                                {{ $participantsAmount > 1 ? 'participantes' : 'participante' }}
                                                            </span>
                                                        @endif
                                                    </span>
                                                @endif

                                            </div>

                                        </div>
                                    </a>
                                @endforeach
                            @endif

                        </div>
                    @endfor

                </div>

            </div>

        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {


            //Lógica para lidar com a posição do scroll centralizado quando a página recarregar
            const timeMarker = document.querySelector('#time-marker');

            const scrollContainer = document.querySelector('#day-schedule-container');

            console.log(scrollContainer)

            const screenHeight = window.innerHeight;


            scrollContainer.scrollTo(0, 0);

            requestAnimationFrame(() => {

                // scrollContainerLeftPosition = scrollContainer.getBoundingClientRect().left;

                const timeMarkerVerticalPostion = timeMarker.getBoundingClientRect().y;

                const halfScreenHeight = screenHeight / 2

                const adjustedScrollPosition = timeMarkerVerticalPostion - halfScreenHeight;

                // Ajuste o scroll para a posição do timeMarker
                scrollContainer.scrollTo(0, adjustedScrollPosition);

            });

        });

        function autoRefreshEveryMinute() {

            console.log("Recarregando a página a cada minuto");
            location.reload();

        }

        setInterval(autoRefreshEveryMinute, 60000);
    </script>

@endsection
