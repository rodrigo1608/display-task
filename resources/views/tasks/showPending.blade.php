@extends('layouts.app')

@section('content')

    <div class="container-fluid pt-4" style="background-color:#E6E6E6; height:94vh; overflow:auto;">

        <div class="container rounded bg-white px-4" style=" width:60%">

            <div class="row d-flex align-items-end py-3">

                <h1 class="poppins fs-3 col-md-8"> {{ $task->title }}</h1>

            </div>

            <div class="row py-3">

                <p class="roboto text-secondary fs-5 col-md-8">{{ $task->description }}</p>

            </div>

            @if ($task->isConcluded)
                <div class="row py-3">

                    <p class="border-info text-info ms-3 rounded border border-2 py-2 text-center">
                        A tarefa foi registrada como concluída
                    </p>
                </div>
            @else
                <div class="row py-3">
                    <p class="roboto col-md-8">{!! $task->recurringMessage !!}</p>
                </div>

                <div class="row text-secondary py-3">

                    <div class="col-md-2 d-flex">
                        {{-- icone de relógio --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" width="1.5em" viewBox="0 0 24 24"
                            stroke-width="1.5   " stroke="currentColor" class="size-6 me-1">

                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />

                        </svg>

                        {{-- input do início da tarefa --}}

                        <input id="start" type="time"
                            class="form-control @error('start') is-invalid @enderror border-0 text-center"
                            value="{{ $task->start }}" readonly>

                        @error('start')
                            <div class="invalid-feedback position-absolute">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <div class="col-md-1 text-center">
                        {{-- icone de seta para direita --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" width="1.6em"
                            stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>

                    </div>
                    <div class="col-md-2 d-flex">
                        {{-- icone de relógio --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" width="1.5em" viewBox="0 0 24 24"
                            stroke-width="1.5   " stroke="currentColor" class="size-6 me-1">

                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />

                        </svg>

                        {{-- input do termino da tarefa --}}
                        <input id="end" type="time"
                            class="form-control fs-6 @error('end') is-invalid @enderror border-0 text-center"
                            value="{{ $task->end }}" readonly>

                        @error('end')
                            <div class="invalid-feedback position-absolute">
                                {{ $message }}
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

                                        <button class="accordion-button text-danger collapsed btn-danger" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#flush-collapseOne"
                                            aria-expanded="false" aria-controls="flush-collapseOne">

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
            @endif

            {{-- Container do cartão com informações sobre o responsável --}}

            <div class="row py-3">

                <div class="col-md-8">

                    <div class="row py-3">
                        <p class="roboto mb-0">Responsável</p>
                    </div>

                    <div class="row m-0 rounded">

                        <div class="col-md-2">

                            <div class="rounded-circle d-flex justify-content-center align-items-center mt-2 overflow-hidden"
                                style="max-width:5rem; min-width:5rem; max-height:5rem; min-height:5rem; border:solid 0.25em {{ $task->creator->color }}"
                                title="{{ $task->creator->name }} {{ $task->creator->lastname }}">

                                <img class="w-100" src="{{ asset('storage/' . $task->creator->profile_picture) }}"
                                    alt="Imagem do usuário">

                            </div>
                        </div>

                        <div class="col-md-10 m-0 p-0">
                            <div class="m-2">

                                <div class="d-flex flex-column">
                                    <h5 class="fs-5 roboto">{{ $task->creator_name }}</h5>

                                    <div class="">
                                        {{-- icone de email --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            width="1.3em" stroke-width="1.5" stroke="currentColor"
                                            class="size-6 text-secondary">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.5 12a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Zm0 0c0 1.657 1.007 3 2.25 3S21 13.657 21 12a9 9 0 1 0-2.636 6.364M16.5 12V8.25" />
                                        </svg>

                                        <span class="roboto-light fs-5 my-1 ms-1">{{ $task->creator_email }}</span>
                                    </div>

                                    <div class="">
                                        {{-- icone telefone --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            width="1.3em" stroke-width="1.5" stroke="currentColor"
                                            class="size-6 text-secondary">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                                        </svg>

                                        <span class="roboto-light fs-5 my-1 ms-1">
                                            {{ getFormatedTelephone($task->creator) }}
                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                </div>

            </div>

            @php
                $hasAnyAttachment = isset($task->attachments) && !empty($task->attachments);
            @endphp

            @if ($hasAnyAttachment)

                <div class="row border-top py-3">
                    <p class="roboto mb-0">Anexo(s)</p>
                </div>

                <div class='row'>

                    <div class="d-flex flex-row flex-wrap">

                        @foreach ($task->attachments as $index => $attachment)
                            <!-- Button trigger modal -->
                            <button type="button" class="btn btn-primary me-3" data-bs-toggle="modal"
                                data-bs-target="#attach{{ $index }}" style="border:1px solid lightgrey">

                                <img style="width:4em" src="{{ asset('storage/' . $attachment->path) }}"
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
                                            <img src="{{ asset('storage/' . $attachment->path) }}" width="100%"
                                                height="auto" alt="Attachment Image">
                                        </div>

                                    </div>

                                </div>

                            </div>
                        @endforeach

                    </div>

                </div>
            @endif

        </div>

        <div class="rounded-bottom container px-4 pb-4 shadow"
            style="width:60%; background-color:#F2F2F2; border-top:solid #D8D8D8 2px">

            @if (!$task->isConcluded)

                @php
                    $firstError = null;

                    // $alertOptions = ['half_an_hour_before', 'one_hour_before', 'two_hours_before', 'one_day_earlier'];

                    foreach ($alertOptions as $alertIndex => $alertValue) {

                        if ($errors->has($alertIndex)) {
                            $firstError = $errors->first($alertIndex);
                            break;
                        }
                    }

                @endphp

                @if ($firstError)
                    <div class="row">

                        <div class="col-md-6 pt-3 invalid-feedback d-block">

                            {{ $firstError }}

                        </div>

                    </div>
                @endif

                @if ($task->shouldDisplayRecurringTimeAlert)

                    <div class="row container mt-4">

                        <div class="col-md-12 my-3">
                            <span
                                class="roboto @if (getDuration($task)->status === 'starting') alert alert-success
                                            @elseif (getDuration($task)->status === 'in_progress')
                                             alert alert-warning
                                            @elseif (getDuration($task)->status === 'finished')
                                             alert alert-danger @endif m-0 rounded py-3">
                                {{ $task->notificationAlert }}
                            </span>
                        </div>

                    </div>

                @endif

                <div class="row mt-4">
                    @if (!$task->shouldHiddenTimeAlertsOptions)
                        <div class="col-4">
                            <p class="roboto m-0">Horário
                                específico
                            </p>
                        </div>

                    @endif
                </div>

                <div class="row m-0">

                    <div class="col-md-11 p-0 px-2">

                        <form class="" action="{{ route('task.acceptPendingTask', $task->id) }}" method="post">
                            @csrf
                            @method('PUT')

                            <input type="hidden" id="formStart" name="start">
                            <input type="hidden" id="formEnd" name="end">

                            <div class="d-flex align-items-top row">

                                @if ($task->shouldHiddenTimeAlertsOptions)
                                    <span
                                        class="fs-6 roboto-semibold col-md-7 @if (getDuration($task)->status === 'starting') alert alert-success
                                            @elseif (getDuration($task)->status === 'in_progress')
                                                alert alert-warning
                                            @elseif (getDuration($task)->status === 'finished')
                                                alert alert-danger @endif border-3 m-0 rounded text-center">
                                        {{ $task->notificationAlert }}
                                    </span>
                                @else

                                    {{-- Input de horário específico --}}

                                    <div class="col-md-3 p-0">

                                        <input id="custom-alert-time" type="time" name="time"
                                            class="form-control fs-6 @error('time') is-invalid @enderror m-0 text-center"
                                            value="{{ old('time') }}">

                                        @error('time')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>

                                    {{-- Accordion horários pré definidos --}}

                                    <div class="col-md-4 offset-1 d-flex align-items-start">

                                        <div class="accordion" id="accordionPanelsStayOpenExample">

                                            <div class="accordion-item m-0 p-0">

                                                <h2 class="accordion-header">

                                                    <button class="accordion-button poppins-regular" type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="false"
                                                        aria-controls="panelsStayOpen-collapseOne">
                                                        Horário pré-definido <span
                                                            class="alertOptionsCounter fs-6 m-2"></span>
                                                    </button>

                                                </h2>

                                                <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse">

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

                                    </div>
                                @endif

                                <div class="col-md-3 offset-1 p-0 text-end">

                                    @if ($task->shoudDisplayButton)
                                        <button class="btn btn-secondary fs-5">Aceitar</button>
                                    @endif

                                </div>

                            </div>

                        </form>

                    </div>

            @endif

            <div class="col h-100">

                {{-- botão de recusar a tarefa --}}

                <button type="button" class="btn rounded-circle btn-outline-danger poppins-regular fs-5 ms-2 border-2"
                    data-bs-toggle="modal" data-bs-target="#deleteParticipantModal">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" width="24"
                        height="24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>

                </button>
            </div>

        </div>

    </div>

    <div class="me-5 mt-5 text-end" style="">

        {{-- botão de voltar --}}

        <a class="btn btn-primary me-3" aria-label="Voltar para a pagina inicial" href="{{ route('home') }}"
            title="voltar">

            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"
                stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
            </svg>
        </a>

    </div>

    {{-- Modal de rejeitar tarefa --}}

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
