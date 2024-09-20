@extends('layouts.app')

@section('content')

    <div class="container">

        <div class="row mt-5">



            <div class='col'>

                <div class='row d-flex justify-content-evenly align-items-center mb-3 px-5 py-3'>

                    <div class='col-md-7'>

                        @php
                            $labelOverview = getlabelOverviewForDay($date, $hasAnytaskToday);
                        @endphp

                        <h2 class="fs-5 poppins-regular m-0 p-0">{{ $labelOverview }}</h2>
                    </div>

                    <div class="col-md-2 me-5">

                        <form action="{{ route('display.displayDay') }}" method="get">
                            @csrf
                            <div class="d-flex">

                                <input type="date" id="input-date" name="date"
                                    class="form-control rounded-0 rounded-start border-end-1 fs-6"
                                    value="{{ old('specific_date', request()->input('date', Carbon\Carbon::now()->format('Y-m-d'))) }}">

                                {{-- Botão para enviar a pesquisa de tarefas por data --}}
                                <button type="submit" class="btn btn-secondary rounded-end rounded-0 border-start-0 py-0"
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

        <div class="row full-height-93vh">

            <div class="container-day col-md-9 container p-0">

                <div id="current-time-marker" class="bg-danger"
                    style="position:absolute; top:50%; left:0; height: 2px;  width:100%; z-index:2; ">
                </div>

                <div class="d-flex flex-column align-items-center justify-content-center m-0"
                    style="position:absolute; top: {{ $position }}%; left: 0; width:100% ">

                    @for ($i = 0; $i < 24; $i++)
                        @php
                            $blockTime = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
                        @endphp

                        <div class="row border-top border-black" style="position:relative; height:100px; width:98%;">

                            <div class="col-md-2 fs-6" style="position:absolute; top:-13px; left:-17%;">
                                <p class="poppins-light text-secondary text-end">{{ $i }}h</p>
                            </div>

                            @php
                                $tasks = getTasksForDayAndTime($blockTime, $date);
                            @endphp

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

                                    <a href="{{ route('task.show', $task->id) }}"
                                        class="col-md-10 w-100 task-container text-decoration-none rounded border border-2 border-black"
                                        style="height:{{ $taskContainerHeigh }}px; position:absolute; left:0; z-index: 1; background:{{ $task->creator->color }};
                                        top:{{ $taskPositionTop }}%">

                                        <p class="text-white">{{ $task->title }} - {{ $start->format('H:i') }} até
                                            {{ $end->format('H:i') }}</p>
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
            const scrollContainer = document.querySelector('.full-height-78vh');


            if (timeMarker && scrollContainer) {

                let timeMarkerPosition = timeMarker.getBoundingClientRect().top;

                scrollContainer.scrollTop = timeMarkerPosition - scrollContainer.clientHeight / 2;
            }
        })

        function autoRefreshEveryMinute() {

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
