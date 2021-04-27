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
   	     
      $ret  = $this->where('phone',$phone)->field('salt,password,id,status')->find();
      $status =(['status'=>1,'msg'=>"账号已禁用!"]);
      if ($ret['status']==0) {

            $status =(['status'=>1,'msg'=>"账号或密码错误!"]);
            //判断密码是否正确
            $salt = $ret['salt'];
            $pwd1 = md5($salt.$pwd);   
            
            if ($ret['password']==$pwd1) {
               $status =(['status'=>0,'msg'=>"验证成功!"]);

               session('admin.id',$ret['id']);
               $ip = request()->ip();
               $this->where('id',$ret['id'])->setField(['last_login_time' => time(),
                                                        'last_login_ip' => $ip]);
            }                          
      }
           
      return $status;
   }
}