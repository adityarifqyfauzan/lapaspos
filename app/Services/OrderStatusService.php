<?php

namespace App\Services;

use App\Helper\Pagination;
use App\Models\OrderStatus;
use App\Repository\OrderStatusRepository;
use Illuminate\Support\Arr;

class OrderStatusService extends Service implements OrderStatusRepository
{
    public function findBy($criteria = [], $page, $size) {

        $offset = Pagination::getOffset($page, $size);
        $order_statuses = OrderStatus::where(Arr::except($criteria, ["name"]));

        if (Arr::exists($criteria, "name")) {
            $order_statuses = $order_statuses->where("name", "like", "%". $criteria["name"] . "%");
        }

        $order_statuses = $order_statuses->offset($offset)->take($size)->get();

        return $order_statuses;

    }

    public function findOneBy($criteria = []) {

        $order_status = OrderStatus::where(Arr::except($criteria, ["name"]));

        if (Arr::exists($criteria, "name")) {
            $order_status = $order_status->where("name", "like", "%". $criteria["name"] . "%");
        }

        $order_status = $order_status->first();

        return $order_status;

    }

    public function create(OrderStatus $order_status): object {
        if($order_status->save()){
            return $this->serviceReturn(true, $order_status);
        }

        return $this->serviceReturn(false);
    }

    public function update(OrderStatus $order_status): object {
        if($order_status->update()){
            return $this->serviceReturn(true, $order_status);
        }

        return $this->serviceReturn(false);
    }

    public function delete(OrderStatus $order_status): object {
        if($order_status->delete()){
            return $this->serviceReturn();
        }

        return $this->serviceReturn(false);
    }

    public function count($criteria = []): int {
        return OrderStatus::where($criteria)->count();
    }

}
