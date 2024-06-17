<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }


    public function showRegistrationForm()
    {
        $avaliableColors =  [

            'DimGray' => '#696969',

            'SlateBlue' => '#6A5ACD',

            'MediumBlue' => '#0000CD',

            'SteelBlue' => '#4682B4',

            'DarkTurquoise' => '#00CED1',

            'SeaGreen' => '#2E8B57',

            'DarkOliveGreen' => '#556B2F',

            'Sienna' => '#A0522D',

            'FireBrick' => '#B22222',

            'MediumVioletRed' => '#C71585',

            'Maroon' => '#800000',

            'DarkSlateGray' => '#2F4F4F'

        ];

        $previouslySelectedColors = User::pluck('color')->toArray();

        foreach ($previouslySelectedColors as $selectedColor) {

            $colorKey = array_search($selectedColor, $avaliableColors);

            $isColorChosen = $colorKey !== false;

            if ($isColorChosen) {

                unset($avaliableColors[$colorKey]);
            }
        }

        return view('auth/register', compact('avaliableColors'));
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $genericStringRules =  ['required', 'min:2', 'max:30'];

        $rules = [
            'name' => $genericStringRules,
            'lastname' => $genericStringRules,
            'role' => $genericStringRules,
            'email' => 'required|email|unique:users',
            'telephone' => 'required|numeric|digits:11|unique:users',
            'color' => 'required|unique:users,color',
            'profile_picture' => 'image',
            'password' => 'required|confirmed',
        ];

        $feedbacks = [
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
        return Validator::make($data, $rules, $feedbacks);
    }

    public function getProfilePicturePath($image, $email)
    {

        $hasUserUploadedPicture = isset($image);

        if ($hasUserUploadedPicture) {

            $emailWithoutDotCom = str_replace('.com', '', $email);

            $profilePictureName = $emailWithoutDotCom . '-' . time() . '-icon.' . $image->getClientOriginalExtension();

            // rodrigo
            // @dd($profilePictureName);

            return  $image->storeAs('profile_pictures', $profilePictureName);
        }

        return 'default_user_icon.jpg';
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {

        $profilePicture = $data['profile_picture'];

        $profilePicturePath = $this->getProfilePicturePath($profilePicture, $data['email']) ?? null;

        return User::create([
            'name' => $data['name'],

            'lastname' => $data['lastname'],

            'role' => $data['role'],

            'telephone' => $data['telephone'],

            'profile_picture' => $profilePicturePath,

            'email' => $data['email'],

            'color' => $data['color'],

            'avaliable' => 'Y',

            'password' => Hash::make($data['password']),
        ]);
    }
}
