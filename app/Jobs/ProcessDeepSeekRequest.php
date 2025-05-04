<?php

namespace App\Jobs;

use App\Services\DeepSeekService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class ProcessDeepSeekRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $messages;
    protected $requestId;
    protected $timeout;

    /**
     * Create a new job instance.
     *
     * @param array $messages
     * @param string $requestId
     * @param int $timeout
     */
    public function __construct(array $messages, string $requestId, int $timeout = 300)
    {
        $this->messages = $messages;
        $this->requestId = $requestId;
        $this->timeout = $timeout;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(DeepSeekService $deepSeekService)
    {
        // Establecer timeout para este job
        set_time_limit($this->timeout);
        
        try {
            // Procesar la petición
            $response = $deepSeekService->createChatCompletion($this->messages);
            
            // Guardar el resultado en cache para recuperarlo después
            Cache::put('deepseek_' . $this->requestId, [
                'status' => 'completed',
                'data' => $response
            ], 60 * 60); // Guardar durante 1 hora
        } catch (\Exception $e) {
            // Guardar el error en cache
            Cache::put('deepseek_' . $this->requestId, [
                'status' => 'error',
                'message' => $e->getMessage()
            ], 60 * 60); // Guardar durante 1 hora
        }
    }
} 