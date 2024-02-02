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



}