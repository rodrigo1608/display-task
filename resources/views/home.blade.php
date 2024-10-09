@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="row d-flex justify-content-center flex-row" style=" border-bottom: 1px solid lightgrey;">

            <div class="col-md-8 poppins-extralight my-3">
                @php
                    $today = getCarbonNow()->format('Y-m-d');
                @endphp

                <a href="{{ route('home') }}"
                    class="text-decoration-none fs-5 side-link {{ !request('filter') ? 'selected poppins' : '' }} text-black">
                    Próximas
                </a>

                <a href="{{ route('home', ['filter' => 'concluded']) }}"
                    class="text-decoration-none fs-5 side-link {{ request('filter') == 'concluded' ? 'selected poppins' : '' }} ms-5 text-black">
                    Concluídas
                </a>

                <a href="{{ route('home', ['filter' => 'created']) }}"
                    class="text-decoration-none fs-5 side-link {{ request('filter') == 'created' ? 'selected poppins' : '' }} ms-5 text-black">
                    Criadas por mim
                </a>

                <a href="{{ route('home', ['filter' => 'participating']) }}"
                    class="text-decoration-none fs-5 side-link {{ request('filter') == 'participating' ? 'selected poppins' : '' }} ms-5 text-black">
                    Estou participando
                </a>

            </div>

        </div>

        <div class="container" style="height:81vh; overflow:auto">

            <div class="row mx-0 mb-5 p-0" style="max-widht:100%">

                <div class="col-md-9" style="position:relative">

                    <div class="row mt-5">

                        <div class='col-md-8 mb-2'>
                            <h2 class="fs-4 poppins-extralight me-1">{!! $labelOverview !!}</h2>
                        </div>

                    </div>

                </div>

            </div>

            <div class="row mx-0 p-0" style="max-widht:100%">

                {{-- aqui pra baixo se for colar o cógigo --}}

                @if (is_array($selectedUserTasks))

                    @if (empty($selectedUserTasks))
                        <span>{{ $labelOverview }}</span>
                    @else
                        <div class='col-md-8 p-0'>

                            <div class="w-100 rounded rounded bg-white" id="accordionFlushExample">
                                @php

                                    $now = getCarbonNow();

                                    $todayDayOfWeek = getDayOfWeek($now, 'pt-br');

                                    $tomorrowDayOfWeek = getDayOfWeek($now->copy()->addDay(), 'pt-br');

                                @endphp

                                {{-- @dd($selectedUserTasks); --}}

                                @foreach ($selectedUserTasks as $day => $tasks)
                                    @php
                                        $isToday = $todayDayOfWeek === $day;
                                        $isTomorrow = $tomorrowDayOfWeek == $day;

                                    @endphp

                                    <span class="text-secondary">
                                        @if ($isToday)
                                            Hoje
                                        @elseIf($isTomorrow)
                                            Amanhã
                                        @else
                                            {{ ucfirst($day) }}
                                        @endif
                                    </span>

                                    @foreach ($tasks as $task)
                                        <div class="accordion-item my-2 px-1 ps-3">

                                            <h2 class="accordion-header d-flex">

                                                <button class="accordion-button accordion-button-secondary collapsed py-3"
                                                    type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#flush-collapse{{ $day }}"
                                                    aria-expanded="false" aria-controls="flush-collapseOne">

                                                    <div class="w-100 d-flex flex-column">

                                                        <div
                                                            class="poppins-regular d-flex align-items-center justify-content-between flex-row">

                                                            @php

                                                                $duration = getDuration($task);
                                                                $start = getCarbonTime($duration->start)->format('H:i');
                                                                $end = getCarbonTime($duration->end)->format('H:i');

                                                            @endphp

                                                            <div class="mb-3">
                                                                <span class="fs-2">{{ $start }}</span>
                                                                <span class="poppins-extralight fs-4 mx-2">até</span>
                                                                <span class="fs-2">{{ $end }}</span>
                                                            </div>

                                                            @if ($task->concluded === 'false')
                                                                <div class="me-3 text-end">

                                                                    {{-- ícone do relógio --}}

                                                                    <svg stroke="currentColor" @class([
                                                                        'text-success' => $duration->status === 'starting',
                                                                        'text-warning' => $duration->status === 'in_progress',
                                                                        'text-danger' => $duration->status === 'finished',
                                                                    ])
                                                                        stroke-width="2" xmlns="http://www.w3.org/2000/svg"
                                                                        fill="none" viewBox="0 0 24 24"
                                                                        stroke-width="1.5" class="size-6"
                                                                        style="width: 1.5em; height: 1.5em;">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                                    </svg>

                                                                </div>
                                                            @endif

                                                        </div>

                                                        <div class="">
                                                            <span class="fs-4 poppins">
                                                                {{ $task->title }}
                                                            </span>
                                                        </div>

                                                    </div>

                                                </button>

                                            </h2>

                                            <div id="flush-collapse{{ $day }}"
                                                class="accordion-collapse fs-5 collapse">

                                                <div class="accordion-body mb-4 pb-5"
                                                    style="border-bottom: solid lightgrey 1px;">

                                                    <p class="roboto fs-5 text-secondary w-75 my-4">
                                                        {{ $task->feedbacks[0]->feedback }}
                                                    </p>

                                                    <p class="roboto-light"><span class="roboto">Local:</span>
                                                        {{ $task->local }}
                                                    </p>

                                                    <p class="roboto-light">
                                                        <span class="roboto">
                                                            Responsável:
                                                        </span>
                                                        {{ $task->creator->name }}
                                                        {{ $task->creator->lastname }}
                                                    </p>

                                                    @php
                                                        $participants = getParticipants($task);
                                                    @endphp

                                                    @if (!$participants->isEmpty())
                                                        <div class="d-flex aligm-items-center flex-row">

                                                            <span class="roboto align-self-center">Participante(s):</span>

                                                            @foreach ($participants as $participant)
                                                                <div class="rounded-circle d-flex justify-content-center align-items-center ms-2 overflow-hidden"
                                                                    style="max-width:2.5em; min-width:2.5em; max-height:2.4em; min-height:2.4em; border:solid 0.25em {{ $participant->color }}"
                                                                    title="{{ $participant->name }} {{ $participant->lastname }}">

                                                                    <img class="w-100"
                                                                        src="{{ asset('storage/' . $participant->profile_picture) }}"
                                                                        alt="Imagem do usuário">
                                                                </div>
                                                            @endforeach

                                                        </div>
                                                    @endif

                                                    <p class="roboto-light mt-2">
                                                        {!! $task->recurringMessage !!}
                                                    </p>

                                                    <div class="text-end" title="Ver tarefa">
                                                        <a href="{{ route('task.show', ['task' => $task->id]) }}"
                                                            class="btn btn-secondary"><svg
                                                                xmlns="http://www.w3.org/2000/svg" width="1.5em"
                                                                height="1.5em" fill="none" viewBox="0 0 24 24"
                                                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                            </svg>
                                                        </a>
                                                    </div>

                                                </div>

                                            </div>

                                        </div>
                                    @endforeach
                                @endforeach
                    @endif
            </div>

        </div>
    @else
        <div class='col-md-8 p-0'>
            {{-- accordion --}}
            <div class="w-100 rounded rounded bg-white" id="accordionFlushExample">

                @if (isset($selectedUserTasks))
                    @foreach ($selectedUserTasks as $index => $task)
                        <div class="accordion-item px-1 ps-3">

                            <h2 class="accordion-header d-flex">

                                <button class="accordion-button accordion-button-secondary collapsed py-3" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#flush-collapse{{ $index }}"
                                    aria-expanded="false" aria-controls="flush-collapseOne">

                                    <div class="w-100 d-flex flex-column">

                                        <div
                                            class="poppins-regular d-flex align-items-center justify-content-between flex-row">

                                            <div class="mb-3">
                                                <span class="fs-2">{{ $task->start }}</span> <span
                                                    class="poppins-extralight fs-4 mx-2">até</span>
                                                <span class="fs-2">{{ $task->end }}</span>
                                            </div>

                                            @if ($task->concluded === 'false')
                                                <div class="me-3 text-end">

                                                    <svg stroke="currentColor" @class([
                                                        'text-success' => $task->status === 'starting',
                                                        'text-warning' => $task->status === 'in_progress',
                                                        'text-danger' => $task->status === 'finished',
                                                    ])
                                                        stroke-width="2" xmlns="http://www.w3.org/2000/svg"
                                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                        class="size-6" style="width: 1.5em; height: 1.5em;">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                    </svg>

                                                </div>
                                            @endif

                                        </div>
                                        <div class="">
                                            <span class="fs-4 poppins"> {{ $task->title }}</span>
                                        </div>

                                    </div>

                                </button>

                            </h2>

                            <div id="flush-collapse{{ $index }}" class="accordion-collapse fs-5 collapse">

                                <div class="accordion-body mb-4 pb-5" style="border-bottom: solid lightgrey 1px;">

                                    <p class="roboto fs-5 text-secondary w-75 my-4">
                                        {{ $task->feedbacks[0]->feedback }}
                                    </p>

                                    <p class="roboto-light"><span class="roboto">Local:</span>
                                        {{ $task->local }}
                                    </p>

                                    <p class="roboto-light"><span class="roboto">Criado por:</span>
                                        {{ $task->creator->name }} {{ $task->creator->lastname }}
                                    </p>

                                    @php
                                        $participants = getParticipants($task);
                                    @endphp

                                    <div class="d-flex aligm-items-center flex-row">

                                        <span class="roboto align-self-center">Participantes:</span>

                                        @foreach ($participants as $participant)
                                            <div class="rounded-circle d-flex justify-content-center align-items-center ms-2 overflow-hidden"
                                                style="max-width:2.5em; min-width:2.5em; max-height:2.4em; min-height:2.4em; border:solid 0.25em {{ $participant->color }}"
                                                title="{{ $participant->name }} {{ $participant->lastname }}">


                                                <img class="w-100"
                                                    src="{{ asset('storage/' . $participant->profile_picture) }}"
                                                    alt="Imagem do usuário">
                                            </div>
                                        @endforeach

                                    </div>

                                    <p class="roboto-light mt-2">
                                        {!! $task->recurringMessage !!}
                                    </p>

                                    <div class="text-end" title="Ver tarefa">
                                        <a href="{{ route('task.show', ['task' => $task->id]) }}"
                                            class="btn btn-secondary"><svg xmlns="http://www.w3.org/2000/svg"
                                                width="1.5em" height="1.5em" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                        </a>
                                    </div>

                                </div>

                            </div>

                        </div>
                    @endforeach
                @endif

            </div>

        </div>

        @endif

        @if ($isThereAnyReminder)
            <div class='col-md-4 d-flex justify-content-end ps-5'>

                <div class="rounded-2 w-100 mb-3 me-2">

                    <div class="border-bottom poppins fs-4 px-1 py-3">Lembretes</div>

                    <div class="rounded pt-3">

                        <ul class="roboto text-black" style="list-style-type: circle;">
                            @php

                                $now = getCarbonNow();

                                $todayDayOfWeek = getDayOfWeek($now, 'pt-br');

                                $tomorrowDayOfWeek = getDayOfWeek($now->copy()->addDay(), 'pt-br');

                            @endphp

                            @foreach ($orderedReminders as $day => $reminders)
                                @php
                                    $isToday = $todayDayOfWeek === $day;
                                    $isTomorrow = $tomorrowDayOfWeek == $day;

                                @endphp
                                <span class="poppins-extralight">

                                    @if ($isToday)
                                        Hoje
                                    @elseIf($isTomorrow)
                                        Amanhã
                                    @else
                                        {{ ucfirst($day) }}
                                    @endif
                                </span>

                                @foreach ($reminders as $reminder)
                                    <li class="roboto text-secondary my-2 ms-4">{{ $reminder->title }}</li>
                                @endforeach
                            @endforeach
                        </ul>

                    </div>

                    <div class="card-footer border-top-0 border bg-transparent">

                        <div class="d-flex justify-content-end">

                            <a href="{{ route('reminder.index') }}" class="poppins fs-6 py-2 text-black">

                                {{-- {{ $currentUserReminders->count() > 5 ? 'Ver todos lembretes' : ' Ver detalhes' }} --}}

                            </a>

                        </div>

                    </div>

                </div>

            </div>
        @endif

    </div>


@endsection
