<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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

        $defaultTimeData = ['required', 'date_format:H:i'];

        return [
            'start_time' => $defaultTimeData,
            'end_time' => $defaultTimeData,
            'title' => 'min:3|max:50',
            'task-attachments' => 'image',
            'description' => 'required'
        ];
    }

    public function messages(): array
    {

        $defaultTimeFeedback = 'O valor deve estar no formato de horas e minutos.';

        return [
            'start_time.required' => 'Escolha o melhor horário para você começar a tarefa.',
            'start_time.date_format' =>  $defaultTimeFeedback,
            'end_time.required' => 'Defina o horário para encerrar a atividade.',
            'end_time.date_format' =>  $defaultTimeFeedback,
            'title.min' => 'Ops! Seu título precisa de pelo menos 3 caracteres para fazer sentido',
            'title.max' => 'Seu título está muito longo, guarde um pouco para mensagem de notificação',
            'description.required' => 'Descrição é importante para que todos os participantes entendam o propósito, as etapas e outros detalhes da tarefa.',

        ];
    }
}
