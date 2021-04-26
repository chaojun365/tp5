<?php
namespace app\common\model;
use think\Db;
use think\Model;

/**
 * 
 */
class Content extends Model
{
	
	 //查出聊天记录 
	public function QueryContent($fid,$id)
  {
		    $uid=Session('user.id');
          
        //将status改为1(已读状态)    
        $Msg=$this->where('id', '<=',$id)->where('uid',$fid)->where('friends_id',$uid)->where('status',0 )->update(['status'=>1]);
        /*$cont=Db::query("SELECT * FROM `tp_content` WHERE ( ( `friends_id` = $fid AND `uid` = $uid ) OR( `uid` = $fid  AND `friends_id`= $uid) ) AND `status`= 0");*/
        $cont=Db::query("SELECT * FROM `tp_content` WHERE ( ( `friends_id` = $fid AND `uid` = $uid ) OR( `uid` = $fid  AND `friends_id`= $uid) )  ORDER BY `id` DESC limit 100 ");
        
        //冒泡排序将数据按id从小到大顺序排列
        /* $len = count($cont);
          //控制轮次数
          for($i=1;$i<$len;$i++){
              //控制次数，并判断大小交换位置
              for($j=0;$j<$len-$i;$j++){
                  //如果当前值大于后面的值
                  if($cont[$j]>$cont[$j+1]){
                      //位置交换
                      //把大的值给临时变量
                      $tmp = $cont[$j];
                      //后面的小值替换大值
                      $cont[$j] = $cont[$j+1];
                      //大值替换小值
                      $cont[$j+1] = $tmp;
                  }
              }
          } */  
        //dump (count($cont));
        //使用php内置排序函数sort将id从小到大顺序排列
        $len = count($cont);
        sort($cont);
        for ($i=0; $i < $len; $i++) { 
           return $cont;
        }
       
   }
   
   //保存信息到数据库
   public function SaveContent($fid,$content,$type){
        $uid=Session('user.id');
        if($type==2)
        {
           $status = model('Image')->Upload($content);

           if($status["img"]===null)
           {

            return json($status);

            exit();
           }else
           {

            $content = $status["img"];
           }

        }
   	    
   	    $SaveContent= $this->create([
                                    'uid'=>$uid,
                                    'friends_id'=>$fid,
                                    'content'=>$content,
                                    'type'=>$type,
                                    'send_time'=>time()]);

        $status = (["status"=>0,"msg"=>"发送成功!"]);
        if (!$SaveContent) {
            $status = (["status"=>1,"msg"=>"发送失败!"]);
        }
            return json($status);
   }

   //获取好友发送的新信息
   public function GetMsg($fid,$id)
   {
       $uid=Session('user.id');
       $Msg=[]; 
       $Msg=Db::name('content')->where('id', '>',$id)->where('uid',$fid)->where('friends_id',$uid)->where('status',0)->select();
       //通过循环将status改为1(已读状态)   
       if($Msg) {

        foreach ($Msg as $k=>$v) { 

             Db::name('content')->where('id',$v['id'])->setField('status',1);
        }

       }
       //

       return $Msg;
   }

   //查出对方发来的最后一条聊天记录
   public function LastMsg($fid)
   {
         $uid  = Session('user.id');
         $max  = $this->where(['uid'=>$fid,'friends_id'=>$uid])->max('id');
         //转换
         $cont = $this->ChangeType($max);
         //dump($cont);
         return $cont;

   }

   //获取未读信息数
   public function QueryMsg($id)
   {
        $uid=Session('user.id');
        if (!$uid) {

           return (['msg'=>'未登入']);
        }
       // $id=(intval((implode($id))));  
        //此处加了ip登入判断
        $sip = User::where('id',$uid)->field('last_login_ip')->find();
        $uip = request()->ip();
        
        if ($uip == $sip['last_login_ip']){
           //
           $msg=$this->where('id', '>',$id)->where('friends_id',$uid)->where('status',0)->field('id,uid,type')->select(); 
           foreach ($msg as $key => $val) {
                      $msg[$key]['content'] = $this->ChangeType($val['id']);
                    }         
           //dump($Msg);      
        }else{
             Session('user.id',null); 
             $msg = (["status"=>3,"msg"=>"账号已在其他ip登入!","err"=>1]);
        }
        return $msg; 
        
   }

   //将消息做必要转换
   public function ChangeType($id)
   {
         $type = $this->where('id',$id)->field('type')->find();
         $cont=[];
         switch ($type['type']) {
           case '1'://文字
             $cont = $this->where('id',$id)->field('content')->find();
             break; 
           case '2'://图片
             $cont['content'] = "[图片]";
             break;
           case '3'://表情
             $cont['content'] = "[表情]";
             break;
           default:
             $cont['content']="还没有聊过哦";
             break;
         }
         //dump($msg);
         return $cont['content'];
   }
}