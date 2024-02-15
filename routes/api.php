<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/players', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login'])->name('login');

Route::middleware('auth:api')->group(function () {

    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
    Route::put('/players/{id}', [UserController::class, 'update'])->middleware('can:update');  


    Route::middleware('role:admin')->group(function () {

        Route::get('/players', [UserController::class, 'getAllPlayers'])->middleware('can:getAllPlayers');
        Route::get('/players/ranking', [UserController::class, 'getRankingWithDetails'])->middleware('can:getRankingWithDetails'); 
        Route::get('/players/ranking/loser', [UserController::class, 'getLoser'])->middleware('can:getLoser'); 
        Route::get('/players/ranking/winner', [UserController::class, 'getWinner'])->middleware('can:getWinner'); 
    });

    Route::middleware('role:player')->group(function () {

        Route::get('/players/{id}/games', [GameController::class, 'getGames'])->middleware('can:getGames');
        Route::post('/players/{id}/games', [GameController::class, 'rollDice'])->middleware('can:rollDice');
        Route::delete('/players/{id}/games', [GameController::class, 'deleteAllGames'])->middleware('can:deleteAllGames');

    });    
});



 




