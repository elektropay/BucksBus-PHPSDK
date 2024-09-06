<?php
return [
    //Testing Merchant
    "testing" => [
        'api_name' => '1680121',
        'api_secret' => 'a50f1076c5fb05cd4ef4461959b89b14',
        'webhook_secret' => '6dbb069475b2020d0ceb34f63182d915',
        'base_url' => 'https://devapi.bucksbus.com/int',
        'success_url' => 'http://localhost/bucksbusSdk/examples/success.html',
        'cancel_url' => 'http://localhost/bucksbusSdk/examples/failure.html',
        'webhook_url' => 'http://localhost/bucksbusSdk/examples/webhook.php',
    ],
    //Real Merchant
    'production' => [
        'api_name' => 'some_api_name',
        'api_secret' => 'some_api_secret',
        'webhook_secret' => 'some_webhook_secret',
        'base_url' => 'https://api.bucksbus.com/int'
    ]
];