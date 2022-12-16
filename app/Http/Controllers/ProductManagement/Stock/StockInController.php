<?php

namespace App\Http\Controllers\ProductManagement\Stock;

use App\Http\Context\Stock\StockContextInterface;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class StockInController extends Controller
{
    protected StockContextInterface $stock_context;

    function __construct(StockContextInterface $stock_context) {
        $this->stock_context = $stock_context;
    }

    public function index(Request $request)
    {
        try {

            $resp = $this->stock_context->getBy($request);
            return $this->success($resp->message, $resp->data, $resp->http_status, $resp->pagination);

        } catch (Exception $e) {
            return $this->failed($this->error($e->getMessage()));
        }
    }

    public function show($id)
    {
        try {

            $resp = $this->stock_context->getOneBy($id);

            if ($resp->http_status == Response::HTTP_OK) {
                return $this->success($resp->message, $resp->data, $resp->http_status);
            }

            return $this->failed($resp->message, $resp->http_status);

        } catch (Exception $e) {
            return $this->failed($this->error($e->getMessage()));
        }
    }

    public function create(Request $request)
    {
        try {

            $validate = Validator::make($request->all(), [
                'product_id' => 'required',
                'supplier_id' => 'required',
                'stock' => 'required',
            ]);

            if ($validate->fails()) {
                return $this->failed($validate->errors()->first(), Response::HTTP_BAD_REQUEST);
            }

            /**
             * create stock in
             */
            $request->status = 'in';

            $resp = $this->stock_context->store($request);

            if ($resp->http_status == Response::HTTP_CREATED) {
                return $this->success($resp->message, $resp->data, $resp->http_status);
            }

            return $this->failed($resp->message, $resp->http_status);

        } catch (Exception $e) {
            return $this->failed($this->error($e->getMessage()));
        }
    }

    public function update($id, Request $request)
    {
        try {

            $validate = Validator::make($request->all(), [
                'product_id' => 'required',
                'supplier_id' => 'required',
                'stock' => 'required',
            ]);

            if ($validate->fails()) {
                return $this->failed($validate->errors()->first(), Response::HTTP_BAD_REQUEST);
            }

            $resp = $this->stock_context->update($id, $request);

            if ($resp->http_status == Response::HTTP_OK) {
                return $this->success($resp->message, $resp->data, $resp->http_status);
            }

            return $this->failed($resp->message, $resp->http_status);

        } catch (Exception $e) {
            return $this->failed($this->error($e->getMessage()));
        }
    }

    public function destroy($id)
    {
        try {

            $resp = $this->stock_context->delete($id);

            if ($resp->http_status == Response::HTTP_OK) {
                return $this->success($resp->message, $resp->data, $resp->http_status);
            }

            return $this->failed($resp->message, $resp->http_status);

        } catch (Exception $e) {
            return $this->failed($this->error($e->getMessage()));
        }
    }
}
