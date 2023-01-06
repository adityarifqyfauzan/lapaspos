<?php

namespace App\Http\Controllers\Order;

use App\Http\Context\Order\OrderContextInterface;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderController extends Controller
{

    protected OrderContextInterface $context;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $resp = $this->context->getBy($request);

            return $this->success(
                $resp->message,
                $resp->data,
                $resp->http_status,
                $resp->pagination
            );

        } catch (Exception $e) {
            return $this->failed($this->error($e->getMessage()));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {


        } catch (Exception $e) {
            return $this->failed($this->error($e->getMessage()));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {


        } catch (Exception $e) {
            return $this->failed($this->error($e->getMessage()));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {

            $resp = $this->context->getByID($id);

            if ($resp->http_status == Response::HTTP_OK) {
                return $this->success(
                    $resp->message,
                    $resp->data,
                    $resp->http_status
                );
            }

            return $this->failed(
                $resp->message,
                $resp->data,
                $resp->http_status
            );

        } catch (Exception $e) {
            return $this->failed($this->error($e->getMessage()));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {


        } catch (Exception $e) {
            return $this->failed($this->error($e->getMessage()));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {


        } catch (Exception $e) {
            return $this->failed($this->error($e->getMessage()));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {


        } catch (Exception $e) {
            return $this->failed($this->error($e->getMessage()));
        }
    }
}
