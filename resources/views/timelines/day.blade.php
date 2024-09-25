@extends('layouts.app')

@section('content')

    <div class="container">

        <div class="'row' mb-5 mt-5">

            <div class='col'>

                <div class='row d-flex justify-content-evenly align-items-center mb-3 px-5 py-3'>

                    <div class='col-md-7 mt-2'>

                        @php
                            $labelOverview = getlabelOverviewForDay($date, $hasAnytaskToday);
                        @endphp

                        <h2 class="fs-4 poppins-regular m-0 p-0">{{ $labelOverview }}</h2>

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
                                    title="Enviar data">
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

        <div id="day-schedule-container" class="row full-height-76vh mt-5">

            <div class="container-day col-md-9 container p-0">

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

                        <div class="row border-top border-black" style="position:relative; height:100px; width:98%;">

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
                                    height:3px;
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
                                        class="col-md-10 task-container text-decoration-none rounded"
                                        style="
                                        height:{{ $taskContainerHeigh }}px;
                                        position:absolute;
                                        left: 50%;
                                        transform: translateX(-50%);
                                        z-index: 1;
                                        background-color:white;
                                        border:3px solid {{ $task->creator->color }};
                                        top:{{ $taskPositionTop }}%;
                                        width:95%;
                                        overflow:hidden
                                        ">

                                        <p class="fs-5 roboto text-black">{{ $task->title }} <span
                                                class="roboto-black mx-2"> |
                                            </span>
                                            {{ $start->format('H:i') }} <span class="mx-2"> até</span>
                                            {{ $end->format('H:i') }}
                                        </p>

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

            const timeMarker = document.querySelector('#time-marker');
            const scrollContainer = document.querySelector('#day-schedule-container');


            if (timeMarker && scrollContainer) {

                let timeMarkerPosition = timeMarker.getBoundingClientRect().top;

                scrollContainer.scrollTop = (timeMarkerPosition - scrollContainer.clientHeight / 2) - 200;

            }
        })

        function autoRefreshEveryMinute() {

            console.log("Recarregando a página a cada minuto");
            location.reload();

        }

        setInterval(autoRefreshEveryMinute, 60000);
    </script>


@endsection


{{-- // Inicializa o valor do top em 50%
let subValue = 50;

// Função que subtrai 0.1% do valor do top a cada minuto


// Chama a função a cada 1 minuto (60000 ms)
// setInterval(moveTimeLine, 60000); --}}
