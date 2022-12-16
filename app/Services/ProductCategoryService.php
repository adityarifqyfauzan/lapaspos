<?php

namespace App\Services;

use App\Helper\Pagination;
use App\Models\ProductCategory;
use App\Repository\ProductCategoryRepository;

class ProductCategoryService extends Service implements ProductCategoryRepository
{

    public function findBy($criteria = []) {

        $product_categories = ProductCategory::where($criteria)->get();

        return $product_categories;

    }

    public function findOneBy($criteria = []) {
        $product_categories = ProductCategory::where($criteria)->first();

        return $product_categories;
    }

    public function create(ProductCategory $product_category): object {

        $check = $this->findOneBy(["product_id" => $product_category->product_id, "category_id" => $product_category->category_id]);

        /**
         * cek kategori produk nya udah ada apa belum
         * kalo udah ada yaudah anggap aja berhasil wkwk
         */
        if ($check) {
            return $this->serviceReturn(true, $check);
        }

        /**
         * kalo belum ya jan lupa disave
         */
        if($product_category->save()) {
            return $this->serviceReturn(true, $product_category);
        }

        return $this->serviceReturn(false);
    }

    public function update(ProductCategory $product_category): object {
        $check = $this->findOneBy(["product_id" => $product_category->product_id, "category_id" => $product_category->category_id]);

        /**
         * cek kategori produk nya udah ada apa belum
         * kalo udah ada yaudah anggap aja berhasil wkwk
         */
        if ($check) {
            return $this->serviceReturn(true);
        }

        /**
         * kalo belum ya jan lupa diupdate
         */
        if($product_category->update()) {
            return $this->serviceReturn(true, $product_category);
        }

        return $this->serviceReturn(false);
    }

    public function delete(ProductCategory $product_category): object {
        if ($product_category->delete()) {
            return $this->serviceReturn();
        }
        return $this->serviceReturn(false);
    }

    public function count($criteria = []): int {
        return ProductCategory::where($criteria)->count();
    }


}
