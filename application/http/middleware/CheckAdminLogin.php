<?php

namespace app\http\middleware;

class CheckAdminLogin
{
    public function handle($request, \Closure $next)
    {
    	//判断账号是否登入
        if (!Session('admin.id')) {
            return redirect(url('admin/login/'));//->with('error','非法请求,请先登入账户');
        }
    	return $next($request);
    }
}
