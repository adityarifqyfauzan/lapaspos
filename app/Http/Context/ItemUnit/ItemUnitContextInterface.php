<?php

namespace App\Http\Context\ItemUnit;

use Illuminate\Http\Request;

interface ItemUnitContextInterface {
    public function getBy(Request $request);
    public function getById($id);
    public function store(Request $request);
    public function update($id, Request $request);
    public function updateStatus($id);
}
