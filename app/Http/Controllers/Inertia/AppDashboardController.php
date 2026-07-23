<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class AppDashboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('DashboardV1', [
            'appName' => config('app.name'),
        ]);
    }
}
