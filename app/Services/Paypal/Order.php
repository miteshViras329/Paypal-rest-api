<?php

namespace App\Services\Paypal;

use GuzzleHttp\Client;
use Illuminate\Support\Str;
use App\Services\Paypal\Paypal;
use GuzzleHttp\Exception\ClientException;

class Order
{
    public $bearer_token, $client, $paypal, $paypalUrl;
    public function __construct()
    {
        $this->client = new Client();
        $this->paypal = new Paypal();
        $this->bearer_token = $this->paypal->getAccessToken();
        $this->paypalUrl = $this->paypal->getPaypalUrl();
    }

    public function createOrder($data)
    {
        try {
            $data['currency_code'] = !empty($data['currency_code']) ? $data['currency_code'] : 'USD'; // if not setted by default is USD
            $data['brand_name'] = !empty($data['brand_name']) ? $data['brand_name'] : 'Test Store'; // if not setted by default is Test Store
            $url = $this->paypalUrl . '/v2/checkout/orders/';
            $res = $this->client->request('post', $url, [
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
                            'currency_code' => $data['currency_code']
                        ],
                    ]],
                    "application_context" => [
                        "cancel_url" => config('paypal.cancle_url'),
                        "return_url" => config('paypal.return_url'),
                        "brand_name" => $data['brand_name'], // Your Brand Name
                    ],
                ]
            ]);

            return json_decode($res->getBody()->getContents());
        } catch (ClientException $e) {
            print_r($e->getResponse()->getBody()->getContents());
        }
    }

    public function executeOrder($order_id)
    {
        try {
            if (empty($order_id)) {
                return 'Order ID Is Not Setted, Please Enter Order Id';
            }

            $url = $this->paypalUrl . '/v2/checkout/orders/' . $order_id . '/capture';
            $res = $this->client->request('post', $url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en_US',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->bearer_token,
                ],
            ]);
            return json_decode($res->getBody()->getContents());
        } catch (ClientException $e) {
            print_r($e->getResponse()->getBody()->getContents());
        }
    }

    public function checkOrder($order_id)
    {
        try {
            if (empty($order_id)) {
                return 'Order ID Is Not Setted, Please Enter Order Id';
            }

            $url = $this->paypalUrl . '/v2/checkout/orders/' . $order_id;
            $res = $this->client->request('get', $url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en_US',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->bearer_token,
                ],
            ]);

            return json_decode($res->getBody()->getContents());
        } catch (ClientException $e) {
            print_r($e->getResponse()->getBody()->getContents());
        }
    }
}
