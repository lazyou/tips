<?php

if (!function_exists('saveImageFromBase64')) {
    /**
     * 从 img 标签中匹配 base64 图片, 并作保存, 并且替换上图片路径到原文本
     */
    function saveImageFromBase64($content)
    {
        if (preg_match_all("/<[img|IMG].*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/", $content, $images)) {
            $date = date('Ym');
            $base64StrArr = array_unique($images[1]);

            // 保存每张 base 64 图片
            foreach ($base64StrArr as $base64Str) {
                if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64Str, $result)) {
                    $uniqid = uniqid();
                    $imageFullDir = public_path() . "/data/images/{$date}/";
                    $imageFullName = "{$imageFullDir}{$uniqid}.{$result[2]}";

                    // 相对的路径, 用于替换使用
                    $imageRelateDir = "/data/images/{$date}/";
                    $imageRelateName = "{$imageRelateDir}{$uniqid}.{$result[2]}";

                    // 目录不存在就创建
                    if (!file_exists($imageFullDir)) {
                        mkdir($imageFullDir, 0777, true);
                    }

                    // base64解码: 图片内容二进制
                    $imageStream = base64_decode(str_replace($result[1], '', $base64Str));

                    // 图片保存成功则替换, 不成功不管
                    if (uploadImageContentToQiniu($imageRelateName, $imageStream)) {
                        $content = str_replace($base64Str, $imageRelateName, $content);
                    }
                }
            }
        }

        // 把图片完整路径替换成相对路径 (移除七牛域名. PS: 试题相关的数据库中始终不存)
        $qiniuDomin = 'http://' . env('QINIU_OSS_DOMAIN_HTTPS');
        $content = str_replace($qiniuDomin, '', $content);

        return $content;
    }
}

if (! function_exists('uploadImageContentToQiniu')) {
    /**
     * [uploadImageContentToQiniu 把图片上传到七牛]
     *
     * @param  [type] $name    [上传到七牛的路径 + 图片名 + 图片后缀 eg: /data/images/201407/59dadfe2a1b0a.png ]
     * @param  [type] $content [图片内容, 二进制]
     * @return [Bool]
     */
    function uploadImageContentToQiniu($name, $content)
    {
        $disk = \Storage::disk('qiniu');

        $result = $disk->put($name, $content);

        return $result;
    }
}
