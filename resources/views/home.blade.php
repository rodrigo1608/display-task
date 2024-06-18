@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="row vh-100">

            <div class="col-md-2 border-end">

                <ul class="list-group poppins">

                    <a href="#" class="side-link">
                        <li class="list-group-item">Meu Perfil</li>
                    </a>

                    <a href="#" class="side-link">
                        <li class="list-group-item">Meu dia</li>
                    </a>

                    <a href="#" class="side-link">
                        <li class="list-group-item">Minha semana</li>
                    </a>

                    <a href="#" class="side-link">
                        <li class="list-group-item">Meu mês</li>
                    </a>

                    <a href="#" class="side-link">
                        <li class="list-group-item active" aria-current="true">Painel geral</li>
                    </a>

                </ul>

            </div>

            <div class="col-md-9 container" style="position:relative">

                <div class="row">

                    @if (session('success'))
                        <div id="success-alert" class="row" style="position:absolute; left: 80%;">
                            <div class="alert alert-success col-md-4">
                                {{ session('success') }}
                            </div>
                        </div>
                    @endif

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {

                            const alertBox = document.getElementById('success-alert');
                            if (alertBox) {
                                setTimeout(() => {

                                    alertBox.style.transition = 'opacity 1s';

                                    alertBox.style.opacity = '0';

                                    setTimeout(() => {
                                        alertBox.remove();
                                    }, 1000);

                                }, 3000);
                            }
                        });
                    </script>

                    <div
                        class="col-md-1 profile-picture-container profile-border rounded-circle d-flex justify-content-center align-items-center overflow-hidden">

                        <img class="img-size" src="{{ asset('storage/' . $user->profile_picture) }}"
                            alt="Imagem do usuário">

                    </div>

                    <div class='col-md-5 ms-4 text-start'>

                        <h2 class="fs-1 poppins-medium m-0 p-0">
                            {{ $user->name }}
                            {{ $user->lastname }}
                        </h2>

                        <p class="fs-5 roboto">{{ $user->role }} </p>

                        <p class="fs-5 roboto">{{ $user->email }} </p>

                        <p class="fs-5 roboto m-0">{{ $user->telephone }} </p>

                        {{-- <a href="{{ route('user.edit', auth()->id()) }}" class="btn btn-primary mt-4">Editar</a> --}}

                    </div>

                    @if ($isThereAnyReminder)
                        <div class='col-md-2 text-start'>

                            <h2 class="fs-6 poppins-medium" style="color:{{ $user->color }}">
                                Lembretes
                            </h2>

                            <ul class="roboto" style="list-style-type: circle">
                                @foreach ($userReminders->take(5) as $reminder)
                                    <li class="">{{ $reminder->title }}</li>
                                @endforeach
                            </ul>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('reminder.index') }}" class="userReminders-link">
                                    {{ $userReminders->count() > 5 ? 'Ver todos lembretes' : ' Ver detalhes' }}
                                </a>
                            </div>

                        </div>
                    @endif

                </div>
                <div class="row mt-5">

                    <h2 class="fs-2">Meu dia</h2>
                </div>
                <div class="row">
                    <div class="col-md-8 p-0">

                        <div class="row">
                            <p>exemplo</p>
                            @foreach ($myTasksToday as $task)
                                <div class="m-3 rounded bg-white p-2 shadow" style="max-width: 18rem;">

                                    <div class="h4 border-bottom mb-4 border-2 border-black pb-2">
                                        {{ $task->title }}
                                    </div>

                                    <div class="">

                                        <h5 class="roboto">Início: {{ $task->start_time }}</h5>

                                        @php

                                            $startTime = \Carbon\Carbon::parse($task->start_time);
                                            $currentTime = \Carbon\Carbon::now();
                                            $timeDifference = $startTime->diffForHumans($currentTime, [
                                                'parts' => 2,
                                                'join' => true,
                                                'syntax' => \Carbon\CarbonInterface::DIFF_RELATIVE_TO_NOW,
                                            ]);
                                        @endphp

                                        <p class="card-text">Começará: {{ $timeDifference }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>

        </div>


    </div>

    </div>

    </div>

    </div>
@endsection
