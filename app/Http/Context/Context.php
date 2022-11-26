<?php

namespace App\Http\Context;

use Symfony\Component\HttpFoundation\Response;

class Context
{

    /**
     * this function used to return the proses of context
     *
     * @param boolean $process -> its represented if the proses is success or not (using boolean)
     * @param Symfony\Component\HttpFoundation\Response $http_status
     * @param string $message
     * @param Object $data
     */
    public function return($process = true, $http_status = Response::HTTP_OK, $message = "", $data = [])
    {
        return (object) [
            "process" => $process,
            "http_status" => $http_status,
            "message" => $message,
            "data" => $data
        ];
    }

}
