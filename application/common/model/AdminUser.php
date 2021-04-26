<?php
namespace app\common\model;

use think\Model;

/**
 * 
 */
class AdminUser extends Model
{
	//检查用户
	public function checkUser(array $data){

         $ret=$this->where(['username'=>$data['username']])->find();
         //判断用户名是否存在
         if(!$ret){
         	return false;
         }
         //判断密码
         $pwd=md5($ret['password']);
         $pwd01=md5($data['password']);
         if($pwd != $pwd01){
            return false;
         }
         //登入成功保存到session
         Session('admin',$ret);
	     return true;
	}

}