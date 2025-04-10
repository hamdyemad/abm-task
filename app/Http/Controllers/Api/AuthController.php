<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    use Helper;


    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required','email', 'exists:users,email'],
            'password' => 'required|string|min:8',
        ]);

        if($validator->fails()) {
            return $this->send_response(
                false,
                [],
                $validator->errors(),
                'Validation Error',
                422
            );
        }

        if(!Auth::attempt($request->only('email', 'password'))) {
            return $this->send_response(
                false,
                [],
                [],
                'Unauthorized',
                401
            );
        }

        // Remove Old Tokens Before Login
        $user = Auth::user();
        $user->tokens()->delete();
        if(Auth::check()) {
            $token =  Auth::user()->createToken('auth_token')->plainTextToken;
            return $this->send_response(
                true,
                [
                    'user' => Auth::user(),
                    'token' => $token,
                ],
                [],
                'User logged in successfully',
                200
            );
        }

    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'max:255'],
        ]);

        if($validator->fails()) {
            return $this->send_response(
                false,
                [],
                $validator->errors(),
                'Validation Error',
                422
            );
        }


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);


        return $this->send_response(
            true,
            $user,
            [],
            'User registered successfully',
            200
        );
    }


}
