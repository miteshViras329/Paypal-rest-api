<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\ClientException;

class CheckoutController extends Controller
{
    public $bearer_token, $client, $paypal;
    public function __construct()
    {
        $this->client = new Client();
        $this->paypal = new PayPalController();
        $this->bearer_token = $this->paypal->getAccessToken();
    }

    public function store()
    {
        $data = request()->validate([
            'amount' => 'required|numeric',
            'name' => 'nullable|string',
        ]);
        $this->createOrder($data);
    }

    public function createOrder($data)
    {
        try {
            $url = 'https://api-m.sandbox.paypal.com/v2/checkout/orders/';
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
                            'currency_code' => 'USD',
                        ],
                    ]],
                    "application_context" => [
                        "cancel_url" => config('paypal.cancle_url'),
                        "return_url" => config('paypal.return_url'),
                        "brand_name" => 'Skyrush.io', // Your Brand Name
                    ],
                ]
            ]);

            $order = json_decode($res->getBody()->getContents());
            return redirect()->to($order->links[1]->href)->send(); // direct redirect to payment page
        } catch (ClientException $e) {
            dd($e->getResponse()->getBody()->getContents());
        }
    }

    public function executeOrder()
    {
        try {
            $url = 'https://api-m.sandbox.paypal.com/v2/checkout/orders/' . request()->token . '/capture';
            $res = $this->client->request('post', $url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en_US',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->bearer_token,
                ],
            ]);

            $order = json_decode($res->getBody()->getContents());
            dd($order);
        } catch (ClientException $e) {
            dd($e->getResponse()->getBody()->getContents());
        }
    }

    public function checkOrder()
    {
        try {
            $url = 'https://api-m.sandbox.paypal.com/v2/checkout/orders/' . request()->token;
            $res = $this->client->request('get', $url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en_US',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->bearer_token,
                ],
            ]);

            $order = json_decode($res->getBody()->getContents());
            dd($order);
        } catch (ClientException $e) {
            dd($e->getResponse()->getBody()->getContents());
        }
    }

    public function cancel()
    {
        dd('Sorry You Payment Has Been Cancled By User');
        return redirect()->route('/');
    }
}
