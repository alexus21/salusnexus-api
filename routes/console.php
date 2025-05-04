<?php

use App\Http\Controllers\Api\HealthTipsController;
use App\Models\Disease;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $response = app(HealthTipsController::class)->generateTip(new Request([
        'disease_ids' => [7],
        'service' => 'gemini_openai',
    ]));

    if ($response->getStatusCode() === 200) {
        $data = json_decode($response->getContent(), true);
        Log::info(json_encode($data));
    } else {
        Log::error('Error generating health tips: ' . $response->getContent());
    }
})
    ->everySecond()
    ->name('test_log_entry');


/*Schedule::call(function () {
    $diseases = Disease::all();
    log::info($diseases);
})
    ->weeklyOn(3, '10:00')
    ->timezone('America/El_Salvador')
    ->name('Send health tips to patients weekly')
    ->withoutOverlapping();*/

