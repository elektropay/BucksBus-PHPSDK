<?php

require '../vendor/autoload.php';

use Bucksbus\Client as BucksbusClient;

// Get the raw POST data from the webhook request
$payloadRawData = file_get_contents('php://input');

// Get the signature from the headers (adjust header name according to API documentation)
$receivedSignature = isset($_SERVER['HTTP_X_WEBHOOK_HMAC_SHA256']) ? $_SERVER['HTTP_X_WEBHOOK_HMAC_SHA256'] : '';

$client = new BucksbusClient();

// Step 1: Validate the signature
if (!$client->validateWebhookSignature($payloadRawData, $receivedSignature)) {
    // Signature does not match, log and stop processing
    http_response_code(400);  // Bad Request
    echo json_encode(['error' => 'Invalid signature']);
    exit;
}

// Step 2: Process the incoming webhook data (assume JSON)
$parsedData = json_decode($payloadRawData, true);
$paymentParams = $parsedData['payment'];

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);  // Bad Request
    echo json_encode(['error' => 'Invalid JSON payload']);
    exit;
}

// Step 3: Handle the webhook data by event type or payment status
$eventType = isset($parsedData['event']) ? $parsedData['event'] : null;
$paymentId = isset($paymentParams['payment_id']) ? $paymentParams['payment_id'] : null;
$paymentStatus = isset($paymentParams['status']) ? $paymentParams['status'] : null;

// Example handling based on event type
switch ($eventType) {
    case 'payment.open':
        // Payment open, handle accordingly
        echo json_encode(['success' => 'Payment Opened successfully']);
        break;

    case 'payment.completed':
        // Payment completed, handle accordingly
        echo json_encode(['success' => 'Payment completed successfully']);
        break;

    case 'payment.cancel':
        // Payment failed, handle accordingly
        echo json_encode(['error' => 'Payment failed']);
        break;

    default:
        // Unknown event type, log and ignore
        http_response_code(400);  // Bad Request
        echo json_encode(['error' => 'Unknown event type']);
        exit;
}

// Return a success response (HTTP 200) to the payment system
http_response_code(200);
echo json_encode(['success' => 'Webhook handled successfully']);
