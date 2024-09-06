<?php

namespace Bucksbus;

class Response
{
    public $success;
    private $errorCode;
    private $errorMessage;
    public $result;

    /**
     * Constructor accepts HTTP status code, response body, and an optional error message.
     *
     * @param int $statusCode
     * @param string $responseBody
     * @param string $errorMessage
     */
    public function __construct($statusCode, $responseBody, $errorMessage = '')
    {
        $this->parseResponse($statusCode, $responseBody, $errorMessage);
    }

    /**
     * Parse the response based on the status code.
     *
     * @param int $statusCode
     * @param string $responseBody
     * @param string $errorMessage
     * @return void
     */
    private function parseResponse($statusCode, $responseBody, $errorMessage)
    {
        // Parse the response body (assuming JSON for now)
        // Determine success based on the HTTP status code
        $this->success = $statusCode == 200;
        $responseData = [];

        // Determine success or failure based on HTTP status codes
        if ($statusCode >= 200 && $statusCode < 300) {
            $statusCode = 0;
            $errorMessage = '';
            // Parse the response body
            $responseData = json_decode($responseBody, true);
        } elseif ($statusCode >= 400 && $statusCode < 500) {
            // Validation or client error (4xx)
            $errorMessage = 'Client error: ' . ($errorMessage ?: $statusCode);
            // Parse the response body
            $responseData = json_decode($responseBody, true);
        } elseif ($statusCode >= 500) {
            // Server error (5xx)
            $errorMessage = 'Server error: ' . ($errorMessage ?: $statusCode);
        } else {
            // Unexpected status code
            $errorMessage = 'Unexpected error: ' . ($errorMessage ?: $statusCode);
        }

        // Set customized error code and message based on their status code and/or response body
        $this->errorCode = $statusCode;
        $this->errorMessage = $errorMessage;

        // Set the result data (could be [] if an error occurred)
        $this->result = $responseData;
    }



    public function isSuccess()
    {
        return $this->success;
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function setResult($data)
    {
        $this->result = $data;
    }
}