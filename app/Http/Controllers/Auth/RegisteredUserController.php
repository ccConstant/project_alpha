<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
       $request->validate([
            'user_firstName' => ['required', 'string', 'max:255'],
            'user_lastName' => ['required', 'string', 'max:255'],
            'user_pseudo' => ['required', 'string', 'max:255'],
           'user_password' => ['required', Rules\Password::defaults()], //rajouter confirmed
        ],[
            'user_firstName.required' => 'You must enter your firstname ',
            'user_firstname.string' => 'Your firstName must be of type string',
            'user_firstname.max' => 'You must enter a maximum of 255 characters',

            'user_lastName.required' => 'You must enter your lastName ',
            'user_lastname.string' => 'Your lastName must be of type string',
            'user_lastname.max' => 'You must enter a maximum of 255 characters',

            'user_pseudo.required' => 'You must enter a pseudo ',
            'user_pseudo.string' => 'Your pseudo must be of type string',
            'user_pseudo.max' => 'You must enter a maximum of 255 characters',

            'user_password.required' => 'You must enter a password',
            'user_password.string' => 'Your password must be of type string',
            'user_password.max' => 'You must enter a maximum of 255 characters',
        ]);
    

        //verifié email
        $user = User::create([
            'user_firstName' => $request->user_firstName,
            'user_lastName' => $request->user_lastName,
            'user_pseudo' => $request->user_pseudo,
            'user_password' => Hash::make($request->user_password),
        ]);
        
        event(new Registered($user));

        Auth::login($user);

        //REDIRIGER VERS UNE PAGE "vous êtes connecté" 
    }
}
