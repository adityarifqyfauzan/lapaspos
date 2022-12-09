<?php

namespace App\Services;

use App\Helper\Pagination;
use App\Models\PaymentMethod;
use App\Repository\PaymentMethodRepository;
use Illuminate\Support\Arr;

class PaymentMethodService extends Service implements PaymentMethodRepository
{
    public function findBy($criteria = [], $page, $size) {

        $offset = Pagination::getOffset($page, $size);
        $payment_methods = PaymentMethod::where(Arr::except($criteria, ["name"]));

        if (Arr::exists($criteria, "name")) {
            $payment_methods = $payment_methods->where("name", "like", "%". $criteria["name"] . "%");
        }

        $payment_methods = $payment_methods->offset($offset)->take($size)->get();

        return $payment_methods;

    }

    public function findOneBy($criteria = []) {

        $payment_method = PaymentMethod::where(Arr::except($criteria, ["name"]));

        if (Arr::exists($criteria, "name")) {
            $payment_method = $payment_method->where("name", "like", "%". $criteria["name"] . "%");
        }

        $payment_method = $payment_method->first();

        return $payment_method;

    }

    public function create(PaymentMethod $payment_method): object {
        if($payment_method->save()){
            return $this->serviceReturn(true, $payment_method);
        }

        return $this->serviceReturn(false);
    }

    public function update(PaymentMethod $payment_method): object {
        if($payment_method->update()){
            return $this->serviceReturn(true, $payment_method);
        }

        return $this->serviceReturn(false);
    }

    public function delete(PaymentMethod $payment_method): object {
        if($payment_method->delete()){
            return $this->serviceReturn();
        }

        return $this->serviceReturn(false);
    }

    public function count($criteria = []): int {
        return PaymentMethod::where($criteria)->count();
    }

}
