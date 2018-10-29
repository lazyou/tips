<?php
// Monolog 使用扩展
// usage： `$this->reporter = historian(storage_path('logs/debug'));`

if (! function_exists('historian')) {
    /**
     * 由于容器本身的log是单例模式，这里需要返回全新的log实例
     *
     * @param     $path
     * @param int $maxFileNumber
     * @return \Illuminate\Log\Writer
     */
    function historian($path, $maxFileNumber = 0)
    {
        $logger = new \Monolog\Logger(app()->environment());
        $dispatcher = app('events');
        $writer = new \Illuminate\Log\Writer($logger, $dispatcher);
        $writer->useDailyFiles($path, $maxFileNumber ? : config('app.log_max_files'));

        return $writer;
    }
}
