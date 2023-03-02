<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //
    function register(Request $request) {
        $fields = $request->validate(
            [
                'name'=> 'required|string',
                'email'=> 'required|string|unique:users,email',
                'password'=> 'required|string|confirmed'
            ]
            );
        
        $user = User::create(
            [
                'name'=> $fields['name'],
                'email'=> $fields['email'],
                'password'=> bcrypt($fields['password'])
            ]
        );

        $token = $user->createToken('myappToken')->plainTextToken;

        $response = [
            'user'=> $user, 
            'token'=> $token
        ];

        return response($response, 201);
    }

    function login(Request $request) {
        $fields = $request->validate(
            [
                'email'=> 'required|string',
                'password'=> 'required|string'
            ]
            );
        
        //check email
        $user = User::where('email', $fields['email'])->first();

        if(!$user || !Hash::check($fields['password'], $user->password)) {
            return response(
                [
                    'message'=> 'Invalid Email or Password'
                ], 401
            );
        }

        $token = $user->createToken('myappToken')->plainTextToken;

        $response = [
            'user'=> $user, 
            'token'=> $token
        ];

        return response($response, 201);
    }

    function logout()  {
        //clear all tokens
        auth()->user()->tokens()->delete();
        return [
            'message'=> 'Log out!'
        ];
    }
}
