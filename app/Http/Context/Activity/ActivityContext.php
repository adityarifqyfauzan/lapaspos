<?php

namespace App\Http\Context\Activity;

use App\Http\Context\Context;
use App\Repository\ActivityRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ActivityContext extends Context implements ActivityContextInterface
{

    protected ActivityRepository $service;

    function __construct(ActivityRepository $service) {
        $this->service = $service;
    }

    private function getCriteria(Request $request): array {
        $criteria = [];

        if ($request->query('user_id') != null) {
            $criteria['user_id'] = $request->query('user_id');
        }

        return $criteria;
    }

    public function getBy(Request $request){

        $criteria = $this->getCriteria($request);
        $pagination = $this->getPageAndSize($request);

        $resp = $this->service->findBy($criteria, $pagination->page, $pagination->size);

        return $this->returnContext(
            Response::HTTP_OK,
            config('messages.general.found'),
            $resp,
            $this->setPagination(
                $pagination->page,
                $pagination->size,
                $this->service->count($criteria)
            )
        );

    }

    public function getById($id){

    }

}
