<?php

namespace App\Http\Context\Auth;

use Illuminate\Http\Request;

interface AuthContextInterface {
    public function login(Request $request);
    public function me(Request $request);
    public function logout(Request $request);
}
