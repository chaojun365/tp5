<?php
namespace app\index\controller;
use app\index\validate\RegValidate;
use think\Controller;
use think\Request;

/**
 * 
 */
class Register extends Controller
{
	
   public function index()
   {
   	 return view();
   }

   //验证手机号是否存在
   public function checkphone($phone)
   {
        $status = model('User')->CheckPhone($phone);
        return $status;
   }

   //提交注册
   public function register(Request $request)
   {
   	   
   	   $input=$request->post();
   	   //dump($input);
       //验证器检测验证码
       $ret=$this->validate($input,RegValidate::class);

       if(true !== $ret){
       	  return $this->error($ret);
       }
       //验证手机号是否存在
       $status = $this->checkphone($input["phone"]);
       
       //dump($res);
       if ($status["status"]===0) {
       	     //号码可用时将注册信息发给模型
       	     $status = Model('User')->SaveUser($input);
       }
       return $status;
   }
}