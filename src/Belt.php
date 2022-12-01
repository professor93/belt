<?php

namespace Uzbek\Belt;

use Illuminate\Support\Facades\Http;
use Uzbek\Belt\Exceptions\CustomerNotFound;
use Uzbek\Belt\Exceptions\InvalidParameters;
use Uzbek\Belt\Exceptions\NotFound;

class Belt
{
    private function sendRequest(string $method, string $url, array|null $data = null)
    {
        $data ??= [];
        $config = config('belt');

        return Http::baseUrl($config['base_url'])
            ->withBasicAuth($config['username'], $config['password'])->$method($url, $data)
            ->throw(static function ($response, $e) {
                if ($response->status() === 400 && $response->json()['code'] === 3) {
                    throw new CustomerNotFound('Customer not found');
                }
                if ($response->status() === 400 && $response->json()['code'] === 0) {
                    throw new InvalidParameters('Invalid parameters');
                }
                if ($response->status() === 400 && $response['code'] === 1) {
                    throw new NotFound('Not found');
                }
                throw $e;
            })->json();
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

    public function getClientCreditList(int $clientId)
    {
        $request = $this->sendRequest('get', "loan/client-credit-list/{$clientId}");

        if (isset($request['data']) && $request['data']) {
            return $request['data'];
        }

        return [];
    }

    public function getLoanCreditProducts(int $creditType = 2)
    {
        $request = $this->sendRequest('get', "loan/credit-products/{$creditType}");
        /*1-Juridik, 2-Fizik*/
        if (isset($request['data']) && $request['data']) {
            return $request['data'];
        }

        return false;
    }

    public function getLoanCreditProductById(int $productId)
    {
        $request = $this->sendRequest('get', "loan/credit-product-by-id/{$productId}");

        if (isset($request['data']) && $request['data']) {
            return $request['data'];
        }

        return false;
    }

    public function getDepositActiveList()
    {
        $request = $this->sendRequest('get', 'deposit/active-list');

        if (isset($request['data']) && $request['data']) {
            return $request['data'];
        }

        return false;
    }

    /**
     * @throws CustomerNotFound
     */
    public function getClientDepositList(int $clientId)
    {
        $request = $this->sendRequest('get', "client/deposits-list/{$clientId}");

        if (isset($request['code'], $request['status']) && $request['code'] === 2 && $request['status'] === 400) {
            throw new CustomerNotFound();
        }

        if (isset($request['code'], $request['status']) && $request['code'] === 1 && $request['status'] === 400) {
            /*Deposit not found*/
            throw new CustomerNotFound();
        }

        return $request['data'];
    }

    public function getClientDepositBySavDepId(int $savDepId)
    {
        $request = $this->sendRequest('post', 'client/deposits-by-sav-dep-id', [
            'savDepId' => $savDepId,
        ]);

        return $request;
    }

    public function getClientDepositPaymentSchedule(int $savDepId)
    {
        $request = $this->sendRequest('post', 'deposit/payment-schedule', [
            'savDepId' => $savDepId,
        ]);

        if (isset($request['data']) && $request['data']) {
            return $request['data'];
        }

        return false;
    }

    public function depositCalculator(string $depId, string $amount)
    {
        $request = $this->sendRequest('post', 'deposit/calculator', [
            "depId" => $depId,
            "amount" => $amount
        ]);

        if (isset($request['data']) && $request['data']) {
            return $request['data'];
        }

        return $request;
    }

    public function getAccountTurnoverForLoan(string $account, string $codeFilial, int $pageNumber, int $pageSize, int $type, string $dateBegin, string $dateClose)
    {
        $request = $this->sendRequest('post', 'account/turnover-for-loan', [
            'account' => $account,
            'codeFilial' => $codeFilial,
            'pageNumber' => $pageNumber ?? 1,
            'pageSize' => $pageSize ?? 15,
            'type' => $type ?? 3,
            'dateBegin' => $dateBegin,
            'dateClose' => $dateClose,
        ]);

        if (isset($request['response']) && $request['response']) {
            return $request['response'];
        }

        return [];
    }

    public function getExchangeRates($date = null, $currency = null)
    {
        $request = $this->sendRequest('post', 'international-card/get-list-exchange-rates', [
            'dateCross' => $date ?? date('Y.m.d'),
            'currencyCode' => $currency ?? 'ALL',
        ]);

        if (isset($request['code'], $request['responseBody']['data']) && $request['code'] === 0 && $request['responseBody']['data']) {
            return $request['responseBody']['data'];
        }

        return false;
    }

    public function getCustomerByPinfl(string $pinfl)
    {
        $request = $this->sendRequest('post', 'customer/by-pinfl', [
            'pinfl' => $pinfl,
        ]);

        if (isset($request['code'], $request['status']) && $request['code'] === 0 && $request['status'] === 404) {
            return false;
        }

        if (isset($request['code'], $request['responseBody'], $request['responseBody']['response']) && $request['code'] === 0 && $request['responseBody'] && $request['responseBody']['response']) {
            return $request['responseBody']['response'];
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

    public function createCustomer(array $params)
    {
        /*  'inn' => $inn,
            'pinfl' => $pinfl,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'middleName' => $middleName,
            'birthDate' => $birthDate,
            'birthPlace' => $birthPlace,
            'birthCountry' => $birthCountry,
            'gender' => $gender,
            'citizenship' => $citizenship,
            'docType' => $docType,
            'series' => $series,
            'number' => $number,
            'docIssueDate' => $docIssueDate,
            'docExpireDate' => $docExpireDate,
            'docIssuePlace' => $docIssuePlace,
            'residenceCountry' => $residenceCountry,
            'codeFilial' => $codeFilial,
            'residenceRegion' => $residenceRegion,
            'residenceDistrict' => $residenceDistrict,
            'residenceAddress' => $residenceAddress,
            'phone' => $phone,
            'mobilePhone' => $mobilePhone,
            'email' => $email,
            'maritalStatus' => $maritalStatus,*/
        $request = $this->sendRequest('post', 'customer/create', $params);

        if (isset($request['clientId'], $request['clientCode'])) {
            return $request;
        }

        return false;
    }

    public function openDeposit(
        int    $depId,
        int    $clientId,
        string $codeFilial,
        string $date,
        string $amount,
        string $account,
        string $codeFilial2,
        string $isInfoOwner,
        int    $depType,
        string $questionnaire,
        string $cardNumber
    )
    {
        $request = $this->sendRequest('post', 'deposit/open', [
            'depId' => $depId,
            'clientId' => $clientId,
            'codeFilial' => $codeFilial,
            'date' => $date,
            'amount' => $amount,
            'account' => $account,
            'codeFilial2' => $codeFilial2,
            'isInfoOwner' => $isInfoOwner,
            'depType' => $depType,
            'questionnaire' => $questionnaire,
            'cardNumber' => $cardNumber,
        ]);

        if (isset($request['code'], $request['responseBody']) && $request['code'] === 0 && $request['responseBody']) {
            return $request['responseBody'];
        }

        return false;
    }

    public function paynet(): Paynet
    {
        return new Paynet();
    }
}
