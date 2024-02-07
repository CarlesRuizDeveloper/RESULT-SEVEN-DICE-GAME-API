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
use Laravel\Passport\Passport;


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

    public function login(Request $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
    
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['error' => 'Error de inicio de sesión. Error en mail o contraseña.'], 401);
            }
    
            $token = $user->createToken('example')->accessToken;
    
            return response()->json([
                'message' => 'Has iniciado sesión',
                'user' => $user,
                'token' => $token
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function logout()
    {
        $user = Auth::user();

        if ($user) {

            $user->tokens->each->revoke();

            return response()->json('Hasta pronto!', 200);
        } else {
            return response()->json('No tienes sesion abierta!', 401);
        }
    }

    public function getAllPlayers()
    {
        $users = User::orderBy('name', 'asc')->get();
        $usersWithSuccessPercentage = $this->calculateSuccessPercentage($users);

        return response()->json([$usersWithSuccessPercentage], 200);
    }

    protected function calculateSuccessPercentage($users)
    {
        $result = $users->map(function ($user) {
            $totalGames = $user->games->count();
            $wonGames = $user->games->where('win', true)->count();

            return [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                ],
                'win_games_percentage' => $totalGames > 0 ? ($wonGames / $totalGames) * 100 : 0,
            ];
        });

        return $result;
    }
    
    public function getLoser()
    {
        $users = User::orderBy('name', 'asc')->get();
        $usersWithSuccessPercentage = $this->calculateSuccessPercentage($users);
    
        $loser = collect($usersWithSuccessPercentage)->sortBy('win_games_percentage')->first();
    
        return response()->json([
            'user' => $loser['user'],
            'win_games_percentage' => $loser['win_games_percentage'],
        ], 200);
    }

    public function getWinner()
    {
        $users = User::orderBy('name', 'asc')->get();
        $usersWithSuccessPercentage = $this->calculateSuccessPercentage($users);

        $winner = collect($usersWithSuccessPercentage)->sortByDesc('win_games_percentage')->first();

        return response()->json([
            'user' => $winner['user'],
            'win_games_percentage' => $winner['win_games_percentage'],
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:users,name,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->save();

        return response()->json($user, 200);
    }


    public function getRankingWithDetails()
{
    $users = User::with('games')->get();
    $usersWithSuccessPercentage = $this->calculateSuccessPercentage($users);

    $sortedUsers = collect($usersWithSuccessPercentage)->sortByDesc('win_games_percentage');

    $ranking = $sortedUsers->map(function ($user) {
        return [
            'name' => $user['user']['name'],
            'win_games_percentage' => $user['win_games_percentage'],
        ];
    });

    return response()->json($ranking, 200);
}

    


}