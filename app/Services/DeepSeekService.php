<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class DeepSeekService
{
    protected $client;
    protected $baseUrl;
    protected $apiKey;
    protected $model;

    public function __construct()
    {
        $this->baseUrl = config('services.deepseek.base_url', 'https://api.deepseek.com');
        $this->apiKey = config('services.deepseek.api_key');
        $this->model = config('services.deepseek.model', 'deepseek-chat');

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'timeout' => 120,
            'connect_timeout' => 30,
        ]);
    }

    /**
     * Create a chat completion with DeepSeek API
     *
     * @param array $messages Array of messages with role and content
     * @return array Response from the API
     */
    public function createChatCompletion(array $messages)
    {
        try {
            $payload = [
                'model' => $this->model,
                'messages' => $messages,
//                'max_tokens' => 1000,
                'temperature' => 0.7,
                'top_p' => 0.9,
            ];

            $response = $this->client->post('/chat/completions', [
                'json' => $payload,
                'timeout' => 120,
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true);

            $data = [];
            $reasoning_model = "deepseek-reasoner";

            if ($responseBody['model'] === $reasoning_model){
                $data = [
                    'reasoning_content' => $responseBody['choices'][0]['message']['reasoning_content'] ?? null,
                ];
            }

            $data['completion'] = $responseBody['choices'][0]['message']['content'] ?? null;
            $data['full_response'] = $responseBody;


            return $data;
        } catch (\Exception $e) {
            Log::error('DeepSeek API Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
