<?php

namespace App\Http\Controllers;

use App\Services\Paypal\Order;
use App\Services\Paypal\Paypal;
use App\Services\Paypal\Plan;
use App\Services\Paypal\Product;
use App\Services\Paypal\Subscription;
use Carbon\Carbon;

class ServicesController extends Controller
{
    public function index()
    {
        $paypal = new Paypal();
        // $paypalOrder = new Order();
        // $paypalProduct = new Product();
        // $paypalPlan = new Plan();
        $paypalSubscription = new Subscription();
        $data = [
            // 'amount' => 500,
            // 'currency' => 'USD',
            // 'name' => 'Skyrush Ultimate',
            // 'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry',
            // 'type' => 'service',
            // 'category' => 'software',
            // 'image_url' => 'https://skyrush.io/skyrush-html-composer/img/favicon/favicon-96x96.png',
            // 'home_url' => 'https://skyr  ush.io'
            // 'op' => 'replace',
            // 'path' => '/description',
            // 'value' => 'Why do we use it?'

            // 'name' => 'Scraper329',
            // 'description' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.',
            // 'interval_unit' => 'DAY',
            // 'interval_count' => 0,
            // 'total_cycles' => 12,
            // 'price_value' => 50,
            // 'currency_code' => 'USD',
            // 'setup_fee' => '0'
            // 'name' => 'Scraper329',
            // 'description' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.',
            // 'interval_unit' => 'DAY',
            // 'interval_count' => 1,
            // 'total_cycles' => 12,
            // 'price_value' => 50,
            // 'currency_code' => 'USD',
            // 'setup_fee' => 0
            // 'brand_name' => 'Skyrush V2',
            // 'auto_renewal' => true,
            // 'local' => 'en-US'

            // [
            //     'op' => 'replace',
            //     'path' => '/plan/payment_preferences/auto_bill_outstanding',
            //     'value' => true
            // ],
            // [
            //     'op' => 'replace',
            //     'path' => '/plan/billing_cycles/@sequence==1/pricing_scheme/fixed_price',
            //     'value' => '500',
            //     'currency_code' => 'USD'
            // ]

            // 'start_date_time' => '2022-06-20 22:04:59',
            // 'end_date_time' => '2020-06-28 14:09:59',
        ];
        // dd($paypal->getAccessToken());
        // dd($paypalPlan->createPlan('PROD-3DN36045AB844170W', $data));
        // dd($paypalProduct->showList());
        // dd($paypalProduct->show('PROD-3DN36045AB844170W'));
        // dd($paypalProduct->createProduct($data));
        // dd($paypalProduct->updateProduct('PROD-3DN36045AB844170W', $data));
        // dd($paypalSubscription->createSubscription('P-3D018928N97379438MK2YHEI', $data));
        // dd($paypalSubscription->show('I-W81GSLF3FPJL'));
        // dd($paypalSubscription->executeSubscription('I-W81GSLF3FPJL'));
        // dd($paypalSubscription->updateSubscription('I-W81GSLF3FPJL', $data)); //pending
        // dd($paypalSubscription->getTransactionList('I-W81GSLF3FPJL', $data)); //pending
        // dd($paypalSubscription->activateSubscription('I-W81GSLF3FPJL'));
        // dd($paypalSubscription->suspendSubscription('I-W81GSLF3FPJL'));
        // dd($paypalSubscription->cancelSubscription('I-W81GSLF3FPJL'));
    }
}
