<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProductStockUpdateJob implements ShouldQueue
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
        $payload = $this->payload;
        foreach ($payload as $value) {
            $product = Product::find($value['product_id']);
            if ($product->have_stock) {
                if(!ProductStock::create([
                    "product_id" => $value['product_id'],
                    "supplier_id" => $value['supplier_id'],
                    "stock" => $value['stock'],
                    "status" => $value['status'],
                    "created_by" => $value['created_by']
                ])){
                    Log::critical("unable to create product stock on queue", $value);
                    return;
                }
            }
        }
    }
}
