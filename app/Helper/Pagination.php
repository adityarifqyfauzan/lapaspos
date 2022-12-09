<?php

namespace App\Helper;

class Pagination
{
    /**
     * get offset from page and size
     * @param integer $page
     * @param integer $size
     * @return int
     */
    public static function getOffset($page, $size)
    {
        if ($page <= 0 || $size <= 0) {
            return -1;
        }
        return ($page - 1) * $size;
    }
}
