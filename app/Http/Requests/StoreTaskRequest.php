<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Validator;

use Carbon\Carbon;

class StoreTaskRequest extends FormRequest
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

        // $defaultTimeData = ['required', 'date_format:H:i'];

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

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {

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

                if ($this->input($alertIndex) === 'true') {

                    $timeDifference = $start->diffInMinutes(now()->addMinutes($minutes), false);


                    $hours = floor($minutes / 60);
                    $minutesRemaining = $minutes % 60;

                    // Create message with hours and minutes
                    $timeText = $hours > 0 ? $hours . ' hora' . ($hours > 1 ? 's' : '') : '';
                    $timeText .= $minutesRemaining > 0 ? ($timeText ? ' e ' : '') . $minutesRemaining . ' minuto' . ($minutesRemaining > 1 ? 's' : '') : '';

                    // dd($timeDifference);

                    if ($timeDifference < $minutes) {
                        $validator->errors()->add($alertIndex, 'O horário de notificação selecionado (' . $timeText . ' antes), pois não há tempo suficiente antes do início da tarefa.');
                    }
                }
            }

            $isOneDayBeforeSelected = isset($specificDate) && $this->input('one_day_earlier') === 'true';

            if ($isOneDayBeforeSelected) {

                $timeDifferenceInDays = $specificDate->diffInDays(now(), false);

                // dd($timeDifferenceInDays);

                if ($timeDifferenceInDays) {
                    $validator->errors()->add('one_day_earlier', 'A notificação de um dia antes não é válida, pois não há tempo suficiente entre a data da notificação e início da tarefa.');
                }
            }
        });
    }
}
