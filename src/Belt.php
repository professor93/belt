<?php

namespace Uzbek\Belt;

use Illuminate\Support\Facades\Http;

class Belt
{
    /**
     * @param  int  $clientId
     * @return mixed
     */
    public function getAccounts(int $clientId)
    {
        return Http::belt('get', "client/{$clientId}/accounts/active");
    }

    /**
     * @param  int  $clientId
     * @param  int  $cardCode (1 - Узкарт, 2 - Хумо, 3 - Виза)
     * @return mixed
     */
    public function getCards(int $clientId, int $cardCode)
    {
        return Http::belt('post', 'card/get-by-client-id-dgb', [
            'clientId' => $clientId,
            'cardCode' => $cardCode,
        ]);
    }

    /**
     * @param $clientId
     * @return mixed
     */
    public function getCredits($clientId)
    {
        return Http::belt('get', "loan/client-credit-list/{$clientId}");
    }
}
