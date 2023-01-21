<?php

namespace App\Http\Context\Activity;

use Illuminate\Http\Request;

interface ActivityContextInterface {
    public function getBy(Request $request);
    public function getById($id);
}
