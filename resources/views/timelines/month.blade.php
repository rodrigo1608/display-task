@extends('layouts.app')

@section('content')

    <div class="container">
        <form action="/your-action" method="GET">
            <div>
                <!-- Select para Meses -->
                <label for="month">Mês:</label>
                <select name="month" id="month">
                    @php
                        $months = [
                            '01' => 'Janeiro',
                            '02' => 'Fevereiro',
                            '03' => 'Março',
                            '04' => 'Abril',
                            '05' => 'Maio',
                            '06' => 'Junho',
                            '07' => 'Julho',
                            '08' => 'Agosto',
                            '09' => 'Setembro',
                            '10' => 'Outubro',
                            '11' => 'Novembro',
                            '12' => 'Dezembro',
                        ];
                        $currentMonth = date('m'); // Mês atual
                    @endphp

                    @foreach ($months as $value => $name)
                        <option value="{{ $value }}" {{ $value == $currentMonth ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <!-- Select para Anos -->
                <label for="year">Ano:</label>
                <select name="year" id="year">
                    @php
                        $currentYear = date('Y'); // Ano atual
                        $startYear = $currentYear - 10; // Ano inicial
                        $endYear = $currentYear + 10; // Ano final
                    @endphp

                    @for ($year = $startYear; $year <= $endYear; $year++)
                        <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endfor
                </select>
            </div>

            <button type="submit">Selecionar</button>
        </form>

        <div class="row m-0 mt-5 p-0">

            @foreach ($carbonWeekDays as $carbonWeekDay)
                @php
                    $nameOfWeekDay = getDayOfWeek($carbonWeekDay, 'pt-br');
                    $abbreviated = mb_substr($nameOfWeekDay, 0, 3, 'UTF-8');
                    $formattedWeekDay = ucfirst($abbreviated) . '.';
                @endphp

                <div class="col p-0">
                    <p class="poppins fs-6 text-center">{{ $formattedWeekDay }}</p>
                </div>
            @endforeach

        </div>

        @php
            $dayIndex = 0;
            $totalDays = count($daysWithEmpty);
        @endphp

        @for ($week = 0; $week < 6; $week++)
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
                                            $monthInEnglish = $currentDay->format('M');
                                        @endphp
                                        {{ $monthsInPortuguese[$monthInEnglish] }}
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
        @endfor


    </div>

    <style>
        .day-block {
            height: 18vh;
        }
    </style>
@endsection
