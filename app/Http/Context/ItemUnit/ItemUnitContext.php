<?php

namespace App\Http\Context\ItemUnit;

use App\Http\Context\Context;
use App\Models\ItemUnit;
use App\Repository\ItemUnitRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class ItemUnitContext extends Context implements ItemUnitContextInterface
{

    protected $service;

    function __construct(ItemUnitRepository $service)
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

        $item_unit = $this->service->findOneBy(['slug' => Str::slug($request->name)]);

        if ($item_unit) {
            return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, 'Satuan sudah ada!');
        }

        $resp = $this->service->create(new ItemUnit($request->all()));

        if ($resp->process) {
            return $this->returnContext(Response::HTTP_CREATED, config('messages.general.created'));
        }

        return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, config('messages.general.error') . ', gagal membuat satuan');

    }

    public function update($id, Request $request) {

        /**
         * cari Satuan yang mau diupdate
         */
        $item_unit = $this->service->findOneBy(["id" => $id]);

        /**
         * cek apakah Satuan tersebut ada atau tidak
         */
        if ($item_unit) {

            /**
             * bagian ini digunakan untuk cek apakah nama Satuan yang baru
             * sebelumnya sudah dipakai oleh data lain
             */
            $check = $this->service->findOneBy(['slug' => $request->name]);

            if ($check && $check->id != $item_unit->id) {
                return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, 'Satuan sudah ada!');
            }

            /**
             * jika Satuan belum tersedia, maka update
             */
            $item_unit->name = $request->name;

            $update = $this->service->update($item_unit);

            if ($update->process) {
                return $this->returnContext(Response::HTTP_OK, config('messages.general.updated'));
            }

            return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, config('messages.general.error') . ', gagal memperbarui satuan');
        }

        return $this->returnContext(Response::HTTP_NOT_FOUND, config('messages.general.not_found'));

    }

    public function updateStatus($id) {
        $item_unit = $this->service->findOneBy(["id" => $id]);

        if ($item_unit) {

            $item_unit->is_active = ($item_unit->is_active) ? false : true;

            $update = $this->service->update($item_unit);

            if ($update->process) {
                return $this->returnContext(Response::HTTP_OK, config('messages.general.updated'));
            }

            return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, config('messages.general.error') . ', gagal memperbarui satuan');

        }

        return $this->returnContext(Response::HTTP_NOT_FOUND, config('messages.general.not_found'));
    }

}
