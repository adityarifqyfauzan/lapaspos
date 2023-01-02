<?php

namespace App\Http\Controllers\Cashier\Report;

use App\Http\Context\Reporting\ReportingContext;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReportingController extends Controller
{
    protected ReportingContext $context;

    function __construct(ReportingContext $context) {
        $this->context = $context;
    }

    public function summary(Request $request)
    {
        try {

            $resp = $this->context->summary($request);

            if ($resp->http_status == Response::HTTP_OK) {
                return $this->success($resp->message, $resp->data, $resp->http_status);
            }

            return $this->failed($resp->message, $resp->http_status);


        } catch (Exception $e) {
            return $this->failed($this->error($e->getMessage()));
        }
    }

    public function productSale(Request $request)
    {
        try {

            $resp = $this->context->productSale($request);

            if ($resp->http_status == Response::HTTP_OK) {
                return $this->success($resp->message, $resp->data, $resp->http_status, $resp->pagination);
            }

            return $this->failed($resp->message, $resp->http_status);


        } catch (Exception $e) {
            return $this->failed($this->error($e->getMessage()));
        }
    }
}
