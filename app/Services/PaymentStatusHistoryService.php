<?php

namespace App\Services;

use App\Helper\Pagination;
use App\Models\PaymentStatusHistory;
use App\Repository\PaymentStatusHistoryRepository;

class PaymentStatusHistoryService extends Service implements PaymentStatusHistoryRepository
{
    public function findBy($criteria = [], $page, $size) {

        $offset = Pagination::getOffset($page, $size);
        $order_status_histories = PaymentStatusHistory::where($criteria)->take($size)->offset($offset)->orderBy('id', 'DESC')->get();

        return $order_status_histories;

    }

    public function findOneBy($criteria = []) {
        $payment_status_history = PaymentStatusHistory::where($criteria)->latest()->first();

        return $payment_status_history;
    }

    public function create(PaymentStatusHistory $payment_status_history): object {
        if ($payment_status_history->save()) {
            return $this->serviceReturn(true, $payment_status_history);
        }
        return $this->serviceReturn(false);
    }

    public function update(PaymentStatusHistory $payment_status_history): object {
        if ($payment_status_history->update()) {
            return $this->serviceReturn(true, $payment_status_history);
        }
        return $this->serviceReturn(false);
    }

    public function delete(PaymentStatusHistory $payment_status_history): object {
        if ($payment_status_history->delete()) {
            return $this->serviceReturn();
        }
        return $this->serviceReturn(false);
    }

    public function count($criteria = []): int {
        return PaymentStatusHistory::where($criteria)->count();
    }

}
