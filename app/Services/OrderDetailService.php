<?php

namespace App\Services;

use App\Helper\Pagination;
use App\Models\OrderDetail;
use App\Repository\OrderDetailRepository;
use Illuminate\Support\Facades\DB;

class OrderDetailService extends Service implements OrderDetailRepository
{
    public function findBy($criteria = [], $page, $size) {

        $order_details = OrderDetail::with('product:id,name', 'product_price:id,base_price,margin,final_price')->where($criteria);

        // using paginate
        if ($page != 0 && $size != 0){

            $offset = Pagination::getOffset($page, $size);
            $order_details = $order_details->take($size)->offset($offset)->get();

        } else {
            $order_details = $order_details->get();
        }

        return $order_details;

    }

    public function findOneBy($criteria = []) {
        $order_detail = OrderDetail::with('product:id,name', 'product_price:id,base_price,margin,final_price')->where($criteria)->first();

        return $order_detail;
    }

    public function create(OrderDetail $order_detail): object {
        if ($order_detail->save()) {
            return $this->serviceReturn(true, $order_detail);
        }
        return $this->serviceReturn(false);
    }

    public function update(OrderDetail $order_detail): object {
        if ($order_detail->update()) {
            return $this->serviceReturn(true, $order_detail);
        }
        return $this->serviceReturn(false);
    }

    public function delete(OrderDetail $order_detail): object {
        if ($order_detail->delete()) {
            return $this->serviceReturn(true, $order_detail);
        }
        return $this->serviceReturn(false);
    }

    public function count($criteria = []): int {
        return DB::table('order_details')->where($criteria)->count();
    }

}
