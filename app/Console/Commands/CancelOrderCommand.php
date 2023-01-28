<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\ProductStock;
use App\Repository\OrderDetailRepository;
use App\Repository\OrderRepository;
use App\Repository\OrderStatusHistoryRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductStockRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CancelOrderCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:cancel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto Cancel Order Daily';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(OrderRepository $order_service,
    OrderDetailRepository $order_detail_service,
    OrderStatusHistoryRepository $order_status_history_service,
    ProductStockRepository $product_stock_service,
    ProductRepository $product_service,)
    {
        try {

            $orders = $order_service->findBy(["order_status_id" => config('constants.order_status.belum-dibayar')], 0, 0);

            foreach ($orders as $order) {

                if ($order->order_status_id == config('constants.order_status.lunas')) {
                    Log::info(now(). " | [order-cancel] order sudah dibatalkan");
                    continue;
                }

                DB::beginTransaction();

                $old_order_status = $order->order_status_id;
                $order->order_status_id = config('constants.order_status.dibatalkan');
                $order = $order_service->update($order);
                if (!$order->process) {
                    DB::rollBack();
                    Log::alert(now(). ' | [order-cancel] unable to update status cancel order, order_id: '. $order->data->id);
                    continue;
                }

                // create order status history
                $order_status_history = new OrderStatusHistory();
                $order_status_history->order_id = $order->data->id;
                $order_status_history->current_status = $order->data->order_status_id;
                $order_status_history->previous_status = $old_order_status;
                $order_status_history = $order_status_history_service->create($order_status_history);
                if (!$order_status_history->process) {
                    DB::rollBack();
                    Log::alert(now(). ' | [order-cancel] unable to create order status history, order_id: '. $order->data->id);
                    continue;
                }

                // return the stock from order detail
                $order_details = $order_detail_service->findBy(["order_id" => $order->data->id], 0, 0);

                foreach ($order_details as $order_detail) {

                    $product = $product_service->findOneBy(["id" => $order_detail->product_id]);
                    if ($product && $product->have_stock) {

                        $product_stock = new ProductStock();
                        $product_stock->product_id = $order_detail->product_id;
                        $product_stock->supplier_id = 1;
                        $product_stock->stock = $order_detail->qty;
                        $product_stock->status = "order_cancel";
                        $product_stock->created_by = $order->data->user_id;
                        $product_stock = $product_stock_service->create($product_stock);
                        if (!$product_stock->process) {
                            DB::rollBack();
                            Log::alert(now(). ' | [order-cancel] unable to return product stock, order_id: '. $order->data->id);
                            continue;
                        }

                    }
                }

                DB::commit();
                Log::info(now(). " | [order-cancel] success to cancel order, order_id: ". $order->data->id);
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {

            DB::rollBack();
            Log::critical(now() . "| [order-cancel] something went wrong: ". $e->getMessage());

        }

    }
}
