<?php

namespace App\Http\Controllers;

use App\Http\Services\ChampionService;
use Illuminate\Http\Request;

class ChampionController extends Controller
{
    protected $championService;

    public function __construct(ChampionService $championService)
    {
        $this->championService = $championService;
    }

    // Haalt champions op, als de database leeg is, haalt ze dan uit de API
    public function getChampions(Request $request)
    {
        return $this->championService->getChampionsFromDatabaseOrApi($request);
    }

    // Details van een specifieke champion
    public function getChampion($id)
    {
        return $this->championService->getChampionDetailsFromApi($id);
    }

    // Importeert champions vanuit de API naar de database
    public function importChampions()
    {
        return $this->championService->importChampionsFromApi();
    }
}
