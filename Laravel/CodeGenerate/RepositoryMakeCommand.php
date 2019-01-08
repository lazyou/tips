<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

/**
 * 仓库生成脚本
 *
 * Class RepositoryMakeCommand
 * @package App\Console\Commands
 */
class RepositoryMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:api.repository';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成仓库';

    protected $type = 'Repository';

    /**
     * 获取模板
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/repository.stub';
    }

    /**
     * 默认命名空间
     *
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Repositories';
    }
}
