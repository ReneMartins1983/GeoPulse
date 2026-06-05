<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;

class StatsController extends Controller
{
    /** Indicadores agregados da frota (KPIs). */
    public function index(): JsonResponse
    {
        $vehicles = Vehicle::all();

        return response()->json([
            'total' => $vehicles->count(),
            'moving' => $vehicles->where('status', 'moving')->count(),
            'idle' => $vehicles->where('status', 'idle')->count(),
            'stopped' => $vehicles->where('status', 'stopped')->count(),
            'maintenance' => $vehicles->where('status', 'maintenance')->count(),
            'avg_speed' => (int) round($vehicles->avg('speed') ?? 0),
            'avg_fuel' => (int) round($vehicles->avg('fuel') ?? 0),
        ]);
    }
}
