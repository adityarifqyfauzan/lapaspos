<?php

namespace App\Http\Context;

use App\Traits\Pagination;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Context
{

    use Pagination;

    /**
     * this function used to return the proses of context
     *
     * @param int $http_status
     * @param string $message
     * @param $data
     * @return object
     */
    public function returnContext($http_status = Response::HTTP_OK, $message = "", $data = null, $pagination = [])
    {
        return (object) [
            "http_status" => $http_status,
            "message" => $message,
            "data" => $data,
            "pagination" => $pagination
        ];
    }

}
