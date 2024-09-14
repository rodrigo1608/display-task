@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="row mt-5">

            @foreach ($weekDays as $weekday)
                @php
                    $nameOfweekDay = getDayOfWeek($weekday, 'pt-br');

                    $abbreviated = mb_substr($nameOfweekDay, 0, 3, 'UTF-8');

                    $formatedWeekDay = ucfirst($abbreviated) . '.';

                @endphp

                <div class="col m-0 p-0">


                    <p class="poppins fs-6 text-center">{{ $formatedWeekDay }}</p>

                    <p class="poppins-regular fs-4 text-center">{{ $weekday->day }}</p>

                </div>
            @endforeach
        </div>
    </div>
@endsection
