<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessDeepSeekRequest;
use App\Services\DeepSeekService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class DeepSeekController extends Controller {
    protected $deepSeekService;

    public function __construct(DeepSeekService $deepSeekService) {
        $this->deepSeekService = $deepSeekService;
    }

    /**
     * Send a message to DeepSeek API and get a response
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function chat(Request $request): JsonResponse {
        // Aumentar el tiempo límite para esta petición específica
        set_time_limit(120); // 2 minutos

        $request->validate([
            'prompt' => 'required|string',
            'system_message' => 'sometimes|string',
            'async' => 'sometimes|boolean',
        ]);

        $systemMessage = $request->input('system_message', 'You are a helpful assistant.');
        $userPrompt = $request->input('prompt');
        $async = $request->input('async', false);

        $messages = [
            ['role' => 'system', 'content' => $systemMessage],
            ['role' => 'user', 'content' => $userPrompt],
        ];

        // Si es una petición asíncrona
        if ($async) {
            // Generar un ID único para esta solicitud
            $requestId = Str::uuid()->toString();

            // Encolar el trabajo en segundo plano
            ProcessDeepSeekRequest::dispatch($messages, $requestId);

            return response()->json([
                'status' => true,
                'message' => 'Tu solicitud está siendo procesada.',
                'async' => true,
                'request_id' => $requestId
            ]);
        }

        // Proceso síncrono (normal)
        try {
            $response = $this->deepSeekService->createChatCompletion($messages);

            return response()->json([
                'status' => true,
                'message' => 'Chat completion successful',
                'response' => $response['completion'],
                'reasoning_content' => $response['reasoning_content'] ?? "No reasoning content available",
//                'full_response' => $response['full_response'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error getting chat completion: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check the status of an asynchronous request
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkStatus(Request $request): JsonResponse {
        $request->validate([
            'request_id' => 'required|string'
        ]);

        $requestId = $request->input('request_id');
        $result = Cache::get('deepseek_' . $requestId);

        if (!$result) {
            return response()->json([
                'status' => true,
                'message' => 'Solicitud en proceso o no encontrada.',
                'request_status' => 'processing'
            ]);
        }

        if ($result['status'] === 'error') {
            return response()->json([
                'status' => false,
                'message' => $result['message'],
                'request_status' => 'error'
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Solicitud completada.',
            'request_status' => 'completed',
            'response' => $result['data']['completion'],
            'reasoning_content' => $result['data']['reasoning_content'] ?? "No reasoning content available",
        ]);
    }
}
