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
            'headers' => [
                'Authorization' => 'Bearer ' . config('services.grafana.service_token'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }

    public function createUser($email, $username, $password)
    {
        $response = $this->client->post('/api/admin/users', [
            'json' => [
                'email' => $email,
                'login' => $username,
                'password' => $password,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    public function authenticateUser($username, $password)
    {
        // Implement the method to authenticate a user against Grafana's API using Basic Auth
    }

    public function getDashboard($dashboardId)
    {
        $response = $this->client->get("/api/dashboards/uid/{$dashboardId}");
        $data = json_decode($response->getBody(), true);

        return [
            'url' => config('services.grafana.url') . '/d/' . $data['dashboard']['uid'],
        ];
    }
}
