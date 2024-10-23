    @extends('layouts.app')

    @section('content')
        <div class="fs-5 container">

            <div class="row justify-content-center">

                <div class="col-md-8">

                    <div class="card mt-5">

                        <div class="card-header">

                            <h5 class="fs-2 poppins-regular" id="createTaskLabel">Criar
                                tarefa
                            </h5>

                        </div>

                        <div class="card-body my-5 px-5">

                            <form method="POST" action="{{ route('task.store') }}" enctype="multipart/form-data">
                                @csrf

                                <input type="hidden" name="userID" value="{{$userID}}">

                                <div class="row mt-2">

                                    <div class="col-9 d-flex">

                                        <p id="task-label" class="poppins-semibold fs-5">

                                        </p>
                                    </div>

                                    <div class="col-3 d-flex justify-content-end">

                                        <input type="date" id="input-date" name="specific_date" class="form-control fs-6"
                                            value="{{ old('specific_date', Carbon\Carbon::now()->format('Y-m-d')) }}"
                                            min="{{ Carbon\Carbon::now()->format('Y-m-d') }}">
                                    </div>

                                </div>

                                {{-- Check boxes dos dias da semana  --}}
                                <div class="weekdays-selects d-flex justify-content-between mt-3 flex-wrap">

                                    <div class="">

                                        <input type="checkbox" name="sunday" value="true"
                                            class="btn-check check-box-input" id="btn-check-outlined-sunday"
                                            {{ old('sunday') == 'true' ? 'checked' : '' }}>

                                        <label
                                            class="week-day btn btn-outline-dark poppins-medium rounded-pill d-flex me-3 mt-4 border-2"
                                            for="btn-check-outlined-sunday">Domingo
                                        </label>

                                    </div>

                                    <div class="">

                                        <input type="checkbox" name="monday" value="true"
                                            class="btn-check check-box-input" id="btn-check-outlined-monday"
                                            {{ old('monday') == 'true' ? 'checked' : '' }}>

                                        <label
                                            class="week-day btn btn-outline-dark poppins-medium rounded-pill d-flex justify-content-center align-items-center mt-4 border-2"
                                            for="btn-check-outlined-monday">Segunda
                                        </label>

                                    </div>

                                    <div class="">
                                        <input type="checkbox" name="tuesday" value="true"
                                            class="btn-check check-box-input" id="btn-check-outlined-tuesday"
                                            {{ old('tuesday') == 'true' ? 'checked' : '' }}>

                                        <label
                                            class="week-day btn btn-outline-dark poppins-medium rounded-pill d-flex justify-content-center align-items-center mt-4 border-2"
                                            for="btn-check-outlined-tuesday">Terça
                                        </label>

                                    </div>

                                    <div class="">
                                        <input type="checkbox" name="wednesday" value="true"
                                            class="btn-check check-box-input" id="btn-check-outlined-wednesday"
                                            {{ old('wednesday') == 'true' ? 'checked' : '' }}>

                                        <label
                                            class="week-day btn btn-outline-dark poppins-medium rounded-pill d-flex justify-content-center align-items-center mt-4 border-2"
                                            for="btn-check-outlined-wednesday">Quarta
                                        </label>

                                    </div>

                                    <div class="">

                                        <input type="checkbox" name="thursday" value="true"
                                            class="btn-check check-box-input" id="btn-check-outlined-thursday"
                                            {{ old('thursday') == 'true' ? 'checked' : '' }}>

                                        <label
                                            class="week-day btn btn-outline-dark poppins-medium rounded-pill d-flex justify-content-center align-items-center mt-4 border-2"
                                            for="btn-check-outlined-thursday">Quinta
                                        </label>

                                    </div>

                                    <div class="">

                                        <input type="checkbox" name="friday" value="true"
                                            class="btn-check check-box-input" id="btn-check-outlined-friday"
                                            {{ old('friday') == 'true' ? 'checked' : '' }}>

                                        <label
                                            class="week-day btn btn-outline-dark poppins-medium rounded-pill d-flex justify-content-center align-items-center mt-4 border-2"
                                            for="btn-check-outlined-friday">Sexta
                                        </label>

                                    </div>

                                    <div class="">

                                        <input type="checkbox" name="saturday" value="true"
                                            class="btn-check check-box-input" id="btn-check-outlined-saturday"
                                            {{ old('saturday') == 'true' ? 'checked' : '' }}>

                                        <label
                                            class="week-day btn btn-outline-dark poppins-medium rounded-pill d-flex justify-content-center align-items-center mt-4 border-2"
                                            for="btn-check-outlined-saturday">Sabado
                                        </label>

                                    </div>

                                </div>

                                <div class="row d-flex jutify-content-between mt-5">

                                    <div class="col-md-3">

                                        <label for="start" class="poppins-regular fs-6 me-3">iniciar em:</label>

                                        <input id="start" type="time" name="start"
                                            class="form-control fs-6 @error('start') is-invalid @enderror text-center"
                                            name="start" value={{ old('start') }}>

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
                                            name="end" value={{ old('end') }}>

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
                                            name="local" value="{{ old('local') }}">

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

                                        <div class="accordion accordion-flush" id="conflicting-accordion">

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
                                                    data-bs-parent="#conflicting-accordion">

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
                                                            </span>
                                                        </p>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="row d-flex align-items-start mt-5">

                                    <div class="col-md-5">

                                        <label for="time" class="poppins-regular fs-6 m-0">Horário da
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

                                    <span class="col-md-1 mt-4 text-center">
                                        ou
                                    </span>

                                    <div class="col-md-6 mt-3">

                                        <div class="accordion" id="pre-defined schedule-accordion">

                                            <div class="accordion-item">

                                                <h2 class="accordion-header">

                                                    <button class="accordion-button poppins-regular" type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="false"
                                                        aria-controls="panelsStayOpen-collapseOne">
                                                        Horário pré-definido <span
                                                            class="alertOptionsCounter fs-6 ms-2"></span>
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

                                            <div class="mt-2">
                                                @php
                                                    $firstError = null;

                                                    $alertOptions = [
                                                        'half_an_hour_before',
                                                        'one_hour_before',
                                                        'two_hours_before',
                                                        'one_day_earlier',
                                                    ];
                                                @endphp

                                                @foreach ($alertOptions as $alertIndex)
                                                    @if ($errors->has($alertIndex))
                                                        @php
                                                            $firstError = $errors->first($alertIndex);
                                                        @endphp
                                                    @break
                                                @endif
                                            @endforeach

                                            @if ($firstError)
                                                <div class="invalid-feedback d-block">
                                                    <strong>{{ $firstError }}</strong>
                                                </div>
                                            @endif

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
                                        value="{{ old('title') }}">

                                    @error('title')
                                        <div class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @enderror
                                </div>

                            </div>

                            {{-- Descrição da tarefa --}}
                            <div class="row mt-3">

                                <div class="">

                                    <label for="description" class="fs-6">Descrição</label>
                                    <textarea name="description" id="description" cols="30" rows="5"
                                        class="form-control roboto @error('description') is-invalid @enderror">{{ old('description') }}</textarea>

                                    @error('description')
                                        <div class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @enderror
                                </div>

                            </div>

                            {{-- Container dos botões de baixo --}}

                            <div class="d-flex justify-content-between align-items-center mt-4">

                                <div class>

                                    {{-- botão de voltar --}}
                                    <a class="btn btn-primary me-3 py-2" href="{{ route('home') }}"  aria-label="Voltar para a pagina inicial">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                                            stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                                        </svg>

                                    </a>

                                    {{-- botão de anexar imagens --}}
                                    <label for="task_attachments" class="btn-custom btn btn-primary">

                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13" />
                                        </svg>

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

                                     {{--Grupo dos botões de visibilidade  --}}

                                        <div class="d-flex ">

                                            <div class="form-check start py-1 px-3 p-0"

                                            style="  border-top-left-radius: 50px;
                                                    border-bottom-left-radius: 50px;"
                                                >
                                                <input class="form-check-input d-none" type="radio" name="visibility" id="flexRadioDefault1" data-visibility  value="public" checked>
                                                <label class="form-check-label fs-6 m-0 p-0" for="flexRadioDefault1">Público</label>
                                            </div>

                                            <div class="form-check  py-1 px-3"
                                            style="  border-top-right-radius: 50px;
                                                    border-bottom-right-radius: 50px;">
                                                <input class="form-check-input d-none" type="radio" name="visibility" id="flexRadioDefault2" value="private"  data-visibility>
                                                <label class="form-check-label fs-6 m-0 p-0" for="flexRadioDefault2">Privado</label>
                                            </div>
                                        </div>

                                     <script>

                                        document.addEventListener('DOMContentLoaded',function(){

                                            const visibilityRadios = document.querySelectorAll('[data-visibility]');

                                            const updateCheckedRadioStyle = () => {

                                                visibilityRadios.forEach(radio => {

                                                    const radioParent  = radio.closest('.form-check');

                                                    if(radio.checked){

                                                        radioParent.classList.remove("btn-primary");
                                                        radioParent.classList.add("btn-secondary");

                                                    }else{
                                                        radioParent.classList.remove("btn-secondary");
                                                        radioParent.classList.add("btn-primary");
                                                    }

                                                });

                                            }

                                            updateCheckedRadioStyle();

                                            visibilityRadios.forEach(radio => {

                                                radio.addEventListener('change',updateCheckedRadioStyle);

                                            });

                                        });


                                     </script>

                                <div class="d-flex justify-content-between">

                                    <div>
                                        @if ($participants->isNotEmpty())

                                            <button id="participants-button" type="button"
                                                class="btn btn-primary me-3" data-bs-toggle="modal"
                                                data-bs-target="#participantsModal">
                                                Adicionar participantes
                                                <span id="participantCounterDisplay"></span>
                                            </button>

                                        @endif

                                        <!-- Modal -->
                                        <div class="modal fade" id="participantsModal" tabindex="-1"
                                            aria-labelledby="exampleModalLabel" aria-hidden="true">

                                            <div class="modal-dialog">

                                                <div class="modal-content">

                                                    <div class="modal-header">

                                                        <h1 class="modal-title fs-5 poppins-semibold"
                                                            id="exampleModalLabel">Participantes
                                                        </h1>

                                                    </div>

                                                    <div class="modal-body">

                                                        @foreach ($participants as $index => $participant)
                                                            <div class="list-group">

                                                                <div class="form-check">

                                                                    <input
                                                                        class="form-check-input participant-checkbox"
                                                                        type="checkbox"
                                                                        value=" {{ $participant->email }}"
                                                                        name="participant{{ $index }}"
                                                                        id="participant{{ $index }}CheckDefault"
                                                                        {{ old('participant' . $index) == $participant->email ? 'checked' : '' }}>

                                                                    <label class="form-check-label"
                                                                        for="participant{{ $index }}CheckDefault">
                                                                        {{ $participant->email }}
                                                                    </label>
                                                                </div>

                                                            </div>
                                                        @endforeach
                                                    </div>

                                                    <div class="modal-footer">

                                                        <button type="button" data-bs-dismiss="modal"
                                                            class="btn btn-primary">Adicionar</button>
                                                    </div>

                                                </div>

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

                                                    participantCheckboxes.forEach(participantCheckbox => participantCheckbox.addEventListener('change', () =>
                                                        updateParticipantCounter()));

                                                    updateParticipantCounter();
                                                </script>

                                            </div>

                                        </div>

                                    </div>

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
                    '-' + (inputDateObject.getDate());

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

            //Primeiramente um reset dos checkboxes, caso o input de data específica haver uma alteração
            inputDate.addEventListener('change', () => {

                weekDaysCheckBoxesInputs.forEach(checkBox => {

                    checkBox.checked = false;

                    weekDaysInString = [];
                });

                setTaskLabelFromInputDate();

            });

            //Caso algum checkbox seja clicado, uma string será construído
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
