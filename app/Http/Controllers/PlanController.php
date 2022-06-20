<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
            $product_id = 'PROD-3DN36045AB844170W';
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
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function show()
    {
        try {
            $plan_id = 'P-80E107653T4828148MKYBNII';
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
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function activate()
    {
        // Note : This is a sample code for activating a plan and it never return a response. reffer paypal api doc for more details.
        try {
            $plan_id = 'P-80E107653T4828148MKYBNII';
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
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function deActivate()
    {
        // Note : This is a sample code for deactivating a plan and it never return a response. reffer paypal api doc for more details.
        try {
            $plan_id = 'P-80E107653T4828148MKYBNII';
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
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function createPlan()
    {

        $product_id = 'PROD-3DN36045AB844170W';
        $url = 'https://api-m.sandbox.paypal.com/v1/billing/plans';
        $res = $this->client->request('post', $url, [
            'headers' => [
                'Accept' => 'application/json',
                'Accept-Language' => 'en_US',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->bearer_token,
                'PayPal-Request-Id' => 'PLAN-' . now()->timestamp . '-' . rand(1, 9999),
            ], [
                "product_id" => $product_id,
                "name" => "Video Streaming Service Plan",
                "description" => "Video Streaming Service basic plan",
                "status" => "ACTIVE",
                "billing_cycles" => [
                    [
                        "frequency" => [
                            "interval_unit" => "MONTH",
                            "interval_count" => 1
                        ],
                        "tenure_type" => "TRIAL",
                        "sequence" => 1,
                        "total_cycles" => 2,
                        "pricing_scheme" => [
                            "fixed_price" => [
                                "value" => "3",
                                "currency_code" => "USD"
                            ]
                        ]
                    ],
                    [
                        "frequency" => [
                            "interval_unit" => "MONTH",
                            "interval_count" => 1
                        ],
                        "tenure_type" => "TRIAL",
                        "sequence" => 2,
                        "total_cycles" => 3,
                        "pricing_scheme" => [
                            "fixed_price" => [
                                "value" => "6",
                                "currency_code" => "USD"
                            ]
                        ]
                    ],
                    [
                        "frequency" => [
                            "interval_unit" => "MONTH",
                            "interval_count" => 1
                        ],
                        "tenure_type" => "REGULAR",
                        "sequence" => 3,
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
                        "value" => "10",
                        "currency_code" => "USD"
                    ],
                    "setup_fee_failure_action" => "CONTINUE",
                    "payment_failure_threshold" => 3
                ],
                "taxes" => [
                    "percentage" => "0",
                    "inclusive" => false
                ]
            ]
        ]);

        $body = json_decode($res->getBody()->getContents());
        dd('Plan Create', $body);
    }
}
