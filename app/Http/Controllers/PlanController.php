<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Message;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\ClientException;
use App\Http\Controllers\PayPalController;

class PlanController extends Controller
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

    public function showList()
    {
        try {
            $page_size = 10;
            $page = 1;
            $total = 'true';
            $product_id = 'PROD-3DN36045AB844170W'; //replace with yours
            $url = 'https://api-m.sandbox.paypal.com/v1/billing/plans?product_id=' . $product_id . '&page_size=' . $page_size . '&page=' . $page . '&total_required=' . $total;
            $res = $this->client->request('get', $url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en_US',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->bearer_token,
                ],
            ]);

            $body = json_decode($res->getBody()->getContents());
            dd('Plan List', $body);
        } catch (ClientException $e) {
            dd($e->getResponse()->getBody()->getContents());
        }
    }

    public function show()
    {
        try {
            $plan_id = 'P-19T662956N2231909MKYV7WQ'; //replace with yours
            $url = 'https://api-m.sandbox.paypal.com/v1/billing/plans/' . $plan_id;
            $res = $this->client->request('get', $url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en_US',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->bearer_token,
                ],
            ]);
            $body = json_decode($res->getBody()->getContents());
            dd('Plan Detail', $body);
        } catch (ClientException $e) {
            dd($e->getResponse()->getBody()->getContents());
        }
    }

    public function activate()
    {
        // Note : This is a sample code for activating a plan and it never return a response. reffer paypal api doc for more details.
        try {
            $plan_id = 'P-80E107653T4828148MKYBNII'; //replace with yours
            $url = 'https://api-m.sandbox.paypal.com/v1/billing/plans/' . $plan_id . '/activate';
            $res = $this->client->request('post', $url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en_US',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->bearer_token,
                ],
            ]);

            $body = json_decode($res->getBody()->getContents());
            dd('Plan Status Activate');
        } catch (ClientException $e) {
            dd($e->getResponse()->getBody()->getContents());
        }
    }

    public function deActivate()
    {
        // Note : This is a sample code for deactivating a plan and it never return a response. reffer paypal api doc for more details.
        try {
            $plan_id = 'P-80E107653T4828148MKYBNII'; //replace with yours
            $url = 'https://api-m.sandbox.paypal.com/v1/billing/plans/' . $plan_id . '/deactivate';
            $res = $this->client->request('post', $url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en_US',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->bearer_token,
                ],
            ]);

            $body = json_decode($res->getBody()->getContents());
            dd('Plan Status Inactivate');
        } catch (ClientException $e) {
            dd($e->getResponse()->getBody()->getContents());
        }
    }

    public function createPlan()
    {
        try {
            $product_id = 'PROD-8MP67799P42213056'; //replace with yours
            $url = 'https://api-m.sandbox.paypal.com/v1/billing/plans';
            $res = $this->client->request('post', $url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en_US',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->bearer_token,
                    'PayPal-Request-Id' => 'PLAN-' . now()->timestamp . '-' . rand(1, 9999),
                ],
                'json' => [
                    "product_id" => $product_id,
                    "name" => "Skyrush Daily",
                    "description" => "Boost your audience with a Giveaway",
                    "status" => "ACTIVE",
                    "billing_cycles" => [
                        [
                            "frequency" => [
                                "interval_unit" => "DAY", //DAY,MONTH,YEAR
                                "interval_count" => 1 // GAP After Unit
                            ],
                            "sequence" => "1",
                            "tenure_type" => "REGULAR",
                            "total_cycles" => 12,
                            "pricing_scheme" => [
                                "fixed_price" => [
                                    "value" => "10",
                                    "currency_code" => "USD"
                                ]
                            ]
                        ]
                    ],
                    "payment_preferences" => [
                        "auto_bill_outstanding" => true,
                        "setup_fee" => [
                            "value" => "0",
                            "currency_code" => "USD"
                        ],
                        "setup_fee_failure_action" => "CONTINUE",
                        "payment_failure_threshold" => 1
                    ],
                ]
            ]);

            $body = json_decode($res->getBody()->getContents());
            dd('Plan Create', $body);
        } catch (ClientException $e) {
            dd($e->getResponse()->getBody()->getContents());
        }
    }

    public function updatePlan()
    {
        try {
            $plan_id = 'P-07031543TH802742PMKYCCZQ'; //replace with yours
            $url = 'https://api-m.sandbox.paypal.com/v1/billing/plans/' . $plan_id;
            $res = $this->client->request('PATCH', $url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en_US',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->bearer_token,
                ],
                'json' => [
                    [
                        "op" => "replace",
                        "path" => "/payment_preferences/payment_failure_threshold",
                        "value" => 15
                    ]
                ],
            ]);
            $body = json_decode($res->getBody()->getContents());
            dd('Plan Update', $body);
        } catch (ClientException $e) {
            dd($e->getResponse());
            dd('Exception', $e->getResponse()->getBody()->getContents());
        }
    }
}
