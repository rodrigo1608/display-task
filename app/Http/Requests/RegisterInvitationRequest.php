<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;


class RegisterInvitationRequest extends FormRequest

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
            'email'=> 'required|email'
        ];
    }

    public function checkIsRegistered($validator){

        $isInvitationRegistered = User::where('email', $this->input('email'))->exists();

       if( $isInvitationRegistered){
        $validator->errors()->add('email', 'O e-mail informado já está registrado em nossa base de dados.');
       }

    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {

            $this->checkIsRegistered($validator);

        });
    }
}
