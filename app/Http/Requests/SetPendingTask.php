<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
}
