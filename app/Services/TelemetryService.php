<?php

namespace App\Services;

use App\Models\Reading;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Cache;

class TelemetryService
{
    // Centro da operação (região de Novo Hamburgo/RS)
    public const CENTER_LAT = -29.6783;
    public const CENTER_LNG = -51.1306;

    /** Avança a simulação um passo para toda a frota. */
    public function tick(): void
    {
        $now = now();

        foreach (Vehicle::all() as $v) {
            if ($v->status === 'maintenance') {
                $v->speed = 0;
            } else {
                $roll = rand(1, 100);
                if ($roll <= 70) {
                    $v->status = 'moving';
                    $v->speed = rand(20, 90);
                } elseif ($roll <= 88) {
                    $v->status = 'idle';
                    $v->speed = rand(1, 8);
                } else {
                    $v->status = 'stopped';
                    $v->speed = 0;
                }

                if ($v->speed > 0) {
                    $v->heading = ($v->heading + rand(-25, 25) + 360) % 360;

                    // se afastou demais do centro, aponta de volta
                    if (abs($v->lat - self::CENTER_LAT) > 0.12 || abs($v->lng - self::CENTER_LNG) > 0.12) {
                        $v->heading = (int) ((rad2deg(atan2(self::CENTER_LNG - $v->lng, self::CENTER_LAT - $v->lat)) + 360)) % 360;
                    }

                    $rad = deg2rad($v->heading);
                    $step = 0.00006 * $v->speed;
                    $v->lat += cos($rad) * $step;
                    $v->lng += sin($rad) * $step;
                    $v->fuel = max(5, $v->fuel - 1);
                }
            }

            $v->last_tick_at = $now;
            $v->save();

            $v->readings()->create([
                'speed' => $v->speed,
                'fuel' => $v->fuel,
                'lat' => $v->lat,
                'lng' => $v->lng,
                'recorded_at' => $now,
            ]);
        }

        // mantém o histórico enxuto
        Reading::where('recorded_at', '<', $now->copy()->subHours(2))->delete();

        Cache::put('telemetry.last_tick', $now->timestamp, 3600);
    }

    /** Executa um tick apenas se o último foi há mais de N segundos (para o polling). */
    public function tickIfStale(int $seconds = 4): void
    {
        $last = (int) Cache::get('telemetry.last_tick', 0);

        if (now()->timestamp - $last >= $seconds) {
            $this->tick();
        }
    }
}
