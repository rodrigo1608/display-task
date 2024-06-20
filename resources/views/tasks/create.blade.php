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

                                <div class="row mt-2">

                                    <div class="col-9 d-flex">

                                        <p id="task-label" class="poppins-semibold fs-5">

                                        </p>
                                    </div>

                                    <div class="col-3 d-flex justify-content-end">

                                        <input type="date" id="input-date" name="specific_date" class="form-control fs-6"
                                            value="{{ Carbon\Carbon::now()->format('Y-m-d') }}"
                                            min="{{ Carbon\Carbon::now()->format('Y-m-d') }}">
                                        {{--  --}}
                                    </div>

                                </div>

                                {{-- Check boxes dos dias da semana  --}}
                                <div class="weekdays-selects d-flex justify-content-between mt-3 flex-wrap">

                                    <div class="">

                                        <input type="checkbox" name="sunday" value="true"
                                            class="btn-check check-box-input" id="btn-check-outlined-sunday"
                                            autocomplete="off">

                                        <label
                                            class="week-day btn btn-outline-dark poppins-medium rounded-pill d-flex me-3 mt-4 border-2"
                                            for="btn-check-outlined-sunday">Domingo
                                        </label>

                                    </div>

                                    <div class="">

                                        <input type="checkbox" name="monday" value="true"
                                            class="btn-check check-box-input" id="btn-check-outlined-monday"
                                            autocomplete="off">

                                        <label
                                            class="week-day btn btn-outline-dark poppins-medium rounded-pill d-flex justify-content-center align-items-center mt-4 border-2"
                                            for="btn-check-outlined-monday">Segunda
                                        </label>

                                    </div>

                                    <div class="">
                                        <input type="checkbox" name="tuesday" value="true"
                                            class="btn-check check-box-input" id="btn-check-outlined-tuesday"
                                            autocomplete="off">

                                        <label
                                            class="week-day btn btn-outline-dark poppins-medium rounded-pill d-flex justify-content-center align-items-center mt-4 border-2"
                                            for="btn-check-outlined-tuesday">Terça
                                        </label>

                                    </div>

                                    <div class="">
                                        <input type="checkbox" name="wednesday" value="true"
                                            class="btn-check check-box-input" id="btn-check-outlined-wednesday"
                                            autocomplete="off">

                                        <label
                                            class="week-day btn btn-outline-dark poppins-medium rounded-pill d-flex justify-content-center align-items-center mt-4 border-2"
                                            for="btn-check-outlined-wednesday">Quarta
                                        </label>

                                    </div>

                                    <div class="">

                                        <input type="checkbox" name="thursday" value="true"
                                            class="btn-check check-box-input" id="btn-check-outlined-thursday"
                                            autocomplete="off">

                                        <label
                                            class="week-day btn btn-outline-dark poppins-medium rounded-pill d-flex justify-content-center align-items-center mt-4 border-2"
                                            for="btn-check-outlined-thursday">Quinta
                                        </label>

                                    </div>

                                    <div class="">

                                        <input type="checkbox" name="friday" value="true"
                                            class="btn-check check-box-input" id="btn-check-outlined-friday"
                                            autocomplete="off">

                                        <label
                                            class="week-day btn btn-outline-dark poppins-medium rounded-pill d-flex justify-content-center align-items-center mt-4 border-2"
                                            for="btn-check-outlined-friday">Sexta
                                        </label>

                                    </div>

                                    <div class="">

                                        <input type="checkbox" name="saturday" value="true"
                                            class="btn-check check-box-input" id="btn-check-outlined-saturday"
                                            autocomplete="off">

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
                                        <div class="accordion" id="accordionPanelsStayOpenExample">

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
                                                                    id="alert{{ $alertIndex }}CheckDefault">

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

                                <div class="d-flex justify-content-between mt-4">

                                    <div class>

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

                                        <div>
                                            @if ($participants->isNotEmpty())
                                                <button type="button" class="btn btn-primary me-3"
                                                    data-bs-toggle="modal" data-bs-target="#participantsModal">
                                                    Adicionar participantes
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

                                                                        <input class="form-check-input" type="checkbox"
                                                                            value=" {{ $participant->email }}"
                                                                            name="participant{{ $index }}"
                                                                            id="participant{{ $index }}CheckDefault">

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

                    const inputDateObject = new Date(someDate.value)

                    const formatedToday = todayObject.getFullYear() +
                        '-' + (todayObject.getMonth() + 1) +
                        '-' + todayObject.getDate();

                    const formatedInputDateValue = inputDateObject.getFullYear() +
                        '-' + (inputDateObject.getMonth() + 1) +
                        '-' + inputDateObject.getDate();

                    return formatedToday == formatedInputDateValue

                }

                const setTaskLabelFromInputDate = () => {

                    const selectedDate = new Date(inputDate.value);

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

                //Abaixo manipula o registro de quantas opções foram selecionadas no accordion de opções foram selecionadas

                const alertOptionsCounterLabel = document.querySelector('.alertOptionsCounter');

                const customAlertTime = document.querySelector('#custom-alert-time');

                const alertOptionsCollection = document.querySelectorAll('.alertOption');


                const alertOptions = Array.from(alertOptionsCollection);

                alertOptions.forEach(optionAlert => {

                    optionAlert.addEventListener('click', () => {

                        const checkedOptions = alertOptions.filter(option => option.checked)

                        const checkedRegister = checkedOptions.length;

                        checkedRegister > 0 ? alertOptionsCounterLabel.innerText = ('(' +
                                checkedRegister + ')') :
                            alertOptionsCounterLabel.innerText = "";

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
