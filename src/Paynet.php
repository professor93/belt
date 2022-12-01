<?php
/**
 * Created by Sodikmirzo.
 * User: Sodikmirzo Sattorov ( https://github.com/Sodiqmirzo )
 * Date: 11/30/2022
 * Time: 5:31 PM
 */

namespace Uzbek\Belt;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Ramsey\Uuid\Uuid;

class Paynet
{
    protected mixed $config;

    protected string $username;

    protected string $password;

    protected string $terminalId;

    protected string|null $token;

    protected PendingRequest $client;

    protected string|null $last_uid;

    protected int $tokenLifeTime;

    public function __construct()
    {
        $this->config = config('paynet');
        $this->username = $this->config['username'];
        $this->password = $this->config['password'];
        $this->terminalId = $this->config['terminal_id'];
        $this->tokenLifeTime = $this->config['token_life_time'] ?? 60 * 60 * 24;

        $proxy_url = $this->config['proxy_url'] ?? (($this->config['proxy_proto'] ?? '').'://'.($this->config['proxy_host'] ?? '').':'.($this->config['proxy_port'] ?? '')) ?? '';
        $options = is_string($proxy_url) && str_contains($proxy_url, '://') && strlen($proxy_url) > 12 ? ['proxy' => $proxy_url] : [];

        $this->client = Http::baseUrl($this->config['base_url'])->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->withOptions($options);

        $this->login();
    }

    public function login(): void
    {
        $this->token = cache()->remember(
            'paynet_token',
            $this->tokenLifeTime,
            fn () => $this->sendRequest('login', [
                'username' => $this->username,
                'password' => $this->password,
            ])['result']['token']
        );
    }

    public function sendRequest($method, $params = [])
    {
        $url = '/gw/transaction';

        $uid = $this->generateUuid();

        $res = $this->client->post($url, [
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $params,
            'id' => $uid,
            'token' => $this->token,
        ])->throw(fn ($r, $e) => self::catchHttpRequestError($r, $e))->json();

        return $res;
    }

    private function generateUuid(): string
    {
        $this->last_uid = Uuid::uuid4();

        return $this->last_uid;
    }

    private static function catchHttpRequestError($res, $e)
    {
        /*if ($res['error']['code'] === -9999) {
            return new Unauthorized();
        }*/
        /*if ($res['error']['code'] === -9999) {
            return new Unauthorized();
        }
        if ($res['error']['code'] === -10000) {
            return new UserInvalid();
        }
        if ($res['error']['code'] === -12007) {
            return new TransactionNotFound();
        }*/
        throw $e;
    }

    public function detailedReportByPeriod(string $start_date, string $end_date)
    {
        $data = $this->sendRequest('summaryReportByDate', compact('start_date', 'end_date'));

        return $data;
    }
}
