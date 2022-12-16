<?php

namespace App\Http\Context\Product;

use App\Http\Context\Context;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductPrice;
use App\Repository\ItemUnitRepository;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductPriceRepository;
use App\Repository\ProductRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductContext extends Context implements ProductContextInterface
{

    protected ProductRepository $product_service;

    protected ProductPriceRepository $product_price_service;

    protected ProductCategoryRepository $product_category_service;

    protected ItemUnitRepository $item_unit_service;

    function __construct(ProductRepository $product_service, ProductPriceRepository $product_price_service, ProductCategoryRepository $product_category_service, ItemUnitRepository $item_unit_service)
    {
        $this->product_service = $product_service;
        $this->product_price_service = $product_price_service;
        $this->product_category_service = $product_category_service;
        $this->item_unit_service = $item_unit_service;
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

        if ($request->query('category_id') != null) {
            $criteria['category_id'] = $request->query('category_id');
        }

        if ($request->query('is_active') != null) {
            $criteria['is_active'] = ($request->query('is_active') == "true") ? 1 : 0;
        }

        if ($request->query('have_stock') != null) {
            $criteria['have_stock'] = ($request->query('have_stock') == "true") ? 1 : 0;
        }

        return $criteria;
    }

    public function getBy(Request $request) {

        $criteria = $this->getCriteria($request);
        $pagination = $this->getPageAndSize($request);

        $products = $this->product_service->findBy($criteria, $pagination->page, $pagination->size);

        $result = [];

        foreach($products as $product) {

            // item unit
            $item_unit = $this->item_unit_service->findOneBy(["id" => $product->item_unit_id]);
            if (!$item_unit) {
                return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, config('messages.general.error') . ', Produk tidak memiliki satuan!');
            }

            $product = Arr::add($product, "item_unit_name", $item_unit->name);

            $result[] = $product;

        }

        return $this->returnContext(Response::HTTP_OK, config('messages.general.found'), $result, $this->setPagination(
            $pagination->page,
            $pagination->size,
            $this->product_service->count($criteria)
        ));

    }

    public function getById($id) {

        $product = $this->product_service->findOneBy(["id" => $id]);

        if (!$product) {
            return $this->returnContext(Response::HTTP_NOT_FOUND, config('messages.general.not_found'));
        }
        return $this->returnContext(Response::HTTP_OK, config('messages.general.found'), $product);

    }

    public function store(Request $request) {

        /**
         * cek apakah product sudah ada?
         */
        $product = $this->product_service->findOneBy(['slug' => Str::slug($request->name)]);

        if ($product) {
            return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, 'Produk dengan nama '. $request->name .' sudah ada, silahkan gunakan nama lain!');
        }

        DB::beginTransaction();

        /**
         * create produk
         */
        $product = new Product();
        $product->name = $request->name;
        $product->item_unit_id = $request->item_unit_id;
        $product->have_stock = $request->have_stock;

        $product = $this->product_service->create($product);

        /**
         * cek apakah proses nya berhasil atau tidak
         */
        if (!$product->process) {
            DB::rollBack();
            return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, config('messages.general.error') . ', gagal membuat produk');
        }

        /**
         * jika berhasil, create juga category dan product price nya
         *
         */

        // product price
        $product_price = new ProductPrice();
        $product_price->product_id = $product->data->id;
        $product_price->base_price = $request->base_price;
        $product_price->margin = $request->margin;

        $product_price = $this->product_price_service->create($product_price);

        if (!$product_price->process) {
            DB::rollBack();
            return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, config('messages.general.error') . ', gagal membuat produk: [product-price]');
        }

        // categories
        foreach ($request->categories as $value) {

            $product_category = new ProductCategory();
            $product_category->product_id = $product->data->id;
            $product_category->category_id = $value;

            $product_category = $this->product_category_service->create($product_category);

            if (!$product_category->process) {
                DB::rollBack();
                return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, config('messages.general.error') . ', gagal membuat produk: [product-category]');
            }
        }

        DB::commit();
        return $this->returnContext(Response::HTTP_CREATED, config('messages.general.created'));

    }

    public function update($id, Request $request) {

        try {

            $product = $this->product_service->findOneBy(["id" => $id]);

            /**
             * cek produk yg mau diupdate ada apa ngga
             */
            if (!$product) {
                return $this->returnContext(Response::HTTP_NOT_FOUND, config('messages.general.not_found'));
            }

            $check_existing = $this->product_service->findOneBy(["slug" => Str::slug($request->name)]);

            if ($check_existing && $check_existing->id != $product->id){
                return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, "Produk lain dengan nama ". $request->name ." sudah ada, Silahkan gunakan nama lain!");
            }

            DB::beginTransaction();

            // update data produk
            $product->name = $request->name;
            $product->item_unit_id = $request->item_unit_id;
            $product->description = $request->description;
            $product->have_stock = $request->have_stock;

            $product = $this->product_service->update($product);
            if (!$product->process) {
                DB::rollBack();
                return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, config('messages.general.error') . ', gagal memperbarui produk');
            }

            $product_price = $this->product_price_service->findOneBy(["product_id" => $product->data->id]);

            if (!$product_price) {
                DB::rollBack();
                return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, config('messages.general.error') . ', gagal memperbarui produk: product price tidak ditemukan');
            }

            /**
             * cek apakah product price berubah?
             * jika berubah maka create new product price (bukan update)
             */
            if ($product_price->base_price != $request->base_price || $product_price->margin != $request->margin) {
                $new_product_price = new ProductPrice();
                $new_product_price->product_id = $product->data->id;
                $new_product_price->base_price = $request->base_price;
                $new_product_price->margin = $request->margin;

                $new_product_price = $this->product_price_service->create($new_product_price);

                if (!$new_product_price->process) {
                    DB::rollBack();
                    return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, config('messages.general.error') . ', gagal memperbarui produk: [product-price]');
                }
            }

            /**
             * skema ini dipakai ketika product category tiap product hanya 1 to 1, bukan 1 to n
             */
            foreach ($request->categories as $value) {

                $product_category = $this->product_category_service->findOneBy(["product_id" => $product->data->id]);

                if (!$product_category) {
                    DB::rollBack();
                    return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, config('messages.general.error') . ', gagal memperbarui produk: product category tidak ditemukan');
                }

                $product_category->category_id = $value;
                $product_category = $this->product_category_service->update($product_category);
                if (!$product_category->process) {
                    DB::rollBack();
                    return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, config('messages.general.error') . ', gagal memperbarui produk: terjadi kesalahan saat memperbarui kategori');
                }

            }

            DB::commit();
            return $this->returnContext(Response::HTTP_OK, config('messages.general.updated'));

        } catch (Exception $e) {

            DB::rollBack();

        }

    }

    public function updateStatus($id) {

        $product = $this->product_service->findOneBy(["id" => $id]);
        if (!$product) {
            return $this->returnContext(Response::HTTP_NOT_FOUND, config('messages.general.not_found'));
        }

        $product->is_active = ($product->is_active) ? false : true;
        $product = $this->product_service->update($product);
        if (!$product->process) {
            return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, config('messages.general.error'). '. gagal memperbarui status produk');
        }

        return $this->returnContext(Response::HTTP_OK, config('messages.general.updated'));
    }

}
