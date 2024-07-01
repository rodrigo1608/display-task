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

            <div class="col-md-10 p-5">

                <div class="row">

                    <div class="col-md-10">

                        <div class="card">

                            <div class="card-body ps-5">

                                <div class="row">
                                    <h1 class="poppins-regular"> {{ $task->title }}</h1>
                                </div>

                                <div class="row mt-4">
                                    <p class="poppins-semibold col-md-2">Responsável: </p>
                                    <p class="roboto col-md-4">{{ $task->creator }}</p>
                                    <p class="roboto col-md-2">{{ $task->creator_telephone }} </p>
                                    <p class="roboto col-md-3">{{ $task->creator_email }} </p>
                                </div>

                                <div class="row mt-3">
                                    <p class="poppins-semibold col-md-2">Descrição: </p>
                                    <p class="roboto col-md-8">{{ $task->description }}</p>
                                </div>

                                @php
                                    $hasAnyAttachment = isset($task->attachments) && !empty($task->attachments);
                                @endphp

                                @if ($hasAnyAttachment)
                                    <p class="poppins-semibold col-md-3">Anexos:</p>
                                    <div class="d-flex w-50 flex-row flex-wrap">

                                        @foreach ($task->attachments as $index => $attachment)
                                            {{-- @dd($attachment) --}}

                                            <!-- Button trigger modal -->
                                            <button type="button" class="btn btn-primary me-3" data-bs-toggle="modal"
                                                data-bs-target="#attach{{ $index }}">

                                                <img style="width:100px" src="{{ asset('storage/' . $attachment->path) }}"
                                                    alt="Attachment Image">

                                            </button>

                                            <!-- Modal -->
                                            <div class="modal fade modal-xl" id="attach{{ $index }}" tabindex="-1"
                                                aria-labelledby="exampleModalLabel" aria-hidden="true">

                                                <div class="modal-dialog">

                                                    <div class="modal-content">

                                                        <div class="modal-header">

                                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Anexo
                                                                {{ $index + 1 }}</h1>

                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>

                                                        <div class="modal-body">
                                                            <img src="{{ asset('storage/' . $attachment->path) }}"
                                                                alt="Attachment Image">
                                                        </div>

                                                    </div>

                                                </div>

                                            </div>
                                        @endforeach

                                    </div>
                                @endif

                                <div class="row mt-3">
                                    <p class="poppins-semibold col-md-2">Recorrencia: </p>
                                    <p class="roboto col-md-8">{!! $task->recurringMessage !!}</p>
                                </div>

                            </div>

                            <div class="card-footer ps-5 pt-4">

                                <form action="{{ route('task.acceptPendingTask', ['task' => $task->id]) }}" method="post">
                                    @csrf
                                    @method('PUT')

                                    <div class="row d-flex align-items-center mb-5">

                                        <p class="poppins-semibold col-md-2 mt-4">Duração: </p>

                                        <div class="col-md-2 position-relative">

                                            <label class="poppins fs-6">Começar em: </label>

                                            <input id="start" type="time" name="start"
                                                class="form-control fs-6 @error('start') is-invalid @enderror text-center"
                                                value="{{ old('start') ?? $task->start }}">

                                            @error('start')
                                                <div class="invalid-feedback position-absolute">
                                                    <strong>{{ $message }}</strong>
                                                </div>
                                            @enderror
                                        </div>

                                        <div class="col-md-2 offset-1 position-relative">

                                            <label class="poppins fs-6">Terminar em: </label>

                                            <input id="end" type="time" name="end"
                                                class="form-control fs-6 @error('end') is-invalid @enderror text-center"
                                                value="{{ old('end') ?? $task->end }}">
                                            @error('end')
                                                <div class="invalid-feedback position-absolute">
                                                    <strong>{{ $message }}</strong>
                                                </div>
                                            @enderror

                                        </div>

                                        <!-- Exibir mensagem de erro -->
                                        @php
                                            $conflictingTask = session()->get('conflictingTask');
                                        @endphp

                                        @if ($errors->has('conflictingDuration'))
                                            <div class="alert border-danger my-4 border border-2 bg-transparent">

                                                <p class="text-danger">As durações propostas estão se sobrepondo com uma
                                                    tarefa já
                                                    criada.</p>

                                                <h1></h1>

                                                <div class="accordion accordion-flush" id="accordionFlushExample">

                                                    <div class="accordion-item">

                                                        <h2 class="accordion-header">
                                                            <button
                                                                class="accordion-button text-danger collapsed btn-danger"
                                                                type="button" data-bs-toggle="collapse"
                                                                data-bs-target="#flush-collapseOne" aria-expanded="false"
                                                                aria-controls="flush-collapseOne">
                                                                <p class="poppins-semibold">Clique para ver os detalhes
                                                                </p>
                                                            </button>
                                                        </h2>

                                                        <div id="flush-collapseOne" class="accordion-collapse collapse"
                                                            data-bs-parent="#accordionFlushExample">
                                                            <div class="accordion-body">
                                                                <p class=""><span class="poppins-semibold">Tarefa
                                                                        conflitante:</span>

                                                                    {{ $conflictingTask['title'] }}
                                                                </p>
                                                                <p>
                                                                    <span class="poppins-semibold">
                                                                        Local:
                                                                    </span>
                                                                    {{ $conflictingTask['local'] }}
                                                                </p>

                                                                <p> <span class="poppins-semibold">
                                                                        Criado por:
                                                                    </span> {{ $conflictingTask['owner'] }} </p>

                                                                <p>
                                                                    <span class="poppins-semibold">
                                                                        Contato:
                                                                    </span> {{ $conflictingTask['owner_telehpone'] }}
                                                                </p>
                                                                <p>
                                                                    <span class="poppins-semibold">
                                                                        email:
                                                                    </span>{{ $conflictingTask['owner_email'] }}
                                                                </p>

                                                                <p><span class="poppins-semibold">

                                                                    </span> {!! $conflictingTask['recurringMessage'] !!}</p>

                                                                <p> <span class="poppins-semibold">
                                                                        das {{ $conflictingTask['start'] }} às
                                                                        {{ $conflictingTask['end'] }}
                                                                    </span></p>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="d-flex col-md-3 offset-1 justify-content-end mt-4">
                                            <button type="button"
                                                class="btn btn-outline-danger poppins-regular me-4 border-2"
                                                data-bs-toggle="modal" data-bs-target="#deleteParticipantModal">
                                                Descartar
                                            </button>

                                            <button class="btn btn-secondary fs-5">Aceitar</button>
                                        </div>

                                    </div>
                                </form>

                                <div class="modal fade" id="deleteParticipantModal" tabindex="-1"
                                    aria-labelledby="deleteParticipantModalLabel" aria-hidden="true">

                                    <div class="modal-dialog">

                                        <div class="modal-content">

                                            <div class="modal-header">

                                                <h1 class="modal-title fs-5 poppins-semibold"
                                                    id="deleteParticipantModalLabel">
                                                    Rejeitar convite
                                                </h1>

                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>

                                            <div class="modal-body">
                                                Deseja realmente não participar da tarefa?
                                            </div>


                                            <div class="modal-footer">
                                                <form action="{{ route('participant.destroy') }}" method="post">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="task_id" value={{ $task->id }}>
                                                    <input type="hidden" name="user_id" value={{ auth()->id() }}>

                                                    <button class="btn btn-danger" type="submit">Descartar</button>
                                                </form>
                                            </div>

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
