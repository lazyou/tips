<?php

if (! function_exists('arrayGet')) {
    /**
     * [简化数组判断是否存在该键值的步骤, 并支持不存在时的默认值]
     */
    function arrayGet($arr, $key, $default)
    {
        return isset($arr[$key]) ? $arr[$key] : $default;
    }
}
