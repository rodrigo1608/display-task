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
                                    <p class="roboto col-md-4">{{ $createdBy->name }} {{ $createdBy->lastname }}</p>
                                    <p class="roboto col-md-2">{{ $createdBy->telephone }} </p>
                                    <p class="roboto col-md-3">{{ $createdBy->email }} </p>
                                </div>


                                <div class="row mt-3">
                                    <p class="poppins-semibold col-md-2">Descrição: </p>
                                    <p class="roboto col-md-8">{{ $description }}</p>
                                </div>

                                @php
                                    $hasAnyAttachment = isset($attachments) && !empty($attachments);
                                @endphp
                                @if ($hasAnyAttachment)
                                    <p class="poppins-semibold col-md-3">Anexos:</p>
                                    <div class="d-flex w-50 flex-row flex-wrap">

                                        @foreach ($attachments as $index => $attachment)
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
                                    <p class="roboto col-md-8">{{ $recurringMessage }}</p>
                                </div>

                            </div>

                            <div class="card-footer ps-5 pt-4">

                                <form action="{{ route('task.update', ['task' => $task->id]) }}" method="post">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                                    <div class="row d-flex align-items-center mb-5">

                                        <p class="poppins-semibold col-md-2 mt-4">Período de Trabalho: </p>

                                        <div class="col-md-2">

                                            <label class="poppins fs-6">Começar em: </label>

                                            <input id="start-time" type="time" name="start_time"
                                                class="form-control fs-6 @error('start-time') is-invalid @enderror text-center"
                                                value="{{ $startTime }}">

                                            @error('start-time')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <div class="col-md-2 offset-1">

                                            <label class="poppins fs-6">Terminar em: </label>

                                            <input id="start-time" type="time" name="end_time"
                                                class="form-control fs-6 @error('end-time') is-invalid @enderror text-center"
                                                value="{{ $endTime }}">

                                            @error('start-time')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                        </div>

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
