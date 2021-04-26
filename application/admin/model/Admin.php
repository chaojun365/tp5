<?php
namespace app\common\model;
use think\Db;
use think\Model;
/**
 * 
 */
class Admin extends Model
{
	
   public function CheckUser($phone,$pwd)
   {
   	     
      $ret  = $this->where('phone',$phone)->field('salt,password')->find();
      
      $salt = $ret['salt'];
      $pwd1 = md5($salt.$pwd);
      $status =(['status'=>0,'msg'=>"验证成功!"]);
      //当加盐后生成的密码不等于原密码时
      if ($ret['password']!=$pwd1) {
        $status =(['status'=>1,'msg'=>"账号或密码错误!"]);
      }
      return $status;
   }
}