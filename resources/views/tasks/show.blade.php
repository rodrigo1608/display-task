@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="row vh-100">

            <div class="col-md-9 container">

                <div class="card mt-5">

                    <div class="card-header text-end">
                        @if (auth()->id() == $task->created_by)
                            <a href="#" class="btn btn-primary">Adicionar participantes</a>
                            <a href="#" class="btn btn-secondary">Marcar como concluída</a>
                        @endif


                    </div>

                    <div class="card-body p-5">

                        <h1>{{ $task->title }}</h1>

                        <p class="roboto fs-4"> {{ $task->description }}</p>

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

    </div>

    </div>
@endsection
