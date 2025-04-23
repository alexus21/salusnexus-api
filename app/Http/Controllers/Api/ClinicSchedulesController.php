<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClinicSchedules;
use App\Models\MedicalClinic;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ClinicSchedulesController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index() {
        if (!Auth::check()) {
            return response()->json(['message' => 'Acceso no autorizado', 'status' => false], 401);
        }

        $schedules = ClinicSchedules::all();

        if ($schedules->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No se encontraron horarios'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $schedules
        ], 200);
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
        if (!Auth::check() && Auth::user()->role !== 'profesional' || !Auth::user()->verified) {
            return response()->json(['message' => 'Acceso no autorizado', 'status' => false], 401);
        }

        $professional_id = DB::table('professional_profiles')
            ->where('user_id', Auth::user()->id)
            ->value('id');

        // Verificar si clinic_id corresponde al professional_id
        $clinic = MedicalClinic::verifyClinicOwnership($request->clinic_id, $professional_id);

        if (!$clinic) {
            return response()->json([
                'status' => false,
                'message' => 'Acceso no autorizado.'
            ], 403);
        }

        try {
            foreach ($request->days as $day) {
                $existingSchedule = ClinicSchedules::where('clinic_id', $request->clinic_id)
                    ->where('day_of_the_week', $day['day_of_the_week'])
                    ->first();

                if ($day['open']) {
                    if ($existingSchedule) {
                        $updates = [];
                        if ($existingSchedule->start_time !== $day['start_time']) {
                            $updates['start_time'] = $day['start_time'];
                        }
                        if ($existingSchedule->end_time !== $day['end_time']) {
                            $updates['end_time'] = $day['end_time'];
                        }

                        if (empty($updates)) {
                            continue;
                        }

                        DB::table('clinic_schedules')
                            ->where('id', $existingSchedule->id)
                            ->update($updates);
                    } else{
                        ClinicSchedules::create([
                            'clinic_id' => $request->clinic_id,
                            'day_of_the_week' => $day['day_of_the_week'],
                            'start_time' => $day['start_time'],
                            'end_time' => $day['end_time'],
                        ]);
                    }
                } else {
                    if ($existingSchedule) {
                        $existingSchedule->delete();
                    }
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Horarios procesados correctamente'
            ], 201);
        } catch (Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => 'Error al procesar los horarios: ' . $ex->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse {
        if (!Auth::check()) {
            return response()->json(['message' => 'Acceso no autorizado', 'status' => false], 401);
        }

        try {
            $schedule = DB::table('clinic_schedules')
                ->where('id', $id)
                ->first();

            if (!$schedule) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontraró información de este horario'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $schedule
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener el horario: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showByClinic($clinic_id): JsonResponse {
        if (!Auth::check()) {
            return response()->json(['message' => 'Acceso no autorizado', 'status' => false], 401);
        }

        try {
            $schedule = DB::table('clinic_schedules')
                ->where('clinic_id', $clinic_id)
                ->get();

            if (!$schedule) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontraron horarios para esta clínica'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $schedule
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener el horario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClinicSchedules $clinicSchedules) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id) {
        if (!Auth::check() && Auth::user()->role !== 'profesional' || !Auth::user()->verified) {
            return response()->json(['message' => 'Acceso no autorizado', 'status' => false], 401);
        }

        $professional_id = DB::table('professional_profiles')
            ->where('user_id', Auth::user()->id)
            ->value('id');

        $clinic_id = DB::table('clinic_schedules')
            ->where('id', $id)
            ->value('clinic_id');

        $clinic = MedicalClinic::verifyClinicOwnership($clinic_id, $professional_id);

        if (!$clinic) {
            return response()->json([
                'status' => false,
                'message' => 'Acceso no autorizado.'
            ], 403);
        }

        $rules = [
            'day_of_the_week' => 'required|string|max:20',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ];

        $messages = [
            'day_of_the_week.required' => 'El campo day_of_the_week es obligatorio.',
            'day_of_the_week.string' => 'El campo day_of_the_week debe ser una cadena de texto.',
            'day_of_the_week.max' => 'El campo day_of_the_week no puede tener más de 20 caracteres.',
            'start_time.required' => 'El campo start_time es obligatorio.',
            'start_time.date_format' => 'El campo start_time debe tener el formato HH:MM.',
            'end_time.required' => 'El campo end_time es obligatorio.',
            'end_time.date_format' => 'El campo end_time debe tener el formato HH:MM.',
            'end_time.after' => 'El campo end_time debe ser posterior a start_time.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $schedule = ClinicSchedules::find($id);

            if (!$schedule) {
                return response()->json([
                    'status' => false,
                    'message' => 'Horario no encontrado'
                ], 404);
            }

            // Verificar si el horario ya existe
            $existingSchedule = ClinicSchedules::where('clinic_id', $schedule->clinic_id)
                ->where('day_of_the_week', $request->day_of_the_week)
                ->where('start_time', $request->start_time)
                ->where('end_time', $request->end_time)
                ->where('id', '!=', $id)
                ->first();

            if ($existingSchedule) {
                return response()->json([
                    'status' => false,
                    'message' => 'Este horario ya existe para esta clínica.'
                ], 409);
            }

            $schedule->update($request->all());

            return response()->json([
                'status' => true,
                'data' => $schedule,
                'message' => 'Horario actualizado correctamente'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar el horario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {
        if (!Auth::check() && Auth::user()->role !== 'profesional' || !Auth::user()->verified) {
            return response()->json(['message' => 'Acceso no autorizado', 'status' => false], 401);
        }

        $professional_id = DB::table('professional_profiles')
            ->where('user_id', Auth::user()->id)
            ->value('id');

        $clinic_id = DB::table('clinic_schedules')
            ->where('id', $id)
            ->value('clinic_id');

        $clinic = MedicalClinic::verifyClinicOwnership($clinic_id, $professional_id);

        if (!$clinic) {
            return response()->json([
                'status' => false,
                'message' => 'Acceso no autorizado.'
            ], 403);
        }

        try {
            $schedule = ClinicSchedules::find($id);

            if (!$schedule) {
                return response()->json([
                    'status' => false,
                    'message' => 'Horario no encontrado'
                ], 404);
            }

            $schedule->delete();

            return response()->json([
                'status' => true,
                'message' => 'Horario eliminado correctamente'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al eliminar el horario: ' . $e->getMessage()
            ], 500);
        }
    }
}
