<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function edit(int $id)
    {
        $user = User::find($id);

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

        return view('user/edit', compact('avaliableColors', 'user'));
    }

    public function update(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        // dd($request->all());

        $user->name = $request->input('name');
        $user->lastname = $request->input('lastname');
        $user->role = $request->input('role');
        $user->email = $request->input('email');
        $user->telephone = $request->input('telephone');

        $profilePicture = $request->profile_picture ?? null;
        if (isset($profilePicture)) {

            $profilePicturePath = getProfilePicturePath($profilePicture, $request->email) ?? null;

            $user->profile_picture = $profilePicturePath;
        }
        // dd($profilePicturePath);

        if ($request->filled('color')) {
            $user->color = $request->input('color');
        }

        // Atualizar a senha apenas se um novo valor for fornecido
        if ($request->filled('password')) {
            $user->password = bcrypt($request->input('password'));
        }

        $user->save();

        return redirect()->route('home')->with('success', 'Perfil atualizado com sucesso.');
    }
}
