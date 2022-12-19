<?php

namespace App\Http\Controllers\Cashier\Product;

use App\Http\Context\Product\ProductContextInterface;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class ProductListController extends Controller
{

    protected ProductContextInterface $context;

    function __construct(ProductContextInterface $context) {
        $this->context = $context;
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        try {

            $resp = $this->context->getBy($request);

            return $this->success(
                $resp->message,
                $resp->data,
                $resp->http_status,
                $resp->pagination
            );

        } catch (Exception $e) {
            return $this->failed($this->error($e->getMessage()));
        }
    }
}
