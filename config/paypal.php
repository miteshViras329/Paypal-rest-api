<?php

return [
    'paypal_sandbox_client_id' => env('PAYPAL_SANDBOX_CLIENT_ID'),
    'paypal_sandbox_secret' => env('PAYPAL_SANDBOX_SECRET'),
    'paypal_live_client_id' => env('PAYPAL_LIVE_CLIENT_ID'),
    'paypal_live_secret' => env('PAYPAL_LIVE_SECRET'),
    'cancle_url' => env('APP_URL') . ':8000/paypal/cancel',
    'return_url' => env('APP_URL') . ':8000/paypal/success',
    'mode' => env('PAYPAL_MODE'),
];
