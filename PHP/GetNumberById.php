<?php

if (!function_exists('getNumberById')) {
    /**
     * [getNumberById description]
     * @param  [type]  $item   模型
     * @param  string $prefix 前缀
     * @param  integer $long 长度
     * @return [string]          编号
     */
    function getNumberById($item, $prefix = '', $long = 10)
    {
        return ($prefix . sprintf("%0{$long}d", $item->id));
    }
}
