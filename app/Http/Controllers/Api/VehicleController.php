<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReadingResource;
use App\Http\Resources\VehicleResource;
use App\Models\Vehicle;
use App\Services\TelemetryService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VehicleController extends Controller
{
    /** Lista a frota com o estado atual (avança a simulação se necessário). */
    public function index(TelemetryService $telemetry): AnonymousResourceCollection
    {
        $telemetry->tickIfStale();

        return VehicleResource::collection(Vehicle::orderBy('name')->get());
    }

    /** Série temporal recente de um veículo (para os gráficos). */
    public function readings(Vehicle $vehicle): AnonymousResourceCollection
    {
        $readings = $vehicle->readings()
            ->latest('recorded_at')
            ->take(40)
            ->get()
            ->reverse()
            ->values();

        return ReadingResource::collection($readings);
    }
}
