<?php

namespace App\Http\Controllers;

use App\Models\Paypal;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redirect;
use GuzzleHttp\Exception\ClientException;

class PayPalController extends Controller
{
    private $credentials;
    private $bearer_token, $provider, $client;
    public function __construct()
    {
        if (config('paypal.mode') == 'live') {
            $this->credentials = [
                config('paypal.paypal_live_client_id'),
                config('paypal.paypal_live_secret'),
            ];
        } else {
            $this->credentials = [
                config('paypal.paypal_sandbox_client_id'),
                config('paypal.paypal_sandbox_secret'),
            ];
        }
        $this->client = new Client();
        $this->authentication();
    }

    private function authentication()
    {
        try {
            $res = $this->client->request('post', 'https://api-m.sandbox.paypal.com/v1/oauth2/token', [
                'auth' => $this->credentials,
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'form_params' => [
                    'grant_type' => 'client_credentials',
                ],
            ]);

            $body = json_decode($res->getBody()->getContents());
            $this->bearer_token = $body->access_token;
            $this->provider = $body;
        } catch (ClientException $e) {
            dd($e->getResponse()->getBody()->getContents());
        }
    }

    public function getAccessToken()
    {
        return $this->bearer_token;
    }

    public function getProvider()
    {
        return $this->provider;
    }

    public function webHook()
    {
        $webHook = request()->all();
        Paypal::create([
            'type' => request()->resource_type,
            'json' => serialize($webHook),
        ]);
    }
}
