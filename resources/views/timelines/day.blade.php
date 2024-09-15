@extends('layouts.app')

@section('content')

    <div class="container">

        <div class="row full-height-93vh">

            <div class="container-day col-9 container p-0">

                <div id="current-time-line" class="bg-danger"
                    style="position:absolute; top:50%;  left: 0; height: 2px;  width:100%; z-index:2; ">
                </div>

                <div id="hour-block" class="bg-alert d-flex flex-column align-items-center justify-content-center m-0"
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
                                $tasks = getTaskAtThatTime($blockTime);
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

                                        $createTaskLinkHeight;

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
        let counter = 0;

        function moveTimeLine() {

            // let hourBlock = document.querySelector('#hour-block');

            // let positionValueOfHourBlock = parseFloat(hourBlock.style.top.slice(0, -1));

            // positionValueOfHourBlock -= 0.185;

            // hourBlock.style.top = positionValueOfHourBlock + '%';

            location.reload();

        }

        setInterval(moveTimeLine, 60000);
    </script>

@endsection





{{-- // Inicializa o valor do top em 50%
let subValue = 50;

// Função que subtrai 0.1% do valor do top a cada minuto


// Chama a função a cada 1 minuto (60000 ms)
// setInterval(moveTimeLine, 60000); --}}
