<?php

namespace App\Providers;

use Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use App\Exceptions\CustomException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        if (App::environment('local')) {
            // 开启 sql 日志
            DB::connection()->enableQueryLog();
        }

        //身份证验证
        Validator::extend('id_card', function ($attribute, $value, $parameters, $validator) {
            return idCardVerify($value);
        });

        Validator::replacer('id_card', function ($message, $attribute, $rule, $parameters) {
            return '身份证格式错误';
        });
    }

    public function register()
    {
        // token 无效异常全局处理
        app('Dingo\Api\Exception\Handler')->register(function (AuthenticationException $exception) {
            return respondMessage(__('common.token_failed'), HttpCode::HTTP_UNAUTHORIZED);
        });

        // 数据查询不到异常全局处理
        app('Dingo\Api\Exception\Handler')->register(function (ModelNotFoundException $exception) {
            return respondMessage(__('common.model_not_found'), HttpCode::HTTP_BAD_REQUEST);
        });

        // 策略 Policy 抛出的异常处理
        app('Dingo\Api\Exception\Handler')->register(function (AuthorizationException $exception) {
            return respondMessage($exception->getMessage() ? : __('common.no_permission'), HttpCode::HTTP_BAD_REQUEST);
        });
    }
}