<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use App\Http\Controllers\PayPalController;

class SubscriptionController extends Controller
{
    public $bearer_token;
    public $client;
    public $paypal;
    public function __construct()
    {
        $this->client = new Client();
        $this->paypal = new PayPalController();
        $this->bearer_token = $this->paypal->getAccessToken();
    }

    public function createSubscription()
    {
        try {
            $plan_id = ''; //replace with your
            $url = 'https://api-m.sandbox.paypal.com/v1/billing/subscriptions';
            $res = $this->client->request('POST', $url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en_US',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->bearer_token,
                    'PayPal-Request-Id' => 'SUBSCRIPTION-' . now()->timestamp . '-' . rand(0, 9999),
                ],
                'json' => [
                    "plan_id" => $plan_id,
                    "auto_renewal" => true,
                    "subscriber" => [
                        "user_id" => "329"
                    ],
                    "application_context" => [
                        "brand_name" => "SKYRUSH",
                        "user_action" => "SUBSCRIBE_NOW",
                        "local" => "en-US",
                        "return_url" => config('paypal.return_url'),
                        "cancel_url" => config('paypal.cancel_url'),
                    ],
                ],
            ]);
            $body = json_decode($res->getBody());
            dd($body);
        } catch (ClientException $e) {
            dd($e->getMessage());
            dd($e->getResponse()->getBody()->getContents());
        }
    }
}
