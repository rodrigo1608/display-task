@extends('layouts.app')

@section('content')
    <div class="container">

        {{-- Formulário para o usuário selecionar as tarefas com base no mês e ano --}}
        <form class="d-flex align_items-center justify-content-end mt-5 flex-row gap-2 p-0"
            action="{{ route('display.month') }}" method="GET">

            <div class="d-flex align-items-center gap-1">
                <div    >
                    @php

                        $currentMonthInEN = getCarbonNow()->format('F');

                        $currentMonthPTBR = $months[$currentMonthInEN];

                        $abreviatedCurrentMonthPTBR = mb_substr($currentMonthPTBR, 0, 3, 'UTF-8');

                    @endphp

                    <select class="btn btn-primary px-3 border-0" name="month" id="month">

                        @foreach ($months as $monthInEN => $monthInPTBR)
                            <option value="{{ $monthInEN }}"
                                {{ $monthInPTBR === $selectedMOnthInPortuguese ? 'selected' : '' }}>
                                {{ $monthInPTBR }}
                            </option>
                        @endforeach

                    </select>

                </div>

                <div>

                    <select class="btn btn-primary border-0 px-3 " name="year" id="year">

                        @php

                            $currentYear = date('Y');
                            $startYear = $currentYear - 5;
                            $endYear = $currentYear + 5;

                        @endphp

                        @for ($year = $startYear; $year <= $endYear; $year++)
                            <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endfor

                    </select>

                </div>

                {{-- Botão para enviar a data escolhida em relação ao ano e mês --}}
                <button type="submit" class="btn btn-primary border-0 rounded-circle py-2" title="Enviar data">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" width="19" height="19"
                        stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                    </svg>

                </button>
            </div>

        </form>

        <div class="row m-0 mt-5 p-0">

            @foreach ($carbonWeekDays as $carbonWeekDay)
                @php
                    $nameOfWeekDay = getDayOfWeek($carbonWeekDay, 'pt-br');
                    $abbreviated = mb_substr($nameOfWeekDay, 0, 3, 'UTF-8');
                    $formattedWeekDay = ucfirst($abbreviated) . '.';
                @endphp

                <div class="col mb-3 p-0 text-center">
                    <span id="abreviated-day-of-week-container"
                        class="{{ $carbonWeekDay->isToday() ? 'poppins-semibold' : ' poppins-extralight' }} fs-5">{{ $formattedWeekDay }}
                    </span>
                </div>
            @endforeach
        </div>

        @php
            $dayIndex = 0;
            $totalDays = count($daysWithEmpty);
        @endphp

        <div id="scroll-container" class="row" style="
            height: 75vh;
            overflow: auto;
        ">

            @foreach ($carbonWeekDays as $carbonWeekDay)
                @php
                    $nameOfWeekDay = getDayOfWeek($carbonWeekDay, 'pt-br');
                @endphp

                <div class='col {{ !$loop->last ? 'border-end' : '' }} p-0'>

                    @foreach ($daysWithEmpty as $day)
                        @if ($nameOfWeekDay === getDayOfWeek($day, 'pt-br'))
                            <form action="{{ route('display.day') }}"method="get">
                                @csrf

                                <input type="hidden" name="date" value="{{ $day->format('Y-m-d') }}">

                                @php
                                    $isToday = $day->isToday();

                                    $tasksBulder = getSelectedUserTasksBuilder($day);

                                    $tasks = sortByStart($tasksBulder);

                                @endphp

                                <button type="submit"
                                    class="day-block w-100 align-items-center d-flex flex-column border-top border-0 bg-white">

                                    {{-- @php
                                        $isToday = $day->isToday();
                                        $tasksBulder = getSelectedUserTasksBuilder($day);
                                        $tasks = sortByStart($tasksBulder);
                                        dd($tasksBulder->get());
                                    @endphp --}}

                                    <time class="poppins fs-6">
                                        @if ($isToday)
                                            <span
                                                class="today-target poppins-semibold rounded bg-black px-2 py-1 text-white">{{ $day->format('d') }}</span>
                                        @elseif(!$day->isCurrentMonth())
                                            <span class="text-light-secondary">
                                                {{ $day->format('d') }}</span>
                                        @else
                                            {{ $day->format('d') }}
                                        @endif

                                        @php
                                            $isFirstDayOfMonth = $day->isSameDay($day->copy()->startOfMonth());
                                        @endphp

                                        @if ($isFirstDayOfMonth)
                                            @php
                                                $monthInEnglish = $day->format('F');

                                                $monthToDisplay = mb_substr($months[$monthInEnglish], 0, 3, 'UTF-8');

                                            @endphp

                                            @if (!$day->isCurrentMonth())
                                                <span class="poppins-extralight text-light-secondary">
                                                    {{ $monthToDisplay }}.</span>
                                            @else
                                                <span class="poppins-extralight">
                                                    {{ $monthToDisplay }}.</span>
                                            @endif
                                        @endif
                                    </time>

                                    @foreach ($tasks->take(3) as $task)
                                        @php
                                            $durarion = getDuration($task);
                                            $start = getCarbonTime($durarion->start)->format('H:m');
                                            $end = getCarbonTime($durarion->end)->format('H:m');
                                        @endphp

                                        <div class="d-flex w-100 mb-1 flex-row">

                                            <div class="rounded"
                                                style="
                                                    width:8px;
                                                    background-color:{{ $task->creator->color }};
                                            ">
                                            </div>

                                            <div class="w-100 rounded pe-2"
                                                style="
                                                    overflow:auto;
                                                    box-shadow: 0px 0px 2px rgba(0, 0, 0, 0.3);
                                                ">

                                                <p class="roboto m-0 py-1 ps-1 text-start">
                                                    {{ \Illuminate\Support\Str::limit($task->title, 15) }}
                                                </p>
                                                {{--
                                                <time class="roboto m-0 mt-1 p-0">

                                                    <svg stroke="currentColor" stroke-width="1.5"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" class="size-6 mx-1 p-0"
                                                        style="width: 1.4em; height: 2.4em; ">

                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                    </svg>

                                                    <span class="mx-1"> {{ $start }}</span> -
                                                    <span class="mx-1">{{ $end }}</span>

                                                </time> --}}

                                            </div>

                                        </div>
                                    @endforeach

                                    @if (count($tasks) > 3)
                                        @php
                                            $remaining = count($tasks) - 3;
                                            $pluralOrSingular = $remaining === 1 ? 'tarefa' : 'tarefas';
                                        @endphp

                                        <p class="roboto"> + {{ $remaining . ' ' . $pluralOrSingular }}</p>
                                    @endif

                                </button>

                            </form>
                        @endif
                    @endforeach

                </div>
            @endforeach

        </div>

    </div>

    <style>
        .text-light-secondary {
            color: #A9A9A9;
            /* cor mais clara */
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const todayTarget = document.querySelector('.today-target');
            const scrollContainer = document.querySelector('#scroll-container');

            const screenHeight = window.innerHeight;

            scrollContainer.scrollTo(0, 0);

            requestAnimationFrame(() => {

                // scrollContainerLeftPosition = scrollContainer.getBoundingClientRect().left;

                const timeMarkerVerticalPostion = todayTarget.getBoundingClientRect().y;

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
