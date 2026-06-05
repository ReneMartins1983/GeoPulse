<?php

namespace App\Console\Commands;

use App\Services\TelemetryService;
use Illuminate\Console\Command;

class TelemetryTick extends Command
{
    protected $signature = 'telemetry:tick';

    protected $description = 'Avança a simulação de telemetria da frota um passo';

    public function handle(TelemetryService $telemetry): int
    {
        $telemetry->tick();
        $this->info('Telemetria atualizada.');

        return self::SUCCESS;
    }
}
