<?php
namespace app\admin\Controller;
use think\Controller;
use think\Request;
/**
 * 
 */
class Index extends Controller
{
    public function index()
    {
       return view();
    }

   /* public function checkuser(Request $request)
    {
    	dump("123");
    	//return json(['status'=>0,'msg'=>'登录成功!']);
    }*/
}
