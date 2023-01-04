<?php

namespace App\Http\Controllers\UserManagement\User;

use App\Http\Context\User\UserContextInterface;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    protected UserContextInterface $context;

    function __construct(UserContextInterface $context) {
        $this->context = $context;
    }

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
            return $this->failed($this->error($e->getMessage()), Response::HTTP_INTERNAL_SERVER_ERROR);
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
            return $this->failed($this->error($e->getMessage()), Response::HTTP_INTERNAL_SERVER_ERROR);
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

            $validate = Validator::make($request->all(), [
                'name' => 'required',
                'username' => 'required|unique:users,username',
                'password' => 'required|min:6',
                'role_id' => 'required',
                'outlet_id' => 'required',
                'email' => 'required'
            ]);

            if ($validate->fails()) {
                return $this->failed($validate->errors()->first(), Response::HTTP_BAD_REQUEST);
            }

            $resp = $this->context->store($request);

            if ($resp->http_status == Response::HTTP_CREATED) {
                return $this->success($resp->message, $resp->data, $resp->http_status);
            }

            return $this->failed($resp->message, $resp->http_status);

        } catch (Exception $e) {
            return $this->failed($this->error($e->getMessage()), Response::HTTP_INTERNAL_SERVER_ERROR);
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

            $resp = $this->context->getById($id);

            if ($resp->http_status != Response::HTTP_OK) {
                return $this->failed($resp->message, $resp->http_status);
            }

            return $this->success(
                $resp->message,
                $resp->data,
                $resp->http_status
            );

        } catch (Exception $e) {
            return $this->failed($this->error($e->getMessage()), Response::HTTP_INTERNAL_SERVER_ERROR);
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
            return $this->failed($this->error($e->getMessage()), Response::HTTP_INTERNAL_SERVER_ERROR);
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

            $validate = Validator::make($request->all(), [
                'name' => 'required',
                'role_id' => 'required',
                'outlet_id' => 'required'
            ]);

            if ($validate->fails()) {
                return $this->failed($validate->errors()->first(), Response::HTTP_BAD_REQUEST);
            }

            $resp = $this->context->update($id, $request);

            if ($resp->http_status == Response::HTTP_OK) {
                return $this->success($resp->message, $resp->data, $resp->http_status);
            }

            return $this->failed($resp->message, $resp->http_status);

        } catch (Exception $e) {
            return $this->failed($this->error($e->getMessage()), Response::HTTP_INTERNAL_SERVER_ERROR);
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
            return $this->failed($this->error($e->getMessage()), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
