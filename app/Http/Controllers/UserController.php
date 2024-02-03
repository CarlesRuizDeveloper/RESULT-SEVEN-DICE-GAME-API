<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use League\OAuth2\Server\Exception\OAuthServerException;

class UserController extends Controller
{


    public function register(Request $request)
    {
        $name = $this->generateName($request);
        $user = $this->createUser($request, $name);
        $this->assignRoleToUser($user);

        return response()->json($user, 201);
    }

    public function generateName(Request $request)
    {
        $name = $request->name;
        if ($name == NULL) {
            $name = 'Anonymous';
        } else {
            $user = User::where('name', $name)->first();
            if ($user) {
                // // handle accordingly in validations Request.
            }
        }
        return $name;
    }

    public function createUser(Request $request, $name)
    {
        $user = User::create([
            'name' => $name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        return $user;
    }

    public function assignRoleToUser($user)
    {
        $role = Role::findByName('player');
        $user->assignRole($role);
    }



    public function login(Request $request)//1@1.com eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiMzkxM2RjMGU3MTJkZWI1YjQ3NjcxNGEzNGI2ZTY3M2I3NmExOGE1OGQ5NWQxNzkyNjEwN2VhMmFiOGM4ZDdkZTk0NDE5ZWZlYjIwNDI5ZGMiLCJpYXQiOjE3MDY5NDcxMjAuMzgyOTI0LCJuYmYiOjE3MDY5NDcxMjAuMzgyOTI4LCJleHAiOjE3Mzg1Njk1MjAuMTI0MjA5LCJzdWIiOiIyMyIsInNjb3BlcyI6W119.o-iEBRqay74StG_5NLNACRmLQsIRAjTOrwUwFa0M0f-dScef3I2fmEVyvfGEeweKjBDYp49D0Y4BrQk3RQCsfhjX6GeK5IwqS-qQW5JeD7wGukbz6EiAkUqCXDPLsrNBv33PrQQsR6zGpiILmXEPXP6a64gHxbCFvB-XM2kCepjvzeF2wfhJdl8EVPiJQxbIDgeSkBfS66sPP2JfXWTfSGsP-iQhtJqSYwgUSUymfAC4YCeYXmNF7v3ncATMW1FXnKxflbIgoOS8CeeDtcQndnF16ab02XogF6FrfroScx-gtksZQ0KFHPtVZjYqhYaBBxw8txr_PsjDC1sc6QrGzqARFthN_YAmoGWPaV8Q00YvSTie6xj7ImpakpKG0XGvalb1V_AZF-CHQl-Kq7Q2RXwqXYw4ZINM32wuRnW2WE3AV38nTLU1Hj4uvF2FpJ3iXW6FYJutYoVzDadcbkzYdKLksek6s5169ZtIbUf-4wxYv2yatkESgDqeePwfb8sQnYGHG6qBiQzdOYzfNgnDOMc8lTKYF8WxVEP17-mC1MeH7H11DZ3v17EsKcZf4MRmGdRlRgun7o5paIB0dH855QHpj6XnqdAXvDej_x12Lck6ejFqbhDaGBuZH8-LXV0hApGjEb1v_cFfMFIwZHBPjNor49Cg7HT7HmZ8iOS_r3g
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (Auth::attempt($credentials)) {
            $user = $request->user();
            $token = $user->createToken('example')->accessToken;

            return response()->json([
                'message' => 'Has iniciado sesión',
                'user' => $user,
                'token' => $token
            ], 200);
        } else {
            return response()->json(['error' => 'Error de inicio de sesión \n Error en mail o contraseña.'], 401);
        }
    }

}