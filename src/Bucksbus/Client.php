<?php

namespace Bucksbus;

class Client
{
    private $apiName;
    private $apiSecret;
    private $webhookSecret;
    private $baseUrl;
    private $successUrl;
    private $cancelUrl;
    private $webhookUrl;


    const STATUS_COMPLETE = "COMPLETE";
    const STATUS_OPEN     = "OPEN";

    public function __construct()
    {
        $merchants = require_once __DIR__ . '/config.php';

        //ToDo: check if this is necessary logic
        $config = $merchants['testing'];

        $this->apiName = $config['api_name'];
        $this->apiSecret = $config['api_secret'];
        $this->webhookSecret = $config['webhook_secret'];
        $this->baseUrl = $config['base_url'];
        $this->successUrl = $config['success_url'];
        $this->cancelUrl = $config['cancel_url'];
        $this->webhookUrl = $config['webhook_url'];
    }

    /**
     * Validate the webhook signature.
     *
     * @param string $data  The raw POST data from the webhook
     * @param string $receivedSignature  The signature received from the webhook header
     * @return bool
     */
    public function validateWebhookSignature($data, $receivedSignature)
    {
        // Recreate the signature using HMAC-SHA256
        $calculatedSignature = hash_hmac('sha256', $data, $this->webhookSecret) ;

        // Compare the received signature with the calculated one
        return hash_equals($calculatedSignature, $receivedSignature);
    }

    /**
     * Check if the status of payment is complete
     *
     * @param string $status
     * @return bool
     */
    public function isCompleteStatus($status)
    {
        return $status !== self::STATUS_COMPLETE;
    }

    /**
     * Incoming parameters validation, you may customize it as you need
     *
     * @param array $params
     * @return Response
     */
    private function validatePaymentParams($params) {
        $errors = [];

        //Additional layer of Payment data validation, you may add as many validation as you need
//        if (!in_array($params['payment_type'], ['FIXED_AMOUNT', 'OPEN_AMOUNT'])) {
//            $errors[] = "Payment type value must be either FIXED_AMOUNT or OPEN_AMOUNT";
//        }
//        if (!isset($params['amount']) || $params['amount'] <= 0) {
//            $errors[] = "Amount must be greater than zero.";
//        }
//        if (!in_array($params['asset_id'], ['USD', 'EUR', 'BTC', 'TRX', 'ETH', 'USDT.ERC20', 'USDT.TRC20'])) {
//            $errors[] = "Incorrect currency value.";
//        }
//        if (!in_array($params['payment_asset_id'], ['BTC', 'TRX', 'ETH', 'USDT.ERC20', 'USDT.TRC20'])) {
//            $errors[] = "Incorrect crypto currency ID value.";
//        }
//        if (!in_array($params['payment_type'], ['FIXED_AMOUNT', 'OPEN_AMOUNT'])) {
//            $errors[] = "Payment type value must be either FIXED_AMOUNT or OPEN_AMOUNT";
//        }
//        if ($params['timeout'] < 0 || $params['timeout'] > 1440) {
//            $errors[] = "Timeout must be between 0 and 1440 minutes.";
//        }
//
//        if (empty($params['payer_email']) || !filter_var($params['payer_email'], FILTER_VALIDATE_EMAIL)) {
//            $errors[] = 'A valid payer email is required.';
//        }

        // If there are validation errors, return a Response object with the error messages
        // Return a 422 status code for validation errors
        if (!empty($errors)) {
            return new Response(422, json_encode(['errors' => $errors]), 'Validation failed');
        }


        // Return a successful validation response if there are no errors
        return new Response(200, json_encode(['message' => 'Validation passed']));
    }


    /**
     * Create a payment after validating the parameters.
     *
     * @param array $params
     * @return Response
     */
    public function createPayment($params)
    {
        //Validate the request parameters
        $validationResponse = $this->validatePaymentParams($params);
        if (!$validationResponse->isSuccess()) {
            // Return validation error response if validation fails
            return $validationResponse;
        }

        //ToDo: need to update the request structure
        $params['amount'] = (float) $params['amount'];
        $params['timeout'] = (int) $params['timeout'];
        $params['custom'] = json_encode([
           "custom_data" => "Some Custom Data"
        ]);
        $params['success_url']   = $this->successUrl;
        $params['cancel_url']    = $this->cancelUrl;
        $params['webhook_url']   = $this->webhookUrl;

        // Proceed with the API request if validation passes
        $request = new Request($this->apiName, $this->apiSecret, $this->baseUrl);
        list($statusCode, $responseBody, $errorMsg) = $request->post('/payment', $params);

        // Return the API response using the Response class
        return new Response($statusCode, $responseBody, $errorMsg);
    }


    /**
     * Retrieve a list of payments with optional filters.
     * Filters can include: from, to, asset_id, status, etc.
     *
     * @param array $filters
     * @return Response
     */
    public function getPayments($filters = [])
    {
        // Initialize Request object
        $request = new Request($this->apiName, $this->apiSecret, $this->baseUrl);

        // Make GET request to receive payments for based on filters
        list($statusCode, $responseBody, $errorMsg) = $request->get('/payments', $filters);

        // Return the Response object to handle status codes and errors
        return new Response($statusCode, $responseBody, $errorMsg);
    }

    /**
     * Retrieve a payment information by paymentsId
     *
     * @param string $paymentId
     * @return Response
     */
    public function getPaymentInfo($paymentId)
    {
        // Initialize Request object
        $request = new Request($this->apiName, $this->apiSecret, $this->baseUrl);

        // Make GET request for specific payment ID
        list($statusCode, $responseBody, $errorMsg) = $request->get("/payment/{$paymentId}");

        return new Response($statusCode, $responseBody, $errorMsg);
    }

}