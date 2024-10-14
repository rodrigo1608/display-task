<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Mail\RegisterInvitationMail;
use App\Models\RegisterInvitation;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class RegisterInvitationController extends Controller
{
    public function index()
    {
        return  view('user/invitation');
    }

    public function invite(Request $request)
    {
        $token = Str::random(50);

        $expiresAt = getCarbonNow()->addMinutes(15);

        RegisterInvitation::first()->delete();

        $users = User::all();

        foreach($users as $user){
            if($user->email === $request->email){

            }
        }

        dd($users);

        RegisterInvitation::create([
            'email' => $request->email,
            'token' => $token,
            'expires_at' => $expiresAt,
        ]);

        $link = route('register', ['token' => $token]);


        Mail::to($request->email)->send(new RegisterInvitationMail($link));

        return  redirect()->route('home')->with('success', 'Convite enviado!');;
    }
}
