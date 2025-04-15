<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubscriptionsRequest;
use App\Http\Requests\UpdateSubscriptionsRequest;
use App\Models\PaymentCard;
use App\Models\Subscriptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SubscriptionsController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index() {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): JsonResponse {
        if (!Auth::check()) {
            return response()->json([
                'message' => 'No autorizado',
            ], 401);
        }

        $rules = [
            'card_number' => 'required|string|max:19',
            'cardholder_name' => 'required|string|max:255',
            'expiration_date' => 'required|date_format:m/y',
            'payment_provider' => 'required|string|in:visa,mastercard,maestro,paypal,diners,amex',
        ];

        $messages = [
            'card_number.required' => 'El número de tarjeta es obligatorio.',
            'cardholder_name.required' => 'El nombre del titular de la tarjeta es obligatorio.',
            'expiration_date.required' => 'La fecha de expiración es obligatoria.',
            'payment_provider.required' => 'El proveedor de pago es obligatorio.',
            'payment_provider.string' => 'El proveedor de pago debe ser una cadena de texto.',
            'payment_provider.in' => 'El proveedor de pago debe ser uno de los siguientes: VISA, Mastercard, Maestro, PayPal, Diners Club, American Express.',
        ];

        $validation = Validator::make($request->all(), $rules, $messages);

        if ($validation->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al validar los datos.',
                'errors' => $validation->errors(),
            ], 422);
        }

        $user_id = Auth::user()->id;
        $user_rol = Auth::user()->user_rol;

        $user_rol == 'paciente' ?
            $subscription_type = 'paciente_avanzado' : $subscription_type = 'profesional_avanzado';

        // Check if the user already has a subscription
        $subscription = Subscriptions::where('user_id', $user_id)
            ->where('subscription_status', 'activa')
            ->where('subscription_type', $subscription_type)
            ->where('subscription_period', $request->subscription_period)
            ->first();

        if ($subscription) {
            return response()->json([
                'message' => 'Ya tienes una suscripción activa.',
            ], 422);
        }

        $paymentCard = PaymentCard::create([
            'card_number' => $request->card_number,
            'cardholder_name' => $request->cardholder_name,
            'expiration_date' => $request->expiration_date,
            'payment_provider' => strtolower($request->payment_provider),
        ]);

        if (!$paymentCard) {
            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al crear la tarjeta de pago.',
                'errors' => $validation->errors(),
            ], 422);
        }

        $current_subscription = Subscriptions::where('user_id', $user_id)
            ->first();

        $current_subscription->subscription_status = 'activa';
        $current_subscription->subscription_type = $subscription_type;
        $current_subscription->subscription_period = $request->subscription_period;
        $current_subscription->start_date = now();
        $current_subscription->end_date = now()->addDays(14);
        $current_subscription->trial_ends_at = now()->addDays(14);
        $current_subscription->auto_renew = false;
        $current_subscription->payment_card_id = $paymentCard->id;
        $current_subscription->save();

        return response()->json([
            'status' => true,
            'message' => 'Te has suscrito exitosamente a SalusNexus.',
            'subscription' => $subscription,
        ], 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(int $id, string $user_rol, $subscription_period): bool {
        // Check if the user already has a subscription
        $existingSubscription = Subscriptions::where('user_id', Auth::user()->id)
            ->where('subscription_status', 'activa')
            ->first();

        if ($existingSubscription) {
            return false;
        }

        $user_rol == 'paciente' ?
            $subscription_type = 'paciente_gratis' : $subscription_type = 'profesional_gratis';

        $subscription = Subscriptions::create([
            'user_id' => $id,
            'subscription_type' => $subscription_type,
            'subscription_status' => 'activa',
            'subscription_period' => $subscription_period,
            'start_date' => now(),
            'end_date' => now()->addYear(),
            'trial_ends_at' => now()->addYear(),
            'auto_renew' => false,
            'payment_card_id' => null,
        ]);

        return $subscription == null;
    }

    /**
     * Display the specified resource.
     */
    public function show(Subscriptions $subscriptions) {
        //
    }

    public function mySubscription(): JsonResponse {
        $user_id = Auth::user()->id;

        $subscription = Subscriptions::where('user_id', $user_id)
            ->where('subscription_status', 'activa')
            ->first();

        if ($subscription) {
            return response()->json([
                'status' => true,
                'message' => 'Ya posees una suscripción activa.',
                'subscription' => $subscription,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No tienes una suscripción activa. ¿Deseas agregar una?',
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subscriptions $subscriptions) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubscriptionsRequest $request, Subscriptions $subscriptions) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subscriptions $subscriptions) {
        //
    }
}
