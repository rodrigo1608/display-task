@extends('layouts.app')

@section('content')

    <div class="container px-5">


        <div class="row m-0 mt-5 p-0 px-5">

            @foreach ($carbonWeekDays as $carbonWeekDay)
                @php
                    $nameOfweekDay = getDayOfWeek($carbonWeekDay, 'pt-br');

                    $abbreviated = mb_substr($nameOfweekDay, 0, 3, 'UTF-8');

                    $formatedWeekDay = ucfirst($abbreviated) . '.';

                @endphp

                <div class="col p-0">

                    <p class="poppins fs-6 text-center">{{ $formatedWeekDay }}</p>

                    <p class="poppins-regular fs-4 text-center">{{ $carbonWeekDay->day }}</p>

                </div>
            @endforeach

        </div>

        <div class="full-height-78vh row m-0 mt-2 p-0 px-5">

            @foreach ($carbonWeekDays as $carbonWeekDay)
                <div class="col m-0 mt-2 p-0" style="{{ $loop->last ? '' : 'border-right: 1px solid lightgrey;' }}">

                    @for ($i = 0; $i < 24; $i++)
                        <div style="border-top: 1px solid black; height:100px; position:relative;">

                            @php

                                $isToday = checkIsToday($carbonWeekDay);

                                $blockTime = getHourForBlock($i);

                                $time = getCarbonTime($blockTime);

                                $tasks = getTasksForDayAndTime($blockTime, $carbonWeekDay);

                                $timePlusOneHour = $time->copy()->addHour()->subSecond();

                                $shouldDisplayTimeMarker = $isToday && $now->between($time, $timePlusOneHour);

                            @endphp

                            @foreach ($tasks as $task)
                                @php

                                    $dayOfWeek = getDayOfWeek($carbonWeekDay);

                                    $recurring = $task->reminder->recurring;

                                    $specificDateWeekday = $recurring->specific_date_weekday;

                                    $isSpecificDateMatchingWeekday = $specificDateWeekday === $dayOfWeek;

                                    $isRecurringOnDay = $recurring->{$dayOfWeek} === 'true';

                                    $shouldDisplayTask = $isSpecificDateMatchingWeekday || $isRecurringOnDay;

                                @endphp

                                @if ($shouldDisplayTask)
                                    @php

                                        $duration = getDuration($task);

                                        $start = getCarbonTime($duration->start);

                                        $end = getCarbonTime($duration->end);

                                        $durationInMinutes = $start->diffInMinutes($end);

                                        $taskContainerHeigh = ($durationInMinutes * 100) / 60;

                                        $blockStartTaskStartGap = getCarbonTime($blockTime)->diffInMinutes($start);

                                        $taskPositionTop = ($blockStartTaskStartGap * 100) / 60;

                                    @endphp

                                    <a href="{{ route('task.show', $task->id) }}"
                                        class="col-md-10 task-container text-decoration-none d-flex rounded px-2"
                                        style="
                                        height:{{ $taskContainerHeigh }}px;
                                        position:absolute;
                                        left: 50%;
                                        transform: translateX(-50%);
                                        z-index: 1;
                                        background-color:white;
                                        border:3px solid {{ $task->creator->color }};
                                        top:{{ $taskPositionTop }}%;
                                        width:96%;
                                        overflow:hidden;
                                        ">

                                        <p class="fs-6 roboto text-black">{{ $task->title }} <span
                                                class="roboto-black mx-2"> |
                                            </span> {{ $start->format('H:i') }}
                                            até
                                            {{ $end->format('H:i') }}
                                        </p>

                                    </a>
                                @endif
                            @endforeach

                            @if ($shouldDisplayTimeMarker)
                                @php
                                    $blockStartTimeMarkerStartGap = getCarbonTime($blockTime)->diffInMinutes($now);

                                    $timeMarkerPosition = ($blockStartTimeMarkerStartGap * 100) / 60;
                                @endphp

                                <div id="time-marker" class="bg-danger"
                                    style="
                                  position:absolute;
                                    top:{{ $timeMarkerPosition }}px;
                                    left: 0;
                                    height: 2px;
                                    width:100%;
                                    z-index:2;">
                                </div>
                            @endif


                            @if ($loop->first)
                                @php
                                    $blockTime = $i . 'h';
                                @endphp

                                <div class="fs-6" style="z-index: 1000 ">

                                    <p class="poppins-light text-secondary text-end"
                                        style="position:absolute;top:-12%;left:-15%;">

                                        {{ $blockTime }}
                                    </p>

                                </div>
                            @endif

                        </div>
                    @endfor

                </div>
            @endforeach
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
