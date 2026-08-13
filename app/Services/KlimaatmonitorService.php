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
}
