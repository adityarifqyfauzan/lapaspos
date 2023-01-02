<?php

namespace App\Jobs;

use App\Models\OrderStatusHistory;
use App\Repository\OrderRepository;
use App\Repository\OrderStatusHistoryRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OrderStatusHistoryJob implements ShouldQueue
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

        $order_status_history = new OrderStatusHistory();
        $order_status_history->order_id = $this->payload->order_id;
        $order_status_history->current_status = $this->payload->current_status;
        $order_status_history->previous_status = $this->payload->previous_status;
        if (!$order_status_history->save()) {
            Log::error('unable to create [order_status_history] ' . now());
            return;
        }
        Log::info('[order_status_history] create successfully ' . now());
    }
}
