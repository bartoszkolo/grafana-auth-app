<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GrafanaService;

class DashboardController extends Controller
{
    public function index(GrafanaService $grafanaService)
{
    $dashboardId = 'your-dashboard-id'; // Replace with your Grafana dashboard ID
    $dashboard = $grafanaService->getDashboard($dashboardId);

    return view('dashboard', ['dashboardUrl' => $dashboard['url']]);
}
}
