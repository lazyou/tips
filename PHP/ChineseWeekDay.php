<?php

if (!function_exists('chineseWeekDay')) {
    /**
     * 将时间戳转换为:Y-m-d (星期N)
     * @param $value 时间戳
     * @return mixed
     */
    function chineseWeekDay($value)
    {
        $map = [
            '(1)' => '(星期一)',
            '(2)' => '(星期二)',
            '(3)' => '(星期三)',
            '(4)' => '(星期四)',
            '(5)' => '(星期五)',
            '(6)' => '(星期六)',
            '(7)' => '(星期日)'
        ];

        $date = date('Y-m-d (N)', $value);

        return str_replace(array_keys($map), array_values($map), $date);
    }
}
