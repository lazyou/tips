<?php

namespace App\Console\Commands;

use App\Models\Permission;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

/**
 * 权限填充: 按照层级 (必须是三级做填充)
 *  TODO: name 和 sort 需要人工一一确定
 * Class ApiRoutesToSeeder
 * @package App\Console\Commands
 */
class ApiRoutesToSeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:api.routes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '权限填充: 按照层级 (必须是三级做填充)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Artisan::call('api:routes');

        $output = Artisan::output();

        // method 处理: 保留一个
        $output = str_replace(
            ['GET|HEAD', 'PUT|PATCH', 'PATCH|PUT'],
            ['GET', 'PUT', 'PATCH'],
            $output
        );

        $apis = explode(PHP_EOL, $output);

        // 逐行路由处理
        foreach ($apis as $key => $api) {
            unset($apis[$key]);
            $apis[$key]['raw'] = $api;
            $route = explode('|', $api);
            $this->dealRoute($route, $api);
        }
    }

    protected function dealRoute($route, $raw)
    {
        $c = count($route);

        if ($c !== 11) {
            $this->error('错误路由:');
            print_r($route);
            return null;
        }

        array_walk($route, function (&$value) {
            $value = trim($value);
            return $value;
        });

        $host       = $route[1];
//        $uri        = $route[3];
        $name       = $route[4];
        $nameArr    = explode('.', $name); // 别名分隔
        $controller = $route[5];

        // 满足条件的才做处理: 后台路由, 且需要权限校验
        if (! str_contains($host, 'admin.') || str_contains($name, 'allow')) {
            return null;
        }

        /**
         * 权限别名固定三级处理:
         *  第一级: 展开的菜单;
         *  第二级: 具体管理的模块;
         *  第三级: 管理模块下的权限接口 (CURD等)
         */
        $countName = count($nameArr);
        if ($countName !== 3) { // 别名必须是三级, 否则手动处理
            $this->error("别名层级错误: {$controller} -- {$name}");
            return null;
        }

        $first  = $nameArr[0];
        $second = "{$nameArr[0]}.{$nameArr[1]}";
        $third  = "{$nameArr[0]}.{$nameArr[1]}.$nameArr[2]";

        // 权限创建
        $pFirst  = Permission::firstOrCreate(['route_name' => $first]);
        $pSecond = Permission::firstOrCreate(['route_name' => $second]);
        $pThird  = Permission::firstOrCreate(['route_name' => $third]);

        // 父级 id
        $pSecond->parent_id = $pFirst->id;
        $pSecond->save();

        $pThird->parent_id = $pSecond->id;
        $pThird->save();
    }
}
