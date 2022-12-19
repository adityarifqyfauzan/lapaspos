<?php

namespace App\Http\Context\Role;

use App\Http\Context\Context;
use App\Models\Role;
use App\Repository\RoleRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class RoleContext extends Context implements RoleContextInterface
{

    protected RoleRepository $service;

    function __construct(RoleRepository $service) {
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

        return $criteria;
    }

    public function getBy(Request $request) {

        $criteria = $this->getCriteria($request);
        $pagination = $this->getPageAndSize($request);

        $roles = $this->service->findBy($criteria, $pagination->page, $pagination->size);

        return $this->returnContext(
            Response::HTTP_OK,
            config('messages.general.found'),
            $roles,
            $this->setPagination(
                $pagination->page,
                $pagination->size,
                $this->service->count($criteria)
            )
        );

    }

    public function getById($id) {

        $role = $this->service->findOneBy(["id" => $id]);
        if (!$role) {
            return $this->returnContext(Response::HTTP_NOT_FOUND, config('messages.general.not_found'));
        }
        return $this->returnContext(Response::HTTP_OK, config('messages.general.found'), $role);

    }

    public function store(Request $request) {

        $check = $this->service->findOneBy(["slug" => Str::slug($request->name)]);
        if ($check) {
            return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, 'Role sudah ada');
        }

        $role = $this->service->create(new Role($request->all()));
        if (!$role->process) {
            return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, config('messages.general.error') . ' gagal menambahkan role');
        }

        return $this->returnContext(Response::HTTP_CREATED, config('messages.general.created'));

    }

    public function update($id, Request $request) {

        $role = $this->service->findOneBy(["id" => $id]);
        if (!$role) {
            return $this->returnContext(Response::HTTP_NOT_FOUND, config('messages.general.not_found'));
        }

        $check = $this->service->findOneBy(["slug" => Str::slug($request->name)]);
        if ($check) {
            if ($role->id != $check->id) {
                return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, 'Role sudah ada');
            }
        }

        $role->name = $request->name;
        $role = $this->service->update($role);
        if (!$role->process) {
            return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, config('messages.general.error') . ' gagal memperbarui role');
        }

        return $this->returnContext(Response::HTTP_CREATED, config('messages.general.updated'));
    }

    public function updateStatus($id) {
        $role = $this->service->findOneBy(["id" => $id]);

        if (!$role) {
            return $this->returnContext(Response::HTTP_NOT_FOUND, config('messages.general.not_found'));
        }

        $role->is_active = ($role->is_active) ? false : true;
        $update = $this->service->update($role);
        if (!$update->process) {
            return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, config('messages.general.error') . ', gagal memperbarui role');
        }

        return $this->returnContext(Response::HTTP_OK, config('messages.general.updated'));
    }

}
