<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class GeminiService {
    protected $client;
    protected $apiKey;
    protected $baseUrl;
    protected $model;

    public function __construct() {
        $this->apiKey = config('services.gemini.api_key');
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1beta';
        $this->model = config('services.gemini.model', 'gemini-2.0-flash');

        $this->client = new Client([
            'timeout' => 120,
            'connect_timeout' => 30,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Generate content with Gemini API
     *
     * @param array $messages Array of messages with role and content
     * @return array Response from the API
     */
    public function generateContent(array $messages) {
        try {
            $url = "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}";
            
            $text = "";
            foreach ($messages as $message) {
                $text .= $message['content'] . "\n\n";
            }
            $text = trim($text);
            
            $payload = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $text]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topP' => 0.9,
                    'maxOutputTokens' => 1000,
                ]
            ];

            $response = $this->client->post($url, [
                'json' => $payload,
                'timeout' => 120,
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true);
            
            $content = $responseBody['candidates'][0]['content']['parts'][0]['text'] ?? null;
            
            if ($content) {
                $content = $this->cleanJsonResponse($content);
            }
            
            return [
                'completion' => $content,
                'full_response' => $responseBody,
            ];
        } catch (\Exception $e) {
            Log::error('Gemini API Error: ' . $e->getMessage());
            Log::error('API URL: ' . $url);
            Log::error('API Payload: ' . json_encode($payload));
            if (method_exists($e, 'getResponse') && $e->getResponse()) {
                Log::error('Response body: ' . $e->getResponse()->getBody());
            }
            throw $e;
        }
    }

    /**
     * Clean JSON response from markdown delimiters
     *
     * @param string $content Response content
     * @return string Cleaned JSON
     */
    protected function cleanJsonResponse($content) {
        $content = preg_replace('/```json\s*/', '', $content);
        $content = preg_replace('/```\s*/', '', $content);

        if (json_decode($content)) {
            return $content;
        }

        preg_match('/\{.*\}/s', $content, $matches);
        if (!empty($matches[0]) && json_decode($matches[0])) {
            return $matches[0];
        }

        return $content;
    }

    /**
     * Validate if the response is in the expected format
     *
     * @param string $jsonContent JSON content to validate
     * @return bool|array Return false if invalid, or the decoded JSON if valid
     */
    public function validateHealthTipFormat($jsonContent) {
        try {
            $data = json_decode($jsonContent, true);

            if (!$data) {
                return false;
            }

            $requiredFields = ['title', 'greeting', 'tip', 'actionable_steps', 'warning_signs', 'conclusion'];

            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    return false;
                }
            }

            if (!is_array($data['actionable_steps']) || !is_array($data['warning_signs'])) {
                return false;
            }

            return $data;
        } catch (\Exception $e) {
            return false;
        }
    }
}
