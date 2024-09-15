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

                    <div class="w-100">

                        <p class="poppins fs-6 text-center">{{ $formatedWeekDay }}</p>

                        <p class="poppins-regular fs-4 text-center">{{ $weekday->day }}</p>
                    </div>

                    <div class="p-0">

                        {{-- <div id="current-time-line" class="bg-danger"
                                    style="position:absolute; top:50%;  left: 0; height: 2px;  width:100%; z-index:2; ">
                                </div> --}}

                        @for ($i = 0; $i < 24; $i++)
                            <div
                                style="border-top: 1px solid black; {{ !$loop->last ? 'border-right: 1px solid lightgrey;' : '' }}  height:100px; position:relative;">

                                @if ($loop->first)
                                    @php
                                        $blockTime = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
                                    @endphp

                                    <div class="fs-6" style="position:absolute; top: -12%; left:-25%">

                                        <p class="poppins-light text-end">{{ $blockTime }}</p>

                                    </div>
                                @endif
                            </div>
                        @endfor

                    </div>

                </div>
            @endforeach
        </div>
    </div>
@endsection
