<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubscriptionsRequest;
use App\Http\Requests\UpdateSubscriptionsRequest;
use App\Models\Subscriptions;
use Illuminate\Http\Request;

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
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(int $id, string $user_rol) {
        $user_rol == 'paciente' ?
            $subscription_type = 'paciente_gratis' : $subscription_type = 'profesional_gratis';

        $subscription = Subscriptions::create([
            'user_id' => $id,
            'subscription_type' => $subscription_type,
            'subscription_status' => 'activa',
            'start_date' => now(),
            'end_date' => now()->addYear(),
            'trial_ends_at' => now()->addYear(),
            'auto_renew' => false,
            'payment_provider_subscription_id' => null,
        ]);

        return $subscription == null;
    }

    /**
     * Display the specified resource.
     */
    public function show(Subscriptions $subscriptions) {
        //
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
