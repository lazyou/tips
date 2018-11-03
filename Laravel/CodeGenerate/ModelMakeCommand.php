<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\DB;

/**
 * 创建自定义模型
 *
 * Class ModelMakeCommand
 * @package App\Console\Commands
 */
class ModelMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:api.model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成模型';

    protected $type = 'Model';

    /**
     * 获取模板
     *
     * @return string
     */
    protected function getStub()
    {
        $modelTemp = $this->setTemplateMore();

        if ($modelTemp) {
            return $modelTemp;
        }

        return __DIR__.'/stubs/model.stub';
    }

    /**
     * 模型生成模板扩展： table 属性  和  fillable 属性支持
     * @return string
     * @author lxl
     */
    protected function setTemplateMore()
    {
        $table = snake_case($this->getNameInput());

        $tables = array_map('reset', DB::select('SHOW TABLES'));

        // 表存在
        if (in_array($table, $tables)) {
            // 所有表字段
            $columns = array_map('reset', DB::select("SHOW COLUMNS FROM `{$table}`"));

            // 用于 fillable 的字段
            $fillable = array_filter($columns, function($value) {
                return ! in_array($value, [
                    'id',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ]);
            });

            $content = file_get_contents(__DIR__.'/stubs/model.stub');

            // table 属性替换
            $content = str_replace("\$table = ''", "\$table = '{$table}'", $content);

            // fillable 拼接
            $fillableStr = "protected \$fillable = [\n";
            $fillableCount = count($fillable);

            foreach ($fillable as $key => $field) {
                $fillableStr .=
                    $key == $fillableCount ?
                        "\t\t'{$field}'," :
                        "\t\t'{$field}',\n";
            }

            // fillable 属性替换
            $content = str_replace("protected \$fillable = [\n", $fillableStr, $content);

            // 生成新模板
            if (file_put_contents(__DIR__.'/stubs/model_tmp.stub', $content)) {
                return __DIR__.'/stubs/model_tmp.stub';
            }
        }
    }

    /**
     * 默认命名空间
     *
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Models';
    }
}
