<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'plate' => $this->plate,
            'driver' => $this->driver,
            'status' => $this->status,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'speed' => $this->speed,
            'fuel' => $this->fuel,
            'heading' => $this->heading,
            'updated_at' => $this->last_tick_at?->toIso8601String(),
        ];
    }
}
