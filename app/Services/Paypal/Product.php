<?php

namespace App\Services\Paypal;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class Product
{
    public $bearer_token, $client, $paypal, $paypalUrl;
    public function __construct()
    {
        $this->client = new Client();
        $this->paypal = new Paypal();
        $this->bearer_token = $this->paypal->getAccessToken();
        $this->paypalUrl = $this->paypal->getPaypalUrl();
    }

    public function showList($page_size = 10, $current_page = 1, $total_records = 'true')
    {
        try {
            $url = $this->paypalUrl . '/v1/catalogs/products?page_size=' . $page_size . '&page=' . $current_page . '&total_required=' . $total_records;
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

    public function show($product_id)
    {
        try {
            if (empty($product_id)) {
                return 'Please Enter Product Id, Product Id Cannot Be Null';
            }
            $url = $this->paypalUrl . '/v1/catalogs/products/' . $product_id;
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

    public function createProduct($data)
    {
        if (!empty($data)) {
            try {
                $url = $this->paypalUrl . '/v1/catalogs/products';
                $res = $this->client->request('post', $url, [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Accept-Language' => 'en_US',
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $this->bearer_token,
                        'PayPal-Request-Id' => 'PRODUCT' . '-' . now()->timestamp . '-' . rand(0, 9999),
                    ],
                    'json' => [
                        "name" => $data['name'],
                        "description" => $data['description'],
                        "type" => $data['type'],
                        "category" => $data['category'],
                        "image_url" => $data['image_url'],
                        "home_url" => $data['home_url']
                    ]
                ]);
                return json_decode($res->getBody()->getContents());
            } catch (ClientException $e) {
                print_r($e->getResponse()->getBody()->getContents());
            }
        } else {
            $error = [];
            if (empty($data['name'])) {
                $error['error_name'] = 'Product Name Is Required, Please Enter Product Name!!!';
            }

            if (empty($data['description'])) {
                $error['error_description'] = 'Product Description Is Required, Please Enter Product Description!!!';
            }

            if (empty($data['type'])) {
                $error['error_type'] = 'Product Type Is Required, Please Enter Product Type!!!';
            }

            if (empty($data['category'])) {
                $error['error_category'] = 'Product Category Is Required, Please Enter Product Category!!!';
            }

            if (empty($data['image_url'])) {
                $error['error_image_url'] = 'Product Image Url Is Required, Please Enter Product Image Url!!!';
            }

            if (empty($data['home_url'])) {
                $error['error_home_url'] = 'Product Home Url Is Required, Please Enter Product Home Url!!!';
            }
            return $error;
        }
    }

    public function updateProduct($product_id, $data)
    {
        // note : patch method never return a response, check paypal api doc for more info.
        try {
            if (!empty($data)) {
                $error = [];
                if (!empty($data)) {
                    if (empty($data['op'])) {
                        $error['error_op'] = 'Product Operation Is Required, Please Enter OP!!!';
                    } else {
                        if (!in_array($data['op'], ['replace', 'remove', 'add'])) {
                            $error['error_op'] = 'Product OP Must Be "replace", "remove" or "add"';
                        }
                    }

                    if (empty($data['path'])) {
                        $error['error_path'] = 'Product Path Is Required, "path" Describe On Which Field You Want To Perform Operation.';
                    } else {
                        if (!in_array($data['path'], ['/description', '/image_url', '/home_url'])) {
                            $error['error_path'] = 'Product path Must Be "/description", "/image_url" or "/home_url"';
                        }
                    }

                    if (empty($data['value'])) {
                        $error['error_value'] = 'Product Value Is Required, Please Enter Product Value!!!';
                    }
                }

                if (!empty($error)) {
                    return $error;
                }

                $url = $this->paypalUrl . '/v1/catalogs/products/' . $product_id;
                $res = $this->client->request('patch', $url, [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Accept-Language' => 'en_US',
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $this->bearer_token,
                    ],
                    'json' => [
                        [
                            'op' => $data['op'],
                            'path' => $data['path'],
                            'value' => $data['value']
                        ]
                    ]
                ]);
                return 'Updated Successfully';
            } else {
            }
        } catch (ClientException $e) {
            print_r($e->getResponse()->getBody()->getContents());
        }
    }
}
