<?php

namespace App\Services;

use App\Helper\Pagination;
use App\Models\OrderStatusHistory;
use App\Repository\OrderStatusHistoryRepository;

class OrderStatusHistoryService extends Service implements OrderStatusHistoryRepository
{
    public function findBy($criteria = [], $page, $size) {

        $offset = Pagination::getOffset($page, $size);
        $order_status_histories = OrderStatusHistory::where($criteria)->take($size)->offset($offset)->orderBy('id', 'DESC')->get();

        return $order_status_histories;

    }

    public function findOneBy($criteria = []) {
        $order_status_history = OrderStatusHistory::where($criteria)->latest()->first();

        return $order_status_history;
    }

    public function create(OrderStatusHistory $order_status_history): object {
        if ($order_status_history->save()) {
            return $this->serviceReturn(true, $order_status_history);
        }
        return $this->serviceReturn(false);
    }

    public function update(OrderStatusHistory $order_status_history): object {
        if ($order_status_history->update()) {
            return $this->serviceReturn(true, $order_status_history);
        }
        return $this->serviceReturn(false);
    }

    public function delete(OrderStatusHistory $order_status_history): object {
        if ($order_status_history->delete()) {
            return $this->serviceReturn();
        }
        return $this->serviceReturn(false);
    }

    public function count($criteria = []): int {
        return OrderStatusHistory::where($criteria)->count();
    }

}
