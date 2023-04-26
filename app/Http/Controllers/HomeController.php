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
        $userEmail = $user->email;

        // Get the folder ID by the user's email
        $folderId = $this->grafanaService->getFolderIdByTitle($apiToken, $userEmail);

        if ($folderId !== null) {
            // Get the dashboards in the folder
            $userDashboards = $this->grafanaService->getDashboardsInFolder($apiToken, $folderId);
        } else {
            // Handle the case when there's no folder with the user's email
            $userDashboards = [];
        }

        return view('home', ['userDashboards' => $userDashboards]);
    }
}
