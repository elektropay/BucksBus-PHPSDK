<?php

namespace Bucksbus;

class Request
{
    private $apiName;
    private $apiSecret;
    private $baseUrl;

    public function __construct($apiName, $apiSecret, $baseUrl)
    {
        $this->apiName = $apiName;
        $this->apiSecret = $apiSecret;
        $this->baseUrl = $baseUrl;
    }

    /**
     * Perform a Post request with parameters.
     *
     * @param string $endpoint
     * @param array $params
     * @return array
     */
    public function post($endpoint, $params)
    {
        $headers = [];
        $headers[] = 'Authorization: Basic ' . base64_encode($this->apiName . ':' . $this->apiSecret);
        $headers[] = 'Content-Type: application/json';

        $ch = curl_init($this->baseUrl . $endpoint);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $responseBody = trim(curl_exec($ch));
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        curl_close($ch);

        // Return the status code, response body, and any cURL error message
        return [$statusCode, $responseBody, $curlError];
    }

    /**
     * Perform a GET request with optional query parameters.
     *
     * @param string $endpoint
     * @param array $params
     * @return array
     */
    public function get($endpoint, $params = [])
    {
        $headers = [];
        $headers[] = 'Authorization: Basic ' . base64_encode($this->apiName . ':' . $this->apiSecret);
        $headers[] = 'Content-Type: application/json';

        // Build query string if $params is not empty
        $queryString = !empty($params) ? '?' . http_build_query($params) : '';

        // Initialize cURL for GET request
        $ch = curl_init($this->baseUrl . $endpoint . $queryString);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $responseBody = trim(curl_exec($ch));
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        curl_close($ch);

        // Return the status code, response body, and any cURL error
        return [$statusCode, $responseBody, $curlError];
    }
}
