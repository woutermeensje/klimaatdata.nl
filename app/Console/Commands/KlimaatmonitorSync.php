<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('klimaatmonitor:sync {--years=10 : Aantal jaren terug vanaf het meest recente volledige jaar}')]
#[Description('Volledige synchronisatie: regio\'s, indicatoren en waarden')]
class KlimaatmonitorSync extends Command
{
    public function handle(): int
    {
        $this->call('klimaatmonitor:sync-regions');
        $this->call('klimaatmonitor:sync-indicators');
        $this->call('klimaatmonitor:sync-values', ['--years' => $this->option('years')]);

        return self::SUCCESS;
    }
}
