<?php

namespace App\Http\Context\Outlet;

use Illuminate\Http\Request;

interface OutletContextInterface {
    public function getBy(Request $request);
    public function getById($id);
    public function store(Request $request);
    public function update($id, Request $request);
    public function updateStatus($id);
}
