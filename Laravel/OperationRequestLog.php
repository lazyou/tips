<?php

namespace App\Http\Middleware;

use App\Jobs\CreateOperationLog;
use Closure;

/**
 * 操作日志中间件 (配合权限名)
 * Class OperationLog
 * @package App\Http\Middleware
 */
class OperationLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $this->adminUser();
        $method = $request->getMethod();
        $action = request()->route()->getAction();

        if (!is_null($user) && $this->isNeedCreate($method)) {
            $log = [];
            $log['user_id'] = $user->id;
            $log['route_name'] = array_get($action, 'as', '');;
            $log['method'] = $method;
            $log['body'] = $request->getContent();
            $log['url'] = $this->getUrl($request);
            $log['created_at'] = getNow();
            $log['updated_at'] = getNow();

            CreateOperationLog::dispatch($log);
        }

        return $next($request);
    }

    /**
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    protected function adminUser()
    {
        return auth('admin')->user();
    }

    /**
     * 当前请求的 uri + query
     * @param $request
     * @return string
     */
    protected function getUrl($request)
    {
        return $request->path() . urldecode(str_replace($request->url(), '',$request->fullUrl()));
    }

    /**
     * 是否需要记录 （GET 不纪录）
     * @param $method
     * @return bool
     */
    protected function isNeedCreate($method)
    {
        $methods = [
            'POST',
            'DELETE',
            'PATCH',
            'PUT',
        ];

        return in_array($method, $methods);
    }
}


CreateOperationLog.php
namespace App\Jobs;

use App\Models\OperationLog;
use App\Models\Permission;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * 操作日志记录创建
 * Class CreateOperationLog
 * @package App\Jobs
 */
class CreateOperationLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $log;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($log)
    {
        $this->log = $log;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->setPermissionId();
        OperationLog::create($this->log);
    }

    protected function setPermissionId()
    {
        $routeName = $this->log['route_name'];
        $permission = Permission::where('route_name', $routeName)->first();
        $this->log['permission_id'] = is_null($permission) ? 0 : $permission->id;
    }
}


表结构:
Schema::create('operation_log', function (Blueprint $table) {
    $table->increments('id');
    $table->integer('user_id')->default(0)->comment('操作人');
    $table->integer('permission_id')->default(0)->comment('权限(菜单)');
    $table->string('method')->default('')->comment('请求方法');
    $table->longText('body')->default('')->comment('请求数据');
    $table->text('url')->default('')->comment('请求url');
    $table->timestamps();
    $table->softDeletes();
});
