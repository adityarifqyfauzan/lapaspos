<?php

namespace App\Http\Context\Outlet;

use App\Helper\Activity;
use App\Http\Context\Context;
use App\Models\Outlet;
use App\Repository\OutletRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OutletContext extends Context implements OutletContextInterface
{

    protected OutletRepository $service;

    function __construct(OutletRepository $service) {
        $this->service = $service;
    }

    private function getCriteria(Request $request): array {
        $criteria = [];

        if ($request->query('name') != null) {
            $criteria['name'] = $request->query('name');
        }

        if ($request->query('slug') != null) {
            $criteria['slug'] = $request->query('slug');
        }

        if ($request->query('is_active') != null) {
            $criteria['is_active'] = ($request->query('is_active') == "true") ? true : false;
        }

        return $criteria;
    }

    public function getBy(Request $request) {
        $criteria = $this->getCriteria($request);
        $pagination = $this->getPageAndSize($request);

        $outlets = $this->service->findBy($criteria, $pagination->page, $pagination->size);

        return $this->returnContext(
            Response::HTTP_OK,
            config('messages.general.found'),
            $outlets,
            $this->setPagination(
                $pagination->page,
                $pagination->size,
                $this->service->count($criteria)
            )
        );
    }

    public function getById($id) {
        $outlet = $this->service->findOneBy(["id" => $id]);
        if (!$outlet) {
            return $this->returnContext(Response::HTTP_NOT_FOUND, config('messages.general.not_found'));
        }
        return $this->returnContext(Response::HTTP_OK, config('messages.general.found'), $outlet);
    }

    public function store(Request $request) {
        $check = $this->service->findOneBy(["slug" => Str::slug($request->name)]);
        if ($check) {
            return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, 'Outlet sudah ada');
        }

        $outlet = $this->service->create(new Outlet($request->all()));
        if (!$outlet->process) {

            // set activity
            Activity::payload(
                Auth::user()->id,
                config('constants.activity_purpose.create'),
                '['.config('constants.activity.outlet').'] Berhasil membuat outlet '. $outlet->data->name
            );

            return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, config('messages.general.error') . ' gagal menambahkan role');
        }

        return $this->returnContext(Response::HTTP_CREATED, config('messages.general.created'));
    }

    public function update($id, Request $request) {
        $outlet = $this->service->findOneBy(["id" => $id]);
        if (!$outlet) {
            return $this->returnContext(Response::HTTP_NOT_FOUND, config('messages.general.not_found'));
        }

        $old_outlet_name = $outlet->name;

        $check = $this->service->findOneBy(["slug" => Str::slug($request->name)]);
        if ($check) {
            if ($outlet->id != $check->id) {
                return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, 'outlet sudah ada');
            }
        }

        $outlet->name = $request->name;
        $outlet = $this->service->update($outlet);
        if (!$outlet->process) {
            return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, config('messages.general.error') . ' gagal memperbarui outlet');
        }

        // set activity
        Activity::payload(
            Auth::user()->id,
            config('constants.activity_purpose.update'),
            '['.config('constants.activity.outlet').'] Mengubah nama outlet '. $old_outlet_name .' menjadi ' . $request->name
        );

        return $this->returnContext(Response::HTTP_OK, config('messages.general.updated'));
    }

    public function updateStatus($id) {
        $outlet = $this->service->findOneBy(["id" => $id]);

        if (!$outlet) {
            return $this->returnContext(Response::HTTP_NOT_FOUND, config('messages.general.not_found'));
        }

        $old_outlet_status = $outlet->is_active;

        $outlet->is_active = ($outlet->is_active) ? false : true;
        $update = $this->service->update($outlet);
        if (!$update->process) {
            return $this->returnContext(Response::HTTP_UNPROCESSABLE_ENTITY, config('messages.general.error') . ', gagal memperbarui outlet');
        }

        // set activity
        Activity::payload(
            Auth::user()->id,
            config('constants.activity_purpose.update'),
            '['.config('constants.activity.category').'] Mengubah status kategori '. $update->data->name .' dari '. ($old_outlet_status ? 'aktif' : 'tidak aktif') .' menjadi ' . ($update->data->is_active ? 'aktif' : 'tidak aktif')
        );

        return $this->returnContext(Response::HTTP_OK, config('messages.general.updated'));
    }

}
