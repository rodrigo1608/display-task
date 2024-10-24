@extends('layouts.app')

@section('content')
@php

    $canNotViewTask = auth()->id() !== $task->created_by && !$task->is_participant;

@endphp

@if ($canNotViewTask )

    <div class="container">

        <div id="warning-alert" class="row justify-content-center mt-4 ">

            <div class="alert fs-4 alert-info p-4 text-center shadow">

                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6" width="2em">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>

                <span>  Para acessar esta tarefa, é necessário que você esteja participando dela.</span>

            </div>

        </div>

    </div>

 @else

    <div class="container-fluid pt-5" style="background-color:#F2F2F2; height:94vh; overflow:auto ">

        <div class="w-50 container mt-5 rounded bg-white ps-4">

            <div class="row px-2 pb-4 pt-3">

                {{-- Botão de visualizar detalhes --}}

                <div class="text-end">

                    <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDetails"
                        aria-expanded="false" aria-controls="collapseDetails" title="Visualizar os detalhes da tarefa">

                        <svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">

                            <path stroke-linecap="round" stroke-linejoin="round" d=" M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0
                                8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638
                                0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>

                    </button>

                </div>

            </div>

            {{-- Título da tarefa e label de duração da tarefa container --}}
            <div class="row mb-5 p-0">

                <div class="col-10 d-flex align-items-center flex-row">

                    <h2 class="poppins fs-3 m-0 p-0">{{ $task->title }} </h2>

                    @if (!$task->isConcluded)
                        <div @class([
                            'ms-2',
                            'd-inline-flex px-2 flex-row align-items-center p-0',
                            'alert alert-success' => $task->status === 'starting',
                            'alert alert-warning' => $task->status === 'in_progress',
                            'alert alert-danger' => $task->status === 'finished',
                        ])>
                            <svg stroke="currentColor" stroke-width="1.5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" class="size-6 me-2" style="width: 0.7em; height: 1em; ">

                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>

                            <p class="m-0 p-0" style="font-size: 0.8rem;">
                                @if ($task->status === 'starting')
                                    A começar
                                @elseif ($task->status === 'in_progress')
                                    Em andamento
                                @elseif ($task->status === 'finished')
                                    Finalizada
                                @endif
                            </p>

                        </div>
                    @else
                        <div class="alert alert-info ms-2 p-0 px-2">
                            <p class="m-0 p-0" style="font-size: 0.8rem;">Concluída</p>
                        </div>
                    @endif

                </div>

            </div>

            {{-- Descrição da tarefa container --}}
            <div class="row pb-2">

                <div class="col-md-10">

                    <p class="roboto fs-5 text-secondary"> {{ $task->description }}</p>

                </div>

            </div>

            {{-- Body com informações detalhadas sobre a tarefa --}}

            <div class="fs-5 collapse pb-3" id="collapseDetails">

                <div class="row">

                @if (!$task->isConcluded)

                        <div class="border-top mb-2 mt-4 border-2">

                            <div class="d-flex align-items-center mt-3 flex-row">
                                {{-- icone do calendário --}}
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24 " width="1.3em"
                                    height="1.3em" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                                </svg>

                                <span class="roboto-light ms-3">{!! $task->recurringMessage !!}</span>
                            </div>

                            <div class="d-flex align-items-center mt-3 flex-row">

                                {{-- Ícone do relógio --}}

                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" width="1.3em" viewBox="0 0 24 24"
                                    stroke-width="1.5   " stroke="currentColor" class="size-6">

                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />

                                </svg>

                                <span class="fs-5 roboto ms-3">{{ $task->start }}</span>

                                <span class="roboto-light mx-2">
                                    até
                                </span>

                                <span class="fs-5 roboto">{{ $task->end }}</span>
                            </div>

                        </div>
                    @endif

                    {{-- Ícone de localização --}}
                    <div class="mt-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            width="1.3em" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                        </svg>

                        <span class="roboto-light ms-3"> {{ $task->local }}</span>
                    </div>

                    {{-- Cartão do autor --}}
                    <p class="roboto mb-0 mt-3">Autor</p>

                    <div class="rounded"
                        style="background-color:#f9f9f9;">

                        <div class="row g-0">

                            <div class="col-md-2">

                                <div class="rounded-circle d-flex justify-content-center align-items-center mt-2 overflow-hidden"
                                    style="max-width:3em; min-width:3em; max-height:3em; min-height:3em; border:solid 0.20em {{ $task->creator->color }}"
                                    title="{{ $task->creator->name }} {{ $task->creator->lastname }}">

                                    <img class="w-100" src="{{ asset('storage/' . $task->creator->profile_picture) }}"
                                        alt="Imagem do usuário">

                                </div>
                            </div>

                            <div class="col">

                                <div class="py-2">

                                    <div class="d-inline-flex fs-6 flex-column">

                                        <h5  class="card-title">{{ $task->creator_name }}</h5>

                                        {{-- Label do email --}}
                                     <div class="mx-3 my-1 border px-2 py-1 gap-1 rounded-pill d-inline-flex justify-content-between" style="background-color:#f9f9f9" title="Copiar e-mail para a área de transferência">
                                        <span id="user-email" class="roboto-light ">{{ $task->creator_email }}</span>

                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6" width="1.2em">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184" />
                                          </svg>
                                     </div>

                                        {{-- Label do telefone --}}
                                     <div class="mx-3 my-1 border p-1 px-2 py-1 d-inline-flex justify-content-between align-item-center rounded-pill" style="background-color:#f9f9f9" title="Copiar telefone para a área de transferência">

                                        <span id="user-email" class="roboto-light ">{{ getFormatedTelephone($task->creator) }}</span>

                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6" width="1.2em">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184" />
                                          </svg>
                                     </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    @php
                        $participants = getParticipants($task);
                    @endphp

                    @if ($participants->isNotEmpty())
                        <p class="roboto mb-0 mt-3">Participantes</p>
                        <div class="d-flex flex-column p-0">

                            @foreach ($participants as $participant)
                                <div class="card mb-3"
                                    style="
                                        max-width: 50%;
                                        min-width:50%;
                                        border: 1px solid lightgrey;
                                        box-shadow: none;
                                    ">

                                    <div class="row g-0">

                                        <div class="col-md-2">
                                            <div class="rounded-circle d-flex justify-content-center align-items-center ms-2 mt-2 overflow-hidden"
                                                style="max-width:3em; min-width:3em; max-height:3em; min-height:3em; border:solid 0.20em {{ $participant->color }}"
                                                title="{{ $participant->name }} {{ $participant->lastname }}">

                                                <img class="w-100"
                                                    src="{{ asset('storage/' . $participant->profile_picture) }}"
                                                    alt="Imagem do usuário">

                                            </div>
                                        </div>

                                        <div class="col-md-8">

                                            <div class="card-body">

                                                <div class="d-flex flex-column fs-6">
                                                    <h5 class="card-title">{{ $participant->name }}

                                                        {{ $participant->lastname }}</h5>

                                                    <span class="roboto-light mx-3 my-1">{{ $participant->email }}</span>

                                                    <span
                                                        class="roboto-light mx-3 my-1">{{ getFormatedTelephone($participant) }}</span>
                                                </div>

                                            </div>

                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if (!empty($task->attachments))

                        <p><span class="roboto">Anexos:</span></p>

                        <div class="d-flex flex-row flex-wrap">

                            @foreach ($task->attachments as $index => $attachment)
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-primary me-3" data-bs-toggle="modal"
                                    data-bs-target="#attach{{ $index }}" style="border:1px solid lightgrey;">

                                    <img style="width:100px" src="{{ asset('storage/' . $attachment->path) }}"
                                        alt="Attachment Image">

                                </button>

                                <!-- Modal -->
                                <div class="modal fade modal-xl" id="attach{{ $index }}" tabindex="-1"
                                    aria-labelledby="attachmentModalLabel" aria-hidden="true">

                                    <div class="modal-dialog">

                                        <div class="modal-content">

                                            <div class="modal-header">

                                                <h1 class="modal-title fs-5" id="attachmentModalLabel">
                                                    Anexo
                                                    {{ $index + 1 }}</h1>

                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>

                                            <div class="modal-body text-center">
                                                <img src="{{ asset('storage/' . $attachment->path) }}"
                                                    alt="Attachment Image" style="max-width: 100%; height: auto;">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endforeach

                        </div>
                    @endif

                </div>

            </div>

        </div>

        <div class="col-md-2 position-fixed" style="bottom: 30px; right: 0;">

            {{-- botão de voltar --}}

            <a class="btn btn-primary me-3" aria-label="Voltar para a pagina inicial" href="{{ route('home') }}"
                title="voltar">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                </svg>
            </a>

            @php
                $shoudDisplayTaskOptions = $task->is_creator
                    ? !$task->isConcluded
                    : $task->shoudDisplayButton && !$task->isConcluded;

            @endphp

            @if ($shoudDisplayTaskOptions)
                {{-- Dropdown de opções --}}
                <div class="btn-group dropup">

                    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false" title="Visualizar opções da tarefa">

                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" width="32" height="32"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>

                    </button>

                    <ul class="dropdown-menu fs-5 mb-4">

                        @if ($task->shoudDisplayButton && $task->created_by === auth()->id())
                            <li>
                                <button id="participants-button" type="button" class="dropdown-item poppins-regular"
                                    data-bs-toggle="modal" data-bs-target="#participantsModal">
                                    Adicionar participantes
                                    <span id="participantCounterDisplay"></span>
                                </button>
                            </li>
                        @endif

                        @if ($task->shoudDisplayButton)
                            <li>
                                <button type="button" class="dropdown-item poppins-regular" data-bs-toggle="modal"
                                    data-bs-target="#staticBackdrop">

                                    Criar Feedback

                                </button>
                            </li>

                        @endif

                        @if ($task->is_creator && !$task->isConcluded)
                            <li>
                                <a class="dropdown-item poppins-regular"
                                    href="{{ route('task.edit', $task->id) }}">Editar</a>
                            </li>
                        @endif

                        @if ($task->is_creator)
                            @php
                                $hasSpecificDate = filled($task->reminder->recurring->specific_date);
                                $expiredTask = getDuration($task)->status === 'finished';
                            @endphp

                            <script>
                                const participantCheckboxes = document.querySelectorAll('.participant-checkbox');

                                const participantCounterDisplay = document.getElementById('participantCounterDisplay');

                                function updateParticipantCounter() {

                                    const participantsCheckBoxesInArray = Array.from(participantCheckboxes);
                                    const checkedParticipants = participantsCheckBoxesInArray.filter(checkbox => checkbox.checked);

                                    const checkedCounter = checkedParticipants.length;

                                    const hasAnyParticipant = checkedCounter > 0;

                                    if (hasAnyParticipant) {
                                        participantCounterDisplay.innerText = '(' + checkedCounter + ')';
                                    } else {
                                        participantCounterDisplay.innerText = '';
                                    }
                                }

                                // updateParticipantCounter();

                                participantCheckboxes.forEach(
                                    participantCheckbox => participantCheckbox.addEventListener('change', () =>
                                        updateParticipantCounter()));

                                updateParticipantCounter();
                            </script>

                            <!-- Button trigger modal -->

                            <li>
                                <button type="button" class="dropdown-item poppins-regular" data-bs-toggle="modal"
                                    data-bs-target="#completeTaskModal">
                                    Marcar como concluída
                                </button>

                            </li>

                        @endif

                    </ul>

                </div>
            @endif

        </div>

        @php
            $feedbacks = $task->feedbacks->skip(1);
        @endphp

        @if (!$feedbacks->isEmpty())
            <div class="row mt-5">

                <div class="col-md-6 container">

                    <h2 class="fs-4">
                        Comentários/Observações
                    </h2>
                </div>
            </div>

            <div class="row">

                <div class="col-md-6 container py-5">

                    <div class="accordion" id="accordionFeedbacks">

                        @foreach ($feedbacks as $key => $feedback)
                            <div class="accordion-item border-bottom border-0 border-2">

                                <h2 class="accordion-header">

                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#panelsStayOpen-collapse{{ $key }}"
                                        aria-expanded="false" aria-controls="panelsStayOpen-collapse{{ $key }}">

                                        <div class="d-flex justify-content-center align-items-center">

                                            <div
                                                class="feedback-picture-container justify-content-center feedback-border rounded-circle overflow-hidden">

                                                <img src="{{ url('storage/' . $feedback->user->profile_picture) }}"
                                                    class="img-size" alt="Imagem do usuário"
                                                    style="max-width: 100%; height: auto;">
                                            </div>

                                            <div class="d-flex align-items-center mx-2 flex-row">
                                                <p class="fs-4 my-0 ms-2">{{ $feedback->user->name }}
                                                    {{ $feedback->user->lastname }}</p>

                                                <p class="fs-5 roboto text-secondary my-0 ms-2 ms-3 text-end">
                                                    {{ $feedback->created_at->format('d/m/Y - H:i') }}</p>
                                            </div>

                                        </div>

                                    </button>

                                </h2>

                                <div id="panelsStayOpen-collapse{{ $key }}" class="accordion-collapse collapse">

                                    <div class="accordion-body text-wrap" style="
                                        overflow-wrap: break-word;
                                        word-wrap: break-word;
                                        white-space: normal;
                                    ">

                                        <p class="roboto text-secondary fs-5">{!! $feedback->feedback !!}</p>


                                        @if (!$feedback->attachments->isEmpty())
                                            <p><span class="poppins-semibold">Anexos:</span></p>

                                            @foreach ($feedback->attachments as $key => $attachment)
                                                <!-- Button trigger modal -->
                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#feedbackAttachModal{{ $key }}" style="border:1px solid lightgrey;">

                                                    <img style="width:100px"
                                                        src="{{ asset('storage/' . $attachment->path) }}"
                                                        alt="Attachment Image">

                                                </button>

                                                <!-- Modal de anexar imagem -->
                                                <div class="modal fade modal-xl"
                                                    id="feedbackAttachModal{{ $key }}" tabindex="-1"
                                                    aria-labelledby="feedbackAttachModalLabel{{ $key }}"
                                                    aria-hidden="true">

                                                    <div class="modal-dialog">

                                                        <div class="modal-content">

                                                            <div class="modal-header">

                                                                <h1 class="modal-title fs-5"
                                                                    id="feedbackAttachModal{{ $key }}">
                                                                    Anexo {{ $key }}
                                                                </h1>

                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>

                                                            </div>

                                                            <div class="modal-body">

                                                                <div class="modal-body text-center">

                                                                    <img src="{{ asset('storage/' . $attachment->path) }}"
                                                                        alt="Attachment Image"
                                                                        style="max-width: 100%; height: auto;">

                                                                </div>

                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif

                                    </div>

                                </div>

                            </div>
                        @endforeach
                    </div>

                </div>

            </div>



        @endif

    </div>



    <!-- Modal adicionar participantes -->

    <div class="modal" tabindex="-1">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">Modal title</h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Modal body text goes here.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <form method="post" action="{{ route('participant.add', ['taskID' => $task->id]) }}">
        @csrf
        <div class="modal fade" id="participantsModal" tabindex="-1" aria-labelledby="participantsModalLabel"
            aria-hidden="true">

            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header">

                        <h1 class="modal-title fs-5 poppins-semibold" id="participantsModalLabel">
                            Participantes
                        </h1>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                    </div>

                    <div class="modal-body">
                        @if ($possibleParticipants->isNotEmpty())
                            @foreach ($possibleParticipants as $index => $possibleParticipant)
                                <div class="list-group">

                                    <div class="form-check">

                                        <input class="form-check-input participant-checkbox" type="checkbox"
                                            value=" {{ $possibleParticipant->email }}"
                                            name="participant{{ $index }}"
                                            id="participant{{ $index }}CheckDefault"
                                            {{ old('participant' . $index) == $possibleParticipant->email ? 'checked' : '' }}>

                                        <label class="form-check-label" for="participant{{ $index }}CheckDefault">
                                            {{ $possibleParticipant->email }}
                                        </label>
                                    </div>

                                </div>
                            @endforeach
                        @else
                            <p>Não há participantes disponíveis</p>
                        @endif

                    </div>

                    @if ($possibleParticipants->isNotEmpty())
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Adicionar</button>
                        </div>
                    @else
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    @endif

                </div>

            </div>

        </div>

    </form>

    <!-- Modal de marcar tarefa como concluída -->
    <div class="modal fade" id="completeTaskModal" tabindex="-1" aria-labelledby="completeTaskModalLabel"
        aria-hidden="true">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">

                    <h1 class="modal-title fs-4" id="completeTaskModalLabel">Concluir tarefa</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <p class="roboto fs-5 m-4">Ao concluir esta tarefa, ela será marcada como finalizada. Tem certeza de
                        que
                        deseja
                        continuar?</p>
                </div>
                <div class="modal-footer">

                    <form class="d-inline-flex" action="{{ route('task.markAsConcluded', $task->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-secondary me-4">Confirmar</button>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="fs-4 poppins-regular" id="createReminderkLabel">Criar
                        observação
                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                </div>

                <div class="modal-body">
                    <form method="POST" action="{{ route('feedback.store') }}" enctype="multipart/form-data"
                        id="feedbackForm">

                        @csrf
                        <div class="row mt-3">
                            <input type="hidden" value={{ $task->id }} name="task_id">

                            <div class="">
                                <textarea name="feedback" id="" placeholder="Digite sua mensagem aqui..." cols="30" rows="5"
                                    class="form-control roboto">{{ old('feedback') }}</textarea>
                            </div>

                        </div>

                        <div class="d-flex justify-content-between mt-4">

                            <div class>
                                <input id="task_attachments" name="task_attachments[]" type="file" accept="image/*"
                                    multiple class="d-none" />
                            </div>
                    </form>

                </div>

                <div class="modal-footer">

                    <label for="task_attachments" class="btn-custom btn btn-primary">
                        Anexar imagens
                        <span id="imageCountDisplay"></span>
                    </label>

                    <script>
                        const fileInput = document.querySelector('#task_attachments');
                        const fileLabel = document.querySelector('#imageCountDisplay');

                        task_attachments.addEventListener('change', () => {

                            const selectedFiles = fileInput.files;
                            const pluralOrSingularString = selectedFiles.length > 1 ? `(${selectedFiles.length}) arquivos` :
                                `(${selectedFiles.length}) arquivo`;

                            fileLabel.innerText = pluralOrSingularString;
                        });
                    </script>

                    <button type="submit" form="feedbackForm" class="btn btn-secondary">Salvar</button>

                </div>

            </div>

        </div>

    </div>
  @endif
@endsection
