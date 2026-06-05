<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use App\Services\TelemetryService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $fleet = [
            ['name' => 'Caminhão 01', 'plate' => 'BRA1A23', 'driver' => 'Carlos Souza', 'status' => 'moving'],
            ['name' => 'Caminhão 02', 'plate' => 'BRA2B34', 'driver' => 'Marina Lopes', 'status' => 'moving'],
            ['name' => 'Van 01', 'plate' => 'BRA3C45', 'driver' => 'João Pereira', 'status' => 'idle'],
            ['name' => 'Van 02', 'plate' => 'BRA4D56', 'driver' => 'Ana Ribeiro', 'status' => 'moving'],
            ['name' => 'Furgão 01', 'plate' => 'BRA5E67', 'driver' => 'Pedro Alves', 'status' => 'stopped'],
            ['name' => 'Furgão 02', 'plate' => 'BRA6F78', 'driver' => 'Júlia Castro', 'status' => 'moving'],
            ['name' => 'Caminhão 03', 'plate' => 'BRA7G89', 'driver' => 'Rafael Dias', 'status' => 'maintenance'],
            ['name' => 'Van 03', 'plate' => 'BRA8H90', 'driver' => 'Beatriz Nunes', 'status' => 'moving'],
        ];

        foreach ($fleet as $data) {
            $vehicle = Vehicle::create([
                ...$data,
                'lat' => TelemetryService::CENTER_LAT + rand(-80, 80) / 1000,
                'lng' => TelemetryService::CENTER_LNG + rand(-80, 80) / 1000,
                'speed' => $data['status'] === 'moving' ? rand(25, 85) : 0,
                'fuel' => rand(45, 100),
                'heading' => rand(0, 359),
                'last_tick_at' => now(),
            ]);

            // histórico inicial (últimos ~30 min) para os gráficos
            $speed = $vehicle->speed;
            $fuel = $vehicle->fuel;
            for ($m = 30; $m >= 1; $m--) {
                $speed = max(0, min(95, $speed + rand(-15, 15)));
                $fuel = max(5, $fuel - rand(0, 1));
                $vehicle->readings()->create([
                    'speed' => $speed,
                    'fuel' => $fuel,
                    'lat' => $vehicle->lat,
                    'lng' => $vehicle->lng,
                    'recorded_at' => now()->subMinutes($m),
                ]);
            }
        }
    }
}
