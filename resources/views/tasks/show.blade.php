@extends('layouts.app')

@section('content')
    <div class="container p-0">
        <div class="row">

            <div class="col-md-9 container p-0">

                <div class="card mt-5">

                    <div class="card-body p-5">

                        <div class="">

                            <div class="row">

                                <div class="col-md-10">

                                    <h1 class="roboto-semibold fs-4">{{ $task->title }}
                                        @if (!$task->isConcluded)
                                            <svg stroke="currentColor" @class([
                                                'text-success' => $task->status === 'starting',
                                                'text-warning' => $task->status === 'in_progress',
                                                'text-danger' => $task->status === 'finished',
                                            ]) stroke-width="2"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                class="size-6" style="width: 1em; height: 2em; ">

                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                        @endif
                                    </h1>

                                    @if ($task->isConcluded)
                                        <p class="text-primary fs-5">
                                            A tarefa está registrada como concluída
                                        </p>
                                    @endif

                                    <p class="roboto fs-5"> {{ $task->description }}</p>

                                </div>

                                {{-- Botão de visualizar detalhes --}}
                                <div class="col-md-2 d-flex align-items-start justify-content-end">

                                    <button class="btn btn-secondary shadow" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseDetails" aria-expanded="false"
                                        aria-controls="collapseDetails" title="Visualizar os detalhes da tarefa">

                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">

                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>

                                    </button>
                                </div>

                            </div>

                        </div>

                        <div class="collapse" id="collapseDetails">

                            <p class= "roboto"><span class="poppins-semibold">Local:</span>
                                {{ $task->local }}
                            </p>

                            <p><span class="poppins-semibold">Criado por:</span> {{ $task->creator_name }}</p>

                            <p><span class="poppins-semibold">Telefone do responsável:</span>
                                {{ $task->creator_telephone }}</p>

                            <p><span class="poppins-semibold">Email do responsável:</span>

                                {{ $task->creator_email }}</p>

                            <p class="roboto"><span class="poppins-semibold">Participantes:</span>
                                {{ $task->emailsParticipants }}
                            </p>

                            @if (!empty($task->attachments))
                                <p><span class="poppins-semibold">Anexos:</span></p>

                                <div class="d-flex flex-row flex-wrap">

                                    @foreach ($task->attachments as $index => $attachment)
                                        <!-- Button trigger modal -->
                                        <button type="button" class="btn btn-primary me-3" data-bs-toggle="modal"
                                            data-bs-target="#attach{{ $index }}">

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

                            @if (!$task->isConcluded)
                                <div class="border-top mb-2 mt-5 border-2">

                                    <p class="mt-4">{!! $task->recurringMessage !!}</p>

                                    <span class="fs-5">{{ $task->start }}</span> <span class="mx-2"> até
                                    </span>
                                    <span class="fs-5">{{ $task->end }}</span>

                                    @if ($task->status === 'starting')
                                        <span class="text-success roboto fs-5 ms-4">
                                            Irá começar
                                        </span>
                                    @elseIf($task->status === 'in_progress')
                                        <span class="text-warning roboto fs-5 ms-4">
                                            Sendo realizada
                                        </span>
                                    @else
                                        <span class="text-danger roboto fs-5 ms-4">
                                            Tempo expirado
                                        </span>
                                    @endif
                                </div>
                            @endif

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="col-md-2 position-fixed" style="bottom: 30px; right: 0;">

        {{-- botão de voltar --}}
        <a class="btn btn-primary me-3" aria-label="Voltar para a pagina inicial" href="{{ route('home') }}"
            title="voltar">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"
                stroke-width="1.5" stroke="currentColor" class="size-6">
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
                                    data-bs-target="#panelsStayOpen-collapse{{ $key }}" aria-expanded="false"
                                    aria-controls="panelsStayOpen-collapse{{ $key }}">

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

                                <div class="accordion-body">
                                    <p class="roboto fs-5">{!! $feedback->feedback !!}</p>

                                    {{-- @dd($feedback->attachments->isEmpty()) --}}

                                    @if (!$feedback->attachments->isEmpty())
                                        <p><span class="poppins-semibold">Anexos:</span></p>

                                        @foreach ($feedback->attachments as $key => $attachment)
                                            <!-- Button trigger modal -->
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#feedbackAttachModal{{ $key }}">

                                                <img style="width:100px"
                                                    src="{{ asset('storage/' . $attachment->path) }}"
                                                    alt="Attachment Image">

                                            </button>

                                            <!-- Modal -->
                                            <div class="modal fade modal-xl" id="feedbackAttachModal{{ $key }}"
                                                tabindex="-1"
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

        <div class="h-100 m-5">


        </div>
    @endif

    </div>

    <!-- Modal -->



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

    {{-- <div class="fixed-bottom w-100 bg-white p-4 text-end" style="right: 130px;">

        @if ($task->is_creator && !$task->isConcluded)

            @if ($task->shoudDisplayButton)
                <button type="button" class="btn btn-primary me-5" data-bs-toggle="modal"
                    data-bs-target="#staticBackdrop">
                    Criar Feedback
                </button>
            @endif

            @php
                $hasSpecificDate = filled($task->reminder->recurring->specific_date);
                $expiredTask = getDuration($task)->status === 'finished';
            @endphp


            @if ($task->shoudDisplayButton)
                <button id="participants-button" type="button" class="btn btn-primary me-4" data-bs-toggle="modal"
                    data-bs-target="#participantsModal">
                    Adicionar participantes
                    <span id="participantCounterDisplay"></span>
                </button>
            @endif

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
            <button type="button" class="btn btn-secondary me-4" data-bs-toggle="modal"
                data-bs-target="#completeTaskModal">
                Marcar como concluída
            </button>
        @endif

    </div> --}}

    <!-- Modal -->
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

                    <form class="d-inline-flex" action="{{ route('tasks.markAsConcluded', $task->id) }}" method="POST">
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
@endsection
