<?php

namespace App\Jobs;

use App\Models\Activity;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UserActivityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected object $payload;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(object $payload)
    {
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {

            if(!Activity::create([
                "user_id" => $this->payload->user_id,
                "purpose" => $this->payload->purpose,
                "description" => $this->payload->description
            ])){
                Log::alert('unable to create user activity '. now());
            }

        } catch (Exception $e) {
            Log::critical('something went wrong went create user activity ['. now() . '] : ' . $e->getMessage());
        }
    }
}
