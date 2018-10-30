<?php

if (!function_exists('checkPasswordFromHeader')) {
    /**
     * [部分操作需要验证操作人的密码, postman 通过 HEADER 传递参数 CheckoutPassword 进行验证]
     * @return [type] [description]
     */
    function checkPasswordFromHeader()
    {
        if (!isset(\Request::header()['checkoutpassword'])) {
            throw new \Exception("请输入密码");
        }

        $password = \Request::header()['checkoutpassword'][0];

        if (!\Hash::check($password, \Auth::user()->password)) {
            throw new \Exception("密码验证不通过");
        }
    }
}
