<?php

namespace App\Http\Context\Order;

use App\Helper\ProductStockHelper;
use App\Http\Context\Context;
use App\Jobs\OrderStatusHistoryJob;
use App\Jobs\ProductStockUpdateJob;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Repository\OrderDetailRepository;
use App\Repository\OrderRepository;
use App\Repository\OrderStatusHistoryRepository;
use App\Repository\OrderStatusRepository;
use App\Repository\PaymentRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Services\ProductStockService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderContext extends Context implements OrderContextInterface
{

    protected OrderRepository $order_service;

    protected OrderDetailRepository $order_detail_service;

    protected OrderStatusRepository $order_status_service;

    protected OrderStatusHistoryRepository $order_status_history_service;

    protected ProductRepository $product_service;

    protected PaymentRepository $payment_service;

    protected ProductStockService $product_stock_service;

    protected UserRepository $user_service;

    function __construct(OrderRepository $order_service, OrderDetailRepository $order_detail_service, OrderStatusRepository $order_status_service, ProductRepository $product_service, OrderStatusHistoryRepository $order_status_history_service, PaymentRepository $payment_service, ProductStockService $product_stock_service, UserRepository $user_service) {
        $this->order_service = $order_service;
        $this->order_detail_service = $order_detail_service;
        $this->order_status_service = $order_status_service;
        $this->order_status_history_service = $order_status_history_service;
        $this->product_service = $product_service;
        $this->payment_service = $payment_service;
        $this->product_stock_service = $product_stock_service;
        $this->user_service = $user_service;
    }

    private function getCriteria(Request $request): array {
        $criteria = [];

        if ($request->query('code') != null) {
            $criteria['code'] = $request->query('code');
        }

        if ($request->query('order_status_id') != null) {
            $criteria['order_status_id'] = $request->query('order_status_id');
        }

        if ($request->query('start_date') != null) {
            $criteria['start_date'] = $request->query('start_date');
        }

        if ($request->query('end_date') != null) {
            $criteria['end_date'] = $request->query('end_date');
        }

        if ($request->query('is_today') != null) {
            $criteria['is_today'] = ($request->query('is_today') == "true") ? true : false;
        }

        return $criteria;
    }

    public function getBy(Request $request) {

        $criteria = $this->getCriteria($request);
        $pagination = $this->getPageAndSize($request);

        $user = Auth::user();

        if ($user->role_id != config('constants.roles.admin')) {
            $criteria["user_id"] = $user->id;
        }

        $orders = $this->order_service->findBy($criteria, $pagination->page, $pagination->size);

        $result = [];

        foreach ($orders as $order) {

            $qty = $this->order_service->sumQty($order->id);
            $total = $this->order_service->sumTotal($order->id);

            $order_status = $this->order_status_service->findOneBy(["id" => $order->order_status_id]);

            $result[] = [
                "id" => $order->id,
                "order_code" => $order->code,
                "order_date" => $order->created_at,
                "order_status_id" => $order->order_status_id,
                "order_status" => $order_status->name,
                "product_qty" => $qty,
                "total_amount" => $total
            ];

        }

        return $this->returnContext(
            Response::HTTP_OK,
            config('messages.general.found'),
            $result,
            $this->setPagination(
                $pagination->page,
                $pagination->size,
                $this->order_service->count($criteria)
            )
        );

    }

    // under construction
    public function getByID($id) {

        $order = $this->order_service->findOneBy(["id" => $id]);
        if (!$order) {
            return $this->returnContext(
                Response::HTTP_NOT_FOUND,
                config('messages.general.not_found') . ' order tidak ada'
            );
        }

        $user = $this->user_service->findOneBy(["id" => $order->user_id]);
        if (!$user) {
            return $this->returnContext(
                Response::HTTP_NOT_FOUND,
                config('messages.general.not_found') . ' kasir tidak ada'
            );
        }

        $order_details = $this->order_detail_service->findBy(["order_id" => $order->id], 0, 0);

        $details = [];
        $total_amount = 0;

        foreach ($order_details as $order_detail) {

            $details[] = [
                "product_name" => $order_detail->product->name,
                "product_price" => $order_detail->product_price->final_price,
                "qty" => $order_detail->qty,
                "final_amount" => $order_detail->total
            ];

            $total_amount += $order_detail->total;

        }


        // cashier name
        $result["cashier"] = $user->name;

        // order information
        $result["order_id"] = $order->id;
        $result["order_code"] = $order->code;
        $result["order_date"] = Carbon::parse($order->created_at)->timezone('Asia/Jakarta')->format('Y-m-d H:i:s');
        $result["order_status_id"] = $order->order_status_id;
        $result["order_status"] = $order->order_status->name;
        $result["order_detail"] = $details;
        $result["total_amount"] = $total_amount;
        $result["paid"] = 0;
        $result["change"] = 0;

        $payment = $this->payment_service->findOneBy(["order_id" => $order->id]);
        if ($payment) {
            $result["paid"] = $payment->paid;
            $result["change"] = $payment->paid - $payment->amount;
        }

        return $this->returnContext(
            Response::HTTP_OK,
            config('messages.general.found'),
            $result
        );

    }

    public function store(Request $request) {

        DB::beginTransaction();

        try {

            // get products
            $products = $request->products;

            // product stock helper
            // product stock helper
            $product_stock_helper = new ProductStockHelper($this->product_stock_service);

            // total amount
            $total_payment = 0;
            $total_update_payment = 0;

            // updated qty
            $updated_qty = [];

            // products
            $order_result = [];

            // cek apakah data product ada atau tidak
            if (count($products) < 1) {
                DB::rollback();
                return $this->returnContext(
                    Response::HTTP_BAD_REQUEST,
                    'Produk tidak boleh kosong'
                );
            }


            // create order
            $order = new Order();
            $order->note = $request->note;
            $order = $this->order_service->create($order);
            if (!$order->process) {
                DB::rollback();
                return $this->returnContext(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    config('messages.general.error') . ' gagal membuat order. [order]'
                );
            }

            $product_collect = [];

            // create order detail
            foreach ($products as $value) {

                // cast value as object
                $value = (object) $value;

                // cek product nya
                $product = $this->product_service->findOneBy(["id" => $value->product_id]);
                    if (!$product) {
                    DB::rollback();
                    return $this->returnContext(
                        Response::HTTP_NOT_FOUND,
                        config('messages.general.not_found') . ' Produk tidak ada!'
                    );
                }

                // cek qty
                if ($value->qty < 1) {
                    DB::rollback();
                    return $this->returnContext(
                        Response::HTTP_BAD_REQUEST,
                        'Qty tidak boleh kurang dari 1'
                    );
                }

                // cek stock
                if ($product->have_stock) {

                    $stock = $product_stock_helper->getProductStock($product->id);

                    // cek stok apabila terjadi double product dalam 1 request
                    if (in_array($product->id, $product_collect) && $stock - ($updated_qty[$product->id] + $value->qty) < 0) {
                        DB::rollback();
                        return $this->returnContext(
                            Response::HTTP_UNPROCESSABLE_ENTITY,
                            'Stok '. $product->name .' tidak cukup. Jumlah stok saat ini: '. $stock
                        );
                    }

                    if ($stock - $value->qty < 0) {
                        DB::rollback();
                        return $this->returnContext(
                            Response::HTTP_UNPROCESSABLE_ENTITY,
                            'Stok '. $product->name .' tidak cukup. Jumlah stok saat ini: '. $stock
                        );
                    }
                }

                // pengecekan apabila product terbuat 2x dalam 1x order [negative case]
                // maka dia akan melakukan update terhadap order detail dengan product id tsb
                if (in_array($value->product_id, $product_collect)) {
                    $order_detail = $this->order_detail_service->findOneBy(["product_id" => $value->product_id, "order_id" => $order->data->id]);
                    if (!$order_detail) {
                        DB::rollback();
                        return $this->returnContext(
                            Response::HTTP_UNPROCESSABLE_ENTITY,
                            config('messages.general.error') . ' detail pesanan tidak ada: [update qty order detail]'
                        );
                    }
                    $order_detail->qty += $value->qty;
                    $order_detail->total = $product->product_price->final_price * $order_detail->qty;

                    $updated_qty[$value->product_id] = $order_detail->qty;

                    $total_update_payment += $total_update_payment + ($product->product_price->final_price * $value->qty);

                    $order_detail = $this->order_detail_service->update($order_detail);
                    if (!$order_detail->process) {
                        DB::rollback();
                        return $this->returnContext(
                            Response::HTTP_UNPROCESSABLE_ENTITY,
                            config('messages.general.error') . ' gagal membuat detail order [update]'
                        );
                    }

                    continue;
                }

                // create order detail
                $order_detail = new OrderDetail();
                $order_detail->order_id = $order->data->id;
                $order_detail->product_id = $product->id;
                $order_detail->product_price_id = $product->product_price->id;
                $order_detail->qty = $value->qty;
                $order_detail->total = $product->product_price->final_price * $value->qty;

                $updated_qty = Arr::add($updated_qty, $product->id, $order_detail->qty);

                $total_payment += $order_detail->total;

                $order_detail = $this->order_detail_service->create($order_detail);
                if (!$order_detail->process) {
                    DB::rollback();
                    return $this->returnContext(
                        Response::HTTP_UNPROCESSABLE_ENTITY,
                        config('messages.general.error') . ' gagal membuat detail order'
                    );
                }

                array_push($product_collect, $value->product_id);

            }

            $payload_history = [
                "order_id" => $order->data->id,
                "current_status" => 1,
                "previous_status" => 1
            ];

            dispatch(new OrderStatusHistoryJob((object) $payload_history));

            // update product stock
            // get order detail
            $payload_product_stock = [];
            $order_details = $this->order_detail_service->findBy(["order_id" => $order->data->id], 0, 0);

            foreach ($order_details as $value) {
                $payload_product_stock[] = [
                    "product_id" => $value->product_id,
                    "supplier_id" => 1,
                    "stock" => $value->qty,
                    "status" => "sale"
                ];

                $order_result[] = [
                    "product_name" => $value->product->name,
                    "qty" => $value->qty,
                    "subtotal" => $value->total
                ];
            }
            dispatch(new ProductStockUpdateJob((object)$payload_product_stock));

            DB::commit();
            return $this->returnContext(
                Response::HTTP_CREATED,
                config('messages.general.created') . ' Order berhasil dibuat',
                [
                    "order_id" => $order->data->id,
                    "products" => $order_result,
                    "total_payment" => $total_payment + $total_update_payment
                ]
            );

        } catch (Exception $e) {
            DB::rollback();

            report($e->getMessage());
            return $this->returnContext(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                config('messages.general.error') . ' hubungi administrator. ' . $e->getMessage()
            );
        }

    }

    public function cancelOrder($id)
    {
        try {

            $order = $this->order_service->findOneBy(["id" => $id]);
            if (!$order) {
                return $this->returnContext(
                    Response::HTTP_NOT_FOUND,
                    config('messages.general.not_found') . ' order tidak ada [order_id = '. $id .']'
                );
            }

            if ($order->order_status_id == config('constants.order_status.dibatalkan')) {
                return $this->returnContext(
                    Response::HTTP_OK,
                    'Pesanan ' . $order->code . ' sudah dibatalkan'
                );
            }

            if ($order->order_status_id == config('constants.order_status.lunas')) {
                return $this->returnContext(
                    Response::HTTP_OK,
                    'Pesanan ' . $order->code . ' sudah berhasil'
                );
            }

            // get previous order status
            $previous_order_status = $order->order_status_id;

            $order->order_status_id = config('constants.order_status.dibatalkan');
            $order = $this->order_service->update($order);
            if (!$order->process) {
                DB::rollback();
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

            dispatch(new OrderStatusHistoryJob((object)$payload_order_history));

            // update product stock
            // get order detail
            $payload_product_stock = [];
            $order_details = $this->order_detail_service->findBy(["order_id" => $order->data->id], 0, 0);

            foreach ($order_details as $value) {
                $payload_product_stock[] = [
                    "product_id" => $value->product_id,
                    "supplier_id" => 1,
                    "stock" => $value->qty,
                    "status" => "order_cancel"
                ];
            }

            dispatch(new ProductStockUpdateJob((object)$payload_product_stock));

            return $this->returnContext(
                Response::HTTP_OK,
                'Pesanan dibatalkan'
            );

        } catch (Exception $e) {

            DB::rollback();

            report($e->getMessage());
            return $this->returnContext(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                config('messages.general.error') . ' hubungi administrator. ' . $e->getMessage()
            );

        }
    }

}
