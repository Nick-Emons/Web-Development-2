<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\ChampionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FavoritesController;
use App\Http\Controllers\UserController;

Route::get('/champions/import', [ChampionController::class, 'importChampions']);

Route::get('/champions', [ChampionController::class, 'getChampions']);
Route::get('/champions/{id}', [ChampionController::class, 'getChampion']);
Route::post('/champions', [ChampionController::class, 'createChampion']);
Route::put('/champions/{id}', [ChampionController::class, 'updateChampion']);
Route::delete('/champions/{id}', [ChampionController::class, 'deleteChampion']);
Route::get('/champions/{id}/skins/import', [ChampionController::class, 'importChampionSkins']);
Route::get('/champions/{id}/skins', [ChampionController::class, 'getChampionSkins']);
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('refresh-token', [AuthController::class, 'refreshToken']);


// Authenticated routes
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user(); 
});

Route::middleware('auth:api')->get('/users', [UserController::class, 'getAllUsers']);
Route::middleware('auth:api')->put('/users/{id}', [UserController::class, 'updateUser']);

Route::middleware('auth:api')->group(function () {
    Route::post('/favorites', [FavoritesController::class, 'store']);  
    Route::delete('/favorites/{champion_id}', [FavoritesController::class, 'destroy']);  
    Route::get('/favorites', [FavoritesController::class, 'index']);  
});
Route::middleware('auth:api')->get('/users/{id}/favorites', [FavoritesController::class, 'getUserFavorites']);

Route::get('/run-migrations', function () {
    // Zorg ervoor dat je alleen in productie de migraties uitvoert
    if (app()->environment('production')) {
        Artisan::call('migrate', ['--force' => true]);
        return response()->json(['status' => 'Migraties uitgevoerd']);
    }

    return response()->json(['error' => 'Niet toegestaan'], 403);
});