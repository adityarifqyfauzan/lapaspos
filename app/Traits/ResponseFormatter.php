<?php

namespace App\Traits;

use App\Jobs\AlertJob;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
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
     * @param string $message
     * @param mixed $data
     * @param int $code
     * @param array $paginate
     *
     * @return json
     */
    public function success($message, $data = null, $code = Response::HTTP_OK, $paginate = [])
    {

        // Log::info($message . " " . now(), isset($data) == null ? ['context' => $data] : []);

        if ($paginate) {
            return response()->json([
                "message" => $message,
                "data" => $data,
                "paginate" => $paginate
            ], $code);
        }

        return response()->json([
            "message" => $message,
            "data" => $data
        ], $code);

    }

    /**
     * Http failed response
     *
     * @param string $message
     * @param int $code
     * @return json
     */
    public function failed($message, $code = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        // dispatch(new AlertJob($message . "[" . $code . "]" . " | " . now(), 'alert'));

        return response()->json([
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

        dispatch(new AlertJob($message . ' ' . now(), 'critical'));

        if (App::environment(['staging', 'local'])) {
            return $message;
        }

        return 'Terjadi kesalahan pada server, silahkan hubungi administrator';
    }
}
