<?php

namespace App\Console\Commands;

use App\Services\KlimaatmonitorService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

#[Signature('klimaatmonitor:variables {--raw : Print the full raw JSON response}')]
#[Description('Fetch the Klimaatmonitor /Variables endpoint and inspect its structure')]
class KlimaatmonitorInspectVariables extends Command
{
    public function handle(KlimaatmonitorService $service): int
    {
        try {
            $variables = $service->variables();
        } catch (RequestException $e) {
            $this->error("Klimaatmonitor API gaf een foutstatus: {$e->response->status()}");
            $this->line($e->response->body());

            return self::FAILURE;
        } catch (ConnectionException $e) {
            $this->error("Kon geen verbinding maken met de Klimaatmonitor API: {$e->getMessage()}");

            return self::FAILURE;
        }

        if ($this->option('raw')) {
            $this->line(json_encode($variables, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return self::SUCCESS;
        }

        $items = $variables['value'] ?? $variables;

        $this->info('Aantal items: '.count($items));

        if ($first = reset($items)) {
            $this->info('Velden op een item:');
            $this->line(json_encode(array_keys($first), JSON_PRETTY_PRINT));

            $this->info('Voorbeelditem:');
            $this->line(json_encode($first, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }

        return self::SUCCESS;
    }
}
