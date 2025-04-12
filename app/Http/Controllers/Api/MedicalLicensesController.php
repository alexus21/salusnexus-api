<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMedicalLicensesRequest;
use App\Http\Requests\UpdateMedicalLicensesRequest;
use App\Models\MedicalLicenses;

class MedicalLicensesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMedicalLicensesRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(MedicalLicenses $medicalLicenses)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MedicalLicenses $medicalLicenses)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMedicalLicensesRequest $request, MedicalLicenses $medicalLicenses)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicalLicenses $medicalLicenses)
    {
        //
    }
}
