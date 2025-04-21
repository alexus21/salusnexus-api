<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubscriptionPlanController extends Controller {
    /**
     * Display a listing of the subscription plans with their features and prices.
     */
    public function index() {
        $plans = SubscriptionPlan::with('features')->where('is_active', true)->get();
        return response()->json($plans);
    }

    /**
     * Display a subscription plan by type with its features.
     */
    public function showByType(Request $request) {
        $type = $request->query('subscription_type');

        if (!$type) {
            return response()->json(['error' => 'subscription_type is required'], 400);
        }

        $plan = SubscriptionPlan::with(['features' => function ($query) use ($type) {
            $query->where('subscription_type', $type);
        }])
            ->where('subscription_type', $type)
            ->where('is_active', true)
            ->first();

        if (!$plan) {
            return response()->json(['error' => 'No plan found'], 404);
        }

        return response()->json($plan);
    }

    /**
     * Display a subscription plan by type with its features (route param).
     */
    public function showByTypeParam($type) {
        if (!$type) {
            return response()->json(['error' => 'subscription_type is required'], 400);
        }

        $plan = SubscriptionPlan::with(['features' => function ($query) use ($type) {
            $query->where('subscription_type', $type);
        }])
            ->where('subscription_type', $type)
            ->where('is_active', true)
            ->first();

        if (!$plan) {
            return response()->json(['error' => 'No plan found'], 404);
        }

        // Solo devolver el valor guardado en discount_percent
        $planArray = $plan->toArray();
        // No se realiza cálculo dinámico
        return response()->json($planArray);
    }

    public function filterByPlan(Request $request): JsonResponse {
        $rules = [
            'plan_type' => 'required|string',
        ];

        $messages = [
            'plan_type.required' => 'El tipo de plan es obligatorio.',
            'plan_type.string' => 'El tipo de plan debe ser una cadena de texto.',
        ];

        $validation = Validator::make($request->all(), $rules, $messages);

        if ($validation->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validation->errors(),
                'status' => false
            ], 422);
        }

        try {
            $plan = SubscriptionPlan::getPlanByType($request->plan_type);

            if ($plan->isEmpty()) {
                return response()->json(['error' => 'No plan found'], 404);
            }

            return response()->json([
                'plan' => $plan,
                'status' => true
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al obtener el plan de suscripción',
                'error' => $e->getMessage(),
                'status' => false
            ], 500);
        }
    }
}
