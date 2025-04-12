<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProfessionalSpecialitiesRequest;
use App\Http\Requests\UpdateProfessionalSpecialitiesRequest;
use App\Models\ProfessionalSpecialities;

class ProfessionalSpecialitiesController extends Controller
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
    public function store(StoreProfessionalSpecialitiesRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ProfessionalSpecialities $professionalSpecialities)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProfessionalSpecialities $professionalSpecialities)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProfessionalSpecialitiesRequest $request, ProfessionalSpecialities $professionalSpecialities)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProfessionalSpecialities $professionalSpecialities)
    {
        //
    }
}
