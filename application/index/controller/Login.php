<?php
namespace app\index\controller;
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

        $input=$request->post();
        //$input["password"] = md5($input["password"]);
        //通过模型查询验证用户和密码
        $status=model('User')->CheckUser($input);
        
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