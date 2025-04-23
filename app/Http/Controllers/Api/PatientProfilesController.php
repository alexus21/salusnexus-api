<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientProfilesRequest;
use App\Http\Requests\UpdatePatientProfilesRequest;
use App\Models\PatientProfiles;
use App\Models\User;
use App\Rules\DUIRule;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PatientProfilesController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index() {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request) {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePatientProfilesRequest $request) {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PatientProfiles $patientProfiles) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PatientProfiles $patientProfiles) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse {
        if (!Auth::check() || !Auth::user()->verified || Auth::user()->id != $id) {
            return response()->json(['message' => 'Acceso no autorizado'], 401);
        }

        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            if ($request->has('first_name')) {
                $user->first_name = $request->first_name;
            }

            if ($request->has('last_name')) {
                $user->last_name = $request->last_name;
            }

            if ($request->has('phone')) {
                $user->phone = $request->phone;
            }

            if ($request->has('dui')) {
                $user->dui = $request->dui;
            }

            if ($request->has('date_of_birth')) {
                $user->date_of_birth = $request->date_of_birth;
            }

            if ($request->has('home_address')) {
                $user->address = $request->home_address;
            }

            $user->save();

            $patient_id = DB::table('patient_profiles')->where('user_id', $id)->value('id');

            $patient = PatientProfiles::find($patient_id);

            if ($request->has('emergency_contact_name')) {
                $patient->emergency_contact_name = $request->emergency_contact_name;
            }

            if ($request->has('emergency_contact_phone')) {
                $patient->emergency_contact_phone = $request->emergency_contact_phone;
            }

            $patient->save();

            $user = (new User())->getUserProfile($id);

            return response()->json([
                'status' => true,
                'message' => 'Información actualizada correctamente',
                'data' => $user
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PatientProfiles $patientProfiles) {
        //
    }

    public function verifyPatientAccount(Request $request): JsonResponse {
        $rules = [
            'home_address' => 'required|string',
            'home_latitude' => 'required|numeric',
            'home_longitude' => 'required|numeric',
            'home_address_reference' => 'nullable|string',
            'emergency_contact_name' => 'required|string',
            'emergency_contact_phone' => 'required|string',
            'profile_photo_path' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'dui' => ['required', 'string', 'unique:users,dui', new DUIRule()],
        ];

        $messages = [
            'home_address.required' => 'La dirección de casa es requerida',
            'home_address.string' => 'La dirección de casa debe ser texto',
            'home_latitude.required' => 'La latitud de casa es requerida',
            'home_latitude.numeric' => 'La latitud de casa debe ser un número',
            'home_longitude.required' => 'La longitud de casa es requerida',
            'home_longitude.numeric' => 'La longitud de casa debe ser un número',
            'home_address_reference.string' => 'La referencia de la dirección de casa debe ser texto',
            'emergency_contact_name.required' => 'El nombre del contacto de emergencia es requerido',
            'emergency_contact_name.string' => 'El nombre del contacto de emergencia debe ser texto',
            'emergency_contact_phone.required' => 'El teléfono del contacto de emergencia es requerido',
            'emergency_contact_phone.string' => 'El teléfono del contacto de emergencia debe ser texto',
            'profile_photo_path.required' => 'La foto de perfil es requerida',
            'profile_photo_path.image' => 'La foto de perfil debe ser una imagen válida',
            'profile_photo_path.mimes' => 'La foto de perfil debe ser un archivo de tipo jpeg, png o jpg',
            'profile_photo_path.max' => 'La foto de perfil no debe exceder los 2MB',
            'dui.required' => 'El DUI es requerido',
            'dui.string' => 'El DUI debe ser texto',
            'dui.unique' => 'El DUI ya está en uso',
            'dui.regex' => 'El DUI no tiene un formato válido',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        // Asegurarse de que el teléfono comience con el código de país "+503" (El Salvador)
        $phone = str_starts_with($request->emergency_contact_phone, "+503") ?
            $request->emergency_contact_phone : "+503 " . $request->emergency_contact_phone;
        // Formatear el teléfono al estilo "+503 XXXX-XXXX"
        $phone = preg_replace('/(\+503)\s?(\d{4})(\d{4})/', '$1 $2-$3', $phone);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors(),
            ], 422);
        }

        try {
            if (!$request->hasFile('profile_photo_path')) {
                return response()->json([
                    'success' => false,
                    'message' => 'La imagen de perfil es requerida'
                ], 422);
            }

            // Generar nombre único para la imagen
            $imageName = Str::uuid() . '.' . $request->profile_photo_path->extension();

            // Obtener ruta anterior de la imagen
            $oldImagePath = DB::table('users')
                ->where('id', Auth::id())
                ->value('profile_photo_path');

            if (!Storage::disk('s3')->exists('images')) {
                Storage::disk('s3')->makeDirectory('images');
            }

            // Verificar y eliminar la imagen anterior si existe
            if ($oldImagePath && Storage::disk('s3')->exists($oldImagePath)) {
                Storage::disk('s3')->delete($oldImagePath);
            }

            // Guardar la imagen en el disco (puedes usar public, s3, etc.)
            $path = $request->file('profile_photo_path')->storeAs('images', $imageName, 's3');

            // URL pública de la imagen (si está en storage/public)
            $url = Storage::disk('s3')->url($path);

            // Guardar la información del usuario en la base de datos
            DB::table('patient_profiles')->where('user_id', Auth::user()->id)->update([
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $phone,
            ]);

            DB::table('users')->where('id', Auth::user()->id)->update([
                'address' => $request->home_address,
                'latitude' => $request->home_latitude,
                'longitude' => $request->home_longitude,
                'address_reference' => $request->home_address_reference,
                'profile_photo_path' => $path,
                'dui' => $request->dui,
                'verified' => true,
                'email_verified_at' => Carbon::now(),
            ]);

            // Crear suscripción gratuita
            (new SubscriptionsController())->store(Auth::user()->id, 'paciente', null);

            $user = ((new User())->getUserInfoByItsId(Auth::user()->id));

            return response()->json([
                'success' => true,
                'user' => $user,
                'message' => 'Perfil verificado correctamente',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al subir la imagen: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPatientsAge(): JsonResponse {
        if (!Auth::check() && Auth::user()->user_rol !== 'profesional') {
            return response()->json([
                'message' => 'No autorizado',
            ], 401);
        }

        try {
            $patient = DB::table('patient_profiles')
                ->select('users.date_of_birth')
                ->join('users', 'patient_profiles.user_id', '=', 'users.id')
                ->get();

            if (!$patient) {
                return response()->json([
                    'status' => false,
                    'message' => 'Paciente no encontrado'
                ], 404);
            }

            // Obtener la edad de cada date_of_birth de la colección
            $ages = $patient->map(function ($item) {
                return Carbon::parse($item->date_of_birth)->age;
            });

            // Ordenar de mayor a menor
            $ages = $ages->sortDesc();

            // Filtrar: edad - cantidad de pacientes con esa edad:
            $ages = $ages->countBy()->map(function ($item, $key) {
                return [
                    'age' => $key,
                    'count' => $item
                ];
            })->values();

            // Devolver la edad de cada paciente
            return response()->json([
                'status' => true,
                'ages' => $ages,
                'message' => 'Edad de los pacientes obtenida correctamente'
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener la edad del paciente',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getPatientsCloseToArea(): JsonResponse {
        if (!Auth::check() && Auth::user()->user_rol !== 'profesional') {
            return response()->json([
                'message' => 'No autorizado',
            ], 401);
        }

        try {
            $patients = DB::table('patient_profiles')
                ->select('users.latitude', 'users.longitude')
                ->join('users', 'patient_profiles.user_id', '=', 'users.id')
                ->get();

            if ($patients->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontraron pacientes'
                ], 404);
            }

            // Obtener la distancia entre cada paciente y el área de trabajo
            $areaLatitude = Auth::user()->latitude;
            $areaLongitude = Auth::user()->longitude;

            $patients->transform(function ($item) use ($areaLatitude, $areaLongitude) {
                $distance = $this->haversineGreatCircleDistance($areaLatitude, $areaLongitude, $item->latitude, $item->longitude);
                $roundedDistance = round($distance, 2);
                return [
                    'latitude' => $item->latitude,
                    'longitude' => $item->longitude,
                    'distance' => $roundedDistance
                ];
            });

            // Filtrar pacientes a 10 km o menos
            $closePatients = $patients->filter(function ($item) {
                return $item['distance'] <= 10;
            });

            // Ordenar por distancia
            $closePatients = $closePatients->sortBy('distance', SORT_NATURAL, true);

            // Obtener el número de pacientes cercanos
            $number = $closePatients->count();

            return response()->json([
                'status' => true,
                'patients' => $closePatients->values(),
                'number' => $number,
                'message' => 'Pacientes cercanos obtenidos correctamente'
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener los pacientes cercanos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function haversineGreatCircleDistance($areaLatitude, $areaLongitude, $latitude, $longitude) {
        $earthRadius = 6371; // Radio de la Tierra en kilómetros

        $latFrom = deg2rad($areaLatitude);
        $lonFrom = deg2rad($areaLongitude);
        $latTo = deg2rad($latitude);
        $lonTo = deg2rad($longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($latFrom) * cos($latTo) *
            sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // Distancia en kilómetros
    }
}
