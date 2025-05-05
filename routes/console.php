<?php

use App\Http\Controllers\Api\HealthTipsController;
use App\Mail\WeekleHealthTipMail;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    try {
        $send_tips_to = DB::table('subscriptions')
            ->join('users', 'subscriptions.user_id', '=', 'users.id')
            ->join('patient_profiles', 'users.id', '=', 'patient_profiles.user_id')
            ->join('patient_diseases', 'patient_profiles.id', '=', 'patient_diseases.patient_profile_id')
            ->join('diseases', 'patient_diseases.disease_id', '=', 'diseases.id')
            ->where('subscriptions.subscription_type', 'paciente_avanzado')
            ->where('patient_profiles.wants_health_tips', true)
            ->select('users.email', DB::raw("STRING_AGG(diseases.id::TEXT, ', ') AS disease_ids"))
            ->groupBy('users.email')
            ->get();

        foreach ($send_tips_to as $patient) {
            $disease_ids = explode(', ', $patient->disease_ids);
            $email = $patient->email;

            if (empty($disease_ids)){
                Log::info('No diseases found for patient: ' . $email);
                continue;
            }

            $response = app(HealthTipsController::class)->generateTip(new Request([
                'disease_ids' => $disease_ids,
                'service' => 'gemini_openai',
            ]));

            if(!$response){
                Log::info('No response from HealthTipsController for patient: ' . $email);
                continue;
            }

            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getContent(), true);

                $title = $data['health_tip']['title'];
                $greeting = $data['health_tip']['greeting'];
                $tip = $data['health_tip']['tip'];

                $details = [
                    'subject' => $title,
                    'greeting' => $greeting,
                    'tip' => $tip,
                ];

                Mail::to($email)->send(new WeekleHealthTipMail($details));
            } else {
                Log::error('Error generando los tips de salud: ' . $response->getContent());
            }
        }
    } catch (Exception $exception) {
        Log::error('Error en la tarea: ' . $exception->getMessage());
    }
})
//    ->everyMinute()
        ->weeklyOn(3, '10:00')
//        ->everyTenSeconds()
    ->timezone('America/El_Salvador')
    ->name('weekly_health_tip');
