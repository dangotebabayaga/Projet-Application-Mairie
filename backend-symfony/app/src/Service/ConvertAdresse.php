<?php

namespace App\Service;

class ConvertAdresse
{
    private string $userAgent;

    public function __construct(string $userAgent = 'MonAppSymfony/1.0')
    {
        $this->userAgent = $userAgent;
    }

    /**
     * Adresse -> coordonnées (lat, lng)
     */
    public function addressToCoordinates(string $adresse): ?array
    {
        $adresse = urlencode($adresse);
        $url = "https://nominatim.openstreetmap.org/search?q=$adresse&format=json&limit=1";

        $context = stream_context_create([
            'http' => [
                'header' => "User-Agent: {$this->userAgent}\r\n"
            ]
        ]);

        $response = file_get_contents($url, false, $context);
        if (!$response) return null;

        $data = json_decode($response, true);
        if (empty($data) || !isset($data[0]['lat'], $data[0]['lon'])) {
            return null;
        }

        return [
            'lat' => (float)$data[0]['lat'],
            'lng' => (float)$data[0]['lon'],
        ];
    }

    /**
     * Coordonnées -> adresse
     */
    public function coordinatesToAddress(float $lat, float $lng): ?string
    {
        $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat=$lat&lon=$lng&addressdetails=1";

        $context = stream_context_create([
            'http' => [
                'header' => "User-Agent: {$this->userAgent}\r\n"
            ]
        ]);

        $response = file_get_contents($url, false, $context);
        if (!$response) return null;

        $data = json_decode($response, true);
        return $data['display_name'] ?? null;
    }
}
