<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateMedicalClinicRequest;
use App\Models\MedicalClinic;
use App\Models\Professional_Specialities;
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

        $clinic = (new MedicalClinic())->getClinicInfo(null);

        if ($clinic->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No se encontraron clínicas médicas'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $clinic
        ], 201);
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
            'clinic_address' => 'required|string|max:512',
            'clinic_address_reference' => 'string|max:512',
            'clinic_latitude' => 'numeric',
            'clinic_longitude' => 'numeric',
            'city_id' => 'required|exists:cities,id',
            'facade_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
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
            'clinic_address.required' => 'La dirección es obligatoria.',
            'clinic_address.string' => 'La dirección debe ser una cadena de texto.',
            'clinic_address.max' => 'La dirección no debe exceder los 512 caracteres.',
            'city_id.required' => 'La ciudad es obligatoria.',
            'facade_photo.required' => 'La foto de la fachada es obligatoria.',
            'facade_photo.image' => 'La foto de la fachada debe ser una imagen.',
            'facade_photo.mimes' => 'La foto de la fachada debe ser un archivo de tipo: jpeg, png, jpg, gif.',
            'facade_photo.max' => 'La foto de la fachada no debe exceder los 2MB.',
            'speciality_type.required' => 'El tipo de especialidad es obligatorio.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Verificamos que exista el perfil profesional
            $professional_id = DB::table('professional_profiles')
                ->where('user_id', Auth::id())
                ->value('id');

            if (!$professional_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontró el perfil profesional'
                ], 404);
            }

            // Crear el directorio si no existe
            if (!Storage::disk('s3')->exists('images/clinics_pics')) {
                Storage::disk('s3')->makeDirectory('images/clinics_pics');
            }

            // Procesamos la foto de fachada
            $imageName = Str::uuid() . '.' . $request->facade_photo->extension();
            $facadePath = $request->file('facade_photo')->storeAs('images/clinics_pics', $imageName, 's3');
            $facadeUrl = Storage::disk('s3')->url($facadePath);

            // Creamos la clínica médica
            $medicalClinic = MedicalClinic::create([
                'clinic_name' => $request->clinic_name,
                'address' => $request->clinic_address,
                'clinic_address_reference' => $request->clinic_address_reference,
                'clinic_latitude' => $request->clinic_latitude,
                'clinic_longitude' => $request->clinic_longitude,
                'description' => $request->description,
                'city_id' => $request->city_id,
                'professional_id' => $professional_id,
                'speciality_type' => strtolower($request->speciality_type),
                'facade_photo' => $facadePath,
                'waiting_room_photo' => '-',
                'office_photo' => '-',
            ]);

            // Procesamos la foto de sala de espera si existe
            if ($request->hasFile('waiting_room_photo')) {
                $waitingRoomImageName = Str::uuid() . '.' . $request->waiting_room_photo->extension();
                $waitingRoomPath = $request->file('waiting_room_photo')->storeAs('images/clinics_pics', $waitingRoomImageName, 's3');
                $waitingRoomUrl = Storage::disk('s3')->url($waitingRoomPath);
                $medicalClinic->waiting_room_photo = $waitingRoomPath;
            }

            // Procesamos la foto de oficina si existe
            if ($request->hasFile('office_photo')) {
                $officeImageName = Str::uuid() . '.' . $request->office_photo->extension();
                $officePath = $request->file('office_photo')->storeAs('images/clinics_pics', $officeImageName, 's3');
                $officeUrl = Storage::disk('s3')->url($officePath);
                $medicalClinic->office_photo = $officePath;
            }

            // Guardamos los cambios si se han añadido fotos adicionales
            if ($request->hasFile('waiting_room_photo') || $request->hasFile('office_photo')) {
                $medicalClinic->save();
            }

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

        $clinic = (new MedicalClinic())->getClinicInfo($id);

        return response()->json([
            'status' => true,
            'data' => $clinic
        ], 200);
    }

    public function showMyClinic(): JsonResponse {
        if (!Auth::check()) {
            return response()->json(['message' => 'Acceso no autorizado'], 401);
        }

        $patient_id = DB::table('professional_profiles')
            ->where('user_id', Auth::id())
            ->value('id');

        try{
            $clinic = MedicalClinic::getMyClinic($patient_id);

            if (!$clinic) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontraron clínicas médicas'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $clinic
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al procesar la petición: ' . $e->getMessage()
            ], 500);
        }
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
