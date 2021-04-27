<?php
namespace app\index\controller;
use app\index\validate\PhoneValidate;
use app\index\validate\PwdValidate;
use think\Controller;
use think\Request;
use think\Session;
use think\Db;
/*use think\Facade\Cache;
*/
/**
 * 
 */
class Index extends Controller
{

  protected $middleware=[
    'CheckLogin'
  ];
  
	public function index()
  {
     
        $uid=Session('user.id');
        //通过模型查询出好友列表
        $friendslist=model('Friends')->checkFriends($uid);
        //$lastinfo =model('content')->LastInfo()
        //Db::name('friends')->where(['uid'=>$uid,'status'=>0])->order('id','desc')->select();  
        //dump($friendslist);
        //通过模型查出当前用户信息
        $umsg=model('User')->QueryUser($uid);
        //dump($umsg);
        /*Cache::store('redis')->set('name','redis',60);
        $name = Cache::store('redis')->get('name');
        //$redis = new Redis ();
        $umsg['username'] = $name;*/
      return view()->assign(['umsg'=>$umsg,'friendslist'=>$friendslist]);
	}
  //秒杀视图
  public function miaosha()
  { 
     return view();
  }       

    //秒杀
  public function miaoshav($goods)
  {  
     $redis = new \Redis();
     $redis->connect('127.0.0.1', 6379);
     $redis->AUTH(123456);
     $redis->select(14);
     //redisl列名
     $redis_name = 'miaosha';

     //商品名
     //$goods = 'IPHONE X';
            //模拟大量用户去请求  --只能模拟高压力,不能模拟高并发的
            /*for ($i=0;$i<20;$i++){
                $uid = rand(1000,9999);*/
                      //接受用户id
                        $uid = [Session('user.id')];
                        $len = $redis->lLen($redis_name); 
         
                        $user = $redis->Lrange($redis_name,0,-1);  
                        //dump($user);
                        foreach ($user as  $value) {
                                $user_arr    = explode('%',$value);
                                $aa = $user_arr[0];
                            //dump($aa);
                            if (in_array($aa,$uid)) {
                                $status = (['status'=>1,'msg'=>"每个用户只有一个抢购名额哦"]);
                                return json($status);
                            } 
    
                        }
                      //检查用户是否已经抢购到了
                      /*for ($i=0; $i < $len; $i++) { */
                          //从队列最左侧取出一个值来
                         // $user = $redis->Lrange($redis_name,0,-1);   
                         
                          //判断用户id是否已经抢到了
                          
                        /*  if ($user) {

                             $status = (['status'=>1,'msg'=>"每个用户只有一个抢购名额哦"]);
                             return json($status);

                          } */
                      /*}
                      */
                      
          
                      //获取一下redis里面已有的数量
                      $num = 10;
                      //如果当天人数少于十的时候，则加入这个队列,
                      if( $len < $num){
                        //dump($len);
                          $redis->rPush($redis_name,$uid[0].'%'.$goods.'%'.microtime());
                          //echo ($i+1).'用户id:'.$uid.$goods.' 秒杀成功 '.'<br/>';
                          $status = (['status'=>0,'msg'=>"恭喜用户id:'$uid[0]'成功秒到'$goods'"]);
                      }else{
                          //如果当天人数已经到达了十个人，则返回秒杀已完成
                          // echo "秒杀已经结束".'<br/>';
                          $status = (['status'=>1,'msg'=>"秒杀已经结束"]);
                      }
                      return json($status);
            /*}*/

       /*$msgqq = Cache::store('redis')->del("1");
       dump($msgqq);
*/  }

    
  //存入数据库
  public function sqlsave()
  {
            //redis连接
            $redis = new \Redis();
            $redis->connect('127.0.0.1','6379');
            $redis->auth(123456);
            $redis->select(14);
            //列表名
            $redis_name = 'miaosha';
            $len = $redis->lLen($redis_name); 
            if ($len<1) {
                echo '列'.$redis_name.'没有数据';
            }
            
            $user = $redis->Lrange($redis_name,0,-1);  
                        //dump($user);
                        foreach ($user as  $value) {
                                $user_arr    = explode('%',$value);
                                $insert_data = [
                                    'uid'=>$user_arr[0],
                                    'goods'=>$user_arr[1],
                                    'time_stamp'=>$user_arr[2],
                                ];
                            
                            //保存到数据库中
                            $res = Db::name('miaosha')->insert($insert_data);
                            //数据库插入的失败的时候的回滚机制
                            if(!$res){
                                $redis->rPush($redis_name,$user);
                            }else{
                              $j =$redis->lPop($redis_name);
                              echo '正在保存'.$j.'<br/>';
                            }

                        }

  } 

   //删除redis列
  public function listdel()
  {
                $redis = new \Redis();
                $redis->connect('127.0.0.1','6379');
                $redis->auth(123456);
                $redis->select(14);
                //redisl列名
                $redis_name = 'miaosha';
                //列长度
                $len = $redis->lLen($redis_name);
                if ($len==0) {
                  echo $redis_name.'中没有数据'.'<br/>';
                } else {

                  for ($i=0; $i < $len; $i++) { 

                      $j =$redis->lPop($redis_name);
                      echo '已删除'.$j.'<br/>';
                  }

                }
        
      
  }

    //用户信息视图
  public function userinfo($id)
  {
       
       $msg = model('User')->QueryUser($id);
       //dump($msg);
       /*Cache::store('redis')->set($msg['id'].'id',$msg['id']);
       Cache::store('redis')->set($msg['id'].'username',$msg['username']);
       Cache::store('redis')->set($msg['id'].'phone',$msg['phone']);
       Cache::store('redis')->set($msg['id'].'sign',$msg['sign']);
       Cache::store('redis')->set($msg['id'].'last_login_time',$msg['last_login_time']);
       Cache::store('redis')->set($msg['id'].'head_img',$msg['head_img']);
       Cache::store('redis')->set($msg['id'].'status',$msg['status']);*/
       /*$msg = [];
       $msg['id'] = Cache::store('redis')->get($id.'id');
       $msg['username'] = Cache::store('redis')->get($msg['id'].'username');
       $msg['phone'] = Cache::store('redis')->get($msg['id'].'phone');
       $msg['sign'] = Cache::store('redis')->get($msg['id'].'sign');
       $msg['last_login_time'] = Cache::store('redis')->get($msg['id'].'last_login_time');
       $msg['head_img'] = Cache::store('redis')->get($msg['id'].'head_img');
       $msg['status'] = Cache::store('redis')->get($msg['id'].'status');*/
       //$msgq = Cache::store('redis')->del('1id');
       /*$msgqq = $msgq = Cache::store('redis')->KEYS("*");
       dump($msgqq);*/
       if ($msg['id'] ==false) {
          $msg = model('User')->QueryUser($id);
         
       }      
       //dump($msg);
      return view()->assign('msg',$msg);
  }

  public function add_user()
  {
      return view();
  }

  public function my_set()
  {    
      return view();
  }

  public function seach_user()
  {
      return view();
  }
    public function checkfriends($fid,$id){

      if($fid){
        $cont = model('Content')->QueryContent($fid,$id);
      	//$content=
      	 //dump($cont);
         return json($cont);  
      }
           
	}
  //发送好友请求
  public function friendrequest($friendid,$message,$nickname){
    //将好友请求发送到模型
    $msg=model('Friends')->friendrequest($friendid,$message,$nickname);
    //dump($msg);
    return $msg;  
  }

	public function msg($id){
		//通过模型查出未读信息数
		$msg=model('Content')->QueryMsg($id);
    //dump($msg);
		return json($msg);  
	}
  
  //接收客户端信息
	public function sendmsg($fid,$content,$type){
        //将信息通过模型保存至数据库
        $cont = model('Content')->SaveContent($fid,$content,$type);
        return $cont;
	}

  //获取好友发送的新信息
  public function fsmsg($fid,$id){
        //获取好友发送的新信息
        $cont = model('Content')->GetMsg($fid,$id);
        return $cont;
  }
  //设置头像
  public function upimg(Request $request){

         $status = model('User')->ChangeHead($request);

         return json($status);        

  }

  //给好友发送图片
  public function upload($fid,$images,$type)
  {
            
            $status = model('Content')->SaveContent($fid,$images,$type);

            return $status;
            /*$status = json(["status"=>0,"msg"=>"图片发送成功!"]);
            if (!$r) {
                $status = json(["status"=>1,"msg"=>"图片发送失败!"]);
            }
                return $status;*/
  }

  //获取未读系统消息
  public function symsg($id)
  {
        //获取好友发送的新信息
        $cont = model('Message')->GetSys($id);
        return $cont;
  }

  //获取未读系统消息数
  public function syscount($id)
  {
        //获取好友发送的新信息
        $count = model('Message')->Syscount($id);
        return $count;
  }

    //更改系统消息状态
  public function correct($id,$type)
  {
        
        $status = model('Message')->correct($id,$type);
        return $status;
  }

  //清除消息
  public function clean($id)
  {
       $status = model('Message')->clean($id);
        return $status; 
  }
  //查找用户
  public function seach($id)
  {
      $cont = model('User')->QueryUser($id);
      return $cont; 
  }

    //请求获取用户链接
  public function qrcode(Request $request)
  {    
       $input=$request->post();
       $uid=Session('user.id');
       if ($uid) {
         $host = $_SERVER['HTTP_HOST']; 
         $content = $host."/userid/".$uid;
         //dump($content);
         return json($content);
        }
  }
  //修改资料
  public function modify_data(Request $request)
  {
        $input = $request->post();
      
        $type = $input['type'];
        $text = $input['text'];
        //dump($type,$text);
        $status = model('User')->ModifyData($type,$text);
        return $status;
  }
  
  //获取验证码
  public function getcode(Request $request)
  {
        
        $input = $request->post();
        //验证器检测验证码
        $ret=$this->validate($input,PhoneValidate::class);

        if(true !== $ret){
          return $this->error($ret);
        }
        $phone = $input['phone'];
        //dump($input);
        $status = model('Code')->CreateCode($phone);
        return $status;
  }

  //验证码校验
  public function checkcode(Request $request)
  {  
      $input = $request->post();
      $phone = $input['phone'];
      $code = $input['code'];
      $status = model('Code')->CheckCode($phone,$code);

      if ($status['status']===0) {
          //dump($status['id']);
          $id = $status['id'];
          $status = model('User')->ModifyData("phone",$phone);
        if ($status['status']==0) {           
            model('Code')->CodeStatus($id);
         }
      }
        return $status;
      

  }

  //修改密码
  public function changepwd(Request $request)
  {
        $input = $request->post();
        //验证器检测验证码
        $ret=$this->validate($input,PwdValidate::class);

        if(true !== $ret){
          return $this->error($ret);
        }
        $pwd['password'] = $input['pwd'];
        $newpwd = $input['newpwd'];
        //dump($type,$text);
        $status = model('User')->CheckPwd($pwd);
        if ($status['status']==0) {
          $status = model('User')->ChengePwd($newpwd);
        }
        return $status;
          
  }
  public function cesi()
  {
    return view();
  }
  //退出
  public function signout(Request $request){
         $input= $request->post();
         if($input){
            session('user.id',null);
            return redirect(url('/login'));
         }
         
  }
 
}
