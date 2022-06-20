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
            dd('Product List', $body);
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function show()
    {
        try {
            $url = 'https://api-m.sandbox.paypal.com/v1/catalogs/products/' . request()->token;
            $res = $this->client->request('get', $url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en_US',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->bearer_token,
                ],
            ]);
            $body = json_decode($res->getBody()->getContents());
            dd('Product Detail', $body);
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
            dd('Activate Plan');
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
            dd('Deactivate Plan');
        } catch (\Throwable $th) {
            dd($th);
        }
    }
}
