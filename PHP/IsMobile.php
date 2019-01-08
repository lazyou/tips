<?php

if (!function_exists('isMobile')) {
    /**
     * 检查是否是手机号码
     */
    function isMobile($phone)
    {
        $pat = '/^1[3-5,7,8]{1}[0-9]{9}$/';
        return preg_match($pat, $phone) === 1 ? true : false;
    }
}
