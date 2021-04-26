<?php

namespace app\index\validate;

use think\Validate;

class PwdValidate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
                 'pwd'=>'require',
                 'newpwd'=>'require',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
                'pwd.require'=>'密码没有写哦!',
                
                'newpwd.require'=>'新密码没有写哦!',
                
    ]; 
}