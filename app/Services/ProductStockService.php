<?php

namespace App\Services;

use App\Helper\Pagination;
use App\Models\ProductStock;
use App\Repository\ProductStockRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ProductStockService extends Service implements ProductStockRepository
{
    public function findBy($criteria = []) {

        $product_stocks = DB::table('product_stocks')->where(Arr::except($criteria, "status"))->select(DB::raw("sum(stock) as
        stock"));

        if (Arr::exists($criteria, "status")) {
            $product_stocks = $product_stocks->whereIn("status", (array) $criteria["status"]);
        }

        $product_stocks = $product_stocks->first('stock');
        return (int) ($product_stocks->stock != null) ? $product_stocks->stock : 0;

    }

    public function findPagedBy($criteria = [], $page, $size) {

        $offset = Pagination::getOffset($page, $size);

        $product_stocks = ProductStock::with('product:id,name', 'supplier:id,name')->where(Arr::except($criteria, "status"));

        if (Arr::exists($criteria, "status")) {
            $product_stocks = $product_stocks->whereIn("status", (array) $criteria["status"]);
        }

        $product_stocks = $product_stocks->take($size)->offset($offset)->get();
        return $product_stocks;

    }

    public function findOneBy($criteria = []) {

        $product_stock = ProductStock::with('product:id,name', 'supplier:id,name')->where($criteria)->first();
        return $product_stock;

    }

    public function create(ProductStock $product_stock): object {
        if ($product_stock->save()) {
            return $this->serviceReturn(true, $product_stock);
        }
        return $this->serviceReturn(false);
    }

    public function update(ProductStock $product_stock): object {
        if ($product_stock->update()) {
            return $this->serviceReturn(true, $product_stock);
        }
        return $this->serviceReturn(false);
    }

    public function delete(ProductStock $product_stock): object {
        if ($product_stock->delete()) {
            return $this->serviceReturn();
        }
        return $this->serviceReturn(false);
    }

    public function count($criteria = []): int {
        $product_stocks = ProductStock::where(Arr::except($criteria, "status"));

        if (Arr::exists($criteria, "status")) {
            $product_stocks = $product_stocks->whereIn("status", (array) $criteria["status"]);
        }

        $product_stocks = $product_stocks->count();
        return $product_stocks;
    }

}