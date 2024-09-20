@extends('layouts.app')

@section('content')
    <div class="container">

        <form class="d-flex align_items-center justify-content-end mt-5 flex-row gap-2 p-0"
            action="{{ route('display.displayMonth') }}" method="GET">

            <div>

                @php

                    $currentMonthInEN = getCarbonNow()->format('F');

                    $currentMonthPTBR = $months[$currentMonthInEN];

                    $abreviatedCurrentMonthPTBR = mb_substr($currentMonthPTBR, 0, 3, 'UTF-8');

                @endphp

                <select class="btn btn-primary" name="month" id="month">


                    @foreach ($months as $monthInEN => $monthInPTBR)
                        <option value="{{ $monthInEN }}"
                            {{ $monthInPTBR === $selectedMOnthInPortuguese ? 'selected' : '' }}>

                            {{ $monthInPTBR }}

                        </option>
                    @endforeach

                </select>

            </div>

            <div>

                <select class="btn btn-primary" name="year" id="year">

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

            {{-- Botão para enviar a data desejada para ver as tarefas do mês --}}
            <button type="submit" class="btn btn-secondary rounded-circle py-2" title="Enviar data">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" width="19" height="19"
                    stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                </svg>

            </button>

        </form>

        <div class="row m-0 mt-5 p-0">

            @foreach ($carbonWeekDays as $carbonWeekDay)
                @php
                    $nameOfWeekDay = getDayOfWeek($carbonWeekDay, 'pt-br');
                    $abbreviated = mb_substr($nameOfWeekDay, 0, 3, 'UTF-8');
                    $formattedWeekDay = ucfirst($abbreviated) . '.';
                @endphp

                <div class="col p-0">
                    <p class="poppins-extralight fs-6 text-center">{{ $formattedWeekDay }}</p>
                </div>
            @endforeach

        </div>

        @php
            $dayIndex = 0;
            $totalDays = count($daysWithEmpty);

        @endphp

        <div class="full-height-78vh row">

            @foreach ($carbonWeekDays as $carbonWeekDay)
                @php
                    $nameOfWeekDay = getDayOfWeek($carbonWeekDay, 'pt-br');
                @endphp

                <div class='col {{ !$loop->last ? 'border-end' : '' }} p-0'>

                    @foreach ($daysWithEmpty as $day)
                        @if ($nameOfWeekDay === getDayOfWeek($day, 'pt-br'))
                            <form action="{{ route('display.displayDay') }}"method="get">
                                @csrf

                                <input type="hidden" name="date" value="{{ $day->format('Y-m-d') }}">

                                <button type="submit"
                                    class="day-block w-100 align-items-center d-flex flex-column border-top border-0 bg-white">

                                    @php
                                        $isToday = $day->isToday();
                                        $tasks = getTasksForDay($day);
                                    @endphp

                                    <p class="poppins fs-6">
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
                                    </p>

                                    @foreach ($tasks->take(4) as $task)
                                        <div class="w-100 mb-1 rounded ps-2"
                                            style="background-color:{{ $task->creator->color }}; overflow:auto">

                                            <p class="roboto m-0 p-0 text-white">
                                                {{ Str::limit($task->title, 20) }}</p>
                                        </div>
                                    @endforeach

                                    @if (count($tasks) > 4)
                                        @php
                                            $remaining = count($tasks) - 4;
                                            $pluralOrSingular = $remaining === 1 ? 'tarefa' : 'tarefas';
                                        @endphp

                                        <p class="roboto"> + {{ $remaining . $pluralOrSingular }}</p>
                                    @endif

                                </button>

                            </form>
                        @endif
                    @endforeach

                </div>
            @endforeach

            {{-- @for ($week = 0; $week < 6; $week++)
                <div class="row">

                    @for ($dayOfWeek = 0; $dayOfWeek < 7; $dayOfWeek++)
                        @php
                            $currentDay = $daysWithEmpty[$dayIndex];
                        @endphp

                        <div class="col p-0 text-center">

                            <div class="day-block d-flex justify-content-center border">

                                @if ($currentDay)
                                    <p class="poppins fs-6">{{ \Carbon\Carbon::parse($currentDay)->format('j') }}

                                        @php
                                            $isFirstDayOfMonth = $currentDay->isSameDay(
                                                $currentDay->copy()->startOfMonth(),
                                            );
                                        @endphp

                                        @if ($isFirstDayOfMonth)
                                            @php
                                                $monthInEnglish = $currentDay->format('F');
                                                $monthToDisplay = mb_substr($months[$monthInEnglish], 0, 3, 'UTF-8');
                                            @endphp

                                            {{ $monthToDisplay }}
                                        @endif
                                    </p>
                                @else
                                    <p class="poppins fs-6">&nbsp;</p>
                                @endif

                            </div>

                        </div>

                        @php
                            $dayIndex++; // Incrementa o índice para o próximo dia
                        @endphp
                    @endfor

                </div>
            @endfor --}}
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
            const scrollContainer = document.querySelector('.full-height-78vh');

            console.log(todayTarget);

            console.log(scrollContainer);

            if (todayTarget && scrollContainer) {

                let todayTargetPosition = todayTarget.getBoundingClientRect().top;

                scrollContainer.scrollTop = todayTargetPosition - scrollContainer.clientHeight / 2;
            }

        })
    </script>
@endsection
