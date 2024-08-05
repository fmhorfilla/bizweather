<?php

namespace App\Http\Controllers;

use App\Services\GeoNamesService;
use Illuminate\Http\JsonResponse;

class GeoNamesController extends Controller
{
    protected $geoNamesService;

    public function __construct(GeoNamesService $geoNamesService)
    {
        $this->geoNamesService = $geoNamesService;
    }

    public function getCitiesWithWeather(): JsonResponse
    {
        $data = $this->geoNamesService->getCitiesData();

        return response()->json($data);
    }
}
