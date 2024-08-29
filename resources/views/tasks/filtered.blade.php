@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="row vh-100">

            <div class="col-md-9 container" style="position:relative">

                <div class="row mt-5">

                    <div class='col-md-8'>
                        {{-- <h2 class="fs-5 me-1">{{ $labelOverview }}</h2> --}}
                    </div>

                </div>

                <div class="row">

                    <div class='col-md-8'>

                        <div class="accordion" id="accordionFlushExample">

                            @foreach ($tasksFilteredByTitle as $index => $task)
                                <div class="accordion-item">

                                    <h2 class="accordion-header">

                                        <button class="accordion-button poppins-semibold collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#flush-collapse{{ $index }}"
                                            aria-expanded="false" aria-controls="flush-collapseOne">

                                            {{ $task->title }}

                                        </button>

                                    </h2>

                                    <div id="flush-collapse{{ $index }}" class="accordion-collapse collapse">

                                        <div class="accordion-body">

                                            <p class="roboto-light fs-5">
                                                {{ $task->reminder->notification_message }}
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

                                            <div class="text-end">
                                                <a href="{{ route('task.show', ['task' => $task->id]) }}"
                                                    class="btn btn-secondary">Ver
                                                    tarefa</a>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            @endforeach

                        </div>



                    </div>

                </div>

            </div>

        </div>

    </div>
@endsection
