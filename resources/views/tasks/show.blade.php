@extends('layouts.app')

@section('content')
    <div class="container p-0">

        <div class="row">

            <div class="w-100">

            </div>
            <div class="col-md-9 container p-0">


                <div class="card mt-5">

                    <div class="card-body p-5">

                        <div class="d-flex justify-content-between align-items-end flex-row">

                            <div>
                                <h1 class="poppins-semibold fs-4">{{ $task->title }}</h1>

                                <p class="roboto fs-5"> {{ $task->description }}</p>
                            </div>

                            @if ($task->is_creator)
                                <a class="h-50 btn btn-primary" href="{{ route('task.edit', $task->id) }}">Editar</a>
                            @endif
                        </div>

                        <div class="accordion" id="accordionExample">
                            <div class="accordion-item my-4">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        Detalhes

                                        <div class="ms-2">
                                            <svg stroke="currentColor" @class([
                                                'text-success' => $task->status === 'starting',
                                                'text-warning' => $task->status === 'in_progress',
                                                'text-danger' => $task->status === 'finished',
                                            ]) stroke-width="2"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" class="size-6" style="width: 1em; height: 1em;">

                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                        </div>
                                    </button>
                                </h2>

                                <div id="collapseOne" class="accordion-collapse show collapse"
                                    data-bs-parent="#accordionExample">

                                    <div class="accordion-body">
                                        <p class= "roboto"><span class="poppins-semibold">Local:</span>
                                            {{ $task->local }}
                                        </p>

                                        <p><span class="poppins-semibold">Criado por:</span> {{ $task->creator_name }}</p>

                                        <p><span class="poppins-semibold">Telefone do responsável:</span>
                                            {{ $task->creator_telephone }}</p>

                                        <p><span class="poppins-semibold">Email do responsável:</span>

                                            {{ $task->creator_email }}</p>

                                        <p class="roboto"><span class="poppins-medium">Participantes:</span>
                                            {{ $task->emailsParticipants }}
                                        </p>

                                        @if (!empty($task->attachments))
                                            <p><span class="poppins-semibold">Anexos:</span></p>

                                            <div class="d-flex flex-row flex-wrap">

                                                @foreach ($task->attachments as $index => $attachment)
                                                    {{-- rodrigo --}}
                                                    {{-- @dd($attachment) --}}

                                                    <!-- Button trigger modal -->
                                                    <button type="button" class="btn btn-primary me-3"
                                                        data-bs-toggle="modal" data-bs-target="#attach{{ $index }}">

                                                        <img style="width:100px"
                                                            src="{{ asset('storage/' . $attachment->path) }}"
                                                            alt="Attachment Image">

                                                    </button>

                                                    <!-- Modal -->
                                                    <div class="modal fade modal-xl" id="attach{{ $index }}"
                                                        tabindex="-1" aria-labelledby="attachmentModalLabel"
                                                        aria-hidden="true">

                                                        <div class="modal-dialog">

                                                            <div class="modal-content">

                                                                <div class="modal-header">

                                                                    <h1 class="modal-title fs-5" id="attachmentModalLabel">
                                                                        Anexo
                                                                        {{ $index + 1 }}</h1>

                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>

                                                                <div class="modal-body text-center">
                                                                    <img src="{{ asset('storage/' . $attachment->path) }}"
                                                                        alt="Attachment Image"
                                                                        style="max-width: 100%; height: auto;">
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                @endforeach

                                            </div>
                                        @endif
                                        <p>{!! $task->recurringMessage !!}</p>

                                        <span class="fs-5">{{ $task->start }}</span> <span class="mx-2">-</span>
                                        <span class="fs-5">{{ $task->end }}</span>

                                        @if ($task->status === 'starting')
                                            <span class="text-success roboto fs-6 ms-4">
                                                Irá começar
                                            </span>
                                        @elseIf($task->status === 'in_progress')
                                            <span class="text-warning roboto fs-6 ms-4">
                                                Sendo realizada
                                            </span>
                                        @else
                                            <span class="text-danger roboto fs-6 ms-4">
                                                Tempo expirado
                                            </span>
                                        @endif
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        @php
            $feedbacks = $task->feedbacks->skip(1);
        @endphp

        @if (!$feedbacks->isEmpty())
            <div class="row mt-5">

                <div class="col-md-9 container px-5">

                    <h2 class="fs-4">
                        Comentários/Observações
                    </h2>
                </div>
            </div>

            <div class="row">

                <div class="col-md-9 container p-5">

                    <div class="accordion" id="accordionPanelsStayOpenExample">

                        @foreach ($feedbacks as $key => $feedback)
                            <div class="accordion-item">

                                <h2 class="accordion-header">

                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#panelsStayOpen-collapse{{ $key }}" aria-expanded="true"
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

                                <div id="panelsStayOpen-collapse{{ $key }}"
                                    class="accordion-collapse show collapse">

                                    <div class="accordion-body">
                                        <p class="roboto">{!! $feedback->feedback !!}</p>

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

                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Close</button>
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

                    </div>

                    <div class="modal-body">

                        @foreach ($possibleParticipants as $index => $possibleParticipant)
                            <div class="list-group">

                                <div class="form-check">

                                    <input class="form-check-input participant-checkbox" type="checkbox"
                                        value=" {{ $possibleParticipant->email }}" name="participant{{ $index }}"
                                        id="participant{{ $index }}CheckDefault"
                                        {{ old('participant' . $index) == $possibleParticipant->email ? 'checked' : '' }}>

                                    <label class="form-check-label" for="participant{{ $index }}CheckDefault">
                                        {{ $possibleParticipant->email }}
                                    </label>
                                </div>

                            </div>
                        @endforeach

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Adicionar</button>
                    </div>

                </div>

            </div>

        </div>
    </form>

    <div class="fixed-bottom w-100 bg-white p-4 text-end" style="right: 130px;">

        <button type="button" class="btn btn-primary me-5" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
            Criar Feedback
        </button>

        @if (auth()->id() == $task->created_by)
            @if ($possibleParticipants->isNotEmpty())
                <button id="participants-button" type="button" class="btn btn-primary me-4" data-bs-toggle="modal"
                    data-bs-target="#participantsModal">

                    Adicionar participantes

                    <span id="participantCounterDisplay"></span>

                </button>
            @endif

            <script>
                const participantCheckboxes = document.querySelectorAll('.participant-checkbox');

                const participantCounterDisplay = document.getElementById('participantCounterDisplay');

                console.log(participantCounterDisplay);

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

            <form class="d-inline-flex" action="{{ route('tasks.markAsConcluded', $task->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-secondary me-4">Marcar como concluída</button>
            </form>
        @endif

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

                    <button type="submit" form="feedbackForm" class="btn btn-secondary">Salvar lembrete</button>

                </div>

            </div>

        </div>

    </div>
@endsection
