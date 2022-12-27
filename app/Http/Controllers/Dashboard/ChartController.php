<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Context\Reporting\ReportingContextInterface;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ChartController extends Controller
{
    protected ReportingContextInterface $context;

    function __construct(ReportingContextInterface $context) {
        $this->context = $context;
    }

    public function transactionSummary(Request $request)
    {
        try {

            $resp = $this->context->transactionSummary($request);

            if ($resp->http_status == Response::HTTP_OK) {
                return $this->success($resp->message, $resp->data, $resp->http_status, $resp->pagination);
            }

            return $this->failed($resp->message, $resp->http_status);


        } catch (Exception $e) {
            return $this->failed($this->error($e->getMessage()));
        }
    }
}
