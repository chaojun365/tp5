<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;

/**
 * 
 */
class Login extends Controller
{   

	public function index()
	{
          
		  return view();
	}
	
	public function login(Request $request){

		       
		    $input = $request->post();
		    $phone = $input['phone'];
		    $code = $input['code'];
            $pwd = $input['password'];
		    //先检查验证码是否正确 
		    $status = model('Code')->CheckCode($phone,$code);
              
	      if ($status['status']===0) {
	          //dump($status['id']);
	          $id = $status['id'];
	          //通过模型查询验证用户和密码
	          $status=model('Admin')->CheckUser($phone,$pwd);
	        if ($status['status']==0) {           
	            model('Code')->CodeStatus($id);
	         }
	      }
	         
	        return $status;
	}

    //获取验证码
    public function getcode(Request $request)
    {
        
        $input = $request->post();
        //验证器检测验证码
/*        $ret=$this->validate($input,PhoneValidate::class);

        if(true !== $ret){
          return $this->error($ret);
        }*/
        $phone = $input['phone'];
        //dump($input);
        $status = model('Code')->CreateCode($phone);
        return $status;
    }	
    //退出
    public function signout(Request $request){
         $input=$request->post();
         if($input){
            session('user.id',null);
            return redirect(url('/login'));
         }
         
    }
}