<?php

namespace Uzbek\Belt;

use Illuminate\Support\Facades\Http;

class Belt
{
    private function sendRequest(string $method, string $url, array $data = [])
    {
        $config = config('belt');

        return Http::baseUrl($config['base_url'])
            ->withBasicAuth($config['username'], $config['password'])->$method($url, $data)->json();
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

    public function getDepositList(int $clientId)
    {
        return $this->sendRequest('get', "client/deposits-list/{$clientId}");
    }
}
