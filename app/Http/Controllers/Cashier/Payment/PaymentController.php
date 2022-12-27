<?php

namespace App\Http\Controllers\Cashier\Payment;

use App\Http\Context\Payment\PaymentContextInterface;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    protected PaymentContextInterface $context;

    function __construct(PaymentContextInterface $context) {
        $this->context = $context;
    }

    public function store(Request $request)
    {
        try {

            $validate = Validator::make($request->all(), [
                'order_id' => 'required',
                'payment_method_id' => 'required',
                'paid' => 'required',
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

    public function show($id)
    {
        try {

            $resp = $this->context->getPayment($id);

            if ($resp->http_status == Response::HTTP_OK) {
                return $this->success($resp->message, $resp->data, $resp->http_status);
            }

            return $this->failed($resp->message, $resp->http_status);


        } catch (Exception $e) {
            return $this->failed($this->error($e->getMessage()));
        }
    }
}
