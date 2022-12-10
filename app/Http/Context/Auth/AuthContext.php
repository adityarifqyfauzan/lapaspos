<?php

namespace App\Http\Context\Auth;

use App\Http\Context\Context;
use App\Models\LoginHistory;
use App\Repository\LoginHistoryRepository;
use App\Repository\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthContext extends Context implements AuthContextInterface
{

    protected LoginHistoryRepository $login_history_service;

    protected UserRepository $user_service;

    function __construct(LoginHistoryRepository $login_history_service, UserRepository $user_service)
    {
        $this->login_history_service = $login_history_service;
        $this->user_service = $user_service;
    }

    public function login(Request $request) {

        $user = $this->user_service->findOneBy(['username' => $request->username]);

        if ($user) {

            if (Hash::check($request->password, $user->password)) {

                $token = Auth::guard('api')->login($user);

                $resp = $this->login_history_service->create(new LoginHistory(['user_id' => $user->id]));
                if (!$resp->process) {
                    return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, config('messages.general.error') . ' gagal membuat login history');
                }

                return $this->returnContext(Response::HTTP_OK, config('messages.auth.login.success'), [
                    "access_token" => $token,
                    "token_type" => "bearer"
                ]);

            }

            return $this->returnContext(Response::HTTP_UNAUTHORIZED, config('messages.auth.login.error') . ', Password yang Anda masukkan salah!');

        }

        return $this->returnContext(Response::HTTP_NOT_FOUND, config('messages.auth.login.not_found'));

    }

    public function me(Request $request) {
        $user_id = Auth::user()->id;

        $resp = $this->user_service->findOneBy(['id' => $user_id]);

        if ($resp) {
            return $this->returnContext(Response::HTTP_OK, config('messages.general.found'), $resp);
        }

        return $this->returnContext(Response::HTTP_UNAUTHORIZED, config('messages.general.unauthorized'));
    }

    public function logout(Request $request) {
        Auth::logout();
        return $this->returnContext(Response::HTTP_OK, config('messages.auth.logout.success'));
    }

}
