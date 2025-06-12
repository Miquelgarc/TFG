<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FacturaDirectaClient
{
    private string $baseUrl = 'https://fdi.api.facturadirecta.com/v1/';
    private string $apiToken;

    public function __construct(string $apiToken = null)
    {
        $this->apiToken = $apiToken ?? config('services.facturadirecta.token');
    }

    private function request(string $method, string $endpoint, array $data = [])
    {
        $response = Http::withToken($this->apiToken)
            ->accept('application/json')
            ->baseUrl($this->baseUrl)
            ->{$method}($endpoint, $data);

        $response->throw(); // Lanza excepción si hay error
        return $response->json(); // Devuelve array asociativo
    }

    public function createClient(array $data)
    {
        return $this->request('post', 'clients', $data);
    }

    public function createInvoice(array $data)
    {
        return $this->request('post', 'invoices', $data);
    }
}
