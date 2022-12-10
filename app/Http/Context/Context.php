<?php

namespace App\Http\Context;

use Symfony\Component\HttpFoundation\Response;

class Context
{

    /**
     * this function used to return the proses of context
     *
     * @param boolean $process -> its represented if the proses is success or not (using boolean)
     * @param int $http_status
     * @param string $message
     * @param $data
     * @return object
     */
    public function returnContext($http_status = Response::HTTP_OK, $message = "", $data = null)
    {
        return (object) [
            "http_status" => $http_status,
            "message" => $message,
            "data" => $data
        ];
    }

}
