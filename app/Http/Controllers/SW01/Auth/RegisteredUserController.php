<?php

namespace App\Http\Controllers\SW01\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Carbon\Carbon;

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
            'user_firstName' => ['required', 'string', 'min:2', 'max:50'],
            'user_lastName' => ['required', 'string', 'min:2', 'max:50'],
            'user_pseudo' => ['required', 'string', 'max:50', 'min:2'],
           'user_password' => ['required', Rules\Password::defaults()], 
           'user_confirmation_password' => ['required', Rules\Password::defaults()], 
        ],[
            'user_firstName.required' => 'You must enter your firstName ',
            'user_firstName.string' => 'Your firstName must be of type string',
            'user_firstName.max' => 'You must enter a maximum of 50 characters',
            'user_firstName.min' => 'You must enter at least 2 characters',

            'user_lastName.required' => 'You must enter your lastName ',
            'user_lastName.string' => 'Your lastName must be of type string',
            'user_lastName.max' => 'You must enter a maximum of 50 characters',
            'user_lastName.min' => 'You must enter at least 2 characters',

            'user_pseudo.required' => 'You must enter a pseudo ',
            'user_pseudo.string' => 'Your pseudo must be of type string',
            'user_pseudo.max' => 'You must enter a maximum of 50 characters',
            'user_pseudo.min' => 'You must enter at least 2 characters',

            'user_password.required' => 'You must enter a password',
            'user_password.string' => 'Your password must be of type string',
            'user_password.max' => 'You must enter a maximum of 255 characters',
            'user_password.min' => 'You must enter at least 8 characters',

            'user_confirmation_password.required' => 'You must confirm your password',
            'user_confirmation_password.string' => 'Your password must be of type string',
            'user_confirmation_password.max' => 'You must enter a maximum of 255 characters',
            'user_confirmation_password.min' => 'You must enter at least 8 characters',
        ]);

        if ($request->user_confirmation_password!==$request->user_password){
            return response()->json([
                'errors' => [
                    'user_confirmation_password' => ["These passwords are different"]
                ]
            ], 429);
        }

        //We check if user_pseudo is already used in the data base
        $users=User::where('user_pseudo', '=', $request->user_pseudo)->get() ; 
        if (count($users)>0){
            return response()->json([
                'errors' => [
                    'user_pseudo' => ["This username is already used"]
                ]
            ], 429);
        }

    

        $date=Carbon::now() ;
        $user = User::create([
           'user_firstName' => $request->user_firstName,
            'user_lastName' => $request->user_lastName,
            'user_pseudo' => $request->user_pseudo,
            'password' => Hash::make($request->user_password),
            'user_startDate'=> $date,
        ]);
        
        event(new Registered($user));

        Auth::login($user);
    }
}
