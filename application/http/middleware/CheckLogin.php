<?php

namespace app\http\middleware;

class CheckLogin
{
    public function handle($request, \Closure $next)
    {
    	//判断账号是否登入
        if (!Session('user.id')) {
            return redirect(url('/login'));//->with('error','非法请求,请先登入账户');
        }
    	return $next($request);
    }
}
