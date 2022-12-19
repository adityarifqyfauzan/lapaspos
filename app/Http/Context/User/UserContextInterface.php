<?php

namespace App\Http\Context\User;

use Illuminate\Http\Request;

interface UserContextInterface {
    public function getBy(Request $request);
    public function getById($id);
    public function store(Request $request);
    public function update($id, Request $request);
    public function newPassword(Request $request);
    public function resetPassword($id, Request $request);
    public function updateStatus($id);
    public function delete($id);
}
