<?php

namespace App\Services;

use App\Helper\Pagination;
use App\Models\Product;
use App\Repository\ReportingRepository;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ReportingService extends Service implements ReportingRepository
{
    private function summary($criteria = []) {
        $orders = DB::table('orders')->leftJoin('order_details', 'orders.id', 'order_details.order_id')->leftJoin('product_prices', 'order_details.product_price_id', 'product_prices.id')->where(Arr::except($criteria, ["start_date", "end_date", "is_today", "is_month"]));

        if (Arr::exists($criteria, "start_date") && Arr::exists($criteria, "end_date")) {

            $start_date = $criteria["start_date"] . " 00:00:00";

            $end_date = $criteria["end_date"] . " 00:00:00";

            $start_date = Carbon::createFromFormat("Y-m-d h:i:s", $start_date);

            $end_date = Carbon::createFromFormat("Y-m-d h:i:s", $end_date)->addDay(1);

            $orders = $orders->whereBetween('orders.created_at', [$start_date, $end_date]);

        } else if (Arr::exists($criteria, "is_today") && $criteria["is_today"]) {
            $orders = $orders->whereDay('orders.created_at', now()->day);
        } else if (Arr::exists($criteria, "is_month") && $criteria["is_month"]) {
            $orders = $orders->whereMonth('orders.created_at', now()->month);
        }

        return $orders;
    }

    public function grossProfit($criteria = []) {
        $orders = $this->summary($criteria);

        $amount = $orders->selectRaw("SUM(order_details.total) as gross_profit")->first();

        return ($amount->gross_profit != null) ? $amount->gross_profit : 0;
    }

    public function transaction($criteria = []) {
        $orders = $this->summary($criteria);

        $transactions = count($orders->groupBy("orders.id")->get("orders.id"));

        return $transactions;
    }

    public function margin($criteria = []) {
        $orders = $this->summary($criteria);

        $margin = $orders->selectRaw("SUM(product_prices.margin * order_details.qty) as margin")->first();

        return ($margin->margin != null) ? $margin->margin : 0;
    }

    public function productSale($criteria = [], $page, $size) {

        $offset = Pagination::getOffset($page, $size);

        $products = Product::join('order_details', 'products.id', '=', 'order_details.product_id')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->where(Arr::except($criteria, ["start_date", "end_date", "is_today"]));

        if (Arr::exists($criteria, "start_date") && Arr::exists($criteria, "end_date")) {
            $products = $products->whereBetween('orders.created_at', [$criteria["start_date"], $criteria["end_date"]]);
        }

        if (Arr::exists($criteria, "is_today") && $criteria["is_today"]) {
            $products = $products->whereDay('orders.created_at', now()->day);
        }

        $products = $products->selectRaw('products.name, SUM(order_details.qty) AS qty, SUM(order_details.total) AS sub_total')
            ->withCasts(['qty' => 'integer'])
            ->groupBy('products.name')
            ->orderBy('products.name')
            ->take($size)
            ->offset($offset)
            ->get();

        return $products;
    }

    public function productSaleCount($criteria = []) {
        $products = Product::join('order_details', 'products.id', '=', 'order_details.product_id')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->where(Arr::except($criteria, ["start_date", "end_date", "is_today"]));

        if (Arr::exists($criteria, "start_date") && Arr::exists($criteria, "end_date")) {
            $products = $products->whereBetween('orders.created_at', [$criteria["start_date"], $criteria["end_date"]]);
        }

        if (Arr::exists($criteria, "is_today") && $criteria["is_today"]) {
            $products = $products->whereDay('orders.created_at', now()->day);
        }

        $products = $products->selectRaw('products.name')
            ->groupBy('products.name')
            ->get();

        return count($products);
    }

    public function transactionSummary($criteria) {

        // default start date adalah 1 tahun
        $start_date = now()->addMonth(-12);

        if (Arr::exists($criteria, "is_quarter") && $criteria["is_quarter"]) {
            $start_date = now()->addMonth(-4);
        }

        if (Arr::exists($criteria, "is_semester") && $criteria["is_semester"]) {
            $start_date = now()->addMonth(-6);
        }

        $orders = DB::table('orders')->where('order_status_id', config('constants.order_status.lunas'));

        if (!Arr::exists($criteria, "is_max")) {
            $orders = $orders->whereBetween('created_at', [$start_date, now()]);
        }

        $orders = $orders->selectRaw("MONTH( created_at) as month, YEAR(created_at) as year, COUNT(*) as total_transaction")->groupByRaw("MONTH( created_at), YEAR(created_at)")->get();

        return $orders;
    }

}
