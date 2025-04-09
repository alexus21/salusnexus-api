<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentCardRequest;
use App\Http\Requests\UpdatePaymentCardRequest;
use App\Models\PaymentCard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentCardController extends Controller {
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
    public function store(Request $request) {
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
        ];

        $validation = Validator::make($request->all(), $rules, $messages);

        if ($validation->fails()){
            return array([
                'status' => false,
                'message' => 'Ocurrió un error al validar los datos.',
                'errors' => $validation->errors(),
            ]);
        }

        $paymentCard = PaymentCard::create([
            'card_number' => $request->card_number,
            'cardholder_name' => $request->cardholder_name,
            'expiration_date' => $request->expiration_date,
            'payment_provider' => strtolower($request->payment_provider),
        ]);

        return array([
            'status' => true,
            'message' => 'Tarjeta de pago creada exitosamente.',
            'payment_card' => $paymentCard,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentCard $paymentCard) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaymentCard $paymentCard) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaymentCardRequest $request, PaymentCard $paymentCard) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentCard $paymentCard) {
        //
    }
}
