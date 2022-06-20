<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use App\Http\Controllers\PayPalController;

class ProductController extends Controller
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
            $url = 'https://api-m.sandbox.paypal.com/v1/catalogs/products?page_size=' . $page_size . '&page=' . $page . '&total_required=' . $total;
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
            //throw $th;
        }
    }

    public function createProduct()
    {
        try {
            $url = 'https://api-m.sandbox.paypal.com/v1/catalogs/products';
            $res = $this->client->request('post', $url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en_US',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->bearer_token,
                    'PayPal-Request-Id' => 'PRODUCT' . '-' . now()->timestamp . '-' . rand(0, 9999),
                ],
                'json' => [
                    "name" => "Skyrush",
                    "description" => "Crypto Airdrop Services",
                    "type" => "SERVICE",
                    "category" => "SOFTWARE",
                    "image_url" => "https://skyrush.io/assets/images/slider/home_giveaway_01-1.png",
                    "home_url" => "https://skyrush.io"
                ]
            ]);

            $body = json_decode($res->getBody()->getContents());
            dd('Product Created Successfully', $body);
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function updateProduct()
    {
        // note : patch method never return a response, check paypal api doc for more info.
        try {
            $url = 'https://api-m.sandbox.paypal.com/v1/catalogs/products/' . request()->token;
            $res = $this->client->request('patch', $url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en_US',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->bearer_token,
                ],
                'json' => [[
                    'op' => 'replace', // replace, remove, add
                    'path' => '/description', // /name, /description, /type, /category, /image_url, /home_url
                    'value' => 'Skyrush', // value to replace
                ]]
            ]);
            dd('Product Updated Successfully');
        } catch (\Throwable $th) {
            dd($th);
        }
    }
}
