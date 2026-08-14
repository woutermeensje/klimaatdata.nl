<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class KlimaatmonitorService
{
    public function get(string $endpoint): array
    {
        return Http::withHeaders([
                'apikey' => config('services.klimaatmonitor.key'),
                'Accept' => 'application/json',
            ])
            ->timeout(60)
            ->retry(3, 1000)
            ->get(rtrim(config('services.klimaatmonitor.base_url'), '/').'/'.ltrim($endpoint, '/'))
            ->throw()
            ->json();
    }

    public function variables(): array
    {
        return $this->get('Variables');
    }

    public function geoLevels(): array
    {
        return $this->get('GeoLevels');
    }

    public function geoItems(): array
    {
        return $this->get('GeoItems');
    }

    public function periodLevels(): array
    {
        return $this->get('PeriodLevels');
    }

    public function periods(): array
    {
        return $this->get('Periods');
    }

    public function geoItemsForLevel(string $geoLevel): array
    {
        $response = $this->get("GeoLevels('{$geoLevel}')/GeoItems?\$top=1000");

        return $response['value'] ?? $response;
    }

    public function geoLevelsForVariable(string $variableCode): array
    {
        $response = $this->get("Variables('{$variableCode}')/GeoLevels");

        return array_map(fn (array $level) => $level['ExternalCode'], $response['value'] ?? $response);
    }

    public function valuesForVariable(string $variableCode, string $geoLevel, string $period, string $periodLevel = 'year'): array
    {
        $response = $this->get(
            "Variables('{$variableCode}')/GeoLevels('{$geoLevel}')/PeriodLevels('{$periodLevel}')/Periods('{$period}')/Values"
        );

        return $response['value'] ?? $response;
    }
}
