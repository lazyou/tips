<?php

if (!function_exists('getNumber')) {
    /**
     * [getNumber 获取编号通用函数]
     * @param  string $prefix [前缀]
     * @param  integer $long [截取微妙的长度]
     * @return [type]          [string]
     */
    function getNumber($prefix = '', $long = 2)
    {
        // 微秒 与 秒
        list($msec, $sec) = explode(" ", microtime());

        // 移除微秒里的 '0.', 并截取指定长度
        $msec = substr(str_replace('0.', '', $msec), 0, $long); 

        // 前缀 . 秒 . 毫秒位数
        $result = "{$prefix}{$sec}{$msec}"; 

        return $result;
    }
}
