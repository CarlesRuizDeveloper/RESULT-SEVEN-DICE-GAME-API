<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Game;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Validator;

class GameController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function rollDice(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->id != Auth::user()->id) {
            return response()->json('No tens permisos per a realitzar aquesta acció.', 403);
        }

        $dice1 = rand(1, 6);
        $dice2 = rand(1, 6);
        $total = $dice1 + $dice2;
        $win = $total === 7 ? true : false;

        $game = Game::create([
            'user_id' => $user->id,
            'dice1' => $dice1,
            'dice2' => $dice2,
            'win' => $win
        ]);

        return response()->json($game, 201);
    }

    public function deleteAllGames($id)
    {
        $user = User::findOrFail($id);

        if ($user->id != Auth::user()->id) {
            return response()->json('No tens permisos per a realitzar aquesta acció.', 403);
        }

        Game::where('user_id', $user->id)->delete();

        return response()->json('Historial de tirades eliminat correctament.', 200);
    }

    public function getAllPlayers()
    {
        $users = User::with('games')->get();
        $players = [];

        foreach ($users as $user) {
            $games = $user->games;
            $wins = $games->where('win', true)->count();
            $percentage = $games->count() > 0 ? round(($wins / $games->count()) * 100, 2) : 0;

            $players[] = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'games_played' => $games->count(),
                'games_won' => $wins,
                'percentage' => $percentage
            ];
        }

        return response()->json($players, 200);
    }

    public function getGames($id)
    {
        $user = User::findOrFail($id);
    
        $games = $user->games;
    
        $gamesCount = $games ? $games->count() : 0;
    
        if ($gamesCount > 0) {
            $wins = $games->where('win', true)->count();
            $percentage = round(($wins / $gamesCount) * 100, 2);
        } else {
            $wins = 0;
            $percentage = 0;
        }
    
        return response()->json([
            'games' => $games,
            'games_played' => $gamesCount,
            'games_won' => $wins,
            'percentage' => $percentage
        ], 200);
    }
    
    
    public function getRanking()
    {
        $users = User::with('games')->get();
        $players = [];

        foreach ($users as $user) {
            $games = $user->games;
            $wins = $games->where('win', true)->count();
            $percentage = $games->count() > 0 ? round(($wins / $games->count()) * 100, 2) : 0;

            $players[] = [
                'id' => $user->id,
                'name' => $user->name,
                'percentage' => $percentage
            ];
        }

        $ranking = collect($players)->sortByDesc('percentage');

        return response()->json($ranking, 200);
    }

    public function getLoser()
    {
        $users = User::with('games')->get();
        $players = [];
    
        foreach ($users as $user) {
            $games = $user->games;
            $wins = $games->where('win', true)->count();
            $percentage = $games->count() > 0 ? round(($wins / $games->count()) * 100, 2) : 0;
    
            $players[] = [
                'id' => $user->id,
                'name' => $user->name,
                'percentage' => $percentage
            ];
        }
    
        $ranking = collect($players)->sortBy('percentage');
        $loser = $ranking->first();
    
        return response()->json($loser, 200);
    }
    
    public function getWinner()
    {
        $users = User::with('games')->get();
        $players = [];
    
        foreach ($users as $user) {
            $games = $user->games;
            $wins = $games->where('win', true)->count();
            $percentage = $games->count() > 0 ? round(($wins / $games->count()) * 100, 2) : 0;
    
            $players[] = [
                'id' => $user->id,
                'name' => $user->name,
                'percentage' => $percentage
            ];
        }
    
        $ranking = collect($players)->sortByDesc('percentage');
        $winner = $ranking->first();
    
        return response()->json($winner, 200);
    }
}    