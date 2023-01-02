<?php

namespace App\Repository;

interface ReportingRepository {
    public function grossProfit($criteria = []);
    public function transaction($criteria = []);
    public function margin($criteria = []);
    public function productSale($criteria = [], $page, $size);
    public function productSaleCount($criteria = []);
    public function transactionSummary($criteria);
}
