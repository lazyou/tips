<?php
// 利用路由的别名实现 RBAC 权限控制

namespace App\Http\Middleware;

use App\Models\Role;
use Auth;
use Closure;
use App\Models\Permission;
use Symfony\Component\HttpFoundation\Response AS HttpCode;

/**
 * [ApiPermission api权限中间件]
 */
class ApiPermission
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
        $hasPermission = $this->hasPermission($this->adminUser(), request()->route()->getAction());

        return $hasPermission ? $next($request) : respondMessage('无权操作', HttpCode::HTTP_BAD_REQUEST);
    }

    /**
     * [hasPermission 根据路由别名判定当前用户(对应角色下)是否有权限访问]
     * 1. 超级管理员拥有全部权限
     * 2. 没有设置别名的路由也默认可以被访问
     * 3. 别名中含 'allow' 默认可以被访问.
     *
     * @return boolean         [description]
     */
    public function hasPermission($user, $action)
    {
        // 没有设置别名的路由默认可被访问
        if (! isset($action['as'])) {
            return true;
        }

        $asName = $action['as'];

        // 路由别名中含有 allow 关键词就允许被访问
        if (strpos($asName, 'allow')) {
            return true;
        }

        // 白名单
        if (in_array($asName, $this->allowList())) {
            return true;
        }

        // 超级管理员拥有全部权限
        if ($user->role_type == Role::TYPE_ADMIN) {
            return true;
        }

        // 路由别名有权限才能被访问
        $item = Permission::where('route_name', $asName)->first();

        if (! is_null($item) && in_array($item->id, $user->getPermissionIds())) {
            return true;
        }

        // 其他情况默认无权操作
        return false;
    }

    // 白名单: 总是允许访问的路由别名列表
    public function allowList()
    {
        return [
            'admin.user.logout',
        ];
    }

    /**
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    protected function adminUser()
    {
        return auth('admin')->user();
    }
}

// 权限相关表结构
Schema::create('permission', function (Blueprint $table) {
    $table->increments('id');
    $table->integer('parent_id')->default(0);
    $table->string('route_name')->comment('路由别名');
    $table->string('name')->comment('权限名');
    $table->integer('sort')->default(0);
    $table->integer('level')->default(0)->comment('权限级别:预留字段');
    $table->timestamps();
    $table->softDeletes();
});

Schema::create('role_permission', function (Blueprint $table) {
    $table->integer('role_id');
    $table->integer('permission_id');
});
