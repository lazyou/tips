<?php

// ApiController.php
namespace App\Http\Controllers;

use Dingo\Api\Routing\Helpers;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as LaravelController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Symfony\Component\HttpFoundation\Response AS HttpCode;

/**
 * 针对 api 处理的控制器基类
 *
 * @package App\Http\Controllers
 */
class ApiController extends LaravelController
{
    use Helpers, AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    const REQUEST_ARRAY = 1;

    const REQUEST_ERROR = 2;

    const REQUEST_NO_CONTENT = 3;

    const REQUEST_CREATED = 4;

    /**
     * dingo 通用 api 返回处理
     *
     * version 2018.03.26. 应安卓要求, 201 204 当作 200 处理, 空数据的情况洗发空数组.
     *
     * @param $result
     * @param int $requestType
     * @return \Dingo\Api\Http\Response|void
     */
    public function respond($result, $requestType = self::REQUEST_ARRAY)
    {
        if ($result['is_ok']) {
            switch ($requestType) {
                case self::REQUEST_ARRAY:
                    return $this->response->array($result['data']);
                    break;
                case self::REQUEST_CREATED:
                    return $this->response->array([]);
                    break;
                case self::REQUEST_NO_CONTENT:
                    return $this->response->array([]);
                    break;
            }
        } else {
            return $this->response->errorBadRequest($result['message']);
        }
    }

    protected function getLoginFailedRespond()
    {
        return respondMessage(__('common.login_failed'), HttpCode::HTTP_BAD_REQUEST);
    }
}


// ApiRepository.php
<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;

/**
 * 针对 api 处理的仓库基类
 *
 * @package App\Repositories\Live
 */
class ApiRepository
{
    // 事务开启标志
    protected $isBeginTransaction = false;

    protected $result = [
        'is_ok'   => true,
        'data'    => [],
        'message' => '操作成功',
    ];

    /**
     * 开启事务，方便后续success、fail方法使用事务
     *
     */
    public function beginTransaction()
    {
        $this->isBeginTransaction = true;
        DB::beginTransaction();
    }

    public function success($data = null, $message = '')
    {
        // 如果开启事务，提交
        if ($this->isBeginTransaction) {
            DB::commit();
        }

        $this->result['message'] = $message ? $message : trans('common.operate_succeed');
        $this->result['data'] = $data ? : $this->result['data'];

        return $this->result;
    }

    /**
     *
     * @param string $message
     * @param array  $data
     * @return array
     */
    public function fail($message = '', $data = null)
    {
        // 如果开启事务，回滚
        if ($this->isBeginTransaction) {
            DB::rollBack();
        }

        // 异常特殊处理
        if ($message instanceof \Exception) {
            // 记录日志
            report($message);

            $environmentNeedToKnow = [
                'local',
                'test'
            ];

            // 替换输出信息
            $message = app()->environment($environmentNeedToKnow) ? $message->getMessage() : null;
        }

        $this->result['is_ok'] = false;
        $this->result['message'] = $message ? $message : trans('common.operate_failed');
        $this->result['data'] = $data ? : $this->result['data'];

        return $this->result;
    }

    /**
     * 权限策略检测
     *
     * 1.检测模型绑定的权限策略，如果不通过，返回403响应；
     * 2.通过返回true，方便后续判断；
     *
     * @param        $instance  |   实例化的模型
     * @param string $method    |   权限名
     * @param null   $message   |   自定义错误信息
     * @return bool
     */
    protected function authorize($instance, $method = '', $message = null)
    {
        $message = $message ? trans($message) : trans('response.permission_denied');

        $authority = Gate::denies($method, $instance, true);

        if ($authority !== false) {
            throw new AuthorizationException($authority ? : $message);
        }

        return true;
    }

}

// usage: 
// 在控制器中使用
return $this->respond($response);

return $this->respond($response, self::REQUEST_CREATED);

return $this->respond($response, self::REQUEST_NO_CONTENT);
