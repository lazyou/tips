<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * 把 postman 文件导入到当前路径下命名为 postman.json
 * 生成文件在当前路径下, 名为 postman.md
 * postman 必须是两级 (第一级目录, 第二级接口)
 *
 * eg:
 *  php artisan make:api.markdown
 *  php artisan make:api.markdown --only=用户管理
 *
 * Class PostmanToMarkdown
 * @package App\Console\Commands
 */
class PostmanToMarkdown extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:api.markdown
                            {--only=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'postman 导出文件转 api 文档 markdown 格式';

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
        $postmans = file_get_contents(app_path() . "/Console/Commands/postman.json");
        $postmans = json_decode($postmans, true);

        if (!isset($postmans['info']) || !isset($postmans['item'])) {
            $this->error('postman json 文件格式有误!');
        }

        $info = $postmans['info'];
        $items = $postmans['item'];

        ob_start();

        foreach ($items as $catalog) {
            // 每个文件夹
            $catalogName = $catalog['name'];
            $catalogApis = $catalog['item'];

            // 导出指定目录
            $only = $this->option('only');
            if ($only && $catalogName != $only) {
                continue;
            }

            echo "-----\n";
            echo "## {$catalogName}\n";

            foreach ($catalogApis as $api) {
                $name = $api['name'];

                $request = $api['request'];
                $requestUrl = $this->getRequestUrl($request);
                $requestMethod = $request['method'];
                $requestBody = isset($request['body']['raw']) ? $request['body']['raw'] : "无";
                $requestHeader = $request['header'];

                $responseCode = 200; // 通常可以根据 requestMethod 判断
                $responseBody = isset($api['response'][0]['body'])
                                    ? json_encode(json_decode($api['response'][0]['body'], true), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT)
                                    : '        无';

                // request
                echo "### {$name}\n";
                echo "* Request:\n";
                echo "    * url: {$requestUrl}\n";
                echo "    * method: {$requestMethod}\n";
                echo "    * header: \n";
                foreach ($requestHeader as $header) {
                    echo "        * {$header['key']}: {$header['value']}\n";
                }
                echo "    * body: \n";
                echo "\n";
                echo "        ```json\n";
                echo "{$requestBody}\n";
                echo "        ```\n";

                // response
                echo "\n";
                echo "* Response:\n";
                echo "    * code: {$responseCode}\n";
                echo "    * body: \n";
                echo "\n";
                echo "        ```json\n";
                echo "{$responseBody}\n";
                echo "        ```\n";
                echo "\n\n";
            }

            file_put_contents(app_path() . "/Console/Commands/postman.md", ob_get_contents());
        }
    }

    protected function getRequestUrl($request)
    {
        $urlpath0 = '/' . $request['url']['path'][0];
        $urlpath1 = isset($request['url']['path'][1]) ? '/' . $request['url']['path'][1] : "";
        $urlpath2 = isset($request['url']['path'][2]) ? '/' . $request['url']['path'][2] : "";
        $urlpath3 = isset($request['url']['path'][3]) ? '/' . $request['url']['path'][3] : "";
        $urlpath4 = isset($request['url']['path'][4]) ? '/' . $request['url']['path'][4] : "";
        $urlpath5 = isset($request['url']['path'][5]) ? '/' . $request['url']['path'][5] : "";
        $urlpath6 = isset($request['url']['path'][6]) ? '/' . $request['url']['path'][6] : "";
        $urlpath7 = isset($request['url']['path'][7]) ? '/' . $request['url']['path'][7] : "";
        $urlpath8 = isset($request['url']['path'][8]) ? '/' . $request['url']['path'][8] : "";
        $requestUrl = $urlpath0 . $urlpath1 . $urlpath2 . $urlpath3 . $urlpath4 . $urlpath5 . $urlpath6 . $urlpath7 . $urlpath8;
        return $requestUrl;
    }
}
