<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReminderRequest extends FormRequest
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
            'time' => 'required',
            'title' => 'min:3|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'time.required' => 'Qual é o horário ideal para receber seus avisos?',
            'title.min' => 'Ops! Seu título precisa de pelo menos 3 caracteres para fazer sentido',
            'title.max' => 'Seu título está muito longo, guarde um pouco para mensagem de notificação',
        ];
    }
}
