<?php

namespace app\index\validate;

use think\Validate;

class RegValidate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
                 'code'    =>'require|length:6',
                 'username'=>'require',
                 'password'=>'require',
                 'phone'=>'require|length:11'
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
                'code.require'=>'验证码没有写哦!',
                'code.length'=>'验证码长度错误!',
                'username.require'=>'用户名没有写哦!',        
                'password.require'=>'密码没有写哦!',
                'phone.require'=>'手机号没有写哦!',
                'phone.length'=>'手机号长度错误!'
    ];
}
