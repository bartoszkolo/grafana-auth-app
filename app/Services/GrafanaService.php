<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;


class GrafanaService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('services.grafana.url'),
            'auth' => [config('services.grafana.username'), config('services.grafana.password')],
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }

    public function createUser($email, $username, $password)
{
    try {
        $response = $this->client->post('/api/admin/users', [
            'json' => [
                'email' => $email,
                'login' => $username,
                'password' => $password,
            ],
        ]);

        $userData = json_decode($response->getBody(), true);

        // Create a new MySQL database for the user
        $this->createUserDatabase($username);

        return $userData;
    } catch (\GuzzleHttp\Exception\ClientException $e) {
        // Inspect the error response
        $errorResponse = $e->getResponse()->getBody()->getContents();
        dd($errorResponse); // Dump the error response and exit
    }
}

    public function authenticateUser($username, $password)
    {
        try {
            $response = $this->client->get('/api/user', [
                'auth' => [$username, $password],
            ]);
    
            // If the request is successful (status code 200), return true
            if ($response->getStatusCode() == 200) {
                return true;
            }
        } catch (ClientException $e) {
            // If the request fails (status code 401, 403, etc.), return false
            if ($e->getCode() == 401 || $e->getCode() == 403) {
                return false;
            }
    
            // If the request fails for other reasons, rethrow the exception
            throw $e;
        }
    
        return false;
    }
    


public function createApiToken($username)
{
    try {
        $response = $this->client->post('/api/auth/keys', [
            'json' => [
                'name' => $username . '-token',
                'role' => 'Editor', // Set the appropriate role for the user
            ],
        ]);

        return json_decode($response->getBody(), true);
    } catch (\Exception $e) {
        return $e->getMessage();
    }
}

public function getDashboards($apiToken)
{
    try {
        $response = $this->client->get('/api/search', [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiToken,
            ],
        ]);

        if ($response->getStatusCode() == 200) {
            return json_decode($response->getBody(), true);
        } else {
            \Log::error("Grafana getDashboards request error: " . $response->getStatusCode() . " - " . $response->getReasonPhrase());
            return [];
        }
    } catch (\Exception $e) {
        \Log::error("Grafana getDashboards exception: " . $e->getMessage());
        return [];
    }
}


public function getDashboardIframeUrl($apiToken, $dashboardUid)
{
    $grafanaUrl = rtrim(config('services.grafana.url'), '/');
    return "{$grafanaUrl}/d/{$dashboardUid}?orgId=1&from=now-5m&to=now&kiosk&auth={$apiToken}";
}

public function getFolderIdByTitle($apiToken, $title)
{
    try {
        $response = $this->client->get('/api/folders', [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiToken,
            ],
        ]);

        $folders = json_decode($response->getBody(), true);
        \Log::info('All Folders:', ['folders' => $folders]);

        foreach ($folders as $folder) {
            \Log::info('Comparing:', ['folderTitle' => $folder['title'], 'targetTitle' => $title]);
            if ($folder['title'] === $title) {
                return $folder['id'];
            }
        }
    } catch (\Exception $e) {
        return null;
    }

    return null;
}

public function getDashboardsInFolder($apiToken, $folderId)
{
    try {
        $response = $this->client->get('/api/search', [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiToken,
            ],
            'query' => [
                'folderIds' => [$folderId],
            ],
        ]);

        $dashboards = json_decode($response->getBody(), true);
        \Log::info('Dashboards in folder:', $dashboards);
        return $dashboards;
    } catch (\Exception $e) {
        \Log::error('Error getting dashboards in folder:', ['message' => $e->getMessage()]);
        return [];
    }
}

private function createUserDatabase($username)
{
    // Sanitize the username to make it suitable for a database name
    $dbName = preg_replace('/[^a-zA-Z0-9_]/', '_', $username) . '_db';

    try {
        DB::statement("CREATE DATABASE {$dbName}");
        \Log::info("Created database: {$dbName}");
    } catch (\Exception $e) {
        \Log::error("Failed to create database: {$dbName}. Error: {$e->getMessage()}");
    }
}


}
