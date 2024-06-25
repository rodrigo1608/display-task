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

            <div class="col-md-9 container">

                <div class="card text-center">
                    <div class="card-header">
                        <ul class="nav nav-pills card-header-pills">
                            <li class="nav-item">
                                <a class="nav-link active" href="#">Active</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Link</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link disabled" aria-disabled="true">Disabled</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">

                        <h1>Título da tarefa: {{ $task->title }}</h1>

                        <p>Local: {{ $task->local }}</p>

                        <p>Dono: {{ $task->creator }}</p>

                        <p>Dono telefone: {{ $task->creator_telephone }}</p>

                        <p>Dono email: {{ $task->creator_telephone }}</p>

                        <p>Dono email: {{ $task->creator_email }}</p>

                        <p>Descrição: {{ $task->description }}</p>

                        <p>Anexos:</p>

                        <div class="d-flex w-50 flex-row flex-wrap">

                            @foreach ($task->attachments as $index => $attachment)
                                {{-- rodrigo --}}
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

                        <p>Começa em: {{ $task->start }}</p>
                        <p>Termina em: {{ $task->end }}</p>

                        <p>{!! $task->recurringMessage !!}</p>

                    </div>
                </div>


            </div>

        </div>

    </div>

    </div>
@endsection
