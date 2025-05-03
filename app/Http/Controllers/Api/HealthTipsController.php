<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHealthTipsRequest;
use App\Http\Requests\UpdateHealthTipsRequest;
use App\Models\HealthTips;
use DeepSeek\DeepSeekClient;
use Illuminate\Http\JsonResponse;

class HealthTipsController extends Controller {
    public function aiTipDemo(): JsonResponse {
        $response = DeepSeekClient::build(env('DEEP_SEEK_API_KEY'))
            ->query('¿Cual es la mejor manera de prevenir el cáncer?')
            ->run();

        $decodedResponse = json_decode($response, true);
        $content = $decodedResponse['choices'][0]['message']['content'] ?? 'No content available';

        return response()->json([
            'content' => $content,
        ]);
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
