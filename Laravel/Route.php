<?php

// 前端路由: 所有的前端路由都要在这个目录下
Route::get('{path?}', 'IndexController@wechat')->where('path', '[\/\w\.-]*');
