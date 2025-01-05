<?php

namespace App\Http\Controllers;

use App\Http\Services\FavoritesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoritesController extends Controller
{
    protected $favoritesService;

    public function __construct(FavoritesService $favoritesService)
    {
        $this->favoritesService = $favoritesService;
        $this->middleware('auth:api');
    }

    public function store(Request $request)
    {
        $request->validate([
            'champion_id' => 'required|string|exists:champions,id',
        ]);

        $user = Auth::user();
        return $this->favoritesService->addChampionToFavorites($user, $request->champion_id);
    }

    public function destroy($champion_id)
    {
        $user = Auth::user();
        return $this->favoritesService->removeChampionFromFavorites($user, $champion_id);
    }

    public function index()
    {
        $user = Auth::user();
        return $this->favoritesService->getUserFavorites($user->id);
    }

    public function getUserFavorites($id)
{
    return $this->favoritesService->getUserFavorites($id);;
}
}
