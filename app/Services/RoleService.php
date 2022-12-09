<?php

namespace App\Services;

use App\Helper\Pagination;
use App\Models\Role;
use App\Repository\RoleRepository;
use Illuminate\Support\Arr;

class RoleService extends Service implements RoleRepository
{
    public function findBy($criteria = [], $page, $size){

        $offset = Pagination::getOffset($page, $size);
        $roles = Role::where(Arr::except($criteria, ["name"]));

        if (Arr::exists($criteria, "name")) {
            $roles = $roles->where("name", "like", "%". $criteria["name"] . "%");
        }

        $roles = $roles->offset($offset)->take($size)->get();

        return $roles;

    }

    public function findOneBy($criteria = []){

        $role = Role::where(Arr::except($criteria, ["name"]));

        if (Arr::exists($criteria, "name")) {
            $role = $role->where("name", "like", "%". $criteria["name"] . "%");
        }

        $role = $role->first();

        return $role;

    }

    public function create(Role $role): object{
        if($role->save()){
            return $this->serviceReturn(true, $role);
        }

        return $this->serviceReturn(false);
    }

    public function update(Role $role): object{
        if($role->update()){
            return $this->serviceReturn(true, $role);
        }

        return $this->serviceReturn(false);
    }

    public function delete(Role $role): object{
        if($role->delete()){
            return $this->serviceReturn();
        }

        return $this->serviceReturn(false);
    }

    public function count($criteria = []): int{
        return Role::where($criteria)->count();
    }

}
