<?php

namespace App\Http\Context\Reporting;

use Illuminate\Http\Request;

interface ReportingContextInterface {
    public function summary(Request $request);
    public function productSale(Request $request);
    public function transactionSummary(Request $request);
}
