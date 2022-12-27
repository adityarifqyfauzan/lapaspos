<?php

namespace App\Http\Controllers\Cashier\OrderStatus;

use App\Http\Context\OrderStatus\OrderStatusContextInterface;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderStatusController extends Controller
{
    protected OrderStatusContextInterface $context;

    function __construct(OrderStatusContextInterface $context) {
        $this->context = $context;
    }

    public function index(Request $request)
    {
        try {

            $resp = $this->context->getBy($request);

            if ($resp->http_status == Response::HTTP_OK) {
                return $this->success($resp->message, $resp->data, $resp->http_status, $resp->pagination);
            }

            return $this->failed($resp->message, $resp->http_status);


        } catch (Exception $e) {
            return $this->failed($this->error($e->getMessage()));
        }
    }
}
