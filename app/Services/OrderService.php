<?php

namespace App\Services;

use App\Helper\Pagination;
use App\Models\Order;
use App\Repository\OrderRepository;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class OrderService extends Service implements OrderRepository
{
    public function findBy($criteria = [], $page, $size) {

        $offset = Pagination::getOffset($page, $size);
        $orders = Order::with('user:id,name', 'outlet:id,name')->where(Arr::except($criteria, ["is_today", "user_id"]))->orderBy("id", "desc");

        if (Arr::exists($criteria, "is_today") && $criteria["is_today"]) {
            $orders = $orders->whereDate('created_at', Carbon::today());
        }

        if (Arr::exists($criteria, "user_id")) {
            $orders = $orders->whereIn('user_id', (array) $criteria['user_id']);
        }

        $orders = $orders->take($size)->offset($offset)->get();

        return $orders;

    }

    public function findOneBy($criteria = []) {

        $order = Order::with('user:id,name', 'outlet:id,name')->where($criteria)->first();
        return $order;

    }

    public function create(Order $order): object {
        if ($order->save()) {
            return $this->serviceReturn(true, $order);
        }
        return $this->serviceReturn(false);
    }

    public function update(Order $order): object {
        if ($order->update()) {
            return $this->serviceReturn(true, $order);
        }
        return $this->serviceReturn(false);
    }

    public function delete(Order $order): object {
        if ($order->delete()) {
            return $this->serviceReturn();
        }
        return $this->serviceReturn(false);
    }

    public function count($criteria = []): int {
        $orders = Order::where(Arr::except($criteria, ["is_today", "user_id"]))->orderBy("id", "desc");

        if (Arr::exists($criteria, "is_today") && $criteria["is_today"]) {
            $orders = $orders->whereDate('created_at', Carbon::today());
        }

        if (Arr::exists($criteria, "user_id")) {
            $orders = $orders->whereIn('user_id', (array) $criteria['user_id']);
        }

        $orders = $orders->count();
        return $orders;
    }

    public function sumQty($order_id): int {

        $orders = DB::table('order_details')->where(["order_id" => $order_id])->select(DB::raw("sum(qty) as qty"))->first('qty');

        return (int) ($orders->qty != null) ? $orders->qty : 0;
    }

    public function sumTotal($order_id): int {

        $orders = DB::table('order_details')->where(["order_id" => $order_id])->select(DB::raw("sum(total) as total"))->first('total');

        return (int) ($orders->total != null) ? $orders->total : 0;
    }

}
