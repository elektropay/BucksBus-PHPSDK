<?php

require '../vendor/autoload.php';

use Bucksbus\Client as BucksbusClient;

//Client side form basic validation of required fields
$errors = [];
if (empty($_POST['amount']) || !is_numeric($_POST['amount'])) {
    $errors[] = "Amount is required and must be a valid number.";
}
if (empty($_POST['asset_id'])) {
    $errors[] = "Amount Currency is required.";
}
if (empty($_POST['payment_asset_id'])) {
    $errors[] = "Crypto Currency is required.";
}
if (empty($_POST['payer_email']) || !filter_var($_POST['payer_email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = "A valid payer email is required.";
}
if (empty($_POST['payer_name'])) {
    $errors[] = "Payer name is required.";
}
if (empty($_POST['description'])) {
    $errors[] = "Payment description is required.";
}
//You can add as much validation as you need for other fields too
//.....

// If there are errors, display them and exit
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<p>$error</p>";
    }
    exit;
}

// Load config
$client = new BucksbusClient();

try {
    // Process the payment request
    $params = [
        'payment_type' => $_POST['payment_type'],
        'amount' => $_POST['amount'],
        'asset_id' => $_POST['asset_id'],
        'payment_asset_id' => $_POST['payment_asset_id'],
        'payer_email' => $_POST['payer_email'],
        'payer_name' => $_POST['payer_name'],
        'payer_lang' => $_POST['payer_lang'],
        'description' => $_POST['description'],
        'timeout' => $_POST['timeout'],
        'custom1' => $_POST['custom1'],
        'custom2' => $_POST['custom2']
        // Additional parameters as needed...
    ];

    $response = $client->createPayment($params);
    if (!$response->isSuccess()) {
        echo "Failed with errors: " . $response->getErrorMessage();
        echo "<pre>" . json_encode($response->getResult(), JSON_PRETTY_PRINT) . "</pre>";
        exit;
    } else {
        echo "Payment processed successfully, here are payment information: ".PHP_EOL;
        echo "<pre>" . json_encode($response->getResult(), JSON_PRETTY_PRINT) . "</pre>";

        echo "<p><b>To continue the payment, client should be redirected to the following URL:</b></p>";
        echo "<a href='" . $response->getResult()["payment_url"] . "' target='_blank'>Payment URL</a>";
    }

} catch (Exception $e) {
    echo "Error processing payment: " . $e->getMessage();
}