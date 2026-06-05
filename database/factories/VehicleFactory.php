<?php

namespace Database\Factories;

use App\Models\Vehicle;
use App\Services\TelemetryService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition(): array
    {
        return [
            'name' => 'Caminhão '.$this->faker->unique()->numberBetween(1, 999),
            'plate' => strtoupper($this->faker->unique()->bothify('???#?##')),
            'driver' => $this->faker->name(),
            'status' => $this->faker->randomElement(Vehicle::STATUSES),
            'lat' => TelemetryService::CENTER_LAT + $this->faker->numberBetween(-80, 80) / 1000,
            'lng' => TelemetryService::CENTER_LNG + $this->faker->numberBetween(-80, 80) / 1000,
            'speed' => $this->faker->numberBetween(0, 90),
            'fuel' => $this->faker->numberBetween(30, 100),
            'heading' => $this->faker->numberBetween(0, 359),
        ];
    }
}
