<?php

if (! function_exists('getDateFromDatetime')) {
    /**
     * [getDateFromDatetime 从 Y-m-d H:i:s 中提取 Y-m-d]
     */
    function getDateFromDatetime($value)
    {
        return substr($value, 0, 10);
    }
}
