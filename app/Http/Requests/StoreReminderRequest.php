<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Validator;

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

    public function checkNotificationTimeNotInPast($validator)
    {
        if (!filled($this->input('time'))) {
            return;
        }

        $specificDate = $this->filled('specific_date')
            ? getCarbonDate($this->input('specific_date'))
            : null;

        if (isset($specificDate) && checkIsToday($specificDate)) {

            $time = getCarbontime($this->input('time'));

            if ($time->isPast()) {
                $validator->errors()->add('time', 'Ops! Esse horário já passou.');
            }
        }
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {

            $this->checkNotificationTimeNotInPast($validator);
        });
    }
}
