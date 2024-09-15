@extends('layouts.app')

@section('content')

    <div class="container px-5">

        <div class="row m-0 mt-5 p-0 px-5">

            @foreach ($weekDays as $weekday)
                @php
                    $nameOfweekDay = getDayOfWeek($weekday, 'pt-br');

                    $abbreviated = mb_substr($nameOfweekDay, 0, 3, 'UTF-8');

                    $formatedWeekDay = ucfirst($abbreviated) . '.';

                @endphp

                <div class="col p-0">

                    <p class="poppins fs-6 text-center">{{ $formatedWeekDay }}</p>

                    <p class="poppins-regular fs-4 text-center">{{ $weekday->day }}</p>

                </div>
            @endforeach

        </div>



        <div class="full-height-78vh row m-0 mt-2 p-0 px-5">

            @for ($i = 0; $i < 7; $i++)
                <div class="col m-0 mt-2 p-0" style="{{ $i === 6 ? '' : 'border-right: 1px solid lightgrey;' }}">

                    {{-- <div id="current-time-line" class="bg-danger"
                            style="position:absolute; top:50%;  left: 0; height: 2px;  width:100%; z-index:2; ">
                        </div> --}}

                    {{-- {{ !$loop->last ? 'border-right: 1px solid lightgrey;' : '' }} --}}

                    @for ($j = 0; $j < 24; $j++)
                        <div class="row" style="border-top: 1px solid black; height:100px; position:relative;">

                            @if ($i === 0)
                                @php
                                    $blockTime = $j . 'h';
                                @endphp

                                <div class="fs-6" style="z-index: 1000 ">

                                    <p class="poppins-light text-secondary text-end"
                                        style="position:absolute;top:-12%;left:-15%;">
                                        {{ $blockTime }}</p>

                                </div>
                            @endif

                        </div>
                    @endfor

                </div>
            @endfor

        </div>


    </div>
@endsection
