<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FacturaDirectaClient
{
    protected string $apiToken;
    protected string $subdomain;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiToken = config('services.facturadirecta.token');
        $this->subdomain = config('services.facturadirecta.subdomain');
        $this->baseUrl = "https://{$this->subdomain}.facturadirecta.com/api/v1";
    }

    protected function request(string $method, string $endpoint, array $data = [])
    {
        $response = Http::withToken($this->apiToken)
            ->acceptJson()
            ->$method("{$this->baseUrl}/{$endpoint}", $data);

        if (!$response->successful()) {
            throw new \Exception('FacturaDirecta error: ' . $response->body());
        }

        return $response->json();
    }

    public function createClient(array $data)
    {
        return $this->request('post', 'clients', ['client' => $data]);
    }

    public function createInvoice(array $data)
    {
        return $this->request('post', 'invoices', ['invoice' => $data]);
    }
}
