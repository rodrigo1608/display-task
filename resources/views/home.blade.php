@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="row vh-100">

            <div class="col-md-9 container" style="position:relative">

                <div class="row">

                    <div class="row mt-5">

                        <div class="col-md-8 d-flex justify-content-end">

                            <form action="{{ route('home') }}" method="get">
                                @csrf

                                <div class="d-flex">

                                    <input type="date" id="input-date" name="specific_date" class="form-control fs-6"
                                        value="{{ old('specific_date', request()->input('specific_date', Carbon\Carbon::now()->format('Y-m-d'))) }}">

                                    <button type="submit" class="btn btn-secondary ms-1 py-0">
                                        Enviar
                                    </button>

                                </div>

                            </form>

                        </div>

                    </div>

                    <div class="row mt-5">

                        <div class='col-md-8'>
                            <h2 class="fs-5 me-1">{{ $labelOverview }}</h2>
                        </div>

                    </div>

                    <div class="row">

                        <div class='col-md-8'>

                            <div class="accordion" id="accordionFlushExample">

                                @foreach ($selectedUserTasks as $index => $task)
                                    <div class="accordion-item">

                                        <h2 class="accordion-header d-flex">

                                            <button class="accordion-button poppins-semibold collapsed" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#flush-collapse{{ $index }}" aria-expanded="false"
                                                aria-controls="flush-collapseOne">

                                                <div class="w-100 d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span class="fs-5">{{ $task->start }}</span> <span
                                                            class="mx-1">-</span>
                                                        <span class="fs-5">{{ $task->end }}</span> <span
                                                            class="mx-1">:</span>
                                                        <span class="fs-5"> {{ $task->title }}</span>
                                                    </div>

                                                    <div class="me-3 text-end">
                                                        <p class="roboto fs-6 my-2 ms-4 ps-4">

                                                            <svg stroke="currentColor" @class([
                                                                'text-success' => $task->isStarting,
                                                                'text-danger' => $task->isFinished,
                                                                'text-warning' => !$task->isStarting && !$task->isFinished,
                                                            ])
                                                                stroke-width="2" xmlns="http://www.w3.org/2000/svg"
                                                                fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                                class="size-6" style="width: 1em; height: 1em;">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                            </svg>

                                                        </p>

                                                    </div>
                                                </div>


                                            </button>

                                        </h2>

                                        <div id="flush-collapse{{ $index }}" class="accordion-collapse collapse">

                                            <div class="accordion-body">

                                                <p class="roboto-light fs-5">
                                                    {{ $task->feedbacks[0]->feedback }}
                                                </p>

                                                @if ($task->isNotificationTimeMissing)
                                                    <p class="text-danger roboto fs-6">Crie um lembrete para ser
                                                        avisado
                                                        antecipadamente, <a href="#"
                                                            class="roboto-bold text-danger">clique
                                                            aqui.</a></p>
                                                @endif

                                                <p class="roboto"><span class="poppins-medium">Local:</span>
                                                    {{ $task->local }}
                                                </p>

                                                <p class="roboto"><span class="poppins-medium">Criado por:</span>
                                                    {{ $task->creator->name }} {{ $task->creator->lastname }}
                                                </p>

                                                <p class="roboto"><span class="poppins-medium">Participantes:</span>
                                                    {{ $task->emailsParticipants }}
                                                </p>

                                                <p class="roboto">
                                                    {!! $task->recurringMessage !!}
                                                </p>

                                                @if ($task->isStarting)
                                                    <p class="text-success roboto fs-6">
                                                        Irá começar</p>
                                                @elseIf($task->isFinished)
                                                    <p class="text-danger roboto fs-6">
                                                        Tempo expirado</p>
                                                @else
                                                    <p class="text-warning roboto fs-6">
                                                        Sendo realizada</p>
                                                    </p>
                                                @endif

                                                <div class="text-end">
                                                    <a href="{{ route('task.show', ['task' => $task->id]) }}"
                                                        class="btn btn-secondary">Ver
                                                        tarefa</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </div>

                        @if ($isThereAnyReminder)
                            <div class='col-md-4 text-start'>

                                <div class="card rounded-2 mb-3 border border-2 border-black shadow-none">

                                    <div class="card-header border-bottom-0 border"> Lembretes</div>

                                    <div class="card-body text-secondary">

                                        <ul class="roboto text-black" style="list-style-type: circle">
                                            @foreach ($orderedReminders as $reminders)
                                                @foreach ($reminders as $reminder)
                                                    <li class="mt-2">{{ $reminder->title }}</li>
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

                </div>

            </div>

        </div>

    </div>
@endsection
