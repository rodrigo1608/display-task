<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetPendingTaskDuration extends FormRequest
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
        $defaultTimeData = ['required', 'date_format:H:i'];

        return [
            'start' => $defaultTimeData,
            'end' => $defaultTimeData,
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
        ];
    }
}
