<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GrafanaService;

class HomeController extends Controller
{
    protected $grafanaService;

    public function __construct(GrafanaService $grafanaService)
    {
        $this->middleware('auth');
        $this->grafanaService = $grafanaService;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $apiToken = $user->api_token;
        $grafanaUrl = rtrim(config('services.grafana.url'), '/');
        $iframeUrl = "{$grafanaUrl}?auth={$apiToken}";
    
        return view('home', ['iframeUrl' => $iframeUrl]);
    }
    

}
