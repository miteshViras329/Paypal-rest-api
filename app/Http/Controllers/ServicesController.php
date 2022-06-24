<?php

namespace App\Http\Controllers;

use App\Services\Paypal\Order;
use App\Services\Paypal\Paypal;
use App\Services\Paypal\Plan;
use App\Services\Paypal\Product;

class ServicesController extends Controller
{
    public function index()
    {
        $paypal = new Paypal();
        // $paypalOrder = new Order();
        // $paypalProduct = new Product();
        $paypalPlan = new Plan();
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
            'name' => 'Scraper329',
            'description' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.',
            'interval_unit' => 'DAY',
            'interval_count' => 1,
            'total_cycles' => 12,
            'price_value' => 50,
            'currency_code' => 'USD',
            'setup_fee' => 0
        ];
        dd($paypalPlan->createPlan('PROD-3DN36045AB844170W', $data));
        // dd($paypalProduct->showList());
        // dd($paypalProduct->show('PROD-3DN36045AB844170W'));
        // dd($paypalProduct->createProduct($data));
        dd($paypalProduct->updateProduct('PROD-3DN36045AB844170W', $data));
    }
}
