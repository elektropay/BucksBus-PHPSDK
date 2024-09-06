<?php

require '../vendor/autoload.php';

use Bucksbus\Client as BucksbusClient;

// Get filter inputs from the form
$filters = [
    'from' => $_POST['from'],
    'to' => $_POST['to'],
    'asset_id' => $_POST['asset_id'],
    'status' => $_POST['status']
];

// Initialize the client
$client = new BucksbusClient();

// Fetch payments with the specified filters
$response = $client->getPayments($filters);

if ($response->isSuccess()) {
    // List the filtered payments
    $payments = $response->getResult();
    echo "<h2>Filtered Payments:</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Payment ID</th><th>Amount</th><th>Currency</th><th>Created Date</th><th>Status</th><th>Details</th></tr>";

    foreach ($payments as $payment) {
        echo "<tr>";
            echo "<td>" . $payment['payment_id'] . "</td>";
            echo "<td>" . $payment['payment_amount'] . "</td>";
            echo "<td>" . $payment['payment_asset_id'] . "</td>";
            echo "<td>" . $payment['start_date'] . "</td>";
            echo "<td>" . $payment['status_name'] . "</td>";
            echo "<td><a href='process_get_payment.php?payment_id=" . $payment['payment_id'] . "'>View Details</a></td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<p>Error fetching payments: " . $response->getErrorMessage() . "</p>";
}
