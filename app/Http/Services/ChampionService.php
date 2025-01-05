<?php

namespace App\Http\Services;

use App\Models\Champion;
use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Http\Request;

class ChampionService
{
    // Haal champions op uit de database of vanuit de API
    public function getChampionsFromDatabaseOrApi(Request $request)
    {
        // Controleer of de 'First-Session' header aanwezig is en de waarde 'true' heeft
        $isFirstSession = $request->hasHeader('First-Session') && $request->header('First-Session') === 'true';

        // Als het de eerste keer is, haal dan altijd de gegevens van de API
        if ($isFirstSession) {
            return $this->importChampionsFromApi();
            return response()->json($champions, 200);
        }

        // Haal champions op uit de database
        $champions = Champion::orderBy('name')->get();

        // Als de database leeg is, haal dan de champions uit de API
        if ($champions->isEmpty()) {
            return $this->importChampionsFromApi();
        }

        return response()->json($champions, 200);
    }

    // Importeer champions vanuit de API naar de database
    public function importChampionsFromApi()
    {
        $champions = $this->getAllChampionsFromApi();

        if ($champions) {
            foreach ($champions['data'] as $champion) {
                Champion::updateOrCreate(
                    ['id' => $champion['key']],
                    [
                        'name' => $champion['name'],
                        'title' => $champion['title'],
                        'blurb' => $champion['blurb'],
                        'image' => $champion['image']['full'],
                        'lore' => '',
                        'tags' => json_encode($champion['tags']),
                        'info' => json_encode([]),
                        'stats' => json_encode([]),
                        'spells' => json_encode([]),
                        'passive' => json_encode([]),
                        'skins' => json_encode([]),
                    ]
                );
            }

            return response()->json(Champion::orderBy('name')->get(), 200);
        }

        return response()->json(['message' => 'Failed to fetch champions from API'], 500);
    }

    // Haal details op van een specifieke champion
    public function getChampionDetailsFromApi($id)
    {
        $champion = Champion::where('id', $id)->first();

        if (!$champion) {
            return response()->json(['message' => 'Champion not found in database'], 404);
        }

        $championDetail = $this->getChampionDetailFromApi($champion->name);

        if (!$championDetail) {
            $cleanedName = $this->normalizeChampionName($champion->name);
            $championDetail = $this->getChampionDetailFromApi($cleanedName);

            if (!$championDetail) {
                $lowerCaseName = $this->lowercaseSecondPart($champion->name);
                $championDetail = $this->getChampionDetailFromApi($lowerCaseName);
            }
        }

        if ($championDetail) {
            $champion->update([
                'lore' => $championDetail['lore'],
                'info' => json_encode($championDetail['info']),
                'stats' => json_encode($championDetail['stats']),
                'spells' => json_encode($championDetail['spells']),
                'passive' => json_encode($championDetail['passive']),
                'skins' => json_encode($championDetail['skins']),
            ]);

            return response()->json([
                'champion' => $champion,
                'details' => $championDetail
            ], 200);
        }

        return response()->json(['message' => 'Champion details not found in API'], 404);
    }

    // Ophalen van alle champions van de API
    private function getAllChampionsFromApi()
    {
        try {
            $response = Http::get("https://ddragon.leagueoflegends.com/cdn/12.14.1/data/en_US/champion.json");

            if ($response->successful()) {
                return $response->json();
            }

            return response()->json(['message' => 'Failed to fetch champions from API. Response was not successful.'], 500);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to fetch champions from API. Error: ' . $e->getMessage()], 500);
        }
    }

    // Haal de details op van een specifieke champion van de API
    private function getChampionDetailFromApi($name)
{
    try {
        $response = Http::get("https://ddragon.leagueoflegends.com/cdn/12.14.1/data/en_US/champion/{$name}.json");

        if ($response->successful()) {
            return $response->json()['data'][$name];
        }

        return response()->json(['message' => "Failed to fetch details for champion {$name}. Response was not successful."], 500);
    } catch (Exception $e) {
        return response()->json(['message' => "Failed to fetch details for champion {$name}. Error: " . $e->getMessage()], 500);
    }
}

    // Normaliseer de naam van de champion (verwijder speciale tekens)
    private function normalizeChampionName($name)
    {
        return str_replace(["'", ' '], '', $name);
    }

    // Zet de tweede deel van de naam in kleine letters en de rest in hoofdletters
    private function lowercaseSecondPart($name)
    {
        $nameWithoutApostrophesAndSpaces = str_replace(["'", ' '], '', $name);
        return ucfirst(strtolower($nameWithoutApostrophesAndSpaces));
    }
}
