<?php

namespace App\Http\Context\Reporting;

use App\Helper\TimeConverter;
use App\Http\Context\Context;
use App\Repository\ReportingRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class ReportingContext extends Context implements ReportingContextInterface
{

    protected ReportingRepository $reporting_service;

    function __construct(ReportingRepository $reporting_service) {
        $this->reporting_service = $reporting_service;
    }

    private function getCriteria(Request $request): array {
        $criteria = [];

        if ($request->query('start_date') != null) {
            $criteria['start_date'] = $request->query('start_date');
        }

        if ($request->query('end_date') != null) {
            $criteria['end_date'] = $request->query('end_date');
        }

        if ($request->query('is_today') != null) {
            $criteria['is_today'] = ($request->query('is_today') == "true") ? true : false;
        }

        if ($request->query('is_quarter') != null) {
            $criteria['is_quarter'] = ($request->query('is_quarter') == "true") ? true : false;
        }

        if ($request->query('is_semester') != null) {
            $criteria['is_semester'] = ($request->query('is_semester') == "true") ? true : false;
        }

        if ($request->query('is_max') != null) {
            $criteria['is_max'] = ($request->query('is_max') == "true") ? true : false;
        }

        return $criteria;
    }

    public function summary(Request $request) {

        $criteria = $this->getCriteria($request);
        $criteria["orders.order_status_id"] = config('constants.order_status.lunas');

        $user = Auth::user();

        if ($user->role_id != 1) {
            $criteria["orders.user_id"] = $user->id;
        }

        $gross_profit = $this->reporting_service->grossProfit($criteria);
        $transactions = $this->reporting_service->transaction($criteria);
        $margin = $this->reporting_service->margin($criteria);

        $result = [];
        $result = Arr::add($result, "gross_profit", $gross_profit);
        $result = Arr::add($result, "transactions", $transactions);
        $result = Arr::add($result, "margin", $margin);

        return $this->returnContext(
            Response::HTTP_OK,
            config('messages.general.found'),
            $result
        );

    }

    public function productSale(Request $request) {
        $criteria = $this->getCriteria($request);
        $pagination = $this->getPageAndSize($request);

        // get only from successed transaction
        $criteria['orders.order_status_id'] = config('constants.order_status.lunas');

        $user = Auth::user();

        if ($user->role_id != 1) {
            $criteria["orders.user_id"] = $user->id;
        }

        $product_sales = $this->reporting_service->productSale($criteria, $pagination->page, $pagination->size);

        return $this->returnContext(Response::HTTP_OK, config('messages.general.found'), $product_sales, $this->setPagination(
            $pagination->page,
            $pagination->size,
            $this->reporting_service->productSaleCount($criteria)
        ));
    }

    public function transactionSummary(Request $request) {
        $criteria = $this->getCriteria($request);

        $transaction_summary = $this->reporting_service->transactionSummary($criteria);

        $result = [];

        foreach ($transaction_summary as $value) {
            $result[] = [
                "month" => TimeConverter::parseMonth($value->month),
                "year" => (int) $value->year,
                "total_transaction" => (int) $value->total_transaction
            ];
        }

        return $this->returnContext(
            Response::HTTP_OK,
            config('messages.general.found'),
            $result
        );
    }
}
