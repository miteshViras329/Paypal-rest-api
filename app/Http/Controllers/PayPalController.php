<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redirect;

class PayPalController extends Controller
{
    private $credentials, $bearer_token, $app_id;
    public $client;
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
        $res = $this->client->request('post', 'https://api-m.sandbox.paypal.com/v1/oauth2/token', [
            'auth' => $this->credentials,
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
            'form_params' => [
                'grant_type' => 'client_credentials',
            ],
        ]);

        $body = json_decode($res->getBody()->getContents());
        $this->bearer_token = $body->access_token;
        $this->app_id = $body->app_id;
    }

    public function store()
    {
        $data = request()->validate([
            'amount' => 'required|numeric',
            'name' => 'nullable|string',
        ]);
        $this->createOrder($data);
    }

    public function getAccessToken()
    {
        return $this->bearer_token;
    }

    public function createOrder($data)
    {
        $res = $this->client->request('post', 'https://api-m.sandbox.paypal.com/v2/checkout/orders/', [
            'headers' => [
                'Accept' => 'application/json',
                'Accept-Language' => 'en_US',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->bearer_token,
            ],
            'json' => [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    "reference_id" => Str::random(4) . '_' . Str::random(6) . '_' . rand(99, 9999),
                    'amount' => [
                        'value' => $data['amount'],
                        'currency_code' => 'USD',
                    ],
                ]],
            ], "application_context" => [
                "cancel_url" => config('paypal.cancle_url'),
                "return_url" => config('paypal.return_url'),
                "brand_name" => 'Iphone 11',
            ],
        ]);

        $order = json_decode($res->getBody()->getContents());
        // dd($order);
        // return redirect()->to('https://www.sandbox.paypal.com/checkoutnow?token=' . $order->id);
        return redirect()->to($order->links[1]->href)->send();
    }
}
