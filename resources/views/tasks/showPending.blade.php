@extends('layouts.app')

@section('content')

    <div class="container-fluid">

        <div class="row vh-100">

            <div class="col-md-8 container p-5">

                <div class="card">

                    <div class="card-body ps-5">

                        <div class="row">
                            <h1 class="poppins-regular fs-3"> {{ $task->title }}</h1>
                        </div>

                        <div class="row mt-4">
                            <p class="poppins-semibold col-md-2">Responsável: </p>
                            <p class="roboto col-md-4">{{ $task->creator_name }}</p>
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

                        @if ($task->isConcluded)
                            <p class="border-info text-info ms-3 mt-5 rounded border border-2 py-2 text-center">
                                A tarefa foi registrada como concluída
                            </p>
                        @else
                            <div class="row mt-3">
                                <p class="poppins-semibold col-md-2">Recorrencia: </p>
                                <p class="roboto col-md-8">{!! $task->recurringMessage !!}</p>
                            </div>

                            <div class="row mt-3">

                                <div class="row d-flex align-items-center mb-5">

                                    <p class="poppins-semibold col-md-2 mt-4">Duração: </p>

                                    <div class="col-md-2 position-relative">

                                        <label class="poppins fs-6">Começar em: </label>

                                        <input id="start" type="time"
                                            class="form-control fs-6 @error('start') is-invalid @enderror border-0 text-center"
                                            value="{{ $task->start }}" readonly>

                                        @error('start')
                                            <div class="invalid-feedback position-absolute">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                        @enderror

                                    </div>

                                    <div class="col-md-2 position-relative">

                                        <label class="poppins fs-6">Terminar em: </label>

                                        <input id="end" type="time"
                                            class="form-control fs-6 @error('end') is-invalid @enderror border-0 text-center"
                                            value="{{ $task->end }}" readonly>

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
                                        <div class="alert border-danger my-4 border border-2 bg-white">

                                            <p class="text-danger">As durações propostas estão se sobrepondo com uma
                                                tarefa já
                                                criada.</p>

                                            <h1></h1>

                                            <div class="accordion accordion-flush" id="accordionFlushExample">

                                                <div class="accordion-item">

                                                    <h2 class="accordion-header">

                                                        <button class="accordion-button text-danger collapsed btn-danger"
                                                            type="button" data-bs-toggle="collapse"
                                                            data-bs-target="#flush-collapseOne" aria-expanded="false"
                                                            aria-controls="flush-collapseOne">

                                                            <p class="poppins-semibold">
                                                                Clique para ver os detalhes
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

                                                            <p>
                                                                <span class="poppins-semibold">

                                                                </span> {!! $conflictingTask['recurringMessage'] !!}
                                                            </p>

                                                            <p>
                                                                <span class="poppins-semibold">
                                                                    das {{ $conflictingTask['start'] }} às
                                                                    {{ $conflictingTask['end'] }}
                                                                </span>

                                                            </p>

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>
                                    @endif

                                </div>

                            </div>
                        @endif

                    </div>

                    <div class="card-footer ps-5">

                        <div class="row d-flex align-items-end py-3">

                            @if (!$task->isConcluded)
                                <div class="col-md-10">

                                    <form class="" action="{{ route('task.acceptPendingTask', $task->id) }}"
                                        method="post">
                                        @csrf
                                        @method('PUT')

                                        <input type="hidden" id="formStart" name="start">
                                        <input type="hidden" id="formEnd" name="end">

                                        <div class="row d-flex align-items-end">

                                            <div class="col-md-3 p-0">

                                                <label for="time" class="poppins-regular fs-6 m-0 p-0">Horário da
                                                    notificação</label>

                                                <input id="custom-alert-time" type="time" name="time"
                                                    class="form-control fs-6 @error('time') is-invalid @enderror m-0 text-center"
                                                    value="{{ old('time') }}">

                                                @error('time')
                                                    <div class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </div>
                                                @enderror

                                            </div>

                                            <div class="col-md-1 mt-4 text-center">
                                                <span>
                                                    ou
                                                </span>
                                            </div>

                                            <div class="col-md-4 d-flex align-items-end">

                                                <div class="accordion" id="accordionPanelsStayOpenExample">

                                                    <div class="accordion-item">

                                                        <h2 class="accordion-header">

                                                            <button class="accordion-button poppins-regular"
                                                                type="button" data-bs-toggle="collapse"
                                                                data-bs-target="#panelsStayOpen-collapseOne"
                                                                aria-expanded="false"
                                                                aria-controls="panelsStayOpen-collapseOne">
                                                                Horário pré-definido <span
                                                                    class="alertOptionsCounter fs-6 m-2"></span>
                                                            </button>

                                                        </h2>

                                                        <div id="panelsStayOpen-collapseOne"
                                                            class="accordion-collapse collapse">

                                                            <div class="accordion-body">

                                                                @foreach ($alertOptions as $alertIndex => $alertValue)
                                                                    <div class="form-check">

                                                                        <input class="form-check-input alertOption"
                                                                            type="checkbox" value="true"
                                                                            name="{{ $alertIndex }}"
                                                                            id="alert{{ $alertIndex }}CheckDefault"
                                                                            {{ old($alertIndex) === 'true' ? 'checked' : '' }}>

                                                                        <label class="form-check-label"
                                                                            for="alert{{ $alertIndex }}CheckDefault">
                                                                            {{ $alertValue }}
                                                                        </label>
                                                                    </div>
                                                                @endforeach

                                                            </div>

                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="mt-2">

                                                    @php
                                                        $firstError = null;

                                                        $alertOptions = [
                                                            'half_an_hour_before',
                                                            'one_hour_before',
                                                            'two_hours_before',
                                                            'one_day_earlier',
                                                        ];

                                                        foreach ($alertOptions as $alertIndex) {
                                                            if ($errors->has($alertIndex)) {
                                                                $firstError = $errors->first($alertIndex);
                                                                break;
                                                            }
                                                        }

                                                    @endphp

                                                    @if ($firstError)
                                                        <div class="invalid-feedback d-block">
                                                            <strong>{{ $firstError }}</strong>
                                                        </div>
                                                    @endif

                                                </div>

                                            </div>

                                            <div class="col-md-3 offset-1 p-0 text-end">
                                                <button class="btn btn-secondary fs-5">Aceitar</button>
                                            </div>

                                        </div>

                                    </form>
                                </div>
                            @endif

                            <div class="col-md-2 h-100">
                                <button type="button"
                                    class="btn rounded-pill btn-outline-danger poppins-regular fs-5 ms-2 border-2"
                                    data-bs-toggle="modal" data-bs-target="#deleteParticipantModal">
                                    Descartar
                                </button>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="modal fade" id="deleteParticipantModal" tabindex="-1" aria-labelledby="deleteParticipantModalLabel"
        aria-hidden="true">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">

                    <h1 class="modal-title fs-5 poppins-semibold" id="deleteParticipantModalLabel">
                        Rejeitar convite
                    </h1>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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

    <script>
        const start = document.getElementById('start').value;
        const end = document.getElementById('end').value;

        const formStart = document.getElementById('formStart');
        const formEnd = document.getElementById('formEnd');

        formStart.value = start
        formEnd.value = end

        //Abaixo manipula o registro de quantas opções foram selecionadas no accordion de horário de alerta.

        const handleInputBasedOnCheckboxSelection = (affectedInput, checkBoxesInputs, valueToFill = '') => {

            const isAnyInputChecked = checkBoxesInputs.some(element => element.checked);

            const affectedInputIsEmpty = affectedInput.value.trim() == '';

            const affectedInputMustBeFilled = !isAnyInputChecked && affectedInputIsEmpty;

            affectedInputMustBeFilled ? affectedInput.value = valueToFill : affectedInput.value = '';

        }

        const alertOptionsCounterLabel = document.querySelector('.alertOptionsCounter');

        const customAlertTime = document.querySelector('#custom-alert-time');

        const alertOptionsCollection = document.querySelectorAll('.alertOption');

        const alertOptions = Array.from(alertOptionsCollection);

        const displaySelectedAlertCounter = () => {

            const checkedOptions = alertOptions.filter(option => option.checked);
            const checkedRegister = checkedOptions.length;

            checkedRegister > 0 ? alertOptionsCounterLabel.innerText = ('(' +
                    checkedRegister + ')') :
                alertOptionsCounterLabel.innerText = "";
        }

        displaySelectedAlertCounter();

        alertOptions.forEach(optionAlert => {

            optionAlert.addEventListener('click', () => {

                displaySelectedAlertCounter();

                handleInputBasedOnCheckboxSelection(customAlertTime, alertOptions);

            })

            customAlertTime.addEventListener('change', () => {
                alertOptions.forEach(checkBox => {

                    checkBox.checked = false;
                    alertOptionsCounterLabel.innerText = '';
                });
            })

        })
    </script>

@endsection
