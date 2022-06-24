<?php

namespace App\Services\Paypal;

use GuzzleHttp\Client;
use Illuminate\Support\Str;
use App\Services\Paypal\Paypal;
use GuzzleHttp\Exception\ClientException;

class Subscription
{
    public $bearer_token, $client, $paypal, $paypalUrl;
    public function __construct()
    {
        $this->client = new Client();
        $this->paypal = new Paypal();
        $this->bearer_token = $this->paypal->getAccessToken();
        $this->paypalUrl = $this->paypal->getPaypalUrl();
    }

    public function show($subscription_id)
    {
        if (empty($subscription_id)) {
            return 'Subscription ID Is Required To View Its Details';
        }
        try {
            $url = $this->paypalUrl . '/v1/billing/subscriptions/' . $subscription_id;
            $res = $this->client->request('GET', $url, [
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

    public function createSubscription($plan_id, $data)
    {
        if (empty($plan_id)) {
            return 'Plan ID Is Required Without Plan Id You Cannot Create Subscription.';
        }
        if (!empty($data)) {
            $error = [];
            if (!isset($data['auto_renewal'])) {
                $error['error_auto_renewal'] = 'Plan Auto Renewal Is Required and It Must Be True Or False.';
            }
            if (!isset($data['brand_name'])) {
                $error['error_brand_name'] = 'Plan Brand Name Is Required,';
            }
            if (!isset($data['local'])) {
                $error['error_local'] = 'Plan Local Is Required, Check This Reference https://developer.paypal.com/api/nvp-soap/locale-codes/';
            }
            if (!empty($error)) {
                return $error;
            }
            try {
                $url = $this->paypalUrl . '/v1/billing/subscriptions';
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
                        "auto_renewal" => $data['auto_renewal'],
                        "application_context" => [
                            "brand_name" => $data['brand_name'],
                            "user_action" => "SUBSCRIBE_NOW",
                            "local" => $data['local'],
                            "return_url" => config('paypal.return_url'),
                            "cancel_url" => config('paypal.cancel_url'),
                        ],
                    ],
                ]);
                return json_decode($res->getBody()->getContents());
            } catch (ClientException $e) {
                print_r($e->getResponse()->getBody()->getContents());
            }
        }
    }

    public function updateSubscription($subscription_id, $data)
    {
        $error = [];
        $json_data = [];
        if (empty($subscription_id)) {
            return 'Subscription ID Is Required To View Its Details';
        }
        if (!empty($data)) {

            foreach ($data as $key => $value) {
                if (empty($value['op'])) {
                    $error[$key]['error_op'] = 'Plan Operation Is Required, Please Enter OP!!!';
                } else {
                    if (!in_array($value['op'], ['replace', 'remove', 'add'])) {
                        $error[$key]['error_op'] = 'Plan OP Must Be "replace", "remove" or "add"';
                    }
                }

                if (empty($value['path'])) {
                    $error[$key]['error_path'] = 'Plan Path Is Required, "path" Describe On Which Field You Want To Perform Operation.';
                } else {
                    if (!in_array($value['path'], ['/plan/billing_cycles/@sequence==1/pricing_scheme/fixed_price', '/plan/payment_preferences/payment_failure_threshold', '/plan/payment_preferences/auto_bill_outstanding', '/payment_preferences/payment_failure_threshold', '/plan/taxes/percentage'])) {
                        $error[$key]['error_path'] = 'Subscription Plan Update path Must Be "/plan/billing_cycles/@sequence==1/pricing_scheme/fixed_price", "/plan/payment_preferences/payment_failure_threshold", "/plan/payment_preferences/auto_bill_outstanding", "/payment_preferences/payment_failure_threshold", "/plan/taxes/percentage"';
                    }
                }

                if (empty($value['value'])) {
                    $error[$key]['error_value'] = 'Value Is Required, Please Enter Value To Be Update!!!';
                }

                if ($value['path'] == '/plan/billing_cycles/@sequence==1/pricing_scheme/fixed_price') {
                    $data[$key] = [
                        'op' => $value['op'],
                        'path' => $value['path'],
                        'value' => [
                            'currency_code' => $value['currency_code'],
                            'value' => $value['value']
                        ]
                    ];
                } else {
                    $data[$key] = [
                        'op' => $value['op'],
                        'path' => $value['path'],
                        'value' => $value['value']
                    ];
                }
            }

            if (!empty($error)) {
                return $error;
            }

            dd($json_data);

            try {
                $url = $this->paypalUrl . '/v1/billing/subscriptions/';
                $res = $this->client->request('PATCH', $url, [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Accept-Language' => 'en_US',
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $this->bearer_token,
                    ],
                    'json' => [$json_data]
                ]);
                return json_decode($res->getBody()->getContents());
            } catch (ClientException $e) {
                print_r($e->getResponse()->getBody()->getContents());
            }
        } else {
            return 'Date Cannot Be Null';
        }
    }
}
