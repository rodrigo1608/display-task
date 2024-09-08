@extends('layouts.app')

@section('content')
    <div class="fs-5 container">

        <div class="row justify-content-center">

            <div class="col-md-8">

                <div class="card mt-5">

                    <div class="card-header">

                        <h5 class="fs-2 poppins-regular" id="createReminderkLabel">Criar
                            lembrete
                        </h5>

                    </div>

                    <div class="card-body my-5 px-5">

                        <form method="POST" action="{{ route('reminder.store') }}">
                            @csrf

                            <div class="row mt-2">

                                <div class="col-9 d-flex">

                                    <p id="reminder-label" class="poppins-semibold fs-5">

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

                                    <input type="checkbox" name="sunday" value="true" class="btn-check check-box-input"
                                        id="btn-check-outlined-sunday" autocomplete="off"
                                        {{ old('sunday') == 'true' ? 'checked' : '' }}>

                                    <label
                                        class="week-day btn btn-outline-dark poppins-medium rounded-pill d-flex me-3 mt-4 border-2"
                                        for="btn-check-outlined-sunday">Domingo
                                    </label>

                                </div>

                                <div class="">

                                    <input type="checkbox" name="monday" value="true" class="btn-check check-box-input"
                                        id="btn-check-outlined-monday" autocomplete="off"
                                        {{ old('monday') == 'true' ? 'checked' : '' }}>

                                    <label
                                        class="week-day btn btn-outline-dark poppins-medium rounded-pill d-flex justify-content-center align-items-center mt-4 border-2"
                                        for="btn-check-outlined-monday">Segunda
                                    </label>

                                </div>

                                <div class="">
                                    <input type="checkbox" name="tuesday" value="true" class="btn-check check-box-input"
                                        id="btn-check-outlined-tuesday" autocomplete="off"
                                        {{ old('tuesday') == 'true' ? 'checked' : '' }}>

                                    <label
                                        class="week-day btn btn-outline-dark poppins-medium rounded-pill d-flex justify-content-center align-items-center mt-4 border-2"
                                        for="btn-check-outlined-tuesday">Terça
                                    </label>

                                </div>

                                <div class="">
                                    <input type="checkbox" name="wednesday" value="true" class="btn-check check-box-input"
                                        id="btn-check-outlined-wednesday" autocomplete="off"
                                        {{ old('wednesday') == 'true' ? 'checked' : '' }}>

                                    <label
                                        class="week-day btn btn-outline-dark poppins-medium rounded-pill d-flex justify-content-center align-items-center mt-4 border-2"
                                        for="btn-check-outlined-wednesday">Quarta
                                    </label>

                                </div>

                                <div class="">

                                    <input type="checkbox" name="thursday" value="true" class="btn-check check-box-input"
                                        id="btn-check-outlined-thursday" autocomplete="off"
                                        {{ old('thursday') == 'true' ? 'checked' : '' }}>

                                    <label
                                        class="week-day btn btn-outline-dark poppins-medium rounded-pill d-flex justify-content-center align-items-center mt-4 border-2"
                                        for="btn-check-outlined-thursday">Quinta
                                    </label>

                                </div>

                                <div class="">

                                    <input type="checkbox" name="friday" value="true" class="btn-check check-box-input"
                                        id="btn-check-outlined-friday" autocomplete="off"
                                        {{ old('friday') == 'true' ? 'checked' : '' }}>

                                    <label
                                        class="week-day btn btn-outline-dark poppins-medium rounded-pill d-flex justify-content-center align-items-center mt-4 border-2"
                                        for="btn-check-outlined-friday">Sexta
                                    </label>

                                </div>

                                <div class="">

                                    <input type="checkbox" name="saturday" value="true" class="btn-check check-box-input"
                                        id="btn-check-outlined-saturday" autocomplete="off"
                                        {{ old('saturday') == 'true' ? 'checked' : '' }}>

                                    <label
                                        class="week-day btn btn-outline-dark poppins-medium rounded-pill d-flex justify-content-center align-items-center mt-4 border-2"
                                        for="btn-check-outlined-saturday">Sabado
                                    </label>

                                </div>

                            </div>

                            <div class="row mt-4">

                                <div class="col-md-7">

                                    <label for="time" class="poppins-regular fs-6 me-3">Horário do
                                        lembrete</label>

                                    <input id="time" type="time" name="time"
                                        class="form-control w-50 fs-6 @error('time') is-invalid @enderror text-center"
                                        name="time" value="{{ old('time', Carbon\Carbon::now()->format('Y-m-d')) }}">
                                    @error('time')
                                        <div class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @enderror
                                </div>
                            </div>

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

                            <div class="row mt-3">

                                <div class="">
                                    <label for="title" class="fs-6">Mensagem de notificação</label>
                                    <textarea name="notification_message" id="" cols="30" rows="5" class="form-control roboto">{{ old('notification_message') }}</textarea>
                                </div>

                            </div>

                            <div class="d-flex justify-content-between mt-3">
                                {{-- botão de voltar --}}
                                <a class="btn btn-primary r me-3 py-2" href="{{ route('home') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                                    </svg>

                                </a>

                                <button type="submit" class="btn btn-secondary">Salvar lembrete</button>
                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

        {{-- Script baixo:Lida com a dinâmica do label de cração de lembrete --}}
        <script>
            const reminderLabel = document.getElementById('reminder-label');

            const inputDate = document.getElementById('input-date');

            const checkBoxesInputsCollection = document.querySelectorAll('.check-box-input');

            const checkBoxesInputs = Array.from(checkBoxesInputsCollection);

            const currentDate = new Date();

            const formattedDate = currentDate.toISOString().slice(0, 10);

            const fillInputDate = () => {

                const isAnyInputChecked = checkBoxesInputs.some(element => element.checked);

                const inputDateIsEmpty = inputDate.value.trim() == '';

                const inputDayMustBeFilled = !isAnyInputChecked && inputDateIsEmpty;

                inputDayMustBeFilled ? inputDate.value = formattedDate : inputDate.value = '';

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

            const setReminderLabelFromCheckboxes = weekDaysInString => {

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
            const setReminderLabelFromInputDate = () => {

                const selectedDate = new Date(inputDate.value + "T00:00:00");

                if (isNaN(selectedDate.getTime())) {
                    reminderLabel.innerText = "Insira uma data válida";
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

                reminderLabel.innerText = labelContent;

            };
            checkBoxesInputs.forEach((checkBox, index) => {

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

                    reminderLabel.innerText = setReminderLabelFromCheckboxes(orderedWeekDays);

                    fillInputDate()

                });

            });

            const checkIsToday = (someDate) => {

                const today = new Date();

                const inputDateObject = new Date(someDate.value + "T00:00:00")

                const date = today.getFullYear() +
                    '-' + (today.getMonth() + 1) +
                    '-' + today.getDate();

                const formatedInputDateValue = inputDateObject.getFullYear() +
                    '-' + (inputDateObject.getMonth() + 1) +
                    '-' + inputDateObject.getDate();

                return date == formatedInputDateValue

            }

            //Abaixo: Manipula o label de aviso do lembrete em relação ao input de uma data específica.

            inputDate.addEventListener('change', function() {

                checkBoxesInputs.forEach(checkBox => {

                    checkBox.checked = false;

                    weekDaysInString = [];
                });

                setReminderLabelFromInputDate();

            });

            setReminderLabelFromInputDate();
        </script>

    </div>
@endsection
