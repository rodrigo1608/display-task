@extends('layouts.app')

@section('content')
    <div class="fs-5 container">

        <div class="row justify-content-center">

            <div class="col-md-8">

                <div class="card mt-5">

                    <div class="card-header">

                        <h5 class="fs-2 poppins-regular" id="createTaskLabel">Editar
                            tarefa
                        </h5>

                    </div>

                    <div class="card-body my-5 px-5">

                        <form method="POST" action="{{ route('task.update', ['task' => $task->id]) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('put')

                            <div class="row mt-2">

                                <div class="col-9 d-flex">

                                    <p id="task-label" class="poppins-semibold fs-5">

                                    </p>
                                </div>

                                <div class="col-3 d-flex justify-content-end">

                                    @php

                                        $specificDate = filled($task->reminder->recurring->specific_date)
                                            ? getCarbonDate($task->reminder->recurring->specific_date)->format('Y-m-d')
                                            : null;

                                        $now = getCarbonNow()->format('Y-m-d');

                                    @endphp

                                    <input type="date" id="input-date" name="specific_date" class="form-control fs-6"
                                        value="{{ old('specific_date', $specificDate) }}" min="{{ $now }}">
                                </div>

                            </div>

                            {{-- Check boxes dos dias da semana  --}}

                            @php
                                $recurring = $task->reminder->recurring;
                            @endphp

                            <div class="weekdays-selects d-flex justify-content-between mt-3 flex-wrap">

                                <div class="">



                                    <input type="checkbox" name="sunday" value="true" class="btn-check check-box-input"
                                        id="btn-check-outlined-sunday"
                                        {{ old('sunday', $recurring->sunday) == 'true' ? 'checked' : '' }}>

                                    <label
                                        class="week-day btn btn-outline-dark poppins-medium rounded-pill d-flex me-3 mt-4 border-2"
                                        for="btn-check-outlined-sunday">Domingo
                                    </label>

                                </div>

                                <div class="">

                                    <input type="checkbox" name="monday" value="true" class="btn-check check-box-input"
                                        id="btn-check-outlined-monday"
                                        {{ old('monday', $recurring->monday) == 'true' ? 'checked' : '' }}>

                                    <label
                                        class="week-day btn btn-outline-dark poppins-medium rounded-pill d-flex justify-content-center align-items-center mt-4 border-2"
                                        for="btn-check-outlined-monday">Segunda
                                    </label>

                                </div>

                                <div class="">
                                    <input type="checkbox" name="tuesday" value="true" class="btn-check check-box-input"
                                        id="btn-check-outlined-tuesday"
                                        {{ old('tuesday', $recurring->tuesday) == 'true' ? 'checked' : '' }}>

                                    <label
                                        class="week-day btn btn-outline-dark poppins-medium rounded-pill d-flex justify-content-center align-items-center mt-4 border-2"
                                        for="btn-check-outlined-tuesday">Terça
                                    </label>

                                </div>

                                <div class="">
                                    <input type="checkbox" name="wednesday" value="true" class="btn-check check-box-input"
                                        id="btn-check-outlined-wednesday"
                                        {{ old('wednesday', $recurring->wednesday) == 'true' ? 'checked' : '' }}>

                                    <label
                                        class="week-day btn btn-outline-dark poppins-medium rounded-pill d-flex justify-content-center align-items-center mt-4 border-2"
                                        for="btn-check-outlined-wednesday">Quarta
                                    </label>

                                </div>

                                <div class="">

                                    <input type="checkbox" name="thursday" value="true" class="btn-check check-box-input"
                                        id="btn-check-outlined-thursday"
                                        {{ old('thursday', $recurring->thursday) == 'true' ? 'checked' : '' }}>

                                    <label
                                        class="week-day btn btn-outline-dark poppins-medium rounded-pill d-flex justify-content-center align-items-center mt-4 border-2"
                                        for="btn-check-outlined-thursday">Quinta
                                    </label>

                                </div>

                                <div class="">

                                    <input type="checkbox" name="friday" value="true" class="btn-check check-box-input"
                                        id="btn-check-outlined-friday"
                                        {{ old('friday', $recurring->friday) == 'true' ? 'checked' : '' }}>

                                    <label
                                        class="week-day btn btn-outline-dark poppins-medium rounded-pill d-flex justify-content-center align-items-center mt-4 border-2"
                                        for="btn-check-outlined-friday">Sexta
                                    </label>

                                </div>

                                <div class="">

                                    <input type="checkbox" name="saturday" value="true" class="btn-check check-box-input"
                                        id="btn-check-outlined-saturday"
                                        {{ old('saturday', $recurring->saturday) == 'true' ? 'checked' : '' }}>

                                    <label
                                        class="week-day btn btn-outline-dark poppins-medium rounded-pill d-flex justify-content-center align-items-center mt-4 border-2"
                                        for="btn-check-outlined-saturday">Sabado
                                    </label>

                                </div>

                            </div>

                            <div class="row d-flex jutify-content-between mt-5">

                                @php

                                    $userID = auth()->id();

                                    $duration = $task
                                        ->durations()
                                        ->where('user_id', $userID)
                                        ->where('task_id', $task->id)
                                        ->first();

                                    $start = getCarbonTime($duration->start)->format('H:i');
                                    $end = getCarbonTime($duration->end)->format('H:i');

                                @endphp

                                <div class="col-md-3">

                                    <label for="start" class="poppins-regular fs-6 me-3">iniciar em:</label>

                                    <input id="start" type="time" name="start"
                                        class="form-control fs-6 @error('start') is-invalid @enderror text-center"
                                        name="start" value={{ old('start', $start) }}>

                                    @error('start')
                                        <div class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-3 offset-md-1">

                                    <label for="end" class="poppins-regular fs-6 me-3">finalizar em:</label>

                                    <input id="end" type="time" name="end"
                                        class="form-control fs-6 @error('end') is-invalid @enderror text-center"
                                        name="end" value={{ old('end', $end) }}>

                                    @error('end')
                                        <div class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @enderror

                                </div>

                                <div class="col-md-4 offset-md-1">

                                    <label for="local" class="poppins-regular fs-6 me-3">Local
                                        (Opcional)</label>

                                    <input id="local" type="text" name="local" class="form-control fs-6"
                                        name="local" value="{{ old('local', $task->local) }}">

                                </div>
                            </div>

                            <!-- Exibir mensagem de erro de conflito de duração da tarefa -->

                            @php
                                $conflictingTask = session()->get('conflictingTask');
                            @endphp

                            @if ($errors->has('conflictingDuration'))
                                <div class="alert border-danger my-4 border border-2 bg-transparent">

                                    <p class="text-danger">As durações propostas estão se sobrepondo com uma tarefa já
                                        criada.</p>

                                    <h1></h1>

                                    <div class="accordion accordion-flush" id="accordionFlushExample">

                                        <div class="accordion-item">

                                            <h2 class="accordion-header">
                                                <button class="accordion-button text-danger collapsed btn-danger"
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

                            <div class="row d-flex align-items-start mt-5">
                                @php
                                    $notificationTime = $task->reminder
                                        ->notificationTimes()
                                        ->where('user_id', $userID)
                                        ->first();

                                    $customTime = filled($notificationTime->custom_time)
                                        ? getCarbonTime($notificationTime->custom_time)
                                        : null;

                                @endphp
                                <div class="col-md-5">

                                    <label for="time" class="poppins-regular fs-6 m-0">Horário da
                                        notificação</label>

                                    <input id="custom-alert-time" type="time" name="time"
                                        class="form-control fs-6 @error('time') is-invalid @enderror m-0 text-center"
                                        {{-- Caso haja a necessidade do old por algum motivo, o código comentado ao lado é a alternativa --> value="{{ old('time', $customTime ? $customTime->format('H:i') : '') }}"> --}} value="">

                                    @error('time')
                                        <div class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @enderror

                                </div>

                                <span class="col-md-1 mt-4 text-center">
                                    ou
                                </span>

                                <div class="col-md-6 mt-3">
                                    <div class="accordion" id="accordionPanelsStayOpenExample">

                                        <div class="accordion-item">

                                            <h2 class="accordion-header">

                                                <button class="accordion-button poppins-regular" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne"
                                                    aria-expanded="false" aria-controls="panelsStayOpen-collapseOne">
                                                    Horário pré-definido <span
                                                        class="alertOptionsCounter fs-6 ms-2"></span>
                                                </button>
                                            </h2>

                                            <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse">

                                                <div class="accordion-body">

                                                    @foreach ($alertOptions as $alertIndex => $alertValue)
                                                        <div class="form-check">

                                                            <input class="form-check-input alertOption" type="checkbox"
                                                                value="true" name="{{ $alertIndex }}"
                                                                id="alert{{ $alertIndex }}CheckDefault"
                                                                {{-- Caso haja a necessidade do old por algum motivo, o código comentado ao lado é a alternativa -->  {{ old($alertIndex, $notificationTime->$alertIndex) === 'true' ? 'checked' : '' }}> --}}>

                                                            <label class="form-check-label"
                                                                for="alert{{ $alertIndex }}CheckDefault">
                                                                {{ $alertValue }}
                                                            </label>''
                                                        </div>
                                                    @endforeach

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            {{-- Título da tarefa --}}
                            <div class="row mt-3">
                                <div class="">
                                    <label for="title" class="poppins-regular fs-6">Título </label>

                                    <input id="title" type="text"
                                        class="form-control fs-6 @error('title') is-invalid @enderror" name="title"
                                        value="{{ old('title', $task->title) }}">

                                    @error('title')
                                        <div class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @enderror
                                </div>

                            </div>

                            {{-- Descrição da tarefa --}}

                            @php
                                $description = $task->feedbacks()->first()->feedback;
                            @endphp

                            <div class="row mt-3">

                                <div class="">
                                    <label for="description" class="fs-6">Descrição</label>


                                    <textarea name="description" id="description" cols="30" rows="5"
                                        class="form-control roboto @error('description') is-invalid @enderror">{{ old('description', $description) }}</textarea>

                                    @error('description')
                                        <div class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @enderror
                                </div>

                            </div>

                            <div class="d-flex justify-content-between mt-4">

                                <div>

                                    <a class="btn btn-primary me-3" href="{{ route('home') }}">voltar</a>

                                    <label for="task_attachments" class="btn-custom btn btn-primary">
                                        Anexar imagens
                                        <span id="imageCountDisplay"></span>
                                    </label>

                                    <input id="task_attachments" name="task_attachments[]" type="file"
                                        accept="image/*" multiple class="d-none" />

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

                                </div>

                                <div class="d-flex justify-content-between">

                                    <a href="{{ route('task.edit', $task->id) }}" class="btn btn-primary me-2">

                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" width="24" height="24"
                                            class="size-2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                        </svg>

                                        <span class="ms-2">Redefinir valores</span>

                                    </a>

                                    <button type="submit" class="btn btn-secondary">Salvar</button>
                                </div>



                            </div>

                        </form>

                    </div>

                </div>

            </div>



        </div>

        {{-- Script baixo:Lida com a dinâmica do label de criação de lembrete --}}
        <script>
            const taskLabel = document.getElementById('task-label');

            const inputDate = document.getElementById('input-date');

            const weekDaysCheckBoxesInputsCollection = document.querySelectorAll('.check-box-input');

            const weekDaysCheckBoxesInputs = Array.from(weekDaysCheckBoxesInputsCollection);

            const currentDate = new Date();

            const formattedDate = currentDate.toISOString().slice(0, 10);


            const handleInputBasedOnCheckboxSelection = (affectedInput, checkBoxesInputs, valueToFill = '') => {

                const isAnyInputChecked = checkBoxesInputs.some(element => element.checked);

                const affectedInputIsEmpty = affectedInput.value.trim() == '';

                const affectedInputMustBeFilled = !isAnyInputChecked && affectedInputIsEmpty;

                affectedInputMustBeFilled ? affectedInput.value = valueToFill : affectedInput.value = '';

            }

            let weekDaysInString = [];

            const getDaysAfterUncheck = (weekdays, weekDay) => weekdays.filter(day => day !== weekDay)

            const handleWeekDaysInString = (weekDayString, status) =>
                status ?
                weekDaysInString.push(weekDayString) :

                weekDaysInString = getDaysAfterUncheck(weekDaysInString, weekDayString);

            const getOrderedWeekDays = unorderedWeekdays => {

                const sortedWeekDays = [
                    ' Domingo',
                    ' Segunda',
                    ' Terça',
                    ' Quarta',
                    ' Quinta',
                    ' Sexta',
                    ' Sábado'
                ];

                return unorderedWeekdays
                    .sort((a, b) => sortedWeekDays.indexOf(a) - sortedWeekDays.indexOf(b));
            }

            const setTaskLabelFromCheckboxes = weekDaysInString => {

                if (weekDaysInString.length === 7) {

                    return 'Todos os dias';

                } else {

                    return weekDaysInString.reduce((acc, day, index) => {

                        if (index === 0) {

                            return `A cada ${day}`;

                        } else if (index === weekDaysInString.length - 1) {

                            return `${acc} e ${day}`;

                        } else {

                            return `${acc}, ${day}`;
                        }

                    }, '');
                }
            }

            const checkIsToday = someDate => {

                const todayObject = new Date();

                const inputDateObject = new Date(someDate.value + "T00:00:00")

                const formatedToday = todayObject.getFullYear() +
                    '-' + (todayObject.getMonth() + 1) +
                    '-' + todayObject.getDate();

                const formatedInputDateValue = inputDateObject.getFullYear() +
                    '-' + (inputDateObject.getMonth() + 1) +
                    '-' + inputDateObject.getDate();

                return formatedToday == formatedInputDateValue

            }

            const setTaskLabelFromInputDate = () => {

                const selectedDate = new Date(inputDate.value + "T00:00:00");

                if (isNaN(selectedDate.getTime())) {

                    taskLabel.innerText = "Defina uma data válida para tarefa.";

                    return;
                }

                const formattedDate = selectedDate.toLocaleDateString('pt-BR', {
                    weekday: 'long',
                    day: 'numeric',
                    month: 'long'
                });

                const isTodayDate = checkIsToday(inputDate);

                const todayString = isTodayDate ? " Hoje, " : "";

                const labelContent = `${todayString}${formattedDate}`;

                taskLabel.innerText = labelContent;

            };

            //Abaixo: Manipula o label de aviso do lembrete em relação ao input de uma data específica.

            inputDate.addEventListener('change', () => {

                weekDaysCheckBoxesInputs.forEach(checkBox => {

                    checkBox.checked = false;

                    weekDaysInString = [];
                });

                setTaskLabelFromInputDate();

            });

            weekDaysCheckBoxesInputs.forEach((checkBox, index) => {

                checkBox.addEventListener('change', () => {

                    const isChecked = checkBox.checked;

                    const checkboxId = checkBox.id;

                    switch (checkboxId) {

                        case 'btn-check-outlined-sunday':

                            handleWeekDaysInString(' Domingo', isChecked)

                            break;

                        case 'btn-check-outlined-monday':

                            handleWeekDaysInString(' Segunda', isChecked)

                            break;

                        case 'btn-check-outlined-tuesday':

                            handleWeekDaysInString(' Terça', isChecked)

                            break;

                        case 'btn-check-outlined-wednesday':

                            handleWeekDaysInString(' Quarta', isChecked)

                            break;

                        case 'btn-check-outlined-thursday':
                            handleWeekDaysInString(' Quinta', isChecked)

                            break;

                        case 'btn-check-outlined-friday':
                            handleWeekDaysInString(' Sexta', isChecked)

                            break;

                        case 'btn-check-outlined-saturday':
                            handleWeekDaysInString(' Sábado', isChecked)
                            break;
                    };

                    const orderedWeekDays = getOrderedWeekDays(weekDaysInString);

                    taskLabel.innerText = setTaskLabelFromCheckboxes(orderedWeekDays);

                    handleInputBasedOnCheckboxSelection(inputDate, weekDaysCheckBoxesInputs,
                        formattedDate);

                    if (!weekDaysCheckBoxesInputs.some(cb => cb.checked)) {
                        setTaskLabelFromInputDate();
                    }

                });

            });

            setTaskLabelFromInputDate();

            //Abaixo manipula o registro de quantas opções foram selecionadas no accordion de horário de alerta.

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

    </div>
@endsection
