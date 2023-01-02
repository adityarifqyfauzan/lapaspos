<?php

namespace App\Http\Context\Category;

use App\Http\Context\Context;
use App\Models\Category;
use App\Repository\CategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class CategoryContext extends Context implements CategoryContextInterface
{

    protected CategoryRepository $category_service;

    function __construct(CategoryRepository $category_service)
    {
        $this->category_service = $category_service;
    }

    private function getCriteria(Request $request): array {
        $criteria = [];

        if ($request->query('category_id') != null) {
            $criteria['id'] = $request->query('category_id');
        }

        if ($request->query('name') != null) {
            $criteria['name'] = $request->query('name');
        }

        if ($request->query('slug') != null) {
            $criteria['slug'] = $request->query('slug');
        }

        if ($request->query('is_active') != null) {
            $criteria['is_active'] = ($request->query('is_active') == "true") ? 1 : 0;
        }

        return $criteria;
    }

    public function getBy(Request $request) {

        $criteria = $this->getCriteria($request);
        $pagination = $this->getPageAndSize($request);

        $resp = $this->category_service->findBy($criteria, $pagination->page, $pagination->size);

        return $this->returnContext(Response::HTTP_OK, config('messages.general.found'), $resp, $this->setPagination($pagination->page, $pagination->size, $this->category_service->count($criteria)));

    }

    public function getById($id) {

        $resp = $this->category_service->findOneBy(["id" => $id]);

        if ($resp->data) {
            return $this->returnContext(Response::HTTP_OK, config('messages.general.found'), $resp->data);
        }

        return $this->returnContext(Response::HTTP_NOT_FOUND, config('messages.general.not_found'));

    }

    public function store(Request $request) {

        $category = $this->category_service->findOneBy(['slug' => Str::slug($request->name)]);

        if ($category->data) {
            return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, 'Kategori sudah ada!');
        }

        $resp = $this->category_service->create(new Category($request->all()));

        if ($resp->process) {
            return $this->returnContext(Response::HTTP_CREATED, config('messages.general.created'));
        }

        return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, config('messages.general.error') . ', gagal membuat kategori');

    }

    public function update($id, Request $request) {

        /**
         * cari kategori yang mau diupdate
         */
        $category = $this->category_service->findOneBy(["id" => $id]);

        /**
         * cek apakah kategori tersebut ada atau tidak
         */
        if ($category->data) {

            /**
             * bagian ini digunakan untuk cek apakah nama kategori yang baru
             * sebelumnya sudah dipakai oleh data lain
             */
            $check = $this->category_service->findOneBy(['slug' => $request->name]);

            if ($check->data && $check->data->id != $category->data->id) {
                return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, 'Kategori sudah ada!');
            }

            /**
             * jika kategori belum tersedia, maka update
             */
            $category->data->name = $request->name;

            $update = $this->category_service->update($category->data);

            if ($update->process) {
                return $this->returnContext(Response::HTTP_OK, config('messages.general.updated'));
            }

            return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, config('messages.general.error') . ', gagal memperbarui kategori');

        }

        return $this->returnContext(Response::HTTP_NOT_FOUND, config('messages.general.not_found'));

    }

    public function updateStatus($id) {
        $category = $this->category_service->findOneBy(["id" => $id]);

        if ($category->data) {

            $category->data->is_active = ($category->data->is_active) ? false : true;

            $update = $this->category_service->update($category->data);

            if ($update->process) {
                return $this->returnContext(Response::HTTP_OK, config('messages.general.updated'));
            }

            return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, config('messages.general.error') . ', gagal memperbarui kategori');

        }

        return $this->returnContext(Response::HTTP_NOT_FOUND, config('messages.general.not_found'));
    }

}
