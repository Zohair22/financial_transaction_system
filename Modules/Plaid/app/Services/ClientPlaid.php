<?php

namespace Modules\Plaid\Services;

use GuzzleHttp\Client;
use Modules\Plaid\Interfaces\IClientPlaid;

class ClientPlaid implements IClientPlaid
{
    protected Client $client;

    protected $client_id;

    protected $client_secret;

    public function __construct()
    {
        $this->client_id = config('plaid.client_id');
        $this->client_secret = config('plaid.secret');
        $this->client = new Client([
            'base_uri' => $this->getBaseUri(),
        ]);
    }

    protected function getBaseUri(): string
    {
        return 'https://sandbox.plaid.com/';
    }

    public function postRequest(string $endpoint, array $data): ?array
    {
        $response = $this->client->post($endpoint, [
            'json' => array_merge($data, [
                'client_id' => $this->client_id,
                'secret' => $this->client_secret,
            ]),
        ]);

        $decoded = json_decode($response->getBody()->getContents(), true);

        return is_array($decoded) ? $decoded : null;
    }
}
