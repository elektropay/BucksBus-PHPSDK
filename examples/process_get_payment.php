<?php

require '../vendor/autoload.php';

use Bucksbus\Client as BucksbusClient;

// Get the payment ID from the URL
$paymentId = $_GET['payment_id'];

// Initialize the client
$client = new BucksbusClient();

// Fetch detailed payment information
$response = $client->getPaymentInfo($paymentId);

if ($response->isSuccess()) {
    echo "<h2>Payment Details:</h2>";
    echo "<pre>" . json_encode($response->getResult(), JSON_PRETTY_PRINT) . "</pre>";
} else {
    echo "<p>Error fetching payment information: " . $response->getErrorMessage() . "</p>";
}
