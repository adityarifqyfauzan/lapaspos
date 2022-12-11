<?php

namespace App\Http\Context\Category;

use Illuminate\Http\Request;

interface CategoryContextInterface {
    public function getBy(Request $request);
    public function getById($id);
    public function store(Request $request);
    public function update($id, Request $request);
    public function updateStatus($id);
}
