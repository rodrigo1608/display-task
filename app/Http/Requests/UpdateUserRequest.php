<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class UpdateUserRequest extends FormRequest
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
        $genericStringRules =  ['required', 'min:2', 'max:30'];

        return [
            'name' => $genericStringRules,
            'lastname' => $genericStringRules,
            'role' => 'nullable|min:2|max:30',
            'email' => 'required|email',
            'telephone' => 'required|numeric|digits:11',
            'color' => 'required',
            'profile_picture' => 'nullable|image',
        ];
    }

    public function messages(): array
    {

        return [
            'name.required' => 'Não esqueça de inserir seu nome para criar o perfil',
            'name.min' => 'Seu nome precisa ter pelo menos 2 caracteres.',
            'name.max' => 'Seu nome não pode ter mais que 30 caracteres.',
            'lastname.required' => 'O campo sobrenome é obrigatório.',
            'lastname.min' => 'Seu sobrenome precisa ter pelo menos 2 caracteres.',
            'lastname.max' => 'O sobrenome é importante para facilitar uma identificação.',
            'email.required' => 'O campo de e-mail é obrigatório.',
            'email.email' => 'Por favor, insira um endereço de e-mail válido.',
            'email.unique' => 'Este endereço de e-mail já está em uso.',
            'telephone.required' => 'O campo telefone é obrigatório.',
            'telephone.numeric' => 'O telefone deve conter apenas números.',
            'telephone.size' => 'O telefone deve ter exatamente 11 dígitos(código do estado + número).',
            'telephone.unique' => 'Este telefone já está sendo utilizado por outro usuário.',
            'color.required' => 'O campo de cor é obrigatório.',
            'color.unique' => 'A cor já está em uso. Por favor, escolha outra cor única.',
            'profile_picture.image' => 'O arquivo deve ser uma imagem válida.',
            'password.required' => 'A senha é obrigatória.',

        ];
    }
}
