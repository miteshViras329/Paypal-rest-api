<?php

namespace App\Services\Paypal;

use Carbon\Carbon;
use GuzzleHttp\Client;
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
                    $error[$key]['error_op'] = 'Subscription Operation Is Required, Please Enter OP!!!';
                } else {
                    if (!in_array($value['op'], ['replace', 'remove', 'add'])) {
                        $error[$key]['error_op'] = 'Subscription OP Must Be "replace", "remove" or "add"';
                    }
                }

                if (empty($value['path'])) {
                    $error[$key]['error_path'] = 'Subscription Path Is Required, "path" Describe On Which Field You Want To Perform Operation.';
                } else {
                    if (!in_array($value['path'], ['/plan/billing_cycles/@sequence==1/pricing_scheme/fixed_price', '/plan/payment_preferences/payment_failure_threshold', '/plan/payment_preferences/auto_bill_outstanding', '/payment_preferences/payment_failure_threshold', '/plan/taxes/percentage'])) {
                        $error[$key]['error_path'] = 'Please Refer Paypal Developers Doc https://developer.paypal.com/docs/api/subscriptions/v1/#subscriptions_patch and Subscription Plan Update path Must Be "/plan/billing_cycles/@sequence==1/pricing_scheme/fixed_price", "/plan/payment_preferences/payment_failure_threshold", "/plan/payment_preferences/auto_bill_outstanding", "/payment_preferences/payment_failure_threshold", "/plan/taxes/percentage"';
                    }
                }

                if (!isset($value['value'])) {
                    $error[$key]['error_value'] = 'Value Is Required, Please Enter Value To Be Update!!!';
                }

                if ($value['path'] == '/plan/billing_cycles/@sequence==1/pricing_scheme/fixed_price') {
                    $json_data[$key] = [
                        'op' => $value['op'],
                        'path' => $value['path'],
                        'value' => [
                            'currency_code' => $value['currency_code'],
                            'value' => $value['value']
                        ]
                    ];
                } else {
                    $json_data[$key] = [
                        'op' => $value['op'],
                        'path' => $value['path'],
                        'value' => $value['value']
                    ];
                }
            }

            if (!empty($error)) {
                return $error;
            }
            try {
                $url = $this->paypalUrl . '/v1/billing/subscriptions/';
                $this->client->request('PATCH', $url, [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Accept-Language' => 'en_US',
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $this->bearer_token,
                    ],
                    'json' => $json_data
                ]);
                return 'Subscription Updated!';
            } catch (ClientException $e) {
                print_r($e->getResponse());
            }
        } else {
            return 'Date Cannot Be Null';
        }
    }

    public function getTransactionList($subscription_id, $data)
    {
        $pattern = '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])[T,t]([0-1][0-9]|2[0-3]):[0-5][0-9]:([0-5][0-9]|60)([.][0-9]+)?([Zz]|[+-][0-9]{2}:[0-9]{2})$/';
        if (empty($subscription_id)) {
            return 'Subscription ID Is Required.';
        }
        if (!empty($data)) {
            if (!empty($data['start_date_time'])) {
                $data['start_date_time'] = Carbon::create($data['start_date_time'])->toIso8601ZuluString('millisecond');
                if (!preg_match($pattern, $data['start_date_time'])) {
                    return 'Start Date Time Format is Invalid, ZULU Zone Designator with Millisecond Is Required';
                }
            } else {
                return 'Start Date Time Is Required!';
            }
            if (!empty($data['end_date_time'])) {
                $data['end_date_time'] = Carbon::create($data['end_date_time'])->toIso8601ZuluString('millisecond');
                if (!preg_match($pattern, $data['end_date_time'])) {
                    return 'End Date Time Format is Invalid, ZULU Zone Designator with Millisecond Is Required';
                }
            } else {
                return 'End Date Time Is Required!';
            }
            try {
                $url = $this->paypalUrl . '/v1/billing/subscriptions/' . $subscription_id . '/transactions?start_time=' . $data['start_date_time'] . '&end_time=' . $data['end_date_time'];
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
                print_r($e->getResponse());
            }
        } else {
            return 'Data Is Required, Data Must Be Array Which Contains start_date and end_date';
        }
    }

    public function activateSubscription($subscription_id)
    {
        if (empty($subscription_id)) {
            return 'Subscription ID Is Required.';
        }

        try {
            $url = $this->paypalUrl . '/v1/billing/subscriptions/' . $subscription_id . '/activate';
            $this->client->request('POST', $url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en_US',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->bearer_token,
                ],
            ]);
            return 'Subscription Activated Successfully';
        } catch (ClientException $e) {
            print_r($e->getResponse());
        }
    }

    public function cancelSubscription($subscription_id)
    {
        if (empty($subscription_id)) {
            return 'Subscription ID Is Required.';
        }

        try {
            $url = $this->paypalUrl . '/v1/billing/subscriptions/' . $subscription_id . '/cancel';
            $this->client->request('POST', $url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en_US',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->bearer_token,
                ],
            ]);
            return 'Subscription Canceled Successfully';
        } catch (ClientException $e) {
            print_r($e->getResponse());
        }
    }

    public function suspendSubscription($subscription_id)
    {
        if (empty($subscription_id)) {
            return 'Subscription ID Is Required.';
        }

        try {
            $url = $this->paypalUrl . '/v1/billing/subscriptions/' . $subscription_id . '/suspend';
            $this->client->request('POST', $url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en_US',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->bearer_token,
                ],
            ]);
            return 'Subscription Suspended Successfully';
        } catch (ClientException $e) {
            print_r($e->getResponse());
        }
    }
}
