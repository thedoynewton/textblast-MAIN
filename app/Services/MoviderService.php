<?php
// app/Services/MoviderService.php

namespace App\Services;

use GuzzleHttp\Client;

class MoviderService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.movider.co/v1/',
            'timeout'  => 2.0,
        ]);
    }

    public function sendBulkSMS(array $recipients, string $message)
    {
        $response = $this->client->post('sms', [
            'form_params' => [
                'api_key' => env('MOVIDER_API_KEY'),
                'api_secret' => env('MOVIDER_API_SECRET'),
                'to' => implode(',', $recipients),
                'text' => $message,
            ]
        ]);

        return json_decode($response->getBody()->getContents());
    }
}
