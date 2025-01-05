<?php

namespace App\Http\Services;

use App\Models\Favorite;
use App\Models\Champion;
use Illuminate\Support\Facades\Auth;
use Exception;

class FavoritesService
{
    public function addChampionToFavorites($user, $championId)
    {
        try {
            $existingFavorite = Favorite::where('user_id', $user->id)
                ->where('champion_id', $championId)
                ->first();

            if ($existingFavorite) {
                return response()->json(['message' => 'This champion is already in your favorites'], 400);
            }

            // Voeg de champion toe aan de favorieten van de gebruiker
            $favorite = Favorite::create([
                'user_id' => $user->id,
                'champion_id' => $championId
            ]);

            return response()->json(['message' => 'Champion added to favorites', 'favorite' => $favorite], 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'An error occurred while adding the champion to favorites', 'error' => $e->getMessage()], 500);
        }
    }

    public function removeChampionFromFavorites($user, $championId)
    {
        try {
            $favorite = Favorite::where('user_id', $user->id)
                ->where('champion_id', $championId)
                ->first();

            if (!$favorite) {
                return response()->json(['message' => 'This champion is not in your favorites'], 404);
            }

            $favorite->delete();

            return response()->json(['message' => 'Champion removed from favorites'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'An error occurred while removing the champion from favorites', 'error' => $e->getMessage()], 500);
        }
    }

    public function getUserFavorites($userId)
    {
        try {
            $favorites = Favorite::where('user_id', $userId)->with('champion')->get();

            if ($favorites->isEmpty()) {
                return response()->json(['message' => 'No favorites found for this user'], 404);
            }

            return $favorites;
        } catch (Exception $e) {
            return response()->json(['message' => 'An error occurred while fetching the favorites', 'error' => $e->getMessage()], 500);
        }
    }
}
