<?php

if (!function_exists('isEmail')) {
    /**
     * 检查是否是邮箱
     */
    function isEmail($email)
    {
        return strlen($email) > 5 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
    }
}
