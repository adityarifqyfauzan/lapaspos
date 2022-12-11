<?php

namespace App\Traits;

use Illuminate\Http\Request;

/**
 * pagination
 */
trait Pagination
{
    /**
     * getPagination
     * @param int $page
     * @param int $size
     * @param int $total
     * @return array
     *
     */
    public function setPagination($page, $size, $total)
    {
        $pagination = [
            'page' => (int)$page,
            'size' => (int)$size,
            'total' => $total,
        ];
        return (object) $pagination;
    }

    /**
     * Get page and size from Request.
     * @param Request $request
     * @return object
     *
     */
    public function getPageAndSize(Request $request)
    {
        $page = $request->query('page', 1);
        $size = $request->query('size', 10); // default size 20
        return (object) ["page" => $page, "size" => $size];
    }
}
