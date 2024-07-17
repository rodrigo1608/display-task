@extends('layouts.app')

@section('content')
    <div class="container p-0">

        <div class="row">

            <div class="col-md-9 container p-0">

                <div class="card mt-5">

                    @if (auth()->id() == $task->created_by)
                        <div class="card-header text-end">
                            <a href="#" class="btn btn-primary">Adicionar participantes</a>
                            <a href="#" class="btn btn-secondary">Marcar como concluída</a>

                        </div>
                    @endif

                    <div class="card-body p-5">

                        <h1 class="poppins-semibold fs-4">{{ $task->title }}</h1>

                        <p class="roboto fs-5"> {{ $task->description }}</p>

                        <div class="accordion" id="accordionExample">
                            <div class="accordion-item my-4">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        Detalhes
                                    </button>
                                </h2>

                                <div id="collapseOne" class="accordion-collapse show collapse"
                                    data-bs-parent="#accordionExample">

                                    <div class="accordion-body">
                                        <p class= "roboto"><span class="poppins-semibold">Local:</span>
                                            {{ $task->local }}
                                        </p>

                                        <p><span class="poppins-semibold">Criado por:</span> {{ $task->creator }}</p>

                                        <p><span class="poppins-semibold">Telefone do responsável:</span>
                                            {{ $task->creator_telephone }}</p>

                                        <p><span class="poppins-semibold">Email do responsável:</span>

                                            {{ $task->creator_email }}</p>

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

                <div class="col-md-9 container">

                    <h2 class="fs-4">
                        Comentários/Observações
                    </h2>
                </div>
            </div>

            <div class="row">

                <div class="col-md-9 container">

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
                                                                <button type="button" class="btn btn-primary">Save
                                                                    changes</button>
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

                <div class="bg-danger m-5">

                </div>

            </div>
        @endif

    </div>

    <div class="fixed-bottom w-100 bg-white p-3 text-end" style=" right: 300px;">

        <button type="button" class="btn btn-secondary me-5" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
            Criar Feedback
        </button>
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
