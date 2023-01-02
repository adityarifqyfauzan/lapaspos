<?php

namespace App\Http\Context\Stock;

use App\Http\Context\Context;
use App\Models\ProductStock;
use App\Repository\ProductRepository;
use App\Repository\ProductStockRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StockContext extends Context implements StockContextInterface
{

    protected ProductStockRepository $product_stock_service;

    protected ProductRepository $product_service;

    function __construct(ProductStockRepository $product_stock_service, ProductRepository $product_service)
    {
        $this->product_stock_service = $product_stock_service;
        $this->product_service = $product_service;
    }

    private function getCriteria(Request $request): array {
        $criteria = [];

        if ($request->query('id') != null) {
            $criteria['id'] = $request->query('id');
        }

        if ($request->query('status') != null) {
            $criteria['status'] = $request->query('status');
        }

        if ($request->query('supplier_id') != null) {
            $criteria['supplier_id'] = $request->query('supplier_id');
        }

        if ($request->query('product_id') != null) {
            $criteria['product_id'] = $request->query('product_id');
        }

        if ($request->query('start_date') != null) {
            $criteria['start_date'] = $request->query('start_date');
        }

        if ($request->query('end_date') != null) {
            $criteria['end_date'] = $request->query('end_date');
        }

        return $criteria;
    }

    public function getBy(Request $request) {

        $criteria = $this->getCriteria($request);
        $pagination = $this->getPageAndSize($request);

        $product_stocks = $this->product_stock_service->findPagedBy($criteria, $pagination->page, $pagination->size);

        return $this->returnContext(
            Response::HTTP_OK,
            config('messages.general.found'),
            $product_stocks,
            $this->setPagination(
                $pagination->page,
                $pagination->size,
                $this->product_stock_service->count($criteria)
            )
        );

    }

    public function getOneBy($id) {

        $product_stock = $this->product_stock_service->findOneBy(["id" => $id]);
        if (!$product_stock) {
            return $this->returnContext(Response::HTTP_NOT_FOUND, config('messages.general.not_found'));
        }
        return $this->returnContext(Response::HTTP_OK, config('messages.general.found'), $product_stock);
    }

    public function store(Request $request) {

        $product = $this->product_service->findOneBy(["id" => $request->product_id]);
        if (!$product) {
            return $this->returnContext(Response::HTTP_NOT_FOUND, config('messages.general.not_found') . ' produk tidak ada');
        }

        if (!$product->have_stock) {
            return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, config('messages.general.error') . ' produk ini tidak bisa ditambahkan stok');
        }

        $product_stock = $this->product_stock_service->create(new ProductStock($request->all()));

        if (!$product_stock->process) {
            return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, config('messages.general.error') . ' gagal membuat stock');
        }
        return $this->returnContext(Response::HTTP_CREATED, config('messages.general.created'));
    }

    public function update($id, Request $request) {

        $product_stock = $this->product_stock_service->findOneBy(["id" => $id]);
        if (!$product_stock) {
            return $this->returnContext(Response::HTTP_NOT_FOUND, config('messages.general.not_found'));
        }

        $product = $this->product_service->findOneBy(["id" => $request->product_id]);
        if (!$product) {
            return $this->returnContext(Response::HTTP_NOT_FOUND, config('messages.general.not_found') . ' produk tidak ada');
        }

        if (!$product->have_stock) {
            return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, config('messages.general.error') . ' produk ini tidak bisa ditambahkan stok');
        }

        $product_stock->product_id = $request->product_id;
        $product_stock->supplier_id = $request->supplier_id;
        $product_stock->stock = $request->stock;
        $product_stock->description = $request->description;

        $product_stock = $this->product_stock_service->update($product_stock);
        if (!$product_stock->process) {
            return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, config('messages.general.error'). ' gagal memperbarui stock');
        }

        return $this->returnContext(Response::HTTP_OK, config('messages.general.updated'));

    }

    public function delete($id) {

        $product_stock = $this->product_stock_service->findOneBy(["id" => $id]);
        if (!$product_stock) {
            return $this->returnContext(Response::HTTP_NOT_FOUND, config('messages.general.not_found'));
        }

        $product_stock = $this->product_stock_service->delete($product_stock);
        if (!$product_stock->process) {
            return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, config('messages.general.error'). ' gagal menghapus stock');
        }

        return $this->returnContext(Response::HTTP_OK, config('messages.general.deleted'));
    }

}
