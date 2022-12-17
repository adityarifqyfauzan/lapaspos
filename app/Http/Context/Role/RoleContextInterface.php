<?php

namespace App\Http\Context\Role;

use Illuminate\Http\Request;

interface RoleContextInterface {
    public function getBy(Request $request);
    public function getById($id);
    public function store(Request $request);
    public function update($id, Request $request);
    public function updateStatus($id);
}
