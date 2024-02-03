<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use League\OAuth2\Server\Exception\OAuthServerException;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function register(Request $request)
    { 
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $name = $this->generateUniqueName($request);
        $user = $this->createUser($request, $name);
        $this->assignPlayerRoleToUser($user);

        return response()->json($user, 201);
    }

    public function generateUniqueName(Request $request)
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

    public function assignPlayerRoleToUser($user)
    {
        $role = Role::findByName('player');
        $user->assignRole($role);
    }



    public function authenticate(Request $request)
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
            return response()->json(['error' => 'Error de inicio de sesión. Error en mail o contraseña.'], 401);
        }
    }

}