<?php

namespace App\Services;

use App\Helper\Pagination;
use App\Models\PaymentStatus;
use App\Repository\PaymentStatusRepository;
use Illuminate\Support\Arr;

class PaymentStatusService extends Service implements PaymentStatusRepository
{
    public function findBy($criteria = [], $page, $size) {
        $offset = Pagination::getOffset($page, $size);
        $payment_statuses = PaymentStatus::where(Arr::except($criteria, ["name"]));

        if (Arr::exists($criteria, "name")) {
            $payment_statuses = $payment_statuses->where("name", "like", "%". $criteria["name"] . "%");
        }

        $payment_statuses = $payment_statuses->offset($offset)->take($size)->get();

        return $payment_statuses;
    }

    public function findOneBy($criteria = []) {
        $payment_status = PaymentStatus::where(Arr::except($criteria, ["name"]));

        if (Arr::exists($criteria, "name")) {
            $payment_status = $payment_status->where("name", "like", "%". $criteria["name"] . "%");
        }

        $payment_status = $payment_status->first();

        return $payment_status;
    }

}
