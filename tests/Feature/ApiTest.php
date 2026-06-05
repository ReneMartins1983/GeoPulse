<?php

namespace Tests\Feature;

use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_stats_endpoint_returns_aggregates(): void
    {
        Vehicle::factory()->count(5)->create(['status' => 'moving']);

        $this->getJson('/api/stats')
            ->assertOk()
            ->assertJsonStructure(['total', 'moving', 'idle', 'stopped', 'maintenance', 'avg_speed', 'avg_fuel'])
            ->assertJsonPath('total', 5);
    }

    public function test_vehicles_endpoint_lists_fleet(): void
    {
        Vehicle::factory()->count(3)->create();

        $this->getJson('/api/vehicles')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [['id', 'name', 'plate', 'status', 'lat', 'lng', 'speed', 'fuel']]]);
    }

    public function test_readings_endpoint_returns_time_series(): void
    {
        $vehicle = Vehicle::factory()->create();
        foreach (range(1, 5) as $m) {
            $vehicle->readings()->create([
                'speed' => 40,
                'fuel' => 80,
                'lat' => $vehicle->lat,
                'lng' => $vehicle->lng,
                'recorded_at' => now()->subMinutes($m),
            ]);
        }

        $this->getJson("/api/vehicles/{$vehicle->id}/readings")
            ->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure(['data' => [['recorded_at', 'speed', 'fuel']]]);
    }

    public function test_vehicles_index_advances_simulation_and_creates_readings(): void
    {
        $vehicle = Vehicle::factory()->create(['status' => 'moving']);

        $this->getJson('/api/vehicles')->assertOk();

        // o tick (disparado pelo index) deve gerar ao menos uma leitura
        $this->assertDatabaseHas('readings', ['vehicle_id' => $vehicle->id]);
    }
}
