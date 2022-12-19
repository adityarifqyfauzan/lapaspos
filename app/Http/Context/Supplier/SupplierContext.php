<?php

namespace App\Http\Context\Supplier;

use App\Http\Context\Context;
use App\Models\Supplier;
use App\Repository\SupplierRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class SupplierContext extends Context implements SupplierContextInterface
{

    protected SupplierRepository $service;

    function __construct(SupplierRepository $service)
    {
        $this->service = $service;
    }

    public function getBy(Request $request) {

        $pagination = $this->getPageAndSize($request);

        $resp = $this->service->findBy([], $pagination->page, $pagination->size);

        return $this->returnContext(Response::HTTP_OK, config('messages.general.found'), $resp, $this->setPagination($pagination->page, $pagination->size, $this->service->count([])));

    }

    public function getById($id) {

        $resp = $this->service->findOneBy(["id" => $id]);

        if ($resp) {
            return $this->returnContext(Response::HTTP_OK, config('messages.general.found'), $resp);
        }

        return $this->returnContext(Response::HTTP_NOT_FOUND, config('messages.general.not_found'));

    }

    public function store(Request $request) {

        $supplier = $this->service->findOneBy(['slug' => Str::slug($request->name)]);

        if ($supplier) {
            return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, 'Supplier sudah ada!');
        }

        $resp = $this->service->create(new Supplier($request->all()));

        if ($resp->process) {
            return $this->returnContext(Response::HTTP_CREATED, config('messages.general.created'));
        }

        return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, config('messages.general.error') . ', gagal membuat supplier');

    }

    public function update($id, Request $request) {

        /**
         * cari Supplier yang mau diupdate
         */
        $supplier = $this->service->findOneBy(["id" => $id]);

        /**
         * cek apakah Supplier tersebut ada atau tidak
         */
        if ($supplier) {

            /**
             * bagian ini digunakan untuk cek apakah nama Supplier yang baru
             * sebelumnya sudah dipakai oleh data lain
             */
            $check = $this->service->findOneBy(['slug' => Str::slug($request->name)]);

            if ($check && $check->id != $supplier->id) {
                return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, 'Supplier sudah ada!');
            }

            /**
             * jika Supplier belum tersedia, maka update
             */
            $supplier->name = $request->name;
            $supplier->description = $request->description;

            $update = $this->service->update($supplier);

            if ($update->process) {
                return $this->returnContext(Response::HTTP_OK, config('messages.general.updated'));
            }

            return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, config('messages.general.error') . ', gagal memperbarui Supplier');
        }

        return $this->returnContext(Response::HTTP_NOT_FOUND, config('messages.general.not_found'));

    }

    public function updateStatus($id) {
        $supplier = $this->service->findOneBy(["id" => $id]);

        if ($supplier) {

            $supplier->is_active = ($supplier->is_active) ? false : true;

            $update = $this->service->update($supplier);

            if ($update->process) {
                return $this->returnContext(Response::HTTP_OK, config('messages.general.updated'));
            }

            return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, config('messages.general.error') . ', gagal memperbarui supplier');

        }

        return $this->returnContext(Response::HTTP_NOT_FOUND, config('messages.general.not_found'));
    }

}
