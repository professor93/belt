<?php

namespace Uzbek\Belt;

use Illuminate\Support\Facades\Http;
use Uzbek\Belt\Dtos\Customer;

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

    public function getCustomerByPinfl(string $pinfl)
    {
        $response = $this->sendRequest('get', "customer/by-pinfl/{$pinfl}");

        if ($response['code'] === 0 && $response['responseBody'] && $response['responseBody']['response']) {
            return $response['responseBody']['response'];
        }
        return false;
    }

    public function getCustomerByCardNumber(string $cardNumber, int $cardCode)
    {
        return $this->sendRequest('post', 'card/get-info', [
            'cardNumber' => $cardNumber,
            'cardCode' => $cardCode, /*(1 - uzcard, 2 - humo, 3 - visa)*/
        ]);
    }

    public function createCustomer(Customer $dto)
    {
        $request = $this->sendRequest('post', 'customer/create', $dto->toArray());

        if (isset($request['clientId'], $request['clientCode'])) {
            return $request;
        }
        return false;
    }
}
