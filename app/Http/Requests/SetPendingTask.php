<?php

namespace App\Http\Requests;


use App\Models\Task;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Validator;

class SetPendingTask extends FormRequest
{
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
            'time' => ['nullable', 'date_format:H:i', 'before:start'],
        ];
    }

    public function messages(): array
    {

        return [
            'time.date_format' => 'O valor deve estar no formato de horas e minutos.',
            'time.before' => 'O horário da notificação deve ser anterior ao horário de início da tarefa.',
        ];
    }

    public function checkTimeRequired($validator)
    {
        $taskID = $this->route('task');

        $task = Task::find($taskID);
        $alertOptions = getAlertOptions();
        $duration = getDuration($task);
        $now = getCarbonNow();
        $start = getCarbonTime($duration->start);

        $willNotStartSoon = $now->diffInMinutes($start) > 30;

        // Filtra as opções onde as chaves estão marcadas como 'true'
        $trueOptions = array_filter($alertOptions, function ($label, $key) {

            // Verifica se o valor da chave na requisição é 'true'
            return $this->input($key) === 'true';
        }, ARRAY_FILTER_USE_BOTH);

        if ((!$this->filled('time')) && empty($trueOptions) && $willNotStartSoon) {

            $validator->errors()->add('time', 'Para não dar branco, crie um lembrete!');
        }
    }

    public function checkAlertTimesSufficiency($validator)
    {
        if (!filled($this->input('start'))) {
            return;
        }

        $start = getCarbonTime($this->input('start'));

        $taskID = $this->route('task');

        $task = Task::find($taskID);

        $specificDateString = $task->reminder->recurring?->specific_date;

        $specificDate = filled($specificDateString)
            ? getCarbonDate($specificDateString)
            : null;

        $alertOptions = [
            'half_an_hour_before' => 30,
            'one_hour_before' => 60,
            'two_hours_before' => 120,
        ];

        foreach ($alertOptions as $alertIndex => $minutes) {

            if (isset($specificDate) && $this->input($alertIndex) === 'true') {

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

    public function checkNotificationTimeNotInPast($validator)
    {

        if (!filled($this->input('time'))) {
            return;
        }

        $taskID = $this->route('task');

        $task = Task::find($taskID);

        $specificDate = filled($task->reminder->recurring->specific_date)
            ? getCarbonDate($task->reminder->recurring->specific_date)
            : null;

        if (filled($specificDate) && checkIsToday($specificDate)) {

            $time = getCarbonTime($this->input('time'));

            if ($time->isPast()) {

                $validator->errors()->add('time', 'Ops! Esse horário já passou.');
            }
        }
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {

            $this->checkAlertTimesSufficiency($validator);

            $this->checkNotificationTimeNotInPast($validator);

            $this->checkTimeRequired($validator);
        });
    }
}
