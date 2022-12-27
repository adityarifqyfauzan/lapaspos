<?php

namespace App\Http\Controllers\Cashier\Order;

use App\Http\Context\Order\OrderContextInterface;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    protected OrderContextInterface $context;

    function __construct(OrderContextInterface $context) {
        $this->context = $context;
    }

    public function index(Request $request)
    {
        try {

            $resp = $this->context->getBy($request);

            if ($resp->http_status == Response::HTTP_OK) {
                return $this->success(
                    $resp->message,
                    $resp->data,
                    $resp->http_status,
                    $resp->pagination
                );
            }

            return $this->failed($resp->message, $resp->http_status);

        } catch (Exception $e) {

            return $this->failed($this->error($e->getMessage()));

        }
    }

    public function show($id)
    {
        try {

            $resp = $this->context->getByID($id);

            if ($resp->http_status == Response::HTTP_OK) {
                return $this->success($resp->message, $resp->data, $resp->http_status);
            }

            return $this->failed($resp->message, $resp->http_status);

        } catch (Exception $e) {

            return $this->failed($this->error($e->getMessage()));

        }
    }

    public function createOrder(Request $request)
    {
        try {

            $validate = Validator::make($request->all(), [
                'products' => 'required',
            ]);

            if ($validate->fails()) {
                return $this->failed($validate->errors()->first(), Response::HTTP_BAD_REQUEST);
            }

            $resp = $this->context->store($request);

            if ($resp->http_status == Response::HTTP_CREATED) {
                return $this->success($resp->message, $resp->data, $resp->http_status);
            }

            return $this->failed($resp->message, $resp->http_status);

        } catch (Exception $e) {

            return $this->failed($this->error($e->getMessage()));

        }
    }

    public function cancelOrder($id)
    {
        try {

            $resp = $this->context->cancelOrder($id);

            if ($resp->http_status == Response::HTTP_OK) {
                return $this->success($resp->message, $resp->data, $resp->http_status);
            }

            return $this->failed($resp->message, $resp->http_status);


        } catch (Exception $e) {
            return $this->failed($this->error($e->getMessage()));
        }
    }
}
