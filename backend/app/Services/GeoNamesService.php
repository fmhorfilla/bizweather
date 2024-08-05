<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeoNamesService
{
    protected $geonamesBaseUrl = 'http://api.geonames.org/searchJSON';
    protected $geonamesUsername = 'franz';
    protected $weatherBaseUrl = 'https://api.openweathermap.org/data/2.5/weather';
    protected $weatherApiKey = 'af38656b494d2c9c7fdcb97f007d3dcb';

    public function getCitiesData(): array
    {
        $response = Http::get($this->geonamesBaseUrl, [
            'country' => 'JP',
            'username' => $this->geonamesUsername,
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to fetch data from Geonames API');
        }

        $data = $response->json();
        $cities = ['Tokyo', 'Yokohama', 'Kyoto', 'Osaka', 'Sapporo', 'Nagoya', 'Hiroshima', 'Yokohama', 'Fukuoka', 'Kobe', 'Sendai', 'Kanazawa', 'Saitama'];

        $citiesData = $this->filterAndFormatCities($data['geonames'], $cities);

        return $this->fetchWeatherData($citiesData);
    }

    protected function filterAndFormatCities(array $geonames, array $cities): array
    {
        return array_map(function ($city) {
            return [
                'name' => $city['toponymName'],
                'latitude' => $city['lat'],
                'longitude' => $city['lng'],
            ];
        }, array_filter($geonames, function ($geoname) use ($cities) {
            return in_array($geoname['toponymName'], $cities);
        }));
    }

    protected function fetchWeatherData(array $citiesData): array
    {
        return array_map(function ($city) {
            $response = Http::get($this->weatherBaseUrl, [
                'lat' => $city['latitude'],
                'lon' => $city['longitude'],
                'appid' => $this->weatherApiKey,
                'units' => 'metric',
            ]);

            if ($response->failed()) {
                throw new \Exception('Failed to fetch weather data from OpenWeatherMap');
            }

            $weatherData = $response->json();

            return array_merge($city, [
                'weather_main' => $weatherData['weather'][0]['main'] ?? 'N/A',
                'weather_description' => $weatherData['weather'][0]['description'] ?? 'N/A',
                'temperature' => $weatherData['main']['temp'] ?? 'N/A',
                'humidity' => $weatherData['main']['humidity'] ?? 'N/A',
                'weather_icon' => $weatherData['weather'][0]['icon'] ?? 'N/A',
            ]);
        }, $citiesData);
    }
}
