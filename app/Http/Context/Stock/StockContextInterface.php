<?php

namespace App\Http\Context\Stock;

use Illuminate\Http\Request;

interface StockContextInterface {
    public function store(Request $request);
    public function update($id, Request $request);
    public function delete($id);
}
