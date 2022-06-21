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
            $plan_id = 'P-19T662956N2231909MKYV7WQ';
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
                    "start_time" => Carbon::now()->addMinutes(10)->toISOString(), // ISO8601 and must be future time
                    "application_context" => [
                        "brand_name" => "Skyrush",
                        "locale" => "en-US",
                        "shipping_preference" => "SET_PROVIDED_ADDRESS",
                        "user_action" => "SUBSCRIBE_NOW",
                        "payment_method" => [
                            "payer_selected" => "PAYPAL",
                            "payee_preferred" => "IMMEDIATE_PAYMENT_REQUIRED"
                        ],
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

    function temp()
    {
        $json = [
            "plan_id" => $plan_id,
            "auto_renewal" => true,
            "start_time" => Carbon::now()->addMinutes(10)->toISOString(),
            "quantity" => "1",
            "shipping_amount" => [
                "currency_code" => "USD",
                "value" => "10.00"
            ],
            "subscriber" => [
                "name" => [
                    "given_name" => "Mitesh",
                    "surname" => "Viras"
                ],
                "email_address" => "sb-6dmij14688129@personal.example.com",
                "shipping_address" => [
                    "name" => [
                        "full_name" => "Mitesh Viras"
                    ],
                    "address" => [
                        "address_line_1" => "2211 N First Street",
                        "address_line_2" => "Building 17",
                        "admin_area_2" => "San Jose",
                        "admin_area_1" => "CA",
                        "postal_code" => "95131",
                        "country_code" => "US"
                    ]
                ]
            ],
            "application_context" => [
                "brand_name" => "walmart",
                "locale" => "en-US",
                "shipping_preference" => "SET_PROVIDED_ADDRESS",
                "user_action" => "SUBSCRIBE_NOW",
                "payment_method" => [
                    "payer_selected" => "PAYPAL",
                    "payee_preferred" => "IMMEDIATE_PAYMENT_REQUIRED"
                ],
                "return_url" => config('paypal.return_url'),
                "cancel_url" => config('paypal.cancel_url'),
            ]
        ];
    }
}
