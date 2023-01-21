<?php

namespace App\Http\Controllers\Activity;

use App\Http\Context\Activity\ActivityContextInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActivityController extends Controller
{

    protected ActivityContextInterface $context;

    function __construct(ActivityContextInterface $context) {
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

        } catch (\Exception $e) {
            return $this->failed($this->error($e->getMessage()));
        }
    }
}
