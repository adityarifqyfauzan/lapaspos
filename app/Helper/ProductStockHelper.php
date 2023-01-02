<?php

namespace App\Helper;

use App\Repository\ProductStockRepository;

class ProductStockHelper {

    protected ProductStockRepository $product_stock_service;

    function __construct(ProductStockRepository $product_stock_service) {
        $this->product_stock_service = $product_stock_service;
    }

    public function getProductStock($product_id)
    {
        $stock_in = $this->product_stock_service->findBy(["product_id" => $product_id, "status" => ["in", "order_cancel"]]);

        // all stock where the status is except in
        $stock_out = $this->product_stock_service->findBy(["product_id" => $product_id, "status" => ["out", "sale", "return", "opname"]]);

        return $stock_in - $stock_out;
    }

}
