@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="row vh-100">

            <div class="col-md-9 container" style="position:relative">

                <div class="row mt-5">

                    <div class="col-md-8 d-flex justify-content-end">

                        <form action="{{ route('home') }}" method="get">
                            @csrf
                            <div class="d-flex">

                                <input type="date" id="input-date" name="specific_date"
                                    class="form-control rounded-0 rounded-start border-end-1 fs-6"
                                    value="{{ old('specific_date', request()->input('specific_date', Carbon\Carbon::now()->format('Y-m-d'))) }}">

                                {{-- Botão para enviar a pesquisa de tarefas por data --}}
                                <button type="submit" class="btn btn-secondary rounded-end rounded-0 border-start-0 py-0"
                                    title="Enviar data">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        width="19" height="19" stroke-width="1.5" stroke="currentColor"
                                        class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                                    </svg>

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

                            @if (isset($selectedUserTasks))
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
                                                    @if ($task->concluded === 'false')
                                                        <div class="me-3 text-end">
                                                            <p class="roboto fs-6 my-2 ms-4 ps-4">

                                                                <svg stroke="currentColor" @class([
                                                                    'text-success' => $task->status === 'starting',
                                                                    'text-warning' => $task->status === 'in_progress',
                                                                    'text-danger' => $task->status === 'finished',
                                                                ])
                                                                    stroke-width="2" xmlns="http://www.w3.org/2000/svg"
                                                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                                    class="size-6" style="width: 1em; height: 1em;">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                                </svg>

                                                            </p>

                                                        </div>
                                                    @endif
                                                </div>

                                            </button>

                                        </h2>

                                        <div id="flush-collapse{{ $index }}" class="accordion-collapse collapse">

                                            <div class="accordion-body">

                                                <p class="roboto-light fs-5">
                                                    {{ $task->feedbacks[0]->feedback }}
                                                </p>

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

                                                @if ($task->concluded === 'false')
                                                    @if ($task->status === 'starting')
                                                        <p class="text-success roboto fs-6">
                                                            Irá começar
                                                        </p>
                                                    @elseIf($task->status === 'in_progress')
                                                        <p class="text-warning roboto fs-6">
                                                            Sendo realizada
                                                        </p>
                                                    @else
                                                        <p class="text-danger roboto fs-6">
                                                            Tempo expirado
                                                        </p>
                                                    @endif
                                                @endif

                                                <div class="text-end">
                                                    <a href="{{ route('task.show', ['task' => $task->id]) }}"
                                                        class="btn btn-secondary">Ver tarefa</a>
                                                </div>

                                            </div>

                                        </div>

                                    </div>
                                @endforeach
                            @endif

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
@endsection
