<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

/**
 * 创建自定义表单验证
 *
 * Class RequestMakeCommand
 * @package App\Console\Commands
 */
class RequestMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:api.request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成表单';

    protected $type = 'Request';

    /**
     * 获取模板
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/request.stub';
    }

    /**
     * 默认命名空间
     *
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Http\Requests';
    }
}
