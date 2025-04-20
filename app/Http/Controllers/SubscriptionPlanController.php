<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    /**
     * Display a listing of the subscription plans with their features and prices.
     */
    public function index()
    {
        $plans = SubscriptionPlan::with('features')->where('is_active', true)->get();
        return response()->json($plans);
    }

    /**
     * Display a subscription plan by type with its features.
     */
    public function showByType(Request $request)
    {
        $type = $request->query('subscription_type');

        if (!$type) {
            return response()->json(['error' => 'subscription_type is required'], 400);
        }

        $plan = SubscriptionPlan::with(['features' => function($query) use ($type) {
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
    public function showByTypeParam($type)
    {
        if (!$type) {
            return response()->json(['error' => 'subscription_type is required'], 400);
        }

        $plan = SubscriptionPlan::with(['features' => function($query) use ($type) {
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
}
