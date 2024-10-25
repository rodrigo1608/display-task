@extends('layouts.app')

@section('content')
    @php

        $canNotViewTask = auth()->id() !== $task->created_by && !$task->is_participant;

    @endphp

    <div class="container-fluid pt-5" style="background-color:#F2F2F2; height:94vh; overflow:auto ">

        @if ($canNotViewTask)

            <div class="row my-3">

                <div id="warning-alert" class="col-md-6 offset-md-3 justify-content-center mt-4">

                    <div class="alert fs-4 alert-info p-4 text-center">

                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6" width="2em">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>

                        <span> Para acessar esta tarefa, é necessário que você esteja participando dela.</span>

                    </div>

                </div>

            </div>
        @else
            {{-- Container de todos os detalhes das tarefas --}}
            <div class="row my-3">

                <div class="col-md-6 offset-3 rounded bg-white">

                    {{-- Botão de visualizar detalhes --}}
                    <div class="row px-2 pb-2 pt-4">


                        <div class="text-end">

                            <button class="btn btn-primary" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseDetails" aria-expanded="false" aria-controls="collapseDetails"
                                title="Visualizar os detalhes da tarefa">

                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="size-6" width="1.5em">
                                    <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                                    <path fill-rule="evenodd"
                                        d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z"
                                        clip-rule="evenodd" />
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
                                    <svg stroke="currentColor" stroke-width="1.5" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" class="size-6 me-2"
                                        style="width: 0.7em; height: 1em; ">

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
                    <div class="row mb-5">

                        <div class="col-md-10">

                            <p class="roboto fs-5 text-secondary"> {{ $task->description }}</p>

                        </div>

                    </div>

                    {{-- Body com informações detalhadas sobre a tarefa --}}
                    <div class="row mb-5">

                        <div class="fs-5 collapse pb-2" id="collapseDetails">

                            @if (!$task->isConcluded)
                                <div class="row border-top">

                                    <div class="d-flex align-items-center mt-3 flex-row">
                                        {{-- icone do calendário --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24 "
                                            width="1.3em" height="1.3em" stroke-width="1.5" stroke="currentColor"
                                            class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                                        </svg>

                                        <span class="roboto-light ms-3">{!! $task->recurringMessage !!}</span>

                                    </div>

                                </div>

                                <div class="row mt-3">

                                    <div class="d-flex align-items-center flex-row">

                                        {{-- Ícone do relógio --}}

                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" width="1.3em"
                                            viewBox="0 0 24 24" stroke-width="1.5   " stroke="currentColor" class="size-6">

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

                            <div class="row mt-3">

                                {{-- Ícone de localização --}}

                                <div class="">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" width="1.3em" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                    </svg>

                                    <span class="roboto-light ms-3"> {{ $task->local }}</span>
                                </div>

                            </div>

                            <div class="row">
                                <p class="roboto mb-0 mt-3">Autor</p>
                            </div>

                            {{-- Card do autor container --}}
                            <div class="row g-0">

                                <div class="col-md-5 rounded-4 border p-2">

                                    <div class="row g-0">

                                        <div class="col-md-2">
                                            {{-- Imagem de perfil do autor --}}
                                            <div class="rounded-circle d-flex justify-content-center align-items-center m-0 overflow-hidden"
                                                style="max-width:3em; min-width:3em; max-height:3em; min-height:3em; border:solid 0.20em {{ $task->creator->color }}"
                                                title="{{ $task->creator->name }} {{ $task->creator->lastname }}">

                                                <img class="w-100"
                                                    src="{{ asset('storage/' . $task->creator->profile_picture) }}"
                                                    alt="Imagem do usuário">

                                            </div>

                                        </div>

                                        <div class="col-md-10">

                                            <div class="row g-0 ms-1 mt-2">
                                                <h5 class="card-title">{{ $task->creator_name }}</h5>
                                            </div>

                                            {{-- Label do email --}}

                                            <div class="row g-0 ms-3 mt-1">
                                                <div class="clipboradLabel"
                                                    title="Copiar e-mail para a área de transferência">

                                                    <span id="user-email"
                                                        class="roboto-light fs-6">{{ $task->creator_email }}</span>

                                                    {{-- icone de email --}}
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" width="1.3em" stroke-width="1.5"
                                                        stroke="currentColor" class="size-6 text-secondary">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M16.5 12a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Zm0 0c0 1.657 1.007 3 2.25 3S21 13.657 21 12a9 9 0 1 0-2.636 6.364M16.5 12V8.25" />
                                                    </svg>
                                                </div>

                                            </div>

                                            {{-- Label do telefone --}}

                                            <div class="row g-0 ms-3 mt-1">

                                                <div class="clipboradLabel"
                                                    title="Copiar telefone para a área de transferência">

                                                    <span id="user-email" class="roboto-light fs-6">
                                                        {{ getFormatedTelephone($task->creator) }}
                                                    </span>

                                                    {{-- icone telefone --}}
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" width="1.3em" stroke-width="1.5"
                                                        stroke="currentColor" class="size-6 text-secondary">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
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

                            {{-- Cartões de participantes --}}
                            @if ($participants->isNotEmpty())
                                <div class="row mt-3">

                                    <div class="row">
                                        <p class="roboto mb-0">Participantes</p>
                                    </div>

                                    <div class="row">

                                        @foreach ($participants as $participant)
                                            {{-- Cartão do participante --}}
                                            <div class="col-md-5 rounded-4 ms-2 border p-2">

                                                <div class="row g-0">

                                                    <div class="col-md-2">

                                                        {{-- Imagem de perfil do participante --}}
                                                        <div class="rounded-circle d-flex justify-content-center align-items-center m-0 overflow-hidden"
                                                            style="max-width:3em; min-width:3em; max-height:3em; min-height:3em; border:solid 0.20em {{ $participant->color }}"
                                                            title="{{ $participant->name }} {{ $participant->lastname }}">

                                                            <img class="w-100"
                                                                src="{{ asset('storage/' . $participant->profile_picture) }}"
                                                                alt="Imagem do usuário">

                                                        </div>

                                                    </div>

                                                    {{-- Coluna dados do participante --}}
                                                    <div class="col-md-10">

                                                        {{-- Label do nome do participante --}}
                                                        <div class="row g-0 ms-1 mt-2">
                                                            <h5 class="card-title">
                                                                {{ $participant->name }} {{ $participant->lastname }}
                                                            </h5>
                                                        </div>

                                                        {{-- Label do email --}}
                                                        <div class="row g-0 ms-3 mt-1">
                                                            <div class="clipboradLabel"
                                                                title="Copiar e-mail para a área de transferência">

                                                                <span id="user-email" class="roboto-light fs-6">
                                                                    {{ $participant->email }}
                                                                </span>
                                                                {{-- icone de email --}}
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                    viewBox="0 0 24 24" width="1.3em" stroke-width="1.5"
                                                                    stroke="currentColor" class="size-6 text-secondary">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M16.5 12a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Zm0 0c0 1.657 1.007 3 2.25 3S21 13.657 21 12a9 9 0 1 0-2.636 6.364M16.5 12V8.25" />
                                                                </svg>


                                                            </div>

                                                        </div>

                                                        {{-- Label do telefone --}}
                                                        <div class="row g-0 ms-3 mt-1">

                                                            <div class="clipboradLabel"
                                                                title="Copiar telefone para a área de transferência">

                                                                <span id="user-email" class="roboto-light fs-6">
                                                                    {{ getFormatedTelephone($participant) }}
                                                                </span>

                                                                {{-- icone telefone --}}
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                    viewBox="0 0 24 24" width="1.3em" stroke-width="1.5"
                                                                    stroke="currentColor" class="size-6 text-secondary">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                                                                </svg>


                                                            </div>

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>
                                        @endforeach

                                    </div>

                                </div>
                            @endif

                            {{-- Anexos da tarefa --}}
                            @if (!empty($task->attachments))
                                <div class="row mt-3">
                                    <span class="roboto">Anexos</span>
                                </div>
                                <div class="row">

                                    <div class="d-flex flex-row flex-wrap">

                                        @foreach ($task->attachments as $index => $attachment)
                                            <!-- Button trigger modal Anexos-->
                                            <button type="button" class="btn btn-primary me-3" data-bs-toggle="modal"
                                                data-bs-target="#attach{{ $index }}"
                                                style="border:1px solid lightgrey;">

                                                <img style="width:100px"
                                                    src="{{ asset('storage/' . $attachment->path) }}"
                                                    alt="Attachment Image">
                                            </button>

                                            <!-- Modal Anexos-->
                                            <div class="modal fade modal-xl" id="attach{{ $index }}"
                                                tabindex="-1" aria-labelledby="attachmentModalLabel" aria-hidden="true">

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

                                </div>
                            @endif

                        </div>

                    </div>

                </div>

            </div>

            @php
                $feedbacks = $task->feedbacks->skip(1);
            @endphp

            @if (!$feedbacks->isEmpty())
                {{-- Label Comentários/Observações --}}
                <div class="row mt-5">

                    <div class="col-md-6 offset-3">
                        <h2 class="poppins text-secondary fs-5">
                            Comentários <span class="mx-2">/<span> Observações
                        </h2>
                    </div>

                </div>

                {{-- Container de feedbacks --}}
                <div class="row g-0 m-0 mb-5 p-0">

                    <div class="col-md-6 offset-3">

                        <div class="accordion" id="accordionFeedbacks">

                            @foreach ($feedbacks as $key => $feedback)
                                <div class="accordion-item border-bottom border-1 border-0"
                                    style="background-color:#FAFAFA">

                                    <h2 class="accordion-header">

                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#panelsStayOpen-collapse{{ $key }}"
                                            aria-expanded="false"
                                            aria-controls="panelsStayOpen-collapse{{ $key }}">

                                            <div class="d-flex justify-content-center align-items-center">

                                                {{-- Imagem do usuário no feedback --}}
                                                <div
                                                    class="feedback-picture-container justify-content-center feedback-border rounded-circle overflow-hidden">

                                                    <img src="{{ url('storage/' . $feedback->user->profile_picture) }}"
                                                        class="img-size" alt="Imagem do usuário"
                                                        style="max-width: 100%; height: auto;">
                                                </div>

                                                <div class="d-flex align-items-center mx-2 flex-row">
                                                    <p class="fs-5 poppins my-0 ms-2">{{ $feedback->user->name }}
                                                        {{ $feedback->user->lastname }}</p>

                                                    <p class="fs-5 roboto text-secondary my-0 ms-3 text-end">
                                                        {{ $feedback->created_at->format('d/m/Y - H:i') }}</p>
                                                </div>

                                            </div>

                                        </button>

                                    </h2>

                                    {{-- Body do feedback --}}
                                    <div id="panelsStayOpen-collapse{{ $key }}"
                                        class="accordion-collapse collapse">

                                        <div class="accordion-body text-wrap"
                                            style="
                                                overflow-wrap: break-word;
                                                word-wrap: break-word;
                                                white-space: normal;
                                            ">

                                            <div class="p-2">
                                                <p class="roboto fs-6 m-0 p-0" style="color:#2E2E2E">
                                                    {!! $feedback->feedback !!}</p>
                                            </div>

                                            @if (!$feedback->attachments->isEmpty())
                                                <div class="row">
                                                    <span class="roboto fs-5 mb-0 mt-3">Anexos</span>
                                                </div>
                                                <div class="row py-1">

                                                    @foreach ($feedback->attachments as $key => $attachment)
                                                        <!-- Button trigger modal -->
                                                        <div class="col-md-2 me-1">
                                                            <button type="button" class="btn btn-primary"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#feedbackAttachModal{{ $key }}"
                                                                style="border:1px solid lightgrey;">

                                                                <img style="width:100px"
                                                                    src="{{ asset('storage/' . $attachment->path) }}"
                                                                    alt="Attachment Image">

                                                            </button>
                                                        </div>

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
                                                                            data-bs-dismiss="modal"
                                                                            aria-label="Close"></button>

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

                                                </div>
                                            @endif

                                        </div>

                                    </div>

                                </div>
                            @endforeach
                        </div>

                    </div>

                </div>
            @endif


            {{-- Botões voltar e opções --}}
            <div class="col-md-2 position-fixed" style="bottom: 30px; right: 0;">

                {{-- botão de voltar --}}

                <a class="btn btn-primary me-3" aria-label="Voltar para a pagina inicial" href="{{ route('home') }}"
                    title="voltar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                    </svg>
                </a>

                @php
                    $shoudDisplayTaskOptions = $task->is_creator
                        ? !$task->isConcluded
                        : $task->shoudDisplayButton && !$task->isConcluded;
                @endphp

                @if ($shoudDisplayTaskOptions && !$canNotViewTask)
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

    <div class="position-fixed bottom-0 end-0 p-3">
        <button class="btn btn-primary">Botão</button>
    </div>
@endsection
