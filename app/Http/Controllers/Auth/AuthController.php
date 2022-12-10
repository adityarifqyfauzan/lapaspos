<?php

namespace App\Http\Controllers\Auth;

use App\Http\Context\Auth\AuthContextInterface;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    protected AuthContextInterface $context;

    function __construct(AuthContextInterface $auth_context_interface)
    {
        $this->context = $auth_context_interface;
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function login(Request $request)
    {
        try {

            $validate = Validator::make($request->all(), [
                'username' => 'required',
                'password' => 'required'
            ]);

            if ($validate->fails()) {
                return $this->failed($validate->errors()->first(), Response::HTTP_BAD_REQUEST);
            }

            $resp = $this->context->login($request);

            return $this->success($resp->message, $resp->data, $resp->http_status);

        } catch (Exception $e) {

            return $this->failed($this->error($e->getMessage()));

        }
    }

    public function me(Request $request)
    {
        try {

            $resp = $this->context->me($request);
            return $this->success($resp->message, $resp->data, $resp->http_status);

        } catch (Exception $e) {

            return $this->failed($this->error($e->getMessage()));

        }
    }

    public function logout(Request $request)
    {
        try {

            $resp = $this->context->logout($request);
            return $this->success($resp->message, null, $resp->http_status);

        } catch (Exception $e) {

            return $this->failed($this->error($e->getMessage()));

        }
    }

}
