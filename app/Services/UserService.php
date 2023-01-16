<?php

namespace App\Services;

use App\Helper\Pagination;
use App\Models\User;
use App\Repository\UserRepository;
use Illuminate\Support\Arr;

class UserService extends Service implements UserRepository
{
    public function findBy($criteria = [], $page, $size){

        $offset = Pagination::getOffset($page, $size);
        $users = User::with('role:id,name,slug', 'outlet:id,name,slug')->where(Arr::except($criteria, ["name"]));

        if (Arr::exists($criteria, "name")) {
            $users = $users->where("name", "like", "%". $criteria["name"] . "%");
        }

        $users = $users->offset($offset)->take($size)->get();

        return $users;

    }

    public function findOneBy($criteria = []){

        $user = User::with('role:id,name,slug', 'outlet:id,name,slug')->where(Arr::except($criteria, ["name"]));

        if (Arr::exists($criteria, "name")) {
            $user = $user->where("name", "like", "%". $criteria["name"] . "%");
        }

        $user = $user->first();

        return $user;

    }

    public function create(User $user): object{
        if($user->save()){
            return $this->serviceReturn(true, $user);
        }

        return $this->serviceReturn(false);
    }

    public function update(User $user): object{
        if($user->update()){
            return $this->serviceReturn(true, $user);
        }

        return $this->serviceReturn(false);
    }

    public function delete(User $user): object{
        if($user->delete()){
            return $this->serviceReturn();
        }

        return $this->serviceReturn(false);
    }

    public function count($criteria = []): int{
        return User::where($criteria)->count();
    }

}
