<?php

use App\Http\Controllers\Api\HealthTipsController;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    try{
        $send_tips_to = DB::table('subscriptions')
            ->join('users', 'subscriptions.user_id', '=', 'users.id')
            ->join('patient_profiles', 'users.id', '=', 'patient_profiles.user_id')
            ->join('patient_diseases', 'patient_profiles.id', '=', 'patient_diseases.patient_profile_id')
            ->join('diseases', 'patient_diseases.disease_id', '=', 'diseases.id')
            ->where('subscriptions.subscription_type', 'paciente_avanzado')
            ->where('patient_profiles.wants_health_tips', true)
            ->select('diseases.id', 'users.email')
            ->get();

        $disease_ids = $send_tips_to->pluck('id')->toArray();

        if (empty($disease_ids)) {
            Log::info('No patients with health tips subscription found.');
            return;
        }

        $response = app(HealthTipsController::class)->generateTip(new Request([
            'disease_ids' => $disease_ids,
            'service' => 'gemini_openai',
        ]));

        if ($response->getStatusCode() === 200) {
            $data = json_decode($response->getContent(), true);
            Log::info(json_encode($data));
        } else {
            Log::error('Error generating health tips: ' . $response->getContent());
        }
    } catch (Exception $exception) {
        Log::error('Error in scheduled task: ' . $exception->getMessage());
    }
})
    ->weeklyOn(3, '10:00')
    ->timezone('America/El_Salvador')
    ->name('weekly_health_tip');
