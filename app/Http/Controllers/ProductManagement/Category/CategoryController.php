<?php

namespace App\Http\Controllers\ProductManagement\Category;

use App\Http\Context\Category\CategoryContextInterface;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{

    protected CategoryContextInterface $category_context;

    function __construct(CategoryContextInterface $category_context)
    {
        $this->category_context = $category_context;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $resp = $this->category_context->getBy($request);
            return $this->success($resp->message, $resp->data, $resp->http_status, $resp->pagination);

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

            $validate = Validator::make($request->all(), [
                'name' => 'required'
            ]);

            if ($validate->fails()) {
                return $this->failed($validate->errors()->first(), Response::HTTP_BAD_REQUEST);
            }

            $resp = $this->category_context->store($request);

            return $this->success($resp->message, $resp->data, $resp->http_status);

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

            $resp = $this->category_context->getById($id);

            return $this->success($resp->message, $resp->data, $resp->http_status);

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

            $validate = Validator::make($request->all(), [
                'name' => 'required'
            ]);

            if ($validate->fails()) {
                return $this->failed($validate->errors()->first(), Response::HTTP_BAD_REQUEST);
            }

            $resp = $this->category_context->update($id, $request);

            return $this->success($resp->message, $resp->data, $resp->http_status);

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

    }

}
