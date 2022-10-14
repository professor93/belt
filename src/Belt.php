<?php

namespace Uzbek\Belt;

use Illuminate\Support\Facades\Http;

class Belt
{
    public function __construct(
        protected readonly string $base_url,
        protected readonly string $username,
        protected readonly string $password,
    ) {
    }

    private function sendRequest(string $method, string $url, array $data = [])
    {
        return Http::baseUrl($this->base_url)
            ->withBasicAuth($this->username, $this->password)->$method($url, $data)->json();
    }

    public function getAccounts(int $clientId)
    {
        return $this->sendRequest('get', "client/{$clientId}/accounts/active");
    }

    public function getCards(int $clientId, int $cardCode)
    {
        return $this->sendRequest('post', 'card/get-by-client-id-dgb', [
            'clientId' => $clientId,
            'cardCode' => $cardCode, /*(1 - uzcard, 2 - humo, 3 - visa)*/
        ]);
    }

    public function getCredits(int $clientId)
    {
        return $this->sendRequest('get', "loan/client-credit-list/{$clientId}");
    }
}
