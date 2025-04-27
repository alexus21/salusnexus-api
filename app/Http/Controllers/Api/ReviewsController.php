<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateReviewsRequest;
use App\Models\Reviews;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ReviewsController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse {
        if (!Auth::check() || !Auth::user()->verified) {
            return response()->json(['message' => 'Acceso no autorizado', 'status' => false], 401);
        }

        $reviews = DB::table('reviews')
            ->join('appointments', 'reviews.appointment_id', '=', 'appointments.id')
            ->join('appointment_users', 'appointments.id', '=', 'appointment_users.appointment_id')
            ->join('patient_profiles', 'appointment_users.patient_user_id', '=', 'patient_profiles.id')
            ->join('users', 'patient_profiles.user_id', '=', 'users.id')
            ->where('reviews.is_published', true)
            ->select('reviews.*', 'users.first_name as patient_first_name', 'users.last_name  as patient_last_name')
            ->get();

        if ($reviews->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron reseñas',
                'data' => []
            ], 404);
        }

        return response()->json([
            'message' => 'Reseñas obtenidas con éxito',
            'data' => $reviews
        ], 200);
    }

    public function getAverage($clinic_id): JsonResponse {
        if (!Auth::check() || !Auth::user()->verified) {
            return response()->json(['message' => 'Acceso no autorizado', 'status' => false], 401);
        }

        try{
            $average = round(DB::table('reviews')
                ->join('appointments', 'reviews.appointment_id', '=', 'appointments.id')
                ->join('appointment_users', 'appointments.id', '=', 'appointment_users.appointment_id')
                ->where('appointment_users.clinic_id', $clinic_id)
                ->where('reviews.is_published', true)
                ->avg('reviews.rating'), 2);

            if ($average === null) {
                return response()->json([
                    'message' => 'No se encontraron reseñas para calcular el promedio',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'message' => 'Promedio de reseñas obtenido con éxito',
                'data' => $average
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al obtener el promedio de reseñas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse {
        if (!Auth::check() || Auth::user()->user_rol != 'paciente' || !Auth::user()->verified) {
            return response()->json(['message' => 'Acceso no autorizado', 'status' => false], 401);
        }

        $rules = [
            'appointment_id' => 'required|integer|exists:appointments,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:255',
            'review_datetime' => 'required|date',
        ];

        $messages = [
            'appointment_id.required' => 'El campo appointment_id es obligatorio.',
            'appointment_id.integer' => 'El campo appointment_id debe ser un número entero.',
            'appointment_id.exists' => 'El appointment_id no existe en la base de datos.',
            'rating.required' => 'El campo rating es obligatorio.',
            'rating.integer' => 'El campo rating debe ser un número entero.',
            'rating.min' => 'El campo rating debe ser al menos 1.',
            'rating.max' => 'El campo rating no puede ser mayor a 5.',
            'comment.required' => 'El campo comment es obligatorio.',
            'comment.string' => 'El campo comment debe ser una cadena de texto.',
            'comment.max' => 'El campo comment no puede tener más de 255 caracteres.',
            'review_datetime.required' => 'El campo review_datetime es obligatorio.',
            'review_datetime.date' => 'El campo review_datetime debe ser una fecha válida.',
        ];

        $validatedData = Validator::make($request->all(), $rules, $messages);

        if ($validatedData->fails()) {
            return response()->json([
                'message' => 'Aun no hay reseñas disponibles',
                'status' => false,
            ], 201);
        }

        try {
            // Una review por cita
            $existingReview = Reviews::where('appointment_id', $request->appointment_id)->first();
            if ($existingReview) {
                return response()->json([
                    'message' => 'Ya has dejado una reseña para esta cita',
                    'status' => false
                ], 400);
            }

            $review = Reviews::create([
                'appointment_id' => $request->appointment_id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'review_datetime' => $request->review_datetime,
                'is_published' => true,
                'professional_response' => null,
                'response_datetime' => null,
            ]);

            return response()->json([
                'message' => 'Reseña agregada con éxito',
                'data' => $review
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al crear la reseña',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function addReply(Request $request): JsonResponse {
        if (!Auth::check() || Auth::user()->user_rol != 'profesional' || !Auth::user()->verified) {
            return response()->json(['message' => 'Acceso no autorizado', 'status' => false], 401);
        }

        $rules = [
            'review_id' => 'required|integer|exists:reviews,id',
            'professional_response' => 'required|string|max:255',
            'response_datetime' => 'required|date',
        ];

        $messages = [
            'review_id.required' => 'El campo review_id es obligatorio.',
            'review_id.integer' => 'El campo review_id debe ser un número entero.',
            'review_id.exists' => 'El review_id no existe en la base de datos.',
            'professional_response.required' => 'El campo professional_response es obligatorio.',
            'professional_response.string' => 'El campo professional_response debe ser una cadena de texto.',
            'professional_response.max' => 'El campo professional_response no puede tener más de 255 caracteres.',
            'response_datetime.required' => 'El campo response_datetime es obligatorio.',
            'response_datetime.date' => 'El campo response_datetime debe ser una fecha válida.',
        ];

        $validatedData = Validator::make($request->all(), $rules, $messages);

        if ($validatedData->fails()) {
            return response()->json([
                'message' => $validatedData->errors(),
                'status' => false,
            ], 400);
        }

        try {
            $review = Reviews::find($request->review_id);

            if (!$review) {
                return response()->json([
                    'message' => 'Reseña no encontrada',
                    'status' => false
                ], 404);
            }

            $review->update([
                'professional_response' => $request->professional_response,
                'response_datetime' => $request->response_datetime,
            ]);

            return response()->json([
                'message' => 'Respuesta agregada con éxito',
                'data' => $review
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al agregar la respuesta',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse {
        if (!Auth::check() || !Auth::user()->verified) {
            return response()->json(['message' => 'Acceso no autorizado', 'status' => false], 401);
        }

        $reviews = DB::table('reviews')
            ->join('appointments', 'reviews.appointment_id', '=', 'appointments.id')
            ->join('appointment_users', 'appointments.id', '=', 'appointment_users.appointment_id')
            ->join('patient_profiles', 'appointment_users.patient_user_id', '=', 'patient_profiles.id')
            ->join('users', 'patient_profiles.user_id', '=', 'users.id')
            ->where('reviews.is_published', true)
            ->select('reviews.*', 'users.first_name as patient_first_name', 'users.last_name  as patient_last_name')
            ->where('reviews.id', $id)
            ->get();

        if ($reviews->isEmpty()) {
            return response()->json([
                'message' => 'Aun no hay reseñas disponibles',
                'status' => false,
            ], 201);
        }

        return response()->json([
            'message' => 'Reseña obtenida con éxito',
            'data' => $reviews
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function showByAppointment($appointment_id): JsonResponse {
        if (!Auth::check() || !Auth::user()->verified) {
            return response()->json(['message' => 'Acceso no autorizado', 'status' => false], 401);
        }

        log::info('ID de la cita solicitada: ' . $appointment_id);

        $reviews = DB::table('reviews')
            ->join('appointments', 'reviews.appointment_id', '=', 'appointments.id')
            ->join('appointment_users', 'appointments.id', '=', 'appointment_users.appointment_id')
            ->join('patient_profiles', 'appointment_users.patient_user_id', '=', 'patient_profiles.id')
            ->join('users', 'patient_profiles.user_id', '=', 'users.id')
            ->where('reviews.is_published', true)
            ->select('reviews.*', 'users.first_name as patient_first_name', 'users.last_name  as patient_last_name')
            ->where('reviews.appointment_id', $appointment_id)
            ->get();

        if ($reviews->isEmpty()) {
            return response()->json([
                'message' => 'Aun no hay reseñas disponibles',
                'status' => false,
            ], 201);
        }

        return response()->json([
            'message' => 'Reseña obtenida con éxito',
            'data' => $reviews
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function showByClinic($clinic_id): JsonResponse {
        if (!Auth::check() || !Auth::user()->verified) {
            return response()->json(['message' => 'Acceso no autorizado', 'status' => false], 401);
        }

        log::info('ID de la clinica solicitada: ' . $clinic_id);

        $reviews = DB::table('appointments')
            ->join('reviews', 'appointments.id', '=', 'reviews.appointment_id')
            ->join('appointment_users', 'appointments.id', '=', 'appointment_users.appointment_id')
            ->join('patient_profiles', 'appointment_users.patient_user_id', '=', 'patient_profiles.id')
            ->join('users', 'patient_profiles.user_id', '=', 'users.id')
            ->where('appointment_users.clinic_id', $clinic_id)
            ->where('reviews.is_published', true)
            ->select('reviews.*', 'users.first_name as patient_first_name', 'users.last_name  as patient_last_name')
            ->get();

        return response()->json([
            'message' => 'Reseña obtenida con éxito',
            'data' => $reviews
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reviews $reviews) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateReview(Request $request, $appointment_id): JsonResponse {
        if (!Auth::check() || Auth::user()->user_rol != 'paciente' || !Auth::user()->verified) {
            return response()->json(['message' => 'Acceso no autorizado', 'status' => false], 401);
        }

        $rules = [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:255',
            'review_datetime' => 'required|date',
        ];

        $messages = [
            'rating.required' => 'El campo rating es obligatorio.',
            'rating.integer' => 'El campo rating debe ser un número entero.',
            'rating.min' => 'El campo rating debe ser al menos 1.',
            'rating.max' => 'El campo rating no puede ser mayor a 5.',
            'comment.required' => 'El campo comment es obligatorio.',
            'comment.string' => 'El campo comment debe ser una cadena de texto.',
            'comment.max' => 'El campo comment no puede tener más de 255 caracteres.',
            'review_datetime.required' => 'El campo review_datetime es obligatorio.',
            'review_datetime.date' => 'El campo review_datetime debe ser una fecha válida.',
        ];

        $validatedData = Validator::make($request->all(), $rules, $messages);

        if ($validatedData->fails()) {
            return response()->json([
                'message' => $validatedData->errors(),
                'status' => false,
            ], 400);
        }

        try {
            $review = Reviews::where('appointment_id', $appointment_id)->first();

            if (!$review) {
                return response()->json([
                    'message' => 'Reseña no encontrada',
                    'status' => false
                ], 404);
            }

            $review->update($request->all());

            return response()->json([
                'message' => 'Reseña actualizada con éxito',
                'data' => $review
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar la reseña',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateReply(Request $request, $appointment_id): JsonResponse {
        if (!Auth::check() || Auth::user()->user_rol != 'profesional' || !Auth::user()->verified) {
            return response()->json(['message' => 'Acceso no autorizado', 'status' => false], 401);
        }

        $rules = [
            'professional_response' => 'required|string|max:255',
            'response_datetime' => 'required|date',
        ];

        $messages = [
            'professional_response.required' => 'El campo professional_response es obligatorio.',
            'professional_response.string' => 'El campo professional_response debe ser una cadena de texto.',
            'professional_response.max' => 'El campo professional_response no puede tener más de 255 caracteres.',
            'response_datetime.required' => 'El campo response_datetime es obligatorio.',
            'response_datetime.date' => 'El campo response_datetime debe ser una fecha válida.',
        ];

        $validatedData = Validator::make($request->all(), $rules, $messages);

        if ($validatedData->fails()) {
            return response()->json([
                'message' => $validatedData->errors(),
                'status' => false,
            ], 400);
        }

        try {
            $review = Reviews::where('appointment_id', $appointment_id)->first();

            if (!$review) {
                return response()->json([
                    'message' => 'Reseña no encontrada',
                    'status' => false
                ], 404);
            }

            $review->update($request->all());

            return response()->json([
                'message' => 'Respuesta actualizada con éxito',
                'data' => $review
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar la respuesta',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse {
        if (!Auth::check() || !Auth::user()->verified) {
            return response()->json([
                'message' => 'Acceso no autorizado',
                'status' => false
            ], 401);
        }

        try {
            $review = Reviews::find($id);

            if (!$review) {
                return response()->json([
                    'message' => 'Reseña no encontrada',
                    'status' => false
                ], 404);
            }

            $review->delete();

            return response()->json([
                'message' => 'Reseña eliminada con éxito',
                'status' => true
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar la reseña',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
