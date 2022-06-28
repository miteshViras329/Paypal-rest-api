<?php

namespace App\Services\Paypal;

use GuzzleHttp\Client;
use App\Services\Paypal\Paypal;
use GuzzleHttp\Exception\ClientException;

class Plan
{
    public $bearer_token, $client, $paypal, $paypalUrl;
    public function __construct()
    {
        $this->client = new Client();
        $this->paypal = new Paypal();
        $this->bearer_token = $this->paypal->getAccessToken();
        $this->paypalUrl = $this->paypal->getPaypalUrl();
    }

    public function showList($product_id, $page_size = 10, $current_page = 1, $total_records = 'true')
    {
        try {
            if (empty($product_id)) {
                return 'Product ID Is Required Without Product ID We Cannot Provide Its Plan List';
            }
            $url = $this->paypalUrl . '/v1/billing/plans?product_id=' . $product_id . '&page_size=' . $page_size . '&page=' . $current_page . '&total_required=' . $total_records;
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

    public function show($plan_id)
    {
        try {
            if (empty($plan_id)) {
                return 'Plan ID Is Required';
            }
            $url = $this->paypalUrl . 'v1/billing/plans/' . $plan_id;
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

    public function activate($plan_id)
    {
        // Note : This is a sample code for activating a plan and it never return a response. reffer paypal api doc for more details.
        try {
            if (empty($plan_id)) {
                return 'Plan ID Is Required';
            }
            $url = $this->paypalUrl . '/v1/billing/plans/' . $plan_id . '/activate';
            $this->client->request('post', $url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en_US',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->bearer_token,
                ],
            ]);

            return 'Plan Status Activate';
        } catch (ClientException $e) {
            print_r($e->getResponse()->getBody()->getContents());
        }
    }

    public function deActivate($plan_id)
    {
        // Note : This is a sample code for deactivating a plan and it never return a response. reffer paypal api doc for more details.
        try {
            if (empty($plan_id)) {
                return 'Plan ID Is Required';
            }
            $url = $this->paypalUrl . '/v1/billing/plans/' . $plan_id . '/deactivate';
            $res = $this->client->request('post', $url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en_US',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->bearer_token,
                ],
            ]);

            return 'Plan Status Inactivate';
        } catch (ClientException $e) {
            print_r($e->getResponse()->getBody()->getContents());
        }
    }

    public function createPlan($product_id, $data)
    {
        if (empty($product_id)) {
            return 'Project ID Is Required, Without Project ID You Cannot Create A Plan.';
        }
        if (!empty($data)) {
            $error = [];
            if (empty($data['name'])) {
                $error['error_name'] = 'Plan Name Is Required!';
            }
            if (empty($data['description'])) {
                $error['error_description'] = 'Plan Description Is Required!';
            }
            if (empty($data['interval_unit'])) {
                $error['error_interval_unit'] = 'Plan Interval Unit Is Required!';
            } else {
                if (!in_array($data['interval_unit'], ['DAY', 'MONTH', 'YEAR'])) {
                    $error['error_interval_unit'] = 'Plan Interval Unit Must Be DAY,MONTH or YEAR!';
                }
            }
            if (!isset($data['interval_count'])) {
                $error['error_interval_count'] = 'Plan Interval Count Is Required & It Means A Gap After The Plans End & It Must Start From 1 to Any Number You Want!';
            }
            if (!isset($data['total_cycles'])) {
                $error['error_total_cycles'] = 'Plan Total Cycles Is Required & Cycles Must Be 1 to 9999!';
            }
            if (!isset($data['price_value'])) {
                $error['error_price_value'] = 'Plan Price Value Is Required!';
            }
            if (empty($data['currency_code'])) {
                $error['error_currency_code'] = 'Plan Currency Code Is Required!';
            }
            if (!isset($data['setup_fee'])) {
                $error['error_setup_fee'] = 'Plan Setup Fee Is Required Or You Can Pass 0 If You Don`t Want To Charge From User.';
            }
            if (!empty($error)) {
                return $error;
            }
            try {
                $url = $this->paypalUrl . '/v1/billing/plans';
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
                        "name" => $data['name'],
                        "description" => $data['description'],
                        "status" => "ACTIVE",
                        "billing_cycles" => [
                            [
                                "frequency" => [
                                    "interval_unit" => $data['interval_unit'], //DAY,MONTH,YEAR
                                    "interval_count" =>  $data['interval_count'] // GAP After Unit
                                ],
                                "sequence" => "1",
                                "tenure_type" => "REGULAR",
                                "total_cycles" => $data['total_cycles'],
                                "pricing_scheme" => [
                                    "fixed_price" => [
                                        "value" => $data['price_value'],
                                        "currency_code" => $data['currency_code'] ?? 'USD'
                                    ]
                                ]
                            ]
                        ],
                        "payment_preferences" => [
                            "auto_bill_outstanding" => true,
                            "setup_fee" => [
                                "value" => $data['setup_fee'],
                                "currency_code" => $data['currency_code'] ?? 'USD'
                            ],
                            "setup_fee_failure_action" => "CONTINUE",
                            "payment_failure_threshold" => 2
                        ],
                    ]
                ]);
                return json_decode($res->getBody()->getContents());
            } catch (ClientException $e) {
                print_r($e->getResponse()->getBody()->getContents());
            }
        } else {
            return 'Data Cannot Be Null';
        }
    }

    public function updatePlan($plan_id, $data)
    {
        $error = [];
        if (empty($plan_id)) {
            return 'Plan ID Is Required, Without Plan ID You Cannot Update A Plan.';
        }
        if (!empty($data)) {
            if (empty($data['op'])) {
                $error['error_op'] = 'Plan Operation Is Required, Please Enter OP!!!';
            } else {
                if (!in_array($data['op'], ['replace', 'remove', 'add'])) {
                    $error['error_op'] = 'Plan OP Must Be "replace", "remove" or "add"';
                }
            }

            if (empty($data['path'])) {
                $error['error_path'] = 'Plan Path Is Required, "path" Describe On Which Field You Want To Perform Operation.';
            } else {
                if (!in_array($data['path'], ['/description', '/payment_preferences/auto_bill_outstanding', '/taxes/percentage', '/payment_preferences/payment_failure_threshold', '/payment_preferences/setup_fee', '/payment_preferences/setup_fee_failure_action'])) {
                    $error['error_path'] = 'Plan path Must Be "/description", "/payment_preferences/auto_bill_outstanding", "/taxes/percentage", "/payment_preferences/payment_failure_threshold", "/payment_preferences/setup_fee", "/payment_preferences/setup_fee_failure_action"';
                }
            }

            if (empty($data['value'])) {
                $error['error_value'] = 'Plan Value Is Required, Please Enter Plan Value!!!';
            }
        } else {
            return 'Date Cannot Be Null';
        }
        try {
            $url = $this->paypalUrl . '/v1/billing/plans/' . $plan_id;
            $this->client->request('PATCH', $url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en_US',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->bearer_token,
                ],
                'json' => [
                    [
                        "op" => $data['op'],
                        "path" => $data['path'],
                        "value" => $data['value']
                    ]
                ],
            ]);
            return 'Plan Updated Successfully';
        } catch (ClientException $e) {
            print_r('Exception', $e->getResponse()->getBody()->getContents());
        }
    }
}
