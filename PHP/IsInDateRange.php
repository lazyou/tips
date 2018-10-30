<?php

if (!function_exists('isDateRange')) {

    /**
     * 判断时间是否在给定的范围内
     *
     * @author lxt 2017.06.27
     * @param $time |   日期字符串或者时间戳
     * @param $start |   日期字符串或者时间戳
     * @param $end |   日期字符串或者时间戳
     * @return bool
     */
    function isDateRange($time, $start, $end)
    {
        $time = is_numeric($time) ? $time : strtotime($time);
        $start = is_numeric($start) ? $start : strtotime($start);
        $end = is_numeric($end) ? $end : strtotime($end);

        if ($time >= $start && $time < $end) {
            return true;
        }

        return false;
    }
}
