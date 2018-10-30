<?php

namespace App\Providers;

use Validator;
use Illuminate\Support\ServiceProvider;
use DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 开启 sql 日志
        DB::connection()->enableQueryLog();

        //身份证验证
        Validator::extend('id_card', function ($attribute, $value, $parameters, $validator) {
            return idCardVerify($value);
        });

        Validator::replacer('id_card', function ($message, $attribute, $rule, $parameters) {
            return    '身份证格式错误';
        });
    }
}