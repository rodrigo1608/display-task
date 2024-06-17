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
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
