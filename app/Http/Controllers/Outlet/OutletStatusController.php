<?php

namespace App\Http\Controllers\Outlet;

use App\Http\Context\Outlet\OutletContextInterface;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Response;

class OutletStatusController extends Controller
{
    protected OutletContextInterface $context;

    function __construct(OutletContextInterface $context) {
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
            if ($resp->http_status == Response::HTTP_OK) {
                return $this->success($resp->message, $resp->data, $resp->http_status);
            }

            return $this->failed($resp->message, $resp->http_status);

        } catch (Exception $e) {
            return $this->failed($this->error($e->getMessage()), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
