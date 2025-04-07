<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorespecialitiesRequest;
use App\Http\Requests\UpdatespecialitiesRequest;
use App\Models\Specialities;
use Illuminate\Http\JsonResponse;

class SpecialitiesController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse {
        $specialities = Specialities::select('id', 'name', 'description')->get();
        return response()->json($specialities);
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
    public function store(StorespecialitiesRequest $request) {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Specialities $specialities) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Specialities $specialities) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatespecialitiesRequest $request, Specialities $specialities) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Specialities $specialities) {
        //
    }
}
