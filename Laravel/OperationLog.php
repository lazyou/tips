<?php

// 核心 OperationLog.php
namespace App\Libs\OperationLog;

use Log AS Logging;
use App\Models\Log;
use App\Models\LogList;
use Exception;
use Illuminate\Support\Facades\Auth;

/**
 * 操作日志: 配置模型的 $_logs 属性即可使用
 *
 *      主表记录: 操作人, 操作表, 操作类型 (增删改)
 *      附表记录变化: 字段名 字段中文 旧值 新值
 *
 * @author lxl 2017.08.18
 */
trait OperationLog
{
    protected static function bootOperationLog()
    {
        self::created(function ($model) {
            $model->logging($model, 'created');
        });

        self::updated(function ($model)  {
            $model->logging($model, 'updated');
        });

        self::deleted(function ($model) {
            $model->logging($model, 'deleted');
        });

        return true;
    }

    // 记录指定字段新旧值
    public function logging($model, $type)
    {
        $result = [
            'log' => [],
            'log_list' => [],
        ];

        $logType = [
            'created' => 1,
            'updated' => 2,
            'deleted' => 3,
        ];

        $user = Auth::user();
        $tableFields = array_keys($model->getAttributes()); // 表字段名

        // 非登陆状态不记录操作日志
        if (! $user || is_null($model->_logs) || ! is_array($model->_logs)) {
            return true;
        }

        try {
            // 日志主表内容
            $result['log'] = new Log([
                // 'loggable_id' => $model->getKey(),
                // 'loggable_type' => $model->getTable(),
                'model_name' => $model->_logModelName,
                'user_id' => $user->id,
                'user_name' => $user->realname,
                'company_id' => $user->company_id,
                'company_name' => '',
                'school_id' => $user->school_id,
                'school_name' => '',
                // 'department_id', // 貌似没有, 通过 role_id 得到
                // 'department_name',
                'role_id' => $user->role_id,
                'role_name' => '',
                'log_type' => $logType[$type],
                // 'description' => '',
            ]);

            // 日志附表内容
            foreach ($model->_logs as $logField) {
                if (in_array($logField['en'], $tableFields)) {
                    $temp = [
                        'field_en' => $logField['en'],
                        'field_cn' => $logField['cn'],
                        'old_value' => (string) $model->getOriginal($logField['en']),
                        'new_value' => (string) $model->{$logField['en']},
                    ];

                    array_push($result['log_list'], new LogList($temp));
                }
            }

            // Logging::debug($result);

            $log = $model->logs()->save($result['log']);

            $log->logList()->saveMany($result['log_list']);
        } catch (Exception $e) {
            Logging::error("OperationLog Error: 操作日志的错误不能影响到正常业务, 所以 try catch 了");
            Logging::error($e->getMessage());
           // Logging::error($e->getTrace());
        }
    }
}


// usage:
BaseModel.php
```
use App\Libs\OperationLog\OperationLog;

/**
 * 模型基础（添加软删除）
 */
class BaseModel extends Model
{
    use OperationLog;
    ...
}
```


Student.php
```

/**
 * 学员模型
 */
class Student extends BaseModel
{
        // 日志属性 -- 模块名
    public $_logModelName = '学员管理';

    // 日志属性 -- 记录字段
    public $_logs = [
        [
            'en' => 'study_teacher_id', // 数据库字段名
            'cn' => '归属学管师',
        ],
        [
            'en' => 'current_school',
            'cn' => '在读学校',
        ],
        [
            'en' => 'current_grade',
            'cn' => '当前年级',
        ]
    ];
```
