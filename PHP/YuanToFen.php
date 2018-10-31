<?php

if (! function_exists('yuanToFen')) {
    /**
     * 人名币 "元" 转为 "分"
     *
     * 参考: https://blog.csdn.net/leedaning/article/details/52485699
     * @param $yuan
     * @return int
     */
    function yuanToFen($yuan) {
        return (intval(strval($yuan * 100)));
    }
}
