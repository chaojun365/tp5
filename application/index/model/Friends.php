<?php
namespace app\index\model;

use think\Model;
/**
 * 
 */
class Friends extends Model
{
	
	//查出好友信息
	public function checkFriends($uid){
		$friendslist=$this->where(['uid'=>$uid,'status'=>0])->select();
         //
		    foreach ($friendslist as $k=>$v) {

            $friendslist[$k]['info'] = model('User')->where('id',$v['friends_id'])->field('head_img,sign,username')->find();
            $friendslist[$k]['info']['sign'] = model('Content')->LastMsg($v['friends_id']);
            /*$sign = $friendslist[$k]['info']['sign'];
            //当签名为空替换为最后一条聊天记录
            if(empty($sign)){
               $sign = model('Content')->LastMsg($v['friends_id']);
               $friendslist[$k]['info']['sign'] = $sign;
            }*/
        }
		if($friendslist){
			return  $friendslist;
		}

	}
   
   //发送好友请求
	public function friendrequest($friendid,$message,$nickname)
	{  
         //是不是自己的账号
         $uid = Session('user.id');
         
         if ($friendid==$uid) {
            $status = (["status"=>1,"msg"=>"不能添加自己!"]);
            return $status;
         }
         if ($friendid==0) {
            $status = (["status"=>1,"msg"=>"用户不存在!"]);
            return $status;
         }
         $userinfo=Model('User')->where('id',$friendid)->field('username,status')->find();
         //验证对方账号状态
         if($userinfo['status']==1)
         {
           $status = (["status"=>1,"msg"=>"对方账号已被禁用!"]);
           return $status;
         }
   
         //各种验证
         $status = $this->Isfriend($friendid,$uid);
         if ($status['status']==1) {
           return $status;
         }
         
        //好友昵称是否存在 不存在就将名字设为昵称
         if(!$nickname)
         {
            $nickname = $userinfo['username'];
         }
         $msg = Model('Message')->create([
                                      'send_id'=>$uid,
                                      'receiver_id'=>$friendid,
                                      'content'=>$message,
                                      'nickname'=>$nickname,
                                      'send_time'=>time()]);
     	   $status = (["status"=>0,"msg"=>"发送成功!"]);
       	 if(!$msg)
       	 {
       	 	$status = (["status"=>1,"msg"=>"发送失败!"]);
       	 }
       	 //dump($status);
       	 return $status;

	}

 //检查是否已经是好友或者是否已经加入黑名单
  public function Isfriend($friendid,$uid)
  {      
         //是否已经是好友或者已经加入黑名单
          $friend = $this->where([
                              'uid'=>$uid,
                              'friends_id'=>$friendid])->find();

          $status = (["status"=>0,"msg"=>"可以正常加好友!"]);

          if ($friend["status"]=="0")
          {
            $status = (["status"=>1,"msg"=>"已经是你的好友!"]);
          }else if ($friend["status"]==1) 
          {
            $status = (["status"=>1,"msg"=>"已拉入黑名单!"]);
          }

         //是否等待对方通过
          $info = Model('Message')->where([
                                         'send_id'=>$uid,
                                         'receiver_id'=>$friendid,
                                         'status'=>0])->find();

          if($info)
          { 
              $status = (["status"=>1,"msg"=>"请不要重复发送!"]);
            
          }
          return $status;  
  }
}