<?php
namespace app\common\model;
use think\Model;
use think\Session;
use think\Db;
/**
 * 
 */
class Code extends Model
{
   
   //生成验证码
   public function CreateCode($phone)
   {    
   	    Session('user.phone',$phone);
   	    $code=[rand(111111,999999)];
       // dump($code);
        //定义豹子号   
        $leopard = ['000000','111111','222222','333333','444444','555555','666666','777777','888888','999999','123456','654321'];

        //判断如果是豹子号 多获取一次
        if (in_array($code[0], $leopard)) {
          $code=rand(111111,999999);
        }
        //写入数据库
        $uid = Session('user.id');
        $writecode = $this->save(['phone'=>$phone,
                                  'code'=>$code[0],//$code,
                                  'send_time'=>time(),
                                  'end_time'=>time()+3000]);//有效时间5分钟

          //后续对接到短信平台
          $status  = (['status'=>1,'msg'=>"发送失败,请重试!"]);
        if ($writecode) {
           //检查后台是否开启短信平台
            $msg = db::name('Config')->where('type','sms')->find();
            $status = (['status'=>0,'msg'=>"发送成功!"]);
           //如果没有开启则直接返回验证码给用户(仅供测试期使用)
            if ($msg['status']==1) {
              $status  = (['status'=>2,'msg'=>"$code[0]"]);
              
            }
         
        }
        
        return  $status;

   }
   //校验验证码
   public function CheckCode($phone,$code)
   {  
      //$phone = Session('user.phone');
      //dump($phone);
         $readcode = $this->where('phone',$phone)->where('status',0)->field('code,end_time,id')->order('id', 'desc')->find();
         //dump($readcode);
      if ($readcode) {         

         if ($readcode['end_time'] >= time()) {

            if ($readcode['code']==$code) {
               //$this->where('id',$readcode['id'])->setField('status',1);
               $status = (['status'=>0,'msg'=>"验证成功!",'id'=>$readcode['id']]);
            }else{
               $status = (['status'=>1,'msg'=>"验证码错误!"]);
            }

         }else{
            $status  = (['status'=>1,'msg'=>"验证码失效,请重新获取!"]);
         }
         
      }else{
        $status  = (['status'=>1,'msg'=>"请先获取验证码!"]);
      }

      return $status;
   }

   //将验证码状态改为1
   public function CodeStatus($id)
   {
      $this->where('id',$id)->setField('status',1);
               
   }
}