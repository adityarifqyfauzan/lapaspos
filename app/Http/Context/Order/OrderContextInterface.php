<?php

namespace App\Http\Context\Order;

use Illuminate\Http\Request;

interface OrderContextInterface {
    public function getBy(Request $request);
    public function getByID($id);
    public function store(Request $request);
    public function cancelOrder($id);
}
