<?php

namespace App\Http\Controllers\UserManagement\User;

use App\Http\Context\User\UserContextInterface;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Response;

class UserStatusController extends Controller
{

    protected UserContextInterface $context;

    function __construct(UserContextInterface $context) {
        $this->context = $context;
    }

    /**
     * Handle the incoming request.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function __invoke($id)
    {
        try {

            $resp = $this->context->updateStatus($id);
            if ($resp->http_status == Response::HTTP_OK) {
                return $this->success(
                    $resp->message,
                    $resp->data,
                    $resp->http_status
                );
            }

            return $this->failed(
                $resp->message,
                $resp->http_status
            );

        } catch (Exception $e) {
            return $this->failed($this->error($e->getMessage()), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
