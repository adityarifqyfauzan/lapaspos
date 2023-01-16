<?php

namespace App\Http\Context\User;

use App\Http\Context\Context;
use App\Models\User;
use App\Repository\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserContext extends Context implements UserContextInterface
{
    protected UserRepository $service;

    function __construct(UserRepository $service) {
        $this->service = $service;
    }

    private function getCriteria(Request $request): array {
        $criteria = [];

        if ($request->query('product_id') != null) {
            $criteria['id'] = $request->query('product_id');
        }

        if ($request->query('name') != null) {
            $criteria['name'] = $request->query('name');
        }

        if ($request->query('slug') != null) {
            $criteria['slug'] = $request->query('slug');
        }

        if ($request->query('outlet_id') != null) {
            $criteria['outlet_id'] = $request->query('outlet_id');
        }

        return $criteria;
    }

    public function getBy(Request $request) {

        $criteria = $this->getCriteria($request);
        $pagination = $this->getPageAndSize($request);

        $users = $this->service->findBy($criteria, $pagination->page, $pagination->size);

        return $this->returnContext(
            Response::HTTP_OK,
            config('messages.general.found'),
            $users,
            $this->setPagination(
                $pagination->page,
                $pagination->size,
                $this->service->count($criteria)
            )
        );

    }

    public function getById($id) {

        $user = $this->service->findOneBy(["id" => $id]);
        if (!$user) {
            return $this->returnContext(
                Response::HTTP_NOT_FOUND,
                config('messages.general.not_found')
            );
        }

        return $this->returnContext(
            Response::HTTP_OK,
            config('messages.general.found'),
            $user
        );

    }

    public function store(Request $request) {

        $user = $this->service->create(new User($request->all()));
        if (!$user->process) {
            return $this->returnContext(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                config('messages.general.error') . ' gagal menambahkan user'
            );
        }

        return $this->returnContext(
            Response::HTTP_CREATED,
            config('messages.general.created')
        );

    }

    public function update($id, Request $request) {
        $user = $this->service->findOneBy(["id" => $id]);
        if (!$user) {
            return $this->returnContext(
                Response::HTTP_NOT_FOUND,
                config('messages.general.not_found')
            );
        }

        $user->name = $request->name;
        $user->role_id = $request->role_id;
        $user->outlet_id = $request->outlet_id;

        $user = $this->service->update($user);
        if (!$user->process) {
            return $this->returnContext(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                config('messages.general.error') . ' gagal memperbarui status'
            );
        }

        return $this->returnContext(
            Response::HTTP_OK,
            config('messages.general.updated')
        );
    }

    public function updateStatus($id) {

        $user = $this->service->findOneBy(["id" => $id]);
        if (!$user) {
            return $this->returnContext(
                Response::HTTP_NOT_FOUND,
                config('messages.general.not_found')
            );
        }

        $user->is_active = ($user->is_active) ? false : true;
        $user = $this->service->update($user);
        if (!$user->process) {
            return $this->returnContext(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                config('messages.general.error') . ' gagal memperbarui status'
            );
        }

        return $this->returnContext(
            Response::HTTP_OK,
            config('messages.general.updated')
        );
    }

    public function newPassword(Request $request) {
        $user = $this->service->findOneBy(["id" => Auth::user()->id]);
        if (!$user) {
            return $this->returnContext(
                Response::HTTP_NOT_FOUND,
                config('messages.general.not_found')
            );
        }

        if (!Hash::check($request->old_password, $user->password)) {
            return $this->returnContext(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                'Password lama tidak sesuai!'
            );
        }

        $user->password = $request->new_password;

        $user = $this->service->update($user);
        if (!$user->process) {
            return $this->returnContext(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                config('messages.general.error') . ' gagal memperbarui password'
            );
        }

        return $this->returnContext(
            Response::HTTP_OK,
            config('messages.general.updated')
        );
    }

    public function resetPassword($id, Request $request) {

        $user = $this->service->findOneBy(["id" => $id]);
        if (!$user) {
            return $this->returnContext(
                Response::HTTP_NOT_FOUND,
                config('messages.general.not_found') . ' user tidak terdaftar'
            );
        }

        $user->password = $request->new_password;

        $user = $this->service->update($user);
        if (!$user->process) {
            return $this->returnContext(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                config('messages.general.error') . ' gagal memperbarui password'
            );
        }

        return $this->returnContext(
            Response::HTTP_OK,
            config('messages.general.updated') . ' password: ' . $request->new_password
        );

    }

    public function delete($id) {

        $user = $this->service->findOneBy(["id" => $id]);
        if (!$user) {
            return $this->returnContext(
                Response::HTTP_NOT_FOUND,
                config('messages.general.not_found') . ' user tidak terdaftar'
            );
        }

        $user = $this->service->delete($user);
        if (!$user->process) {
            return $this->returnContext(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                config('messages.general.error') . ' gagal menghapus user'
            );
        }

        return $this->returnContext(
            Response::HTTP_OK,
            config('messages.general.deleted')
        );

    }

}
