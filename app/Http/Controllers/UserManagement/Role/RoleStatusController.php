<?php

namespace App\Http\Controllers\UserManagement\Role;

use App\Http\Context\Role\RoleContextInterface;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RoleStatusController extends Controller
{
    protected RoleContextInterface $context;

    function __construct(RoleContextInterface $context) {
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
