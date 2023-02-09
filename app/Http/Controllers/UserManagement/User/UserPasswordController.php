<?php

namespace App\Http\Controllers\UserManagement\User;

use App\Http\Context\User\UserContextInterface;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class UserPasswordController extends Controller
{
    protected UserContextInterface $context;

    function __construct(UserContextInterface $context) {
        $this->context = $context;
    }

    public function newPassword(Request $request)
    {
        try {

            $validate = Validator::make($request->all(), [
                'old_password' => 'required',
                'new_password' => 'required|min:6',
                'password_confirmation' => 'required|same:new_password'
            ], [
                'old_password.required' => 'Password lama harus diisi',
                'new_password.required' => 'Password baru harus diisi',
                'new_password.min' => 'Password baru minimal 6 karakter (huruf/angka)',
                'password_confirmation.required' => 'Konfirmasi password harus diisi',
                'password_confirmation.same' => 'Password baru dan konfirmasi password harus sama'
            ]);

            if ($validate->fails()) {
                return $this->failed($validate->errors()->first(), Response::HTTP_BAD_REQUEST);
            }

            $resp = $this->context->newPassword($request);

            if ($resp->http_status == Response::HTTP_OK) {
                return $this->success($resp->message, $resp->data, $resp->http_status);
            }

            return $this->failed($resp->message, $resp->http_status);

        } catch (Exception $e) {
            return $this->failed($this->error($e->getMessage()), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function resetPassword($id, Request $request)
    {
        try {

            $validate = Validator::make($request->all(), [
                'new_password' => 'required|min:6',
            ]);

            if ($validate->fails()) {
                return $this->failed($validate->errors()->first(), Response::HTTP_BAD_REQUEST);
            }

            $resp = $this->context->resetPassword($id, $request);

            if ($resp->http_status == Response::HTTP_OK) {
                return $this->success($resp->message, $resp->data, $resp->http_status);
            }

            return $this->failed($resp->message, $resp->http_status);

        } catch (Exception $e) {
            return $this->failed($this->error($e->getMessage()), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
