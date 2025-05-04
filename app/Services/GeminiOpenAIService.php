<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class GeminiOpenAIService
{
    protected $client;
    protected $apiKey;
    protected $baseUrl;
    protected $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        // De acuerdo con la documentación: https://ai.google.dev/api/rest/v1/models
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
     * Create a chat completion with Gemini API using OpenAI compatibility layer
     *
     * @param array $messages Array of messages with role and content
     * @return array Response from the API
     */
    public function createChatCompletion(array $messages)
    {
        try {
            // URL para la compatibilidad con OpenAI
            $url = "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}";
            
            // Convertir formato de mensajes OpenAI a formato Gemini
            $contents = [];
            $systemContent = '';
            
            // Primero, extraer cualquier mensaje de sistema
            foreach ($messages as $message) {
                if (strtolower($message['role']) === 'system') {
                    $systemContent .= $message['content'] . "\n";
                }
            }
            
            // Luego, agregar los mensajes que no son del sistema
            foreach ($messages as $message) {
                if (strtolower($message['role']) !== 'system') {
                    // Si hay contenido de sistema y este es el primer mensaje de usuario,
                    // prepend el contenido del sistema al primer mensaje de usuario
                    if (strtolower($message['role']) === 'user' && !empty($systemContent) && empty($contents)) {
                        $message['content'] = $systemContent . "\n" . $message['content'];
                    }
                    
                    // Mapear roles de OpenAI a roles de Gemini
                    $geminiRole = strtolower($message['role']) === 'assistant' ? 'model' : 'user';
                    
                    $contents[] = [
                        'role' => $geminiRole,
                        'parts' => [
                            ['text' => $message['content']]
                        ]
                    ];
                }
            }
            
            // Si solo hay mensajes de sistema, crear un mensaje de usuario genérico
            if (empty($contents) && !empty($systemContent)) {
                $contents[] = [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $systemContent]
                    ]
                ];
            }
            
            $payload = [
                'contents' => $contents,
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
            
            // Procesar respuesta de Gemini (formato diferente al de OpenAI)
            $content = $responseBody['candidates'][0]['content']['parts'][0]['text'] ?? null;
            
            // Limpiar la respuesta JSON si contiene delimitadores markdown
            if ($content) {
                $content = $this->cleanJsonResponse($content);
            }
            
            return [
                'completion' => $content,
                'full_response' => $responseBody,
            ];
        } catch (\Exception $e) {
            Log::error('Gemini OpenAI API Error: ' . $e->getMessage());
            // Registrar información adicional para depuración
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
    protected function cleanJsonResponse($content)
    {
        // Remover delimitadores de código markdown
        $content = preg_replace('/```json\s*/', '', $content);
        $content = preg_replace('/```\s*/', '', $content);
        
        // Asegurarse de que es un JSON válido
        if (json_decode($content)) {
            return $content;
        }
        
        // Si no es JSON válido, intentar extraer JSON de la respuesta
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
    public function validateHealthTipFormat($jsonContent)
    {
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