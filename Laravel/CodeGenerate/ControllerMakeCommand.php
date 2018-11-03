<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

/**
 * 创建自定义控制器
 *
 * Class ControllerMakeCommand
 * @package App\Console\Commands
 */
class ControllerMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:api.controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成控制器';

    protected $type = 'Controller';

    /**
     * 获取模板
     *
     * @return string
     */
    protected function getStub()
    {
        $stub = 'controller.stub';

        if ($this->option('repository')) {
            $stub = 'controller.repository.stub';
        }

        return __DIR__.'/stubs/' . $stub;
    }

    /**
     * 重写创建类方法
     *
     * @param string $name
     * @return mixed
     */
    protected function buildClass($name)
    {
        $replace = [];

        if ($this->option('repository')) {
            $replace = $this->buildRepositoryReplacements($replace);
        }

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    /**
     * 生成仓库替换内容
     *
     * @param array $replace
     * @return array
     * @throws \Exception
     */
    protected function buildRepositoryReplacements(array $replace) : array
    {
        $repositoryName = str_replace('Controller', 'Repository', $this->argument('name'));
        $repositoryClass = $this->resolveRepository($repositoryName);

        if (! class_exists($repositoryClass)) {
            if ($this->confirm("A {$repositoryClass} repository does not exist. Do you want to generate it?", true)) {
                $this->call('make:api.repository', ['name' => $repositoryClass]);
            }
        }

        return array_merge($replace, [
            'DummyFullRepositoryClass' => $repositoryClass,
            'DummyRepositoryClass' => class_basename($repositoryClass),
        ]);
    }

    /**
     * 解析仓库
     *
     * @param $repository
     * @return string
     * @throws \Exception
     */
    protected function resolveRepository($repository)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $repository)) {
            throw new \Exception('Repository name contains invalid characters.');
        }

        $repository = trim(str_replace('/', '\\', $repository), '\\');

        if (! Str::startsWith($repository, $rootNamespace = $this->laravel->getNamespace())) {
            $repository = $rootNamespace . 'Repositories\\' . $repository;
        }

        return $repository;
    }

    /**
     * 默认命名空间
     *
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Http\Controllers';
    }

    /**
     * 选项
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            [
                'repository',
                'r',
                InputOption::VALUE_NONE,
                'Generate a controller for the given repository.'
            ]
        ];
    }
}
