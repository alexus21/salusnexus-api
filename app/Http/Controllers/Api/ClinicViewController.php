<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClinicViewRequest;
use App\Http\Requests\UpdateClinicViewRequest;
use App\Models\ClinicView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClinicViewController extends Controller {
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
        if (!Auth::check() || Auth::user()->user_rol != 'paciente' || !Auth::user()->verified) {
            return response()->json(['message' => 'Acceso no autorizado'], 401);
        }

        $clinicView = ClinicView::where([
            'patient_id' => $request->patient_id,
            'clinic_id' => $request->clinic_id
        ])->first();

        if ($clinicView) {
            $clinicView->increment('view_count');
        } else {
            $clinicView = ClinicView::create([
                'patient_id' => $request->patient_id,
                'clinic_id' => $request->clinic_id,
                'view_count' => 1
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $clinicView
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ClinicView $clinicView) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClinicView $clinicView) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClinicViewRequest $request, ClinicView $clinicView) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClinicView $clinicView) {
        //
    }
}
