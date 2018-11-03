<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

/**
 * 订阅者生成器
 *
 * Class SubscribeMakeCommand
 * @package App\Console\Commands
 */
class SubscribeMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:api.subscribe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成订阅者';

    protected $type = 'Subscribe';

    /**
     * 获取模板
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/subscribe.stub';
    }

    /**
     * 默认命名空间
     *
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Listeners';
    }
}
