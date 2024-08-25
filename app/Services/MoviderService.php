<?php
// app/Services/MoviderService.php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

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
    
    public function getBalance()
    {
        try {
            Log::info('Fetching Movider balance.');

            $response = $this->client->post('balance', [
                'form_params' => [
                    'api_key' => env('MOVIDER_API_KEY'),
                    'api_secret' => env('MOVIDER_API_SECRET'),
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            Log::info('Movider Balance Response:', $data);

            // Update this part to properly extract the amount from the response
            $balance = $data['amount'] ?? 0;

            return ['balance' => $balance];
        } catch (\Exception $e) {
            Log::error('Error fetching Movider balance: ' . $e->getMessage());
            return ['balance' => 0];
        }
    }
}