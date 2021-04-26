<?php
namespace app\common\model;
use think\Model;
use think\Db;
/**
 * 
 */
trait Uid
{
  public function uid()
  {
        $uid = Session('user.id');
        return $uid;
  }
}
class User extends Model
{  
  use Uid;
	//登入验证
	public function CheckUser(array $data){
         
         //验证手机号和密码是否存在
         $phone  = $data['phone'];
         $pwd    = $data['password'];
         $res = $this->CheckPwd($data);
         $status=(['status'=>1,'msg'=>'用户名或密码错误!']);
         if($res['status']==0){
               //获取用户id和账号状态
               $ret = $this->where('phone',$data['phone'])->field('id,status')->find();
               //dump($data);
               if ($ret['status']==0) {
                 //登入成功将用户id保存到session
                 $uid =$ret['id'];
                 $uip = request()->ip();
                 Session('user.id',$uid);
                 Session('user.ip',$uip);
                 $this->where('id',$uid)->setField(['last_login_time' => time(),'last_login_ip' => $uip]);
                 $status=(['status'=>0,'msg'=>'登入成功!']);
               }else{

                 $status=(['status'=>2,'msg'=>'账户已禁用!']);
               }
              
         }
         return $status;
	}

   //查出用户资料
   public function QueryUser($id){
      $uid =$this->uid();
      //当传过来的id是本用户时
      if ($uid==$id) {
        $ret = $this->where('id',$id)->field('id,head_img,sign,username,status,last_login_time,phone')->find();
      }else{
          //当查询其他用户时
          //先通过id 查询 如果没有则查手机号
          $ret=$this->where('id',$id)->field('id,head_img,sign,username,last_login_time,phone,last_login_time,phone,status')->find();
            //将电话号码做处理后再输出
            $phone = $this->str($ret['phone']);           
            $ret['phone'] = $phone;
          if(empty($ret['id'])){

              $ret = $this->where('phone',$id)->field('id,head_img,sign,username,last_login_time,phone,status')->find();
              //将电话号码做处理后再输出
              $phone = $this->str($ret['phone']);           
              $ret['phone'] = $phone;
          }
         
      } 
      return $ret;     
   }

   //数据隐藏处理默认只显示前三位和后三位字符
   public function str($str)
   {
            //隐藏号码中间4位
            //$str = $phone['phone'];//15555555555
            $res1 =(substr($str, 0,3));
            $res2 =(substr($str, -3,3));       
            $newstr = ($res1.'*****'.$res2);
            return $newstr;
   }
   //通过手机号码查找用户是否存在
   public function CheckPhone($phone)
   {
      $ret=$this->where('phone',$phone)->field('id')->find();
      $status = (["status"=>0,"msg"=>"号码可用!"]);
      if($ret){
         $status = (["status"=>1,"msg"=>"已被注册!"]);
      }
      //dump($status);
      return $status;
   }   

   //保存用户注册信息
   public function SaveUser($input)
   {     
         $uip  = request()->ip();
         $salt = $this->create_salt();
         $pwd  = md5($salt.$input["password"]);
         $msg = $this->create([
                               'username'=>$input["username"],
                               'phone'=>$input["phone"],
                               'head_img'=>$input["head_img"],
                               'salt'=>$salt,
                               'password'=>$pwd,
                               'last_login_ip'=>$uip,
                               'last_login_time'=>time(),
                               'reg_time'=>time()]);
            $status = (["status"=>"1","msg"=>"注册失败"]);
         if ($msg) {
            $status = (["status"=>"0","msg"=>"注册成功"]);
         }
         return $status;
   }

   //修改用户头像
   public function ChangeHead($request)
   {
        $uid =$this->uid();
        $content = model('Image')->UpImg($request);
        //dump($content);
        $status = (['status'=>1,'msg'=>'修改失败!']);
       if ($content['status']==0) {
         $res = Db::name('user')->where('id',$uid)->setField('head_img',$content['img']);
         if ($res) {
           $status = (["img"=>$content['img'],"status"=>0,"msg"=>"修改成功!"]);
         }
         
       }
       
       return $status;
   }

   //修改用户资料
   public function ModifyData($type,$text)
   {  
      $uid = $this->uid();
      //dump($type,$text);
      //异常处理
      try {
        $cont = $this->where('id',$uid)->setField("$type",$text);//将变量赋值给数据库的字段名
        $status =(['status'=>0,'msg'=>"修改成功!"]);
          if ($cont == 0) {
             $status =(['status'=>1,'msg'=>"未做修改!"]);
          }
      
      } catch (\Exception $e) {
          $dd = $e->getcode();
          $status =(['status'=>1,'msg'=>"修改失败!此号码已被注册!"]);
      }
     
      return $status;
      
   } 
    
   //验证密码
   public function CheckPwd(array $data)
   {
      $uid  = $this->uid();
      //dump($data);
      /*查出用户密码和密码盐
       当用户已登入时用Session户id里的用查,否则用phone*/
      if (empty($data['phone'])) {
         $ret  = $this->where('id',$uid)->field('salt,password')->find();
      }else{
         $ret  = $this->where('phone',$data['phone'])->field('salt,password')->find();
      }   
      $salt = $ret['salt'];
      $pwd1 = md5($salt.$data['password']);
      $status =(['status'=>0,'msg'=>"密码正确!"]);
      //当加盐后生成的密码不等于原密码时
      if ($ret['password']!=$pwd1) {
        $status =(['status'=>1,'msg'=>"密码错误!"]);
      }
      return $status;
   }

   //修改密码
   public function ChengePwd($newpwd)
   {
      $uid  = $this->uid();
      $salt = $this->create_salt();
      //密码加盐
      $pwd  = md5($salt.$newpwd);
      $cont =$this->where('id',$uid)->setField(['password'=>$pwd,'salt'=>$salt]);
      $status =(['status'=>0,'msg'=>"修改成功!"]);
      if ($cont == 0) {
         $status =(['status'=>1,'msg'=>"未做修改!"]);
      }
      return $status;
   }

   //生成密码盐
    function create_salt() {
    $code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $rand = $code[rand(0,25)]
        .strtoupper(dechex(date('m')))
        .date('d')
        .substr(time(),-5)
        .substr(microtime(),2,5)
        .sprintf('%02d',rand(0,99));
    for(
        $a = md5( $rand, true ),
        $s = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        $d = '',
        $f = 0;
        $f < 6;
        $g = ord( $a[ $f ] ),
        $d .= $s[ ( $g ^ ord( $a[ $f + 8 ] ) ) - $g & 0x1F ],
        $f++
    );
    return $d;
}
}