<?php

if (! function_exists('getAttributeName')) {
    /**
     * 字典转中文
     *
     * @param $options
     * @param $value
     * @param string $default
     * @return string
     */
    function getAttributeName($options, $value, $default = '')
    {
        foreach ($options as $option) {
            if ($option['value'] == $value) {
                return $option['name'];
            }
        }

        return $default;
    }
}

// usage:
namespace App\Models;

/**
 * 优惠券 领取 使用记录表
 *
 * Class CouponGet
 * @package App\Models
 */
class CouponGet
{
    // 优惠券使用状态: coupon_use_status
    const USE_STATUS_UNUSED = 1;
    const USE_STATUS_UNUSED_NAME = '未使用';

    const USE_STATUS_USED = 2;
    const USE_STATUS_USED_NAME = '已使用';

    const USE_STATUS_DUED = 3;
    const USE_STATUS_DUED_NAME = '已过期';

    /**
     * 优惠券使用状态 字典
     *
     * @return array
     */
    public function getUseStatus()
    {
        return [
            [
                'value' => self::USE_STATUS_UNUSED,
                'name'  => self::USE_STATUS_UNUSED_NAME,
            ],
            [
                'value' => self::USE_STATUS_USED,
                'name'  => self::USE_STATUS_USED_NAME,
            ],
            [
                'value' => self::USE_STATUS_DUED,
                'name'  => self::USE_STATUS_DUED_NAME,
            ],
        ];
    }

    /**
     * 优惠券使用状态转中文
     *
     * @return string
     */
    public function getCouponUseStatusNameAttribute()
    {
        if ($this->coupon_use_status) {
            return getAttributeName($this->getUseStatus(), $this->coupon_use_status);
        }
    }
}
