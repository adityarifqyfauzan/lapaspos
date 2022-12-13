<?php

namespace App\Http\Controllers\ProductManagement\ItemUnit;

use App\Http\Context\ItemUnit\ItemUnitContextInterface;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class ItemUnitStatusController extends Controller
{
    protected ItemUnitContextInterface $context;

    function __construct(ItemUnitContextInterface $context)
    {
        $this->context = $context;
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke($id)
    {
        try {

            $resp = $this->context->updateStatus($id);

            return $this->success($resp->message, $resp->data, $resp->http_status);

        } catch (Exception $e) {
            return $this->failed($this->error($e->getMessage()));
        }
    }
}
