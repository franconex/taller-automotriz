<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PlacaVerificationService
{
    protected string $baseUrl;
    protected ?string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.placa_verification.base_url', 'https://api.example.com');
        $this->apiKey = config('services.placa_verification.api_key');
    }

    public function verify(string $placa): ?array
    {
        if ($this->apiKey) {
            return $this->callExternalApi($placa);
        }

        return $this->mockVerification($placa);
    }

    protected function callExternalApi(string $placa): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])->get("{$this->baseUrl}/vehicles/{$placa}");

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'placa' => strtoupper($placa),
                    'marca' => $data['marca'] ?? null,
                    'modelo' => $data['modelo'] ?? null,
                    'anio' => $data['anio'] ?? null,
                    'color' => $data['color'] ?? null,
                    'numero_chasis' => $data['chasis'] ?? $data['numero_chasis'] ?? null,
                    'numero_motor' => $data['motor'] ?? $data['numero_motor'] ?? null,
                    'combustible' => $data['combustible'] ?? null,
                    'origen' => 'api',
                ];
            }

            return null;
        } catch (\Exception $e) {
            logger()->error('Error al verificar placa: ' . $e->getMessage());
            return null;
        }
    }

    protected function mockVerification(string $placa): array
    {
        $placa = strtoupper(trim($placa));

        $mockData = [
            'ABC123' => [
                'marca' => 'Toyota',
                'modelo' => 'Corolla',
                'anio' => 2020,
                'color' => 'Blanco',
                'numero_chasis' => '8X8TFBRX7LY123456',
                'numero_motor' => '2ZR-FE1234567',
                'combustible' => 'Gasolina',
            ],
            'DEF456' => [
                'marca' => 'Suzuki',
                'modelo' => 'Swift',
                'anio' => 2022,
                'color' => 'Rojo',
                'numero_chasis' => '9Y9UGCTY8MZ789012',
                'numero_motor' => 'K14B-8901234',
                'combustible' => 'Gasolina',
            ],
            'GHI789' => [
                'marca' => 'Nissan',
                'modelo' => 'Versa',
                'anio' => 2021,
                'color' => 'Gris',
                'numero_chasis' => '7W7RHDQW6KX345678',
                'numero_motor' => 'HR16-5678901',
                'combustible' => 'Gasolina',
            ],
        ];

        if (isset($mockData[$placa])) {
            $data = $mockData[$placa];
            return [
                'placa' => $placa,
                'marca' => $data['marca'],
                'modelo' => $data['modelo'],
                'anio' => $data['anio'],
                'color' => $data['color'],
                'numero_chasis' => $data['numero_chasis'],
                'numero_motor' => $data['numero_motor'],
                'combustible' => $data['combustible'],
                'origen' => 'mock',
            ];
        }

        return [
            'placa' => $placa,
            'marca' => null,
            'modelo' => null,
            'anio' => null,
            'color' => null,
            'numero_chasis' => null,
            'numero_motor' => null,
            'combustible' => null,
            'origen' => 'mock',
        ];
    }
}
