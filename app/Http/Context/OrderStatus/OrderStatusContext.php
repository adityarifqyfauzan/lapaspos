<?php

namespace App\Http\Context\OrderStatus;

use App\Http\Context\Context;
use App\Repository\OrderStatusRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderStatusContext extends Context implements OrderStatusContextInterface
{

    protected OrderStatusRepository $service;

    function __construct(OrderStatusRepository $service) {
        $this->service = $service;
    }

    private function getCriteria(Request $request): array {
        $criteria = [];

        if ($request->query('is_active') != null) {
            $criteria['is_active'] = ($request->query('is_active') == "true") ? 1 : 0;
        }

        return $criteria;
    }

    public function getBy(Request $request) {

        $criteria = $this->getCriteria($request);
        $pagination = $this->getPageAndSize($request);

        $order_statuses = $this->service->findBy($criteria, $pagination->page, $pagination->size);

        return $this->returnContext(
            Response::HTTP_OK,
            config('messages.general.found'),
            $order_statuses,
            $this->setPagination(
                $pagination->page,
                $pagination->size,
                $this->service->count($criteria)
            )
        );

    }
}
