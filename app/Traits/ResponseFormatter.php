<?php

namespace App\Traits;

use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 *  Response formatter
 */
trait ResponseFormatter
{
    /**
     * Http success response
     * this function used when you want to create success
     * response
     *
     * @param String $message
     * @param Array $data (optional)
     * @param Symfony\Component\HttpFoundation\Response $code
     * @param Array $paginate (optional)
     *
     * @return Json
     */
    public function success($message, $data = null, $code = Response::HTTP_OK, $paginate = [])
    {

        if ($paginate) {
            return json_encode([
                "message" => $message,
                "data" => $data,
                "paginate" => $paginate
            ], $code);
        }

        return json_encode([
            "message" => $message,
            "data" => $data
        ], $code);

    }

    /**
     * Http failed response
     *
     * @param String $message
     * @param Symfony\Component\HttpFoundation\Response $code
     * @return Json
     */
    public function failed($message, $code = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        return json_encode([
            "message" => $message
        ], $code);
    }

    /**
     * Paginate function
     * digunakan untuk membuat sebuah pagination
     * @param integer $page
     * @param integer $size
     * @param integer $total
     *
     * @return Array
     */
    public function pagination($page, $size, $total)
    {
        return [
            "page" => $page,
            "size" => $size,
            "total" => $total
        ];
    }

    /**
     * Untuk melakukan filter error sesuai dengan environment
     * @param string $message
     * @return string
     */
    public function error($message)
    {
        if (App::environment(['staging', 'local'])) {
            return $message;
        }

        return 'Terjadi kesalahan pada server, silahkan hubungi administrator';
    }
}
