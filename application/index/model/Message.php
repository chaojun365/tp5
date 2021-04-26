<?php
namespace app\common\model;
use think\Db;
use think\Model;
/**
 * 
 */
class Message extends Model
{
   //查出所有未读系统消息
   public function GetSys($id)
   {
     $uid  = Session('user.id'); 
     $cont = $this->where('id', '>',$id)->where('receiver_id',$uid)->select();
     $Msg  = $this->where('id', '<=',$id)->where('receiver_id',$uid)->where('status',0 )->update(['status'=>1]);
     if(!$cont)
     {
        $cont = (["status"=>3,"msg"=>"没有系统消息","type"=>4]);
     }
     foreach ($cont as $key => $value) {
        $cont[$key]['username'] = Model('User')->where('id',$value['send_id'])->field('username')->find();
     }
     //dump($cont);
     return json($cont);
   }
   //查出未读系统消息数
   public function Syscount($id)
   { 
   	 $uid = Session('user.id'); 
     $count = $this->where('receiver_id',$uid)->where('status',0)->count();
     if ($count==0)
     {
     	$count = (["status"=>2,"msg"=>"没有系统新消息","type"=>3]);
     }
     return json($count);
   }

   //更改系统消息状态
   public function correct($id,$type)
   {
      $uid    = Session('user.id');
      $info   = $this->where('id',$id)->field('send_id,receiver_id,nickname')->find();
      $send_id =$info['send_id'];
      $nickname=$info['nickname'];
      $userinfo=Model('User')->where('id',$send_id)->field('username,status')->find();
     if($userinfo['status']==1)
     {
       $status = (["status"=>2,"msg"=>"对方账号已被禁用!"]);
       return json($status);
     }
     if($type==1){
         //检查是否是已经是好友
         $msg = Model('Friends')->where([
                                     'uid'=>$uid,
                                     'friends_id'=>$send_id])->find();
         if ($msg) {
           $status = (["status"=>1,"msg"=>"已经是好友!"]);        
           return json($status);
         }
         //添加到他的好友列表
         $msg = Model('friends')->create([
                                    'uid'=>$send_id,
                                    'friends_id'=>$uid,
                                    'nickname'=>$nickname,
                                    'add_time'=>time()]);
         //添加到我的好友列表
         $msg = Model('friends')->create([
                                    'uid'=>$uid,
                                    'friends_id'=>$send_id,
                                    'nickname'=>$userinfo['username'],
                                    'add_time'=>time()]);
     }

   	 //
   	         $this->create([
                                    'send_id'=>$uid,
                                    'receiver_id'=>$send_id,
                                    'status'=>0,
                                    'type'=>$type,
                                    'send_time'=>time()]);
   	 $msg = $this->where('id',$id)->update(['type'=>$type,'status'=>1]);
   	 $status = (["status"=>0,"msg"=>"修改成功!"]);
   	 if(!$msg)
   	 {
   	 	$status = (["status"=>1,"msg"=>"修改失败!"]);
   	 }
   	 return json($status);
   }

   //删除消息
   public function clean($id)
   {
   	 $uid = Session('user.id');
   	 $msg = $this::where(['id'=>$id,'receiver_id'=>$uid])->delete();
     //dump($id);
   	 $status = (["status"=>0,"msg"=>"删除成功!"]);
   	 if($msg==0)
   	 {
   	 	$status = (["status"=>1,"msg"=>"删除失败!"]);
   	 }
   	 return json($status);
   }

}