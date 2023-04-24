<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GrafanaService;

class DashboardController extends Controller
{
    public function getDashboardIframeUrl($username, $password)
    {
        $dashboardUid = '1'; // Replace this with the UID of the dashboard you want to display
        $grafanaUrl = rtrim(config('services.grafana.url'), '/');
        $encodedCredentials = base64_encode("{$username}:{$password}");
    
        return "{$grafanaUrl}/d-solo/{$dashboardUid}?orgId=1&from=now-5m&to=now&kiosk&panelId=2&auth={$encodedCredentials}";
    }

    public function index(Request $request)
{
    $username = $request->user()->name;
    $password = $request->user()->password; // You may need to adjust this to get the user's plaintext password

    $iframeUrl = $this->getDashboardIframeUrl($username, $password);

    return view('dashboard', ['iframeUrl' => $iframeUrl]);
}

    
}
