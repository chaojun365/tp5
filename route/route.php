<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

/*Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});

Route::get('hello/:name', 'index/hello');

return [

];*/
// 注册路由到index模块的register控制器的index操作
Route::rule('register','index/register/index');
Route::rule('login','index/login/index');
Route::rule('/','index/index/index');
Route::rule('adduser','index/index/add_user');
Route::rule('seachuser','index/index/seach_user');
Route::rule('myset','index/index/my_set');
//Route::rule('blog/:id', 'Blog/read'); // 静态地址和动态地址结合
//Route::rule(':user/:blog_id', 'Blog/read'); // 全动态地址
Route::rule('userid/:id','index/index/userinfo');
Route::rule('miaosha','index/index/miaosha');
Route::rule('sqlsave','index/index/sqlsave');
Route::rule('listdel','index/index/listdel');

//Route::rule('admin/login','admin/login/index');
//Route::rule('admin/checkuser','admin/index/checkuser');

