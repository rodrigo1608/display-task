<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Validator;

use App\Models\Task;

use Carbon\Carbon;

class UpdateTaskRequest extends FormRequest
{

    private $task;
    private $pastStartMessage;
    private $pasEndtMessage;

    public function __construct()
    {
        $this->task = Task::with('durations')->findOrFail($this->route('task'));
        $this->pastStartMessage = 'Ops! Esse horário já passou';

        $this->pasEndtMessage = 'Não é possível editar o tempo de expiração de uma tarefa que não será repetida para o passado, pois ela já está expirada.';
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */


    public function rules(): array
    {
        return [
            'start' => ['required', 'date_format:H:i'],
            'end' => ['required', 'date_format:H:i', 'after:start'],
            'time' => ['nullable', 'date_format:H:i', 'before:start'],
            'title' => 'min:3|max:50',
            'task-attachments' => 'image',
            'description' => 'required'
        ];
    }

    public function messages(): array
    {

        $defaultTimeFeedback = 'O valor deve estar no formato de horas e minutos.';

        return [
            'start.required' => 'Escolha o melhor horário para você começar a tarefa.',
            'start.date_format' =>  $defaultTimeFeedback,
            'end.required' => 'Defina o horário para encerrar a atividade.',
            'end.date_format' =>  $defaultTimeFeedback,
            'end.after' => 'O horario de término deve ser posterior a de início.',
            'time.date_format' => $defaultTimeFeedback,
            'time.before' => 'O horário da notificação deve ser anterior ao horário de início da tarefa.',
            'title.min' => 'Ops! Seu título precisa de pelo menos 3 caracteres para fazer sentido',
            'title.max' => 'Seu título está muito longo, guarde um pouco para mensagem de notificação',
            'description.required' => 'Descrição é importante para que todos os participantes entendam o propósito, as etapas e outros detalhes da tarefa.',

        ];
    }

    // public function checkTimeRequired($validator)
    // {

    //     $alertOptions = getAlertOptions();

    //     // Filtra as opções onde as chaves estão marcadas como 'true'
    //     $trueOptions = array_filter($alertOptions, function ($label, $key) {
    //         // Verifica se o valor da chave na requisição é 'true'
    //         return $this->input($key) === 'true';
    //     }, ARRAY_FILTER_USE_BOTH);

    //     if ((!$this->filled('time')) && empty($trueOptions)) {

    //         $validator->errors()->add('time', 'Para não dar branco, crie um lembrete!');
    //     }
    // }

    public function checkAlertTimesSufficiency($validator)
    {
        if (!filled($this->input('start'))) {
            return;
        }

        $start = Carbon::createFromFormat('H:i', $this->input('start'));

        $specificDate = $this->filled('specific_date')
            ? getCarbonDate($this->input('specific_date'))
            : null;

        $alertOptions = [
            'half_an_hour_before' => 30,
            'one_hour_before' => 60,
            'two_hours_before' => 120,
        ];

        foreach ($alertOptions as $alertIndex => $minutes) {

            if (isset($specificDate) && $this->input($alertIndex) === 'true') {

                // $timeDifference = $start->diffInMinutes(now()->addMinutes($minutes), false);

                $timeDifference = now()->diffInMinutes($start, false);

                $hours = floor($minutes / 60);
                $minutesRemaining = $minutes % 60;

                // Create message with hours and minutes
                $timeText = $hours > 0 ? $hours . ' hora' . ($hours > 1 ? 's' : '') : '';
                $timeText .= $minutesRemaining > 0 ? ($timeText ? ' e ' : '') . $minutesRemaining . ' minuto' . ($minutesRemaining > 1 ? 's' : '') : '';

                if ($timeDifference < $minutes) {
                    $validator->errors()->add($alertIndex, 'O horário de notificação selecionado (' . $timeText . ' antes), pois não há tempo suficiente antes do início da tarefa.');
                }
            }
        }

        $isOneDayBeforeSelected = isset($specificDate) && $this->input('one_day_earlier') === 'true';

        if ($isOneDayBeforeSelected) {

            $dateTimeString  = $specificDate->format('m/d/Y') . ' ' . $start->format('H:i');

            $dateTime = getCarbonDate($dateTimeString);

            $timeDifferenceInDays = now()->diffInDays($dateTime);

            if ($timeDifferenceInDays < 1) {
                $validator->errors()->add('one_day_earlier', 'A notificação de um dia antes não é válida, pois não há tempo suficiente entre a data da notificação e início da tarefa.');
            }
        }
    }

    public function checkStartTimeNotInPast($validator)
    {

        $hasStartTimeChanged = $this->input('start') !==  substr(getDuration($this->task)->start, 0, 5);

        if (!filled($this->input('start'))) {
            return;
        }

        $specificDate = $this->filled('specific_date')
            ? getCarbonDate($this->input('specific_date'))
            : null;

        $isSpecificDateAndToday = isset($specificDate) && checkIsToday($specificDate);

        if ($isSpecificDateAndToday && $hasStartTimeChanged) {

            $start = Carbon::createFromFormat('H:i', $this->input('start'));

            if ($start->isPast()) {
                $validator->errors()->add('start', $this->pastStartMessage);
            }
        }
    }

    public function checkEndTimeInPast($validator)
    {
        if (!filled($this->input('start'))) {
            return;
        }


        getDuration($this->task)->status;

        $specificDate = $this->filled('specific_date')
            ? getCarbonDate($this->input('specific_date'))
            : null;

        $isSpecificDateAndToday = isset($specificDate) && checkIsToday($specificDate);

        $isTaskTimeExpired = getDuration($this->task)->status === 'finished';


        $hasEndTimeChanged = $this->input('end') !==  substr(getDuration($this->task)->end, 0, 5);

        if (($isSpecificDateAndToday && $isTaskTimeExpired) && $hasEndTimeChanged) {

            $end = Carbon::createFromFormat('H:i', $this->input('end'));

            if ($end->isPast()) {
                $validator->errors()->add('end', $this->pasEndtMessage);
            }
        }
    }

    public function checkNotificationTimeNotInPast($validator)
    {
        if (!filled($this->input('time'))) {
            return;
        }

        $specificDate = $this->filled('specific_date')
            ? getCarbonDate($this->input('specific_date'))
            : null;

        if (isset($specificDate) && checkIsToday($specificDate)) {

            $time = Carbon::createFromFormat('H:i', $this->input('time'));

            if ($time->isPast()) {
                $validator->errors()->add('time', 'Ops! Esse horário já passou.');
            }
        }
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {

            $this->checkAlertTimesSufficiency($validator);

            $this->checkStartTimeNotInPast($validator);

            $this->checkEndTimeInPast($validator);

            $this->checkNotificationTimeNotInPast($validator);

            // $this->checkTimeRequired($validator);
        });
    }
}
