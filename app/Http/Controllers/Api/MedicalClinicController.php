<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMedicalClinicRequest;
use App\Http\Requests\UpdateMedicalClinicRequest;
use App\Models\MedicalClinic;
use App\Models\Medications;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MedicalClinicController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse {
        if (!Auth::check()) {
            return response()->json(['message' => 'Acceso no autorizado'], 401);
        }

        return response()->json((new MedicalClinic())->getClinicInfo(null), 201);
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
    public function store(Request $request): JsonResponse {
        if (!Auth::check() || Auth::user()->user_rol != 'profesional' || !Auth::user()->verified) {
            return response()->json(['message' => 'Acceso no autorizado'], 401);
        }

        $rules = [
            'clinic_name' => 'required|string|max:200',
            'description' => 'string|max:512',
            'address' => 'required|string|max:512',
            'city_id' => 'required|exists:cities,id',
//            'facade_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'waiting_room_photo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'office_photo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'speciality_type' => 'required|in:primaria,secundaria',
        ];

        $messages = [
            'clinic_name.required' => 'El nombre de la clínica es obligatorio.',
            'clinic_name.string' => 'El nombre de la clínica debe ser una cadena de texto.',
            'clinic_name.max' => 'El nombre de la clínica no debe exceder los 200 caracteres.',
            'description.string' => 'La descripción debe ser una cadena de texto.',
            'description.max' => 'La descripción no debe exceder los 512 caracteres.',
            'address.required' => 'La dirección es obligatoria.',
            'address.string' => 'La dirección debe ser una cadena de texto.',
            'address.max' => 'La dirección no debe exceder los 512 caracteres.',
            'city_id.required' => 'La ciudad es obligatoria.',
            /*'facade_photo.required' => 'La foto de la fachada es obligatoria.',
            'facade_photo.image' => 'La foto de la fachada debe ser una imagen.',
            'facade_photo.mimes' => 'La foto de la fachada debe ser un archivo de tipo: jpeg, png, jpg, gif.',
            'facade_photo.max' => 'La foto de la fachada no debe exceder los 2MB.',*/
            'speciality_type.required' => 'El tipo de especialidad es obligatorio.',
        ];

        if ($request->has('waiting_room_photo')) {
            $rules['waiting_room_photo'] = 'image|mimes:jpeg,png,jpg,gif|max:2048';
            $messages['waiting_room_photo.image'] = 'La foto de la sala de espera debe ser una imagen.';
            $messages['waiting_room_photo.mimes'] = 'La foto de la sala de espera debe ser un archivo de tipo: jpeg, png, jpg, gif.';
        }

        if ($request->has('office_photo')) {
            $rules['office_photo'] = 'image|mimes:jpeg,png,jpg,gif|max:2048';
            $messages['office_photo.image'] = 'La foto de la oficina debe ser una imagen.';
            $messages['office_photo.mimes'] = 'La foto de la oficina debe ser un archivo de tipo: jpeg, png, jpg, gif.';
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $professional_id = DB::table('professional_profiles')
                ->where('user_id', Auth::id())
                ->value('id');

            $medicalClinic = new MedicalClinic();
            $medicalClinic->clinic_name = $request->clinic_name;
            $medicalClinic->description = $request->description;
            $medicalClinic->city_id = $request->city_id;
            $medicalClinic->professional_id = $professional_id;
            $medicalClinic->speciality_type = strtolower($request->speciality_type);

            $user = new User();
            $user->address = $request->address;

            /*if(!$request->hasFile('facade_photo')){
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró ninguna imagen'
                ], 400);
            }

            // Generar nombre único para la imagen
            $imageName = Str::uuid() . '.' . $request->facade_photo->extension();

            // Obtener ruta anterior de la imagen
            $oldImagePath = DB::table('users')
                ->where('id', Auth::id())
                ->value('facade_photo');

            if (!Storage::disk('s3')->exists('images/clinics')) {
                Storage::disk('s3')->makeDirectory('images/clinics');
            }

            // Verificar y eliminar la imagen anterior si existe
            if ($oldImagePath && Storage::disk('s3')->exists($oldImagePath)) {
                Storage::disk('s3')->delete($oldImagePath);
            }

            // Guardar la imagen en el disco (puedes usar public, s3, etc.)
            $path = $request->file('facade_photo')->storeAs('images/clinics', $imageName, 's3');

            // URL pública de la imagen (si está en storage/public)
            $url = Storage::disk('s3')->url($path);*/

            // Guardar la información de la clinica en la base de datos
            $medicalClinic->facade_photo = '-';

            if($request->hasFile('waiting_room_photo')){
                $waitingRoomImageName = Str::uuid() . '.' . $request->waiting_room_photo->extension();
                $waitingRoomPath = $request->file('waiting_room_photo')->storeAs('images/clinics', $waitingRoomImageName, 's3');
                $medicalClinic->waiting_room_photo = Storage::disk('s3')->url($waitingRoomPath);
            }

            if($request->hasFile('office_photo')){
                $officeImageName = Str::uuid() . '.' . $request->office_photo->extension();
                $officePath = $request->file('office_photo')->storeAs('images/clinics', $officeImageName, 's3');
                $medicalClinic->office_photo = Storage::disk('s3')->url($officePath);
            }

            $medicalClinic->save();

            return response()->json([
                'success' => true,
                'message' => 'Establecimiento clínico agregado exitosamente',
                'data' => $medicalClinic
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la petición: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse {
        if (!Auth::check()) {
            return response()->json(['message' => 'Acceso no autorizado'], 401);
        }

        return response()->json((new MedicalClinic())->getClinicInfo($id), 201);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MedicalClinic $medicalClinic) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMedicalClinicRequest $request, MedicalClinic $medicalClinic) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicalClinic $medicalClinic) {
        //
    }
}
