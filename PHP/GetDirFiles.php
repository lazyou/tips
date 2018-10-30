<?php

if (! function_exists('getDirFiles')) {
    /**
     * 遍历目录下的文件列表
     *
     * @param $dir
     * @param $arr
     */
    function getDirFiles($dir, &$arr)
    {
        $dir = rtrim($dir, '/') . '/';

        if (is_dir($dir)) {
            $hadle = opendir($dir);

            while (($file = readdir($hadle)) !== false) {
                if (! in_array($file, array('.', '..'))) {
                    if (is_file($dir . $file)) {
                        array_push($arr, $dir . $file);
                    }

                    if (is_dir($dir . $file)) {
                        getDirFiles($dir . $file . '/', $arr);
                    }
                }
            }
        }
    }
}
