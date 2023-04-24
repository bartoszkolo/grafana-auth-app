<?php

namespace App\Services;

use GuzzleHttp\Client;

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
    
            return json_decode($response->getBody(), true);
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
        return json_decode($response->getBody(), true);
    } catch (\Exception $e) {
        return $e->getMessage();
    }
}

public function getDashboardIframeUrl($apiToken, $dashboardUid)
{
    $grafanaUrl = rtrim(config('services.grafana.url'), '/');
    return "{$grafanaUrl}/d/{$dashboardUid}?orgId=1&from=now-5m&to=now&kiosk&auth={$apiToken}";
}

}
