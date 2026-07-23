<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ViciDialStatsRequest;
use App\Services\ViciDialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ViciDialController extends Controller
{
    protected ViciDialService $viciDialService;

    public function __construct(ViciDialService $viciDialService)
    {
        $this->viciDialService = $viciDialService;
    }

    public function index()
    {
        return Inertia::render('ViciDial/Test', [
            'appName' => config('app.name'),
        ]);
    }

    public function statsPage()
    {
        return Inertia::render('ViciDial/Stats', [
            'appName' => config('app.name'),
        ]);
    }

    public function getUserDetails(Request $request, string $agent_user)
    {
        $stage = $request->query('stage', 'csv');
        $dialer = (int) $request->query('dialer', 4);

        try {
            $result = $this->viciDialService->getAgentDetails($agent_user, $stage, 'test', $dialer);

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Throwable $exception) {
            Log::error('VICIdial getUserDetails error', [
                'agent_user' => $agent_user,
                'stage' => $stage,
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch VICIdial agent details.',
            ], 500);
        }
    }

    public function getAgentStats(ViciDialStatsRequest $request)
    {
        $dialer = (int) $request->input('dialer', 4);

        try {
            $result = $this->viciDialService->getAgentStats(
                $request->input('start_date'),
                $request->input('end_date'),
                $request->input('stage', 'csv'),
                'crm',
                $dialer,
                $request->input('group_by_campaign', 'YES')
            );

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Throwable $exception) {
            Log::error('VICIdial getAgentStats error', [
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch VICIdial statistics.',
            ], 500);
        }
    }
}
