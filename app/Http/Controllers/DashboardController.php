<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GrafanaService;

class DashboardController extends Controller
{

    protected $grafanaService;

    public function __construct(GrafanaService $grafanaService)
    {
        $this->grafanaService = $grafanaService;
    }
    public function getDashboardIframeUrl($apiToken, $dashboardUid)
    {
        $grafanaUrl = rtrim(config('services.grafana.url'), '/');
        return "{$grafanaUrl}/d/{$dashboardUid}?orgId=1&from=now-5m&to=now&kiosk&auth={$apiToken}";
    }
    
    public function index(Request $request)
    {
        $apiToken = $request->user()->api_token;
    
        // Get the dashboards for the current user
        $dashboards = $this->grafanaService->getDashboards($apiToken);
    
        if (count($dashboards) > 0) {
            // Get the first dashboard's UID created by the user
            $dashboardUid = $dashboards[0]['uid'];
    
            $iframeUrl = $this->getDashboardIframeUrl($apiToken, $dashboardUid);
            return view('dashboard', ['iframeUrl' => $iframeUrl]);
        } else {
            // Handle the case when there are no dashboards
            return view('dashboard', ['iframeUrl' => null]);
        }
        return view('home', ['userDashboards' => $userDashboards]);
    }
    

    
}
