@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="row almost-full-height">

            <div class="container-day col-9 container p-0">

                <div id="current-time-line" class="bg-danger"
                    style="position:absolute; top:50%;  left: 0; height: 3px;  width:100% ">
                </div>

                <div class="bg-alert d-flex flex-column m-0" style="position:absolute;top: 50%; left: 0; width:100%">

                    @for ($i = 0; $i < 24; $i++)
                        <div class="border-top-2 ms-2 border" style="height:60px; width:98%;">
                            {{ $i }}
                        </div>
                    @endfor
                </div>


            </div>

        </div>

    </div>
@endsection
