<?php

namespace App\Http\Controllers\Auth;

use App\Http\Context\Auth\AuthContextInterface;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{

    protected AuthContextInterface $context;

    function __construct(AuthContextInterface $auth_context_interface)
    {
        $this->context = $auth_context_interface;
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

            $validate = Validator::make($request->all(), [
                'email' => 'required|email'
            ]);

            if ($validate->fails()) {
                return $this->failed($validate->errors()->first(), Response::HTTP_BAD_REQUEST);
            }

            $resp = $this->context->forgotPassword($request);

            if ($resp->http_status == Response::HTTP_OK) {
                return $this->success(
                    $resp->message
                );
            }

            return $this->failed(
                $resp->message,
                $resp->http_status
            );

        } catch (Exception $e) {
            return $this->failed($this->error($e->getMessage()));
        }
    }
}
