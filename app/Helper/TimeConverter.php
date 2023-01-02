<?php

namespace App\Helper;

class TimeConverter {

    /**
     * Parsing data bulan (number) to string
     * @param int $month
     */
    public static function parseMonth(int $month)
    {
        $month_str = "";
        switch ($month) {
            case 1:
                $month_str = "Jan";
                break;
            case 2:
                $month_str = "Feb";
                break;
            case 3:
                $month_str = "Mar";
                break;
            case 4:
                $month_str = "Apr";
                break;
            case 5:
                $month_str = "May";
                break;
            case 6:
                $month_str = "Jun";
                break;
            case 7:
                $month_str = "Jul";
                break;
            case 8:
                $month_str = "Aug";
                break;
            case 9:
                $month_str = "Sep";
                break;
            case 10:
                $month_str = "Oct";
                break;
            case 11:
                $month_str = "Nov";
                break;

            default:
                $month_str = "Dec";
                break;
        }

        return $month_str;
    }

}
