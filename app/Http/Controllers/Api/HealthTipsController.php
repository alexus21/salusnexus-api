<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHealthTipsRequest;
use App\Http\Requests\UpdateHealthTipsRequest;
use App\Models\Disease;
use App\Models\HealthTips;
use App\Services\DeepSeekService;
use App\Services\GeminiOpenAIService;
use App\Services\GeminiService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class HealthTipsController extends Controller {
    protected $geminiService;
    protected $geminiOpenAIService;
    protected $deepSeekService;

    public function __construct(
        GeminiService       $geminiService,
        GeminiOpenAIService $geminiOpenAIService,
        DeepSeekService     $deepSeekService
    ) {
        $this->geminiService = $geminiService;
        $this->geminiOpenAIService = $geminiOpenAIService;
        $this->deepSeekService = $deepSeekService;
    }

    /**
     * Generate a health tip for a patient with specific diseases
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function generateTip(Request $request): JsonResponse {
        // Validar entrada
        $validator = Validator::make($request->all(), [
            'disease_ids' => 'required|array',
            'disease_ids.*' => 'exists:diseases,id',
            'service' => 'sometimes|in:gemini,gemini_openai,deepseek',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Obtener enfermedades
        $diseases = Disease::whereIn('id', $request->disease_ids)->get();
        if ($diseases->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No se encontraron enfermedades con los IDs proporcionados'
            ], 404);
        }

        log::info($diseases);

        // Crear lista de nombres de enfermedades
        $diseaseNames = $diseases->pluck('name')->implode(', ');

        // Construir prompt
        $prompt = $this->buildHealthTipPrompt($diseaseNames);

        try {
            // Usar servicio seleccionado (Gemini OpenAI por defecto)
            $service = $request->input('service', 'gemini_openai');

            if ($service === 'gemini') {
                $response = $this->geminiService->generateContent([
                    ['role' => 'user', 'content' => $prompt]
                ]);

                $jsonContent = $response['completion'];
                $validatedData = $this->geminiService->validateHealthTipFormat($jsonContent);
            } elseif ($service === 'gemini_openai') {
                $response = $this->geminiOpenAIService->createChatCompletion([
                    ['role' => 'user', 'content' => $prompt]
                ]);

                $jsonContent = $response['completion'];
                $validatedData = $this->geminiOpenAIService->validateHealthTipFormat($jsonContent);
            } else {
                $response = $this->deepSeekService->createChatCompletion([
                    ['role' => 'user', 'content' => $prompt]
                ]);

                $jsonContent = $response['completion'];
                // Limpiar posibles delimitadores markdown en la respuesta
                $jsonContent = preg_replace('/```json\s*/', '', $jsonContent);
                $jsonContent = preg_replace('/```\s*/', '', $jsonContent);

                $validatedData = json_decode($jsonContent, true);
            }

            // Verificar si la respuesta tiene el formato correcto
            if (!$validatedData) {
                return response()->json([
                    'status' => false,
                    'message' => 'La respuesta de la IA no tiene el formato correcto',
                    'raw_response' => $jsonContent
                ], 500);
            }

            return response()->json([
                'status' => true,
                'message' => 'Consejo de salud generado correctamente',
                'health_tip' => $validatedData,
                'diseases' => $diseases->pluck('name'),
            ]);
        } catch (Exception $e) {
            $errorDetails = [
                'message' => $e->getMessage(),
                'prompt' => $prompt,
                'service' => $service,
                'model' => $service === 'gemini'
                    ? config('services.gemini.model')
                    : ($service === 'gemini_openai'
                        ? config('services.gemini.model')
                        : config('services.deepseek.model')),
                'exception_class' => get_class($e)
            ];

            // Log error for debugging
            Log::error('Health tip generation error', $errorDetails);

            // Check if running in debug mode to return more details
            if (config('app.debug')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error al generar consejo de salud: ' . $e->getMessage(),
                    'debug_info' => $errorDetails
                ], 500);
            }

            return response()->json([
                'status' => false,
                'message' => 'Error al generar consejo de salud: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Build prompt for health tip generation
     *
     * @param string $diseaseNames Comma-separated list of disease names
     * @return string The prompt
     */
    protected function buildHealthTipPrompt(string $diseaseNames): string {
        return "Genera un consejo de salud para un paciente con las siguientes condiciones médicas: {$diseaseNames}.
El consejo debe seguir EXACTAMENTE este formato JSON:
{
  \"title\": \"Título breve del consejo\",
  \"greeting\": \"Saludo personalizado mencionando la condición\",
  \"tip\": \"Consejo específico para la condición, máximo 150 palabras\",
  \"actionable_steps\": [\"Paso 1\", \"Paso 2\", \"Paso 3\"],
  \"warning_signs\": [\"Señal 1\", \"Señal 2\"],
  \"conclusion\": \"Conclusión breve\"
}
Importante: Mantén este formato exacto sin añadir campos adicionales.";
    }

    /**
     * Demo function to test Gemini integration (accessible via web)
     *
     * @return View
     */
    public function demo(): View {
        return view('health-tips.demo');
    }

    /**
     * Display a listing of the resource.
     */
    public function index() {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreHealthTipsRequest $request) {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(HealthTips $healthTips) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HealthTips $healthTips) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateHealthTipsRequest $request, HealthTips $healthTips) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HealthTips $healthTips) {
        //
    }
}
