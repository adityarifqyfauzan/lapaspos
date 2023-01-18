<?php

namespace App\Http\Context\Payment;

use App\Http\Context\Context;
use App\Jobs\OrderStatusHistoryJob;
use App\Jobs\PaymentStatusHistoryJob;
use App\Models\Payment;
use App\Repository\OrderDetailRepository;
use App\Repository\OrderRepository;
use App\Repository\PaymentMethodRepository;
use App\Repository\PaymentRepository;
use App\Repository\PaymentStatusHistoryRepository;
use App\Repository\PaymentStatusRepository;
use App\Repository\ProductStockRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentContext extends Context implements PaymentContextInterface
{
    protected PaymentRepository $payment_service;

    protected OrderRepository $order_service;

    protected OrderDetailRepository $order_detail_service;

    protected PaymentStatusRepository $payment_status_service;

    protected PaymentMethodRepository $payment_method_service;

    protected PaymentStatusHistoryRepository $payment_status_history_service;

    protected ProductStockRepository $product_stock_service;

    function __construct(PaymentRepository $payment_service, OrderRepository $order_service, PaymentStatusRepository $payment_status_service, PaymentMethodRepository $payment_method_service, PaymentStatusHistoryRepository $payment_status_history_service, ProductStockRepository $product_stock_service, OrderDetailRepository $order_detail_service) {
        $this->payment_service = $payment_service;
        $this->order_service = $order_service;
        $this->order_detail_service = $order_detail_service;
        $this->payment_method_service = $payment_method_service;
        $this->payment_status_service = $payment_status_service;
        $this->payment_status_history_service = $payment_status_history_service;
        $this->product_stock_service = $product_stock_service;
    }

    public function getPayment($id) {

        $order = $this->order_service->findOneBy(["id" => $id]);
        if (!$order) {
            return $this->returnContext(
                Response::HTTP_NOT_FOUND,
                config('messages.general.not_found') . ' order tidak ada'
            );
        }

        if ($order->order_status_id == config('constants.order_status.lunas')) {
            return $this->returnContext(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                'Pesanan sudah dibayar'
            );
        }

        if ($order->order_status_id == config('constants.order_status.dibatalkan')) {
            return $this->returnContext(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                'Pesanan sudah dibatalkan'
            );
        }

        $order_details = $this->order_detail_service->findBy(["order_id" => $id], 0, 0);

        $order_result = [];
        $total_payment = 0;

        foreach ($order_details as $value) {
            $order_result[] = [
                "product_name" => $value->product->name,
                "qty" => $value->qty,
                "subtotal" => $value->total
            ];
            $total_payment += $value->total;
        }

        return $this->returnContext(
            Response::HTTP_OK,
            config('messages.general.found'),
            [
                "order_id" => $order->id,
                "products" => $order_result,
                "total_payment" => $total_payment
            ]
        );
    }

    public function store(Request $request) {

        try {

            // cek order
            $order = $this->order_service->findOneBy(["id" => $request->order_id, "outlet_id" => Auth::user()->outlet_id]);
            if (!$order) {
                return $this->returnContext(
                    Response::HTTP_NOT_FOUND,
                    config('messages.general.not_found') . ' pesanan tidak ada'
                );
            }

            // cek apakah order sudah dibayar sebelumnya ?
            if ($order->order_status_id == config('constants.order_status.lunas')) {
                return $this->returnContext(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    'Pesanan sudah dibayar'
                );
            }

            // cek payment method
            $payment_method = $this->payment_method_service->findOneBy(["id" => $request->payment_method_id]);
            if (!$payment_method) {
                return $this->returnContext(
                    Response::HTTP_NOT_FOUND,
                    config('messages.general.not_found') . ' metode pembayaran tidak ada'
                );
            }

            // cek apakah pembayaran sudah sesuai dengan total pada order?
            $total = $this->order_service->sumTotal($order->id);
            if ($request->paid < $total) {
                return $this->returnContext(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    'Uang yang dibayarkan kurang Rp.'. $total - $request->paid .'!'
                );
            }

            DB::beginTransaction();

            // create payment
            $payment = new Payment();
            $payment->order_id = $request->order_id;
            $payment->payment_method_id = $request->payment_method_id;
            $payment->paid = $request->paid;
            $payment->amount = $total;
            $payment->payment_status_id = config('constants.payment_status.pending');

            // cek payment yang dipilih, apabila methodnya adalah tunai,
            // maka status payment tsb langsung sukses
            if ($payment_method->slug == config('constants.payment_method.tunai')) {
                $payment->payment_status_id = config('constants.payment_status.sukses');
            }

            $payment = $this->payment_service->create($payment);
            if (!$payment->process) {
                DB::rollBack();
                return $this->returnContext(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    config('messages.general.error') . ' gagal membuat pembayaran'
                );
            }

            // apabila berhasil create payment dan status payment adalah sukses,
            // jangan lupa update status order
            if ($payment->data->payment_status_id == config('constants.payment_status.sukses')) {
                // get previous order status
                $previous_order_status = $order->order_status_id;

                $order->order_status_id = config('constants.order_status.lunas');
                $order = $this->order_service->update($order);
                if (!$order->process) {
                    DB::rollBack();
                    return $this->returnContext(
                        Response::HTTP_UNPROCESSABLE_ENTITY,
                        config('messages.general.error') . ' gagal memperbarui status pesanan'
                    );
                }

                // set current order status
                $current_order_status = $order->data->order_status_id;

                $payload_order_history = [
                    "order_id" => $order->data->id,
                    "current_status" => $current_order_status,
                    "previous_status" => $previous_order_status
                ];

                dispatch(new OrderStatusHistoryJob((object)$payload_order_history))->afterCommit();

            }

            $payload_payment_history = [
                "payment_id" => $order->data->id,
                "current_status" => $payment->data->payment_status_id,
                "previous_status" => $payment->data->payment_status_id
            ];

            dispatch(new PaymentStatusHistoryJob((object) $payload_payment_history))->afterCommit();

            DB::commit();

            return $this->returnContext(
                Response::HTTP_CREATED,
                'Pembayaran berhasil'
            );

        } catch (Exception $e) {

            DB::rollBack();

            report($e->getMessage());
            return $this->returnContext(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                config('messages.general.error') . ' hubungi administrator. ' . $e->getMessage()
            );

        }

    }
}
