<?php

namespace App\Jobs;

use App\Models\PaymentStatusHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PaymentStatusHistoryJob implements ShouldQueue
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
        // create payment history status
        $payment_status_history = new PaymentStatusHistory();
        $payment_status_history->payment_id = $this->payload->payment_id;
        $payment_status_history->current_status = $this->payload->current_status;
        $payment_status_history->previous_status = $this->payload->previous_status;
        if (!$payment_status_history->save()) {
            Log::error('unable to create [payment_status_history] ' . now());
            return;
        }
        Log::info('[payment_status_history] create successfully ' . now());
    }
}
