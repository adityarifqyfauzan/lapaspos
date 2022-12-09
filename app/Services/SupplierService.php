<?php

namespace App\Services;

use App\Helper\Pagination;
use App\Models\Supplier;
use App\Repository\SupplierRepository;
use Illuminate\Support\Arr;

class SupplierService extends Service implements SupplierRepository
{
    public function findBy($criteria = [], $page, $size){
        $offset = Pagination::getOffset($page, $size);
        $suppliers = Supplier::where(Arr::except($criteria, ["name"]));

        if (Arr::exists($criteria, "name")) {
            $suppliers = $suppliers->where("name", "like", "%". $criteria["name"] . "%");
        }

        $suppliers = $suppliers->offset($offset)->take($size)->get();

        return $suppliers;
    }

    public function findOneBy($criteria = []){
        $supplier = Supplier::where(Arr::except($criteria, ["name"]));

        if (Arr::exists($criteria, "name")) {
            $supplier = $supplier->where("name", "like", "%". $criteria["name"] . "%");
        }

        $supplier = $supplier->first();

        return $supplier;
    }

    public function create(Supplier $supplier): object{
        if($supplier->save()){
            return $this->serviceReturn(true, $supplier);
        }

        return $this->serviceReturn(false);
    }

    public function update(Supplier $supplier): object{
        if($supplier->update()){
            return $this->serviceReturn(true, $supplier);
        }

        return $this->serviceReturn(false);
    }

    public function delete(Supplier $supplier): object{
        if($supplier->delete()){
            return $this->serviceReturn();
        }

        return $this->serviceReturn(false);
    }

    public function count($criteria = []): int{
        return Supplier::where($criteria)->count();
    }

}
